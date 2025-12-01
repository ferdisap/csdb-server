<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolesPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    // public function manage_oauth_client(User $user, Role $role): bool
    public function manage_oauth_client(User $user): bool
    {
        return $user->roles ? $user->roles->isInRole('admin') : false;
    }
    
}
