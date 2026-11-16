<section id="section-wishes" class="sm-section sm-section-wishes">
    <div class="sm-section-header">
        <div class="sm-decor-heart-center" aria-hidden="true">
            <x-sunny-meadow.svg name="heart-line" class="sm-svg-heart-sm" />
        </div>
        <p class="sm-section-subtitle">{{ __('invitation.wishes.heading') }}</p>
        <h2 class="sm-section-title">{{ __('invitation.wishes.heading') }}</h2>
        <p class="sm-section-desc">{{ __('invitation.wishes.subtitle') }}</p>
    </div>

    <div class="sm-card sm-form-card">
        <form
            id="sm-wishes-form"
            class="sm-form"
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

            <div class="sm-field">
                <label for="sm-wish-name" class="sm-label">{{ __('invitation.wishes.name_label') }}</label>
                <input
                    id="sm-wish-name"
                    name="name"
                    type="text"
                    class="sm-input"
                    required
                    minlength="2"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.name_placeholder') }}"
                    value="{{ old('name', $guest?->name ?? '') }}"
                    aria-describedby="sm-wish-name-error"
                >
                <p id="sm-wish-name-error" class="sm-field-error" role="alert" @if(!$errors->has('name')) hidden @endif>
                    {{ $errors->first('name') }}
                </p>
            </div>

            <div class="sm-field">
                <label for="sm-wish-origin" class="sm-label">{{ __('invitation.wishes.origin_label') }}</label>
                <input
                    id="sm-wish-origin"
                    name="origin"
                    type="text"
                    class="sm-input"
                    maxlength="100"
                    placeholder="{{ __('invitation.wishes.origin_placeholder') }}"
                    value="{{ old('origin', '') }}"
                    aria-describedby="sm-wish-origin-error"
                >
                <p id="sm-wish-origin-error" class="sm-field-error" role="alert" @if(!$errors->has('origin')) hidden @endif>
                    {{ $errors->first('origin') }}
                </p>
            </div>

            <div class="sm-field">
                <div class="sm-label-row">
                    <label for="sm-wish-message" class="sm-label">{{ __('invitation.wishes.message_label') }}</label>
                    <span id="sm-wish-char-count" class="sm-char-count" aria-hidden="true">0 / 500</span>
                </div>
                <textarea
                    id="sm-wish-message"
                    name="message"
                    rows="3"
                    required
                    minlength="3"
                    maxlength="500"
                    class="sm-textarea"
                    placeholder="{{ __('invitation.wishes.message_placeholder') }}"
                    aria-describedby="sm-wish-message-error"
                >{{ old('message', '') }}</textarea>
                <p id="sm-wish-message-error" class="sm-field-error" role="alert" @if(!$errors->has('message')) hidden @endif>
                    {{ $errors->first('message') }}
                </p>
            </div>

            <div
                id="sm-wish-feedback"
                class="sm-form-feedback {{ session('wish_status') ? 'is-success' : '' }}"
                role="status"
                aria-live="polite"
                @if(!session('wish_status')) hidden @endif
            >
                {{ session('wish_status') }}
            </div>

            <button type="submit" id="sm-wish-submit" class="sm-btn sm-btn-primary sm-btn--block">
                {{ __('invitation.wishes.submit') }}
            </button>
        </form>
    </div>

    <div id="sm-wishes-list" class="sm-wishes-list" aria-live="polite">
        @php
            $accentClasses = ['sm-wish-card--sun', 'sm-wish-card--coral', 'sm-wish-card--mint', 'sm-wish-card--sky'];
        @endphp
        @forelse($wishes as $index => $wish)
            @php
                $accentClass = $accentClasses[$index % count($accentClasses)];
            @endphp
            <article class="sm-card sm-wish-card {{ $accentClass }}">
                <div class="sm-wish-header">
                    <strong class="sm-wish-name">{{ $wish->name }}</strong>
                    @if($wish->origin)
                        <span class="sm-wish-origin">{{ $wish->origin }}</span>
                    @endif
                    <time class="sm-wish-time" datetime="{{ $wish->created_at?->toIso8601String() }}">
                        {{ $wish->created_at?->diffForHumans() }}
                    </time>
                </div>
                <p class="sm-wish-message">{{ $wish->message }}</p>
            </article>
        @empty
            <p id="sm-wishes-empty" class="sm-wishes-empty">{{ __('invitation.wishes.empty') }}</p>
        @endforelse
    </div>

    @if($wishes->hasMorePages())
        <div class="sm-wishes-more">
            <button
                type="button"
                id="sm-wishes-load-more"
                class="sm-btn sm-btn-outline sm-btn--sm"
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

