<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LontaraText extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'latin_text',
        'lontara_text',
        'status',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * Hanya teks Lontara yang telah diverifikasi yang boleh ditampilkan di publik.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'diverifikasi');
    }

    public function isVerified(): bool
    {
        return $this->status === 'diverifikasi';
    }

    protected static function booted(): void
    {
        static::updating(function (self $model): void {
            if ($model->getOriginal('status') === 'diverifikasi' && $model->isDirty(['latin_text', 'lontara_text'])) {
                $model->status = 'draf';
            }
        });
    }
}
