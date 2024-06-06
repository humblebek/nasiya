<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Http\Services\DeviceService;
use App\Models\Device;

class DeviceController extends Controller
{
    // Service to handle device-related operations
    public $deviceService;

    // Constructor to inject the DeviceService
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function index()
    {
        // Retrieve all devices from the service and return the collection of devices as a resource
        $dataIndex = $this->deviceService->index();
        return DeviceResource::collection($dataIndex);
    }

    public function store(StoreDeviceRequest $request)
    {
        // Create a new device using the service
        $dataStore = $this->deviceService->store($request);
        return response()->json(['message' => 'Device created successfully', 'device' => $dataStore], 201);
    }

    public function show(Device $device)
    {
        // Return the device as a resource
        return new DeviceResource($device);
    }

    public function update(UpdateDeviceRequest $request, Device $device)
    {
        // Update the device using the service
        $dataUpdate = $this->deviceService->update($device, $request);
        return response()->json(['message' => 'Device updated successfully', 'device' => $dataUpdate], 201);
    }

    public function destroy(Device $device)
    {
        // Delete the device using the service
        $this->deviceService->destroy($device);
        return response()->json(['message' => 'Device deleted successfully'], 201);
    }
}
