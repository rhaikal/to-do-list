<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Validation\Rules;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\Rule;
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
    public function index(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'search' => 'string'
        ])->validated();

        if(isset($validatedData['search'])){
            $user = $this->userService->getUsers($validatedData['search']);
        } else {
            $user = $this->userService->getUsers();
        }

        if($user->isEmpty()) {
            throw new NotFoundHttpException;
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
        $this->authorize('view', $user);
        
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
        $this->authorize('update', $user);
        
        $validatedData = Validator::make($request->all(), [
            'role' => ['in:reguler,admin,super-admin', Rule::excludeIf(auth()->user()->role == 'admin')],
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
        $this->authorize('delete', $user);

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
    
        $token = $this->userService->register($validatedData);
        $user = $this->userService->getUserAuth();
        return new UserResource($user, 'Successfully registered new user', $token);
    }

    public function data()
    {
        $user = $this->userService->getUserAuth();
        return new UserResource($user, 'Successfully get user data');
    }

    public function setting(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|max:255',
            'password' => ['confirmed', Rules\Password::defaults()]
        ])->validated();

        $user = $this->userService->getUserAuth();
        $this->userService->updateUser($user, $validatedData);
        return new UserResource($user, 'Successfully updated user data');
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
