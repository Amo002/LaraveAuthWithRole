<?php

namespace App\Services\Admin;

use App\Models\Merchant;
use App\Models\User;
use Spatie\Permission\Models\Permission;
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

        // 1) Create the merchant
        $merchant = Merchant::create([
            'name'    => $data['name'],
            'address' => $data['address'],
        ]);

        // 2) Make sure we tell Spatie which team (merchant) we are working with
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        // 3) Create the user
        $email = $merchant->name . '_superadmin@example.com';
        $user = User::create([
            'name'        => $merchant->name,
            'email'       => $email,
            'merchant_id' => $merchant->id,
            'password'    => bcrypt($merchant->name),
        ]);

        // 4) Create the superadmin role
        $roleName = $merchant->name . '_superadmin';
        $role = \Spatie\Permission\Models\Role::firstOrCreate(
            [
                'name'        => $roleName,
                'merchant_id' => $merchant->id,
            ]
        );

        // 5) Assign that role to the user
        $user->assignRole($role);

        return [
            'status' => true,
            'message' => "Merchant [{$merchant->name}] created with a Super Admin: {$email}",
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

    public function toggleMerchantStatus($id)
    {
        $merchant = Merchant::find($id);

        if (!$merchant || $merchant->id === 1) {
            return [
                'status' => false,
                'message' => 'Invalid merchant.'
            ];
        }

        $merchant->is_active = !$merchant->is_active;
        $merchant->save();

        return [
            'status' => true,
            'message' => $merchant->is_active
                ? 'Merchant enabled successfully.'
                : 'Merchant disabled successfully.'
        ];
    }
}
