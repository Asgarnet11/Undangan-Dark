<div class="bh-gift-content">
    <h2 class="bh-display bh-section-title">{{ __('invitation.gift.heading') }}</h2>

    @if($invitation->thank_you_gift_note)
        <p class="bh-body-serif" style="max-width: 380px; margin: 0 auto 1.5rem; color: var(--bh-ink-soft);">
            {{ $invitation->thank_you_gift_note }}
        </p>
    @else
        <p class="bh-body-serif" style="max-width: 380px; margin: 0 auto 1.5rem; color: var(--bh-ink-soft);">
            Doa restu Anda merupakan karunia terindah bagi kami. Namun jika memberi adalah tanda kasih, kami menyediakan fitur amplop digital berikut.
        </p>
    @endif

    <div
        class="bh-gift-panel"
        data-gift-panel
        data-gifts-endpoint="{{ route('invitation.gifts.index', ['slug' => $invitation->slug]) }}"
        data-label-copy="{{ __('invitation.gift.copy') }}"
        data-label-copied="{{ __('invitation.gift.copied') }}"
        data-label-copy-failed="{{ __('invitation.gift.copy_failed') }}"
        data-label-holder="{{ __('invitation.gift.account_holder') }}"
    >
        @if($invitation->giftAccounts->isNotEmpty())
            <div class="bh-gift-accounts" style="margin-bottom: 1.5rem;">
                @foreach($invitation->giftAccounts as $account)
                    @php
                        $digits = preg_replace('/\s+/', '', (string) $account->account_number);
                        $masked = '•••• '.mb_substr($digits, -4);
                    @endphp
                    <article class="bh-card bh-card--gold-border bh-gift-account-card" style="margin-bottom: 1.25rem; text-align: center;" data-gift-account-id="{{ $account->id }}">
                        <p class="bh-kicker" style="font-size: 0.85rem; margin-bottom: 0.25rem; color: var(--bh-gold-soft);">
                            {{ $account->bank_name }}
                        </p>
                        <p style="font-family: monospace; font-size: 1.35rem; font-weight: 700; color: var(--bh-accent); letter-spacing: 0.05em; margin-bottom: 0.25rem;" data-gift-number>
                            {{ $masked }}
                        </p>
                        <p class="bh-body-serif" style="font-size: 0.85rem; margin-bottom: 0.85rem; color: var(--bh-ink-soft);">
                            {{ __('invitation.gift.account_holder') }} <strong>{{ $account->account_name }}</strong>
                        </p>
                        <button
                            type="button"
                            class="bh-btn bh-btn--sm"
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
            <div class="bh-gift-addresses" style="margin-bottom: 1.5rem;">
                <h3 class="bh-display" style="font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--bh-accent);">
                    {{ __('invitation.gift.physical_heading') }}
                </h3>
                @foreach($invitation->giftAddresses as $address)
                    <article class="bh-card bh-card--gold-border" style="margin-bottom: 1rem; text-align: left;" data-gift-address-id="{{ $address->id }}">
                        <strong class="bh-kicker" style="display: block; margin-bottom: 0.25rem; color: var(--bh-gold-soft);">
                            {{ $address->label }}
                        </strong>
                        <p class="bh-body-serif" style="font-size: 0.88rem; color: var(--bh-ink-soft);" data-gift-address-text>
                            {{ __('invitation.gift.reveal_button') }}
                        </p>
                    </article>
                @endforeach
            </div>
        @endif

        <button type="button" class="bh-btn bh-btn--sm" data-gift-reveal-btn style="margin-bottom: 1.5rem;">
            {{ __('invitation.gift.reveal_button') }}
        </button>

        <x-shared.qris-modal :invitation="$invitation" />
        <x-shared.gift-registry :invitation="$invitation" :guest="$guest ?? null" />

        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--bh-line); text-align: center;">
            <button
                type="button"
                class="bh-btn bh-btn--sm"
                id="bh-btn-toggle-gift-confirm"
                style="margin-bottom: 1rem;"
                onclick="const el = document.getElementById('bh-gift-confirm-section'); el.style.display = el.style.display === 'none' ? 'block' : 'none';"
            >
                Konfirmasi Kirim Tanda Kasih
            </button>

            <div id="bh-gift-confirm-section" class="bh-card bh-card--gold-border" style="display: none; text-align: left; margin-top: 1rem; padding: 1.5rem;">
                <p class="bh-kicker" style="margin-bottom: 0.5rem;">Konfirmasi Tanda Kasih</p>
                <form
                    method="POST"
                    action="{{ route('invitation.gifts.confirm', ['slug' => $invitation->slug]) }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label class="bh-kicker" style="display: block; margin-bottom: 0.25rem;">Nama Pengirim</label>
                        <input type="text" name="sender_name" required class="bh-input" value="{{ old('sender_name', $guest?->name ?? '') }}">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="bh-kicker" style="display: block; margin-bottom: 0.25rem;">Jumlah / Keterangan</label>
                        <input type="text" name="amount_or_item" required class="bh-input" placeholder="Contoh: Transfer BCA / Kado Sprei">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="bh-kicker" style="display: block; margin-bottom: 0.25rem;">Bukti Transfer (Opsional)</label>
                        <input type="file" name="proof" accept="image/*" class="bh-input" style="padding: 0.5rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem;">
                        <label class="bh-kicker" style="display: block; margin-bottom: 0.25rem;">Pesan untuk Pengantin</label>
                        <textarea name="message" rows="2" class="bh-input"></textarea>
                    </div>
                    <button type="submit" class="bh-btn bh-btn--primary bh-btn--sm" style="width: 100%;">
                        Kirim Konfirmasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
