<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Services\TodoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    private TodoService $todoService;

    public function __construct()
    {
        if(!request()->expectsJson()){
            abort(404);
        }

        $this->todoService = new TodoService();
    }
    
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'category' => 'string|max:15',
            'priority' => 'integer|min:0|max:5',
            'task' => 'required|string|max:255',
            'dueDay' => 'integer|min:1|max:31',
            'dueMonth' => 'integer|min:1|max:12',
            'dueYear' => 'integer|min:' . now()->year
        ])->validated();
    
        $todo = $this->todoService->createTodo($validatedData);

        return new TodoResource($todo, 'Successfully created new todo');
    }
    
    public function index()
    {
        $todos = $this->todoService->getTodos();
        return TodoResource::collection($todos, 'Successfully called all todo');
    }

    public function show(string $id)
    {
        $todo = $this->todoService->getTodo($id);
        return new TodoResource($todo, 'Successfully called data todo');
    }

    public function update(Request $request, string $id)
    {
        $validatedData = Validator::make($request->all(), [
            'category' => 'string|max:15',
            'priority' => 'integer|min:0|max:5',
            'task' => 'string|max:255',
            'dueDay' => 'integer|min:1|max:31',
            'dueMonth' => 'integer|min:1|max:12',
            'dueYear' => 'integer|min:' . now()->year,
            'complete' => 'boolean'
        ])->validated();

        $todo = $this->todoService->getTodo($id);
        if(!$todo){
            return new TodoResource($todo);
        }

        $this->todoService->updateTodo($todo, $validatedData);
        return new TodoResource($todo, 'Successfully updated data todo');
    }

    public function delete(string $id)
    {
        $todo = $this->todoService->getTodo($id);
        if(!$todo){
            return new TodoResource($todo);
        } 

        $this->todoService->deleteTodo($todo);
        return response()->json([
            'message' => 'Successfully deleted todo'
        ]);
    }
}
