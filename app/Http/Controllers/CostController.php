<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCostRequest;
use App\Http\Requests\UpdateCostRequest;
use App\Http\Resources\CostResource;
use App\Models\Cost;
use App\Http\Services\CostService;

class CostController extends Controller
{
    // Service to handle cost-related operations
    public $costService;

    // Constructor to inject the CostService
    public function __construct(CostService $costService)
    {
        $this->costService = $costService;
    }

    public function index()
    {
        // Retrieve all costs from the service and return the collection of costs as a resource
        $dataIndex = $this->costService->index();
        return CostResource::collection($dataIndex);
    }

    public function store(StoreCostRequest $request)
    {
        // Create a new cost using the service
        $dataStore = $this->costService->store($request);
        return response()->json(['message' => 'Cost stored successfully', 'cost' => $dataStore], 201);
    }

    public function show(Cost $cost)
    {
        // Return the cost as a resource
        return new CostResource($cost);
    }

    public function update(UpdateCostRequest $request, Cost $cost)
    {
        // Update the cost using the service
        $dataUpdate = $this->costService->update($cost, $request);
        return response()->json(['message' => 'Cost updated successfully', 'updatedCost' => $dataUpdate], 201);
    }

    public function destroy(Cost $cost)
    {
        // Delete the cost using the service
        $this->costService->destroy($cost);
        return response()->json(['message' => 'Cost deleted successfully'], 201);
    }
}


