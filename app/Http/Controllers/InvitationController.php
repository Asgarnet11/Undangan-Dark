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
            $expiredView = view()->exists("themes.{$invitation->theme}.expired")
                ? "themes.{$invitation->theme}.expired"
                : 'themes.dark-boho.expired';

            return view($expiredView, [
                'invitation' => $invitation,
            ]);
        }

        $guest = $this->resolveGuestByCode($invitation, (string) $request->query('to', ''));
        if ($guest !== null) {
            if ($guest->opened_at === null) {
                $guest->update(['opened_at' => now()]);
            }
            $guest->load('rsvp');

            if (! empty($guest->allowed_event_ids) && is_array($guest->allowed_event_ids)) {
                $invitation->setRelation(
                    'events',
                    $invitation->events->whereIn('id', $guest->allowed_event_ids)->values()
                );
            }
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

    public function preview(Request $request, Invitation $invitation): View|RedirectResponse
    {
        $user = $request->user();

        // If accessed by an unauthenticated visitor or crawler:
        if ($user === null && ! $request->hasValidSignature()) {
            if ($invitation->is_published) {
                return redirect()->route('invitation.show', ['slug' => $invitation->slug]);
            }

            return redirect()->guest('/admin/login');
        }

        // If authenticated but unauthorized:
        if ($user !== null && $invitation->user_id !== $user->id && ! $user->email) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pratinjau draft undangan ini.');
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
        if ($guest !== null && ! empty($guest->allowed_event_ids) && is_array($guest->allowed_event_ids)) {
            $invitation->setRelation(
                'events',
                $invitation->events->whereIn('id', $guest->allowed_event_ids)->values()
            );
        }
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
        $invitation = $this->resolvePublishedInvitation($slug, ['events']);
        $guest = $this->resolveGuestByCode($invitation, (string) $request->input('guest_code', ''));
        $maxPax = $guest !== null ? max(1, $guest->max_pax) : 10;
        $maxAdults = $guest !== null ? max(1, (int) ($guest->max_adults ?? $maxPax)) : 10;
        $maxChildren = $guest !== null ? max(0, (int) ($guest->max_children ?? 0)) : 10;
        $isPerEvent = $invitation->rsvp_mode === 'per_acara' && $request->has('events');

        $rules = [
            'guest_code' => ['nullable', 'string', 'max:64'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'attending' => [$isPerEvent ? 'nullable' : 'required', 'boolean'],
            'pax' => ['nullable', 'integer', 'min:0', 'max:'.$maxPax],
            'message' => ['nullable', 'string', 'max:500'],
        ];

        if ($isPerEvent) {
            $rules['events'] = ['required', 'array', 'min:1'];
            $rules['events.*.attending'] = ['required', 'boolean'];
            $rules['events.*.adults'] = ['nullable', 'integer', 'min:0', 'max:'.$maxAdults];
            $rules['events.*.children'] = ['nullable', 'integer', 'min:0', 'max:'.$maxChildren];
        }

        $validated = $request->validate($rules, [], [
            'name' => __('invitation.rsvp.name_label'),
            'attending' => __('invitation.rsvp.status_label'),
            'pax' => __('invitation.rsvp.pax_label'),
            'message' => __('invitation.rsvp.message_label'),
        ]);

        $validEventIds = $invitation->events->pluck('id')->all();

        if ($isPerEvent) {
            $submittedEvents = $validated['events'];
            foreach (array_keys($submittedEvents) as $eventId) {
                if (! in_array((int) $eventId, $validEventIds, true)) {
                    abort(422, 'Acara tidak ditemukan pada undangan ini.');
                }
            }

            $anyAttending = false;
            $totalPax = 0;
            $eventPayloads = [];

            foreach ($submittedEvents as $eventId => $row) {
                $evAttending = (bool) $row['attending'];
                $adults = $evAttending ? max(1, min($maxAdults, (int) ($row['adults'] ?? 1))) : 0;
                $children = $evAttending ? max(0, min($maxChildren, (int) ($row['children'] ?? 0))) : 0;

                if ($evAttending) {
                    $anyAttending = true;
                    $totalPax += ($adults + $children);
                }

                $eventPayloads[(int) $eventId] = [
                    'attending' => $evAttending,
                    'adults' => $adults,
                    'children' => $children,
                ];
            }

            $attending = $anyAttending;
            $pax = $totalPax;
        } else {
            $attending = (bool) $validated['attending'];
            $pax = $attending ? max(1, min($maxPax, (int) ($validated['pax'] ?? 1))) : 0;
            $eventPayloads = [];
        }

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

        foreach ($eventPayloads as $eventId => $evData) {
            $rsvp->rsvpEvents()->updateOrCreate(
                ['event_id' => $eventId],
                $evData
            );
        }

        $thankYouMessage = $attending
            ? (trim((string) $invitation->rsvp_thanks_attending) ?: __('invitation.rsvp.saved_attending'))
            : (trim((string) $invitation->rsvp_thanks_declined) ?: __('invitation.rsvp.saved_declined'));

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $thankYouMessage,
                'rsvp' => [
                    'id' => $rsvp->id,
                    'name' => $rsvp->name,
                    'attending' => $rsvp->attending,
                    'pax' => $rsvp->pax,
                    'message' => $rsvp->message,
                    'events' => $eventPayloads,
                ],
            ]);
        }

        return back()
            ->with('rsvp_status', $thankYouMessage)
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
        $tz = $wish->invitation?->resolvedTimezone() ?? 'Asia/Jakarta';

        return [
            'id' => $wish->id,
            'name' => $wish->name,
            'origin' => $wish->origin,
            'message' => $wish->message,
            'created_at_human' => $wish->created_at?->locale('id')->diffForHumans() ?? 'Baru saja',
            'created_at_full' => $wish->created_at?->copy()->timezone($tz)->locale('id')->translatedFormat('d M Y, H:i') ?? '',
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

    public function checkInPage(Request $request, string $slug): View
    {
        $invitation = $this->resolvePublishedInvitation($slug);
        $guests = $invitation->guests()->with('rsvp')->orderByRaw('checked_in_at IS NULL, checked_in_at DESC')->orderBy('name')->get();

        $totalGuests = $guests->count();
        $checkedInCount = $guests->whereNotNull('checked_in_at')->count();
        $totalPaxPresent = $guests->whereNotNull('checked_in_at')->sum(fn (Guest $g) => $g->rsvp?->attending ? max(1, (int) $g->rsvp->pax) : 1);
        $pendingCount = max(0, $totalGuests - $checkedInCount);

        return view('check-in.reception', [
            'invitation' => $invitation,
            'guests' => $guests,
            'stats' => [
                'total' => $totalGuests,
                'checked_in' => $checkedInCount,
                'pax_present' => $totalPaxPresent,
                'pending' => $pendingCount,
            ],
        ]);
    }

    public function processCheckIn(Request $request, string $slug): JsonResponse
    {
        $invitation = $this->resolvePublishedInvitation($slug);
        $code = strtoupper(trim((string) $request->input('code', '')));
        $guestId = $request->input('guest_id');

        $guest = null;
        if ($guestId !== null) {
            $guest = $invitation->guests()->with('rsvp')->find($guestId);
        } elseif ($code !== '') {
            $guest = $invitation->guests()->with('rsvp')->where('code', $code)->first();
        }

        if ($guest === null) {
            return response()->json([
                'ok' => false,
                'message' => 'Tamu dengan kode atau data tersebut tidak ditemukan.',
            ], 404);
        }

        $wasCheckedIn = $guest->checked_in_at !== null;
        if (! $wasCheckedIn) {
            $guest->update(['checked_in_at' => now()]);
        }

        $pax = $guest->rsvp?->attending ? max(1, (int) $guest->rsvp->pax) : max(1, (int) $guest->max_pax);

        return response()->json([
            'ok' => true,
            'already_checked_in' => $wasCheckedIn,
            'message' => $wasCheckedIn
                ? "Tamu {$guest->name} sudah check-in sebelumnya pada {$guest->checked_in_at?->format('H:i d/m')}."
                : "Check-in berhasil! Selamat datang, {$guest->name}.",
            'guest' => [
                'id' => $guest->id,
                'name' => $guest->name,
                'code' => $guest->code,
                'group' => $guest->group ?: 'Umum',
                'pax' => $pax,
                'checked_in_at' => $guest->checked_in_at?->format('H:i:s d/m/Y'),
            ],
        ]);
    }

    public function undoCheckIn(Request $request, string $slug, Guest $guest): JsonResponse
    {
        $invitation = $this->resolvePublishedInvitation($slug);
        if ($guest->invitation_id !== $invitation->id) {
            abort(404);
        }

        $guest->update(['checked_in_at' => null]);

        return response()->json([
            'ok' => true,
            'message' => "Status kehadiran {$guest->name} berhasil dibatalkan.",
        ]);
    }

    public function storeGiftConfirmation(Request $request, string $slug): JsonResponse
    {
        $invitation = $this->resolvePublishedInvitation($slug);
        $guest = $this->resolveGuestByCode($invitation, (string) $request->input('guest_code', ''));

        $validated = $request->validate([
            'sender_name' => ['required', 'string', 'min:2', 'max:120'],
            'target_account' => ['nullable', 'string', 'max:120'],
            'amount' => ['nullable', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:500'],
            'proof_file' => ['nullable', 'image', 'max:5120'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('gift-proofs', 'public');
        }

        $confirmation = $invitation->giftConfirmations()->create([
            'guest_id' => $guest?->id,
            'sender_name' => $this->cleanText($validated['sender_name'], false),
            'target_account' => $this->cleanText($validated['target_account'] ?? '', false) ?: null,
            'amount' => $this->cleanText($validated['amount'] ?? '', false) ?: null,
            'notes' => $this->cleanText($validated['notes'] ?? '', true) ?: null,
            'proof_file' => $proofPath,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Konfirmasi tanda kasih berhasil dikirim. Terima kasih banyak atas perhatian dan doa restu Anda.',
            'confirmation' => [
                'id' => $confirmation->id,
                'sender_name' => $confirmation->sender_name,
            ],
        ]);
    }
}
