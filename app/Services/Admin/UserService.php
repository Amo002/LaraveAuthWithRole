<?php

namespace App\Services\Admin;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserService
{
    /**
     * Get all users across all merchants
     */
    public function getUsers($authUser)
    {
        $allUsers = collect();
        $availableRoles = collect();

        // Get all unique merchant_ids from users
        $merchantIds = User::select('merchant_id')->distinct()->pluck('merchant_id');

        foreach ($merchantIds as $merchantId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($merchantId);

            // Users under current merchant
            $users = User::where('merchant_id', $merchantId)
                ->with('roles')
                ->select('id', 'name', 'email', 'merchant_id')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'merchant_id' => $user->merchant_id,
                        'roles' => $user->roles->pluck('name')->toArray(),
                    ];
                });

            // Roles available for this merchant
            $rolesForTeam = Role::where('merchant_id', $merchantId)->pluck('name');
            $availableRoles[$merchantId] = $rolesForTeam;

            $allUsers = $allUsers->merge($users);
        }

        return [
            'success' => true,
            'data' => $allUsers,
            'roles' => $availableRoles,
            'message' => 'Users retrieved successfully.'
        ];
    }

    /**
     * Update user role for a given user
     */
    public function updateUserRole($id, $role)
    {
        $user = User::find($id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found.'
            ];
        }

        if ((int) $user->id === 1 || auth()->id() === $user->id) {
            return [
                'status' => false,
                'message' => 'You cannot change this user\'s role.'
            ];
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($user->merchant_id);

        if (!Role::where('name', $role)->where('merchant_id', $user->merchant_id)->exists()) {
            return [
                'status' => false,
                'message' => 'Invalid role for this merchant.'
            ];
        }

        $user->syncRoles([$role]);

        return [
            'status' => true,
            'message' => 'User role updated successfully.'
        ];
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found.'
            ];
        }

        if ((int) $user->id === 1) {
            return [
                'status' => false,
                'message' => 'Cannot delete the Super Admin.'
            ];
        }

        $user->delete();

        return [
            'status' => true,
            'message' => 'User deleted successfully.'
        ];
    }
}
