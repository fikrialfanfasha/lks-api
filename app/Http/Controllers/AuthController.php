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
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Wrong username or password'
            ], 401);
        }

        $token = Str::random(60);

        $user->api_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'bio' => $user->bio,
                'is_private' => $user->is_private,
                'created_at' => $user->created_at
            ]
        ]);
    }
    public function logout(Request $request)
{
    $authHeader = $request->header('Authorization');

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $token = substr($authHeader, 7);

    $user = \App\Models\User::where('api_token', $token)->first();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $user->api_token = null;
    $user->save();

    return response()->json(['message' => 'Logout success']);
}

}

