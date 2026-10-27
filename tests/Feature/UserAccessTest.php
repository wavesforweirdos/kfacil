<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $roleModel = Role::firstOrCreate(['name' => $role]);

        return User::factory()->create(['role_id' => $roleModel->id]);
    }

    private function newUserData(int $roleId): array
    {
        return [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@kfacil.test',
            'password' => 'Password1',
            'role_id' => $roleId,
        ];
    }

    public function test_guests_are_sent_to_the_login(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/admin/properties')->assertRedirect('/login');
    }

    public function test_an_agent_cannot_manage_users(): void
    {
        $agent = $this->userWithRole('agente');
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $this->actingAs($agent)->get('/admin/users')->assertForbidden();
        $this->actingAs($agent)->post('/admin/users', $this->newUserData($adminRole->id))->assertForbidden();
        // ni siquiera existe una ruta de usuarios bajo /agente
        $this->actingAs($agent)->get('/agente/users')->assertNotFound();
        $this->actingAs($agent)->post('/agente/users', $this->newUserData($adminRole->id))->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'nuevo@kfacil.test']);
    }

    public function test_an_agent_cannot_open_the_admin_panel_but_an_admin_cannot_open_the_agent_one(): void
    {
        $agent = $this->userWithRole('agente');
        $admin = $this->userWithRole('admin');

        $this->actingAs($agent)->get('/admin')->assertForbidden();
        $this->actingAs($agent)->get('/agente')->assertOk();
        $this->actingAs($admin)->get('/agente')->assertForbidden();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_an_admin_can_create_a_user(): void
    {
        $admin = $this->userWithRole('admin');
        $agentRole = Role::firstOrCreate(['name' => 'agente']);

        $this->actingAs($admin)
            ->post('/admin/users', $this->newUserData($agentRole->id))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'nuevo@kfacil.test', 'role_id' => $agentRole->id]);
    }

    public function test_the_password_rules_are_enforced(): void
    {
        $admin = $this->userWithRole('admin');
        $agentRole = Role::firstOrCreate(['name' => 'agente']);

        $this->actingAs($admin)
            ->post('/admin/users', ['password' => 'weak'] + $this->newUserData($agentRole->id))
            ->assertSessionHasErrors('password');
    }

    public function test_an_admin_cannot_delete_themselves_or_leave_the_app_without_admins(): void
    {
        $admin = $this->userWithRole('admin');
        $agentRole = Role::firstOrCreate(['name' => 'agente']);

        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHas('error');
        $this->assertModelExists($admin);

        // tampoco se le puede quitar el rol de admin al último administrador
        $this->actingAs($admin)
            ->put("/admin/users/{$admin->id}", ['name' => $admin->name, 'email' => $admin->email, 'role_id' => $agentRole->id])
            ->assertSessionHas('error');
        $this->assertSame('admin', $admin->fresh()->role->name);
    }

    public function test_an_admin_can_delete_another_admin_when_one_remains(): void
    {
        $admin = $this->userWithRole('admin');
        $other = $this->userWithRole('admin');

        $this->actingAs($admin)->delete("/admin/users/{$other->id}")->assertRedirect(route('admin.users.index'));
        $this->assertModelMissing($other);
    }
}
