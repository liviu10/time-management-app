<?php

namespace App\BusinessLogic\Interfaces\Admin\Settings;

use Illuminate\Http\Request;
use App\Http\Requests\ErrorAndNotificationSystemValidateRequest;

/**
 * ErrorAndNotificationSystemInterface is a contract for what methods will be used in the ErrorAndNotificationSystemService class.
 * This resource may be accessed only by the webmaster.
 * This consists of the following CRUD operations methods:
 * - handleIndex();
 * - handleStore();
 * - handleShow();
 * - handleUpdate();
 */
interface ErrorAndNotificationSystemInterface
{
    /**
     * This function is specific for displaying all the resources from the database by handling all the HTTP requests [ GET ].
     * 
     * @return \Illuminate\Http\Response
     */
    public function handleIndex();

    /**
     * This function is specific for storing a resource in the database by handling all the HTTP request [ POST ].
     * 
     * @param App\Http\Requests\ErrorAndNotificationSystemValidateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function handleStore(ErrorAndNotificationSystemValidateRequest $request);

    /**
     * This function is specific for displaying a single resource from the database by handling all the HTTP request [ GET ].
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function handleShow($id);

    /**
     * This function is specific for updating a resource from the database by handling all the HTTP request [ PUT ].
     * 
     * @param App\Http\Requests\ErrorAndNotificationSystemValidateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function handleUpdate(ErrorAndNotificationSystemValidateRequest $request, $id);
}
