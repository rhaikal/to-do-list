<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function __construct($user = null, string $message = null, $token = null)
    {
        parent::__construct($user);
        $this->message = !empty($user) ? $message : 'User not found';
        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($request->routeIs('users')){
            return [
                '_id' => $this->id,
                'role' => $this->role,
                'name' => $this->name,
                'email' => $this->email
            ];
        } else {
            return [
                'message' => $this->message,
                $this->mergeWhen(!empty($this->id), [
                    'data' => [
                        'user' => [
                            '_id' => !empty($this->id) ? $this->id : null,
                            'role' => !empty($this->role) ? $this->role : null,
                            'name' => !empty($this->name) ? $this->name : null,
                            'email' => !empty($this->email) ? $this->email : null
                        ],
                        $this->mergeWhen(!empty($this->token), [
                            'authorization' => [
                                'token' => $this->token,
                                'type' => 'Bearer',
                                'expired' => Auth::factory()->getTTL() * 60,
                            ]
                        ])
                    ]
                ])
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
        if(empty($this->user)){
            $response->setStatusCode(404);
        }

        if($request->routeIs('auth.register')){
            $response->setStatusCode(201);
        }
    }
}
