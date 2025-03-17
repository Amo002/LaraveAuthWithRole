<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\RolePermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);



        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@ex.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin'),
            ]
        );

        $Admin = User::updateOrCreate(
            ['email' => 'user@ex.com'],
            [
                'name' => 'User',
                'password' => bcrypt('user'),
            ]
        );

        $superAdmin->assignRole('admin');
        $Admin->assignRole('user');
    }
}
