@extends('themes.minimalist-botanical.layout')

@php
    $previewMode = !empty($isPreview);
@endphp

@section('content')
    @include('themes.minimalist-botanical.partials.cover')

    <main id="mb-main" class="mb-main">
        @include('themes.minimalist-botanical.partials.countdown')

        @if(data_get($themeConfig, 'sections.verse', true) || $previewMode)
            <div data-preview-section="verse" @if(!data_get($themeConfig, 'sections.verse', true)) hidden @endif>
                @include('themes.minimalist-botanical.partials.verse')
            </div>
        @endif

        @include('themes.minimalist-botanical.partials.couple')

        @include('themes.minimalist-botanical.partials.events')

        @if(data_get($themeConfig, 'sections.gallery', true) || $previewMode)
            <div data-preview-section="gallery" @if(!data_get($themeConfig, 'sections.gallery', true)) hidden @endif>
                @include('themes.minimalist-botanical.partials.gallery')
            </div>
        @endif

        @if((data_get($themeConfig, 'sections.video', false) || $previewMode) && $invitation->video_url)
            <div data-preview-section="video" @if(!data_get($themeConfig, 'sections.video', false)) hidden @endif>
                @include('themes.minimalist-botanical.partials.video')
            </div>
        @endif

        @if((data_get($themeConfig, 'sections.livestream', false) || $previewMode) && $invitation->livestream_url)
            <div data-preview-section="livestream" @if(!data_get($themeConfig, 'sections.livestream', false)) hidden @endif>
                @include('themes.minimalist-botanical.partials.livestream')
            </div>
        @endif

        @include('themes.minimalist-botanical.partials.rsvp')

        @if((data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true)) || $previewMode)
            <div
                data-preview-section="gift-inline"
                @if(!(data_get($themeConfig, 'sections.gift', true) && data_get($themeConfig, 'layout.giftInline', true))) hidden @endif
            >
                @include('themes.minimalist-botanical.partials.gift')
            </div>
        @endif

        @if(data_get($themeConfig, 'sections.wishes', true) || $previewMode)
            <div data-preview-section="wishes" @if(!data_get($themeConfig, 'sections.wishes', true)) hidden @endif>
                @include('themes.minimalist-botanical.partials.wishes')
            </div>
        @endif

        @include('themes.minimalist-botanical.partials.closing')
    </main>

    @include('themes.minimalist-botanical.partials.bottom-nav')
@endsection
