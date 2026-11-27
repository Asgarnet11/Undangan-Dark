@php
    $selectedName = old('name', $existingRsvp?->name ?? $guest?->name ?? '');
    $selectedAttending = old('attending', $existingRsvp !== null ? ($existingRsvp->attending ? '1' : '0') : '1');
    $maxPax = $guest !== null ? max(1, $guest->max_pax) : 10;
    $selectedPax = (int) old('pax', $existingRsvp?->pax ?? min(2, $maxPax));
    if ($selectedPax < 1) {
        $selectedPax = 1;
    }
    $selectedMessage = old('message', $existingRsvp?->message ?? '');
@endphp

<section id="section-rsvp" class="db-section db-reveal-section">
    <p class="db-kicker">{{ __('invitation.rsvp.kicker') }}</p>
    <h2 class="db-display db-section-title">{{ __('invitation.rsvp.heading') }}</h2>
    <p class="db-body-soft db-section-subtitle">{{ __('invitation.rsvp.subtitle') }}</p>

    @if($guest !== null)
        <div class="db-card" style="text-align: center; margin-bottom: 2rem; padding: 1.5rem 1rem;">
            <p class="db-kicker" style="font-size: 0.75rem; letter-spacing: 0.15em; margin-bottom: 0.5rem;">BUKU TAMU DIGITAL</p>
            <h3 class="db-display" style="font-size: 1.25rem; margin-bottom: 0.5rem; color: #fff;">QR Code Kehadiran Tamu</h3>
            <p class="db-body-soft" style="font-size: 0.85rem; max-width: 320px; margin: 0 auto 1.25rem;">
                Tunjukkan QR Code ini kepada panitia saat tiba di lokasi untuk konfirmasi kehadiran langsung.
            </p>
            <div style="display: inline-block; background: #ffffff; padding: 0.75rem; border-radius: 0.75rem; box-shadow: 0 4px 20px rgba(0,0,0,0.4);">
                <img src="{{ $guest->qrCodeDataUri() }}" alt="QR Code Kehadiran {{ $guest->name }}" style="width: 160px; height: 160px; display: block;" loading="lazy">
            </div>
            <div style="margin-top: 1rem;">
                <span style="font-family: monospace; font-size: 1.1rem; font-weight: 700; letter-spacing: 0.15em; color: var(--db-primary, #c0714f);">{{ $guest->code }}</span>
                <p style="font-size: 0.95rem; font-weight: 600; color: #fff; margin-top: 0.25rem;">{{ $guest->name }}</p>
                @if($guest->checked_in_at)
                    <div style="display: inline-block; margin-top: 0.75rem; padding: 0.35rem 0.85rem; background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                        Telah Check-In ({{ $guest->checked_in_at->translatedFormat('d M Y, H:i') }} {{ $invitation->timezoneAbbreviation() }})
                    </div>
                @endif
                <x-shared.seating-info :guest="$guest" />
            </div>
        </div>
    @endif

    <div class="db-card db-form-card">
        <form
            id="db-rsvp-form"
            class="db-form"
            method="POST"
            action="{{ route('invitation.rsvp.store', ['slug' => $invitation->slug]) }}"
            data-label-submit="{{ __('invitation.rsvp.submit') }}"
            data-label-update="{{ __('invitation.rsvp.update_submit') }}"
            data-label-submitting="{{ __('invitation.rsvp.submitting') }}"
            novalidate
        >
            @csrf
            @if($guestCode !== '')
                <input type="hidden" name="guest_code" value="{{ $guestCode }}">
            @endif

            <div class="db-field">
                <label for="db-rsvp-name" class="db-label">{{ __('invitation.rsvp.name_label') }}</label>
                <input
                    id="db-rsvp-name"
                    name="name"
                    type="text"
                    class="db-input"
                    required
                    minlength="2"
                    maxlength="120"
                    autocomplete="name"
                    placeholder="{{ __('invitation.rsvp.name_placeholder') }}"
                    value="{{ $selectedName }}"
                    aria-describedby="db-rsvp-name-error"
                >
                <p id="db-rsvp-name-error" class="db-field-error" role="alert" @if(!$errors->has('name')) hidden @endif>
                    {{ $errors->first('name') }}
                </p>
            </div>

            <fieldset class="db-field db-fieldset">
                <legend class="db-label">{{ __('invitation.rsvp.status_label') }}</legend>
                <div class="db-radio-group">
                    <label class="db-radio-pill" for="db-rsvp-yes">
                        <input
                            id="db-rsvp-yes"
                            name="attending"
                            type="radio"
                            value="1"
                            @checked((string) $selectedAttending === '1')
                        >
                        <span>{{ __('invitation.rsvp.attending_yes') }}</span>
                    </label>
                    <label class="db-radio-pill" for="db-rsvp-no">
                        <input
                            id="db-rsvp-no"
                            name="attending"
                            type="radio"
                            value="0"
                            @checked((string) $selectedAttending === '0')
                        >
                        <span>{{ __('invitation.rsvp.attending_no') }}</span>
                    </label>
                </div>
            </fieldset>

            <div class="db-field" id="db-rsvp-pax-wrap" @if((string) $selectedAttending === '0') hidden @endif>
                <label for="db-rsvp-pax" class="db-label">{{ __('invitation.rsvp.pax_label') }}</label>
                <input
                    id="db-rsvp-pax"
                    name="pax"
                    type="number"
                    class="db-input"
                    min="1"
                    max="{{ $maxPax }}"
                    inputmode="numeric"
                    value="{{ $selectedPax }}"
                    aria-describedby="db-rsvp-pax-hint db-rsvp-pax-error"
                    @disabled((string) $selectedAttending === '0')
                >
                @if($guest !== null)
                    <p id="db-rsvp-pax-hint" class="db-field-hint">
                        {{ __('invitation.rsvp.pax_max_hint', ['max' => $maxPax]) }}
                    </p>
                @else
                    <span id="db-rsvp-pax-hint" hidden></span>
                @endif
                <p id="db-rsvp-pax-error" class="db-field-error" role="alert" @if(!$errors->has('pax')) hidden @endif>
                    {{ $errors->first('pax') }}
                </p>
            </div>

            <div class="db-field">
                <label for="db-rsvp-message" class="db-label">{{ __('invitation.rsvp.message_label') }}</label>
                <textarea
                    id="db-rsvp-message"
                    name="message"
                    rows="3"
                    maxlength="500"
                    class="db-textarea"
                    placeholder="{{ __('invitation.rsvp.message_placeholder') }}"
                    aria-describedby="db-rsvp-message-error"
                >{{ $selectedMessage }}</textarea>
                <p id="db-rsvp-message-error" class="db-field-error" role="alert" @if(!$errors->has('message')) hidden @endif>
                    {{ $errors->first('message') }}
                </p>
            </div>

            <div
                id="db-rsvp-feedback"
                class="db-form-feedback {{ session('rsvp_status') ? 'is-success' : '' }}"
                role="status"
                aria-live="polite"
                @if(!session('rsvp_status')) hidden @endif
            >
                {{ session('rsvp_status') }}
            </div>

            <button type="submit" id="db-rsvp-submit" class="db-btn db-btn--block">
                {{ $existingRsvp !== null ? __('invitation.rsvp.update_submit') : __('invitation.rsvp.submit') }}
            </button>
        </form>
    </div>
</section>
