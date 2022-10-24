<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function __construct(string $message, string $token = null, User $user = null)
    {
        parent::__construct($user);
        $this->token = $token;
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
        return [
            'message' => $this->message,
            'data' => [
                'user' => !empty($this->user) ? $this->user : Auth::user(),
                $this->mergeWhen(!empty($this->token), [
                    'authorization' => [
                        'token' => $this->token,
                        'type' => 'Bearer',
                        'expired' => Auth::factory()->getTTL() * 60,
                    ]
                ])
            ]
        ];
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
