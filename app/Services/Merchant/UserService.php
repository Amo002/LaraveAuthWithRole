<?php

namespace App\Services\Merchant;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserService
{
    /**
     * List users belonging to the authenticated merchant
     */
    public function getUsersForMerchant(User $authUser)
    {
        return User::where('merchant_id', $authUser->merchant_id)
            ->with('roles')
            ->orderBy('id')
            ->get();
    }

    /**
     * Create a user under the merchant without assigning a role
     */
    public function createUserForMerchant($request, User $authUser)
    {
        $exists = User::where('email', $request->email)->exists();

        if ($exists) {
            return [
                'status' => false,
                'message' => 'Email already exists.',
            ];
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'merchant_id' => $authUser->merchant_id,
        ]);

        return [
            'status' => true,
            'message' => 'User created successfully.',
        ];
    }

    /**
     * Get roles assignable by the merchant admin (excluding merchant_admin)
     */
    public function getAssignableRolesForMerchant(User $authUser)
    {
        return Role::where('merchant_id', $authUser->merchant_id)
            ->where('name', '!=', 'merchant_admin')
            ->get();
    }

    /**
     * Update a user's role within the same merchant
     */
    public function updateUserRole($userId, string $role, User $authUser)
    {
        $user = User::where('id', $userId)
            ->where('merchant_id', $authUser->merchant_id)
            ->first();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found.',
            ];
        }

        if ($user->id === $authUser->id) {
            return [
                'status' => false,
                'message' => 'You cannot change your own role.',
            ];
        }

        if ($role === 'merchant_admin') {
            return [
                'status' => false,
                'message' => 'You cannot assign the merchant_admin role.',
            ];
        }

        // Set team scope to this merchant
        app(PermissionRegistrar::class)->setPermissionsTeamId($authUser->merchant_id);

        $validRole = Role::where('name', $role)
            ->where('merchant_id', $authUser->merchant_id)
            ->first();

        if (!$validRole) {
            return [
                'status' => false,
                'message' => 'Invalid role for this merchant.',
            ];
        }

        $user->syncRoles([$validRole]);

        return [
            'status' => true,
            'message' => 'User role updated successfully.',
        ];
    }

    /**
     * Delete a user under the same merchant
     */
    public function deleteUser($userId, User $authUser)
    {
        $user = User::where('id', $userId)
            ->where('merchant_id', $authUser->merchant_id)
            ->first();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found.',
            ];
        }

        $user->delete();

        return [
            'status' => true,
            'message' => 'User deleted successfully.',
        ];
    }
}
