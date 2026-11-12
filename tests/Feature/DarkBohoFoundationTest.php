<?php

namespace Tests\Feature;

use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarkBohoFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dark_boho_theme_is_registered_with_four_presets(): void
    {
        $options = ThemeRegistry::options();
        $this->assertArrayHasKey('dark-boho', $options);
        $this->assertSame('Dark Boho', $options['dark-boho']);

        $presets = ThemeRegistry::presets('dark-boho');
        $this->assertSame(['terracotta', 'sage', 'dusty-rose', 'gold'], array_keys($presets));
    }

    public function test_public_invitation_renders_foundation_with_local_fonts_and_no_external_cdn(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/raka-nadia');

        $response->assertOk();
        $response->assertSee('Nadia');
        $response->assertSee('Raka');
        $response->assertSee('Tamu Undangan');
        $response->assertSee('themes/dark-boho/fonts/fonts.css', false);
        $response->assertSee('themes/dark-boho/theme.css', false);
        $response->assertSee('themes/dark-boho/theme.js', false);
        $response->assertDontSee('fonts.googleapis.com', false);
        $response->assertDontSee('fonts.gstatic.com', false);
    }

    public function test_guest_code_query_personalizes_cover_guest_name(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/raka-nadia?to=KEL01');

        $response->assertOk();
        $response->assertSee('Bapak Agus Salim &amp; Keluarga', false);
    }
}
