<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GalleryPhoto extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'invitation_id',
        'path',
        'caption',
        'width',
        'height',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function url(): string
    {
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://') || str_starts_with($this->path, '/')) {
            return $this->path;
        }

        return Storage::url($this->path);
    }
}
