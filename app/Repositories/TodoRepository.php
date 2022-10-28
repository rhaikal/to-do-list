<?php

namespace App\Repositories;

use App\Models\Todo;
use Illuminate\Support\Carbon;

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
     * Untuk mengambil sebagian data todo
     *
     * @param integer $perPage
     * @return \App\Models\Todo
     */
    public function getWithPaginate($perPage)
    {
        $todo = Todo::paginate($perPage);
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

    public function paginateByUserId($paginate, $userId, $option = null)
    {
        $query = Todo::query();
        if(!empty($option)){
            $query = $this->processOption($option, $query);
        }

        $query->where('user_id', $userId);
        $todos = $query->paginate($paginate);

        return $todos;
    }

    public function paginateAll($paginate, $option = null)
    {
        $query = Todo::query();
        if(!empty($option)){
            $query = $this->processOption($option, $query);
        }

        $todos = $query->paginate($paginate);

        return $todos;
    }

    public function processOption($option, $oldQuery)
    {
        if(isset($option['search'])){
            $search = $option['search'];
            if(isset($search['category'])){
                $query = $this->whereCategory($search['category'], $oldQuery);
            }
        }

        if(isset($option['today']) && $option['today']){
            $query = $this->whereToday($oldQuery);
        }

        if(isset($option['sort']) && $option['sort']){
            $query = $oldQuery->orderBy('priority', 'asc');
            $query = $oldQuery->orderBy('dueDates', 'asc');    
        }

        return $query;
    }

    public function whereCategory($value, $oldQuery)
    {
        $query = $oldQuery->where('category', 'LIKE', '%'.$value.'%');   

        return $query;
    }

    public function whereToday($oldQuery)
    {
        $start = Carbon::createFromTime();
        $end = Carbon::createFromTime(24);
        $query = $oldQuery->whereBetween('dueDate', [$start, $end]);
    
        return $query;
    }
}
