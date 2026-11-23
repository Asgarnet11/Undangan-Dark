<?php

namespace Tests\Feature;

use App\Models\AdatGlossaryEntry;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BugisHeritageQualityAssuranceTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_theme_asset_is_referenced_and_documented_with_zero_cdns_or_emojis(): void
    {
        $themeRoot = public_path('themes/bugis-heritage');
        $usageDoc = (string) File::get($themeRoot.'/ASSET-USAGE.md');

        $corpus = '';
        foreach (File::allFiles(resource_path('views/themes/bugis-heritage')) as $file) {
            $corpus .= "\n".File::get($file->getPathname());
        }
        foreach (File::allFiles(resource_path('views/components/bugis-heritage')) as $file) {
            $corpus .= "\n".File::get($file->getPathname());
        }
        foreach (File::allFiles($themeRoot) as $file) {
            if (in_array($file->getExtension(), ['css', 'js'], true)) {
                $corpus .= "\n".File::get($file->getPathname());
            }
        }

        // 1. Zero CDN assertion
        $this->assertDoesNotMatchRegularExpression(
            '/fonts\.googleapis\.com|fonts\.gstatic\.com|unpkg\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com/i',
            $corpus
        );

        // 2. All assets used & documented
        foreach (File::allFiles($themeRoot) as $file) {
            $filename = $file->getFilename();
            if ($filename === 'ASSET-USAGE.md') {
                continue;
            }

            $searchToken = $file->getExtension() === 'svg'
                ? pathinfo($filename, PATHINFO_FILENAME)
                : $filename;

            $this->assertStringContainsString($searchToken, $corpus, "Aset tidak terpakai: {$filename}");
            $this->assertStringContainsString($filename, $usageDoc, "Aset belum tercatat di ASSET-USAGE.md: {$filename}");
        }

        // 3. Zero Emoji assertion
        $this->assertDoesNotMatchRegularExpression('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', $corpus);
    }

    public function test_all_four_presets_render_with_proper_css_classes_and_design_tokens(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $presets = ['marun-emas', 'lumut-emas', 'terong-emas', 'krem-sutra'];

        foreach ($presets as $preset) {
            $config = ThemeRegistry::defaultConfig('bugis-heritage');
            $config['preset'] = $preset;

            $invitation->update([
                'theme' => 'bugis-heritage',
                'theme_config' => $config,
            ]);

            $response = $this->get('/'.$invitation->slug);
            $response->assertOk();
            $response->assertSee('theme-bugis-heritage', false);
            $response->assertSee("bh-preset-{$preset}", false);
            $response->assertSee('themes/bugis-heritage/fonts/fonts.css', false);
            $response->assertSee('themes/bugis-heritage/theme.css', false);
            $response->assertSee('themes/bugis-heritage/theme.js', false);
            $response->assertDontSee('fonts.googleapis.com', false);
        }
    }

    public function test_expired_invitation_renders_dedicated_bugis_heritage_expired_view(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
            'active_days_after_event' => 7,
        ]);
        $invitation->events()->update([
            'starts_at' => now()->subDays(60),
            'ends_at' => now()->subDays(60)->addHours(3),
        ]);

        $this->assertTrue($invitation->fresh()->isExpired());

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();
        $response->assertSee('Masa Undangan Selesai');
        $response->assertSee('theme-bugis-heritage', false);
        $response->assertSee('themes/bugis-heritage/fonts/fonts.css', false);
        $response->assertSee('themes/bugis-heritage/theme.css', false);
    }

    public function test_cultural_gate_compliance_in_rendered_theme(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $config = ThemeRegistry::defaultConfig('bugis-heritage');
        $config['adat']['showGlossary'] = true;

        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => $config,
        ]);

        // Create verified entry and draft entry
        AdatGlossaryEntry::create([
            'term' => 'Siri\' na Pacce',
            'latin_reading' => 'Siri\' na Pacce',
            'lontara_text' => 'ᨔᨗᨑᨗ ᨊ ᨄᨌᨙ',
            'meaning_id' => 'Falsafah hidup Bugis tentang harga diri dan empati kemanusiaan.',
            'status' => 'diverifikasi',
            'is_published' => true,
        ]);

        AdatGlossaryEntry::create([
            'term' => 'Istilah Draf Rahasia',
            'latin_reading' => 'Istilah Draf Rahasia',
            'lontara_text' => '',
            'meaning_id' => 'Istilah adat ini masih draf dan tidak boleh tampil ke publik.',
            'status' => 'draf',
            'is_published' => false,
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();
        $response->assertSee('Siri\' na Pacce');
        $response->assertDontSee('Istilah Draf Rahasia');
        $response->assertDontSee('uang panai');
        $response->assertDontSee('mahar sebesar');
    }

    public function test_modals_and_overlays_are_hidden_by_default_and_not_blocking_screen(): void
    {
        $this->seed(DatabaseSeeder::class);

        AdatGlossaryEntry::create([
            'term' => 'Sulapa Eppa',
            'latin_reading' => 'Sulapa Eppa',
            'lontara_text' => 'ᨔᨘᨒᨄ ᨕᨙᨄ',
            'meaning_id' => 'Bentuk belah ketupat empat sisi.',
            'status' => 'diverifikasi',
            'is_published' => true,
        ]);

        $invitation = Invitation::first();
        $config = ThemeRegistry::defaultConfig('bugis-heritage');
        $config['adat']['showGlossary'] = true;

        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => $config,
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // Modals must have hidden attribute in rendered HTML
        $response->assertSee('id="bh-credits-modal" class="bh-modal" role="dialog" aria-modal="true" aria-labelledby="bh-credits-title" hidden', false);
        $response->assertSee('id="bh-glossary-modal" class="bh-modal" role="dialog" aria-modal="true" aria-labelledby="bh-glossary-title" hidden', false);
        $response->assertSee('id="bh-gallery-lightbox" class="bh-lightbox" aria-hidden="true" role="dialog" aria-modal="true" hidden', false);
        $response->assertSee('id="bh-bottom-nav" aria-label="Navigasi bagian undangan" hidden', false);

        // CSS must enforce display none for hidden modals to prevent blocking overlay
        $css = (string) File::get(public_path('themes/bugis-heritage/theme.css'));
        $this->assertStringContainsString('[hidden]', $css);
        $this->assertStringContainsString('.bh-modal[hidden]', $css);
        $this->assertStringContainsString('.bh-modal:not(.is-open)', $css);
        $this->assertStringContainsString('.bh-lightbox[hidden]', $css);
        $this->assertStringContainsString('.bh-bottom-nav[hidden]', $css);
    }

    public function test_existing_themes_remain_intact_and_functional_without_regressions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();

        // 1. Dark Boho
        $invitation->update([
            'theme' => 'dark-boho',
            'theme_config' => ThemeRegistry::defaultConfig('dark-boho'),
        ]);
        $responseBoho = $this->get('/'.$invitation->slug);
        $responseBoho->assertOk();
        $responseBoho->assertSee('theme-dark-boho', false);

        // 2. Sunny Meadow
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);
        $responseSunny = $this->get('/'.$invitation->slug);
        $responseSunny->assertOk();
        $responseSunny->assertSee('theme-sunny-meadow', false);

        // 3. Minimalist Botanical
        $invitation->update([
            'theme' => 'minimalist-botanical',
            'theme_config' => ThemeRegistry::defaultConfig('minimalist-botanical'),
        ]);
        $responseBotanical = $this->get('/'.$invitation->slug);
        $responseBotanical->assertOk();
        $responseBotanical->assertSee('theme-minimalist-botanical', false);
    }
}
