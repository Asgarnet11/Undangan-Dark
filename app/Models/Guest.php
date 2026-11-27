<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'invitation_id',
        'name',
        'code',
        'group',
        'seating_table_id',
        'seat_label',
        'max_pax',
        'max_adults',
        'max_children',
        'phone',
        'opened_at',
        'checked_in_at',
        'allowed_event_ids',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_pax' => 'integer',
            'max_adults' => 'integer',
            'max_children' => 'integer',
            'opened_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'allowed_event_ids' => 'array',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function seatingTable(): BelongsTo
    {
        return $this->belongsTo(SeatingTable::class, 'seating_table_id');
    }

    public function rsvp(): HasOne
    {
        return $this->hasOne(Rsvp::class);
    }

    public function giftConfirmations(): HasMany
    {
        return $this->hasMany(GiftConfirmation::class);
    }

    public function qrCodeDataUri(): string
    {
        return (new QRCode)->render($this->code);
    }

    public function isCheckedIn(): bool
    {
        return $this->checked_in_at !== null;
    }

    /**
     * @return array{visible: bool, assigned: bool, table_name: ?string, seat_label: ?string, unassigned_note: string}|null
     */
    public function visibleSeatingInfo(): ?array
    {
        $invitation = $this->invitation;
        if ($invitation === null) {
            return null;
        }

        $flags = $invitation->feature_flags ?? [];
        if (array_key_exists('seating', $flags) && ! $flags['seating']) {
            return null;
        }

        $visibility = $invitation->seat_info_visibility ?: 'after_checkin';
        if ($visibility === 'hidden') {
            return null;
        }

        if ($visibility === 'after_checkin' && ! $this->isCheckedIn()) {
            return null;
        }

        $tableName = $this->seatingTable?->name;
        $seatLabel = trim((string) $this->seat_label) !== '' ? trim((string) $this->seat_label) : null;
        $assigned = $tableName !== null;

        return [
            'visible' => true,
            'assigned' => $assigned,
            'table_name' => $tableName,
            'seat_label' => $seatLabel,
            'unassigned_note' => trim((string) $invitation->seating_unassigned_note) ?: 'Meja dan kursi akan diinformasikan di lokasi.',
        ];
    }
}
