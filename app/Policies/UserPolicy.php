<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
<<<<<<< HEAD
     * Super Admin (merchant_id = 1) bypasses all policies.
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin') && $user->merchant_id === 1) {
            return true;
=======
     * Allow admin to bypass all checks.
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin')) {
            return true; // Admin can do anything
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
        }
    }

    /**
<<<<<<< HEAD
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
=======
     * Merchant can view their own users.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('merchant');
    }

    /**
     * Merchant can view only their own users.
     */
    public function view(User $user, User $targetUser)
    {
        if ($user->hasRole('merchant')) {
            return $user->id === $targetUser->merchant_id;
        }

        return false;
    }

    /**
     * Only admin can create users (handled by before()).
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Admin can update, Merchant **CANNOT** update.
     */
    public function update(User $user, User $targetUser)
    {
        return false;
    }

    /**
     * Merchant can only delete their own users.
     */
    public function delete(User $user, User $targetUser)
    {
        if ($user->hasRole('merchant')) {
            return $user->id === $targetUser->merchant_id;
        }

        return false;
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
    }
}
