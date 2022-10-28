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
	 * Untuk mengambil semua user dengan paginate
	 */
	public function getWithPaginate($perPage)
	{
		$user = User::paginate($perPage);
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

	/**
	 * Untuk query mencari user berdasarkan field dan mendekati keyword
	 */
	public function whereLikes($field, $keyword, $oldQuery, $or = false)
	{
		if($or){
			$query = $oldQuery->orWhere($field, 'LIKE', '%'. $keyword .'%');
		}else {
			$query = $oldQuery->where($field, 'LIKE', '%'. $keyword .'%');
		}

		return $query;
	}

	/**
	 * Untuk mengambil semua user yang bukan super-admin dengan paginate
	 */
	public function paginateWithoutSuperAdmin($paginate, $search = null)
	{
		$query = User::query();
		if(!empty($search)){
			$query = $this->whereLikes('name', $search, $query);
			$query = $this->whereLikes('email', $search, $query, true);
		}

		$query = $query->where('role', '!=', 'super-admin');
		$users = $query->paginate($paginate);

		return $users;
	}

	/**
	 * Untuk mengambil semua user dengan paginate
	 */
	public function paginateAll($paginate, $search = null)
	{
		$query = User::query();
		if(!empty($search)){
			$query = $this->whereLikes('name', $search, $query);
			$query = $this->whereLikes('email', $search, $query, true);
		}

		$users = $query->paginate($paginate);

		return $users;
	}
}
?>