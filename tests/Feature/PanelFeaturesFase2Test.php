<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelFeaturesFase2Test extends TestCase
{
    use RefreshDatabase;

    public function test_event_maps_url_and_google_calendar_utc_format(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();
        $event = $invitation->events()->firstOrFail();

        $event->update([
            'maps_url' => 'https://maps.google.com/?q=-5.135399,119.423790',
        ]);

        $this->assertSame('https://maps.google.com/?q=-5.135399,119.423790', $event->fresh()->googleMapsUrl());

        $calUrl = $event->fresh()->googleCalendarUrl($invitation);
        $this->assertStringContainsString('calendar.google.com/calendar/render?action=TEMPLATE', $calUrl);
        $this->assertMatchesRegularExpression('/dates=\d{8}T\d{6}Z\/\d{8}T\d{6}Z/', $calUrl);
        $this->assertStringContainsString('location='.rawurlencode(trim($event->venue_name.', '.$event->address)), $calUrl);
    }

    public function test_per_event_rsvp_enforces_adult_child_limits_and_custom_thanks(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();
        $invitation->update([
            'rsvp_mode' => 'per_acara',
            'rsvp_thanks_attending' => 'Sampai jumpa di hari bahagia kami.',
            'rsvp_thanks_declined' => 'Terima kasih atas doa restunya dari jauh.',
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->firstOrFail();
        $guest->update([
            'max_adults' => 2,
            'max_children' => 1,
        ]);

        $event = $invitation->events()->firstOrFail();

        // Exceeding max_children must fail server validation
        $invalid = $this->postJson(route('invitation.rsvp.store', ['slug' => $invitation->slug]), [
            'guest_code' => $guest->code,
            'name' => $guest->name,
            'events' => [
                $event->id => ['attending' => 1, 'adults' => 2, 'children' => 5],
            ],
        ]);
        $invalid->assertStatus(422);

        // Valid per-event attending RSVP
        $valid = $this->postJson(route('invitation.rsvp.store', ['slug' => $invitation->slug]), [
            'guest_code' => $guest->code,
            'name' => $guest->name,
            'events' => [
                $event->id => ['attending' => 1, 'adults' => 2, 'children' => 1],
            ],
        ]);
        $valid->assertOk()->assertJsonPath('message', 'Sampai jumpa di hari bahagia kami.');

        $this->assertDatabaseHas('rsvp_event', [
            'event_id' => $event->id,
            'attending' => true,
            'adults' => 2,
            'children' => 1,
        ]);

        // Declining returns declined thank-you message and zeroes pax
        $declined = $this->postJson(route('invitation.rsvp.store', ['slug' => $invitation->slug]), [
            'guest_code' => $guest->code,
            'name' => $guest->name,
            'events' => [
                $event->id => ['attending' => 0, 'adults' => 2, 'children' => 1],
            ],
        ]);
        $declined->assertOk()
            ->assertJsonPath('message', 'Terima kasih atas doa restunya dari jauh.')
            ->assertJsonPath('rsvp.pax', 0);

        // Cross-invitation event ID is rejected
        $otherEvent = Event::create([
            'invitation_id' => Invitation::create([
                'user_id' => $invitation->user_id,
                'slug' => 'other-event-check',
                'title' => 'Other',
                'bride_name' => 'A',
                'bride_nickname' => 'A',
                'groom_name' => 'B',
                'groom_nickname' => 'B',
                'is_published' => true,
            ])->id,
            'title' => 'Foreign Event',
            'starts_at' => now()->addDays(10),
            'venue_name' => 'Hall',
            'address' => 'Street',
        ]);

        $cross = $this->postJson(route('invitation.rsvp.store', ['slug' => $invitation->slug]), [
            'guest_code' => $guest->code,
            'name' => $guest->name,
            'events' => [
                $otherEvent->id => ['attending' => 1, 'adults' => 1, 'children' => 0],
            ],
        ]);
        $cross->assertStatus(422);
    }

    public function test_gift_whatsapp_url_normalizes_number_and_omits_sensitive_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();
        $invitation->update([
            'gift_whatsapp_number' => '0812-3456-7890',
            'gift_whatsapp_template' => 'Halo, saya {nama} konfirmasi kado untuk {mempelai}.',
        ]);

        $url = $invitation->giftWhatsappUrl('Bapak Budi');
        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString('Bapak%20Budi', $url);
    }
}
