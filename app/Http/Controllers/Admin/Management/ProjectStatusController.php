<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Management\ProjectStatusInterface;
use App\Http\Requests\ProjectStatusValidateRequest;

class ProjectStatusController extends Controller
{
    protected ProjectStatusInterface $projectStatusService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(ProjectStatusInterface $projectStatusService)
    {
        $this->projectStatusService = $projectStatusService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->projectStatusService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProjectStatusValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectStatusValidateRequest $request)
    {
        return $this->projectStatusService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->projectStatusService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProjectStatusValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectStatusValidateRequest $request, $id)
    {
        return $this->projectStatusService->handleUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->projectStatusService->handleDestroy($id);
    }
}
