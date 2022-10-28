<?php

namespace App\Repositories;

use App\Models\Todo;

class TodoRepository
{    
    /**
     * Untuk membuat todo baru
     *
     * @param  mixed $data
     * @return \App\Models\Todo
     */
    public function create(array $data)
    {
        $dataSaved = [
            'user_id' => auth()->id(),
            'category' => $data['category'],
            'task' => $data['task'],
            'priority' => $data['priority'],
            'dueDate' => $data['dueDate'],
            'complete' => false
        ];

        $todo = Todo::create($dataSaved);
        return $todo;
    }
    
    /**
     * Untuk mengambil semua data todo
     *
     * @return \App\Models\Todo
     */
    public function getAll()
    {
        $todo = Todo::all();
        return $todo;
    }
    
    /**
     * Untuk mengambil data todo berdasarkan id
     *
     * @param  mixed $id
     * @return \App\Models\Todo
     */
    public function getById($id)
    {
        $todo = Todo::find($id);
        return $todo;
    }
    
    /**
     * Untuk mengubah data todo
     *
     * @param  \App\Models\Todo $todo
     * @param  mixed $data
     * @return \App\Models\Todo
     */
    public function update(Todo $todo, array $data)
    {
        $todo->update($data);
        return $todo;
    }
    
    /**
     * Untuk menghapus todo
     *
     * @param  \App\Models\Todo $todo
     * @return void
     */
    public function delete(Todo $todo)
    {
        $todo->delete();
    }
    
    /**
     * Untuk menambah data pada field todo
     *
     * @param  \App\Models\Todo $todo
     * @param  mixed $data
     * @return \App\Models\Todo
     */
    public function push(Todo $todo, string $field, string|array $data)
    {
        $todo->push($field, $data);
        
        // merefresh todo dikarenakan bug tidak memperbarui model setelah push pada mongodb
        $todo = Todo::find($todo->id);
        return $todo;
    }
    
    /**
     * Untuk menghapus values pada field todo
     *
     * @param  \App\Models\Todo $todo
     * @param  mixed $data
     * @return \App\Models\Todo
     */
    public function pull(Todo $todo, string $field, string|array $data)
    {
        $todo->pull($field, $data);
        
        // merefresh todo dikarenakan bug tidak memperbarui model setelah push pada mongodb
        $todo = Todo::find($todo->id);
        return $todo;
    }

    /**
     * Untuk query mengurutkan todo berdasarkan field
     *
     * @param  mixed $field
     * @param  mixed $sort
     * @param  \Illuminate\Database\Eloquent\Builder $oldQuery
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function orderBy(array|string $field, array|string $sort, $oldQuery = null)
    {
        $query = empty($oldQuery) ? Todo::query() : $oldQuery;
        
        if(is_array($field)){
            $query->orderBy($field[0], $sort[0]);
            for($i = 1; $i < count($field); $i++){
                $query->orderBy($field[$i], $sort[$i]);
            }
        } else {
            $query->orderBy($field, $sort);
        }

        return $query;
    }

    /**
     * Untuk query mencari todo berdasarkan field dan value
     *
     * @param  mixed $field
     * @param  mixed $operator
     * @param  mixed $value
     * @param  \Illuminate\Database\Eloquent\Builder $oldQuery
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function where(array|string $field, array|string $operator, $value, $oldQuery = null)
    {
        $query = empty($oldQuery) ? Todo::query() : $oldQuery;
        
        if(is_array($field)){
            $query->where($field[0], $operator[0], $value[0]);
            if($operator == '='){
                $query->where($field[0], $value[0]);
            }
            for($i = 1; $i < count($field); $i++){
                $query->where($field[$i], $operator[$i], $value[$i]);
                if($operator == '='){
                    $query->where($field[$i], $value[$i]);
                }
            }
        } else {
            if($operator == '='){
                $query->where($field, $value);
            } else {
                $query->where($field, $operator, $value);
            }
        }

        return $query;
    }

    /**
     * Untuk mendapatkan todo dari hasil query
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \App\Models\Todo $todo
     */
    public function get($query)
    {
        $todo = $query->get();
        
        return $todo;
    }
}
