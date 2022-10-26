<?php 
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository(); 
    }

    /**
	 * NOTE: untuk melakukan login
	 */
    public function login(array $credentials)
    {
        $token = Auth::attempt($credentials);;

        return $token;
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
	 * NOTE: untuk mendapatkan data user yang login
	 */
    public function data()
    {
        $user = Auth::user();
        return $user;
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
        $token = Auth::refresh();
    
        return $token;
    }
}
?>