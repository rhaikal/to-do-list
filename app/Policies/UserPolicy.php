<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        switch ($user->role) {
            case 'super-admin':
                return true;
            case 'admin':
                return $model->role != 'super-admin' ? Response::allow() : Response::denyAsNotFound();
            default:
                return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        switch ($user->role) {
            case 'super-admin':
                return true;
            case 'admin':
                return $model->role != 'super-admin' ? Response::allow() : Response::denyAsNotFound();
            default:
                return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        switch ($user->role) {
            case 'super-admin':
                return true;
            case 'admin':
                return $model->role != 'super-admin' ? Response::allow() : Response::denyAsNotFound();
            default:
                return false;
        }
    }
}
