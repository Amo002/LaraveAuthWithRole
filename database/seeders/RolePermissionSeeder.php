<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        // Global permissions
        $permissions = [
            'manage-users',
            'view-merchant-users',
            'edit-content',
            'invite-users',
        ];
=======
        // Create roles if not exists
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'merchant']);

        // Create permissions if not exists
        Permission::firstOrCreate(['name' => 'manage-users']);
        Permission::firstOrCreate(['name' => 'view-merchant-users']);

        // Assign permissions to roles
        $admin = Role::findByName('admin');
        $admin->givePermissionTo('manage-users');
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

<<<<<<< HEAD
        // Global roles
        $this->createRole('admin', 1, $permissions);
        $this->createRole('viewer', 1, ['view-merchant-users']);

        // Yahala roles (merchant_id = 2)
        $this->createRole('yahala_admin', 2, ['manage-users', 'view-merchant-users', 'invite-users']);
        $this->createRole('yahala_user', 2, ['view-merchant-users']);
        $this->createRole('yahala_editor', 2, ['edit-content']);

        // ZeroGame roles (merchant_id = 3)
        $this->createRole('zerogame_admin', 3, ['manage-users', 'view-merchant-users', 'invite-users']);
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
=======
        $merchant = Role::findByName('merchant');
        $merchant->givePermissionTo('view-merchant-users');
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
    }
}
