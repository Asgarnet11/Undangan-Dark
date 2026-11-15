<?php

namespace Tests\Feature;

use App\Filament\Resources\Invitations\Pages\EditInvitation;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\SupardinInvitationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DarkBohoAdminMediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_couple_photos_cover_and_audio_onto_public_disk_and_render_them(): void
    {
        Storage::fake('public');

        $this->seed(SupardinInvitationSeeder::class);

        $invitation = Invitation::query()->where('slug', 'supardin')->firstOrFail();
        $admin = User::query()->where('email', 'admin@undangan.test')->firstOrFail();

        $this->actingAs($admin);

        // Minimal ISO Base Media (ftypisom) header so finfo detects video/mp4 even with .mp3 extension.
        $mp4ContainerBytes = hex2bin('000000186674797069736f6d0000020069736f6d69736f326d703431');

        $bridePhoto = UploadedFile::fake()->image('bride.jpg', 480, 640);
        $groomPhoto = UploadedFile::fake()->image('groom.jpg', 480, 640);
        $coverPhoto = UploadedFile::fake()->image('cover.jpg', 900, 1400);
        $musicAudio = UploadedFile::fake()->createWithContent('bgm-song.mp3', $mp4ContainerBytes !== false ? $mp4ContainerBytes : 'fake-audio');

        Livewire::test(EditInvitation::class, ['record' => $invitation->getRouteKey()])
            ->set('data.bride_photo_media', [$bridePhoto])
            ->set('data.groom_photo_media', [$groomPhoto])
            ->set('data.cover_media', [$coverPhoto])
            ->set('data.music_media', [$musicAudio])
            ->call('save')
            ->assertHasNoFormErrors();

        $invitation->refresh();

        $brideMedia = $invitation->getFirstMedia('bride_photo');
        $groomMedia = $invitation->getFirstMedia('groom_photo');
        $coverMedia = $invitation->getFirstMedia('cover');
        $musicMedia = $invitation->getFirstMedia('music');

        $this->assertNotNull($brideMedia);
        $this->assertNotNull($groomMedia);
        $this->assertNotNull($coverMedia);
        $this->assertNotNull($musicMedia);

        $this->assertSame('public', $brideMedia->disk);
        $this->assertSame('public', $groomMedia->disk);
        $this->assertSame('public', $coverMedia->disk);
        $this->assertSame('public', $musicMedia->disk);

        $brideUrl = $invitation->resolveMediaOrPathUrl('bride_photo', $invitation->bride_photo);
        $groomUrl = $invitation->resolveMediaOrPathUrl('groom_photo', $invitation->groom_photo);
        $coverUrl = $invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo);
        $musicUrl = $invitation->resolveMediaOrPathUrl('music', $invitation->music_file, '');

        $this->assertNotNull($brideUrl);
        $this->assertNotNull($groomUrl);
        $this->assertNotNull($coverUrl);
        $this->assertNotNull($musicUrl);

        $response = $this->get(route('invitation.show', ['slug' => 'supardin']));
        $response->assertOk();
        $response->assertSee($brideUrl, false);
        $response->assertSee($groomUrl, false);
        $response->assertSee($coverUrl, false);
        $response->assertSee($musicUrl, false);
    }
}

