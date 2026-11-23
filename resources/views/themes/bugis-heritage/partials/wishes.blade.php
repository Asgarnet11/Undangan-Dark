<section id="section-wishes" class="bh-section bh-wishes-section" aria-label="Ucapan dan Doa Restu">
    <div class="bh-divider" aria-hidden="true">
        <x-bugis-heritage.svg name="divider-diamond" />
    </div>

    <p class="bh-kicker">{{ __('invitation.wishes.kicker') }}</p>
    <h2 class="bh-display bh-section-title">{{ __('invitation.wishes.heading') }}</h2>
    <p class="bh-body-serif bh-section-subtitle">{{ __('invitation.wishes.subtitle') }}</p>

    {{-- Form Kirim Ucapan --}}
    <div class="bh-card bh-card--gold-border bh-form-card" style="margin-bottom: 2rem;">
        <form
            id="bh-wishes-form"
            method="POST"
            action="{{ route('invitation.wishes.store', ['slug' => $invitation->slug]) }}"
            novalidate
        >
            @csrf
            @if($guestCode !== '')
                <input type="hidden" name="guest_code" value="{{ $guestCode }}">
            @endif

            <div style="margin-bottom: 1rem; text-align: left;">
                <label for="bh-wish-name" class="bh-kicker" style="display: block; margin-bottom: 0.25rem;">
                    {{ __('invitation.wishes.name_label') }}
                </label>
                <input
                    id="bh-wish-name"
                    name="name"
                    type="text"
                    class="bh-input"
                    required
                    minlength="2"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.name_placeholder') }}"
                    value="{{ old('name', $guest?->name ?? '') }}"
                >
            </div>

            <div style="margin-bottom: 1rem; text-align: left;">
                <label for="bh-wish-origin" class="bh-kicker" style="display: block; margin-bottom: 0.25rem;">
                    {{ __('invitation.wishes.origin_label') }}
                </label>
                <input
                    id="bh-wish-origin"
                    name="origin"
                    type="text"
                    class="bh-input"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.origin_placeholder') }}"
                    value="{{ old('origin') }}"
                >
            </div>

            <div style="margin-bottom: 1.25rem; text-align: left;">
                <label for="bh-wish-message" class="bh-kicker" style="display: block; margin-bottom: 0.25rem;">
                    {{ __('invitation.wishes.message_label') }}
                </label>
                <textarea
                    id="bh-wish-message"
                    name="message"
                    class="bh-input"
                    rows="3"
                    required
                    minlength="3"
                    maxlength="500"
                    placeholder="{{ __('invitation.wishes.message_placeholder') }}"
                >{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="bh-btn bh-btn--primary" style="width: 100%;">
                {{ __('invitation.wishes.submit') }}
            </button>
        </form>
    </div>

    {{-- Daftar Ucapan --}}
    @if(isset($wishes) && $wishes->isNotEmpty())
        <div class="bh-wishes-list" style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($wishes as $wish)
                <article class="bh-card bh-card--gold-border bh-wish-card" style="text-align: left; padding: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                        <h4 class="bh-display" style="font-size: 1.1rem; color: var(--bh-accent);">
                            {{ $wish->name }}
                        </h4>
                        <time class="bh-kicker" style="font-size: 0.68rem; opacity: 0.7;">
                            {{ $wish->created_at->diffForHumans() }}
                        </time>
                    </div>
                    @if($wish->origin)
                        <p class="bh-kicker" style="font-size: 0.72rem; color: var(--bh-ink-faint); margin-bottom: 0.5rem; text-transform: none; letter-spacing: normal;">
                            {{ $wish->origin }}
                        </p>
                    @endif
                    <p class="bh-body-serif" style="font-size: 0.88rem; line-height: 1.6; color: var(--bh-ink-soft);">
                        {{ $wish->message }}
                    </p>
                </article>
            @endforeach
        </div>
    @endif
</section>
