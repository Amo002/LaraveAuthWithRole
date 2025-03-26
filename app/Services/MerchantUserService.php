<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MerchantUserService
{
    public function getMerchantUsers($merchantId)
    {
        return User::where('merchant_id', $merchantId)->get();
    }



    public function deleteUser($id, $merchantId)
    {
        $user = User::where('id', $id)
            ->where('merchant_id', $merchantId)
            ->first();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found or unauthorized.'
            ];
        }

        $user->delete();

        return [
            'status' => true,
            'message' => 'User deleted successfully.'
        ];
    }
}
