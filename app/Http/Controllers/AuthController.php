<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
Use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\Rules;
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        if(!request()->expectsJson()){
            abort(404);
        }   
        $this->authService = new AuthService();
    }

    public function login(Request $request)
    {
        $validatedData = validator($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ])->validated();

        $token = $this->authService->login($validatedData);
        if(!$token){
            throw new AuthenticationException;
        }

        $user = $this->authService->data();        
        return new UserResource($user, 'Successfully logged in', $token);
    }

    public function register(Request $request)
    {
        $validatedData = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ])->validated();
    
        $token = $this->authService->register($validatedData);
        $user = $this->authService->data();
        return new UserResource($user, 'Successfully registered new user', $token);
    }

    public function data()
    {
        $user = $this->authService->data();
        return new UserResource($user, 'Successfully get user data');
    }

    public function logout()
    {
        $this->authService->logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        $token = $this->authService->refresh();
        $user = $this->authService->data();
        return new UserResource($user, 'Successfully refreshed authentication token', $token);
    }
}
