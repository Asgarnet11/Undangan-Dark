<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meja Resepsi & Buku Tamu - {{ $invitation->title }}</title>
    <style>
        :root {
            --bg-main: #141413;
            --bg-card: #1c1c1a;
            --bg-card-hover: #262624;
            --primary: #c0714f;
            --primary-hover: #d28362;
            --text-main: #efe6d8;
            --text-muted: #a69e94;
            --border: rgba(255, 255, 255, 0.08);
            --success: #22c55e;
            --success-bg: rgba(34, 197, 94, 0.15);
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            line-height: 1.5;
            padding: 1rem;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        header {
            text-align: center;
            padding: 1.5rem 0 2rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 2rem;
        }
        .kicker {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        h1 { font-size: 1.75rem; font-weight: 700; color: #fff; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.25rem;
            text-align: center;
        }
        .stat-value { font-size: 2rem; font-weight: 700; color: #fff; }
        .stat-value.primary { color: var(--primary); }
        .stat-value.success { color: var(--success); }
        .stat-label { font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem; }
        .main-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .main-grid { grid-template-columns: 380px 1fr; }
        }
        .panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
        }
        .panel-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #reader {
            width: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #000;
            min-height: 250px;
        }
        .manual-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .input {
            flex: 1;
            background: #262624;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 0.65rem 0.85rem;
            color: #fff;
            font-size: 0.95rem;
        }
        .input:focus { outline: none; border-color: var(--primary); }
        .btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover { background: var(--primary-hover); }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-muted); }
        .btn-outline:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .result-box {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            display: none;
            text-align: center;
        }
        .result-box.success { background: var(--success-bg); border: 1px solid var(--success); color: #4ade80; }
        .result-box.warning { background: rgba(245, 158, 11, 0.15); border: 1px solid var(--warning); color: #fbbf24; }
        .result-box.error { background: rgba(239, 68, 68, 0.15); border: 1px solid var(--danger); color: #f87171; }
        .search-box { margin-bottom: 1rem; }
        .guest-list {
            max-height: 520px;
            overflow-y: auto;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
        }
        .guest-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
            transition: background 0.15s;
        }
        .guest-item:last-child { border-bottom: none; }
        .guest-item:hover { background: var(--bg-card-hover); }
        .guest-item.checked { background: rgba(34, 197, 94, 0.05); }
        .guest-info h4 { font-size: 0.95rem; font-weight: 600; color: #fff; }
        .guest-meta { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem; }
        .badge {
            display: inline-block;
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            font-weight: 500;
        }
        .badge-code { background: #333; color: #ddd; font-family: monospace; }
        .badge-success { background: var(--success-bg); color: #4ade80; border: 1px solid var(--success); }
        .badge-gray { background: #262624; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <p class="kicker">Buku Tamu & Meja Resepsi</p>
            <h1>{{ $invitation->title }}</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">
                {{ $invitation->bride_nickname }} &amp; {{ $invitation->groom_nickname }}
            </p>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" id="stat-total">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Tamu Terdaftar</div>
            </div>
            <div class="stat-card">
                <div class="stat-value success" id="stat-checked">{{ $stats['checked_in'] }}</div>
                <div class="stat-label">Sudah Check-In</div>
            </div>
            <div class="stat-card">
                <div class="stat-value primary" id="stat-pax">{{ $stats['pax_present'] }}</div>
                <div class="stat-label">Total Pax Hadir</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-pending">{{ $stats['pending'] }}</div>
                <div class="stat-label">Belum Tiba</div>
            </div>
        </section>

        <div class="main-grid">
            <section class="panel">
                <h2 class="panel-title">
                    <span>Pemindai QR Code</span>
                    <button type="button" class="btn btn-sm btn-outline" id="btn-toggle-cam">Mulai Kamera</button>
                </h2>
                <div id="reader"></div>
                
                <div id="result-box" class="result-box">
                    <p id="result-message" style="font-weight: 600;"></p>
                    <p id="result-detail" style="font-size: 0.85rem; margin-top: 0.25rem;"></p>
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <label for="manual-code" style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 0.35rem;">
                        Check-in Cepat via Kode Tamu
                    </label>
                    <form id="manual-form" class="manual-form">
                        <input type="text" id="manual-code" class="input" placeholder="Masukkan 6 digit kode" maxlength="32" style="text-transform: uppercase;">
                        <button type="submit" class="btn">Check-In</button>
                    </form>
                </div>
            </section>

            <section class="panel">
                <h2 class="panel-title">
                    <span>Daftar Tamu Undangan</span>
                    <span style="font-size: 0.85rem; font-weight: 400; color: var(--text-muted);">
                        Real-time Check-In
                    </span>
                </h2>

                <div class="search-box">
                    <input type="text" id="search-guest" class="input" placeholder="Cari nama atau grup tamu..." style="width: 100%;">
                </div>

                <div class="guest-list" id="guest-items-container">
                    @forelse($guests as $guest)
                        @php
                            $isChecked = $guest->checked_in_at !== null;
                            $pax = $guest->rsvp?->attending ? max(1, (int) $guest->rsvp->pax) : max(1, (int) $guest->max_pax);
                        @endphp
                        <div class="guest-item @if($isChecked) checked @endif" data-guest-id="{{ $guest->id }}" data-code="{{ $guest->code }}" data-name="{{ strtolower($guest->name) }}" data-group="{{ strtolower($guest->group ?: '') }}">
                            <div class="guest-info">
                                <h4>{{ $guest->name }}</h4>
                                <div class="guest-meta">
                                    <span class="badge badge-code">{{ $guest->code }}</span>
                                    <span>{{ $guest->group ?: 'Umum' }}</span>
                                    <span>&bull; {{ $pax }} Pax</span>
                                    @if($isChecked)
                                        <span class="badge badge-success check-time">{{ $guest->checked_in_at->format('H:i') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="guest-actions">
                                @if($isChecked)
                                    <button type="button" class="btn btn-sm btn-outline btn-undo" data-id="{{ $guest->id }}" data-name="{{ $guest->name }}">Batal</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-checkin" data-id="{{ $guest->id }}" data-code="{{ $guest->code }}">Hadir</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p style="padding: 1.5rem; text-align: center; color: var(--text-muted);">Belum ada tamu terdaftar.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const checkInUrl = "{{ route('invitation.check-in.process', ['slug' => $invitation->slug]) }}";
            const undoBaseUrl = "{{ url('/'.$invitation->slug.'/check-in') }}";

            const resultBox = document.getElementById('result-box');
            const resultMsg = document.getElementById('result-message');
            const resultDetail = document.getElementById('result-detail');
            const manualForm = document.getElementById('manual-form');
            const manualInput = document.getElementById('manual-code');
            const searchInput = document.getElementById('search-guest');
            const guestContainer = document.getElementById('guest-items-container');
            const toggleCamBtn = document.getElementById('btn-toggle-cam');

            let html5QrCode = null;
            let isScanning = false;

            function showResult(message, detail, type) {
                resultBox.className = 'result-box ' + type;
                resultMsg.textContent = message;
                resultDetail.textContent = detail || '';
                resultBox.style.display = 'block';
            }

            function submitCheckIn(codeOrId, isId = false) {
                const payload = isId ? { guest_id: codeOrId } : { code: codeOrId };

                fetch(checkInUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
                .then(response => {
                    const data = response.data;
                    if (!response.ok) {
                        showResult('Gagal Check-In', data.message || 'Data tidak ditemukan.', 'error');
                        return;
                    }

                    const type = data.already_checked_in ? 'warning' : 'success';
                    showResult(data.message, data.guest ? `${data.guest.name} (${data.guest.pax} Pax) - ${data.guest.code}` : '', type);

                    if (data.guest) {
                        updateGuestInList(data.guest.id, data.guest.checked_in_at, true);
                    }
                })
                .catch(err => {
                    showResult('Kesalahan Jaringan', 'Gagal menghubungi server.', 'error');
                });
            }

            function submitUndo(guestId, guestName) {
                if (!confirm(`Batalkan kehadiran untuk ${guestName}?`)) return;

                fetch(`${undoBaseUrl}/${guestId}/undo`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        showResult('Check-In Dibatalkan', data.message, 'warning');
                        updateGuestInList(guestId, null, false);
                    }
                })
                .catch(() => {
                    showResult('Gagal', 'Terjadi kesalahan saat membatalkan.', 'error');
                });
            }

            function updateGuestInList(guestId, checkTime, isChecked) {
                const item = document.querySelector(`.guest-item[data-guest-id="${guestId}"]`);
                if (!item) return;

                const actions = item.querySelector('.guest-actions');
                const meta = item.querySelector('.guest-meta');
                let timeBadge = meta.querySelector('.check-time');

                if (isChecked) {
                    item.classList.add('checked');
                    if (!timeBadge) {
                        timeBadge = document.createElement('span');
                        timeBadge.className = 'badge badge-success check-time';
                        meta.appendChild(timeBadge);
                    }
                    timeBadge.textContent = 'Hadir';
                    actions.innerHTML = `<button type="button" class="btn btn-sm btn-outline btn-undo" data-id="${guestId}">Batal</button>`;
                } else {
                    item.classList.remove('checked');
                    if (timeBadge) timeBadge.remove();
                    const code = item.getAttribute('data-code');
                    actions.innerHTML = `<button type="button" class="btn btn-sm btn-checkin" data-id="${guestId}" data-code="${code}">Hadir</button>`;
                }

                // Recalculate stats
                const totalChecked = document.querySelectorAll('.guest-item.checked').length;
                const total = document.querySelectorAll('.guest-item').length;
                document.getElementById('stat-checked').textContent = totalChecked;
                document.getElementById('stat-pending').textContent = Math.max(0, total - totalChecked);
            }

            // Delegated click on guest list
            guestContainer.addEventListener('click', function (e) {
                const checkBtn = e.target.closest('.btn-checkin');
                if (checkBtn) {
                    const id = checkBtn.getAttribute('data-id');
                    submitCheckIn(id, true);
                    return;
                }

                const undoBtn = e.target.closest('.btn-undo');
                if (undoBtn) {
                    const id = undoBtn.getAttribute('data-id');
                    const name = undoBtn.getAttribute('data-name') || 'Tamu';
                    submitUndo(id, name);
                }
            });

            // Manual code submit
            manualForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const code = manualInput.value.trim().toUpperCase();
                if (!code) return;
                submitCheckIn(code, false);
                manualInput.value = '';
            });

            // Search filter
            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                const items = document.querySelectorAll('.guest-item');
                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    const group = item.getAttribute('data-group') || '';
                    const code = (item.getAttribute('data-code') || '').toLowerCase();
                    if (name.includes(term) || group.includes(term) || code.includes(term)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Camera QR Scanner
            toggleCamBtn.addEventListener('click', function () {
                if (isScanning) {
                    stopCamera();
                } else {
                    startCamera();
                }
            });

            function startCamera() {
                if (typeof Html5Qrcode === 'undefined') {
                    showResult('Scanner Gagal', 'Library pemindai tidak tersedia.', 'error');
                    return;
                }

                html5QrCode = new Html5Qrcode("reader");
                const config = { fps: 10, qrbox: { width: 220, height: 220 } };

                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        // QR detected
                        let code = decodedText.trim();
                        try {
                            const parsedUrl = new URL(code);
                            const toParam = parsedUrl.searchParams.get('to');
                            if (toParam) code = toParam.trim();
                        } catch (e) {}

                        submitCheckIn(code, false);
                    },
                    (errorMessage) => {
                        // ignore scan errors
                    }
                ).then(() => {
                    isScanning = true;
                    toggleCamBtn.textContent = 'Hentikan Kamera';
                    toggleCamBtn.classList.remove('btn-outline');
                }).catch(err => {
                    showResult('Izin Kamera Ditolak', 'Pastikan izin akses kamera diberikan pada peramban.', 'error');
                });
            }

            function stopCamera() {
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        isScanning = false;
                        toggleCamBtn.textContent = 'Mulai Kamera';
                        toggleCamBtn.classList.add('btn-outline');
                    });
                }
            }
        });
    </script>
</body>
</html>
