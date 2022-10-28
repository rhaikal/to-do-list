<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Services\TodoService;
use App\Http\Resources\TodoResource;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    private TodoService $todoService;

    public function __construct()
    {
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
    
    public function index(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'today' => 'boolean',
            'category' => 'string|max:25',
        ])->validated();

        if(auth()->user()->role == 'admin' || auth()->user()->role == 'super-admin'){
            $todos = $this->todoService->getTodos();
        }else{
            $todos = $this->todoService->getOwnTodos($validatedData);
        }

        if($todos->isEmpty()){
            return response()->json([
                'message' => 'Todo not found'
            ], 404);
        }

        return TodoResource::collection($todos, 'Successfully called all todo');
    }

    public function show(Todo $todo)
    {
        $this->authorize('view', $todo);
        
        return new TodoResource($todo, 'Successfully called data todo');
    }

    public function update(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo);
        
        $validatedData = Validator::make($request->all(), [
            'category' => 'string|max:15',
            'priority' => 'integer|min:0|max:5',
            'task' => 'string|max:255',
            'dueDay' => 'integer|min:1|max:31',
            'dueMonth' => 'integer|min:1|max:12',
            'dueYear' => 'integer|min:' . now()->year,
            'complete' => 'boolean'
        ])->validated();

        $this->todoService->updateTodo($todo, $validatedData);
        return new TodoResource($todo, 'Successfully updated data todo');
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);

        $this->todoService->deleteTodo($todo);
        return response()->json([
            'message' => 'Successfully deleted todo'
        ]);
    }
}
