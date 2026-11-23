<?php

namespace Tests\Feature;

use App\Models\AdatEventType;
use App\Models\AdatGlossaryEntry;
use App\Models\Event;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugisHeritageAdatEventsAndGlossaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_adat_event_type_falls_back_to_standard_title(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        // Create a DRAFT adat event type
        $draftAdatType = AdatEventType::create([
            'key' => 'massita-baiseng',
            'label_display' => 'Massita Baiseng Belum Terverifikasi',
            'alt_spellings' => ['massita'],
            'description_id' => 'Deskripsi rahasia draf',
            'status' => 'draf',
            'is_published' => false,
        ]);

        $event = $invitation->events()->first();
        $event->update([
            'title' => 'Resepsi Pernikahan Standar',
            'adat_event_type_id' => $draftAdatType->id,
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // Must display standard title and NOT leak the unverified label or description
        $response->assertSee('Resepsi Pernikahan Standar');
        $response->assertDontSee('Massita Baiseng Belum Terverifikasi');
        $response->assertDontSee('Deskripsi rahasia draf');
    }

    public function test_verified_adat_event_type_displays_verified_label_and_pacci_leaf(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        // Update seeded Mappacci adat event type to VERIFIED
        $verifiedMappacci = AdatEventType::where('key', 'mappacci')->firstOrFail();
        $verifiedMappacci->update([
            'label_display' => 'Mappacci (Malam Pacar)',
            'description_id' => 'Upacara penyucian diri calon mempelai dengan daun pacar sebelum akad nikah.',
            'status' => 'diverifikasi',
            'verified_by' => 'Tetua Adat',
            'verified_at' => now(),
            'is_published' => true,
        ]);

        $event = $invitation->events()->first();
        $event->update([
            'title' => 'Malam Pacci',
            'adat_event_type_id' => $verifiedMappacci->id,
            'display_label_override' => 'Mappacci Adat Bugis Bone',
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // 1. Shows display label override
        $response->assertSee('Mappacci Adat Bugis Bone');

        // 2. Shows verified description
        $response->assertSee('Upacara penyucian diri calon mempelai');

        // 3. Shows pacci-leaf svg accent
        $response->assertSee('bh-event-pacci-accent', false);
    }

    public function test_glossary_only_displays_verified_and_published_entries(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $config = ThemeRegistry::defaultConfig('bugis-heritage');
        $config['adat']['showGlossary'] = true;
        $config['adat']['showLontara'] = true;

        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => $config,
        ]);

        // Create 1 draft entry and 1 verified entry
        AdatGlossaryEntry::create([
            'term' => 'Istilah Draf Rahasia',
            'latin_reading' => 'draf rahasia',
            'meaning_id' => 'Makna draf yang belum boleh tampil ke publik.',
            'status' => 'draf',
            'is_published' => false,
        ]);

        $verifiedEntry = AdatGlossaryEntry::create([
            'term' => 'Sulapa Eppa',
            'latin_reading' => 'sulapa eppa',
            'lontara_text' => 'ᨔᨘᨒᨄ ᨕᨙᨄ',
            'meaning_id' => 'Empat sisi; bentuk belah ketupat yang melambangkan kesempurnaan kosmos dalam falsafah Bugis.',
            'regional_note' => 'Penafsiran dapat bervariasi antar daerah.',
            'status' => 'diverifikasi',
            'verified_by' => 'Pakar Budaya',
            'verified_at' => now(),
            'is_published' => true,
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // 1. Glossary button and modal exist
        $response->assertSee('id="bh-glossary-btn"', false);
        $response->assertSee('id="bh-glossary-modal"', false);

        // 2. Verified entry appears with Lontara text and attributes
        $response->assertSee('Sulapa Eppa');
        $response->assertSee('ᨔᨘᨒᨄ ᨕᨙᨄ');
        $response->assertSee('lang="bug-Bugi"', false);
        $response->assertSee('aria-label="sulapa eppa"', false);
        $response->assertSee('Empat sisi; bentuk belah ketupat');
        $response->assertSee('Penafsiran dapat bervariasi antar daerah.');

        // 3. Draft entry does NOT appear
        $response->assertDontSee('Istilah Draf Rahasia');
        $response->assertDontSee('Makna draf yang belum boleh tampil ke publik.');
    }

    public function test_glossary_hidden_when_disabled_in_config(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $config = ThemeRegistry::defaultConfig('bugis-heritage');
        $config['adat']['showGlossary'] = false;

        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => $config,
        ]);

        AdatGlossaryEntry::create([
            'term' => 'Walasuji',
            'latin_reading' => 'walasuji',
            'meaning_id' => 'Pagar anyaman bambu berbentuk belah ketupat.',
            'status' => 'diverifikasi',
            'is_published' => true,
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();
        $response->assertDontSee('id="bh-glossary-btn"', false);
        $response->assertDontSee('id="bh-glossary-modal"', false);
    }
}
