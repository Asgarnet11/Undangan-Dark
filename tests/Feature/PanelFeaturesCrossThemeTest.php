<?php

namespace Tests\Feature;

use App\Models\GiftRegistryLink;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\LoveStoryChapter;
use App\Models\QrisAccount;
use App\Models\SeatingTable;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelFeaturesCrossThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_themes_render_shared_panel_features_consistently(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();
        $invitation->update([
            'feature_flags' => [
                'love_story' => true,
                'seating' => true,
                'qris' => true,
                'registry' => true,
            ],
            'seat_info_visibility' => 'always',
            'gift_whatsapp_number' => '081234567890',
        ]);

        LoveStoryChapter::create([
            'invitation_id' => $invitation->id,
            'title' => 'Bab Pertemuan',
            'time_label' => '2022',
            'body' => 'Awal cerita kami.',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $table = SeatingTable::create([
            'invitation_id' => $invitation->id,
            'name' => 'Meja Anggrek 1',
            'capacity' => 8,
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->firstOrFail();
        $guest->update([
            'seating_table_id' => $table->id,
            'seat_label' => '2',
        ]);

        QrisAccount::create([
            'invitation_id' => $invitation->id,
            'label' => 'QRIS Resmi',
            'image_path' => 'qris-images/demo.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        GiftRegistryLink::create([
            'invitation_id' => $invitation->id,
            'label' => 'Daftar Kado Utama',
            'url' => 'https://example.com/kado',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $themes = ['dark-boho', 'sunny-meadow', 'minimalist-botanical', 'bugis-heritage'];

        foreach ($themes as $themeKey) {
            $invitation->update([
                'theme' => $themeKey,
                'theme_config' => ThemeRegistry::defaultConfig($themeKey),
            ]);

            $response = $this->get('/'.$invitation->slug.'?to='.$guest->code);
            $response->assertOk();
            $response->assertSee('Bab Pertemuan');
            $response->assertSee('Meja Anggrek 1');
            $response->assertSee('Kursi: 2');
            $response->assertSee('QRIS Resmi');
            $response->assertSee('Daftar Kado Utama');
            $response->assertSee('Konfirmasi via WhatsApp');
        }
    }
}
