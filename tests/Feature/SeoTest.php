<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PropertyOperationSeeder;
use Database\Seeders\PropertyStatusSeeder;
use Database\Seeders\PropertyTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PropertyTypeSeeder::class, PropertyOperationSeeder::class, PropertyStatusSeeder::class]);
    }

    private function sold(array $attributes = []): Property
    {
        return Property::factory()->create(['status_id' => PropertyStatus::where('name', 'vendido')->value('id')] + $attributes);
    }

    public function test_the_sitemap_lists_published_pages_and_visible_properties_only(): void
    {
        Page::factory()->create(['slug' => 'inicio', 'type' => 'home', 'published' => true]);
        Page::factory()->create(['slug' => 'servicios', 'published' => true]);
        Page::factory()->create(['slug' => 'borrador', 'published' => false]);
        $free = Property::factory()->create(['title' => 'Piso visible']);
        $sold = $this->sold(['title' => 'Piso vendido']);

        $xml = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->getContent();

        $this->assertStringContainsString('<loc>'.url('/').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.url('servicios').'</loc>', $xml);
        $this->assertStringNotContainsString('borrador', $xml);
        $this->assertStringContainsString($free->publicUrl(), $xml);
        $this->assertStringNotContainsString($sold->publicUrl(), $xml);
    }

    public function test_robots_hides_the_crm_and_announces_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /messages')
            ->assertSee('Sitemap: '.route('sitemap'), false);
    }

    public function test_the_property_page_has_canonical_open_graph_and_structured_data(): void
    {
        $property = Property::factory()->create(['title' => 'Ático con vistas', 'city' => 'Alicante', 'description' => 'Un ático luminoso.']);

        $html = $this->get($property->publicUrl())->assertOk()->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="'.$property->publicUrl().'">', $html);
        $this->assertStringContainsString('<title>Ático con vistas en Alicante |', $html);
        $this->assertStringContainsString('property="og:title" content="Ático con vistas"', $html);
        $this->assertStringContainsString('"@type":"RealEstateListing"', $html);
        $this->assertStringContainsString('"priceCurrency":"EUR"', $html);
    }

    public function test_the_structured_data_cannot_be_broken_by_the_title(): void
    {
        $property = Property::factory()->create(['title' => 'Piso </script><script>alert(1)</script>']);

        $html = $this->get($property->publicUrl())->assertOk()->getContent();

        $this->assertStringNotContainsString('</script><script>alert(1)', $html);
    }

    public function test_old_property_urls_redirect_permanently_to_the_canonical_one(): void
    {
        $property = Property::factory()->create(['title' => 'Casa en la playa']);

        $this->get("/inmuebles/{$property->id}")->assertStatus(301)->assertRedirect($property->publicUrl());
        $this->get("/inmuebles/{$property->id}/titulo-viejo")->assertStatus(301)->assertRedirect($property->publicUrl());
        $this->assertStringEndsWith("/inmuebles/{$property->id}/casa-en-la-playa", $property->publicUrl());
    }

    public function test_cms_pages_have_a_canonical_url_and_drafts_are_not_indexed(): void
    {
        Page::factory()->create(['slug' => 'servicios', 'published' => true]);
        $draft = Page::factory()->create(['slug' => 'borrador', 'published' => false]);
        $staff = User::factory()->create(['role_id' => Role::firstOrCreate(['name' => 'admin'])->id]);

        $this->get('/servicios')->assertOk()->assertSee('<link rel="canonical" href="'.url('servicios').'">', false);
        $this->actingAs($staff)->get('/'.$draft->slug)->assertOk()->assertSee('noindex', false);
    }
}
