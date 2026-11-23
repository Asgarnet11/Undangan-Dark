<section id="section-wishes" class="mb-section mb-reveal">
    <p class="mb-kicker">{{ __('invitation.wishes.kicker') }}</p>
    <h2 class="mb-display mb-section-title">{{ __('invitation.wishes.heading') }}</h2>
    <p class="mb-body-soft mb-section-subtitle">{{ __('invitation.wishes.subtitle') }}</p>

    <div class="mb-divider-wrap" aria-hidden="true">
        <x-minimalist-botanical.svg name="botanical-divider" />
    </div>

    <div class="mb-card" style="margin-bottom: 2rem;">
        <form
            id="mb-wishes-form"
            method="POST"
            action="{{ route('invitation.wishes.store', ['slug' => $invitation->slug]) }}"
            novalidate
        >
            @csrf
            @if($guestCode !== '')
                <input type="hidden" name="guest_code" value="{{ $guestCode }}">
            @endif

            <div style="margin-bottom: 1rem; text-align: left;">
                <label for="mb-wish-name" class="mb-kicker" style="display: block; margin-bottom: 0.25rem;">
                    {{ __('invitation.wishes.name_label') }}
                </label>
                <input
                    id="mb-wish-name"
                    name="name"
                    type="text"
                    class="mb-input"
                    required
                    minlength="2"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.name_placeholder') }}"
                    value="{{ old('name', $guest?->name ?? '') }}"
                >
            </div>

            <div style="margin-bottom: 1rem; text-align: left;">
                <label for="mb-wish-origin" class="mb-kicker" style="display: block; margin-bottom: 0.25rem;">
                    {{ __('invitation.wishes.origin_label') }}
                </label>
                <input
                    id="mb-wish-origin"
                    name="origin"
                    type="text"
                    class="mb-input"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.origin_placeholder') }}"
                    value="{{ old('origin') }}"
                >
            </div>

            <div style="margin-bottom: 1.25rem; text-align: left;">
                <label for="mb-wish-message" class="mb-kicker" style="display: block; margin-bottom: 0.25rem;">
                    {{ __('invitation.wishes.message_label') }}
                </label>
                <textarea
                    id="mb-wish-message"
                    name="message"
                    class="mb-input"
                    rows="3"
                    required
                    minlength="3"
                    maxlength="500"
                    placeholder="{{ __('invitation.wishes.message_placeholder') }}"
                >{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="mb-btn" style="width: 100%;">
                {{ __('invitation.wishes.submit') }}
            </button>
        </form>
    </div>

    <div class="mb-wishes-list" id="mb-wishes-list" style="text-align: left;">
        @forelse($wishes as $wish)
            <article class="mb-card" style="margin-bottom: 0.75rem; padding: 1rem 1.25rem;">
                <header style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                    <strong style="font-size: 0.95rem; color: var(--mb-text-main);">{{ $wish->name }}</strong>
                    <time style="font-size: 0.75rem; color: var(--mb-text-soft);">{{ $wish->created_at->diffForHumans() }}</time>
                </header>
                @if($wish->origin)
                    <p class="mb-kicker" style="font-size: 0.75rem; color: var(--mb-secondary); margin-bottom: 0.35rem;">{{ $wish->origin }}</p>
                @endif
                <p class="mb-body-soft" style="font-size: 0.9rem;">{{ $wish->message }}</p>
            </article>
        @empty
            <p class="mb-body-soft" style="text-align: center;">{{ __('invitation.wishes.empty') }}</p>
        @endforelse
    </div>
</section>
