<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessLogic\Interfaces\Admin\Management\ClientInterface;
use App\Http\Requests\ClientValidateRequest;

class ClientController extends Controller
{
    protected ClientInterface $clientService;

    /**
     * Instantiate the interface that will be used to get all the methods that are going to be used in this controller.
     */
    public function __construct(ClientInterface $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->clientService->handleIndex();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ClientValidateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientValidateRequest $request)
    {
        return $this->clientService->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->clientService->handleShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ClientValidateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientValidateRequest $request, $id)
    {
        return $this->clientService->handleUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->clientService->handleDestroy($id);
    }
}
