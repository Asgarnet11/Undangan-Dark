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

<section id="section-rsvp" class="sm-section sm-section-rsvp">
    <div class="sm-section-header">
        <p class="sm-section-subtitle">{{ __('invitation.rsvp.kicker') }}</p>
        <h2 class="sm-section-title">{{ __('invitation.rsvp.heading') }}</h2>
        <p class="sm-section-desc">{{ __('invitation.rsvp.subtitle') }}</p>
    </div>

    @if($guest !== null)
        <div class="sm-card" style="text-align: center; margin-bottom: 2rem; padding: 1.5rem 1rem;">
            <p class="sm-section-subtitle" style="font-size: 0.75rem; letter-spacing: 0.15em; margin-bottom: 0.5rem;">BUKU TAMU DIGITAL</p>
            <h3 class="sm-title-display" style="font-size: 1.25rem; margin-bottom: 0.5rem; color: #3b2f2a;">QR Code Kehadiran Tamu</h3>
            <p class="sm-section-desc" style="font-size: 0.85rem; max-width: 320px; margin: 0 auto 1.25rem;">
                Tunjukkan QR Code ini kepada panitia saat tiba di lokasi untuk konfirmasi kehadiran langsung.
            </p>
            <div style="display: inline-block; background: #ffffff; padding: 0.75rem; border-radius: 0.75rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.06);">
                <img src="{{ $guest->qrCodeDataUri() }}" alt="QR Code Kehadiran {{ $guest->name }}" style="width: 160px; height: 160px; display: block;" loading="lazy">
            </div>
            <div style="margin-top: 1rem;">
                <span style="font-family: monospace; font-size: 1.1rem; font-weight: 700; letter-spacing: 0.15em; color: var(--sm-primary, #f6b93b);">{{ $guest->code }}</span>
                <p style="font-size: 0.95rem; font-weight: 600; color: #3b2f2a; margin-top: 0.25rem;">{{ $guest->name }}</p>
                @if($guest->checked_in_at)
                    <div style="display: inline-block; margin-top: 0.75rem; padding: 0.35rem 0.85rem; background: rgba(34, 197, 94, 0.15); color: #15803d; border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                        Telah Check-In ({{ $guest->checked_in_at->translatedFormat('d M Y, H:i') }} {{ $invitation->timezoneAbbreviation() }})
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="sm-card sm-form-card">
        <form
            id="sm-rsvp-form"
            class="sm-form"
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

            <div class="sm-field">
                <label for="sm-rsvp-name" class="sm-label">{{ __('invitation.rsvp.name_label') }}</label>
                <input
                    id="sm-rsvp-name"
                    name="name"
                    type="text"
                    class="sm-input"
                    required
                    minlength="2"
                    maxlength="120"
                    autocomplete="name"
                    placeholder="{{ __('invitation.rsvp.name_placeholder') }}"
                    value="{{ $selectedName }}"
                    aria-describedby="sm-rsvp-name-error"
                >
                <p id="sm-rsvp-name-error" class="sm-field-error" role="alert" @if(!$errors->has('name')) hidden @endif>
                    {{ $errors->first('name') }}
                </p>
            </div>

            <fieldset class="sm-field sm-fieldset">
                <legend class="sm-label">{{ __('invitation.rsvp.status_label') }}</legend>
                <div class="sm-radio-group">
                    <label class="sm-radio-pill" for="sm-rsvp-yes">
                        <input
                            id="sm-rsvp-yes"
                            name="attending"
                            type="radio"
                            value="1"
                            @checked((string) $selectedAttending === '1')
                        >
                        <span>{{ __('invitation.rsvp.attending_yes') }}</span>
                    </label>
                    <label class="sm-radio-pill" for="sm-rsvp-no">
                        <input
                            id="sm-rsvp-no"
                            name="attending"
                            type="radio"
                            value="0"
                            @checked((string) $selectedAttending === '0')
                        >
                        <span>{{ __('invitation.rsvp.attending_no') }}</span>
                    </label>
                </div>
            </fieldset>

            <div class="sm-field" id="sm-rsvp-pax-wrap" @if((string) $selectedAttending === '0') hidden @endif>
                <label for="sm-rsvp-pax" class="sm-label">{{ __('invitation.rsvp.pax_label') }}</label>
                <input
                    id="sm-rsvp-pax"
                    name="pax"
                    type="number"
                    class="sm-input"
                    min="1"
                    max="{{ $maxPax }}"
                    inputmode="numeric"
                    value="{{ $selectedPax }}"
                    aria-describedby="sm-rsvp-pax-hint sm-rsvp-pax-error"
                    @disabled((string) $selectedAttending === '0')
                >
                @if($guest !== null)
                    <p id="sm-rsvp-pax-hint" class="sm-field-hint">
                        {{ __('invitation.rsvp.pax_max_hint', ['max' => $maxPax]) }}
                    </p>
                @else
                    <span id="sm-rsvp-pax-hint" hidden></span>
                @endif
                <p id="sm-rsvp-pax-error" class="sm-field-error" role="alert" @if(!$errors->has('pax')) hidden @endif>
                    {{ $errors->first('pax') }}
                </p>
            </div>

            <div class="sm-field">
                <label for="sm-rsvp-message" class="sm-label">{{ __('invitation.rsvp.message_label') }}</label>
                <textarea
                    id="sm-rsvp-message"
                    name="message"
                    rows="3"
                    maxlength="500"
                    class="sm-textarea"
                    placeholder="{{ __('invitation.rsvp.message_placeholder') }}"
                    aria-describedby="sm-rsvp-message-error"
                >{{ $selectedMessage }}</textarea>
                <p id="sm-rsvp-message-error" class="sm-field-error" role="alert" @if(!$errors->has('message')) hidden @endif>
                    {{ $errors->first('message') }}
                </p>
            </div>

            <div
                id="sm-rsvp-feedback"
                class="sm-form-feedback {{ session('rsvp_status') ? 'is-success' : '' }}"
                role="status"
                aria-live="polite"
                @if(!session('rsvp_status')) hidden @endif
            >
                {{ session('rsvp_status') }}
            </div>

            <button type="submit" id="sm-rsvp-submit" class="sm-btn sm-btn-primary sm-btn--block">
                {{ $existingRsvp !== null ? __('invitation.rsvp.update_submit') : __('invitation.rsvp.submit') }}
            </button>
        </form>
    </div>
</section>

