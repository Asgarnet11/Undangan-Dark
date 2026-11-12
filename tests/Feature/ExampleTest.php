<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_admin_when_no_published_invitation_exists(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }

    public function test_home_redirects_to_published_invitation_when_available(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/');

        $response->assertRedirect(route('invitation.show', ['slug' => 'raka-nadia']));
    }
}
