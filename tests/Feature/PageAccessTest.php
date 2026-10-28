<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageAccessTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role): User
    {
        return User::factory()->create(['role_id' => Role::firstOrCreate(['name' => $role])->id]);
    }

    private function pageData(array $overrides = []): array
    {
        return array_merge(['slug' => 'oferta', 'title' => 'Oferta', 'type' => 'servicios'], $overrides);
    }

    public function test_guests_cannot_open_the_page_manager(): void
    {
        $this->get('/pages')->assertRedirect('/login');
        $this->post('/pages', $this->pageData())->assertRedirect('/login');
    }

    public function test_staff_can_open_the_page_manager(): void
    {
        $this->actingAs($this->staff('admin'))->get('/pages')->assertOk();
        $this->actingAs($this->staff('agente'))->get('/pages')->assertOk();
    }

    public function test_users_without_a_role_cannot_use_the_page_manager(): void
    {
        $this->actingAs(User::factory()->create())->get('/pages')->assertForbidden();
    }

    public function test_an_agent_can_edit_the_pages_they_create(): void
    {
        $agent = $this->staff('agente');

        $this->actingAs($agent)->post('/pages', $this->pageData())->assertRedirect();

        $page = Page::where('slug', 'oferta')->firstOrFail();
        $this->assertSame($agent->id, $page->created_by);
        $this->actingAs($agent)->get("/pages/{$page->slug}/edit")->assertOk();
    }

    public function test_an_agent_cannot_edit_an_admin_page_unless_it_is_shared_with_agents(): void
    {
        $admin = $this->staff('admin');
        $agent = $this->staff('agente');
        $private = Page::factory()->create(['created_by' => $admin->id, 'editable_by_agents' => false]);
        $shared = Page::factory()->create(['created_by' => $admin->id, 'editable_by_agents' => true]);

        $this->actingAs($agent)->get("/pages/{$private->slug}/edit")->assertForbidden();
        $this->actingAs($agent)->get("/pages/{$shared->slug}/edit")->assertOk();
        $this->actingAs($admin)->get("/pages/{$private->slug}/edit")->assertOk();
    }

    public function test_the_slug_must_be_unique_and_the_type_valid(): void
    {
        $admin = $this->staff('admin');
        Page::factory()->create(['slug' => 'oferta']);

        $this->actingAs($admin)->post('/pages', $this->pageData())->assertSessionHasErrors('slug');
        $this->actingAs($admin)->post('/pages', $this->pageData(['slug' => 'otra', 'type' => 'inventado']))->assertSessionHasErrors('type');
    }

    public function test_unpublished_pages_are_only_visible_to_logged_in_staff(): void
    {
        $draft = Page::factory()->create(['slug' => 'borrador', 'published' => false]);
        $live = Page::factory()->create(['slug' => 'publicada', 'published' => true]);

        $this->get('/publicada')->assertOk();
        $this->get('/borrador')->assertNotFound();
        $this->actingAs($this->staff('agente'))->get('/borrador')->assertOk();
    }
}
