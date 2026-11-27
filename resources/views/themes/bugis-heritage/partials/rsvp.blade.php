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

<section id="section-rsvp" class="bh-section bh-rsvp-section" aria-label="Konfirmasi Kehadiran">
    <h2 class="bh-display bh-section-title">{{ __('invitation.rsvp.heading') }}</h2>
    <p class="bh-body-serif bh-section-subtitle">{{ __('invitation.rsvp.subtitle') }}</p>

    {{-- Digital Guest Book / QR Code Kehadiran --}}
    @if($guest !== null)
        <div class="bh-card bh-qr-card" style="text-align: center; margin-bottom: 2rem; padding: 1.75rem 1.25rem;">
            <p class="bh-qr-badge" style="font-family: var(--bh-font-label); font-size: 0.82rem; font-weight: 500; letter-spacing: 0.18em; color: var(--bh-gold-soft); margin-bottom: 0.5rem; text-transform: uppercase;">BUKU TAMU DIGITAL</p>
            <h3 class="bh-display" style="font-size: 1.35rem; margin-bottom: 0.5rem; color: var(--bh-accent);">QR Code Kehadiran Tamu</h3>
            <p class="bh-body-serif" style="font-size: 0.88rem; max-width: 320px; margin: 0 auto 1.25rem; color: var(--bh-ink-soft);">
                Tunjukkan QR Code ini kepada panitia saat tiba di lokasi untuk konfirmasi kehadiran langsung.
            </p>
            <div style="display: inline-block; background: #ffffff; padding: 0.85rem; border-radius: var(--bh-radius); box-shadow: 0 4px 20px rgba(0,0,0,0.3); border: 1px solid var(--bh-line);">
                <img src="{{ $guest->qrCodeDataUri() }}" alt="QR Code Kehadiran {{ $guest->name }}" style="width: 160px; height: 160px; display: block;" loading="lazy">
            </div>
            <div style="margin-top: 1rem;">
                <span style="font-family: monospace; font-size: 1.15rem; font-weight: 700; letter-spacing: 0.15em; color: var(--bh-accent);">{{ $guest->code }}</span>
                <p style="font-size: 1.05rem; font-weight: 600; color: var(--bh-ink); margin-top: 0.25rem;">{{ $guest->name }}</p>
                @if($guest->checked_in_at)
                    <div style="display: inline-block; margin-top: 0.75rem; padding: 0.4rem 1rem; background: rgba(201, 164, 92, 0.15); color: var(--bh-gold-soft); border: 1px solid var(--bh-line); border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                        Telah Check-In ({{ $guest->checked_in_at->translatedFormat('d M Y, H:i') }} {{ $invitation->timezoneAbbreviation() }})
                    </div>
                @endif
                <x-shared.seating-info :guest="$guest" />
            </div>
        </div>
    @endif

    {{-- Form RSVP --}}
    <div class="bh-card bh-card--gold-border bh-form-card">
        <form
            id="bh-rsvp-form"
            method="POST"
            action="{{ route('invitation.rsvp.store', ['slug' => $invitation->slug]) }}"
            novalidate
        >
            @csrf
            @if($guestCode !== '')
                <input type="hidden" name="guest_code" value="{{ $guestCode }}">
            @endif

            <div style="margin-bottom: 1.25rem; text-align: left;">
                <label for="bh-rsvp-name" class="bh-kicker" style="display: block; margin-bottom: 0.35rem;">
                    {{ __('invitation.rsvp.name_label') }}
                </label>
                <input
                    id="bh-rsvp-name"
                    name="name"
                    type="text"
                    class="bh-input"
                    required
                    minlength="2"
                    maxlength="120"
                    placeholder="{{ __('invitation.rsvp.name_placeholder') }}"
                    value="{{ $selectedName }}"
                >
            </div>

            <fieldset style="border: none; margin-bottom: 1.25rem; text-align: left;">
                <legend class="bh-kicker" style="margin-bottom: 0.5rem;">
                    {{ __('invitation.rsvp.status_label') }}
                </legend>
                <div style="display: flex; gap: 0.75rem;">
                    <label class="bh-radio-card" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.75rem; border: 1px solid var(--bh-line); border-radius: var(--bh-radius); cursor: pointer; background: var(--bh-bg); font-size: 0.85rem;">
                        <input type="radio" name="attending" value="1" @checked($selectedAttending === '1')>
                        <span>{{ __('invitation.rsvp.status_attending') }}</span>
                    </label>
                    <label class="bh-radio-card" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.75rem; border: 1px solid var(--bh-line); border-radius: var(--bh-radius); cursor: pointer; background: var(--bh-bg); font-size: 0.85rem;">
                        <input type="radio" name="attending" value="0" @checked($selectedAttending === '0')>
                        <span>{{ __('invitation.rsvp.status_not_attending') }}</span>
                    </label>
                </div>
            </fieldset>

            <div style="margin-bottom: 1.25rem; text-align: left;">
                <label for="bh-rsvp-pax" class="bh-kicker" style="display: block; margin-bottom: 0.35rem;">
                    {{ __('invitation.rsvp.pax_label') }}
                </label>
                <select id="bh-rsvp-pax" name="pax" class="bh-input">
                    @for($i = 1; $i <= $maxPax; $i++)
                        <option value="{{ $i }}" @selected($selectedPax === $i)>{{ $i }} Orang</option>
                    @endfor
                </select>
            </div>

            <div style="margin-bottom: 1.5rem; text-align: left;">
                <label for="bh-rsvp-message" class="bh-kicker" style="display: block; margin-bottom: 0.35rem;">
                    {{ __('invitation.rsvp.message_label') }}
                </label>
                <textarea
                    id="bh-rsvp-message"
                    name="message"
                    class="bh-input"
                    rows="3"
                    placeholder="{{ __('invitation.rsvp.message_placeholder') }}"
                >{{ $selectedMessage }}</textarea>
            </div>

            <button type="submit" class="bh-btn bh-btn--primary" style="width: 100%;">
                {{ $existingRsvp ? __('invitation.rsvp.update_submit') : __('invitation.rsvp.submit') }}
            </button>
        </form>
    </div>
</section>
