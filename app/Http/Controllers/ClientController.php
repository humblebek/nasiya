<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Services\ClientService;
use App\Models\Client;

class ClientController extends Controller
{

    public $clientService;

    // Constructor to inject the ClientService
    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        // Retrieve all clients from the service and return the collection of clients as a resource
        $dataIndex = $this->clientService->index();
        return ClientResource::collection($dataIndex);
    }

    public function store(StoreClientRequest $request)
    {
        // Create a new client using the service
        $dataStore = $this->clientService->store($request);
        return response()->json(['message' => 'Client created successfully', 'client' => $dataStore], 201);
    }

    public function show(Client $client)
    {
        // Return the client as a resource
        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        // Update the client using the service
        $dataUpdate = $this->clientService->update($request, $client);
        return response()->json(['message' => 'Client updated successfully', 'client' => $dataUpdate], 201);
    }

    public function destroy(Client $client)
    {
        // Delete the client using the service
        $this->clientService->destroy($client);
        return response()->json(['message' => 'Client deleted successfully'], 201);
    }
}

