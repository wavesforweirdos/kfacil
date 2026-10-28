<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@kfacil.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'avatar' => null,
            ],
        );
    }
}
