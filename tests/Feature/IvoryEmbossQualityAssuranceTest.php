<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class IvoryEmbossQualityAssuranceTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleInvitation(string $theme = 'ivory-emboss'): Invitation
    {
        $user = User::factory()->create();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'slug' => 'qa-ivory-'.uniqid(),
            'title' => 'Pernikahan Nisa & Dimas',
            'theme' => $theme,
            'bride_name' => 'Nisa Kartika',
            'bride_nickname' => 'Nisa',
            'groom_name' => 'Dimas Pratama',
            'groom_nickname' => 'Dimas',
            'opening_text' => 'Dengan memohon rahmat dan ridho Allah SWT.',
            'closing_text' => 'Terima kasih atas doa restu Anda.',
            'is_published' => true,
            'theme_config' => config("themes.themes.{$theme}.default_config", []),
        ]);

        Event::create([
            'invitation_id' => $invitation->id,
            'title' => 'Akad Nikah',
            'starts_at' => now()->addDays(14)->setTime(9, 0),
            'ends_at' => now()->addDays(14)->setTime(11, 0),
            'venue_name' => 'Pendopo Melati',
            'address' => 'Jl. Kemuning No. 8, Bandung',
            'sort_order' => 1,
        ]);

        return $invitation;
    }

    public function test_ivory_emboss_assets_exist_and_have_no_external_cdns(): void
    {
        $manifest = json_decode(File::get(base_path('tools/assets/manifest.json')), true);
        $this->assertIsArray($manifest);

        foreach ($manifest['reliefAssets'] as $asset) {
            $id = $asset['id'];
            $this->assertFileExists(public_path("themes/ivory-emboss/relief/{$id}.webp"));
            $this->assertFileExists(public_path("themes/ivory-emboss/relief/{$id}@2x.webp"));
        }

        foreach ($manifest['textureAssets'] as $tex) {
            $id = $tex['id'];
            $this->assertFileExists(public_path("themes/ivory-emboss/texture/{$id}.webp"));
        }

        $this->assertFileExists(public_path('themes/ivory-emboss/fonts/fonts.css'));
        $this->assertFileExists(public_path('themes/ivory-emboss/theme.css'));
        $this->assertFileExists(public_path('themes/ivory-emboss/theme.js'));
        $this->assertFileExists(public_path('themes/ivory-emboss/motion.config.js'));
        $this->assertFileExists(public_path('themes/ivory-emboss/ASSET-USAGE.md'));

        $css = File::get(public_path('themes/ivory-emboss/theme.css'));
        $fontsCss = File::get(public_path('themes/ivory-emboss/fonts/fonts.css'));

        $this->assertStringNotContainsString('fonts.googleapis.com', $css.$fontsCss);
        $this->assertStringNotContainsString('fonts.gstatic.com', $css.$fontsCss);
        $this->assertStringNotContainsString('cdn.jsdelivr.net', $css.$fontsCss);
    }

    public function test_cross_theme_parity_guest_personalization_and_audio_preload(): void
    {
        $invitation = $this->createSampleInvitation('ivory-emboss');

        $guest = Guest::create([
            'invitation_id' => $invitation->id,
            'name' => 'Ibu Ratna Dewi',
            'code' => 'RATNA01',
            'max_pax' => 2,
        ]);

        foreach (array_keys(config('themes.themes')) as $themeKey) {
            $invitation->update([
                'theme' => $themeKey,
                'theme_config' => config("themes.themes.{$themeKey}.default_config", []),
            ]);

            // Valid guest code
            $resValid = $this->get(route('invitation.show', ['slug' => $invitation->slug, 'to' => $guest->code]));
            $resValid->assertStatus(200);
            $resValid->assertSee('Ibu Ratna Dewi');
            $resValid->assertSee('Akad Nikah');
            $resValid->assertSee('preload="none"', false);

            // Invalid guest code falls back to Tamu Undangan
            $resInvalid = $this->get(route('invitation.show', ['slug' => $invitation->slug, 'to' => 'TIDAK_VALID_999']));
            $resInvalid->assertStatus(200);
            $resInvalid->assertSee('Tamu Undangan');
            $resInvalid->assertDontSee('Ibu Ratna Dewi');
        }
    }
}
