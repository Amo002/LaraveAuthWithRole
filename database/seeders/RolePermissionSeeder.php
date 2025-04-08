<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Global permissions
        $permissions = [
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-roles',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
            'view-permissions',
            'assign-merchant-roles',
            'assign-merchant-permissions',
            'create-users',
            'edit-users',
            'delete-users',
            'view-users',
            'create-merchant-users',
            'edit-merchant-users',
            'delete-merchant-users',
            'view-merchant-users',
            'edit-content',
            'invite-users',
            'dev',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Global admin role (merchant_id = 1)
        $this->createRole('admin', 1, $permissions);
    }

    private function createRole(string $name, $merchantId, array $permissions): void
    {
        $role = Role::updateOrCreate([
            'name' => $name,
            'merchant_id' => $merchantId,
        ]);

        $role->givePermissionTo($permissions, $merchantId);
    }
}
