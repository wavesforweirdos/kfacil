<?php

namespace Database\Seeders;

use App\Models\PropertyStatus;
use Illuminate\Database\Seeder;

class PropertyStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'disponible', 'label' => 'Disponible', 'sort_order' => 1],
            ['name' => 'reservado', 'label' => 'Reservado', 'sort_order' => 2],
            ['name' => 'vendido', 'label' => 'Vendido', 'sort_order' => 3],
            ['name' => 'alquilado', 'label' => 'Alquilado', 'sort_order' => 4],
        ];

        foreach ($statuses as $status) {
            PropertyStatus::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
