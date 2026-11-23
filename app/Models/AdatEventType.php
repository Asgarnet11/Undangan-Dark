<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdatEventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label_display',
        'alt_spellings',
        'description_id',
        'regional_note',
        'status',
        'verified_by',
        'verified_at',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'alt_spellings' => 'array',
            'verified_at' => 'datetime',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Hanya entri berstatus diverifikasi dan dipublikasikan yang boleh tampil di publik.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'diverifikasi')->where('is_published', true);
    }

    public function isVerified(): bool
    {
        return $this->status === 'diverifikasi' && $this->is_published;
    }

    protected static function booted(): void
    {
        static::updating(function (self $model): void {
            // Jika ada perubahan konten pada entri yang sudah diverifikasi, status kembali ke draf untuk diperiksa ulang.
            $contentAttributes = ['key', 'label_display', 'alt_spellings', 'description_id', 'regional_note'];
            if ($model->getOriginal('status') === 'diverifikasi' && $model->isDirty($contentAttributes)) {
                $model->status = 'draf';
                $model->is_published = false;
            }
        });
    }
}
