<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Support\DatabaseBackup;
use Database\Seeders\SupardinInvitationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdvancedInvitationFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_wa_template_interpolates_placeholders_correctly(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        $invitation->update([
            'wa_template' => "Salam Hangat {nama}!\nKunjungi: {link}\nDari: {mempelai}",
        ]);

        $guest = $invitation->guests()->create([
            'name' => 'Bapak Budi Santoso',
            'code' => 'BUDI01',
            'max_pax' => 2,
        ]);

        $message = $invitation->generateWaMessage($guest);

        $this->assertStringContainsString('Salam Hangat Bapak Budi Santoso!', $message);
        $this->assertStringContainsString('to=BUDI01', $message);
        $this->assertStringContainsString($invitation->bride_nickname, $message);
    }

    public function test_receptionist_check_in_page_and_check_in_process(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        $guest = $invitation->guests()->create([
            'name' => 'Ahmad Fadillah',
            'code' => 'AHMAD1',
            'max_pax' => 2,
        ]);

        $response = $this->get(route('invitation.check-in', ['slug' => $invitation->slug]));
        $response->assertOk();
        $response->assertSee('Ahmad Fadillah');
        $response->assertSee('AHMAD1');

        $checkInResponse = $this->postJson(route('invitation.check-in.process', ['slug' => $invitation->slug]), [
            'code' => 'AHMAD1',
        ]);

        $checkInResponse->assertOk();
        $checkInResponse->assertJson([
            'ok' => true,
            'already_checked_in' => false,
        ]);

        $this->assertNotNull($guest->fresh()->checked_in_at);

        // Second check-in warns already checked in
        $repeatResponse = $this->postJson(route('invitation.check-in.process', ['slug' => $invitation->slug]), [
            'code' => 'AHMAD1',
        ]);
        $repeatResponse->assertOk();
        $repeatResponse->assertJson([
            'ok' => true,
            'already_checked_in' => true,
        ]);
    }

    public function test_receptionist_can_undo_check_in(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        $guest = $invitation->guests()->create([
            'name' => 'Rini Astuti',
            'code' => 'RINI01',
            'checked_in_at' => now(),
        ]);

        $response = $this->postJson(route('invitation.check-in.undo', [
            'slug' => $invitation->slug,
            'guest' => $guest->id,
        ]));

        $response->assertOk();
        $this->assertNull($guest->fresh()->checked_in_at);
    }

    public function test_guest_can_submit_gift_confirmation(): void
    {
        Storage::fake('public');
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        $guest = $invitation->guests()->create([
            'name' => 'Keluarga Hendra',
            'code' => 'HEND01',
        ]);

        $proofFile = UploadedFile::fake()->image('transfer.jpg', 600, 400);

        $response = $this->postJson(route('invitation.gifts.confirm', ['slug' => $invitation->slug]), [
            'guest_code' => 'HEND01',
            'sender_name' => 'Keluarga Hendra',
            'target_account' => 'BCA 12345678',
            'amount' => 'Rp 500.000',
            'notes' => 'Selamat berbahagia untuk kedua mempelai.',
            'proof_file' => $proofFile,
        ]);

        $response->assertOk();
        $response->assertJson(['ok' => true]);

        $this->assertDatabaseHas('gift_confirmations', [
            'invitation_id' => $invitation->id,
            'guest_id' => $guest->id,
            'sender_name' => 'Keluarga Hendra',
            'target_account' => 'BCA 12345678',
            'amount' => 'Rp 500.000',
        ]);
    }

    public function test_guest_event_segmentation_filters_displayed_events(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        // Create 2 events if not already present
        $invitation->events()->delete();
        $eventAkad = $invitation->events()->create([
            'title' => 'Akad Nikah Khusus',
            'starts_at' => now()->addDays(5)->setTime(8, 0),
            'venue_name' => 'Masjid Raya',
            'address' => 'Jl. Masjid No. 1',
            'sort_order' => 1,
        ]);

        $eventResepsi = $invitation->events()->create([
            'title' => 'Resepsi Akbar',
            'starts_at' => now()->addDays(5)->setTime(13, 0),
            'venue_name' => 'Gedung Serbaguna',
            'address' => 'Jl. Protokol No. 10',
            'sort_order' => 2,
        ]);

        // Guest only invited to Resepsi
        $guestResepsiOnly = $invitation->guests()->create([
            'name' => 'Tamu Resepsi Saja',
            'code' => 'RESEPSI1',
            'allowed_event_ids' => [$eventResepsi->id],
        ]);

        $response = $this->get(route('invitation.show', [
            'slug' => $invitation->slug,
            'to' => $guestResepsiOnly->code,
        ]));

        $response->assertOk();
        $response->assertSee('Resepsi Akbar');
        $response->assertDontSee('Akad Nikah Khusus');
    }

    public function test_noindex_meta_tag_renders_when_enabled(): void
    {
        $this->seed(SupardinInvitationSeeder::class);
        $invitation = Invitation::firstOrFail();

        $invitation->update(['no_index' => true]);

        $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        $invitation->update(['no_index' => false]);
        $responseNormal = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
        $responseNormal->assertOk();
        $responseNormal->assertDontSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_database_backup_download_streams_valid_sql(): void
    {
        $this->seed(SupardinInvitationSeeder::class);

        $response = DatabaseBackup::download();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/sql', $response->headers->get('Content-Type'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Backup Database Undangan Digital', $content);
        $this->assertStringContainsString('INSERT INTO `invitations`', $content);
        $this->assertStringContainsString('INSERT INTO `guests`', $content);
    }
}
