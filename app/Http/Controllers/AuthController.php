<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
Use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        if(!request()->expectsJson()){
            abort(404);
        }   
    }

    public function login(Request $request)
    {
        $validatedData = validator($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ])->validated();

        $token = Auth::attempt($validatedData);
        if(!$token){
            throw new AuthenticationException;
        }

        $user = Auth::user();
        return response()->json([
            'message' => 'Successfully logged in',
            'data' => [
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expired' => Auth::factory()->getTTL() * 60,
                ]
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validatedData = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ])->validated();
    
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user = User::create($validatedData);
        
        $token = Auth::login($user); 
        return response()->json([
            'message' => 'Successfully registered new user',
            'data' => [
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expired' => Auth::factory()->getTTL() * 60,
                ]
            ]
        ], 201);
    }

    public function data()
    {
        return response()->json([
            'message' => 'Successfully called user data',
            'data' => [
                'user' => Auth::user()
            ]
        ]);
    }

    public function logout()
    {
        auth()->logout();
        
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'message' => 'Successfully refreshed authentication token',
            'data' => [
                'user' => Auth::user(),
                'authorization' => [
                    'token' => Auth::refresh(),
                    'type' => 'Bearer',
                    'expired' => Auth::factory()->getTTL() * 60,
                ]
            ]
        ]);
    }
}
