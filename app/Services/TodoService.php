<?php

namespace App\Services;

use App\Models\Todo;
use Illuminate\Support\Carbon;
use App\Repositories\TodoRepository;

class TodoService
{
    private TodoRepository $todoRepository;
    public function __construct()
    {
        $this->todoRepository = new TodoRepository();
    }
    
    /**
     * NOTE: untuk membuat todo baru
     *
     * @param  array $formData
     * @return \App\Models\Todo
     */
    public function createTodo(array $formData)
    {
        $data['category'] = isset($formData['category']) ? $formData['category'] : null;
        $data['task'] = $formData['task'];
        $data['priority'] = isset($formData['priority']) ? $formData['priority'] : 5 ;
        
        $day = isset($formData['dueDay']) ? $formData['dueDay'] : now()->day;
        $month = isset($formData['dueMonth']) ? $formData['dueMonth'] : now()->month;
        $year = isset($formData['dueYear']) ? $formData['dueYear'] : now()->year;
        $data['dueDate'] = Carbon::createFromDate($year, $month, $day);


        $todo = $this->todoRepository->create($data);
        return $todo;
    }

    /**
     * NOTE: untuk membuat todo baru
     *
     * @return \App\Models\Todo
     */
    public function getTodos()
    {
        $query = $this->todoRepository->orderBy(['dueDate', 'priority'], ['asc', 'asc']);
        
        $todos = $this->todoRepository->get($query);
        return $todos;
    }

    /**
     * NOTE: untuk membuat todo baru
     *
     * @param  string $id
     * @return \App\Models\Todo
     */
    public function getTodo($id)
    {
        $todo = $this->todoRepository->getById($id);
        return $todo;
    }

    /**
     * NOTE: untuk mengubah data todo
     *
     * @param \App\Models\Todo $todo
     * @param  array $formData
     * @return \App\Models\Todo
     */
    public function updateTodo(Todo $todo, array $formData)
    {
        if(isset($formData['category'])){
            $data['category'] = $formData['category'];
        }

        if(isset($formData['task'])){
            $data['task'] = $formData['task'];
        }

        if(isset($formData['priority'])){
            $data['priority'] = $formData['priority'];
        }

        if(isset($formData['dueDay']) || isset($formData['dueMonth']) || isset($formData['dueYear'])){
            $day = isset($formData['dueDay']) ? $formData['dueDay'] : $todo->dueDate->day;
            $month = isset($formData['dueMonth']) ? $formData['dueMonth'] : $todo->dueDate->month;
            $year = isset($formData['dueYear']) ? $formData['dueYear'] : $todo->dueDate->year;
            $data['dueDate'] = Carbon::createFromDate($year, $month, $day);
        }

        if(isset($formData['complete'])){
            $data['complete'] = $formData['complete'] == 1 ? true : false;
        }

        $todo = $this->todoRepository->update($todo, $data);
        return $todo;
    }

    /**
     * NOTE: untuk menghapus todo
     *
     * @param \App\Models\Todo $todo
     * @return void
     */
    public function deleteTodo(Todo $todo)
    {
        $this->todoRepository->delete($todo);
    }
}
