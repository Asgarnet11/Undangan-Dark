<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RsvpEvent extends Model
{
    protected $table = 'rsvp_event';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'rsvp_id',
        'event_id',
        'attending',
        'adults',
        'children',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attending' => 'boolean',
            'adults' => 'integer',
            'children' => 'integer',
        ];
    }

    public function rsvp(): BelongsTo
    {
        return $this->belongsTo(Rsvp::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
