<?php

namespace Tests\Feature;

use App\Models\Invitation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageFallbackAndPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_storage_fallback_route_serves_public_disk_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('invitations/covers/sample.jpg', 'fake-image-binary-content');

        $response = $this->get('/storage/invitations/covers/sample.jpg');
        $response->assertOk();
        $this->assertSame('fake-image-binary-content', $response->streamedContent());

        // Traversal is rejected
        $this->get('/storage/../outside.txt')->assertNotFound();

        // Non existent file is 404
        $this->get('/storage/not-found.jpg')->assertNotFound();
    }

    public function test_preview_redirects_unauthenticated_visitor_to_slug_when_published(): void
    {
        $this->seed(DatabaseSeeder::class);
        $invitation = Invitation::where('is_published', true)->firstOrFail();

        $response = $this->get(route('invitation.preview', ['invitation' => $invitation]));
        $response->assertRedirect(route('invitation.show', ['slug' => $invitation->slug]));
    }

    public function test_preview_redirects_unauthenticated_visitor_to_login_when_unpublished(): void
    {
        $this->seed(DatabaseSeeder::class);
        $invitation = Invitation::firstOrFail();
        $invitation->update(['is_published' => false]);

        $response = $this->get(route('invitation.preview', ['invitation' => $invitation]));
        $response->assertRedirect('/admin/login');
    }
}
