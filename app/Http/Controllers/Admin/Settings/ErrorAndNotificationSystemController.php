<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Settings\ErrorAndNotificationSystemInterface;
use App\Http\Requests\ErrorAndNotificationSystemValidateRequest;

class ErrorAndNotificationSystemController extends Controller
{
    protected ErrorAndNotificationSystemInterface $errorAndNotificationSystemService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(ErrorAndNotificationSystemInterface $errorAndNotificationSystemService)
    {
        $this->errorAndNotificationSystemService = $errorAndNotificationSystemService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->errorAndNotificationSystemService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ErrorAndNotificationSystemValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ErrorAndNotificationSystemValidateRequest $request)
    {
        return $this->errorAndNotificationSystemService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->errorAndNotificationSystemService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ErrorAndNotificationSystemValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ErrorAndNotificationSystemValidateRequest $request, $id)
    {
        return $this->errorAndNotificationSystemService->handleUpdate($request, $id);
    }
}
