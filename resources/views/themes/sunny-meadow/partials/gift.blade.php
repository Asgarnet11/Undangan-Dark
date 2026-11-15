@if($invitation->giftAccounts->isNotEmpty() || $invitation->giftAddresses->isNotEmpty() || $invitation->thank_you_gift_note)
<section id="section-gift" class="sm-section sm-section-gift">
    @include('themes.sunny-meadow.partials.gift-content')
</section>
@endif
