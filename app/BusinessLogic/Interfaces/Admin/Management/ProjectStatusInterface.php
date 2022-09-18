<?php

namespace App\BusinessLogic\Interfaces\Admin\Management;

use Illuminate\Http\Request;
use App\Http\Requests\ProjectStatusValidateRequest;

/**
 * ProjectStatusInterface is a contract for what methods will be used in the ClientService class.
 * This consists of the following CRUD operations methods:
 * - handleIndex();
 * - handleStore();
 * - handleShow();
 * - handleUpdate();
 * - handleDestroy();
 */
interface ProjectStatusInterface
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
     * @param App\Http\Requests\ProjectStatusValidateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function handleStore(ProjectStatusValidateRequest $request);

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
     * @param App\Http\Requests\ProjectStatusValidateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function handleUpdate(ProjectStatusValidateRequest $request, $id);

    /**
     * This function is specific for deleting a resource from the database by handling all the HTTP request [ DELETE ].
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function handleDestroy($id);
}
