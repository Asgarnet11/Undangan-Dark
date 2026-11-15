<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SunnyMeadowAnimationPolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_css_contains_reduced_motion_and_all_specified_animation_rules(): void
    {
        $cssPath = public_path('themes/sunny-meadow/theme.css');
        $this->assertFileExists($cssPath);

        $css = file_get_contents($cssPath);

        // 1. Reduced motion support
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);

        // 2. Bobbing, spinning, drifting, wave-flowing, blooming, twinkling
        $this->assertStringContainsString('sm-bobbing', $css);
        $this->assertStringContainsString('sm-spin', $css);
        $this->assertStringContainsString('sm-drift', $css);
        $this->assertStringContainsString('sm-wave-flow', $css);
        $this->assertStringContainsString('sm-bloom', $css);
        $this->assertStringContainsString('sm-twinkle', $css);
        $this->assertStringContainsString('sm-bounce-card', $css);
        $this->assertStringContainsString('sm-hop', $css);
        $this->assertStringContainsString('sm-wish-pop', $css);
        $this->assertStringContainsString('sm-confetti-container', $css);
    }

    public function test_public_invitation_renders_motion_data_attributes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk();
        $response->assertSee('data-motion-confetti="true"', false);
        $response->assertSee('data-motion-decor="true"', false);
    }
}
