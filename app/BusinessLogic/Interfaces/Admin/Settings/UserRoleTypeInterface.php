<?php

namespace App\BusinessLogic\Interfaces\Admin\Settings;

use Illuminate\Http\Request;
use App\Http\Requests\UserRoleTypeValidateRequest;

/**
 * UserRoleTypeInterface is a contract for what methods will be used in the UserRoleTypeService class.
 * This consists of the following CRUD operations methods:
 * - handleIndex();
 * - handleStore();
 * - handleShow();
 * - handleUpdate();
 */
interface UserRoleTypeInterface
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
     * @param App\Http\Requests\UserSettingsValidationRules\UserRoleTypeValidateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function handleStore(UserRoleTypeValidateRequest $request);

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
     * @param App\Http\Requests\UserSettingsValidationRules\UserRoleTypeValidateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function handleUpdate(UserRoleTypeValidateRequest $request, $id);
}
