<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\SeatingTable;
use App\Support\SeatingValidator;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PanelFeaturesFase3Test extends TestCase
{
    use RefreshDatabase;

    public function test_seating_validator_enforces_ownership_capacity_and_unique_seat_labels(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();
        $table = SeatingTable::create([
            'invitation_id' => $invitation->id,
            'name' => 'Meja VIP 1',
            'capacity' => 4,
            'allow_over_capacity' => false,
        ]);

        $guest1 = Guest::where('invitation_id', $invitation->id)->firstOrFail();
        $guest1->update([
            'seating_table_id' => $table->id,
            'seat_label' => 'A1',
            'max_pax' => 2,
        ]);

        // Duplicate seat label on same table rejected
        try {
            SeatingValidator::validateAssignment($invitation->id, $table->id, 'a1', 1);
            $this->fail('Expected ValidationException for duplicate seat label.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('seat_label', $e->errors());
        }

        // Capacity overflow (2 + 3 > 4) rejected
        try {
            SeatingValidator::validateAssignment($invitation->id, $table->id, 'A2', 3);
            $this->fail('Expected ValidationException for table capacity overflow.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('seating_table_id', $e->errors());
        }

        // Cross-invitation table rejected
        $otherInvitation = Invitation::create([
            'user_id' => $invitation->user_id,
            'slug' => 'other-seating-inv',
            'title' => 'Other',
            'bride_name' => 'A',
            'bride_nickname' => 'A',
            'groom_name' => 'B',
            'groom_nickname' => 'B',
            'is_published' => true,
        ]);

        try {
            SeatingValidator::validateAssignment($otherInvitation->id, $table->id, 'B1', 1);
            $this->fail('Expected ValidationException for cross-invitation table.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('seating_table_id', $e->errors());
        }

        // Valid assignment within capacity passes
        SeatingValidator::validateAssignment($invitation->id, $table->id, 'A2', 2);
        $this->assertTrue(true);
    }

    public function test_guest_visible_seating_info_and_checkin_scanner_response(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();
        $invitation->update([
            'seat_info_visibility' => 'after_checkin',
        ]);

        $table = SeatingTable::create([
            'invitation_id' => $invitation->id,
            'name' => 'Meja Melati 2',
            'capacity' => 8,
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->firstOrFail();
        $guest->update([
            'seating_table_id' => $table->id,
            'seat_label' => '5-6',
            'checked_in_at' => null,
        ]);

        // Before check-in, after_checkin hides seating info
        $this->assertNull($guest->fresh()->visibleSeatingInfo());

        // Check-in returns table_name and seat_label
        $response = $this->postJson(route('invitation.check-in.process', ['slug' => $invitation->slug]), [
            'code' => $guest->code,
        ]);

        $response->assertOk()
            ->assertJsonPath('guest.table_name', 'Meja Melati 2')
            ->assertJsonPath('guest.seat_label', '5-6');

        // After check-in, seating info is visible
        $info = $guest->fresh()->visibleSeatingInfo();
        $this->assertNotNull($info);
        $this->assertTrue($info['assigned']);
        $this->assertSame('Meja Melati 2', $info['table_name']);
        $this->assertSame('5-6', $info['seat_label']);

        // Hidden mode hides even after check-in
        $invitation->update(['seat_info_visibility' => 'hidden']);
        $this->assertNull($guest->fresh()->visibleSeatingInfo());
    }
}
