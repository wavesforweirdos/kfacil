<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyOperation;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PropertyOperationSeeder;
use Database\Seeders\PropertyStatusSeeder;
use Database\Seeders\PropertyTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PropertyTypeSeeder::class, PropertyOperationSeeder::class, PropertyStatusSeeder::class]);
        Storage::fake('public');
    }

    private function staff(string $role): User
    {
        return User::factory()->create(['role_id' => Role::firstOrCreate(['name' => $role])->id]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Piso luminoso',
            'type_id' => PropertyType::where('name', 'piso')->value('id'),
            'operation_id' => PropertyOperation::where('name', 'venta')->value('id'),
            'status_id' => PropertyStatus::where('name', 'disponible')->value('id'),
            'price' => 185000,
            'reference_mode' => 'auto',
        ], $overrides);
    }

    public function test_guests_cannot_use_the_property_crud(): void
    {
        $this->get('/admin/properties')->assertRedirect('/login');
        $this->post('/admin/properties', $this->payload())->assertRedirect('/login');
    }

    public function test_admins_and_agents_can_create_a_property_with_images(): void
    {
        foreach (['admin' => 'admin', 'agente' => 'agente'] as $role => $prefix) {
            $user = $this->staff($role);

            $this->actingAs($user)->post("/{$prefix}/properties", $this->payload([
                'title' => "Piso de {$role}",
                'main_image' => UploadedFile::fake()->image('principal.jpg'),
                'secondary_images' => [UploadedFile::fake()->image('uno.jpg'), UploadedFile::fake()->image('dos.png')],
                'floor_plans' => [UploadedFile::fake()->image('plano.jpg')],
            ]))->assertRedirect(route("{$prefix}.properties.index"));

            $property = Property::where('title', "Piso de {$role}")->firstOrFail();

            $this->assertSame($user->id, $property->user_id);
            $this->assertNotEmpty($property->reference);
            Storage::disk('public')->assertExists($property->main_image);
            $this->assertCount(2, $property->images()->where('type', 'secondary')->get());
            $this->assertCount(1, $property->images()->where('type', 'floor_plan')->get());
        }
    }

    public function test_the_property_form_is_validated(): void
    {
        $admin = $this->staff('admin');

        $this->actingAs($admin)->post('/admin/properties', $this->payload(['price' => -5, 'rooms' => 500]))
            ->assertSessionHasErrors(['price', 'rooms']);

        $this->actingAs($admin)->post('/admin/properties', $this->payload(['price' => 1000000000]))
            ->assertSessionHasErrors('price');

        $this->actingAs($admin)->post('/admin/properties', $this->payload(['main_image' => UploadedFile::fake()->create('virus.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrors('main_image');

        $this->assertDatabaseCount('properties', 0);
    }

    public function test_a_property_can_be_edited_keeping_its_reference(): void
    {
        $admin = $this->staff('admin');
        $property = Property::factory()->create(['reference' => 'REF-100', 'title' => 'Antiguo']);

        $this->actingAs($admin)
            ->put("/admin/properties/{$property->id}", $this->payload(['title' => 'Nuevo título', 'reference_mode' => 'keep']))
            ->assertRedirect(route('admin.properties.index'));

        $property->refresh();
        $this->assertSame('Nuevo título', $property->title);
        $this->assertSame('REF-100', $property->reference);
    }

    public function test_the_main_image_can_be_removed_on_edit(): void
    {
        $admin = $this->staff('admin');
        $path = UploadedFile::fake()->image('p.jpg')->store('properties', 'public');
        $property = Property::factory()->create(['main_image' => $path]);

        $this->actingAs($admin)
            ->put("/admin/properties/{$property->id}", $this->payload(['reference_mode' => 'keep', 'remove_image' => 1]))
            ->assertRedirect();

        $this->assertNull($property->fresh()->main_image);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_deleting_a_property_removes_its_files(): void
    {
        $agent = $this->staff('agente');
        $path = UploadedFile::fake()->image('p.jpg')->store('properties', 'public');
        $property = Property::factory()->create(['user_id' => $agent->id, 'main_image' => $path]);

        $this->actingAs($agent)->delete("/agente/properties/{$property->id}")->assertRedirect();

        $this->assertModelMissing($property);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_cloning_copies_the_main_image_so_deleting_one_does_not_break_the_other(): void
    {
        $admin = $this->staff('admin');
        $path = UploadedFile::fake()->image('p.jpg')->store('properties', 'public');
        $original = Property::factory()->create(['reference' => 'ABC', 'main_image' => $path]);

        $this->actingAs($admin)->post("/admin/properties/{$original->id}/clone")->assertRedirect();

        $clone = Property::where('reference', 'ABC-1')->firstOrFail();
        $this->assertNotSame($original->main_image, $clone->main_image);
        Storage::disk('public')->assertExists($clone->main_image);

        $this->actingAs($admin)->delete("/admin/properties/{$clone->id}");
        Storage::disk('public')->assertExists($original->main_image);
    }

    public function test_the_list_filters_and_sorts_by_the_catalogues(): void
    {
        $admin = $this->staff('admin');
        $casa = PropertyType::where('name', 'casa')->value('id');
        Property::factory()->create(['title' => 'Una casa en Alicante', 'type_id' => $casa, 'city' => 'Alicante']);
        Property::factory()->create(['title' => 'Un piso en Valencia', 'city' => 'Valencia']);

        $this->actingAs($admin)->get('/admin/properties?city=Alicante')
            ->assertOk()->assertSee('Una casa en Alicante')->assertDontSee('Un piso en Valencia');

        // casa < piso por nombre del catálogo (y no falla al ordenar por tipo/operación/estado)
        $this->actingAs($admin)->get('/admin/properties?sort=type&dir=asc')
            ->assertOk()->assertSeeInOrder(['Una casa en Alicante', 'Un piso en Valencia']);

        foreach (['operation', 'status', 'price', 'city', 'title'] as $sort) {
            $this->actingAs($admin)->get("/admin/properties?sort={$sort}")->assertOk();
        }
    }
}
