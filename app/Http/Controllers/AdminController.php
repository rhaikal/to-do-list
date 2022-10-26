<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Validation\Rules;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    private AdminService $adminService;
    
    public function __construct()
    {
        if(!request()->expectsJson()){
            abort(404);
        }

        $this->adminService = new AdminService();
    }

    /**
     * Display a listing of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function showUsers()
    {
        if(auth()->user()->role == 'admin'){
            $user = $this->adminService->searchUser('role', '!=', 'super-admin');
        } else {
            $user = $this->adminService->getUsers();
        }

        return UserResource::collection($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUser($id)
    {
        $user = $this->adminService->getUserById($id);
        if(auth()->user()->role == 'admin' && $user->role == 'super-admin'){
            abort(404);
        }

        return new UserResource($user, 'Successfully get user data');
    }

    /**
     * Update the specified user in collection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        if(auth()->user()->role == 'admin' && $request->has('role')){
            abort(404);
        }

        $validatedData = Validator::make($request->all(), [
            'role' => 'in:reguler,admin,super-admin',
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'password' => Rules\Password::defaults(),
        ])->validated();

        $user = $this->adminService->getUserById($id);
        if(!$user){
            return new UserResource($user);
        }

        $this->adminService->updateUser($user, $validatedData);
        return new UserResource($user, 'Successfully updated user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyUser($id)
    {
        $user = $this->adminService->getUserById($id);
        if(!$user){
            return new UserResource($user);           
        }
        
        if(auth()->user()->role == 'admin' && $user->role == 'super-admin'){
            abort(404);
        }

        $this->adminService->deleteUser($user);
        return response()->json([
            'message' => 'Successfully deleted user'
        ]);
    }
}
