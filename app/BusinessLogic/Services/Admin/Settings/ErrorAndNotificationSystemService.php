<?php

namespace App\BusinessLogic\Services\Admin\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Settings\ErrorAndNotificationSystemInterface;
use App\Http\Requests\ErrorAndNotificationSystemValidateRequest;
use App\Models\Admin\ErrorAndNotificationSystem;
use App\Models\User;
use App\Models\Admin\Log;

/**
 * ErrorAndNotificationSystemService is a service class the will implement all the methods from the ErrorAndNotificationSystemInterface contract and will handle the business logic.
 */
class ErrorAndNotificationSystemService implements ErrorAndNotificationSystemInterface
{
    protected $modelName;
    protected $tableName;
    protected $userModelName;
    protected $userTableName;
    protected $modelNameLog;
    protected $getCurrentUserRole;

    /**
     * Instantiate the variables that will be used to get the model and table name.
     *
     * @return Collection|String|Integer
     */
    public function __construct()
    {
        $this->modelName           = new ErrorAndNotificationSystem();
        $this->tableName           = $this->modelName->getTable();
        $this->userModelName       = new User();
        $this->userTableName       = $this->userModelName->getTable();
        $this->modelNameLog        = new Log();
        $this->getCurrentUserRole  = $this->userModelName::select('user_role_type_id')->where('user_role_type_id', '=', Auth::id())->get()->toArray()[0]['user_role_type_id'];
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function handleIndex()
    {
        try
        {
            $apiDisplayAllRecords = $this->modelName->select('id', 'notify_code', 'notify_reference')->get();
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
                        'title'              => __('dashboard_application.error_and_notification.index.error.title'),
                        'notify_code'        => 'WAR_00001',
                        'description'        => __('dashboard_application.error_and_notification.index.error.description.no_records', [
                            'methodName'     => $_SERVER['REQUEST_METHOD'],
                            'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                            'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
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
                        'title'              => __('dashboard_application.error_and_notification.index.no_error.title'),
                        'notify_code'        => 'INFO_00001',
                        'description'        => __('dashboard_application.error_and_notification.index.no_error.description', [
                            'numberOfRecords'=> $apiDisplayAllRecords->count(),
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName
                        ]),
                        'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00001')->pluck('notify_reference')[0],
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
                    'title'              => __('dashboard_application.error_and_notification.index.error.title'),
                    'notify_code'        => 'WAR_00001',
                    'description'        => __('dashboard_application.error_and_notification.index.error.description.no_rights'),
                    'reference'          => $this->modelName::where('notify_code', '=', 'WAR_00001')->pluck('notify_reference')[0],
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
                        'tableName'      => 'error_and_notification_system',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.index.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.index.error.description.no_table', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'error_and_notification_system',
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
                        'tableName'      => 'error_and_notification_system',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.index.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.index.error.description.no_fields', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'error_and_notification_system',
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
                        'tableName'      => 'error_and_notification_system',
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.index.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.index.error.description.no_rights'),
                    'reference'          => $this->modelName::where('notify_code', '=', 'ERR_00001')->pluck('notify_reference')[0],
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
     * @param  ErrorAndNotificationSystemValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handleStore(ErrorAndNotificationSystemValidateRequest $request)
    {
        try
        {
            if (Auth::check() && $this->getCurrentUserRole === 1)
            {
                $notifyReference = explode('_', $request->get('notify_code'))[0];
                if ($notifyReference === 'INFO')
                {
                    $checkIfRecordExists = $this->modelName->select('notify_code')->where('notify_code', '=', $request->get('notify_code'))->get();
                    if ($checkIfRecordExists->isNotEmpty())
                    {
                        $this->modelName->log()->create([
                            'status'             => 'Duplicate',
                            'status_description' => Auth::user()->name . ' failed to create a new resource in ' . $this->tableName . ', because of duplicates restrictions.',
                            'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                                'record'         => null,
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName,
                            ]),
                            'response_details'   => 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                        ]);
                        return response([
                            'title'              => __('dashboard_application.error_and_notification.store.error.title'),
                            'notify_code'        => 'ERR_00003',
                            'description'        => __('dashboard_application.error_and_notification.store.error.description.no_duplicates', [
                                'methodName'     => $_SERVER['REQUEST_METHOD'],
                                'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                                'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                                'databaseName'   => config('database.connections.mysql.database'),
                                'notifyCode'     => $request->get('notify_code'),
                            ]),
                            'reference'          => config('app.url') . '/documentation/error#ERR_00003',
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 406,
                                'general_message'=> 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406',
                            ],
                            'records'            => [],
                        ], 406);
                    }
                    else
                    {
                        $formUrl = config('app.url') . '/admin/documentation' . '/information#' . $request->get('notify_code');
                        $apiInsertSingleRecord = $this->modelName->create(array_merge($request->input(), [ 'notify_reference' => $formUrl ]));
                        $apiInsertSingleRecord->save();
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
                            'title'              => __('dashboard_application.error_and_notification.store.no_error.title'),
                            'notify_code'        => 'INFO_00002',
                            'description'        => __('dashboard_application.error_and_notification.store.no_error.description', [
                                'notifyCode'     => $request->get('notify_code'),
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName
                            ]),
                            'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 201,
                                'general_message'=> 'The HTTP 201 Created success status response code indicates that the request has succeeded and has led to the creation of a resource.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/201',
                            ],
                            'records'            => $apiInsertSingleRecord,
                        ], 201);
                    }
                }
                if ($notifyReference === 'WAR')
                {
                    $checkIfRecordExists = $this->modelName->select('notify_code')->where('notify_code', '=', $request->get('notify_code'))->get();
                    if ($checkIfRecordExists->isNotEmpty())
                    {
                        $this->modelName->log()->create([
                            'status'             => 'Duplicate',
                            'status_description' => Auth::user()->name . ' failed to create a new resource in ' . $this->tableName . ', because of duplicates restrictions.',
                            'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                                'record'         => null,
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName,
                            ]),
                            'response_details'   => 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                        ]);
                        return response([
                            'title'              => __('dashboard_application.error_and_notification.store.error.title'),
                            'notify_code'        => 'ERR_00003',
                            'description'        => __('dashboard_application.error_and_notification.store.error.description.no_duplicates', [
                                'methodName'     => $_SERVER['REQUEST_METHOD'],
                                'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                                'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                                'databaseName'   => config('database.connections.mysql.database'),
                                'notifyCode'     => $request->get('notify_code'),
                            ]),
                            'reference'          => config('app.url') . '/documentation/error#ERR_00003',
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 406,
                                'general_message'=> 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406',
                            ],
                            'records'            => [],
                        ], 406);
                    }
                    else
                    {
                        $formUrl = config('app.url') . '/admin/documentation' . '/warning#' . $request->get('notify_code');
                        $apiInsertSingleRecord = $this->modelName->create(array_merge($request->input(), [ 'notify_reference' => $formUrl ]));
                        $apiInsertSingleRecord->save();
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
                            'title'              => __('dashboard_application.error_and_notification.store.no_error.title'),
                            'notify_code'        => 'INFO_00002',
                            'description'        => __('dashboard_application.error_and_notification.store.no_error.description', [
                                'notifyCode'     => $request->get('notify_code'),
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName
                            ]),
                            'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 201,
                                'general_message'=> 'The HTTP 201 Created success status response code indicates that the request has succeeded and has led to the creation of a resource.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/201',
                            ],
                            'records'            => $apiInsertSingleRecord,
                        ], 201);
                    }
                }
                if ($notifyReference === 'ERR')
                {
                    $checkIfRecordExists = $this->modelName->select('notify_code')->where('notify_code', '=', $request->get('notify_code'))->get();
                    if ($checkIfRecordExists->isNotEmpty())
                    {
                        $this->modelName->log()->create([
                            'status'             => 'Duplicate',
                            'status_description' => Auth::user()->name . ' failed to create a new resource in ' . $this->tableName . ', because of duplicates restrictions.',
                            'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                                'record'         => null,
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName,
                            ]),
                            'response_details'   => 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                        ]);
                        return response([
                            'title'              => __('dashboard_application.error_and_notification.store.error.title'),
                            'notify_code'        => 'ERR_00003',
                            'description'        => __('dashboard_application.error_and_notification.store.error.description.no_duplicates', [
                                'methodName'     => $_SERVER['REQUEST_METHOD'],
                                'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                                'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                                'databaseName'   => config('database.connections.mysql.database'),
                                'notifyCode'     => $request->get('notify_code'),
                            ]),
                            'reference'          => config('app.url') . '/documentation/error#ERR_00003',
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 406,
                                'general_message'=> 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406',
                            ],
                            'records'            => [],
                        ], 406);
                    }
                    else 
                    {
                        $formUrl = config('app.url') . '/admin/documentation' . '/error#' . $request->get('notify_code');
                        $apiInsertSingleRecord = $this->modelName->create(array_merge($request->input(), [ 'notify_reference' => $formUrl ]));
                        $apiInsertSingleRecord->save();
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
                            'title'              => __('dashboard_application.error_and_notification.store.no_error.title'),
                            'notify_code'        => 'INFO_00002',
                            'description'        => __('dashboard_application.error_and_notification.store.no_error.description', [
                                'notifyCode'     => $request->get('notify_code'),
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName
                            ]),
                            'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                            'api_endpoint'       => $_SERVER['REQUEST_URI'],
                            'http_response'      => [
                                'code'           => 201,
                                'general_message'=> 'The HTTP 201 Created success status response code indicates that the request has succeeded and has led to the creation of a resource.',
                                'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/201',
                            ],
                            'records'            => $apiInsertSingleRecord,
                        ], 201);
                    }
                }
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
                    'title'              => __('dashboard_application.error_and_notification.store.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.store.error.description.no_rights'),
                    'reference'          => $this->modelName::where('notify_code', '=', 'ERR_00001')->pluck('notify_reference')[0],
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
                        'tableName'      => 'error_and_notification_system',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.store.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.store.error.description.no_table', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'error_and_notification_system',
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
                        'title'              => __('dashboard_application.error_and_notification.show.error.title'),
                        'notify_code'        => 'WAR_00001',
                        'description'        => __('dashboard_application.error_and_notification.show.error.description.no_records', [
                            'methodName'     => $_SERVER['REQUEST_METHOD'],
                            'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                            'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
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
                            'title'              => __('dashboard_application.error_and_notification.show.error.title'),
                            'notify_code'        => 'INFO_00006',
                            'description'        => __('dashboard_application.error_and_notification.show.error.description.no_records', [
                                'methodName'     => $_SERVER['REQUEST_METHOD'],
                                'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                                'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName,
                                'lookupRecord'   => $id,
                            ]),
                            'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00006')->pluck('notify_reference')[0],
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
                            'title'              => __('dashboard_application.error_and_notification.show.no_error.title'),
                            'notify_code'        => 'INFO_00001',
                            'description'        => __('dashboard_application.error_and_notification.show.no_error.description', [
                                'databaseName'   => config('database.connections.mysql.database'),
                                'tableName'      => $this->tableName
                            ]),
                            'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00001')->pluck('notify_reference')[0],
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
                    'title'              => __('dashboard_application.error_and_notification.show.error.title'),
                    'notify_code'        => 'WAR_00001',
                    'description'        => __('dashboard_application.error_and_notification.show.error.description.no_rights'),
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
                        'tableName'      => 'error_and_notification_system',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.show.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.show.error.description.no_table', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'error_and_notification_system'
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
            elseif (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '42S02')
            {
                $this->modelName->log()->create([
                    'status'             => 'Error',
                    'status_description' => Auth::user()->name . ' failed to fetch data from ' . $this->tableName . ', because the table has an inconsistent structure.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'error_and_notification_system'
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.show.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.show.error.description.no_fields', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'error_and_notification_system'
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
                        'tableName'      => 'error_and_notification_system',
                    ]),
                    'response_details'   => 'This status is similar to 401, but for the 403 Forbidden status code re-authenticating makes no difference. The access is permanently forbidden and tied to the application logic, such as insufficient rights to a resource.',
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.show.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.show.error.description.no_rights'),
                    'reference'          => $this->modelName::where('notify_code', '=', 'ERR_00001')->pluck('notify_reference')[0],
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
     * @param  ErrorAndNotificationSystemValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function handleUpdate(ErrorAndNotificationSystemValidateRequest $request, $id)
    {
        try
        {
            if (Auth::check() && $this->getCurrentUserRole === 1)
            {
                $notifyReference = explode('_', $request->get('notify_code'))[0];
                if($notifyReference === 'INFO')
                {
                    $formUrl = config('app.url') . '/admin/documentation' . '/information#' . $request->get('notify_code');
                    $apiUpdateSingleRecord = $this->modelName->find($id);
                    $getCurrentNotifyCode = $apiUpdateSingleRecord['notify_code'];
                    $apiUpdateSingleRecord->update([
                        'notify_code' => $request->get('notify_code'),
                        'notify_short_description' => $request->get('notify_short_description'),
                        'notify_reference' => $formUrl,
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
                        'title'              => __('dashboard_application.error_and_notification.update.no_error.title'),
                        'notify_code'        => 'INFO_00002',
                        'description'        => __('dashboard_application.error_and_notification.update.no_error.description', [
                            'notifyCode'     => $getCurrentNotifyCode,
                            'newNotifyCode'  => $apiUpdateSingleRecord['notify_code'],
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName
                        ]),
                        'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 200,
                            'general_message'=> 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200',
                        ],
                        'records'            => $apiUpdateSingleRecord,
                    ], 200);
                }
                if ($notifyReference === 'WAR') 
                {
                    $formUrl = config('app.url') . '/admin/documentation' . '/warning#' . $request->get('notify_code');
                    $apiUpdateSingleRecord = $this->modelName->find($id);
                    $getCurrentNotifyCode = $apiUpdateSingleRecord['notify_code'];
                    $apiUpdateSingleRecord->update([
                        'notify_code' => $request->get('notify_code'),
                        'notify_short_description' => $request->get('notify_short_description'),
                        'notify_reference' => $formUrl,
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
                        'title'              => __('dashboard_application.error_and_notification.update.no_error.title'),
                        'notify_code'        => 'INFO_00002',
                        'description'        => __('dashboard_application.error_and_notification.update.no_error.description', [
                            'notifyCode'     => $getCurrentNotifyCode,
                            'newNotifyCode'  => $apiUpdateSingleRecord['notify_code'],
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName
                        ]),
                        'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 200,
                            'general_message'=> 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200',
                        ],
                        'records'            => $apiUpdateSingleRecord,
                    ], 200);
                }
                if ($notifyReference === 'ERR')
                {
                    $formUrl = config('app.url') . '/admin/documentation' . '/error#' . $request->get('notify_code');
                    $apiUpdateSingleRecord = $this->modelName->find($id);
                    $getCurrentNotifyCode = $apiUpdateSingleRecord['notify_code'];
                    $apiUpdateSingleRecord->update([
                        'notify_code' => $request->get('notify_code'),
                        'notify_short_description' => $request->get('notify_short_description'),
                        'notify_reference' => $formUrl,
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
                        'title'              => __('dashboard_application.error_and_notification.update.no_error.title'),
                        'notify_code'        => 'INFO_00002',
                        'description'        => __('dashboard_application.error_and_notification.update.no_error.description', [
                            'notifyCode'     => $getCurrentNotifyCode,
                            'newNotifyCode'  => $apiUpdateSingleRecord['notify_code'],
                            'databaseName'   => config('database.connections.mysql.database'),
                            'tableName'      => $this->tableName
                        ]),
                        'reference'          => $this->modelName::where('notify_code', '=', 'INFO_00002')->pluck('notify_reference')[0],
                        'api_endpoint'       => $_SERVER['REQUEST_URI'],
                        'http_response'      => [
                            'code'           => 200,
                            'general_message'=> 'The HTTP 200 OK success status response code indicates that the request has succeeded. A 200 response is cacheable by default.',
                            'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200',
                        ],
                        'records'            => $apiUpdateSingleRecord,
                    ], 200);
                }
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
                    'title'              => __('dashboard_application.error_and_notification.update.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.update.error.description.no_rights'),
                    'reference'          => $this->modelName::where($this->tableAllColumns[1], '=', 'ERR_00001')->pluck($this->tableAllColumns[3])[0],
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
                        'tableName'      => 'error_and_notification_system',
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.',
                    'sql_details'        => $mysqlError->getMessage(),
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.update.error.title'),
                    'notify_code'        => 'ERR_00001',
                    'description'        => __('dashboard_application.error_and_notification.update.error.description.no_table', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => 'error_and_notification_system',
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
            if (Auth::check() && $this->getCurrentUserRole === 1 && $mysqlError->getCode() === '23000')
            {
                $this->modelName->log()->create([
                    'status'             => 'Duplicate',
                    'status_description' => Auth::user()->name . ' failed to create a new resource in ' . $this->tableName . ', because of duplicates restrictions.',
                    'request_details'    => __('error_and_notification_system.store.info_00002_notify.user_has_rights.message_super_admin', [
                        'record'         => null,
                        'databaseName'   => config('database.connections.mysql.database'),
                        'tableName'      => $this->tableName,
                    ]),
                    'response_details'   => 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                ]);
                return response([
                    'title'              => __('dashboard_application.error_and_notification.update.error.title'),
                    'notify_code'        => 'ERR_00003',
                    'description'        => __('dashboard_application.error_and_notification.update.error.description.no_duplicates', [
                        'methodName'     => $_SERVER['REQUEST_METHOD'],
                        'apiEndpoint'    => $_SERVER['REQUEST_URI'],
                        'serviceName' => __NAMESPACE__ . '\\' . basename(ErrorAndNotificationSystemService::class) . '.php',
                        'databaseName'   => config('database.connections.mysql.database'),
                        'notifyCode'     => $request->get('notify_code'),
                    ]),
                    'reference'          => config('app.url') . '/documentation/error#ERR_00003',
                    'api_endpoint'       => $_SERVER['REQUEST_URI'],
                    'http_response'      => [
                        'code'           => 406,
                        'general_message'=> 'The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response code indicates that the server cannot produce a response matching the list of acceptable values defined in the request\'s proactive content negotiation headers, and that the server is unwilling to supply a default representation.',
                        'url'            => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406',
                    ],
                    'sql_response'       => [
                        'sql_err_code'   => $mysqlError->getCode(),
                        'sql_err_message'=> $mysqlError->getMessage(),
                        'sql_err_url'    => 'https://dev.mysql.com/doc/mysql-errors/8.0/en/server-error-reference.html#error_er_dup_entry'
                    ],
                    'records'            => [],
                ], 406);
            }
        }
    }
}