<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\AuthAdminService;


class AuthController extends Controller
{
    // Dependency injection of AuthAdminService into the controller
    public $authAdminService;

    // Constructor method to initialize the authAdminService
    public function __construct(AuthAdminService $authAdminService)
    {
        $this->authAdminService = $authAdminService;
    }

    public function allAdmins()
    {
        // Fetch all admins from the service and return a JSON response with the retrieved data
        $dataIndex = $this->authAdminService->index();
        return response()->json(['message' => 'All admins retrieved successfully', 'admins' => $dataIndex], 200);
    }

    public function showAdmin($id)
    {
        // Fetch admin data by ID from the service
        $dataShow = $this->authAdminService->show($id);
        return response()->json(['message' => 'Admin data retrieved successfully', 'user' => $dataShow], 200);
    }

    public function createAdmin(Request $request)
    {
        // Store the new admin data using the service
        $user = $this->authAdminService->store($request);

        // If the service returns a JsonResponse (e.g., validation errors), return it directly
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        // Return a JSON response with the created user data and a token
        return response()->json([
            'message' => 'User created successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken,
            'user' => $user,
        ], 200);
    }

    public function updateAdmin(Request $request, $id)
    {
        // Update the admin data using the service
        $dataUpdate = $this->authAdminService->update($request, $id);

        // Return a JSON response with the updated data
        return response()->json([
            'message' => 'User data updated successfully',
            'user' => $dataUpdate,
        ], 200);
    }

    public function deleteAdmin($id)
    {
        // Delete the admin using the service
        $this->authAdminService->destroy($id);

        // Return a JSON response confirming the deletion
        return response()->json([
            'message' => 'Admin deleted successfully',
        ], 200);
    }

    // Method for admin login
    public function loginAdmin(Request $request)
    {
        // Authenticate the admin using the service
        $dataLogin = $this->authAdminService->login($request);

        // Return a JSON response with a token for the logged-in admin
        return response()->json([
            'message' => 'User Logged In Successfully',
            'token' => $dataLogin->createToken("API TOKEN")->plainTextToken
        ], 200);
    }
}

