<?php 
namespace App\Repositories;

use App\Models\User;

class UserRepository
{	
	/**
	 * Untuk membuat user baru
	 */
	public function create(array $data)
	{
		$dataSaved = [
			'role' => 'reguler',
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => $data['password'],
		];
		
		$user = User::create($dataSaved);
		
		return $user;
	}
	
	/**
	 * Untuk mengambil semua user
	 */
	public function getAll()
	{
		$user = User::all();
		return $user;
	}
	
	/**
	 * Untuk mengambil user berdasarkan id
	 */
	public function getById(string $id)
	{
		$user = User::find($id);
		return $user;
	}

	/**
	 * Untuk mengupdate user
	 */
	public function update(User $user, array $data)
	{
		$user->update($data);

		return $user;
	}

	/**
	 * Untuk menghapus user 
	 */
	public function delete(User $user)
	{
		$user->delete();
	}
}
?>