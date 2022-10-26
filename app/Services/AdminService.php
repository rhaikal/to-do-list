<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class AdminService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
	 * NOTE: untuk mengambil semua users di collection users
	 */
    public function getUsers()
    {
        $users = $this->userRepository->getAll();
        return $users;
    }

    /**
	 * NOTE: untuk mengambil user berdasarkan id di collection users
	 */
    public function getUserById(string $id)
    {
        $user = $this->userRepository->getById($id);
        return $user;
    }

    /**
	 * NOTE: untuk memperbarui data user
	 */
    public function updateUser(User $user, array $formData)
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
    public function deleteUser(User $user)
    {
        $this->userRepository->delete($user);
    }

    /**
	 * NOTE: untuk menghapus user
	 */
    public function searchUser(string $keyword, string $operator, $compare)
    {
        $user = $this->userRepository->search($keyword, $operator, $compare);
        return $user;
    }
}
