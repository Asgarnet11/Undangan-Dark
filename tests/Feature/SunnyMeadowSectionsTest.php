<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SunnyMeadowSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sunny_meadow_renders_all_content_sections(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk();

        // 1. Countdown
        $response->assertSee('section-countdown', false);
        $response->assertSee('sm-countdown-box--days', false);
        $response->assertSee('sm-countdown-box--hours', false);
        $response->assertSee('sm-countdown-box--minutes', false);
        $response->assertSee('sm-countdown-box--seconds', false);

        // 2. Verse
        $response->assertSee('section-verse', false);
        $response->assertSee('sm-text-arabic', false);

        // 3. Couple
        $response->assertSee('section-couple', false);
        $response->assertSee('sm-photo-frame-wrap', false);
        $response->assertSee($invitation->bride_name);
        $response->assertSee($invitation->groom_name);

        // 4. Events
        $response->assertSee('section-events', false);
        $response->assertSee('sm-scallop-accent', false);
        $response->assertSee('sm-svg-wave', false);
        $response->assertSee('google.com/maps', false);

        // 5. Gallery
        $response->assertSee('section-gallery', false);
        $response->assertSee('sm-gallery-grid', false);
        $response->assertSee('sm-lightbox', false);

        // 6. Bottom Navigation
        $response->assertSee('sm-bottom-nav', false);
        $response->assertSee('sm-bottom-nav-track', false);
        $response->assertSee($invitation->monogramText());
    }
}
