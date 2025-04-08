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
    public function view(User $user, User $targetUser): bool
    {
        return $user->merchant_id === $targetUser->merchant_id &&
            ($user->can('view-users') || $user->can('view-merchant-users'));
    }

    /**
     * View list of users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-users') || $user->can('view-merchant-users');
    }

    /**
     * Create a new user.
     */
    public function create(User $user): bool
    {
        return $user->can('create-users') || $user->can('create-merchant-users');
    }

    /**
     * Update a user's profile (e.g., name/email).
     */
    public function update(User $user, User $targetUser): bool
    {
        return $user->merchant_id === $targetUser->merchant_id &&
            ($user->can('edit-users') || $user->can('edit-merchant-users')) &&
            $targetUser->id !== 1;
    }

    /**
     * Delete a user.
     */
    public function delete(User $authUser, User $targetUser): bool
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

        // Prevent deleting users with equal or higher roles
        if ($authRole === $targetRole || $targetRole === 'merchant_admin') {
            return false;
        }

        return true;
    }

    /**
     * Assign a role to another user.
     */
    public function assignRole(User $authUser): bool
    {
        return $authUser->can('assign-merchant-roles');
    }
}
