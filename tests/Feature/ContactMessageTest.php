<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role_id' => Role::firstOrCreate(['name' => $role])->id]);
    }

    private function validMessage(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ana Visitante',
            'email' => 'ana@example.test',
            'phone' => '600123123',
            'subject' => 'Quiero visitar un piso',
            'message' => 'Me interesa el piso de la calle Mayor.',
        ], $overrides);
    }

    public function test_a_visitor_can_send_a_message_from_the_contact_page(): void
    {
        $this->seed();

        $this->get('/contacto')->assertOk()->assertSee(route('contact.store'), false);

        $this->post('/contacto', $this->validMessage())
            ->assertRedirect()
            ->assertSessionHas('contact_success');

        $this->assertDatabaseHas('contact_messages', ['email' => 'ana@example.test', 'read_at' => null]);
    }

    public function test_the_message_is_validated(): void
    {
        $this->post('/contacto', ['email' => 'no-es-un-email'])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_bots_that_fill_the_honeypot_are_discarded_silently(): void
    {
        $this->post('/contacto', $this->validMessage(['website' => 'http://spam.test']))
            ->assertSessionHas('contact_success');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_the_contact_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contacto', $this->validMessage())->assertRedirect();
        }

        $this->post('/contacto', $this->validMessage())->assertStatus(429);
    }

    public function test_guests_cannot_see_the_messages(): void
    {
        $message = ContactMessage::factory()->create();

        $this->get('/messages')->assertRedirect('/login');
        $this->get("/messages/{$message->id}")->assertRedirect('/login');
    }

    public function test_staff_can_list_and_read_messages_and_reading_marks_them(): void
    {
        $unread = ContactMessage::factory()->create(['subject' => 'Sin leer']);
        ContactMessage::factory()->read()->create(['subject' => 'Ya leído']);

        foreach (['admin', 'agente'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get('/messages')->assertOk()->assertSee('Sin leer')->assertSee('Ya leído');
        }

        $this->actingAs($this->userWithRole('admin'))
            ->get('/messages?estado=sin-leer')->assertOk()->assertSee('Sin leer')->assertDontSee('Ya leído');

        $this->actingAs($this->userWithRole('agente'))->get("/messages/{$unread->id}")->assertOk();
        $this->assertNotNull($unread->fresh()->read_at);
    }

    public function test_only_admins_can_delete_messages(): void
    {
        $message = ContactMessage::factory()->create();

        $this->actingAs($this->userWithRole('agente'))->delete("/messages/{$message->id}")->assertForbidden();
        $this->assertModelExists($message);

        $this->actingAs($this->userWithRole('admin'))->delete("/messages/{$message->id}")->assertRedirect(route('messages.index'));
        $this->assertModelMissing($message);
    }

    public function test_users_without_a_role_cannot_see_the_messages(): void
    {
        $this->actingAs(User::factory()->create())->get('/messages')->assertForbidden();
    }
}
