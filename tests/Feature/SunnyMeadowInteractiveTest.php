<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SunnyMeadowInteractiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_sunny_meadow_rsvp_submission_and_display(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->first();

        // 1. Submit RSVP
        $response = $this->postJson(route('invitation.rsvp.store', ['slug' => $invitation->slug]), [
            'guest_code' => $guest->code,
            'name' => $guest->name,
            'attending' => true,
            'pax' => 2,
            'message' => 'Selamat untuk kedua mempelai!',
        ]);

        $response->assertOk()->assertJsonPath('ok', true);

        // 2. Assert saved in database
        $this->assertDatabaseHas('rsvps', [
            'invitation_id' => $invitation->id,
            'name' => $guest->name,
            'attending' => 1,
            'pax' => 2,
        ]);
    }

    public function test_sunny_meadow_wishes_submission_and_display(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->first();

        // 1. Submit Wish
        $response = $this->postJson(route('invitation.wishes.store', ['slug' => $invitation->slug]), [
            'guest_code' => $guest->code,
            'name' => 'Budi Santoso',
            'origin' => 'Jakarta Selatan',
            'message' => 'Semoga sakinah mawaddah warahmah selalu.',
        ]);

        $response->assertCreated()->assertJsonPath('ok', true);

        // 2. Assert saved in database
        $this->assertDatabaseHas('wishes', [
            'invitation_id' => $invitation->id,
            'name' => 'Budi Santoso',
            'origin' => 'Jakarta Selatan',
        ]);
    }

    public function test_sunny_meadow_renders_gift_panel_and_closing(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk();
        $response->assertSee('section-gift', false);
        $response->assertSee('sm-gift-panel', false);
        $response->assertSee('section-closing', false);
        $response->assertSee('sm-closing-names', false);
        $response->assertSee('sm-svg-heart-closing', false);
    }
}
