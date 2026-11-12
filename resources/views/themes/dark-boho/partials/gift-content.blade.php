<div class="db-ornament-cluster" aria-hidden="true">
    <x-dark-boho.svg name="dried-flower-cluster" class="db-svg-cluster" />
</div>

<p class="db-kicker">{{ __('invitation.gift.heading') }}</p>
<h2 class="db-display db-section-title">{{ __('invitation.gift.heading') }}</h2>

@if($invitation->thank_you_gift_note)
    <p class="db-body-soft db-gift-note">{{ $invitation->thank_you_gift_note }}</p>
@endif

<div
    class="db-gift-panel"
    data-gift-panel
    data-gifts-endpoint="{{ route('invitation.gifts.index', ['slug' => $invitation->slug]) }}"
    data-label-copy="{{ __('invitation.gift.copy') }}"
    data-label-copied="{{ __('invitation.gift.copied') }}"
    data-label-copy-failed="{{ __('invitation.gift.copy_failed') }}"
    data-label-holder="{{ __('invitation.gift.account_holder') }}"
>
    @if($invitation->giftAccounts->isNotEmpty())
        <div class="db-gift-accounts" data-gift-accounts>
            @foreach($invitation->giftAccounts as $account)
                @php
                    $digits = preg_replace('/\s+/', '', (string) $account->account_number);
                    $masked = '•••• '.mb_substr($digits, -4);
                @endphp
                <article class="db-card db-gift-card" data-gift-account-id="{{ $account->id }}">
                    <p class="db-gift-bank">{{ $account->bank_name }}</p>
                    <p class="db-gift-number" data-gift-number>{{ $masked }}</p>
                    <p class="db-gift-holder">{{ __('invitation.gift.account_holder') }} {{ $account->account_name }}</p>
                    <button
                        type="button"
                        class="db-btn db-btn--ghost db-btn--sm db-copy-btn"
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
        <div class="db-gift-addresses" data-gift-addresses>
            <h3 class="db-kicker db-gift-addresses-title">{{ __('invitation.gift.physical_heading') }}</h3>
            @foreach($invitation->giftAddresses as $address)
                <article class="db-card db-gift-address-card" data-gift-address-id="{{ $address->id }}">
                    <strong class="db-gift-address-label">{{ $address->label }}</strong>
                    <p class="db-body-soft db-gift-address-text" data-gift-address-text>
                        {{ __('invitation.gift.reveal_button') }}
                    </p>
                </article>
            @endforeach
        </div>
    @endif

    <button type="button" class="db-btn db-btn--ghost db-btn--sm db-gift-reveal-btn" data-gift-reveal-btn>
        {{ __('invitation.gift.reveal_button') }}
    </button>

    <p class="db-form-feedback db-gift-status" data-gift-status role="status" aria-live="polite" hidden></p>
</div>

