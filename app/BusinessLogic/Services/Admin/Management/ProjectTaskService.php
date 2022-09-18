<?php

namespace App\BusinessLogic\Services\Admin\Management;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Management\ProjectTaskInterface;
use App\Http\Requests\ProjectTaskValidateRequest;
use App\Models\Management\ProjectTask;
use App\Models\User;
use App\Models\Admin\ErrorAndNotificationSystem;
use App\Models\Admin\Log;

/**
 * ProjectTaskService is a service class the will implement all the methods from the ProjectTaskInterface contract and will handle the business logic.
 */
class ProjectTaskService implements ProjectTaskInterface
{
    protected $modelName;
    protected $tableName;
    protected $userModelName;
    protected $userTableName;
    protected $errorModelName;
    protected $errorTableName;
    protected $modelNameLog;
    protected $getCurrentUserRole;

    /**
     * Instantiate the variables that will be used to get the model and table name as well as the table's columns.
     * 
     * @return Collection|String|Integer
     */
    public function __construct()
    {
        $this->modelName          = new ProjectTask();
        $this->tableName          = $this->modelName->getTable();
        $this->userModelName      = new User();
        $this->userTableName      = $this->userModelName->getTable();
        $this->errorModelName     = new ErrorAndNotificationSystem();
        $this->errorTableName     = $this->errorModelName->getTable();
        $this->modelNameLog       = new Log();
        $this->getCurrentUserRole = $this->userModelName::select('user_role_type_id')->where('user_role_type_id', '=', Auth::id())->get()->toArray()[0]['user_role_type_id'];
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function handleIndex()
    {
        try
        {
            $apiDisplayAllRecords = $this->modelName->select('id', 'project_id', 'name', 'observation')
            ->isDone()
            ->get();

            if (Auth::check() && $this->getCurrentUserRole === 1) 
            {
                if ($apiDisplayAllRecords->isEmpty()) 
                {
                    $this->modelName->log()->create([
                        'status'             => 'Non-Existent',
                        'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the table is empty.',
                        'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                            'record'         => null,
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName,
                        ]),
                        'response_details'   => 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                    ]);
                    return response([
                        'title'              => __('contact_me_system.index.war_00001_notify.user_has_rights.message_title'),
                        'notify_code'        => 'WAR_00001',
                        'short_description'  => '',
                        'description'        => __('contact_me_system.index.war_00001_notify.user_has_rights.message_super_admin', [ 
                            'methodName'     => $_SERVER['REQUEST_METHOD'],
                            'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                            'serviceName'    => __NAMESPACE__ . '\\' . substr(basename(ClientController::class), 0, -10) . '.php',
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName 
                        ]),
                        'reference'          => config('app.url') . '/documentation/warning#WAR_00001',
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 404,
                            'general_message'=> 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404',
                        ],
                        'records'            => [],
                    ], 404);
                }
                else
                {
                    $this->modelName->log()->create([
                        'status'             => 'Fetch',
                        'status_description' => Auth::user()->name . ' successfully fetch data from ' . $this->tableName . '.',
                        'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                            'record'         => null,
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName,
                        ]),
                        'response_details'   => 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                    ]);
                    return response([
                        'title'              => __('contact_me_system.index.info_00001_notify.user_has_rights.message_title'),
                        'notify_code'        => 'INFO_00001',
                        'short_description'  => '',
                        'description'        => __('contact_me_system.index.info_00001_notify.user_has_rights.message_super_admin', [ 
                            'numberOfRecords'=> $apiDisplayAllRecords->count(),
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName 
                        ]),
                        'reference'          => $this->errorModelName::where('notify_code', '=', 'INFO_00001')->pluck('notify_reference')[0],
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 200,
                            'general_message'=> 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200',
                        ],
                        'records'            => $apiDisplayAllRecords,
                    ], 200);
                }
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Forbidden',
                    'status_description' => 'User failed to fetch data from ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('user_role_system.index.war_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'WAR_00001',
                    'description'        => __('user_role_system.index.war_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => $this->errorModelName::where('notify_code', '=', 'WAR_00001')->pluck('notify_reference')[0],
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
        catch (\Illuminate\Database\QueryException $mysqlError)
        {
            if (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S02') 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the table does not exist.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('contact_me_system.index.err_00001_notify.user_has_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'short_description'  => '',
                    'description'        => __('contact_me_system.index.err_00001_notify.user_has_rights.message_super_admin', [ 
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName'    => __NAMESPACE__ . '\\' . substr(basename(ClientController::class), 0, -10) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'contact_me'
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 500,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/refman/8.0/en/cannot-find-table.html'
                    ],
                    'records'            => [],
                ], 500);
            }
            elseif (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S22') 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the table has an inconsistent structure.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('user_role_system.index.err_00001_notify.user_has_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('user_role_system.index.err_00001_notify.user_has_rights.message_super_admin', [ 
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ClientController::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 500,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/refman/8.0/en/cannot-find-table.html'
                    ],
                    'records'            => [],
                ], 500);
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error-Forbidden',
                    'status_description' => 'User failed to fetch data from ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('contact_me_system.index.err_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'short_description'  => '',
                    'description'        => __('contact_me_system.index.err_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => config('app.url') . '/documentation/warning#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProjectTaskValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handleStore(ProjectTaskValidateRequest $request)
    {
        try
        {
            if (Auth::check() && $this->getCurrentUserRole === 1) 
            {
                $apiInsertSingleRecord = $this->modelName->create([
                    'project_id'   => $request->get('project_id'),
                    'name'         => $request->get('name'),
                    'observation'  => $request->get('observation'),
                    'is_done'      => $request->get('is_done'),
                ]);
                $this->modelName->find($apiInsertSingleRecord->id)->log()->create([
                    'status'             => 'Create',
                    'status_description' => Auth::user()->name . ' has just a new resource in ' . $this->tableName . '.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => $apiInsertSingleRecord->id,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'The HTTP 201 Created success status response code indicates that the request has succeeded and has led to the creation of a resource.',
                ]);
                return response([
                    'title'              => __('contact_me_system.store.info_00002_notify.user_has_rights.message_title'),
                    'notify_code'        => 'INFO_00002',
                    'description'        => __('contact_me_system.store.info_00002_notify.user_has_rights.message_super_admin', [ 
                        'notifyCode'     => $request->get('notify_code'),
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName 
                    ]),
                    'reference'          => $this->errorModelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 201,
                        'general_message'=> 'The HTTP 201 Created success status response code indicates that the request has succeeded and has led to the creation of a resource.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/201',
                    ],
                    'records'            => $apiInsertSingleRecord,
                ], 201);
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Forbidden',
                    'status_description' => 'User failed to create a new resource in ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('contact_me_system.store.err_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('contact_me_system.store.err_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => $this->errorModelName::where('notify_code', '=', 'ERR_00001')->pluck('notify_reference')[0],
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
        catch  (\Illuminate\Database\QueryException $mysqlError)
        {
            if (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S02') 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to create a new resource in ' . $this->tableName . ', because the table does not exist.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('contact_me_system.store.err_00001_notify.user_has_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('contact_me_system.store.err_00001_notify.user_has_rights.message_super_admin', [ 
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName'    => __NAMESPACE__ . '\\' . basename(ProjectTaskService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 500,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/refman/8.0/en/cannot-find-table.html'
                    ],
                    'records'            => [],
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function handleShow($id)
    {
        try
        {
            $apiDisplayAllRecords = $this->modelName->select('id')->get();
            $apiDisplaySingleRecord = $this->modelName->find($id);
            if (Auth::check() && $this->getCurrentUserRole === 1) 
            {
                if ($apiDisplayAllRecords->isEmpty()) 
                {
                    $this->modelName->log()->create([
                        'status'             => 'Non-Existent',
                        'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the table is empty.',
                        'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                            'record'         => null,
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName,
                        ]),
                        'response_details'   => 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                    ]);
                    return response([
                        'title'              => __('contact_me_system.show.war_00001_notify.user_has_rights.message_title'),
                        'notify_code'        => 'WAR_00001',
                        'description'        => __('contact_me_system.show.war_00001_notify.user_has_rights.message_super_admin', [ 
                            'methodName'     => $_SERVER['REQUEST_METHOD'],
                            'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                            'serviceName'    => __NAMESPACE__ . '\\' . basename(ProjectTaskService::class) . '.php',
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName 
                        ]),
                        'reference'          => config('app.url') . '/documentation/warning#WAR_00001',
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 404,
                            'general_message'=> 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404',
                        ],
                        'records'            => [],
                    ], 404);
                }
                else 
                {
                    if (is_null($apiDisplaySingleRecord))
                    {
                        $this->modelName->log()->create([
                            'status'             => 'Non-Existent',
                            'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the record does not exist.',
                            'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                                'record'         => null,
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName,
                            ]),
                            'response_details'   => 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                        ]);
                        return response([
                            'title'              => __('contact_me_system.show.info_00006_notify.user_has_rights.message_title'),
                            'notify_code'        => 'INFO_00006',
                            'description'        => __('contact_me_system.show.info_00006_notify.user_has_rights.message_super_admin', [ 
                                'methodName'     => $_SERVER['REQUEST_METHOD'],
                                'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                                'serviceName'    => __NAMESPACE__ . '\\' . basename(ProjectTaskService::class) . '.php',
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName,
                                'lookupRecord'   => $id,
                            ]),
                            'reference'          => $this->errorModelName::where('notify_code', '=', 'INFO_00006')->pluck('notify_reference')[0],
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 404,
                                'general_message'=> 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404',
                            ],
                            'records'            => [],
                        ], 404);
                    }
                    else
                    {
                        $this->modelName->log()->create([
                            'status'             => 'Fetch',
                            'status_description' => Auth::user()->name . ' successfully fetch data from ' . $this->tableName . '.',
                            'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                                'record'         => null,
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName,
                            ]),
                            'response_details'   => 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                        ]);
                        return response([
                            'title'              => __('contact_me_system.show.info_00001_notify.user_has_rights.message_title'),
                            'notify_code'        => 'INFO_00001',
                            'description'        => __('contact_me_system.show.info_00001_notify.user_has_rights.message_super_admin', [ 
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName 
                            ]),
                            'reference'          => $this->errorModelName::where('notify_code', '=', 'INFO_00001')->pluck('notify_reference')[0],
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 200,
                                'general_message'=> 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200',
                            ],
                            'records'            => $apiDisplaySingleRecord,
                        ], 200);
                    }
                }
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Forbidden',
                    'status_description' => 'User failed to fetch data from ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('contact_me_system.show.war_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'WAR_00001',
                    'description'        => __('contact_me_system.show.war_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => $this->errorModelName::where('notify_code', '=', 'WAR_00001')->pluck('notify_reference')[0],
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
        catch (\Illuminate\Database\QueryException $mysqlError)
        {
            if (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S02') 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the table does not exist.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('contact_me_system.show.err_00001_notify.user_has_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('contact_me_system.show.err_00001_notify.user_has_rights.message_super_admin', [ 
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName'    => __NAMESPACE__ . '\\' . basename(ProjectTaskService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'contact_me'
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 500,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/refman/8.0/en/cannot-find-table.html'
                    ],
                    'records'            => [],
                ], 500);
            }
            elseif (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S22')
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the table has an inconsistent structure.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('error_and_notification_system.show.err_00001_notify.user_has_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('error_and_notification_system.show.err_00001_notify.user_has_rights.message_super_admin', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ProjectTaskService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'contact_me'
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 500,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/mysql-errors/8.0/en/server-error-reference.html#error_er_bad_field_error'
                    ],
                    'records'            => [],
                ], 500);
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error-Forbidden',
                    'status_description' => 'User failed to fetch data from ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('contact_me_system.index.err_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('contact_me_system.index.err_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => $this->errorModelName::where('notify_code', '=', 'ERR_00001')->pluck('notify_reference')[0],
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProjectTaskValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function handleUpdate(ProjectTaskValidateRequest $request, $id)
    {
        try
        {
            if (Auth::check() && $this->getCurrentUserRole === 1) 
            {
                $apiUpdateSingleRecord = $this->modelName->find($id);
                $apiUpdateSingleRecord->update([
                    'project_id'   => $request->get('project_id'),
                    'name'         => $request->get('name'),
                    'observation'  => $request->get('observation'),
                    'is_done'      => $request->get('is_done'),
                ]);
                $this->modelName->find($apiUpdateSingleRecord->id)->log()->create([
                    'status'             => 'Update',
                    'status_description' => Auth::user()->name . ' has just a updated a resource in ' . $this->tableName . '.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => $apiUpdateSingleRecord->id,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                ]);
                return response([
                    'title'              => __('error_and_notification_system.update.info_00002_notify.user_has_rights.message_title'),
                    'notify_code'        => 'INFO_00002',
                    'description'        => __('error_and_notification_system.update.info_00002_notify.user_has_rights.message_super_admin', [ 
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName 
                    ]),
                    'reference'          => $this->errorModelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 200,
                        'general_message'=> 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200',
                    ],
                    'records'            => $apiUpdateSingleRecord,
                ], 200);
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Forbidden',
                    'status_description' => 'User failed to create a new resource in ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('error_and_notification_system.update.err_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('error_and_notification_system.update.err_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => $this->errorModelName::where('notify_code', '=', 'ERR_00001')->pluck('notify_reference')[0],
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
        catch (\Illuminate\Database\QueryException $mysqlError)
        {
            if (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S02') 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to update a resource in ' . $this->tableName . ', because the table does not exist.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('error_and_notification_system.update.err_00001_notify.user_has_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('error_and_notification_system.update.err_00001_notify.user_has_rights.message_super_admin', [ 
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName'    => __NAMESPACE__ . '\\' . basename(ProjectTaskService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 500,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/refman/8.0/en/cannot-find-table.html'
                    ],
                    'records'            => [],
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function handleDestroy($id)
    {
        try
        {
            $apiDisplayAllRecords = $this->modelName->all();
            $apiDisplaySingleRecord = $this->modelName->find($id);
            if (Auth::check() && $this->getCurrentUserRole === 1) 
            {
                if ($apiDisplayAllRecords->isEmpty()) 
                {
                    $this->modelName->log()->create([
                        'status'             => 'Non-Existent',
                        'status_description' => Auth::user()->name . ' failed to delete data from ' . $this->tableName . ', because the table is empty.',
                        'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                            'record'         => null,
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName,
                        ]),
                        'response_details'   => 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                    ]);
                    return response([
                        'title'              => __('contact_me_system.delete.war_00001_notify.user_has_rights.message_title'),
                        'notify_code'        => 'WAR_00001',
                        'short_description'  => '',
                        'description'        => __('contact_me_system.delete.war_00001_notify.user_has_rights.message_super_admin', [ 
                            'methodName'     => $_SERVER['REQUEST_METHOD'],
                            'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                            'serviceName'    => __NAMESPACE__ . '\\' . substr(basename(ClientController::class), 0, -10) . '.php',
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName 
                        ]),
                        'reference'          => config('app.url') . '/documentation/warning#WAR_00001',
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 404,
                            'general_message'=> 'The HTTP 404 Not Found client error response code indicates that the server can\'t find the requested resource.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404',
                        ],
                        'records'            => [],
                    ], 404);
                }
                else
                {
                    $apiDeleteSingleRecord = $this->modelName->find($id)->delete();
                    $this->modelName->log()->create([
                        'status'             => 'Delete',
                        'status_description' => Auth::user()->name . ' successfully deleted data from ' . $this->tableName . '.',
                        'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                            'record'         => null,
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName,
                        ]),
                        'response_details'   => 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                    ]);
                    return response([
                        'title'              => __('contact_me_system.index.info_00002_notify.user_has_rights.message_title'),
                        'notify_code'        => 'INFO_00002',
                        'short_description'  => '',
                        'description'        => __('contact_me_system.index.info_00002_notify.user_has_rights.message_super_admin', [ 
                            'numberOfRecords'=> $apiDisplaySingleRecord->count(),
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName 
                        ]),
                        'reference'          => $this->errorModelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 200,
                            'general_message'=> 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200',
                        ],
                        'records'            => $apiDisplaySingleRecord,
                    ], 200);
                }
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Forbidden',
                    'status_description' => 'User failed to delete data from ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('contact_me_system.index.err_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'short_description'  => '',
                    'description'        => __('contact_me_system.index.err_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => config('app.url') . '/documentation/warning#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
        catch (\Illuminate\Database\QueryException $mysqlError)
        {
            if (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S02') 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to delete data from ' . $this->tableName . ', because the table does not exist.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('contact_me_system.delete.err_00001_notify.user_has_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'short_description'  => '',
                    'description'        => __('contact_me_system.delete.err_00001_notify.user_has_rights.message_super_admin', [ 
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName'    => __NAMESPACE__ . '\\' . substr(basename(ClientController::class), 0, -10) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'contact_me'
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 500,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/refman/8.0/en/cannot-find-table.html'
                    ],
                    'records'            => [],
                ], 500);
            }
            else 
            {
                $this->modelName->log()->create([
                    'status'             => 'Error-Forbidden',
                    'status_description' => 'User failed to delete data from ' . $this->tableName . ', because of not being authenticated or does not have rights.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'clients',
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('contact_me_system.delete.err_00001_notify.user_do_not_have_rights.message_title'),
                    'notify_code'        => 'ERR_00001',
                    'short_description'  => '',
                    'description'        => __('contact_me_system.delete.err_00001_notify.user_do_not_have_rights.message_generic_user'),
                    'reference'          => config('app.url') . '/documentation/warning#ERR_00001',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 403,
                    ],
                    'records'            => [],
                ], 403);
            }
        }
    }
}