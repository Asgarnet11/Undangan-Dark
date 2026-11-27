<?php

namespace App\Support;

use App\Models\Guest;
use App\Models\SeatingTable;
use Illuminate\Validation\ValidationException;

class SeatingValidator
{
    /**
     * Validate table ownership, capacity, and duplicate seat label for a guest assignment.
     *
     * @throws ValidationException
     */
    public static function validateAssignment(
        int $invitationId,
        ?int $seatingTableId,
        ?string $seatLabel,
        int $guestPax,
        ?int $ignoreGuestId = null
    ): void {
        if ($seatingTableId === null) {
            return;
        }

        $table = SeatingTable::query()
            ->where('id', $seatingTableId)
            ->where('invitation_id', $invitationId)
            ->first();

        if ($table === null) {
            throw ValidationException::withMessages([
                'seating_table_id' => 'Meja yang dipilih tidak dimiliki oleh undangan ini.',
            ]);
        }

        if (! $table->allow_over_capacity) {
            $occupied = (int) Guest::query()
                ->where('seating_table_id', $table->id)
                ->when($ignoreGuestId !== null, fn ($q) => $q->where('id', '!=', $ignoreGuestId))
                ->sum('max_pax');

            if (($occupied + max(1, $guestPax)) > $table->capacity) {
                throw ValidationException::withMessages([
                    'seating_table_id' => "Kapasitas meja {$table->name} ({$table->capacity} kursi) akan terlampaui (terisi {$occupied}, ditambah {$guestPax}).",
                ]);
            }
        }

        $normalizedSeat = trim((string) $seatLabel);
        if ($normalizedSeat !== '') {
            $duplicateExists = Guest::query()
                ->where('seating_table_id', $table->id)
                ->whereRaw('LOWER(seat_label) = ?', [mb_strtolower($normalizedSeat)])
                ->when($ignoreGuestId !== null, fn ($q) => $q->where('id', '!=', $ignoreGuestId))
                ->exists();

            if ($duplicateExists) {
                throw ValidationException::withMessages([
                    'seat_label' => "Label kursi '{$normalizedSeat}' sudah digunakan oleh tamu lain di meja {$table->name}.",
                ]);
            }
        }
    }
}
