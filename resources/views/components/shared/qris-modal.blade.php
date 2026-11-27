@props(['invitation'])

@php
    $enabled = $invitation->isFeatureEnabled('qris', true);
    $qrisAccounts = $invitation->relationLoaded('qrisAccounts')
        ? $invitation->qrisAccounts->where('is_active', true)->values()
        : $invitation->qrisAccounts()->where('is_active', true)->get();
@endphp

@if($enabled && $qrisAccounts->isNotEmpty())
    <div
        class="shared-qris-block"
        data-qris-block
        style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--inv-border, rgba(128, 128, 128, 0.22)); text-align: center;"
    >
        <h3 style="font-family: var(--inv-font-display, inherit); font-size: 1.2rem; margin-bottom: 0.75rem;">
            {{ __('invitation.gift.qris_heading') }}
        </h3>
        @foreach($qrisAccounts as $qris)
            @if($qris->imageUrl())
                <div style="margin-bottom: 1rem; padding: 1rem; border-radius: 12px; border: 1px solid var(--inv-border, rgba(128, 128, 128, 0.22)); background: var(--inv-surface, rgba(128, 128, 128, 0.06));">
                    @if($qris->label)
                        <p style="font-size: 0.92rem; font-weight: 600; margin-bottom: 0.6rem;">{{ $qris->label }}</p>
                    @endif
                    <div style="display: inline-block; padding: 0.5rem; background: #ffffff; border-radius: 8px;">
                        <img
                            src="{{ $qris->imageUrl() }}"
                            alt="{{ $qris->label ?: 'Kode QRIS Tanda Kasih' }}"
                            style="max-width: 220px; width: 100%; height: auto; display: block;"
                            loading="lazy"
                        >
                    </div>
                    <div style="margin-top: 0.75rem;">
                        <a
                            href="{{ $qris->imageUrl() }}"
                            download
                            style="display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0.55rem 1.25rem; border-radius: 9999px; border: 1px solid var(--inv-border, currentColor); text-decoration: none; font-size: 0.88rem; font-weight: 500; color: inherit;"
                        >
                            {{ __('invitation.gift.download_qris') }}
                        </a>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
