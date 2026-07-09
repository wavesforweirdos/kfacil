<?php

namespace Database\Seeders;

use App\Models\PropertyOperation;
use Illuminate\Database\Seeder;

class PropertyOperationSeeder extends Seeder
{
    public function run(): void
    {
        $operations = [
            ['name' => 'venta', 'label' => 'Venta', 'sort_order' => 1],
            ['name' => 'alquiler', 'label' => 'Alquiler', 'sort_order' => 2],
        ];

        foreach ($operations as $operation) {
            PropertyOperation::firstOrCreate(
                ['name' => $operation['name']],
                $operation
            );
        }
    }
}
