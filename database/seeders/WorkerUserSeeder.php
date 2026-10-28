<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agentRole = Role::where('name', 'agente')->first();

        for ($i = 1; $i <= 20; $i++) {
            User::firstOrCreate(
                ['email' => 'user_'.$i.'@kfacil.test'],
                [
                    'name' => 'User '.$i,
                    'password' => Hash::make('password123'),
                    'role_id' => $agentRole->id,
                    'avatar' => null,
                ],
            );
        }
    }
}
