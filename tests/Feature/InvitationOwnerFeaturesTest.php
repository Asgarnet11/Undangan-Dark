<?php

namespace Tests\Feature;

use App\Filament\Resources\Invitations\Pages\EditInvitation;
use App\Filament\Resources\Invitations\RelationManagers\GuestsRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\RsvpsRelationManager;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\SupardinInvitationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationOwnerFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_import_guests_from_csv(): void
    {
        Storage::fake('local');

        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();
        $admin = User::firstOrFail();

        $this->actingAs($admin);

        $csvContent = "Nama,Grup,Maksimal Tamu,Nomor WhatsApp\n"
            ."Budi Santoso,Keluarga,3,081234567890\n"
            ."Dewi Lestari,Sahabat,2,089876543210\n";

        $file = UploadedFile::fake()->createWithContent('guests.csv', $csvContent);

        Livewire::test(GuestsRelationManager::class, [
            'ownerRecord' => $invitation,
            'pageClass' => EditInvitation::class,
        ])
            ->callTableAction('import_csv', data: ['file' => [$file]])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('guests', [
            'invitation_id' => $invitation->id,
            'name' => 'Budi Santoso',
            'group' => 'Keluarga',
            'max_pax' => 3,
            'phone' => '081234567890',
        ]);

        $this->assertDatabaseHas('guests', [
            'invitation_id' => $invitation->id,
            'name' => 'Dewi Lestari',
            'group' => 'Sahabat',
            'max_pax' => 2,
            'phone' => '089876543210',
        ]);
    }

    public function test_owner_can_export_guest_links_and_download_sample_csv(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();
        $admin = User::firstOrFail();

        $this->actingAs($admin);

        Livewire::test(GuestsRelationManager::class, [
            'ownerRecord' => $invitation,
            'pageClass' => EditInvitation::class,
        ])
            ->callTableAction('download_sample_csv')
            ->assertFileDownloaded('format-impor-tamu.csv');

        Livewire::test(GuestsRelationManager::class, [
            'ownerRecord' => $invitation,
            'pageClass' => EditInvitation::class,
        ])
            ->callTableAction('export_csv')
            ->assertHasNoTableActionErrors();
    }

    public function test_owner_can_export_rsvp_summary(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();
        $admin = User::firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RsvpsRelationManager::class, [
            'ownerRecord' => $invitation,
            'pageClass' => EditInvitation::class,
        ])
            ->callTableAction('export_csv')
            ->assertHasNoTableActionErrors();
    }

    public function test_owner_can_toggle_published_status_and_preview_draft(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();
        $admin = User::firstOrFail();

        $this->actingAs($admin);

        Livewire::test(EditInvitation::class, ['record' => $invitation->getRouteKey()])
            ->callAction('toggle_publish');

        $this->assertFalse($invitation->fresh()->is_published);

        // Guest preview still works for authenticated owner when unpublished
        $this->get(route('invitation.preview', ['invitation' => $invitation]))
            ->assertOk();

        // Toggle back to published
        Livewire::test(EditInvitation::class, ['record' => $invitation->getRouteKey()])
            ->callAction('toggle_publish');

        $this->assertTrue($invitation->fresh()->is_published);
    }

    public function test_backup_database_command_creates_backup_file(): void
    {
        $exitCode = Artisan::call('app:backup-database', ['--keep' => 3]);
        $this->assertSame(0, $exitCode);

        $backupDir = storage_path('app/backups');
        $this->assertTrue(File::isDirectory($backupDir));
        $this->assertNotEmpty(File::files($backupDir));
    }

    public function test_invitation_expires_after_active_days_window(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        // Set past event date
        $invitation->events()->delete();
        $invitation->events()->create([
            'title' => 'Akad Nikah',
            'starts_at' => Carbon::now()->subDays(45),
            'venue_name' => 'Lokasi Acara',
            'address' => 'Alamat Acara',
            'maps_query' => 'Alamat Acara',
            'sort_order' => 1,
        ]);
        $invitation->update(['active_days_after_event' => 30]);

        $this->assertTrue($invitation->isExpired());

        // Public visitor receives graceful expired page
        $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
        $response->assertOk();
        $response->assertSee('Masa Undangan Selesai');
        $response->assertSee('Terima kasih yang tak terhingga');
    }

    public function test_security_sanitizes_wishes_and_rsvp_input_and_does_not_leak_guests(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        // XSS script injection in wish
        $wishResponse = $this->postJson(route('invitation.wishes.store', ['slug' => $invitation->slug]), [
            'name' => 'Tamu <script>alert("xss")</script>',
            'origin' => '<b>Kendari</b>',
            'message' => 'Selamat ya <iframe src="evil.com"></iframe> semoga langgeng!',
        ]);

        $wishResponse->assertCreated();
        $this->assertDatabaseMissing('wishes', ['message' => 'Selamat ya <iframe src="evil.com"></iframe> semoga langgeng!']);
        $this->assertDatabaseHas('wishes', ['message' => 'Selamat ya  semoga langgeng!']);
        $this->assertDatabaseHas('wishes', ['name' => 'Tamu alert("xss")']);

        // Wishes API does not leak guest codes, phone numbers, or emails
        $wishesApiResponse = $this->getJson(route('invitation.wishes.index', ['slug' => $invitation->slug]));
        $wishesApiResponse->assertOk();
        $wishesApiResponse->assertJsonMissing(['phone' => '08']);
        $wishesApiResponse->assertJsonMissing(['email' => '@']);
        $wishesApiResponse->assertJsonMissing(['guest_id' => 1]);
    }
}
