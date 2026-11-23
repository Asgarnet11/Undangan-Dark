<?php

namespace Tests\Feature;

use App\Models\AdatEventType;
use App\Models\AdatGlossaryEntry;
use App\Models\Invitation;
use App\Models\LontaraText;
use App\Models\User;
use Database\Seeders\AdatCatalogSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdatVerificationGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_adat_catalog_seeds_all_entries_with_draft_status(): void
    {
        $this->seed(AdatCatalogSeeder::class);

        $this->assertSame(14, AdatEventType::count());
        $this->assertSame(12, AdatGlossaryEntry::count());

        // Zero entries should be published or verified initially
        $this->assertSame(0, AdatEventType::where('status', 'diverifikasi')->count());
        $this->assertSame(0, AdatEventType::where('is_published', true)->count());
        $this->assertSame(0, AdatGlossaryEntry::where('status', 'diverifikasi')->count());
        $this->assertSame(0, AdatGlossaryEntry::where('is_published', true)->count());
    }

    public function test_server_gate_strictly_filters_unverified_adat_event_types(): void
    {
        $draft = AdatEventType::create([
            'key' => 'test_draft',
            'label_display' => 'Prosesi Draf',
            'status' => 'draf',
            'is_published' => false,
        ]);

        $rejected = AdatEventType::create([
            'key' => 'test_rejected',
            'label_display' => 'Prosesi Ditolak',
            'status' => 'ditolak',
            'is_published' => false,
        ]);

        $verifiedUnpublished = AdatEventType::create([
            'key' => 'test_verified_unpub',
            'label_display' => 'Prosesi Belum Terbit',
            'status' => 'diverifikasi',
            'is_published' => false,
        ]);

        $verified = AdatEventType::create([
            'key' => 'test_verified',
            'label_display' => 'Prosesi Sah',
            'status' => 'diverifikasi',
            'is_published' => true,
            'verified_by' => 'Puang Baso & Andi Tenri',
            'verified_at' => now(),
        ]);

        $publicList = AdatEventType::verified()->get();

        $this->assertCount(1, $publicList);
        $this->assertTrue($publicList->contains($verified));
        $this->assertFalse($publicList->contains($draft));
        $this->assertFalse($publicList->contains($rejected));
        $this->assertFalse($publicList->contains($verifiedUnpublished));
    }

    public function test_server_gate_strictly_filters_unverified_glossary_entries(): void
    {
        $draft = AdatGlossaryEntry::create([
            'term' => 'Istilah Draf',
            'latin_reading' => 'Istilah Draf',
            'meaning_id' => 'Makna draf',
            'status' => 'draf',
            'is_published' => false,
        ]);

        $verified = AdatGlossaryEntry::create([
            'term' => 'Walasuji',
            'latin_reading' => 'Walasuji',
            'lontara_text' => 'ᨓᨒᨔᨘᨍᨗ',
            'meaning_id' => 'Pagar anyaman bambu belah ketupat',
            'status' => 'diverifikasi',
            'is_published' => true,
            'verified_by' => 'Tetua Adat',
            'verified_at' => now(),
        ]);

        $publicList = AdatGlossaryEntry::verified()->get();

        $this->assertCount(1, $publicList);
        $this->assertTrue($publicList->contains($verified));
        $this->assertFalse($publicList->contains($draft));
    }

    public function test_server_gate_strictly_filters_unverified_lontara_texts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $invitation = Invitation::firstOrFail();

        $draftLontara = LontaraText::create([
            'invitation_id' => $invitation->id,
            'latin_text' => 'Salama',
            'lontara_text' => 'ᨔᨒᨆ',
            'status' => 'draf',
        ]);

        $verifiedLontara = LontaraText::create([
            'invitation_id' => $invitation->id,
            'latin_text' => 'Kurru Sumange',
            'lontara_text' => 'ᨀᨘᨑᨘ ᨔᨘᨆᨂᨙ',
            'status' => 'diverifikasi',
            'verified_by' => 'Pengecek Adat',
            'verified_at' => now(),
        ]);

        $verifiedTexts = $invitation->lontaraTexts()->verified()->get();

        $this->assertCount(1, $verifiedTexts);
        $this->assertTrue($verifiedTexts->contains($verifiedLontara));
        $this->assertFalse($verifiedTexts->contains($draftLontara));
    }

    public function test_editing_verified_content_automatically_revokes_status_to_draft(): void
    {
        $entry = AdatGlossaryEntry::create([
            'term' => 'Sulapa Eppa',
            'latin_reading' => 'Sulapa Eppa',
            'meaning_id' => 'Makna awal',
            'status' => 'diverifikasi',
            'is_published' => true,
            'verified_by' => 'Tetua Adat',
            'verified_at' => now(),
        ]);

        $this->assertTrue($entry->isVerified());

        // Someone edits the meaning content
        $entry->update([
            'meaning_id' => 'Perubahan tafsir baru yang belum ditinjau',
        ]);

        $entry->refresh();

        // Must automatically reset to draft and unpublished for re-verification
        $this->assertSame('draf', $entry->status);
        $this->assertFalse($entry->is_published);
        $this->assertFalse($entry->isVerified());
    }

    public function test_admin_can_access_adat_resources_and_perform_verification_action(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@undangan.test')->firstOrFail();

        $this->actingAs($admin);

        $responseEvents = $this->get('/admin/adat-event-types');
        $responseEvents->assertOk();
        $responseEvents->assertSee('Katalog Acara Adat');

        $responseGlossary = $this->get('/admin/adat-glossary-entries');
        $responseGlossary->assertOk();
        $responseGlossary->assertSee('Glosarium Istilah');
    }
}
