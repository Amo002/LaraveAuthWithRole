<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Container\Attributes\Auth;

class UserService
{
    public function getUsers($authUser)
    {
        if ($authUser->hasRole('admin')) {
            $users = User::with('roles')->get();

            if ($users->isEmpty()) {
                return [
                    'status' => false,
                    'data' => [],
                    'message' => 'No users found.'
                ];
            }

            return [
                'status' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully.'
            ];
        }

        return [
            'status' => false,
            'data' => [],
            'message' => 'Unauthorized access.'
        ];
    }



    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found.'
            ];
        }

        if ($user->delete()) {
            return [
                'status' => true,
                'message' => 'User deleted successfully.'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Failed to delete user.'
            ];
        }
    }

    public function updateUserRole($id, $role)
    {
        $authUser = auth()->user();

        if ($authUser->id == $id) {
            return [
                'status' => false,
                'message' => 'You cannot update your own role.'
            ];
        }
        $user = User::find($id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found.'
            ];
        }

        $user->roles()->detach();

        if ($role === 'admin') {
            $user->assignRole('admin');
        } else {
            $user->assignRole('user');
        }

        return [
            'status' => true,
            'message' => 'User role updated successfully.'
        ];
    }
}
