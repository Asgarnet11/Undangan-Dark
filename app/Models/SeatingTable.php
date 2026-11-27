<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatingTable extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'invitation_id',
        'name',
        'capacity',
        'notes',
        'sort_order',
        'allow_over_capacity',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'sort_order' => 'integer',
            'allow_over_capacity' => 'boolean',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'seating_table_id');
    }

    public function occupiedSeatsCount(): int
    {
        return (int) $this->guests()->sum('max_pax');
    }
}
