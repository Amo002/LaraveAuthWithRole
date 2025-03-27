<?php

namespace App\Services\Merchant;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getUsersForMerchant($authUser)
    {
        return User::where('merchant_id', $authUser->merchant_id)
            ->orderBy('id')
            ->get();
    }

    public function createUserForMerchant($request, $authUser)
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

    public function deleteUser($id, $authUser)
    {
        $user = User::where('id', $id)
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
