<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SunnyMeadowCoverAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_fifteen_required_svg_assets_exist_under_thirty_kilobytes(): void
    {
        $assets = [
            'corner-tl.svg',
            'corner-br.svg',
            'flower-big.svg',
            'flower-small.svg',
            'sun-doodle.svg',
            'cloud.svg',
            'confetti-set.svg',
            'wave-divider.svg',
            'scallop-edge.svg',
            'photo-frame-flower.svg',
            'photo-frame-blob.svg',
            'heart-line.svg',
            'leaf-sprig.svg',
            'star-small.svg',
            'bg-pattern.svg',
            'monogram-badge.svg',
        ];

        foreach ($assets as $file) {
            $path = public_path("themes/sunny-meadow/svg/{$file}");
            $this->assertFileExists($path, "SVG asset {$file} does not exist.");
            $this->assertLessThan(30 * 1024, filesize($path), "SVG asset {$file} exceeds 30KB limit.");
        }

        $this->assertFileExists(public_path('themes/sunny-meadow/ASSET-USAGE.md'));
    }

    public function test_cover_renders_with_personalization_and_required_elements(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->first();

        $response = $this->get('/'.$invitation->slug.'?to='.$guest->code);

        $response->assertOk();
        $response->assertSee('sm-cover', false);
        $response->assertSee($guest->name);
        $response->assertSee('Buka Undangan');
        $response->assertSee('sm-svg-sun', false);
        $response->assertSee('sm-svg-cloud', false);
        $response->assertSee('sm-svg-corner', false);
        $response->assertSee('sm-anim-bobbing', false);
    }
}
