<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Wish;
use App\Support\ThemeRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $invitation = $this->resolvePublishedInvitation($slug, [
            'events',
            'galleryPhotos',
            'giftAccounts',
            'giftAddresses',
            'coInviters',
            'media',
        ]);

        if ($invitation->isExpired() && ! auth()->check()) {
            return view('themes.dark-boho.expired', [
                'invitation' => $invitation,
            ]);
        }

        $guest = $this->resolveGuestByCode($invitation, (string) $request->query('to', ''));
        if ($guest !== null) {
            if ($guest->opened_at === null) {
                $guest->update(['opened_at' => now()]);
            }
            $guest->load('rsvp');
        }

        $wishes = $invitation->visibleWishes()->paginate(5);
        $themeConfig = $invitation->resolvedThemeConfig();
        $viewName = ThemeRegistry::viewName($invitation->theme);

        if (! view()->exists($viewName)) {
            abort(404, 'Tema undangan tidak ditemukan.');
        }

        return view($viewName, [
            'invitation' => $invitation,
            'guest' => $guest,
            'guestCode' => $guest?->code ?? '',
            'existingRsvp' => $guest?->rsvp,
            'wishes' => $wishes,
            'themeConfig' => $themeConfig,
            'isPreview' => false,
        ]);
    }

    public function preview(Request $request, Invitation $invitation): View
    {
        $user = $request->user();
        if ($user === null || ($invitation->user_id !== $user->id && ! $user->email)) {
            abort(403);
        }

        $invitation->load([
            'events',
            'galleryPhotos',
            'giftAccounts',
            'giftAddresses',
            'coInviters',
            'media',
        ]);

        $guest = $invitation->guests()->with('rsvp')->first();
        $wishes = $invitation->visibleWishes()->paginate(5);
        $themeConfig = $invitation->resolvedThemeConfig();

        $presetOverride = trim((string) $request->query('preset', ''));
        if ($presetOverride !== '' && array_key_exists($presetOverride, ThemeRegistry::presets($invitation->theme))) {
            $themeConfig['preset'] = $presetOverride;
        }

        $viewName = ThemeRegistry::viewName($invitation->theme);
        if (! view()->exists($viewName)) {
            abort(404, 'Tema undangan tidak ditemukan.');
        }

        return view($viewName, [
            'invitation' => $invitation,
            'guest' => $guest,
            'guestCode' => $guest?->code ?? '',
            'existingRsvp' => $guest?->rsvp,
            'wishes' => $wishes,
            'themeConfig' => $themeConfig,
            'isPreview' => true,
        ]);
    }

    public function storeRsvp(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $invitation = $this->resolvePublishedInvitation($slug);
        $guest = $this->resolveGuestByCode($invitation, (string) $request->input('guest_code', ''));
        $maxPax = $guest !== null ? max(1, $guest->max_pax) : 10;

        $validated = $request->validate([
            'guest_code' => ['nullable', 'string', 'max:64'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'attending' => ['required', 'boolean'],
            'pax' => ['nullable', 'integer', 'min:0', 'max:'.$maxPax],
            'message' => ['nullable', 'string', 'max:500'],
        ], [], [
            'name' => __('invitation.rsvp.name_label'),
            'attending' => __('invitation.rsvp.status_label'),
            'pax' => __('invitation.rsvp.pax_label'),
            'message' => __('invitation.rsvp.message_label'),
        ]);

        $attending = (bool) $validated['attending'];
        $pax = $attending ? max(1, min($maxPax, (int) ($validated['pax'] ?? 1))) : 0;

        $payload = [
            'name' => $this->cleanText($validated['name'], false),
            'attending' => $attending,
            'pax' => $pax,
            'message' => $this->cleanText($validated['message'] ?? '', true) ?: null,
        ];

        $rsvp = $guest !== null
            ? $invitation->rsvps()->updateOrCreate(
                ['guest_id' => $guest->id],
                $payload
            )
            : $invitation->rsvps()->create($payload);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => __('invitation.rsvp.saved'),
                'rsvp' => [
                    'id' => $rsvp->id,
                    'name' => $rsvp->name,
                    'attending' => $rsvp->attending,
                    'pax' => $rsvp->pax,
                    'message' => $rsvp->message,
                ],
            ]);
        }

        return back()
            ->with('rsvp_status', __('invitation.rsvp.saved'))
            ->withFragment('section-rsvp');
    }

    public function wishes(Request $request, string $slug): JsonResponse
    {
        $invitation = $this->resolvePublishedInvitation($slug);
        $themeConfig = $invitation->resolvedThemeConfig();

        if (! data_get($themeConfig, 'sections.wishes', true)) {
            abort(404);
        }

        $wishes = $invitation->visibleWishes()->paginate(5);

        return response()->json([
            'items' => collect($wishes->items())
                ->map(fn (Wish $wish): array => $this->serializeWish($wish))
                ->values(),
            'current_page' => $wishes->currentPage(),
            'last_page' => $wishes->lastPage(),
            'has_more' => $wishes->hasMorePages(),
            'next_page' => $wishes->hasMorePages() ? $wishes->currentPage() + 1 : null,
        ]);
    }

    public function storeWish(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $invitation = $this->resolvePublishedInvitation($slug);
        $themeConfig = $invitation->resolvedThemeConfig();

        if (! data_get($themeConfig, 'sections.wishes', true)) {
            abort(404);
        }

        $validated = $request->validate([
            'guest_code' => ['nullable', 'string', 'max:64'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'origin' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:3', 'max:500'],
        ], [], [
            'name' => __('invitation.wishes.name_label'),
            'origin' => __('invitation.wishes.origin_label'),
            'message' => __('invitation.wishes.message_label'),
        ]);

        $guest = $this->resolveGuestByCode($invitation, (string) $request->input('guest_code', ''));

        /** @var Wish $wish */
        $wish = $invitation->wishes()->create([
            'guest_id' => $guest?->id,
            'name' => $this->cleanText($validated['name'], false),
            'origin' => $this->cleanText($validated['origin'] ?? '', false) ?: null,
            'message' => $this->cleanText($validated['message'], true),
            'is_visible' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => __('invitation.wishes.saved'),
                'wish' => $this->serializeWish($wish),
            ], 201);
        }

        return back()
            ->with('wish_status', __('invitation.wishes.saved'))
            ->withFragment('section-wishes');
    }

    public function gifts(string $slug): JsonResponse
    {
        $invitation = $this->resolvePublishedInvitation($slug, ['giftAccounts', 'giftAddresses']);
        $themeConfig = $invitation->resolvedThemeConfig();

        if (! data_get($themeConfig, 'sections.gift', true)) {
            abort(404);
        }

        return response()->json([
            'note' => $invitation->thank_you_gift_note,
            'accounts' => $invitation->giftAccounts->map(fn ($account): array => [
                'id' => $account->id,
                'bank_name' => $account->bank_name,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
            ])->values(),
            'addresses' => $invitation->giftAddresses->map(fn ($address): array => [
                'id' => $address->id,
                'label' => $address->label,
                'address' => $address->address,
            ])->values(),
        ]);
    }

    public function calendarIcs(string $slug, Event $event): Response
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        if ($event->invitation_id !== $invitation->id) {
            abort(404);
        }

        $timezone = $invitation->resolvedTimezone();
        $start = $event->startsAtInTimezone($timezone);
        $end = $event->resolvedEndsAt($timezone);
        $utcNow = now('UTC')->format('Ymd\THis\Z');

        $summary = $this->escapeIcsText($event->title.' - '.$invitation->bride_nickname.' & '.$invitation->groom_nickname);
        $location = $this->escapeIcsText(trim($event->venue_name.', '.$event->address));
        $description = $this->escapeIcsText($invitation->title.' - '.$event->googleMapsUrl());
        $uid = 'event-'.$event->id.'@'.parse_url(config('app.url'), PHP_URL_HOST);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Undangan Digital//Dark Boho//ID',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:'.$uid,
            'DTSTAMP:'.$utcNow,
            'DTSTART;TZID='.$timezone.':'.$start->format('Ymd\THis'),
            'DTEND;TZID='.$timezone.':'.$end->format('Ymd\THis'),
            'SUMMARY:'.$summary,
            'DESCRIPTION:'.$description,
            'LOCATION:'.$location,
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        $filename = str($invitation->slug.'-'.$event->title)->slug()->append('.ics')->toString();

        return response(implode("\r\n", $lines)."\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * @param  list<string>  $with
     */
    private function resolvePublishedInvitation(string $slug, array $with = []): Invitation
    {
        return Invitation::query()
            ->when($with !== [], fn ($query) => $query->with($with))
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
    }

    private function resolveGuestByCode(Invitation $invitation, string $guestCode): ?Guest
    {
        $code = trim($guestCode);
        if ($code === '') {
            return null;
        }

        return Guest::query()
            ->where('invitation_id', $invitation->id)
            ->where('code', $code)
            ->first();
    }

    private function cleanText(?string $value, bool $allowMultiline = true): string
    {
        $stripped = strip_tags((string) $value);
        $normalized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $stripped) ?? '';
        $sanitized = preg_replace('/(?i)javascript\s*:/', '', $normalized) ?? '';

        if (! $allowMultiline) {
            $sanitized = preg_replace('/[\r\n\t]+/', ' ', $sanitized) ?? '';
        }

        return trim($sanitized);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeWish(Wish $wish): array
    {
        return [
            'id' => $wish->id,
            'name' => $wish->name,
            'origin' => $wish->origin,
            'message' => $wish->message,
            'created_at_human' => $wish->created_at?->diffForHumans() ?? 'Baru saja',
        ];
    }

    private function escapeIcsText(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\r\n", "\n", "\r"],
            ['\\\\', '\;', '\,', '\n', '\n', '\n'],
            $value
        );
    }
}
