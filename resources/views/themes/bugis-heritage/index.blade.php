@extends('themes.bugis-heritage.layout')

@php
    $previewMode = !empty($isPreview);
@endphp

@section('content')
    @include('themes.bugis-heritage.partials.cover')

    <main id="bh-main" class="bh-main">
        @include('themes.bugis-heritage.partials.countdown')

        @if(data_get($themeConfig, 'sections.verse', true) || $previewMode)
            <div data-preview-section="verse" @if(!data_get($themeConfig, 'sections.verse', true)) hidden @endif>
                @include('themes.bugis-heritage.partials.verse')
            </div>
        @endif

        @include('themes.bugis-heritage.partials.couple')

        @if(data_get($themeConfig, 'sections.loveStory', true) || $previewMode)
            <div data-preview-section="loveStory" @if(!data_get($themeConfig, 'sections.loveStory', true)) hidden @endif>
                @include('themes.bugis-heritage.partials.love-story')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.gallery', true) || $previewMode)
            <div data-preview-section="gallery" @if(!data_get($themeConfig, 'sections.gallery', true)) hidden @endif>
                @include('themes.bugis-heritage.partials.gallery')
            </div>
        @endif

        @include('themes.bugis-heritage.partials.events')

        @include('themes.bugis-heritage.partials.rsvp')

        @if((data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true)) || $previewMode)
            <div
                data-preview-section="gift-inline"
                @if(!(data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true))) hidden @endif
            >
                @include('themes.bugis-heritage.partials.gift')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.wishes', true) || $previewMode)
            <div data-preview-section="wishes" @if(!data_get($themeConfig, 'sections.wishes', true)) hidden @endif>
                @include('themes.bugis-heritage.partials.wishes')
            </div>
        @endif

        @include('themes.bugis-heritage.partials.closing')
    </main>

    @include('themes.bugis-heritage.partials.bottom-nav')

    @include('themes.bugis-heritage.partials.credits')
@endsection
