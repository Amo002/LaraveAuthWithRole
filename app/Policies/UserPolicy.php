<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Super Admin (merchant_id = 1) bypasses all policies.
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin') && $user->merchant_id === 1) {
            return true;
        }
    }

    /**
     * View a single user (must be in the same merchant).
     */
    public function view(User $user, User $targetUser)
    {
        return $user->merchant_id === $targetUser->merchant_id;
    }

    /**
     * View all users (only for merchants, not global admin).
     */
    public function viewAny(User $user)
    {
        return $user->merchant_id !== 1;
    }

    /**
     * Create new user (requires 'manage-users' permission).
     */
    public function create(User $user)
    {
        return $user->can('manage-users');
    }

    /**
     * Update user info (must be same merchant).
     */
    public function update(User $user, User $targetUser)
    {
        if ($targetUser->id === 1) {
            return false;
        }

        return $user->merchant_id === $targetUser->merchant_id;
    }

    /**
     * Delete user (requires 'edit-content' and same merchant).
     */
    public function delete(User $user, User $targetUser)
    {
        return $user->can('edit-content') && $user->merchant_id === $targetUser->merchant_id;
    }
}
