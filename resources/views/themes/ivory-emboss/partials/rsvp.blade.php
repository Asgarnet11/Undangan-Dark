@php
    $guestCode = $guest?->code ?? '';
    $selectedName = old('name', $existingRsvp?->name ?? $guest?->name ?? '');
    $selectedMessage = old('message', $existingRsvp?->message ?? '');
@endphp

<section id="section-rsvp" class="iv-section iv-rsvp" aria-label="Konfirmasi Kehadiran">
    <div class="iv-relief-edge iv-relief-edge--left" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-left-c.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-left-c.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-left-c@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>
    <div class="iv-relief-edge iv-relief-edge--right" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/edge-right-b.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/edge-right-b.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/edge-right-b@2x.webp') }} 2x"
            alt=""
            width="360"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="iv-safe-zone">
        <p class="iv-kicker">{{ __('invitation.rsvp.kicker') }}</p>
        <h2 class="iv-display iv-section-title">{{ __('invitation.rsvp.heading') }}</h2>
        <p class="iv-section-subtitle">{{ __('invitation.rsvp.subtitle') }}</p>

        @if($guest !== null)
            <div class="iv-card iv-qr-card" style="margin-bottom: 1.5rem;">
                <p class="iv-kicker">Kartu Tamu Digital</p>
                <div class="iv-qr-frame">
                    <img src="{{ $guest->qrCodeDataUri() }}" alt="QR Code Kehadiran {{ $guest->name }}" width="160" height="160" loading="lazy">
                </div>
                <p class="iv-qr-code">{{ $guest->code }}</p>
                <p class="iv-qr-name">{{ $guest->name }}</p>
                @if($guest->checked_in_at)
                    <p class="iv-kicker" style="margin-top: 0.5rem;">
                        Telah Check-In ({{ $guest->checked_in_at->translatedFormat('d M Y, H:i') }} {{ $invitation->timezoneAbbreviation() }})
                    </p>
                @endif
                <x-shared.seating-info :guest="$guest" />
            </div>
        @endif

        <div class="iv-card iv-form-card">
            <form
                id="iv-rsvp-form"
                method="POST"
                action="{{ route('invitation.rsvp.store', ['slug' => $invitation->slug]) }}"
                novalidate
            >
                @csrf
                @if($guestCode !== '')
                    <input type="hidden" name="guest_code" value="{{ $guestCode }}">
                @endif

                <div class="iv-field">
                    <label for="iv-rsvp-name" class="iv-kicker iv-field-label">
                        {{ __('invitation.rsvp.name_label') }}
                    </label>
                    <input
                        id="iv-rsvp-name"
                        name="name"
                        type="text"
                        class="iv-input"
                        required
                        minlength="2"
                        maxlength="120"
                        placeholder="{{ __('invitation.rsvp.name_placeholder') }}"
                        value="{{ $selectedName }}"
                        @if($guestCode !== '') readonly @endif
                    >
                </div>

                <x-shared.rsvp-form :invitation="$invitation" :guest="$guest" :existing-rsvp="$existingRsvp ?? null" />

                <div class="iv-field">
                    <label for="iv-rsvp-message" class="iv-kicker iv-field-label">
                        {{ __('invitation.rsvp.message_label') }}
                    </label>
                    <textarea
                        id="iv-rsvp-message"
                        name="message"
                        class="iv-input"
                        rows="3"
                        maxlength="500"
                        placeholder="{{ __('invitation.rsvp.message_placeholder') }}"
                    >{{ $selectedMessage }}</textarea>
                </div>

                <button type="submit" class="iv-btn" style="width: 100%;">
                    {{ __('invitation.rsvp.submit') }}
                </button>
                <p id="iv-rsvp-feedback" class="iv-form-feedback" aria-live="polite" hidden></p>
            </form>
        </div>
    </div>
</section>
