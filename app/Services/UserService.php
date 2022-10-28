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
            $formData['passsword'] = bcrypt($formData['password']);
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
    public function getUsers($keyword = null)
    {
        // mencari berdasarkan name / email bila ada keyword
        if(!empty($keyword)){
            $query = $this->userRepository->orWhere(['name', 'email'], ['LIKE', 'LIKE'], ['%' . $keyword . '%', '%' . $keyword . '%']);
        } else {
            $query = null;
        }
        
        // untuk mencari agar admin tidak mendapatkan user super-admin
        if(auth()->user()->role == 'admin'){
            $query = $this->userRepository->where('role', '!=', 'super-admin', $query);
        }
        
        $user = $this->userRepository->paginate(5, $query);
        return $user;
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
        $token = Auth::login($user);
        
        return $token;
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
