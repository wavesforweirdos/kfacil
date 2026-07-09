<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'piso', 'label' => 'Piso', 'sort_order' => 1],
            ['name' => 'casa', 'label' => 'Casa', 'sort_order' => 2],
            ['name' => 'local', 'label' => 'Local', 'sort_order' => 3],
            ['name' => 'oficina', 'label' => 'Oficina', 'sort_order' => 4],
            ['name' => 'garaje', 'label' => 'Garaje', 'sort_order' => 5],
            ['name' => 'terreno', 'label' => 'Terreno', 'sort_order' => 6],
            ['name' => 'otro', 'label' => 'Otro', 'sort_order' => 7],
        ];

        foreach ($types as $type) {
            PropertyType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
