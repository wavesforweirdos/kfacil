<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'title' => ucfirst($title),
            'type' => 'servicios',
            'description' => fake()->sentence(),
            'editable_by_agents' => false,
            'published' => true,
            'created_by' => User::factory(),
        ];
    }
}
