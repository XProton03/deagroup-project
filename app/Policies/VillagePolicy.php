<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Village;
use Illuminate\Auth\Access\Response;

class VillagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_village');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Village $village): bool
    {
        return $user->can('view_village');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_village');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Village $village): bool
    {
        return $user->can('update_village');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Village $village): bool
    {
        return $user->can('delete_village');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Village $village): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_village');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Village $village): bool
    {
        return false;
    }
}
