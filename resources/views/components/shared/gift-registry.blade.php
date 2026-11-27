@props([
    'invitation',
    'guest' => null,
])

@php
    $registryEnabled = $invitation->isFeatureEnabled('registry', true);
    $links = $invitation->relationLoaded('giftRegistryLinks')
        ? $invitation->giftRegistryLinks->where('is_active', true)->filter(fn ($l) => str_starts_with(strtolower(trim($l->url)), 'https://'))->values()
        : $invitation->giftRegistryLinks()->where('is_active', true)->get()->filter(fn ($l) => str_starts_with(strtolower(trim($l->url)), 'https://'))->values();
    $waUrl = $invitation->giftWhatsappUrl($guest?->name);
@endphp

@if(($registryEnabled && $links->isNotEmpty()) || $waUrl !== null)
    <div
        class="shared-gift-registry"
        data-gift-registry
        style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--inv-border, rgba(128, 128, 128, 0.22)); text-align: center;"
    >
        @if($registryEnabled && $links->isNotEmpty())
            <h3 style="font-family: var(--inv-font-display, inherit); font-size: 1.2rem; margin-bottom: 0.45rem;">
                {{ __('invitation.gift.registry_heading') }}
            </h3>
            <p style="font-size: 0.9rem; opacity: 0.85; max-width: 360px; margin: 0 auto 1rem; line-height: 1.6;">
                {{ $invitation->registry_intro ?: __('invitation.gift.registry_intro_default') }}
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 0.6rem; justify-content: center; margin-bottom: 1rem;">
                @foreach($links as $link)
                    <a
                        href="{{ $link->url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        style="display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0.6rem 1.35rem; border-radius: 9999px; border: 1px solid var(--inv-border, currentColor); text-decoration: none; font-size: 0.88rem; font-weight: 500; color: inherit;"
                    >
                        {{ $link->label }}
                    </a>
                @endforeach
            </div>
        @endif

        @if($waUrl !== null)
            <div style="margin-top: 0.75rem;">
                <a
                    href="{{ $waUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    style="display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0.6rem 1.35rem; border-radius: 9999px; border: 1px solid var(--inv-border, currentColor); text-decoration: none; font-size: 0.88rem; font-weight: 600; color: inherit;"
                >
                    {{ __('invitation.gift.whatsapp_confirm') }}
                </a>
            </div>
        @endif
    </div>
@endif
