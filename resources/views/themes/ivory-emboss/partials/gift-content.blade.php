<div class="iv-gift-content">
    <p class="iv-kicker">{{ __('invitation.gift.kicker') }}</p>
    <h2 class="iv-display iv-section-title">{{ __('invitation.gift.heading') }}</h2>

    @if($invitation->thank_you_gift_note)
        <p class="iv-section-subtitle">{{ $invitation->thank_you_gift_note }}</p>
    @else
        <p class="iv-section-subtitle">{{ __('invitation.gift.subtitle') }}</p>
    @endif

    <div class="iv-bud-accent" data-density-layer="normal" aria-hidden="true">
        <img
            src="{{ asset('themes/ivory-emboss/relief/bud-cluster.webp') }}"
            srcset="{{ asset('themes/ivory-emboss/relief/bud-cluster.webp') }} 1x, {{ asset('themes/ivory-emboss/relief/bud-cluster@2x.webp') }} 2x"
            alt=""
            width="240"
            height="240"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div
        class="iv-gift-panel"
        data-gift-panel
        data-gifts-endpoint="{{ route('invitation.gifts.index', ['slug' => $invitation->slug]) }}"
        data-label-copy="{{ __('invitation.gift.copy') }}"
        data-label-copied="{{ __('invitation.gift.copied') }}"
        data-label-copy-failed="{{ __('invitation.gift.copy_failed') }}"
        data-label-holder="{{ __('invitation.gift.account_holder') }}"
    >
        @if($invitation->giftAccounts->isNotEmpty())
            <div class="iv-gift-accounts">
                @foreach($invitation->giftAccounts as $account)
                    @php
                        $digits = preg_replace('/\s+/', '', (string) $account->account_number);
                        $masked = '•••• ' . mb_substr($digits, -4);
                    @endphp
                    <article class="iv-card iv-gift-card" data-gift-account-id="{{ $account->id }}">
                        <p class="iv-kicker">{{ $account->bank_name }}</p>
                        <p class="iv-gift-number" data-gift-number>{{ $masked }}</p>
                        <p class="iv-gift-holder">
                            {{ __('invitation.gift.account_holder') }} <strong>{{ $account->account_name }}</strong>
                        </p>
                        <button type="button" class="iv-btn iv-btn--ghost iv-gift-copy-btn" data-copy-btn disabled>
                            {{ __('invitation.gift.copy') }}
                        </button>
                    </article>
                @endforeach
            </div>
        @endif

        @if($invitation->giftAddresses->isNotEmpty())
            <div class="iv-gift-addresses">
                <h3 class="iv-kicker" style="margin-bottom: 0.75rem;">{{ __('invitation.gift.physical_heading') }}</h3>
                @foreach($invitation->giftAddresses as $address)
                    <article class="iv-card iv-gift-card" style="text-align: left;" data-gift-address-id="{{ $address->id }}">
                        <strong class="iv-kicker" style="display: block; margin-bottom: 0.35rem;">{{ $address->label }}</strong>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--iv-ink-soft);" data-gift-address-text>
                            {{ __('invitation.gift.reveal_button') }}
                        </p>
                    </article>
                @endforeach
            </div>
        @endif

        @if($invitation->giftAccounts->isNotEmpty() || $invitation->giftAddresses->isNotEmpty())
            <button type="button" class="iv-btn" data-gift-reveal-btn style="margin-bottom: 1.25rem;">
                {{ __('invitation.gift.reveal_button') }}
            </button>
        @endif

        <x-shared.qris-modal :invitation="$invitation" />
        <x-shared.gift-registry :invitation="$invitation" :guest="$guest ?? null" />

        @if(!empty($invitation->gift_whatsapp_number))
            @php
                $waNumber = preg_replace('/\D+/', '', $invitation->gift_whatsapp_number);
                $waTemplate = $invitation->gift_whatsapp_template ?: 'Halo, saya ingin mengonfirmasi pengiriman kado untuk pernikahan ' . $invitation->bride_nickname . ' & ' . $invitation->groom_nickname . '.';
                if (!empty($guest?->name)) {
                    $waTemplate = str_replace('{nama_tamu}', $guest->name, $waTemplate);
                }
            @endphp
            @if($waNumber)
                <div style="margin-top: 1.25rem;">
                    <a
                        href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($waTemplate) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="iv-btn iv-btn--ghost"
                    >
                        Konfirmasi via WhatsApp
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>
