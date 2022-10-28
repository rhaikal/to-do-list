<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Validation\Rules;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->role == 'admin'){
            $user = $this->userService->searchUser('role', '!=', 'super-admin');
        } else {
            $user = $this->userService->getUsers();
        }

        return UserResource::collection($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if(auth()->user()->role == 'admin' && $user->role == 'super-admin'){
            throw new NotFoundHttpException;
        }

        return new UserResource($user, 'Successfully get user data');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if(auth()->user()->role == 'admin' && $request->has('role')){
            throw new NotFoundHttpException;
        }

        $validatedData = Validator::make($request->all(), [
            'role' => 'in:reguler,admin,super-admin',
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'password' => Rules\Password::defaults(),
        ])->validated();

        $this->userService->updateUser($user, $validatedData);
        return new UserResource($user, 'Successfully updated user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(auth()->user()->role == 'admin' && $user->role == 'super-admin'){
            throw new NotFoundHttpException;
        }

        $this->userService->deleteUser($user);
        return response()->json([
            'message' => 'Successfully deleted user'
        ]);
    }

    public function login(Request $request)
    {
        $validatedData = validator($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ])->validated();

        $auth = $this->userService->login($validatedData);
        if(empty($auth['token'])){
            throw new AuthenticationException;
        }
        
        $user = $this->userService->getUserAuth();        
        return new UserResource($user, 'Successfully logged in', $auth);
    }

    public function register(Request $request)
    {
        $validatedData = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ])->validated();
    
        $user = $this->userService->getUserAuth();
        $token = $this->userService->register($validatedData);
        return new UserResource($user, 'Successfully registered new user', $token);
    }

    public function data()
    {
        $user = $this->userService->getUserAuth();
        return new UserResource($user, 'Successfully get user data');
    }

    public function setting(Request $request)
    {
        $validatedData = Validator($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|max:255',
            'password' => ['confirmed', Rules\Password::defaults()]
        ])->validated();

        $user = $this->userService->getUserAuth();
        $this->userService->updateUser($user, $validatedData);
        return new UserResource($user, 'Successfully get user data');
    }

    public function delete()
    {
        $user = $this->userService->getUserAuth();
        return response()->json([
            'message' => 'Successfully deleted user'
        ]);
    }

    public function logout()
    {
        $this->userService->logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        $user = $this->userService->getUserAuth();
        $auth = $this->userService->refresh();
        return new UserResource($user, 'Successfully refreshed authentication token', $auth);
    }
}
