<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
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

        $data = $this->authService->login($validatedData);
        
        if(!$data['token']){
            throw new AuthenticationException;
        }
        
        return new AuthResource($data['message'], $data['token']);
    }

    public function register(Request $request)
    {
        $validatedData = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ])->validated();
    
        $data = $this->authService->register($validatedData);
        return new AuthResource($data['message'], $data['token'], $data['user']);
    }

    public function data()
    {
        $data = $this->authService->data();
        return new AuthResource($data['message']);
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
        $data = $this->authService->refresh();
        return new AuthResource($data['message'], $data['token']);
    }
}
