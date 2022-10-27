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
}
