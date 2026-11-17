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
