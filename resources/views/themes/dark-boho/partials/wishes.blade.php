<section id="section-wishes" class="db-section db-reveal-section">
    <p class="db-kicker">{{ __('invitation.wishes.kicker') }}</p>
    <h2 class="db-display db-section-title">{{ __('invitation.wishes.heading') }}</h2>
    <p class="db-body-soft db-section-subtitle">{{ __('invitation.wishes.subtitle') }}</p>

    <div class="db-card db-form-card">
        <form
            id="db-wishes-form"
            class="db-form"
            method="POST"
            action="{{ route('invitation.wishes.store', ['slug' => $invitation->slug]) }}"
            data-label-submit="{{ __('invitation.wishes.submit') }}"
            data-label-submitting="{{ __('invitation.wishes.submitting') }}"
            novalidate
        >
            @csrf
            @if($guestCode !== '')
                <input type="hidden" name="guest_code" value="{{ $guestCode }}">
            @endif

            <div class="db-field">
                <label for="db-wish-name" class="db-label">{{ __('invitation.wishes.name_label') }}</label>
                <input
                    id="db-wish-name"
                    name="name"
                    type="text"
                    class="db-input"
                    required
                    minlength="2"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.name_placeholder') }}"
                    value="{{ old('name', $guest?->name ?? '') }}"
                    aria-describedby="db-wish-name-error"
                >
                <p id="db-wish-name-error" class="db-field-error" role="alert" @if(!$errors->has('name')) hidden @endif>
                    {{ $errors->first('name') }}
                </p>
            </div>

            <div class="db-field">
                <label for="db-wish-origin" class="db-label">{{ __('invitation.wishes.origin_label') }}</label>
                <input
                    id="db-wish-origin"
                    name="origin"
                    type="text"
                    class="db-input"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.origin_placeholder') }}"
                    value="{{ old('origin', '') }}"
                    aria-describedby="db-wish-origin-error"
                >
                <p id="db-wish-origin-error" class="db-field-error" role="alert" @if(!$errors->has('origin')) hidden @endif>
                    {{ $errors->first('origin') }}
                </p>
            </div>

            <div class="db-field">
                <div class="db-label-row">
                    <label for="db-wish-message" class="db-label">{{ __('invitation.wishes.message_label') }}</label>
                    <span id="db-wish-char-count" class="db-char-count" aria-hidden="true">0 / 500</span>
                </div>
                <textarea
                    id="db-wish-message"
                    name="message"
                    rows="3"
                    required
                    minlength="3"
                    maxlength="500"
                    class="db-textarea"
                    placeholder="{{ __('invitation.wishes.message_placeholder') }}"
                    aria-describedby="db-wish-message-error"
                >{{ old('message', '') }}</textarea>
                <p id="db-wish-message-error" class="db-field-error" role="alert" @if(!$errors->has('message')) hidden @endif>
                    {{ $errors->first('message') }}
                </p>
            </div>

            <div
                id="db-wish-feedback"
                class="db-form-feedback {{ session('wish_status') ? 'is-success' : '' }}"
                role="status"
                aria-live="polite"
                @if(!session('wish_status')) hidden @endif
            >
                {{ session('wish_status') }}
            </div>

            <button type="submit" id="db-wish-submit" class="db-btn db-btn--block">
                {{ __('invitation.wishes.submit') }}
            </button>
        </form>
    </div>

    <div id="db-wishes-list" class="db-wishes-list" aria-live="polite">
        @forelse($wishes as $wish)
            <article class="db-card db-wish-card">
                <div class="db-wish-header">
                    <strong class="db-wish-name">{{ $wish->name }}</strong>
                    @if($wish->origin)
                        <span class="db-wish-origin">{{ $wish->origin }}</span>
                    @endif
                    <time class="db-wish-time" datetime="{{ $wish->created_at?->toIso8601String() }}">
                        {{ $wish->created_at?->diffForHumans() }}
                    </time>
                </div>
                <p class="db-wish-message">{{ $wish->message }}</p>
            </article>
        @empty
            <p id="db-wishes-empty" class="db-body-soft db-wishes-empty">{{ __('invitation.wishes.empty') }}</p>
        @endforelse
    </div>

    @if($wishes->hasMorePages())
        <div class="db-wishes-more">
            <button
                type="button"
                id="db-wishes-load-more"
                class="db-btn db-btn--ghost db-btn--sm"
                data-endpoint="{{ route('invitation.wishes.index', ['slug' => $invitation->slug]) }}"
                data-next-page="{{ $wishes->currentPage() + 1 }}"
                data-label-default="{{ __('invitation.wishes.load_more') }}"
                data-label-loading="{{ __('invitation.wishes.loading_more') }}"
            >
                {{ __('invitation.wishes.load_more') }}
            </button>
        </div>
    @endif
</section>
