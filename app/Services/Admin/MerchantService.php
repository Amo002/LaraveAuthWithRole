<?php

namespace App\Services\Admin;

use App\Models\Merchant;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MerchantService
{
    public function getAllMerchants()
    {
        $merchants = Merchant::where('id', '!=', 1)->get();
    
        return [
            'status' => true,
            'data' => $merchants,
            'message' => 'Merchants retrieved successfully.',
        ];
    }
    

    public function createMerchant(array $data)
    {
        if (empty($data['name']) || empty($data['address'])) {
            return [
                'status' => false,
                'message' => 'Name and address are required.',
            ];
        }

        Merchant::create([
            'name' => $data['name'],
            'address' => $data['address'],
        ]);

        return [
            'status' => true,
            'message' => 'Merchant created successfully.',
        ];
    }

    public function deleteMerchant($id)
    {
        $merchant = Merchant::find($id);

        if (!$merchant) {
            return [
                'status' => false,
                'message' => 'Merchant not found.',
            ];
        }

        if ($merchant->id == 1) {
            return [
                'status' => false,
                'message' => 'Cannot delete global merchant.',
            ];
        }

        // Delete associated roles (Spatie roles)
        Role::where('merchant_id', $merchant->id)->each(function ($role) {
            $role->permissions()->detach(); // Remove permissions from role
            $role->users()->detach();       // Remove users from role
            $role->delete();                // Delete role itself
        });

        // Delete all users under this merchant
        User::where('merchant_id', $merchant->id)->each(function ($user) {
            $user->syncRoles([]); // Detach all roles
            $user->delete();      // Delete user
        });

        // Finally delete the merchant
        $merchant->delete();

        return [
            'status' => true,
            'message' => 'Merchant and all associated users and roles deleted successfully.',
        ];
    }
}
