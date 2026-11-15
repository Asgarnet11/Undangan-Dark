@extends('themes.sunny-meadow.layout')

@php
    $previewMode = !empty($isPreview);
@endphp

@section('content')
    @include('themes.sunny-meadow.partials.cover')

    <main id="sm-main" class="sm-main">
        @include('themes.sunny-meadow.partials.countdown')

        @if(data_get($themeConfig, 'sections.verse', true) || $previewMode)
            <div data-preview-section="verse" @if(!data_get($themeConfig, 'sections.verse', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.verse')
            </div>
        @endif

        @include('themes.sunny-meadow.partials.couple')

        @if((data_get($themeConfig, 'sections.loveStory', true) || $previewMode))
            <div data-preview-section="loveStory" @if(!data_get($themeConfig, 'sections.loveStory', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.love-story')
            </div>
        @endif

        @include('themes.sunny-meadow.partials.events')

        @if(data_get($themeConfig, 'sections.gallery', true) || $previewMode)
            <div data-preview-section="gallery" @if(!data_get($themeConfig, 'sections.gallery', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.gallery')
            </div>
        @endif

        @if((data_get($themeConfig, 'sections.livestream', false) || $previewMode) && $invitation->livestream_url)
            <div data-preview-section="livestream" @if(!data_get($themeConfig, 'sections.livestream', false)) hidden @endif>
                @include('themes.sunny-meadow.partials.livestream')
            </div>
        @endif

        @include('themes.sunny-meadow.partials.rsvp')

        @if((data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true)) || $previewMode)
            <div
                data-preview-section="gift-inline"
                @if(!(data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true))) hidden @endif
            >
                @include('themes.sunny-meadow.partials.gift')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.wishes', true) || $previewMode)
            <div data-preview-section="wishes" @if(!data_get($themeConfig, 'sections.wishes', true)) hidden @endif>
                @include('themes.sunny-meadow.partials.wishes')
            </div>
        @endif

        @include('themes.sunny-meadow.partials.closing')
    </main>

    @if((data_get($themeConfig, 'sections.gift', true) && !data_get($themeConfig, 'layout.giftInline', true)) || $previewMode)
        <button
            type="button"
            id="sm-gift-fab"
            class="sm-gift-fab"
            aria-label="{{ __('invitation.gift.floating_button') }}"
            hidden
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true">
                <rect x="3" y="8" width="18" height="4" rx="1" />
                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7M12 8v13" />
                <path d="M12 8H8.5a2.5 2.5 0 0 1 0-5C10.5 3 12 8 12 8Zm0 0h3.5a2.5 2.5 0 0 0 0-5C13.5 3 12 8 12 8Z" />
            </svg>
            <span>{{ __('invitation.gift.floating_button') }}</span>
        </button>

        <div
            id="sm-gift-modal"
            class="sm-modal"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('invitation.gift.heading') }}"
            hidden
        >
            <div class="sm-modal-backdrop" data-gift-modal-close></div>
            <div class="sm-modal-dialog sm-card">
                <button
                    type="button"
                    id="sm-gift-modal-close"
                    class="sm-modal-close"
                    aria-label="{{ __('invitation.gift.close_modal') }}"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                    </svg>
                </button>
                @include('themes.sunny-meadow.partials.gift-content')
            </div>
        </div>
    @endif

    @include('themes.sunny-meadow.partials.bottom-nav')
@endsection
