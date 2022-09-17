<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Support\Facades\Route;
use App\Models\Admin\Log;

class Handler extends ExceptionHandler
{
    // protected $modelNameLog;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    // /**
    //  * Instantiate the variables that will be used to get the model.
    //  * 
    //  * @return Collection
    //  */
    // public function __construct()
    // {
    //     $this->modelNameLog = new Log();
    // }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
                    ? response()->json(['message' => $exception->getMessage()], 401)
                    : redirect()->guest(url('/login'));
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        $response = parent::prepareJsonResponse($request, $e);

        if ($response->getStatusCode() === 500 && config('app.debug')) {
            return $this->prepareResponse($request, $e);
        }

        return $response;
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register()
    {
        // Log::create([
        //     'status'             => 'Error',
        //     'status_description' => 'The requested API route is not allowed!',
        //     'request_details'    => __('error_and_notification_system.store.err_00004_notify.user_has_rights.message_super_admin', [
        //         'methodName'     => $_SERVER['REQUEST_METHOD'],
        //         'apiEndpoint'    => $_SERVER['REQUEST_URI'],
        //         'serviceName'    => __NAMESPACE__ . '\\' . basename(__FILE__),
        //         'databaseName'   => config('database.connections.mysql.database'),
        //         'tableName'      => '',
        //     ]),
        //     'response_details'   => 'The HyperText Transfer Protocol (HTTP) 405 Method Not Allowed response status code indicates that the server knows the request method, but the target resource doesn\'t support this method.',
        //     'sql_response'       => '',
        // ]);

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'title'              => __('error_and_notification_system.update.err_00004_notify.user_has_rights.message_title'),
                'notify_code'        => 'ERR_00004',
                'description'        => __('error_and_notification_system.update.err_00004_notify.user_has_rights.message_super_admin'),
                'reference'          => config('app.url') . '/documentation/error#ERR_00004',
                'api_endpoint'       => $_SERVER['REQUEST_URI'],
            ], 405);
        });
    }
}
