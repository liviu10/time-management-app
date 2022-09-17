<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Settings\UserListInterface;
use App\Http\Requests\UserListValidateRequest;

class UserListController extends Controller
{
    protected UserListInterface $userListService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(UserListInterface $userListService)
    {
        $this->userListService = $userListService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->userListService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserListValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserListValidateRequest $request)
    {
        return $this->userListService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->userListService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserListValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserListValidateRequest $request, $id)
    {
        return $this->userListService->handleUpdate($request, $id);
    }
}
