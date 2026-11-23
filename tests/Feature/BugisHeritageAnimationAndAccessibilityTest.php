<?php

namespace Tests\Feature;

use App\Models\AdatGlossaryEntry;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugisHeritageAnimationAndAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_accessibility_attributes_and_wcag_conformity(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $config = ThemeRegistry::defaultConfig('bugis-heritage');
        $config['adat']['showGlossary'] = true;
        $config['adat']['showLontara'] = true;

        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => $config,
            'verse_arabic' => 'وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا',
            'verse_translation' => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu.',
        ]);

        AdatGlossaryEntry::create([
            'term' => 'Sulapa Eppa',
            'latin_reading' => 'sulapa eppa',
            'lontara_text' => 'ᨔᨘᨒᨄ ᨕᨙᨄ',
            'meaning_id' => 'Empat sisi; falsafah kesempurnaan kosmos Bugis.',
            'status' => 'diverifikasi',
            'is_published' => true,
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // 1. Arabic accessibility
        $response->assertSee('lang="ar"', false);
        $response->assertSee('dir="rtl"', false);

        // 2. Lontara accessibility
        $response->assertSee('lang="bug-Bugi"', false);
        $response->assertSee('aria-label="sulapa eppa"', false);

        // 3. Modals have role="dialog" and aria-modal="true"
        $response->assertSee('id="bh-glossary-modal"', false);
        $response->assertSee('role="dialog"', false);
        $response->assertSee('aria-modal="true"', false);
        $response->assertSee('id="bh-credits-modal"', false);

        // 4. Form inputs have labels
        $response->assertSee('for="bh-rsvp-name"', false);
        $response->assertSee('for="bh-rsvp-pax"', false);
        $response->assertSee('for="bh-rsvp-message"', false);
        $response->assertSee('for="bh-wish-name"', false);
        $response->assertSee('for="bh-wish-message"', false);
    }

    public function test_motion_and_gate_door_elements_present(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // 1. Traditional gate door panels present in rendered cover
        $response->assertSee('bh-cover-door--left', false);
        $response->assertSee('bh-cover-door--right', false);

        // 2. CSS contains solemn animation rules and reduced motion safeguard
        $cssPath = public_path('themes/bugis-heritage/theme.css');
        $this->assertFileExists($cssPath);
        $css = file_get_contents($cssPath);

        $this->assertStringContainsString('bh-cover-door--left', $css);
        $this->assertStringContainsString('bh-cover-door--right', $css);
        $this->assertStringContainsString('bh-shimmer', $css);
        $this->assertStringContainsString('bh-draw-stroke', $css);
        $this->assertStringContainsString('prefers-reduced-motion', $css);
        $this->assertStringContainsString('outline: 2px solid var(--bh-gold)', $css);

        // 3. JS contains solemn scroll reveal & animation controller
        $jsPath = public_path('themes/bugis-heritage/theme.js');
        $this->assertFileExists($jsPath);
        $js = file_get_contents($jsPath);

        $this->assertStringContainsString('startOpenedAnimations', $js);
        $this->assertStringContainsString('ScrollTrigger', $js);
    }
}
