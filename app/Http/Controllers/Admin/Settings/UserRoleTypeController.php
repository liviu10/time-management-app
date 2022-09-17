<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Settings\UserRoleTypeInterface;
use App\Http\Requests\UserRoleTypeValidateRequest;

class UserRoleTypeController extends Controller
{
    protected UserRoleTypeInterface $userRoleTypeService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(UserRoleTypeInterface $userRoleTypeService)
    {
        $this->userRoleTypeService = $userRoleTypeService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->userRoleTypeService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRoleTypeValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRoleTypeValidateRequest $request)
    {
        return $this->userRoleTypeService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->userRoleTypeService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserRoleTypeValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRoleTypeValidateRequest $request, $id)
    {
        return $this->userRoleTypeService->handleUpdate($request, $id);
    }
}
