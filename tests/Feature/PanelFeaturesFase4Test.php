<?php

namespace Tests\Feature;

use App\Models\GiftRegistryLink;
use App\Models\Invitation;
use App\Models\QrisAccount;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelFeaturesFase4Test extends TestCase
{
    use RefreshDatabase;

    public function test_gifts_endpoint_returns_active_qris_and_https_registry_links_respecting_flags(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();
        $invitation->update([
            'feature_flags' => [
                'qris' => true,
                'registry' => true,
            ],
        ]);

        QrisAccount::create([
            'invitation_id' => $invitation->id,
            'label' => 'QRIS Mempelai',
            'image_path' => 'qris-images/sample.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        QrisAccount::create([
            'invitation_id' => $invitation->id,
            'label' => 'QRIS Nonaktif',
            'image_path' => 'qris-images/inactive.png',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        GiftRegistryLink::create([
            'invitation_id' => $invitation->id,
            'label' => 'Registry Resmi',
            'url' => 'https://example.com/registry',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Unsafe URL scheme must not be exposed publicly even if present in DB
        GiftRegistryLink::create([
            'invitation_id' => $invitation->id,
            'label' => 'Registry Berbahaya',
            'url' => 'javascript:alert(1)',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->getJson(route('invitation.gifts.index', ['slug' => $invitation->slug]));
        $response->assertOk()
            ->assertJsonCount(1, 'qris')
            ->assertJsonPath('qris.0.label', 'QRIS Mempelai')
            ->assertJsonCount(1, 'registries')
            ->assertJsonPath('registries.0.label', 'Registry Resmi');

        // Disable feature flags -> empty arrays
        $invitation->update([
            'feature_flags' => [
                'qris' => false,
                'registry' => false,
            ],
        ]);

        $disabled = $this->getJson(route('invitation.gifts.index', ['slug' => $invitation->slug]));
        $disabled->assertOk()
            ->assertJsonCount(0, 'qris')
            ->assertJsonCount(0, 'registries');
    }
}
