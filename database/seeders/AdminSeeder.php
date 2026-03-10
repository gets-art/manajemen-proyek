<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);

        $user = User::firstOrCreate(
            ['email' => 'admin@reflect.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        $user->assignRole($superAdmin);
    }
}
