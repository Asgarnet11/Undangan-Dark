<?php

namespace Tests\Feature;

use App\Models\Invitation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarkBohoCoverAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_svg_assets_exist_under_30kb_and_have_aria_hidden(): void
    {
        $expectedSvgs = [
            'pampas-left.svg',
            'pampas-right.svg',
            'arch-frame.svg',
            'sun-moon.svg',
            'leaf-branch.svg',
            'dried-flower-cluster.svg',
            'divider-boho.svg',
            'monogram-frame.svg',
            'grain.svg',
            'bg-pattern.svg',
        ];

        foreach ($expectedSvgs as $file) {
            $path = public_path('themes/dark-boho/svg/'.$file);
            $this->assertFileExists($path);
            $this->assertLessThan(30 * 1024, filesize($path));
            $content = (string) file_get_contents($path);
            $this->assertStringContainsString('aria-hidden="true"', $content);
        }
    }

    public function test_cover_renders_arch_doors_pampas_particles_and_preload_none_music(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/raka-nadia');

        $response->assertOk();
        $response->assertSee('id="db-bgm"', false);
        $response->assertSee('preload="none"', false);
        $response->assertSee('themes/dark-boho/audio/boho-serenade.mp3', false);
        $response->assertSee('themes/dark-boho/img/cover-boho.webp', false);
        $response->assertSee('db-arch-door--left', false);
        $response->assertSee('db-arch-door--right', false);
        $response->assertSee('pampas-left-stem-1', false);
        $response->assertSee('pampas-right-stem-1', false);
        $response->assertSee('id="arch-outer"', false);
        $response->assertSee('id="celestial-sun"', false);
        $response->assertSee('id="branch-stem"', false);
        $response->assertSee('id="dried-stems"', false);
        $response->assertSee('id="divider-center"', false);
        $response->assertSee('id="monogram-ring"', false);
    }

    public function test_cover_styles_and_usephoto_option_change_rendered_markup(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $config = $invitation->resolvedThemeConfig();
        $config['cover']['style'] = 'new';
        $config['cover']['usephoto'] = false;
        $invitation->update(['theme_config' => $config]);

        $response = $this->get('/raka-nadia');

        $response->assertOk();
        $response->assertSee('data-cover-style="new"', false);
        $response->assertSee('db-cover--new', false);
        $response->assertSee('data-cover-use-photo="false"', false);
        $response->assertDontSee('class="db-cover-photo"', false);
    }
}
