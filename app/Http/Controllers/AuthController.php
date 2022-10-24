<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
Use Illuminate\Auth\AuthenticationException;
use App\Http\Resources\AuthResource;
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

        $data['token'] = Auth::attempt($validatedData);
        if(!$data['token']){
            throw new AuthenticationException;
        }
        
        $data['message'] = 'Successfully logged in';
        return new AuthResource($data['message'], $data['token']);
    }

    public function register(Request $request)
    {
        $validatedData = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ])->validated();
    
        $validatedData['password'] = bcrypt($validatedData['password']);
        
        $data['user'] = User::create($validatedData);
        
        $data['token'] = Auth::login($data['user']); 
        
        $data['message'] = 'Successfully created new user';
        return new AuthResource($data['message'], $data['token'], $data['user']);
    }

    public function data()
    {
        $data['message'] = 'Successfully called user data';
        return new AuthResource($data['message']);
    }

    public function logout()
    {
        Auth::logout();
        
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        $data['message'] = 'Successfully refreshed authentication token';
        $data['token'] = Auth::refresh();
        return new AuthResource($data['message'], $data['token']);
    }
}
