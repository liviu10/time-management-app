<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Management\ProjectSettingInterface;
use App\Http\Requests\ProjectSettingValidateRequest;

class ProjectSettingController extends Controller
{
    protected ProjectSettingInterface $projectSettingService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(ProjectSettingInterface $projectSettingService)
    {
        $this->projectSettingService = $projectSettingService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->projectSettingService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProjectSettingValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectSettingValidateRequest $request)
    {
        return $this->projectSettingService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->projectSettingService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProjectSettingValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectSettingValidateRequest $request, $id)
    {
        return $this->projectSettingService->handleUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->projectSettingService->handleDestroy($id);
    }
}
