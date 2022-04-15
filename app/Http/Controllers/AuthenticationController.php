<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function createAccount (Request $request)
    {
        $attr = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|unique:user,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'username' => $attr['username'],
            'password' => bcrypt($attr['password']),
            'email' => $attr['email'],
        ]);

        return $this->success([
            'token' => $user->createToken('tokens')->plainTextToken
        ]);
    }


    public function signin(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        if(!Auth::attempt($attr)){
            return $this->error('Credentials not match', 401);
        }

        return $this->success([
            'token' => auth()->user()->createToken('Api token')->plainTextToken
        ]);
    }

    public function signout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Token Revoked'
        ];
    }
}
