<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarkBohoAnimationPolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_js_is_lightweight_under_150kb_gzip_and_holds_heavy_animations_until_open(): void
    {
        $jsPath = public_path('themes/dark-boho/theme.js');
        $cssPath = public_path('themes/dark-boho/theme.css');

        $this->assertFileExists($jsPath);
        $this->assertFileExists($cssPath);

        $jsContent = (string) file_get_contents($jsPath);
        $cssContent = (string) file_get_contents($cssPath);

        $gzippedBytes = strlen((string) gzencode($jsContent, 9));
        $this->assertLessThan(150 * 1024, $gzippedBytes);

        // Pastikan seluruh elemen animasi Bagian 8.1 terdefinisi
        $this->assertStringContainsString('startOpenedAnimations', $jsContent);
        $this->assertStringContainsString('splitTitleWordsForMask', $jsContent);
        $this->assertStringContainsString('clipPath', $jsContent);
        $this->assertStringContainsString('strokeDashoffset', $jsContent);
        $this->assertStringContainsString('moveIndicatorTo', $jsContent);
        $this->assertStringContainsString('prefers-reduced-motion', $jsContent);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $cssContent);
        $this->assertStringContainsString('.db-nav-indicator', $cssContent);
        $this->assertStringContainsString('.db-word-mask', $cssContent);
    }

    public function test_rendered_page_includes_performance_seo_and_animation_hooks(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/raka-nadia');
        $response->assertOk();

        $html = $response->getContent();
        $this->assertStringContainsString('name="theme-color"', $html);
        $this->assertStringContainsString('property="og:title"', $html);
        $this->assertStringContainsString('id="db-nav-indicator"', $html);
        $this->assertStringContainsString('class="db-draw-path"', $html);
        $this->assertStringContainsString('preload="none"', $html);
        $this->assertStringContainsString('fetchpriority="high"', $html);
        $this->assertLessThanOrEqual(20, substr_count($html, 'class="db-particle"'));
    }
}
