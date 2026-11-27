@props([
    'invitation',
    'guest' => null,
    'existingRsvp' => null,
])

@php
    $isPerEvent = $invitation->rsvp_mode === 'per_acara' && $invitation->events->isNotEmpty();
    $maxAdults = $guest !== null ? max(1, (int) ($guest->max_adults ?? $guest->max_pax)) : 10;
    $maxChildren = $guest !== null ? max(0, (int) ($guest->max_children ?? 0)) : 10;
    $guestCode = $guest?->code ?? '';
    $selectedName = old('name', $existingRsvp?->name ?? $guest?->name ?? '');
    $existingByEvent = $existingRsvp?->relationLoaded('rsvpEvents')
        ? $existingRsvp->rsvpEvents->keyBy('event_id')
        : ($existingRsvp ? $existingRsvp->rsvpEvents()->get()->keyBy('event_id') : collect());
@endphp

@if($isPerEvent)
    <div class="shared-per-event-rsvp" data-rsvp-per-event>
        <p style="font-size: 0.88rem; opacity: 0.85; margin-bottom: 1rem;">
            {{ __('invitation.rsvp.quota_note', ['adults' => $maxAdults, 'children' => $maxChildren]) }}
        </p>

        @foreach($invitation->events as $event)
            @php
                $row = $existingByEvent->get($event->id);
                $evAttending = (bool) old("events.{$event->id}.attending", $row ? $row->attending : true);
                $evAdults = (int) old("events.{$event->id}.adults", $row ? $row->adults : 1);
                $evChildren = (int) old("events.{$event->id}.children", $row ? $row->children : 0);
            @endphp
            <div style="padding: 1rem; margin-bottom: 1rem; border-radius: 10px; border: 1px solid var(--inv-border, rgba(128, 128, 128, 0.25));">
                <p style="font-weight: 600; margin-bottom: 0.6rem;">{{ $event->title }}</p>
                <div style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; min-height: 44px; cursor: pointer;">
                        <input type="radio" name="events[{{ $event->id }}][attending]" value="1" @checked($evAttending)>
                        <span>{{ __('invitation.rsvp.status_attending') }}</span>
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; min-height: 44px; cursor: pointer;">
                        <input type="radio" name="events[{{ $event->id }}][attending]" value="0" @checked(!$evAttending)>
                        <span>{{ __('invitation.rsvp.status_not_attending') }}</span>
                    </label>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label for="rsvp-ev-{{ $event->id }}-adults" style="display: block; font-size: 0.85rem; margin-bottom: 0.25rem;">
                            {{ __('invitation.rsvp.adults_label') }} (maks. {{ $maxAdults }})
                        </label>
                        <input
                            id="rsvp-ev-{{ $event->id }}-adults"
                            type="number"
                            name="events[{{ $event->id }}][adults]"
                            min="0"
                            max="{{ $maxAdults }}"
                            value="{{ $evAdults }}"
                            style="width: 100%; min-height: 44px; padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--inv-border, rgba(128, 128, 128, 0.3)); background: transparent; color: inherit; font-size: 16px;"
                        >
                    </div>
                    <div>
                        <label for="rsvp-ev-{{ $event->id }}-children" style="display: block; font-size: 0.85rem; margin-bottom: 0.25rem;">
                            {{ __('invitation.rsvp.children_label') }} (maks. {{ $maxChildren }})
                        </label>
                        <input
                            id="rsvp-ev-{{ $event->id }}-children"
                            type="number"
                            name="events[{{ $event->id }}][children]"
                            min="0"
                            max="{{ $maxChildren }}"
                            value="{{ $evChildren }}"
                            style="width: 100%; min-height: 44px; padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--inv-border, rgba(128, 128, 128, 0.3)); background: transparent; color: inherit; font-size: 16px;"
                        >
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
