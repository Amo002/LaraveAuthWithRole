<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RolePermissionSeeder::class]);

        // Create global merchant
        $global = Merchant::updateOrCreate(['id' => 1], [
            'name' => 'Global',
            'address' => 'System-wide',
        ]);

        // Set Spatie's team context
        app(PermissionRegistrar::class)->setPermissionsTeamId($global->id);

        // Create super admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@ex.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin'),
                'merchant_id' => $global->id,
            ]
        );

        $superAdmin->assignRole('admin');
    }
}
