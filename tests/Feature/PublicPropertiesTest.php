<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Role;
use App\Models\User;
use App\Support\PublicProperties;
use Database\Seeders\PageSeeder;
use Database\Seeders\PropertyOperationSeeder;
use Database\Seeders\PropertyStatusSeeder;
use Database\Seeders\PropertyTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PropertyTypeSeeder::class, PropertyOperationSeeder::class, PropertyStatusSeeder::class]);
    }

    private function withStatus(string $status, array $attributes = []): Property
    {
        return Property::factory()->create(['status_id' => PropertyStatus::where('name', $status)->value('id')] + $attributes);
    }

    public function test_only_available_and_reserved_properties_are_public(): void
    {
        $this->withStatus('disponible', ['title' => 'Libre']);
        $this->withStatus('reservado', ['title' => 'Reservado']);
        $this->withStatus('vendido', ['title' => 'Vendido']);
        $this->withStatus('alquilado', ['title' => 'Alquilado']);

        $titles = PublicProperties::featured(10)->pluck('title')->all();

        $this->assertEqualsCanonicalizing(['Libre', 'Reservado'], $titles);
        $this->assertEqualsCanonicalizing(['Libre', 'Reservado'], PublicProperties::search([])->pluck('title')->all());
    }

    public function test_the_search_filters_by_text_type_and_price_and_ignores_invalid_values(): void
    {
        $casa = PropertyType::where('name', 'casa')->value('id');
        $this->withStatus('disponible', ['title' => 'Casa con jardín', 'type_id' => $casa, 'price' => 300000]);
        $this->withStatus('disponible', ['title' => 'Piso céntrico', 'price' => 120000]);

        $this->assertSame(['Casa con jardín'], PublicProperties::search(['search' => 'jardín'])->pluck('title')->all());
        $this->assertSame(['Casa con jardín'], PublicProperties::search(['type' => $casa])->pluck('title')->all());
        $this->assertSame(['Piso céntrico'], PublicProperties::search(['max_price' => 150000])->pluck('title')->all());
        $this->assertSame(['Casa con jardín'], PublicProperties::search(['min_price' => 200000])->pluck('title')->all());
        // valores no numéricos y comodines: no rompen la consulta ni filtran de más
        $this->assertCount(2, PublicProperties::search(['min_price' => 'abc', 'type' => 'xx']));
        $this->assertCount(0, PublicProperties::search(['search' => '%']));
    }

    public function test_a_sold_property_page_is_hidden_from_visitors_but_not_from_staff(): void
    {
        $sold = $this->withStatus('vendido');
        $free = $this->withStatus('disponible');
        $admin = User::factory()->create(['role_id' => Role::firstOrCreate(['name' => 'admin'])->id]);

        $this->get($free->publicUrl())->assertOk();
        $this->get($sold->publicUrl())->assertNotFound();
        $this->actingAs($admin)->get($sold->publicUrl())->assertOk();
    }

    public function test_pagination_keeps_the_search_filters(): void
    {
        User::factory()->create(['role_id' => Role::firstOrCreate(['name' => 'admin'])->id]);
        $this->seed(PageSeeder::class);
        Property::factory()->count(15)->create(['title' => 'Casa de prueba']);

        $this->get('/propiedades?search=prueba')
            ->assertOk()
            ->assertSee('search=prueba', false);
    }
}
