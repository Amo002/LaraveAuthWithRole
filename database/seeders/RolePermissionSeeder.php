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
            'manage-users',
            'view-merchant-users',
            'edit-content',
            'invite-users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Global roles
        $this->createRole('admin', 1, $permissions);
        $this->createRole('viewer', 1, ['view-merchant-users']);

        // Yahala roles (merchant_id = 2)
        $this->createRole('yahala_admin', 2, ['manage-users', 'view-merchant-users', 'invite-users', 'edit-content']);
        $this->createRole('yahala_user', 2, ['view-merchant-users']);
        $this->createRole('yahala_editor', 2, ['edit-content']);

        // ZeroGame roles (merchant_id = 3)
        $this->createRole('zerogame_admin', 3, ['manage-users', 'view-merchant-users', 'invite-users', 'edit-content']);
        $this->createRole('zerogame_user', 3, ['view-merchant-users']);
        $this->createRole('zerogame_editor', 3, ['edit-content']);
    }

    private function createRole(string $name, $merchantId, array $permissions): void
    {
        $role = Role::create([
            'name' => $name,
            'merchant_id' => $merchantId,
        ]);

        $role->givePermissionTo($permissions);
    }
}
