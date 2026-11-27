<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LoveStoryChapter extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $chapter): void {
            $count = self::query()
                ->where('invitation_id', $chapter->invitation_id)
                ->count();

            if ($count >= 8) {
                throw ValidationException::withMessages([
                    'title' => 'Maksimal 8 bab Kisah Cinta per undangan telah tercapai.',
                ]);
            }
        });

        static::saving(function (self $chapter): void {
            $chapter->title = trim(strip_tags((string) $chapter->title));
            $chapter->time_label = $chapter->time_label !== null
                ? (trim(strip_tags((string) $chapter->time_label)) ?: null)
                : null;
            $chapter->body = trim(strip_tags((string) $chapter->body));
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'invitation_id',
        'title',
        'body',
        'photo_path',
        'time_label',
        'sort_order',
        'is_visible',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function photoUrl(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return Storage::disk('public')->url($this->photo_path);
    }
}
