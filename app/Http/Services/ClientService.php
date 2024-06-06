<?php


namespace App\Http\Services;

use App\Models\Client;


class ClientService
{
    // Method to retrieve all clients
    public function index()
    {
        // Retrieve all clients
        $allClient = Client::all();
        return $allClient;
    }

    // Method to store a new client
    public function store($request)
    {
        // Validate the request and retrieve all input data
        $validatedData = $request->validated();

        // Check if a file is uploaded
        if ($request->hasFile('file_passport')) {
            // Upload the file and store the path in the database
            $file_path = $request->file('file_passport')->store('passports', 'public');
            $validatedData['file_passport'] = $file_path;
        }

        // Create the client
        $storedClient = Client::create($validatedData);

        return $storedClient;
    }

    // Method to update an existing client
    public function update($request, Client $client)
    {
        $requestData = $request->all();
        // Check if a new file is uploaded
        if ($request->hasFile('file_passport')) {
            // If the client already has a file, delete it
            if (isset($client->file_passport) && file_exists(public_path('/files/clientsPassport/' . $client->file_passport))) {
                unlink(public_path('/files/clientsPassport/' . $client->file_passport));
            }
            // Upload the new file and update the path in the database
            $requestData['file_passport'] = $this->fileUpload();
        }
        // Update the client with the new data
        $updatedClient =  $client->update($requestData);
        return $updatedClient;
    }

    // Method to delete a client
    public function destroy(Client $client)
    {
        // If the client has a file, delete it from storage
        if (isset($client->file_passport) && file_exists(public_path('/files/clientsPassport/' . $client->file_passport))) {
            unlink(public_path('/files/clientsPassport/' . $client->file_passport));
        }
        // Delete the client from the database
        $client->delete();
    }

    // Method to upload a file
    public function fileUpload()
    {
        $file = request()->file('file_passport');
        $fileName = time() . '-' . $file->getClientOriginalName();
        // Move the file to the designated folder
        $file->move('files/clientsPassport/', $fileName);
        return $fileName;
    }
}

