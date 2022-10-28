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

	/**
	 * UUntuk query mencari user berdasarkan field dan value
	 */
	public function where(array|string $field, array|string $operator, $keyword, $oldQuery = null)
	{
		$query = empty($oldQuery) ? User::query() : $oldQuery;

		if(is_array($field)){
            $query->where($field[0], $operator[0], $keyword[0]);
            if($operator == '='){
                $query->where($field[0], $keyword[0]);
            }
            for($i = 1; $i < count($field); $i++){
                $query->orWhere($field[$i], $operator[$i], $keyword[$i]);
                if($operator == '='){
                    $query->orWhere($field[$i], $keyword[$i]);
                }
            }
        } else {
            if($operator == '='){
                $query->where($field, $keyword);
            } else {
                $query->where($field, $operator, $keyword);
            }
        }

		return $query;
	}

	/**
	 * Untuk query mencari todo berdasarkan field dan value menggunakan orWhere
	 */
	public function orWhere(array|string $field, array|string $operator, $keyword, $oldQuery = null)
	{
		$query = empty($oldQuery) ? User::query() : $oldQuery;

		if(is_array($field)){
            $query->where($field[0], $operator[0], $keyword[0]);
            if($operator == '='){
                $query->where($field[0], $keyword[0]);
            }
            for($i = 1; $i < count($field); $i++){
                $query->orWhere($field[$i], $operator[$i], $keyword[$i]);
                if($operator == '='){
                    $query->orWhere($field[$i], $keyword[$i]);
                }
            }
        } else {
            if($operator == '='){
                $query->orWhere($field, $keyword);
            } else {
                $query->orWhere($field, $operator, $keyword);
            }
        }

		return $query;
	}

	

	/**
     * Untuk mendapatkan todo dengan paginate dari hasil query
     */
    public function paginate(int $perPage, $oldQuery = null)
    {
		$query = empty($oldQuery) ? User::query() : $oldQuery;

        $user = $query->paginate($perPage);
        
        return $user;
    }
}
?>