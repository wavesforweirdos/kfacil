<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_home_page_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_root_shows_the_published_home_page_of_the_cms(): void
    {
        $this->seed();

        $this->get('/')->assertOk()->assertSee('Bienvenido a K Fácil Inmobiliaria');
    }
}
