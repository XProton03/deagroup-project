<?php

namespace App\Policies;

use App\Models\EmployementStatus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployementStatusPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employement::status');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployementStatus $employementStatus): bool
    {
        return $user->can('view_employement::status');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_employement::status');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployementStatus $employementStatus): bool
    {
        return $user->can('update_employement::status');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployementStatus $employementStatus): bool
    {
        return $user->can('delete_employement::status');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_employement::status');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmployementStatus $employementStatus): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmployementStatus $employementStatus): bool
    {
        return false;
    }
}
