<p class="mb-kicker">{{ __('invitation.gift.kicker') }}</p>
<h2 class="mb-display mb-section-title">{{ __('invitation.gift.heading') }}</h2>

@if($invitation->thank_you_gift_note)
    <p class="mb-body-soft" style="max-width: 380px; margin: 0 auto 1.5rem;">{{ $invitation->thank_you_gift_note }}</p>
@endif

<div
    class="mb-gift-panel"
    data-gift-panel
    data-gifts-endpoint="{{ route('invitation.gifts.index', ['slug' => $invitation->slug]) }}"
    data-label-copy="{{ __('invitation.gift.copy') }}"
    data-label-copied="{{ __('invitation.gift.copied') }}"
    data-label-copy-failed="{{ __('invitation.gift.copy_failed') }}"
    data-label-holder="{{ __('invitation.gift.account_holder') }}"
>
    @if($invitation->giftAccounts->isNotEmpty())
        <div class="mb-gift-accounts" style="margin-bottom: 1.5rem;">
            @foreach($invitation->giftAccounts as $account)
                @php
                    $digits = preg_replace('/\s+/', '', (string) $account->account_number);
                    $masked = '•••• '.mb_substr($digits, -4);
                @endphp
                <article class="mb-card" style="margin-bottom: 1rem; text-align: center;" data-gift-account-id="{{ $account->id }}">
                    <p class="mb-kicker" style="font-size: 0.85rem; margin-bottom: 0.25rem;">{{ $account->bank_name }}</p>
                    <p style="font-family: monospace; font-size: 1.25rem; font-weight: 700; color: var(--mb-text-main); margin-bottom: 0.25rem;" data-gift-number>{{ $masked }}</p>
                    <p class="mb-body-soft" style="font-size: 0.85rem; margin-bottom: 0.75rem;">{{ __('invitation.gift.account_holder') }} {{ $account->account_name }}</p>
                    <button
                        type="button"
                        class="mb-btn mb-btn--ghost mb-btn--sm"
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
        <div class="mb-gift-addresses" style="margin-bottom: 1.5rem;">
            <h3 class="mb-display" style="font-size: 1.25rem; margin-bottom: 0.75rem;">{{ __('invitation.gift.physical_heading') }}</h3>
            @foreach($invitation->giftAddresses as $address)
                <article class="mb-card" style="margin-bottom: 1rem; text-align: left;" data-gift-address-id="{{ $address->id }}">
                    <strong class="mb-kicker" style="display: block; margin-bottom: 0.25rem;">{{ $address->label }}</strong>
                    <p class="mb-body-soft" data-gift-address-text>
                        {{ __('invitation.gift.reveal_button') }}
                    </p>
                </article>
            @endforeach
        </div>
    @endif

    <button type="button" class="mb-btn mb-btn--ghost mb-btn--sm" data-gift-reveal-btn style="margin-bottom: 1.5rem;">
        {{ __('invitation.gift.reveal_button') }}
    </button>

    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--mb-border); text-align: center;">
        <button
            type="button"
            class="mb-btn mb-btn--sm"
            id="mb-btn-toggle-gift-confirm"
            style="margin-bottom: 1rem;"
            onclick="const el = document.getElementById('mb-gift-confirm-section'); el.style.display = el.style.display === 'none' ? 'block' : 'none';"
        >
            Konfirmasi Kirim Tanda Kasih
        </button>

        <div id="mb-gift-confirm-section" class="mb-card" style="display: none; text-align: left; margin-top: 1rem; padding: 1.25rem;">
            <h4 class="mb-display" style="font-size: 1.15rem; margin-bottom: 0.5rem; color: var(--mb-text-main);">Form Konfirmasi Tanda Kasih</h4>
            <p class="mb-body-soft" style="font-size: 0.85rem; margin-bottom: 1rem;">
                Bagi Bapak/Ibu/Saudara/i yang telah mengirimkan tanda kasih, dapat melakukan konfirmasi di bawah ini agar kami dapat menyampaikan ucapan terima kasih secara langsung.
            </p>

            <form id="mb-gift-confirm-form" method="POST" action="{{ route('invitation.gifts.confirm', ['slug' => $invitation->slug]) }}" enctype="multipart/form-data">
                @csrf
                @if($guestCode !== '')
                    <input type="hidden" name="guest_code" value="{{ $guestCode }}">
                @endif

                <div style="margin-bottom: 0.75rem;">
                    <label class="mb-kicker" style="display: block; font-size: 0.75rem; margin-bottom: 0.25rem;">Nama Pengirim</label>
                    <input type="text" name="sender_name" class="mb-input" required value="{{ $guest?->name ?? '' }}" placeholder="Nama Anda / Keluarga">
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <label class="mb-kicker" style="display: block; font-size: 0.75rem; margin-bottom: 0.25rem;">Tujuan Rekening / Hadiah</label>
                    <select name="target_account" class="mb-input">
                        @foreach($invitation->giftAccounts as $acc)
                            <option value="{{ $acc->bank_name }} ({{ $acc->account_name }})">
                                {{ $acc->bank_name }} - a.n. {{ $acc->account_name }}
                            </option>
                        @endforeach
                        @if($invitation->giftAddresses->isNotEmpty())
                            <option value="Kirim Hadiah Fisik ke Alamat">Kirim Hadiah Fisik ke Alamat Mempelai</option>
                        @endif
                    </select>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <label class="mb-kicker" style="display: block; font-size: 0.75rem; margin-bottom: 0.25rem;">Nominal (Opsional)</label>
                    <input type="text" name="amount" class="mb-input" placeholder="Contoh: Rp 200.000">
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <label class="mb-kicker" style="display: block; font-size: 0.75rem; margin-bottom: 0.25rem;">Bukti Transfer (Opsional)</label>
                    <input type="file" name="proof_file" accept="image/*" class="mb-input" style="padding: 0.4rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="mb-kicker" style="display: block; font-size: 0.75rem; margin-bottom: 0.25rem;">Pesan / Doa Restu (Opsional)</label>
                    <textarea name="notes" class="mb-input" rows="2" placeholder="Tuliskan pesan atau doa untuk kedua mempelai..."></textarea>
                </div>

                <button type="submit" class="mb-btn mb-btn--sm" style="width: 100%;" id="mb-btn-submit-gift">
                    Kirim Konfirmasi
                </button>
            </form>
            <p id="mb-gift-confirm-feedback" style="display: none; margin-top: 0.75rem; font-size: 0.85rem; text-align: center; color: var(--mb-primary);"></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('mb-gift-confirm-form');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('mb-btn-submit-gift');
            const feedback = document.getElementById('mb-gift-confirm-feedback');
            btn.disabled = true;
            btn.textContent = 'Mengirim...';

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                feedback.style.display = 'block';
                if (data.ok) {
                    feedback.style.color = '#15803d';
                    feedback.textContent = data.message;
                    form.reset();
                } else {
                    feedback.style.color = '#dc2626';
                    feedback.textContent = data.message || 'Gagal mengirim konfirmasi.';
                }
            })
            .catch(() => {
                feedback.style.display = 'block';
                feedback.style.color = '#dc2626';
                feedback.textContent = 'Terjadi kesalahan jaringan.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Kirim Konfirmasi';
            });
        });
    });
</script>
