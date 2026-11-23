<?php

namespace Tests\Feature;

use App\Models\GiftAccount;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Wish;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugisHeritageSharedComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_rsvp_submission_and_qr_checkin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->first();

        // 1. Digital guest book QR check-in displays for guest
        $pageResponse = $this->get('/'.$invitation->slug.'?to='.$guest->code);
        $pageResponse->assertOk();
        $pageResponse->assertSee('BUKU TAMU DIGITAL');
        $pageResponse->assertSee($guest->code);

        // 2. RSVP submission
        $rsvpResponse = $this->post(route('invitation.rsvp.store', ['slug' => $invitation->slug]), [
            'guest_code' => $guest->code,
            'name' => 'Keluarga Andi Petta',
            'attending' => '1',
            'pax' => 2,
            'message' => 'Insya Allah kami hadir memenuhi undangan.',
        ]);

        $rsvpResponse->assertRedirect();
        $this->assertDatabaseHas('rsvps', [
            'invitation_id' => $invitation->id,
            'name' => 'Keluarga Andi Petta',
            'attending' => true,
            'pax' => 2,
        ]);
    }

    public function test_cashless_gift_accounts_and_no_panai_claim(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        // Add a gift account
        GiftAccount::create([
            'invitation_id' => $invitation->id,
            'bank_name' => 'Bank Mandiri',
            'account_number' => '1420012345678',
            'account_name' => 'Andi Fitria',
        ]);

        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();

        // Must render cashless gift section with masked number
        $response->assertSee('Bank Mandiri');
        $response->assertSee('•••• 5678');
        $response->assertSee('data-gift-panel', false);

        // Strict Rule 9: No mention of uang belanja or uang panai or dowry amounts
        $html = strtolower($response->getContent());
        $this->assertStringNotContainsString('panai', $html);
        $this->assertStringNotContainsString('uang belanja', $html);
        $this->assertStringNotContainsString('sompa', $html);
    }

    public function test_wishes_submission_and_display(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        // Submit wish
        $wishResponse = $this->post(route('invitation.wishes.store', ['slug' => $invitation->slug]), [
            'name' => 'Andi Nurhaliza',
            'origin' => 'Makassar',
            'message' => 'Selamat menempuh hidup baru, semoga sakinah mawaddah warahmah.',
        ]);

        $wishResponse->assertRedirect();

        $this->assertDatabaseHas('wishes', [
            'invitation_id' => $invitation->id,
            'name' => 'Andi Nurhaliza',
            'message' => 'Selamat menempuh hidup baru, semoga sakinah mawaddah warahmah.',
        ]);

        // Verify it displays on the page
        $pageResponse = $this->get('/'.$invitation->slug);
        $pageResponse->assertOk();
        $pageResponse->assertSee('Andi Nurhaliza');
        $pageResponse->assertSee('Makassar');
        $pageResponse->assertSee('Selamat menempuh hidup baru');
    }

    public function test_love_story_timeline_and_credits_modal(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'bugis-heritage',
            'theme_config' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ]);

        // 1. Credits button & modal rendered on normal page load
        $response = $this->get('/'.$invitation->slug);
        $response->assertOk();
        $response->assertSee('id="bh-credits-btn"', false);
        $response->assertSee('id="bh-credits-modal"', false);
        $response->assertSee('Cormorant Garamond');
        $response->assertSee('Noto Sans Buginese');
        $response->assertSee(config('app.vendor_name', 'a2dev.my.id'));

        // 2. Love story section rendered when chapters relation is loaded
        $invitation->setRelation('loveStoryChapters', collect([
            (object) [
                'time_label' => 'Oktober 2023',
                'title' => 'Pertemuan Pertama',
                'body' => 'Pertama kali dipertemukan dalam acara silaturahmi keluarga di Bone.',
                'photo_path' => null,
            ],
        ]));

        $rendered = view('themes.bugis-heritage.index', [
            'invitation' => $invitation,
            'guest' => null,
            'guestCode' => '',
            'existingRsvp' => null,
            'wishes' => collect(),
            'themeConfig' => ThemeRegistry::defaultConfig('bugis-heritage'),
        ])->render();

        $this->assertStringContainsString('id="section-love-story"', $rendered);
        $this->assertStringContainsString('Pertemuan Pertama', $rendered);
        $this->assertStringContainsString('Oktober 2023', $rendered);
        $this->assertStringContainsString('Pertama kali dipertemukan', $rendered);
    }
}
