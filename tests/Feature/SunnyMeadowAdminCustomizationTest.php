<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SunnyMeadowAdminCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sunny_meadow_admin_options_and_presets(): void
    {
        $presets = ThemeRegistry::presets('sunny-meadow');
        $this->assertCount(4, $presets);
        $this->assertArrayHasKey('sunny', $presets);
        $this->assertArrayHasKey('mint-fresh', $presets);
        $this->assertArrayHasKey('coral-blush', $presets);
        $this->assertArrayHasKey('sky-pastel', $presets);

        $defaultConfig = ThemeRegistry::defaultConfig('sunny-meadow');
        $this->assertSame('sunny-meadow', $defaultConfig['theme']);
        $this->assertSame('sunny', $defaultConfig['preset']);
        $this->assertSame('flower', $defaultConfig['layout']['photoFrame']);
    }

    public function test_admin_can_access_preview_endpoint_with_sunny_meadow(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::first();
        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $response = $this->actingAs($user)->get(route('invitation.preview', ['invitation' => $invitation]));

        $response->assertOk();
        $response->assertSee('theme-sunny-meadow', false);
        $response->assertSee('data-preview-mode="true"', false);
    }
}

