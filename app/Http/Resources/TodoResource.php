<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    public function __construct($todo = null, string $message = null)
    {
        parent::__construct($todo);
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($request->routeIs('todo.index')){
            return [
                '_id' => $this->id,
                'user_id' => $this->user_id, 
                'category' => $this->category,
                'priority' => $this->priority,
                'task' => $this->task,
                'dueDate' => $this->dueDate,
                'complete' => $this->complete
            ];
        } else {
            return [
                'message' => $this->message,
                'data' => [
                    'todo' => [
                        '_id' => $this->id,
                        'user_id' => $this->user_id, 
                        'category' => $this->category,
                        'priority' => $this->priority,
                        'task' => $this->task,
                        'dueDate' => $this->dueDate,
                        'complete' => $this->complete
                    ],
                ]
            ];
        }
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        if($request->routeIs('todo.store')){
            $response->setStatusCode(201);
        }
    }
}
