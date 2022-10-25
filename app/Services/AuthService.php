<?php 
namespace App\Services;

use App\Models\User;
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
        $data['token'] = Auth::attempt($credentials);
        $data['message'] = 'Successfully logged in';

        return $data;
    }

    /**
	 * NOTE: untuk melakukan register
	 */
    public function register(array $formData)
    {
        $formData['password'] = bcrypt($formData['password']);
        $data['user'] = $this->userRepository->create($formData);
        $data['token'] = Auth::login($data['user']); 
        $data['message'] = 'Successfully created new user';

        return $data;
    }

    /**
	 * NOTE: untuk mendapatkan data user
	 */
    public function data()
    {
        $data['message'] = 'Successfully called user data';
        
        return $data;
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
        $data['message'] = 'Successfully refreshed authentication token';
        $data['token'] = Auth::refresh();
    
        return $data;
    }
}
?>