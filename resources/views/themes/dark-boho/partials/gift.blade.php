@if($invitation->giftAccounts->isNotEmpty() || $invitation->giftAddresses->isNotEmpty() || $invitation->thank_you_gift_note)
<section id="section-gift" class="db-section db-section--deep db-reveal-section">
    @include('themes.dark-boho.partials.gift-content')
</section>
@endif
