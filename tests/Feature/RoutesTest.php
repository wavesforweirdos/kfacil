<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_route_points_to_an_existing_controller_method(): void
    {
        $missing = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getActionName())
            ->filter(fn ($action) => str_contains($action, '@'))
            ->filter(function ($action) {
                [$class, $method] = explode('@', $action);

                return ! method_exists($class, $method);
            })
            ->unique()
            ->values();

        $this->assertSame([], $missing->all(), 'Rutas que apuntan a un método que no existe');
    }

    public function test_the_public_pages_of_the_seeded_site_render(): void
    {
        $this->seed();

        foreach (['/inicio', '/servicios', '/propiedades', '/inmuebles/1'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
