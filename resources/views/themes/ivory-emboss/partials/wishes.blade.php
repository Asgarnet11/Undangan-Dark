@php
    $guestCode = $guest?->code ?? '';
    $timezone = $invitation->resolvedTimezone();
    $tzAbbr = $invitation->timezoneAbbreviation();
    $allWishes = isset($wishes) ? $wishes : collect();
@endphp

<section id="section-wishes" class="iv-section iv-wishes" aria-label="Ucapan dan Doa">
    <div class="iv-safe-zone">
        <p class="iv-kicker">{{ __('invitation.wishes.kicker') }}</p>
        <h2 class="iv-display iv-section-title">{{ __('invitation.wishes.heading') }}</h2>
        <p class="iv-section-subtitle">{{ __('invitation.wishes.subtitle') }}</p>

        <div class="iv-card iv-form-card" style="margin-bottom: 1.75rem;">
            <form
                id="iv-wishes-form"
                method="POST"
                action="{{ route('invitation.wishes.store', ['slug' => $invitation->slug]) }}"
                novalidate
            >
                @csrf
                @if($guestCode !== '')
                    <input type="hidden" name="guest_code" value="{{ $guestCode }}">
                @endif

                <div class="iv-field">
                    <label for="iv-wish-name" class="iv-kicker iv-field-label">
                        {{ __('invitation.wishes.name_label') }}
                    </label>
                    <input
                        id="iv-wish-name"
                        name="name"
                        type="text"
                        class="iv-input"
                        required
                        minlength="2"
                        maxlength="100"
                        placeholder="{{ __('invitation.wishes.name_placeholder') }}"
                        value="{{ old('name', $guest?->name ?? '') }}"
                    >
                </div>

                <div class="iv-field">
                    <label for="iv-wish-origin" class="iv-kicker iv-field-label">
                        {{ __('invitation.wishes.origin_label') }}
                    </label>
                    <input
                        id="iv-wish-origin"
                        name="origin"
                        type="text"
                        class="iv-input"
                        maxlength="100"
                        placeholder="{{ __('invitation.wishes.origin_placeholder') }}"
                        value="{{ old('origin') }}"
                    >
                </div>

                <div class="iv-field">
                    <label for="iv-wish-message" class="iv-kicker iv-field-label">
                        {{ __('invitation.wishes.message_label') }}
                    </label>
                    <textarea
                        id="iv-wish-message"
                        name="message"
                        class="iv-input"
                        rows="3"
                        required
                        minlength="3"
                        maxlength="500"
                        placeholder="{{ __('invitation.wishes.message_placeholder') }}"
                    >{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="iv-btn" style="width: 100%;">
                    {{ __('invitation.wishes.submit') }}
                </button>
                <p id="iv-wish-feedback" class="iv-form-feedback" aria-live="polite" hidden></p>
            </form>
        </div>

        <div id="iv-wishes-container" data-per-page="5">
            <div id="iv-wishes-list" class="iv-wishes-list">
                @foreach($allWishes as $wish)
                    @php
                        $localTime = $wish->created_at?->copy()->timezone($timezone)->locale('id');
                    @endphp
                    <article class="iv-card iv-wish-card" data-wish-item>
                        <div class="iv-wish-head">
                            <div>
                                <strong class="iv-wish-author">{{ $wish->name }}</strong>
                                @if($wish->origin)
                                    <span class="iv-wish-origin">&middot; {{ $wish->origin }}</span>
                                @endif
                            </div>
                            @if($localTime)
                                <time
                                    class="iv-wish-time"
                                    datetime="{{ $localTime->toIso8601String() }}"
                                    title="{{ $localTime->translatedFormat('d M Y, H:i') }} {{ $tzAbbr }}"
                                >
                                    {{ $localTime->diffForHumans() }}
                                </time>
                            @endif
                        </div>
                        <p class="iv-wish-body">{{ $wish->message }}</p>
                    </article>
                @endforeach
            </div>

            <div id="iv-wishes-pagination" class="iv-pagination" hidden>
                <button type="button" class="iv-btn iv-btn--ghost iv-page-btn" id="iv-wish-prev">Sebelumnya</button>
                <span id="iv-wish-page-info" class="iv-page-info">Halaman 1 dari 1</span>
                <button type="button" class="iv-btn iv-btn--ghost iv-page-btn" id="iv-wish-next">Selanjutnya</button>
            </div>
        </div>
    </div>
</section>
