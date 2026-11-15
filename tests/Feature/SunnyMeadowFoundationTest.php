<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SunnyMeadowFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sunny_meadow_theme_is_registered_with_four_presets(): void
    {
        $options = ThemeRegistry::options();
        $this->assertArrayHasKey('sunny-meadow', $options);
        $this->assertSame('Sunny Meadow', $options['sunny-meadow']);

        $presets = ThemeRegistry::presets('sunny-meadow');
        $this->assertSame(['sunny', 'mint-fresh', 'coral-blush', 'sky-pastel'], array_keys($presets));
    }

    public function test_sunny_meadow_renders_foundation_with_local_fonts_and_no_external_cdn(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk();
        $response->assertSee('theme-sunny-meadow', false);
        $response->assertSee('themes/sunny-meadow/fonts/fonts.css', false);
        $response->assertSee('themes/sunny-meadow/theme.css', false);
        $response->assertSee('themes/sunny-meadow/theme.js', false);
        $response->assertDontSee('fonts.googleapis.com', false);
        $response->assertDontSee('fonts.gstatic.com', false);
    }
}
