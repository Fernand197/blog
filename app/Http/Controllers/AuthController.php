<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public $errors;
    
    public function register(Request $request): JsonResponse
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users|max:255',
            'email'    => 'required|email|unique:users|max:255',
            'password' => 'required|min:6|confirmed',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        }
        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message'      => 'User created successfully',
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }
    
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors,
            ], 400);
        }
        
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message'      => 'User logged in successfully',
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }
    
    public function me()
    {
        return auth()->user();
    }
    
    public function logout(): JsonResponse
    {
        // auth()->user()->tokens()->delete();
        auth()->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'You have successfully logged out and the token was successfully deleted',
        ]);
    }
}
