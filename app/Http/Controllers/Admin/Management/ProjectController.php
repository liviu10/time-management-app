<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Management\ProjectInterface;
use App\Http\Requests\ProjectValidateRequest;

class ProjectController extends Controller
{
    protected ProjectInterface $projectService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(ProjectInterface $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->projectService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProjectValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectValidateRequest $request)
    {
        return $this->projectService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->projectService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProjectValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectValidateRequest $request, $id)
    {
        return $this->projectService->handleUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->projectService->handleDestroy($id);
    }
}
