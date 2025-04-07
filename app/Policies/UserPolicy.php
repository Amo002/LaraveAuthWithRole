<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Global super admin (merchant_id = 1 + admin role) bypasses all.
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin') && $user->merchant_id === 1) {
            return true;
        }
    }

    /**
     * View a specific user.
     */
    public function view(User $user, User $targetUser)
    {
        return $user->merchant_id === $targetUser->merchant_id
            && ($user->can('view-users') || $user->can('view-merchant-users'));
    }

    /**
     * View any users (used in index view).
     */
    public function viewAny(User $user)
    {
        return $user->can('view-users') || $user->can('view-merchant-users');
    }

    /**
     * Create a new user.
     */
    public function create(User $user)
    {
        return $user->can('create-users') || $user->can('create-merchant-users');
    }

    /**
     * Update a user.
     */
    public function update(User $user, User $targetUser)
    {
        if ($targetUser->id === 1) return false;

        return $user->merchant_id === $targetUser->merchant_id &&
               ($user->can('edit-users') || $user->can('edit-merchant-users'));
    }

    /**
     * Delete a user.
     */
    public function delete(User $authUser, User $targetUser)
    {
        if ($authUser->id === $targetUser->id || $targetUser->id === 1) {
            return false;
        }

        if ($authUser->merchant_id !== $targetUser->merchant_id) {
            return false;
        }

        if (!($authUser->can('delete-users') || $authUser->can('delete-merchant-users'))) {
            return false;
        }

        $authRole = $authUser->getRoleNames()->first();
        $targetRole = $targetUser->getRoleNames()->first();

        // Disallow deleting same or higher role
        if ($authRole === $targetRole) {
            return false;
        }

        return true;
    }
}
