<?php

namespace App\Http\Services;
use App\Models\Device;

class DeviceService
{
    // Method to retrieve all devices
    public function index()
    {
        // Retrieve all devices
        $allDevice = Device::all();
        return $allDevice;
    }

    // Method to store a new device
    public function store($request)
    {
        // Extract all request data
        $requestData = $request->all();

        // Create a new device using the extracted data
        $storedDevice = Device::create($requestData);

        // Return the newly stored device
        return $storedDevice;
    }

    // Method to update an existing device
    public function update(Device $device, $request)
    {
        // Extract all request data
        $requestData = $request->all();

        // Update the specified device with the extracted data
        $updatedDevice = $device->update($requestData);

        // Return whether the update was successful
        return $updatedDevice;
    }

    // Method to delete a device
    public function destroy(Device $device)
    {
        // Delete the specified device
        $device->delete();
    }
}

