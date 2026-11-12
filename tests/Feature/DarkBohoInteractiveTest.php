<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\Wish;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarkBohoInteractiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_code_personalizes_cover_and_rsvp_while_invalid_code_falls_back_safely(): void
    {
        $this->seed(DatabaseSeeder::class);

        $validResponse = $this->get('/raka-nadia?to=KEL01');
        $validResponse->assertOk();
        $validResponse->assertSee('Bapak Agus Salim &amp; Keluarga', false);
        $validResponse->assertSee('value="KEL01"', false);
        $validResponse->assertSee('max="4"', false);

        $guest = Guest::where('code', 'KEL01')->firstOrFail();
        $this->assertNotNull($guest->opened_at);

        $invalidResponse = $this->get('/raka-nadia?to=TIDAKADA');
        $invalidResponse->assertOk();
        $invalidResponse->assertSee('Tamu Undangan');
        $invalidResponse->assertDontSee('Bapak Agus Salim');
        $invalidResponse->assertDontSee('Ibu Kartika Sari');
    }

    public function test_rsvp_validates_sanitizes_updates_single_guest_record_and_enforces_max_pax(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $guest = Guest::where('invitation_id', $invitation->id)->where('code', 'SHB01')->firstOrFail();

        $tooManyPax = $this->postJson(route('invitation.rsvp.store', ['slug' => 'raka-nadia']), [
            'guest_code' => 'SHB01',
            'name' => 'Ibu Kartika Sari',
            'attending' => true,
            'pax' => 5,
        ]);
        $tooManyPax->assertUnprocessable()->assertJsonValidationErrors(['pax']);

        $firstSubmit = $this->postJson(route('invitation.rsvp.store', ['slug' => 'raka-nadia']), [
            'guest_code' => 'SHB01',
            'name' => '<b>Ibu Kartika Sari</b>',
            'attending' => true,
            'pax' => 2,
            'message' => '<script>alert(1)</script>Insyaallah hadir tepat waktu.',
        ]);

        $firstSubmit->assertOk()->assertJsonPath('ok', true);
        $this->assertDatabaseHas('rsvps', [
            'invitation_id' => $invitation->id,
            'guest_id' => $guest->id,
            'name' => 'Ibu Kartika Sari',
            'attending' => 1,
            'pax' => 2,
            'message' => 'alert(1)Insyaallah hadir tepat waktu.',
        ]);

        $secondSubmit = $this->postJson(route('invitation.rsvp.store', ['slug' => 'raka-nadia']), [
            'guest_code' => 'SHB01',
            'name' => 'Ibu Kartika Sari',
            'attending' => false,
            'pax' => 2,
            'message' => 'Mohon maaf berhalangan hadir.',
        ]);

        $secondSubmit->assertOk();
        $this->assertSame(1, Rsvp::where('invitation_id', $invitation->id)->where('guest_id', $guest->id)->count());
        $this->assertDatabaseHas('rsvps', [
            'guest_id' => $guest->id,
            'attending' => 0,
            'pax' => 0,
            'message' => 'Mohon maaf berhalangan hadir.',
        ]);
    }

    public function test_livestream_renders_only_when_enabled_and_url_is_safe(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/raka-nadia')
            ->assertOk()
            ->assertDontSee('id="section-livestream"', false);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $config = $invitation->resolvedThemeConfig();
        $config['sections']['livestream'] = true;
        $invitation->update([
            'theme_config' => $config,
            'livestream_url' => 'https://youtube.com/live/raka-nadia',
            'livestream_note' => 'Tayangan langsung pukul 08.00 WIB.',
        ]);

        $this->get('/raka-nadia')
            ->assertOk()
            ->assertSee('id="section-livestream"', false)
            ->assertSee('https://youtube.com/live/raka-nadia', false)
            ->assertSee('Tayangan langsung pukul 08.00 WIB.');
    }

    public function test_gift_accounts_are_hidden_until_opened_respect_inline_option_and_404_when_disabled(): void
    {
        $this->seed(DatabaseSeeder::class);

        $initialResponse = $this->get('/raka-nadia');
        $initialResponse->assertOk();
        $initialResponse->assertSee('id="section-gift"', false);
        $initialResponse->assertSee('Bank BCA');
        $initialResponse->assertDontSee('8465021940', false);
        $initialResponse->assertDontSee('1370019284750', false);

        $giftJson = $this->getJson(route('invitation.gifts.index', ['slug' => 'raka-nadia']));
        $giftJson->assertOk()
            ->assertJsonFragment(['account_number' => '8465021940'])
            ->assertJsonFragment(['account_number' => '1370019284750']);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $config = $invitation->resolvedThemeConfig();
        $config['layout']['giftInline'] = false;
        $invitation->update(['theme_config' => $config]);

        $floatingResponse = $this->get('/raka-nadia');
        $floatingResponse->assertOk();
        $floatingResponse->assertDontSee('id="section-gift"', false);
        $floatingResponse->assertSee('id="db-gift-fab"', false);
        $floatingResponse->assertSee('id="db-gift-modal"', false);

        $config['sections']['gift'] = false;
        $invitation->update(['theme_config' => $config]);

        $this->get('/raka-nadia')
            ->assertOk()
            ->assertDontSee('id="section-gift"', false)
            ->assertDontSee('id="db-gift-fab"', false);

        $this->getJson(route('invitation.gifts.index', ['slug' => 'raka-nadia']))
            ->assertNotFound();
    }

    public function test_wishes_submission_sanitizes_paginates_and_respects_moderation(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();

        $invalid = $this->postJson(route('invitation.wishes.store', ['slug' => 'raka-nadia']), [
            'name' => 'A',
            'message' => str_repeat('x', 501),
        ]);
        $invalid->assertUnprocessable()->assertJsonValidationErrors(['name', 'message']);

        $created = $this->postJson(route('invitation.wishes.store', ['slug' => 'raka-nadia']), [
            'name' => '<i>Budi Santoso</i>',
            'origin' => 'Semarang',
            'message' => '<script>bad()</script>Semoga berbahagia selamanya.',
        ]);
        $created->assertCreated()
            ->assertJsonPath('wish.name', 'Budi Santoso')
            ->assertJsonPath('wish.message', 'bad()Semoga berbahagia selamanya.');

        Wish::create([
            'invitation_id' => $invitation->id,
            'name' => 'Tamu Disembunyikan',
            'message' => 'Ucapan tersembunyi.',
            'is_visible' => false,
        ]);

        for ($i = 1; $i <= 4; $i++) {
            Wish::create([
                'invitation_id' => $invitation->id,
                'name' => 'Pengirim '.$i,
                'origin' => 'Yogyakarta',
                'message' => 'Doa terbaik ke-'.$i,
                'is_visible' => true,
            ]);
        }

        $pageOneHtml = $this->get('/raka-nadia')->assertOk()->getContent();
        $this->assertStringContainsString('id="db-wishes-load-more"', $pageOneHtml);
        $this->assertStringNotContainsString('Tamu Disembunyikan', $pageOneHtml);

        $pageTwo = $this->getJson(route('invitation.wishes.index', ['slug' => 'raka-nadia', 'page' => 2]));
        $pageTwo->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonMissing(['name' => 'Tamu Disembunyikan']);
    }

    public function test_rate_limit_blocks_excessive_wish_submissions(): void
    {
        $this->seed(DatabaseSeeder::class);

        for ($i = 1; $i <= 4; $i++) {
            $this->postJson(route('invitation.wishes.store', ['slug' => 'raka-nadia']), [
                'name' => 'Tamu '.$i,
                'message' => 'Selamat menempuh hidup baru ke-'.$i,
            ])->assertCreated();
        }

        $this->postJson(route('invitation.wishes.store', ['slug' => 'raka-nadia']), [
            'name' => 'Tamu 5',
            'message' => 'Ucapan kelima dalam semenit',
        ])->assertTooManyRequests();
    }
}
