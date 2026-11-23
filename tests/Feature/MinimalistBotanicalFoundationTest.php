<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinimalistBotanicalFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_minimalist_botanical_theme_is_registered_with_four_presets(): void
    {
        $options = ThemeRegistry::options();
        $this->assertArrayHasKey('minimalist-botanical', $options);
        $this->assertSame('Minimalist Botanical', $options['minimalist-botanical']);

        $presets = ThemeRegistry::presets('minimalist-botanical');
        $this->assertSame(['olive', 'eucalyptus', 'champagne', 'white-gold'], array_keys($presets));
    }

    public function test_minimalist_botanical_renders_foundation_with_local_fonts_and_no_external_cdn(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'minimalist-botanical',
            'theme_config' => ThemeRegistry::defaultConfig('minimalist-botanical'),
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk();
        $response->assertSee('theme-minimalist-botanical', false);
        $response->assertSee('themes/minimalist-botanical/fonts/fonts.css', false);
        $response->assertSee('themes/minimalist-botanical/theme.css', false);
        $response->assertSee('themes/minimalist-botanical/theme.js', false);
        $response->assertDontSee('fonts.googleapis.com', false);
        $response->assertDontSee('fonts.gstatic.com', false);
    }

    public function test_minimalist_botanical_personalization_and_calendar(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'minimalist-botanical',
            'theme_config' => ThemeRegistry::defaultConfig('minimalist-botanical'),
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
}
