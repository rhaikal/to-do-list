<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function __construct($user = null, string $message = null, $authorization = null)
    {
        parent::__construct($user);
        $this->message = $message;
        $this->token = isset($authorization['token']) ? $authorization['token'] : null;
        $this->exp = isset($authorization['exp']) ? $authorization['exp'] : 60;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($request->routeIs('user.index')){
            return [
                '_id' => $this->id,
                'role' => $this->role,
                'name' => $this->name,
                'email' => $this->email
            ];
        } else {
            return [
                'message' => $this->message,
                'data' => [
                    'user' => [
                        '_id' => $this->id,
                        'role' => $this->role,
                        'name' => $this->name,
                        'email' => $this->email
                    ],
                    $this->mergeWhen(!empty($this->token), [
                        'authorization' => [
                            'token' => $this->token,
                            'type' => 'Bearer',
                            'expired' => $this->exp * 60,
                        ]
                    ])
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
        if($request->routeIs('auth.register')){
            $response->setStatusCode(201);
        }
    }
}
