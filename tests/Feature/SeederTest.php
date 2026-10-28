<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_database_can_be_seeded_more_than_once(): void
    {
        $this->seed();
        $counts = [Role::count(), User::count(), Property::count(), Page::count()];

        $this->seed();

        $this->assertSame($counts, [Role::count(), User::count(), Property::count(), Page::count()]);
        $this->assertSame(2, Role::count());
        $this->assertSame(21, User::count());
    }
}
