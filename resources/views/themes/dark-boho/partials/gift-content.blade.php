<div class="db-ornament-cluster" aria-hidden="true">
    <x-dark-boho.svg name="dried-flower-cluster" class="db-svg-cluster" />
</div>

<p class="db-kicker">{{ __('invitation.gift.kicker') }}</p>
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

    <x-shared.qris-modal :invitation="$invitation" />
    <x-shared.gift-registry :invitation="$invitation" :guest="$guest ?? null" />

    <p class="db-form-feedback db-gift-status" data-gift-status role="status" aria-live="polite" hidden></p>

    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.08); text-align: center;">
        <button
            type="button"
            class="db-btn db-btn--sm"
            id="db-btn-toggle-gift-confirm"
            style="margin-bottom: 1rem;"
            onclick="const el = document.getElementById('db-gift-confirm-section'); el.style.display = el.style.display === 'none' ? 'block' : 'none';"
        >
            Konfirmasi Kirim Tanda Kasih
        </button>

        <div id="db-gift-confirm-section" class="db-card" style="display: none; text-align: left; margin-top: 1rem; padding: 1.25rem;">
            <h4 class="db-display" style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #fff;">Form Konfirmasi Tanda Kasih</h4>
            <p class="db-body-soft" style="font-size: 0.85rem; margin-bottom: 1rem;">
                Bagi Bapak/Ibu/Saudara/i yang telah mengirimkan tanda kasih, dapat melakukan konfirmasi di bawah ini agar kami dapat menyampaikan ucapan terima kasih secara langsung.
            </p>

            <form id="db-gift-confirm-form" method="POST" action="{{ route('invitation.gifts.confirm', ['slug' => $invitation->slug]) }}" enctype="multipart/form-data">
                @csrf
                @if($guestCode !== '')
                    <input type="hidden" name="guest_code" value="{{ $guestCode }}">
                @endif

                <div class="db-field" style="margin-bottom: 0.75rem;">
                    <label class="db-label" style="font-size: 0.85rem;">Nama Pengirim</label>
                    <input type="text" name="sender_name" class="db-input" required value="{{ $guest?->name ?? '' }}" placeholder="Nama Anda / Keluarga">
                </div>

                <div class="db-field" style="margin-bottom: 0.75rem;">
                    <label class="db-label" style="font-size: 0.85rem;">Tujuan Rekening / Hadiah</label>
                    <select name="target_account" class="db-input" style="background: #1d1d1b;">
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

                <div class="db-field" style="margin-bottom: 0.75rem;">
                    <label class="db-label" style="font-size: 0.85rem;">Nominal (Opsional)</label>
                    <input type="text" name="amount" class="db-input" placeholder="Contoh: Rp 200.000">
                </div>

                <div class="db-field" style="margin-bottom: 0.75rem;">
                    <label class="db-label" style="font-size: 0.85rem;">Bukti Transfer (Opsional)</label>
                    <input type="file" name="proof_file" accept="image/*" class="db-input" style="padding: 0.4rem;">
                </div>

                <div class="db-field" style="margin-bottom: 1rem;">
                    <label class="db-label" style="font-size: 0.85rem;">Pesan / Doa Restu (Opsional)</label>
                    <textarea name="notes" class="db-input" rows="2" placeholder="Tuliskan pesan atau doa untuk kedua mempelai..."></textarea>
                </div>

                <button type="submit" class="db-btn db-btn--sm" style="width: 100%;" id="db-btn-submit-gift">
                    Kirim Konfirmasi
                </button>
            </form>
            <p id="db-gift-confirm-feedback" style="display: none; margin-top: 0.75rem; font-size: 0.85rem; text-align: center; color: #4ade80;"></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('db-gift-confirm-form');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('db-btn-submit-gift');
                const feedback = document.getElementById('db-gift-confirm-feedback');
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
                        feedback.style.color = '#4ade80';
                        feedback.textContent = data.message;
                        form.reset();
                    } else {
                        feedback.style.color = '#f87171';
                        feedback.textContent = data.message || 'Gagal mengirim konfirmasi.';
                    }
                })
                .catch(() => {
                    feedback.style.display = 'block';
                    feedback.style.color = '#f87171';
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

