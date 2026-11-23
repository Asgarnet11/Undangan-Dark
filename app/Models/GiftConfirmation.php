<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftConfirmation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'invitation_id',
        'guest_id',
        'sender_name',
        'target_account',
        'amount',
        'proof_file',
        'notes',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
