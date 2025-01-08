<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function before(Admin|User $user): ?bool
    {
        if ($user instanceof User && $user->isOwner(Filament::getTenant())) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin|User $user): bool
    {
        return $user->can('view_any_role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin|User $user, Role $role): bool
    {
        return $user->can('view_role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin|User $user): bool
    {
        return $user->can('create_role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin|User $user, Role $role): bool
    {
        return $user->can('update_role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin|User $user, Role $role): bool
    {
        return $user->can('delete_role');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(Admin|User $user): bool
    {
        return $user->can('delete_any_role');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(Admin|User $user, Role $role): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(Admin|User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(Admin|User $user, Role $role): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(Admin|User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(Admin|User $user, Role $role): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(Admin|User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
}
