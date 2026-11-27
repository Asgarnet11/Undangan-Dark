<?php

namespace App\Models;

use App\Support\ThemeRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Invitation extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'slug',
        'theme',
        'theme_config',
        'feature_flags',
        'seat_info_visibility',
        'rsvp_mode',
        'rsvp_thanks_attending',
        'rsvp_thanks_declined',
        'timezone',
        'title',
        'monogram_initial',
        'bride_name',
        'bride_nickname',
        'bride_father',
        'bride_mother',
        'bride_order_label',
        'bride_origin',
        'bride_instagram',
        'bride_photo',
        'groom_name',
        'groom_nickname',
        'groom_father',
        'groom_mother',
        'groom_order_label',
        'groom_origin',
        'groom_instagram',
        'groom_photo',
        'opening_text',
        'verse_arabic',
        'verse_translation',
        'verse_source',
        'cover_photo',
        'music_file',
        'preset_music',
        'music_title',
        'video_url',
        'gallery_title',
        'gallery_quote',
        'livestream_url',
        'livestream_title',
        'livestream_note',
        'closing_text',
        'dress_note',
        'wa_template',
        'thank_you_gift_note',
        'gift_note',
        'gift_whatsapp_number',
        'gift_whatsapp_template',
        'love_story_title',
        'love_story_intro',
        'registry_intro',
        'seating_unassigned_note',
        'is_published',
        'no_index',
        'active_days_after_event',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'theme_config' => 'array',
            'feature_flags' => 'array',
            'is_published' => 'boolean',
            'no_index' => 'boolean',
            'active_days_after_event' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class)->orderBy('sort_order')->orderBy('starts_at');
    }

    public function galleryPhotos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class)->orderBy('sort_order');
    }

    public function giftAccounts(): HasMany
    {
        return $this->hasMany(GiftAccount::class)->orderBy('sort_order');
    }

    public function giftAddresses(): HasMany
    {
        return $this->hasMany(GiftAddress::class)->orderBy('sort_order');
    }

    public function coInviters(): HasMany
    {
        return $this->hasMany(CoInviter::class)->orderBy('sort_order');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function loveStoryChapters(): HasMany
    {
        return $this->hasMany(LoveStoryChapter::class)->orderBy('sort_order');
    }

    public function seatingTables(): HasMany
    {
        return $this->hasMany(SeatingTable::class)->orderBy('sort_order');
    }

    public function qrisAccounts(): HasMany
    {
        return $this->hasMany(QrisAccount::class)->orderBy('sort_order');
    }

    public function giftRegistryLinks(): HasMany
    {
        return $this->hasMany(GiftRegistryLink::class)->orderBy('sort_order');
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }

    public function visibleWishes(): HasMany
    {
        return $this->hasMany(Wish::class)->where('is_visible', true)->latest();
    }

    public function giftConfirmations(): HasMany
    {
        return $this->hasMany(GiftConfirmation::class)->latest();
    }

    public function lontaraTexts(): HasMany
    {
        return $this->hasMany(LontaraText::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->useDisk('public')->singleFile();
        $this->addMediaCollection('bride_photo')->useDisk('public')->singleFile();
        $this->addMediaCollection('groom_photo')->useDisk('public')->singleFile();
        $this->addMediaCollection('music')->useDisk('public')->singleFile();
        $this->addMediaCollection('gallery')->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->performOnCollections('cover', 'bride_photo', 'groom_photo', 'gallery')
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->performOnCollections('cover', 'bride_photo', 'groom_photo', 'gallery')
            ->width(640)
            ->format('webp')
            ->nonQueued();
    }

    /**
     * @return array<string, mixed>
     */
    public function resolvedThemeConfig(): array
    {
        return ThemeRegistry::resolveConfig($this->theme, $this->theme_config);
    }

    public function resolvedTimezone(): string
    {
        $allowed = ['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura'];

        return in_array($this->timezone, $allowed, true) ? $this->timezone : 'Asia/Jakarta';
    }

    public function timezoneAbbreviation(): string
    {
        return match ($this->resolvedTimezone()) {
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => 'WIB',
        };
    }

    public function monogramText(): string
    {
        $first = mb_strtoupper(mb_substr(trim($this->bride_nickname), 0, 1));
        $second = mb_strtoupper(mb_substr(trim($this->groom_nickname), 0, 1));

        return $first.'&'.$second;
    }

    public function youtubeVideoId(): ?string
    {
        $url = trim((string) $this->video_url);
        if ($url === '') {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @return list<array{thumb: string, full: string, caption: string, width: int, height: int}>
     */
    public function resolvedGalleryItems(int $limit = 12): array
    {
        $items = [];

        foreach ($this->getMedia('gallery') as $media) {
            $items[] = [
                'thumb' => $media->getUrl('thumb') ?: $media->getUrl(),
                'full' => $media->getUrl('webp') ?: $media->getUrl(),
                'caption' => (string) $media->getCustomProperty('caption', ''),
                'width' => 720,
                'height' => 960,
            ];
        }

        foreach ($this->galleryPhotos as $photo) {
            $url = $photo->url();
            $items[] = [
                'thumb' => $url,
                'full' => $url,
                'caption' => (string) ($photo->caption ?? ''),
                'width' => $photo->width ?: 720,
                'height' => $photo->height ?: 960,
            ];
        }

        return array_slice($items, 0, $limit);
    }

    public function resolveMediaOrPathUrl(string $collection, ?string $columnValue = null, string $conversion = 'webp'): ?string
    {
        $mediaUrl = $this->getFirstMediaUrl($collection, $conversion) ?: $this->getFirstMediaUrl($collection);
        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        if (empty($columnValue)) {
            return null;
        }

        if (str_starts_with($columnValue, 'http://') || str_starts_with($columnValue, 'https://') || str_starts_with($columnValue, '/')) {
            return $columnValue;
        }

        return Storage::url($columnValue);
    }

    public function lastEventDate(): ?Carbon
    {
        $lastEvent = $this->events()->latest('starts_at')->first();

        return $lastEvent?->starts_at;
    }

    public function expiresAt(): ?Carbon
    {
        $lastDate = $this->lastEventDate();
        if ($lastDate === null) {
            return null;
        }

        $days = max(1, (int) ($this->active_days_after_event ?: 30));

        return $lastDate->copy()->addDays($days)->endOfDay();
    }

    public function isExpired(): bool
    {
        $expiry = $this->expiresAt();

        return $expiry !== null && now()->greaterThan($expiry);
    }

    public function defaultWaTemplate(): string
    {
        return "Kepada Yth. {nama},\n\n"
            ."Tanpa mengurangi rasa hormat, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk menghadiri hari bahagia pernikahan kami.\n\n"
            ."Tautan undangan resmi:\n{link}\n\n"
            ."Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila berkenan hadir dan memberikan doa restu.\n\n"
            ."Terima kasih.\n"
            .'{mempelai}';
    }

    public function generateWaMessage(Guest $guest): string
    {
        $template = trim((string) $this->wa_template);
        if ($template === '') {
            $template = $this->defaultWaTemplate();
        }

        $url = route('invitation.show', ['slug' => $this->slug, 'to' => $guest->code]);
        $mempelai = trim("{$this->bride_nickname} & {$this->groom_nickname}");
        $firstEvent = $this->events()->first();
        $tanggal = $firstEvent?->starts_at ? $firstEvent->starts_at->translatedFormat('l, d F Y') : '';
        $lokasi = (string) ($firstEvent?->location_name ?? '');

        return strtr($template, [
            '{nama}' => $guest->name,
            '{link}' => $url,
            '{url}' => $url,
            '{mempelai}' => $mempelai,
            '{tanggal}' => $tanggal,
            '{lokasi}' => $lokasi,
        ]);
    }

    public function giftWhatsappUrl(?string $guestName = null): ?string
    {
        $rawNumber = preg_replace('/\D+/', '', (string) $this->gift_whatsapp_number);
        if ($rawNumber === null || strlen($rawNumber) < 8) {
            return null;
        }

        if (str_starts_with($rawNumber, '0')) {
            $rawNumber = '62'.substr($rawNumber, 1);
        }

        $template = trim((string) $this->gift_whatsapp_template);
        if ($template === '') {
            $template = 'Halo, saya {nama} ingin mengkonfirmasi pengiriman tanda kasih untuk pernikahan {mempelai}.';
        }

        $name = trim((string) $guestName) !== '' ? trim((string) $guestName) : 'Tamu Undangan';
        $mempelai = trim("{$this->bride_nickname} & {$this->groom_nickname}");

        $message = strtr($template, [
            '{nama}' => $name,
            '{mempelai}' => $mempelai,
        ]);

        return 'https://wa.me/'.$rawNumber.'?text='.rawurlencode($message);
    }

    public function effectiveMusicUrl(): ?string
    {
        $customUrl = $this->resolveMediaOrPathUrl('music', $this->music_file);
        if ($customUrl !== null && $customUrl !== '') {
            return $customUrl;
        }

        if (! empty($this->preset_music)) {
            $preset = config("invitation_music.presets.{$this->preset_music}");
            if ($preset && ! empty($preset['file'])) {
                return asset($preset['file']);
            }
        }

        return null;
    }
}
