<?php

namespace Database\Seeders;

use App\Models\User;
<<<<<<< HEAD
use App\Models\Merchant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
=======
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
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
=======
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Create Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@ex.com'],
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin'),
                'merchant_id' => $global->id,
            ]
        );
<<<<<<< HEAD
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
=======
        $admin->assignRole('admin');

        // Create Merchant A
        $merchantA = User::updateOrCreate(
            ['email' => 'merchantA@ex.com'],
            [
                'name' => 'Merchant A',
                'password' => bcrypt('merchantA'),
            ]
        );
        $merchantA->assignRole('merchant');

        // Create Merchant B
        $merchantB = User::updateOrCreate(
            ['email' => 'merchantB@ex.com'],
            [
                'name' => 'Merchant B',
                'password' => bcrypt('merchantB'),
            ]
        );
        $merchantB->assignRole('merchant');

        // Create 10 users for Merchant A
        for ($i = 1; $i <= 10; $i++) {
            $user = User::updateOrCreate(
                ['email' => "userA{$i}@ex.com"],
                [
                    'name' => "User A{$i}",
                    'password' => bcrypt('password'),
                    'merchant_id' => $merchantA->id, // Link user to Merchant A
                ]
            );
            $user->assignRole('user');
        }

        // Create 10 users for Merchant B
        for ($i = 1; $i <= 10; $i++) {
            $user = User::updateOrCreate(
                ['email' => "userB{$i}@ex.com"],
                [
                    'name' => "User B{$i}",
                    'password' => bcrypt('password'),
                    'merchant_id' => $merchantB->id, // Link user to Merchant B
                ]
            );
            $user->assignRole('user');
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
        }
    }
}
