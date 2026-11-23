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

<section id="section-rsvp" class="mb-section mb-reveal">
    <p class="mb-kicker">{{ __('invitation.rsvp.kicker') }}</p>
    <h2 class="mb-display mb-section-title">{{ __('invitation.rsvp.heading') }}</h2>
    <p class="mb-body-soft mb-section-subtitle">{{ __('invitation.rsvp.subtitle') }}</p>

    @if($guest !== null)
        <div class="mb-card" style="text-align: center; margin-bottom: 2rem; padding: 1.5rem 1rem;">
            <p class="mb-kicker" style="font-size: 0.75rem; letter-spacing: 0.15em; margin-bottom: 0.5rem;">BUKU TAMU DIGITAL</p>
            <h3 class="mb-display" style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--mb-text-main);">QR Code Kehadiran Tamu</h3>
            <p class="mb-body-soft" style="font-size: 0.85rem; max-width: 320px; margin: 0 auto 1.25rem;">
                Tunjukkan QR Code ini kepada panitia saat tiba di lokasi untuk konfirmasi kehadiran langsung.
            </p>
            <div style="display: inline-block; background: #ffffff; padding: 0.75rem; border-radius: 0.75rem; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid var(--mb-border);">
                <img src="{{ $guest->qrCodeDataUri() }}" alt="QR Code Kehadiran {{ $guest->name }}" style="width: 160px; height: 160px; display: block;" loading="lazy">
            </div>
            <div style="margin-top: 1rem;">
                <span style="font-family: monospace; font-size: 1.1rem; font-weight: 700; letter-spacing: 0.15em; color: var(--mb-primary);">{{ $guest->code }}</span>
                <p style="font-size: 0.95rem; font-weight: 600; color: var(--mb-text-main); margin-top: 0.25rem;">{{ $guest->name }}</p>
                @if($guest->checked_in_at)
                    <div style="display: inline-block; margin-top: 0.75rem; padding: 0.35rem 0.85rem; background: rgba(74, 93, 78, 0.12); color: var(--mb-primary); border: 1px solid var(--mb-border); border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                        Telah Check-In ({{ $guest->checked_in_at->translatedFormat('d M Y, H:i') }} {{ $invitation->timezoneAbbreviation() }})
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="mb-card mb-form-card">
        <form
            id="mb-rsvp-form"
            method="POST"
            action="{{ route('invitation.rsvp.store', ['slug' => $invitation->slug]) }}"
            novalidate
        >
            @csrf
            @if($guestCode !== '')
                <input type="hidden" name="guest_code" value="{{ $guestCode }}">
            @endif

            <div style="margin-bottom: 1rem; text-align: left;">
                <label for="mb-rsvp-name" class="mb-kicker" style="display: block; margin-bottom: 0.35rem;">
                    {{ __('invitation.rsvp.name_label') }}
                </label>
                <input
                    id="mb-rsvp-name"
                    name="name"
                    type="text"
                    class="mb-input"
                    required
                    minlength="2"
                    maxlength="120"
                    placeholder="{{ __('invitation.rsvp.name_placeholder') }}"
                    value="{{ $selectedName }}"
                >
            </div>

            <fieldset style="border: none; margin-bottom: 1rem; text-align: left;">
                <legend class="mb-kicker" style="margin-bottom: 0.5rem;">
                    {{ __('invitation.rsvp.status_label') }}
                </legend>
                <div style="display: flex; gap: 0.75rem;">
                    <label style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.65rem; border: 1px solid var(--mb-border); border-radius: 0.5rem; cursor: pointer; background: #fff; font-size: 0.85rem;">
                        <input type="radio" name="attending" value="1" @checked($selectedAttending === '1')>
                        <span>{{ __('invitation.rsvp.status_attending') }}</span>
                    </label>
                    <label style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.65rem; border: 1px solid var(--mb-border); border-radius: 0.5rem; cursor: pointer; background: #fff; font-size: 0.85rem;">
                        <input type="radio" name="attending" value="0" @checked($selectedAttending === '0')>
                        <span>{{ __('invitation.rsvp.status_not_attending') }}</span>
                    </label>
                </div>
            </fieldset>

            <div style="margin-bottom: 1rem; text-align: left;">
                <label for="mb-rsvp-pax" class="mb-kicker" style="display: block; margin-bottom: 0.35rem;">
                    {{ __('invitation.rsvp.pax_label') }}
                </label>
                <select id="mb-rsvp-pax" name="pax" class="mb-input">
                    @for($i = 1; $i <= $maxPax; $i++)
                        <option value="{{ $i }}" @selected($selectedPax === $i)>{{ $i }} Orang</option>
                    @endfor
                </select>
            </div>

            <div style="margin-bottom: 1.5rem; text-align: left;">
                <label for="mb-rsvp-message" class="mb-kicker" style="display: block; margin-bottom: 0.35rem;">
                    {{ __('invitation.rsvp.message_label') }}
                </label>
                <textarea
                    id="mb-rsvp-message"
                    name="message"
                    class="mb-input"
                    rows="3"
                    placeholder="{{ __('invitation.rsvp.message_placeholder') }}"
                >{{ $selectedMessage }}</textarea>
            </div>

            <button type="submit" class="mb-btn" style="width: 100%;">
                {{ $existingRsvp ? __('invitation.rsvp.update_submit') : __('invitation.rsvp.submit') }}
            </button>
        </form>
    </div>
</section>
