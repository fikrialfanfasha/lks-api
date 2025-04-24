<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name'  => 'required|string|max:255',
            'username'   => 'required|string|min:3|max:255|unique:users,username',
            'password'   => 'required|string|min:6',
            'bio'        => 'required|string|max:100',
            'is_private' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'full_name'  => $request->full_name,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'bio'        => $request->bio,
            'is_private' => $request->is_private ?? false,
            'api_token'  => Str::random(60), 
        ]);

        return response()->json([
            'message' => 'Register success',
            'token' => $user->api_token,
            'user' => [
                'full_name' => $user->full_name,
                'username'  => $user->username,
                'bio'       => $user->bio,
                'is_private'=> $user->is_private,
            ]
        ], 201);
    }
}

