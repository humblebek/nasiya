<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthAdminService
{
    public function index()
    {
        // Retrieve all users with the role 'admin'
        $allAdmins = User::role('admin')->get();
        return $allAdmins;
    }

    public function show($id)
    {
        // Find the user by ID or throw an exception
        $user = User::findOrFail($id);
        return $user;
    }

    public function store($request)
    {
        // Validate the incoming request data
        $validateUser = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // If validation fails, return error response
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        // Handle photo upload if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Store the photo and get its path
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/photos', $photoName);
            $photoPath = 'storage/photos/' . $photoName;
        }

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' => $photoPath,
        ]);

        // Assign the 'admin' role to the user
        $user->assignRole([2]);
        $user['roles'] = $user->getRoleNames();

        return $user;
    }

    public function update($request, $id)
    {
        // Find the user by ID or throw an exception
        $user = User::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'password' => 'sometimes|required|confirmed',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'required_with:password',
        ]);

        // Check if current password is provided and matches the user's password
        if ($request->has('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The current password is incorrect.',
                ], 422);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'The current password is required for verification.',
            ], 422);
        }

        // Handle password update if provided
        if ($request->has('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Handle photo update if provided
        if ($request->hasFile('photo')) {
            // Delete the previous photo if exists
            if ($user->photo) {
                Storage::delete($user->photo);
            }

            // Store the new photo and update its path
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/photos', $photoName);
            $photoPath = 'storage/photos/' . $photoName;
            $user->photo = $photoPath;
        }

        // Save the updated user data
        $user->save();

        return $user;
    }

    public function destroy($id)
    {
        // Find the user by ID or throw an exception, then delete
        $user = User::findOrFail($id);
        $user->delete();
    }

    public function login($request)
    {
        // Validate the incoming login request
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        // If validation fails, return error response
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        // Attempt to authenticate the user
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Email & Password does not match with our record.',
            ], 401);
        }

        // If authentication is successful, retrieve the user
        $user = User::where('email', $request->email)->first();

        return $user;
    }
}

