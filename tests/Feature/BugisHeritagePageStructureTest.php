<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugisHeritagePageStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_bugis_heritage_renders_all_fase3_sections(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
            'verse_arabic' => 'وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا',
            'verse_translation' => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu.',
            'verse_source' => 'QS. Ar-Rum: 21',
            'bride_photo' => 'sample-bride.jpg',
            'groom_photo' => 'sample-groom.jpg',
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk();

        // 1. Cover section
        $response->assertSee('id="bh-cover"', false);
        $response->assertSee('id="bh-open-invitation"', false);
        $response->assertSee($invitation->bride_nickname);
        $response->assertSee($invitation->groom_nickname);

        // 2. Verse section with proper lang & dir
        $response->assertSee('id="section-verse"', false);
        $response->assertSee('lang="ar"', false);
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('وَمِنْ آيَاتِهِ', false);
        $response->assertSee('QS. Ar-Rum: 21');

        // 3. Couple section
        $response->assertSee('id="section-couple"', false);
        $response->assertSee($invitation->bride_name);
        $response->assertSee($invitation->groom_name);
        $response->assertSee('bh-photo-frame', false);
        $response->assertSee('data-role="bride"', false);
        $response->assertSee('data-role="groom"', false);

        // 4. Countdown section
        $response->assertSee('id="section-countdown"', false);
        $response->assertSee('id="bh-countdown"', false);
        $response->assertSee('data-timestamp=', false);

        // 5. Gallery section
        $response->assertSee('id="section-gallery"', false);
        $response->assertSee('bh-gallery-grid', false);

        // 6. Closing section
        $response->assertSee('id="section-closing"', false);
        $response->assertSee('bh-closing-card', false);
        $response->assertSee(config('app.vendor_name', 'a2dev.my.id'));

        // 7. Sticky bottom navigation
        $response->assertSee('id="bh-bottom-nav"', false);
        $response->assertSee('href="#section-couple"', false);
        $response->assertSee('href="#section-events"', false);
        $response->assertSee('href="#bh-cover"', false);
        $response->assertSee('href="#section-gallery"', false);
        $response->assertSee('href="#section-wishes"', false);
    }

    public function test_bugis_heritage_cover_style_and_bride_first_options(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();

        // Test frame cover style & groom first
        $config = ThemeRegistry::defaultConfig('bugis-heritage');
        $config['cover']['style'] = 'frame';
        $config['layout']['brideFirst'] = false;
        $config['layout']['photoFrame'] = 'round';

        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => $config,
            'bride_photo' => 'sample-bride.jpg',
            'groom_photo' => 'sample-groom.jpg',
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();
        $response->assertSee('bh-cover--frame', false);
        $response->assertSee('bh-cover-frame-border', false);
        $response->assertSee('bh-photo-frame--round', false);

        // Verify Groom appears before Bride when brideFirst is false
        $html = $response->getContent();
        $this->assertLessThan(
            strpos($html, 'data-role="bride"'),
            strpos($html, 'data-role="groom"')
        );
    }

    public function test_bugis_heritage_cultural_accuracy_and_zero_emojis(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
            'bride_name' => 'Andi Fitria Nurandini, S.Kom',
            'groom_name' => 'Andi Sastra Syakur, S.H.',
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // Honor titles and names without altering case or stripping apostrophes
        $response->assertSee('Andi Fitria Nurandini, S.Kom');
        $response->assertSee('Andi Sastra Syakur, S.H.');

        // Zero emojis rule check on rendered page
        $content = $response->getContent();
        $this->assertDoesNotMatchRegularExpression(
            '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u',
            $content
        );
    }
}
