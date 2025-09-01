<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = $request->user(); // authenticated user

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'password'          => 'nullable|min:6|confirmed', // confirmed => needs password_confirmation field
        ], [
            'name.required'     => 'Enter Name',
            'password.confirmed'=> 'Password and Confirm Password do not match',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $user->name = $request->name;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 'Profile updated successfully',
                'data'    => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
