<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

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
        $user = $this->adminService->getUsers();
        return response()->json($user);
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
        return response()->json($user);
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
        $validatedData = Validator::make($request->all(), [
            'role' => 'in:reguler,admin,super-admin',
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'password' => Rules\Password::defaults(),
        ])->validated();

        $user = $this->adminService->getUserById($id);
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $this->adminService->updateUser($user, $validatedData);
        return response()->json($user);
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
            return response()->json([
                'message' => 'User not found'
            ], 404);            
        }

        $this->adminService->deleteUser($user);
        return response()->json([
            'message' => 'Successfully deleted user'
        ]);
    }
}
