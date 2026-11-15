<div class="sm-section-header">
    <div class="sm-decor-flower-center" aria-hidden="true">
        <x-sunny-meadow.svg name="flower-small" class="sm-svg-flower-sm" />
    </div>
    <p class="sm-section-subtitle">{{ __('invitation.gift.heading') }}</p>
    <h2 class="sm-section-title">{{ __('invitation.gift.heading') }}</h2>
</div>

@if($invitation->thank_you_gift_note)
    <p class="sm-gift-note">{{ $invitation->thank_you_gift_note }}</p>
@endif

<div
    class="sm-gift-panel"
    data-gift-panel
    data-gifts-endpoint="{{ route('invitation.gifts.index', ['slug' => $invitation->slug]) }}"
    data-label-copy="{{ __('invitation.gift.copy') }}"
    data-label-copied="{{ __('invitation.gift.copied') }}"
    data-label-copy-failed="{{ __('invitation.gift.copy_failed') }}"
    data-label-holder="{{ __('invitation.gift.account_holder') }}"
>
    @if($invitation->giftAccounts->isNotEmpty())
        <div class="sm-gift-accounts" data-gift-accounts>
            @foreach($invitation->giftAccounts as $account)
                @php
                    $digits = preg_replace('/\s+/', '', (string) $account->account_number);
                    $masked = '•••• '.mb_substr($digits, -4);
                @endphp
                <article class="sm-card sm-gift-card" data-gift-account-id="{{ $account->id }}">
                    <p class="sm-gift-bank">{{ $account->bank_name }}</p>
                    <p class="sm-gift-number" data-gift-number>{{ $masked }}</p>
                    <p class="sm-gift-holder">{{ __('invitation.gift.account_holder') }} {{ $account->account_name }}</p>
                    <button
                        type="button"
                        class="sm-btn sm-btn-primary sm-btn--sm sm-copy-btn"
                        data-copy-btn
                        disabled
                    >
                        {{ __('invitation.gift.copy') }}
                    </button>
                </article>
            @endforeach
        </div>
    @endif

    @if($invitation->giftAddresses->isNotEmpty())
        <div class="sm-gift-addresses" data-gift-addresses>
            <h3 class="sm-title-display sm-gift-addresses-title">{{ __('invitation.gift.physical_heading') }}</h3>
            @foreach($invitation->giftAddresses as $address)
                <article class="sm-card sm-gift-address-card" data-gift-address-id="{{ $address->id }}">
                    <strong class="sm-gift-address-label">{{ $address->label }}</strong>
                    <p class="sm-gift-address-text" data-gift-address-text>
                        {{ __('invitation.gift.reveal_button') }}
                    </p>
                </article>
            @endforeach
        </div>
    @endif

    <button type="button" class="sm-btn sm-btn-outline sm-btn--sm sm-gift-reveal-btn" data-gift-reveal-btn>
        {{ __('invitation.gift.reveal_button') }}
    </button>

    <p class="sm-form-feedback sm-gift-status" data-gift-status role="status" aria-live="polite" hidden></p>
</div>
