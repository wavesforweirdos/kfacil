<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage_property_types', 'description' => 'Manage property types'],
            ['name' => 'manage_property_operations', 'description' => 'Manage property operations'],
            ['name' => 'manage_property_statuses', 'description' => 'Manage property statuses'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Assign all permissions to admin and agente roles
        $admin = Role::where('name', 'admin')->first();
        $agente = Role::where('name', 'agente')->first();

        if ($admin) {
            $admin->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', array_column($permissions, 'name'))->pluck('id')
            );
        }

        if ($agente) {
            $agente->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', array_column($permissions, 'name'))->pluck('id')
            );
        }
    }
}
