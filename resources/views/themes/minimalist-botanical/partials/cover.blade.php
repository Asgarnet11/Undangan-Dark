@php
    $coverPhoto = $invitation->resolveMediaOrPathUrl('cover', $invitation->cover_photo)
        ?: ($invitation->resolveMediaOrPathUrl('bride', $invitation->bride_photo));
    $guestName = $guest?->name ?: __('invitation.cover.default_guest');
    $primaryEvent = $invitation->events->first();
    $eventDate = $primaryEvent ? $primaryEvent->startsAtInTimezone($invitation->resolvedTimezone())->translatedFormat('l, d F Y') : null;
@endphp

<section id="mb-cover" class="mb-cover" aria-label="Sampul Undangan">
    @if($coverPhoto)
        <img src="{{ $coverPhoto }}" alt="Foto Sampul {{ $invitation->title }}" class="mb-cover-bg" loading="eager">
    @endif
    <div class="mb-cover-overlay" aria-hidden="true"></div>

    <div class="mb-cover-arch" aria-hidden="true">
        <x-minimalist-botanical.svg name="arch-botanical" />
    </div>

    <div class="mb-cover-corner-tl" aria-hidden="true">
        <x-minimalist-botanical.svg name="botanical-branch-top" />
    </div>
    <div class="mb-cover-corner-br" aria-hidden="true">
        <x-minimalist-botanical.svg name="botanical-branch-bottom" />
    </div>

    <header class="mb-cover-header">
        <p class="mb-kicker">{{ __('invitation.cover.subtitle') }}</p>
        <div class="mb-cover-wreath" aria-hidden="true">
            <x-minimalist-botanical.svg name="olive-wreath" />
        </div>
        <h1 class="mb-display mb-cover-title">
            {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
        </h1>
        @if($eventDate)
            <p class="mb-kicker" style="color: var(--mb-text-soft); font-size: 0.8rem; margin-top: 0.25rem;">
                {{ $eventDate }}
            </p>
        @endif
    </header>

    <div class="mb-cover-footer">
        <div class="mb-cover-guest-card">
            <p class="mb-kicker" style="margin-bottom: 0.25rem; font-size: 0.7rem;">{{ __('invitation.cover.guest_prefix') }}</p>
            <h2 class="mb-cover-guest-name">{{ $guestName }}</h2>
            @if($guest && $guest->group)
                <p class="mb-body-soft" style="font-size: 0.8rem; margin-top: 0.25rem;">{{ $guest->group }}</p>
            @endif
        </div>

        <button type="button" id="mb-open-btn" class="mb-btn" aria-label="{{ __('invitation.cover.open_button') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="18" height="18" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <span>{{ __('invitation.cover.open_button') }}</span>
        </button>
    </div>
</section>
