<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RolePermissionSeeder::class]);

        // Create merchants first
        $global = Merchant::updateOrCreate(['id' => 1], [
            'name' => 'Global',
            'address' => 'System-wide',
        ]);

        $yahala = Merchant::updateOrCreate(['id' => 2], [
            'name' => 'Yahala',
            'address' => '123 Yahala Street',
        ]);

        $zeroGame = Merchant::updateOrCreate(['id' => 3], [
            'name' => 'ZeroGame',
            'address' => '456 ZeroGame Road',
        ]);

        // GLOBAL USERS
        app(PermissionRegistrar::class)->setPermissionsTeamId($global->id);

        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@ex.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin'),
                'merchant_id' => $global->id,
            ]
        );
        $superAdmin->assignRole('admin');

        $this->createUserWithRole('viewer_global@ex.com', 'Global Viewer', 'viewer', 1);

        // YAHALA USERS
        app(PermissionRegistrar::class)->setPermissionsTeamId($yahala->id);
        $this->createRandomMerchantUsers(2, 'yahala');

        // ZEROGAME USERS
        app(PermissionRegistrar::class)->setPermissionsTeamId($zeroGame->id);
        $this->createRandomMerchantUsers(3, 'zerogame');
    }

    private function createUserWithRole($email, $name, $role, $merchantId): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($merchantId);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('password'),
                'merchant_id' => $merchantId,
            ]
        );

        $user->assignRole($role);
    }

    private function createRandomMerchantUsers(int $merchantId, string $prefix): void
    {
        $roles = ["{$prefix}_admin", "{$prefix}_user", "{$prefix}_editor"];

        foreach ($roles as $role) {
            for ($i = 1; $i <= 3; $i++) {
                $this->createUserWithRole(
                    "{$prefix}_{$role}{$i}@ex.com",
                    Str::title($prefix) . " " . Str::title(str_replace("{$prefix}_", '', $role)) . " {$i}",
                    $role,
                    $merchantId
                );
            }
        }
    }
}
