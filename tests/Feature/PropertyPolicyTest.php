<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PropertyOperationSeeder;
use Database\Seeders\PropertyStatusSeeder;
use Database\Seeders\PropertyTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PropertyTypeSeeder::class, PropertyOperationSeeder::class, PropertyStatusSeeder::class]);
    }

    private function staff(string $role): User
    {
        return User::factory()->create(['role_id' => Role::firstOrCreate(['name' => $role])->id]);
    }

    private function payload(Property $property): array
    {
        return [
            'title' => 'Cambiado',
            'type_id' => $property->type_id,
            'operation_id' => $property->operation_id,
            'status_id' => $property->status_id,
            'price' => 100000,
            'reference_mode' => 'keep',
        ];
    }

    public function test_an_agent_cannot_edit_clone_or_delete_properties_of_other_users(): void
    {
        $agent = $this->staff('agente');
        $other = $this->staff('agente');
        $property = Property::factory()->create(['user_id' => $other->id, 'title' => 'De otro']);

        $this->actingAs($agent)->put("/agente/properties/{$property->id}", $this->payload($property))->assertForbidden();
        $this->actingAs($agent)->post("/agente/properties/{$property->id}/clone")->assertForbidden();
        $this->actingAs($agent)->delete("/agente/properties/{$property->id}")->assertForbidden();

        $this->assertSame('De otro', $property->fresh()->title);
        $this->assertDatabaseCount('properties', 1);
    }

    public function test_an_agent_can_manage_their_own_properties_and_see_all_of_them(): void
    {
        $agent = $this->staff('agente');
        $own = Property::factory()->create(['user_id' => $agent->id, 'title' => 'Mío']);
        $foreign = Property::factory()->create(['title' => 'Ajeno']);

        $this->actingAs($agent)->get('/agente/properties')->assertOk()->assertSee('Mío')->assertSee('Ajeno');
        $this->actingAs($agent)->get("/agente/properties/{$foreign->id}")->assertOk();

        $this->actingAs($agent)->put("/agente/properties/{$own->id}", $this->payload($own))->assertRedirect();
        $this->actingAs($agent)->post("/agente/properties/{$own->id}/clone")->assertRedirect();
        $this->actingAs($agent)->delete("/agente/properties/{$own->id}")->assertRedirect();
    }

    public function test_admins_can_manage_any_property(): void
    {
        $admin = $this->staff('admin');
        $property = Property::factory()->create(['user_id' => $this->staff('agente')->id]);

        $this->actingAs($admin)->put("/admin/properties/{$property->id}", $this->payload($property))->assertRedirect();
        $this->actingAs($admin)->post("/admin/properties/{$property->id}/clone")->assertRedirect();
        $this->actingAs($admin)->delete("/admin/properties/{$property->id}")->assertRedirect();
    }

    public function test_the_list_only_offers_the_actions_the_user_can_use(): void
    {
        $agent = $this->staff('agente');
        Property::factory()->create(['user_id' => $agent->id, 'reference' => 'MIA-1']);
        Property::factory()->create(['reference' => 'AJENA-1']);

        $html = $this->actingAs($agent)->get('/agente/properties')->assertOk()->getContent();

        // el icono de editar aparece una sola vez: solo para el inmueble propio
        $this->assertSame(1, substr_count($html, "openDrawer('edit'"));
    }
}
