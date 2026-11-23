<div class="sm-section-header">
    <div class="sm-decor-flower-center" aria-hidden="true">
        <x-sunny-meadow.svg name="flower-small" class="sm-svg-flower-sm" />
    </div>
    <p class="sm-section-subtitle">{{ __('invitation.gift.kicker') }}</p>
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

    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(0,0,0,0.06); text-align: center;">
        <button
            type="button"
            class="sm-btn sm-btn-primary sm-btn--sm"
            id="sm-btn-toggle-gift-confirm"
            style="margin-bottom: 1rem;"
            onclick="const el = document.getElementById('sm-gift-confirm-section'); el.style.display = el.style.display === 'none' ? 'block' : 'none';"
        >
            Konfirmasi Kirim Tanda Kasih
        </button>

        <div id="sm-gift-confirm-section" class="sm-card" style="display: none; text-align: left; margin-top: 1rem; padding: 1.25rem;">
            <h4 class="sm-title-display" style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #3b2f2a;">Form Konfirmasi Tanda Kasih</h4>
            <p class="sm-gift-note" style="font-size: 0.85rem; margin-bottom: 1rem;">
                Bagi Bapak/Ibu/Saudara/i yang telah mengirimkan tanda kasih, dapat melakukan konfirmasi di bawah ini agar kami dapat menyampaikan ucapan terima kasih secara langsung.
            </p>

            <form id="sm-gift-confirm-form" method="POST" action="{{ route('invitation.gifts.confirm', ['slug' => $invitation->slug]) }}" enctype="multipart/form-data">
                @csrf
                @if($guestCode !== '')
                    <input type="hidden" name="guest_code" value="{{ $guestCode }}">
                @endif

                <div class="sm-field" style="margin-bottom: 0.75rem;">
                    <label class="sm-label" style="font-size: 0.85rem;">Nama Pengirim</label>
                    <input type="text" name="sender_name" class="sm-input" required value="{{ $guest?->name ?? '' }}" placeholder="Nama Anda / Keluarga">
                </div>

                <div class="sm-field" style="margin-bottom: 0.75rem;">
                    <label class="sm-label" style="font-size: 0.85rem;">Tujuan Rekening / Hadiah</label>
                    <select name="target_account" class="sm-input">
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

                <div class="sm-field" style="margin-bottom: 0.75rem;">
                    <label class="sm-label" style="font-size: 0.85rem;">Nominal (Opsional)</label>
                    <input type="text" name="amount" class="sm-input" placeholder="Contoh: Rp 200.000">
                </div>

                <div class="sm-field" style="margin-bottom: 0.75rem;">
                    <label class="sm-label" style="font-size: 0.85rem;">Bukti Transfer (Opsional)</label>
                    <input type="file" name="proof_file" accept="image/*" class="sm-input" style="padding: 0.4rem;">
                </div>

                <div class="sm-field" style="margin-bottom: 1rem;">
                    <label class="sm-label" style="font-size: 0.85rem;">Pesan / Doa Restu (Opsional)</label>
                    <textarea name="notes" class="sm-input" rows="2" placeholder="Tuliskan pesan atau doa untuk kedua mempelai..."></textarea>
                </div>

                <button type="submit" class="sm-btn sm-btn-primary sm-btn--sm" style="width: 100%;" id="sm-btn-submit-gift">
                    Kirim Konfirmasi
                </button>
            </form>
            <p id="sm-gift-confirm-feedback" style="display: none; margin-top: 0.75rem; font-size: 0.85rem; text-align: center; color: #15803d;"></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('sm-gift-confirm-form');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('sm-btn-submit-gift');
                const feedback = document.getElementById('sm-gift-confirm-feedback');
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
</div>

