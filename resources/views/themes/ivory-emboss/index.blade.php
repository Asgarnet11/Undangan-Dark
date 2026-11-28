@extends('themes.ivory-emboss.layout')

@php
    $previewMode = !empty($isPreview);
@endphp

@section('content')
    @include('themes.ivory-emboss.partials.cover')

    <main id="iv-main" class="iv-main">
        @include('themes.ivory-emboss.partials.hero')

        @if(data_get($themeConfig, 'sections.verse', true) || $previewMode)
            <div data-preview-section="verse" @if(!data_get($themeConfig, 'sections.verse', true)) hidden @endif>
                @include('themes.ivory-emboss.partials.verse')
            </div>
        @endif

        @include('themes.ivory-emboss.partials.couple')

        @include('themes.ivory-emboss.partials.countdown')

        @include('themes.ivory-emboss.partials.events')

        @if(data_get($themeConfig, 'sections.livestream', false) || $previewMode)
            <div data-preview-section="livestream" @if(!data_get($themeConfig, 'sections.livestream', false)) hidden @endif>
                @include('themes.ivory-emboss.partials.livestream')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.loveStory', true) || $previewMode)
            <div data-preview-section="loveStory" @if(!data_get($themeConfig, 'sections.loveStory', true)) hidden @endif>
                @include('themes.ivory-emboss.partials.love-story')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.video', false) || $previewMode)
            <div data-preview-section="video" @if(!data_get($themeConfig, 'sections.video', false)) hidden @endif>
                @include('themes.ivory-emboss.partials.video')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.gallery', true) || $previewMode)
            <div data-preview-section="gallery" @if(!data_get($themeConfig, 'sections.gallery', true)) hidden @endif>
                @include('themes.ivory-emboss.partials.gallery')
            </div>
        @endif

        @include('themes.ivory-emboss.partials.rsvp')

        @if((data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true)) || $previewMode)
            <div
                data-preview-section="gift-inline"
                @if(!(data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true))) hidden @endif
            >
                @include('themes.ivory-emboss.partials.gift')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.wishes', true) || $previewMode)
            <div data-preview-section="wishes" @if(!data_get($themeConfig, 'sections.wishes', true)) hidden @endif>
                @include('themes.ivory-emboss.partials.wishes')
            </div>
        @endif

        @include('themes.ivory-emboss.partials.closing')
    </main>

    @include('themes.ivory-emboss.partials.bottom-nav')
    @include('themes.ivory-emboss.partials.credits')
@endsection
