<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Management\ProjectTaskInterface;
use App\Http\Requests\ProjectTaskValidateRequest;

class ProjectTaskController extends Controller
{
    protected ProjectTaskInterface $projectTaskService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(ProjectTaskInterface $projectTaskService)
    {
        $this->projectTaskService = $projectTaskService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->projectTaskService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProjectTaskValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectTaskValidateRequest $request)
    {
        return $this->projectTaskService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->projectTaskService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProjectTaskValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectTaskValidateRequest $request, $id)
    {
        return $this->projectTaskService->handleUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->projectTaskService->handleDestroy($id);
    }
}
