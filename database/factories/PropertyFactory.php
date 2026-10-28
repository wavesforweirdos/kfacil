<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyOperation;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'type_id' => fn () => PropertyType::firstOrCreate(['name' => 'piso'], ['label' => 'Piso'])->id,
            'operation_id' => fn () => PropertyOperation::firstOrCreate(['name' => 'venta'], ['label' => 'Venta'])->id,
            'status_id' => fn () => PropertyStatus::firstOrCreate(['name' => 'disponible'], ['label' => 'Disponible'])->id,
            'price' => fake()->numberBetween(60000, 900000),
            'surface' => fake()->numberBetween(35, 300),
            'rooms' => fake()->numberBetween(1, 6),
            'bathrooms' => fake()->numberBetween(1, 4),
            'city' => fake()->city(),
            'country' => 'España',
        ];
    }
}
