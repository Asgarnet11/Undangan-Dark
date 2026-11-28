@if($invitation->livestream_url || !empty($isPreview))
<section id="section-livestream" class="iv-section iv-livestream" aria-label="Siaran Langsung">
    <div class="iv-safe-zone">
        <div class="iv-card">
            <p class="iv-kicker">Siaran Langsung</p>
            <h2 class="iv-display iv-section-title" style="font-size: 1.4rem;">
                {{ $invitation->livestream_title ?: 'Live Streaming Acara' }}
            </h2>
            @if($invitation->livestream_description)
                <p class="iv-section-subtitle" style="margin-bottom: 1.25rem;">
                    {{ $invitation->livestream_description }}
                </p>
            @endif
            @if($invitation->livestream_url)
                <a
                    href="{{ $invitation->livestream_url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="iv-btn"
                >
                    Gabung Sekarang
                </a>
            @endif
        </div>
    </div>
</section>
@endif
