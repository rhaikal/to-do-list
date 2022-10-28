<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(); 
    }
    
    /**
	 * NOTE: untuk mendapatkan data user yang login
	 */
    public function getUserAuth()
    {
        $user = Auth::user();
        return $user;
    }

    /**
	 * NOTE: untuk memperbarui data user
	 */
    public function updateUser(User|Authenticatable $user, array $formData)
    {
        if(isset($formData['password'])){
            $formData['password'] = bcrypt($formData['password']);
        }

        $user = $this->userRepository->update($user, $formData);
        return $user;
    }

    /**
	 * NOTE: untuk menghapus user
	 */
    public function deleteUser(User|Authenticatable $user)
    {
        $this->userRepository->delete($user);
    }

    /**
	 * NOTE: untuk mengambil semua user
	 */
    public function getUsers($data)
    {
        if(isset($data['search'])){
            $keyword = $data['search'];
        } else {
            $keyword = null;
        }

        if(auth()->user()->role == 'admin'){
            $users = $this->userRepository->paginateWithoutSuperAdmin(5, $keyword); 
        } else {
            $users = $this->userRepository->paginateAll(5, $keyword);
        }

        return $users;
    }

    /**
	 * NOTE: untuk melakukan login
	 */
    public function login(array $formData)
    {
        $credentials = [
            'email' => $formData['email'], 
            'password' => $formData['password']
        ];

        if(isset($formData['remember']) && $formData['remember']){
            Auth::factory()->setTTL(60 * 24);
            $auth['exp'] = 60 * 24;
        }

        $auth['token'] = Auth::attempt($credentials);
        return $auth;
    }

    /**
	 * NOTE: untuk melakukan register
	 */
    public function register(array $formData)
    {
        $formData['password'] = bcrypt($formData['password']);
        $user = $this->userRepository->create($formData);
        $auth['token'] = Auth::login($user);
        
        return $auth;
    }

    /**
	 * NOTE: untuk melakukan logout
	 */
    public function logout()
    {
        Auth::logout();
    }

    /**
	 * NOTE: untuk melakukan refresh
	 */
    public function refresh()
    {
        Auth::factory()->setTTL(60 * 24);
        $auth['exp'] = 60 * 24;
        $auth['token'] = Auth::refresh();
    
        return $auth;
    }
}
