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

