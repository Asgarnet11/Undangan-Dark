<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'invitation_id',
        'title',
        'starts_at',
        'ends_at',
        'venue_name',
        'address',
        'maps_query',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function startsAtInTimezone(string $timezone): CarbonInterface
    {
        return $this->starts_at->copy()->timezone($timezone)->locale('id');
    }

    public function resolvedEndsAt(string $timezone): CarbonInterface
    {
        $end = $this->ends_at
            ? $this->ends_at->copy()->timezone($timezone)
            : $this->starts_at->copy()->timezone($timezone)->addHours(2);

        if ($end->lessThanOrEqualTo($this->starts_at->copy()->timezone($timezone))) {
            $end = $this->starts_at->copy()->timezone($timezone)->addHours(2);
        }

        return $end->locale('id');
    }

    public function resolvedMapsQuery(): string
    {
        if (! empty($this->maps_query)) {
            return trim($this->maps_query);
        }

        return trim($this->venue_name.', '.$this->address);
    }

    public function googleMapsUrl(): string
    {
        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($this->resolvedMapsQuery());
    }

    public function googleMapsEmbedUrl(): string
    {
        return 'https://www.google.com/maps?q='.rawurlencode($this->resolvedMapsQuery()).'&output=embed';
    }

    public function googleCalendarUrl(Invitation $invitation): string
    {
        $timezone = $invitation->resolvedTimezone();
        $start = $this->startsAtInTimezone($timezone);
        $end = $this->resolvedEndsAt($timezone);

        $title = $this->title.' - '.$invitation->bride_nickname.' & '.$invitation->groom_nickname;
        $dates = $start->format('Ymd\THis').'/'.$end->format('Ymd\THis');
        $location = trim($this->venue_name.', '.$this->address);
        $details = $invitation->title.' ('.$this->venue_name.')';

        return 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            .'&text='.rawurlencode($title)
            .'&dates='.$dates
            .'&ctz='.rawurlencode($timezone)
            .'&details='.rawurlencode($details)
            .'&location='.rawurlencode($location);
    }
}
