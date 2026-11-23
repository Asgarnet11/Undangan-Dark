<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugisHeritageFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bugis_heritage_theme_is_registered_with_four_presets(): void
    {
        $options = ThemeRegistry::options();
        $this->assertArrayHasKey('bugis-heritage', $options);
        $this->assertSame('Bugis Heritage', $options['bugis-heritage']);

        $presets = ThemeRegistry::presets('bugis-heritage');
        $this->assertSame(['marun-emas', 'lumut-emas', 'terong-emas', 'krem-sutra'], array_keys($presets));
    }

    public function test_bugis_heritage_renders_foundation_with_local_fonts_and_no_external_cdn(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk();
        $response->assertSee('theme-bugis-heritage', false);
        $response->assertSee('themes/bugis-heritage/fonts/fonts.css', false);
        $response->assertSee('themes/bugis-heritage/theme.css', false);
        $response->assertSee('themes/bugis-heritage/theme.js', false);
        $response->assertDontSee('fonts.googleapis.com', false);
        $response->assertDontSee('fonts.gstatic.com', false);
    }

    public function test_bugis_heritage_personalization_and_calendar(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->first();

        // 1. Valid guest code personalization
        $validResponse = $this->get('/'.$invitation->slug.'?to='.$guest->code);
        $validResponse->assertOk();
        $validResponse->assertSee($guest->name);
        $validResponse->assertSee($guest->code);

        // 2. Invalid guest code fallback
        $invalidResponse = $this->get('/'.$invitation->slug.'?to=KODETIDAKVALID');
        $invalidResponse->assertOk();
        $invalidResponse->assertSee('Tamu Undangan');

        // 3. Calendar .ics download
        $event = Event::where('invitation_id', $invitation->id)->first();
        $icsResponse = $this->get(route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $event->id]));
        $icsResponse->assertOk();
        $this->assertStringContainsString('text/calendar', (string) $icsResponse->headers->get('content-type'));
    }

    public function test_bugis_heritage_assets_exist_and_conform_to_rules(): void
    {
        $themePath = public_path('themes/bugis-heritage');

        $requiredSvgs = [
            'walasuji-tile.svg',
            'walasuji-frame.svg',
            'lipa-check-tile.svg',
            'border-gold.svg',
            'corner-ornament.svg',
            'rumah-panggung.svg',
            'pacci-leaf.svg',
            'divider-diamond.svg',
            'monogram-badge.svg',
            'grain-silk.svg',
            'bg-pattern.svg',
            'glossary-icon.svg',
        ];

        foreach ($requiredSvgs as $svg) {
            $this->assertFileExists("{$themePath}/svg/{$svg}");
            $this->assertLessThan(30720, filesize("{$themePath}/svg/{$svg}"), "SVG {$svg} exceeds 30KB limit");
        }

        $requiredFonts = [
            'cormorant-garamond-latin-500-normal.woff2',
            'cormorant-garamond-latin-600-normal.woff2',
            'cormorant-garamond-latin-400-italic.woff2',
            'jost-latin-300-normal.woff2',
            'jost-latin-400-normal.woff2',
            'jost-latin-500-normal.woff2',
            'amiri-arabic-400-normal.woff2',
            'allura-latin-400-normal.woff2',
            'noto-sans-buginese-400-normal.woff2',
        ];

        foreach ($requiredFonts as $font) {
            $this->assertFileExists("{$themePath}/fonts/{$font}");
        }

        $this->assertFileExists("{$themePath}/audio/heritage-instrumental.mp3");
        $this->assertFileExists("{$themePath}/img/og-preview.jpg");
        $this->assertFileExists("{$themePath}/ASSET-USAGE.md");

        // Zero emojis rule check
        $assetUsageContent = file_get_contents("{$themePath}/ASSET-USAGE.md");
        $this->assertDoesNotMatchRegularExpression(
            '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u',
            $assetUsageContent
        );
    }
}
