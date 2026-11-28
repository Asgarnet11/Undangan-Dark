/**
 * Ivory Emboss Theme JS — Fase 4: Cover, Countdown, Nav, Maps, Video, Lightbox, RSVP, Wishes, Gift, Credits
 */
(function () {
    'use strict';

    const root = document.body;
    if (!root || !root.classList.contains('theme-ivory-emboss')) {
        return;
    }

    const cover = document.getElementById('iv-cover');
    const openBtn = document.getElementById('iv-open-invitation');
    const bottomNav = document.getElementById('iv-bottom-nav');
    const bgm = document.getElementById('iv-bgm');
    const musicBtn = document.getElementById('iv-music-toggle');

    function setMusicState(playing) {
        if (!musicBtn) {
            return;
        }
        musicBtn.classList.toggle('is-playing', playing);
        musicBtn.setAttribute('aria-pressed', playing ? 'true' : 'false');
        const label = playing
            ? musicBtn.getAttribute('data-label-pause')
            : musicBtn.getAttribute('data-label-play');
        if (label) {
            musicBtn.setAttribute('aria-label', label);
        }
    }

    if (musicBtn && bgm) {
        musicBtn.addEventListener('click', function () {
            if (bgm.paused) {
                bgm.play().then(() => setMusicState(true)).catch(() => setMusicState(false));
            } else {
                bgm.pause();
                setMusicState(false);
            }
        });
    }

    function openInvitation() {
        root.classList.remove('is-locked');
        if (cover) {
            cover.classList.add('is-opening');
            requestAnimationFrame(function () {
                cover.classList.add('is-opened');
            });
            setTimeout(function () {
                cover.classList.remove('is-opening');
            }, 1200);
        }
        if (bottomNav) {
            bottomNav.hidden = false;
        }
        if (musicBtn) {
            musicBtn.hidden = false;
        }
        if (bgm && bgm.paused) {
            bgm.play().then(() => setMusicState(true)).catch(() => setMusicState(false));
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', openInvitation);
    }

    if (root.getAttribute('data-preview-mode') === 'true' && bottomNav) {
        bottomNav.hidden = false;
    }

    document.querySelectorAll('.iv-nav-item[href="#iv-cover"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            if (cover) {
                cover.classList.remove('is-opened');
                root.classList.add('is-locked');
                if (bottomNav) {
                    bottomNav.hidden = true;
                }
            }
        });
    });

    // Hitung mundur langsung
    const countdownEl = document.getElementById('iv-countdown');
    const elapsedEl = document.getElementById('iv-countdown-elapsed');
    if (countdownEl) {
        const targetTs = Number(countdownEl.getAttribute('data-timestamp') || 0) * 1000;
        const els = {
            days: document.getElementById('iv-days'),
            hours: document.getElementById('iv-hours'),
            minutes: document.getElementById('iv-minutes'),
            seconds: document.getElementById('iv-seconds'),
        };

        const setUnit = (el, val) => {
            if (!el) {
                return;
            }
            const formatted = String(val).padStart(2, '0');
            if (el.textContent !== formatted) {
                el.classList.add('is-ticking');
                setTimeout(() => {
                    el.textContent = formatted;
                    el.classList.remove('is-ticking');
                }, 110);
            }
        };

        const updateCountdown = () => {
            if (!targetTs) {
                return;
            }
            const diff = targetTs - Date.now();
            if (diff <= 0) {
                countdownEl.hidden = true;
                if (elapsedEl) {
                    elapsedEl.hidden = false;
                }
                return;
            }
            const totalSec = Math.floor(diff / 1000);
            setUnit(els.days, Math.floor(totalSec / 86400));
            setUnit(els.hours, Math.floor((totalSec % 86400) / 3600));
            setUnit(els.minutes, Math.floor((totalSec % 3600) / 60));
            setUnit(els.seconds, totalSec % 60);
        };

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // Active indicator di bottom nav saat scroll
    if ('IntersectionObserver' in window) {
        const navItems = Array.from(document.querySelectorAll('.iv-nav-item[data-section]'));
        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        navItems.forEach((item) => {
                            item.classList.toggle('is-active', item.getAttribute('data-section') === id);
                        });
                    }
                });
            },
            { rootMargin: '-35% 0px -50% 0px', threshold: 0.01 }
        );

        ['section-couple', 'section-events', 'section-gallery', 'section-wishes'].forEach((id) => {
            const sec = document.getElementById(id);
            if (sec) {
                observer.observe(sec);
            }
        });
    }

    // Lazy Google Map
    document.querySelectorAll('[data-lazy-map]').forEach(function (card) {
        const btn = card.querySelector('[data-load-map-btn]');
        const container = card.querySelector('[data-map-container]');
        const src = card.getAttribute('data-map-src');
        if (btn && container && src) {
            btn.addEventListener('click', function () {
                const iframe = document.createElement('iframe');
                iframe.src = src;
                iframe.loading = 'lazy';
                iframe.title = 'Peta Lokasi Acara';
                container.innerHTML = '';
                container.appendChild(iframe);
            });
        }
    });

    // Video YouTube facade
    document.querySelectorAll('.iv-video-facade[data-video-id]').forEach(function (box) {
        const trigger = box.querySelector('.iv-video-trigger');
        const id = box.getAttribute('data-video-id');
        if (trigger && id) {
            trigger.addEventListener('click', function () {
                const iframe = document.createElement('iframe');
                iframe.src = 'https://www.youtube.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0';
                iframe.allow = 'autoplay; encrypted-media';
                iframe.allowFullscreen = true;
                iframe.title = 'Video Pernikahan';
                box.innerHTML = '';
                box.appendChild(iframe);
            });
        }
    });

    // Galeri Lightbox
    const lightbox = document.getElementById('iv-gallery-lightbox');
    if (lightbox) {
        const items = Array.from(document.querySelectorAll('[data-gallery-index]'));
        const lbImg = document.getElementById('iv-lightbox-img');
        const lbCaption = document.getElementById('iv-lightbox-caption');
        let currentIdx = 0;

        const showIndex = (idx) => {
            if (!items.length) {
                return;
            }
            currentIdx = (idx + items.length) % items.length;
            const img = items[currentIdx].querySelector('img');
            if (img && lbImg) {
                lbImg.src = img.getAttribute('data-full') || img.src;
                lbImg.alt = img.alt;
            }
            if (lbCaption) {
                lbCaption.textContent = img ? img.alt : '';
            }
            lightbox.hidden = false;
        };

        items.forEach((item, idx) => {
            item.addEventListener('click', () => showIndex(idx));
        });

        lightbox.querySelectorAll('[data-lightbox-close]').forEach((el) => {
            el.addEventListener('click', () => {
                lightbox.hidden = true;
            });
        });

        lightbox.querySelector('[data-lightbox-prev]')?.addEventListener('click', () => showIndex(currentIdx - 1));
        lightbox.querySelector('[data-lightbox-next]')?.addEventListener('click', () => showIndex(currentIdx + 1));

        document.addEventListener('keydown', (e) => {
            if (lightbox.hidden) {
                return;
            }
            if (e.key === 'Escape') {
                lightbox.hidden = true;
            } else if (e.key === 'ArrowLeft') {
                showIndex(currentIdx - 1);
            } else if (e.key === 'ArrowRight') {
                showIndex(currentIdx + 1);
            }
        });
    }

    // RSVP AJAX
    const rsvpForm = document.getElementById('iv-rsvp-form');
    if (rsvpForm) {
        const feedback = document.getElementById('iv-rsvp-feedback');
        rsvpForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = rsvpForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            try {
                const res = await fetch(rsvpForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(rsvpForm),
                });
                const json = await res.json().catch(() => ({}));
                if (feedback) {
                    feedback.hidden = false;
                    feedback.textContent = json.message || (res.ok ? 'Terima kasih, konfirmasi kehadiran tersimpan.' : 'Mohon periksa kembali isian Anda.');
                }
            } catch {
                if (feedback) {
                    feedback.hidden = false;
                    feedback.textContent = 'Terjadi gangguan jaringan. Silakan coba lagi.';
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        });
    }

    // Ucapan AJAX + Paginasi (5 per halaman)
    const wishesContainer = document.getElementById('iv-wishes-container');
    const wishesList = document.getElementById('iv-wishes-list');
    const paginationEl = document.getElementById('iv-wishes-pagination');
    let currentWishPage = 1;

    function renderWishPagination() {
        if (!wishesContainer || !wishesList || !paginationEl) {
            return;
        }
        const perPage = Number(wishesContainer.getAttribute('data-per-page') || 5);
        const cards = Array.from(wishesList.querySelectorAll('[data-wish-item]'));
        const totalPages = Math.max(1, Math.ceil(cards.length / perPage));
        if (currentWishPage > totalPages) {
            currentWishPage = totalPages;
        }
        const start = (currentWishPage - 1) * perPage;
        const end = start + perPage;
        cards.forEach((card, idx) => {
            card.hidden = idx < start || idx >= end;
        });

        paginationEl.hidden = totalPages <= 1;
        const info = document.getElementById('iv-wish-page-info');
        const prevBtn = document.getElementById('iv-wish-prev');
        const nextBtn = document.getElementById('iv-wish-next');
        if (info) {
            info.textContent = 'Halaman ' + currentWishPage + ' dari ' + totalPages;
        }
        if (prevBtn) {
            prevBtn.disabled = currentWishPage <= 1;
        }
        if (nextBtn) {
            nextBtn.disabled = currentWishPage >= totalPages;
        }
    }

    if (wishesContainer) {
        renderWishPagination();
        document.getElementById('iv-wish-prev')?.addEventListener('click', () => {
            if (currentWishPage > 1) {
                currentWishPage--;
                renderWishPagination();
            }
        });
        document.getElementById('iv-wish-next')?.addEventListener('click', () => {
            currentWishPage++;
            renderWishPagination();
        });
    }

    const wishesForm = document.getElementById('iv-wishes-form');
    if (wishesForm) {
        const feedback = document.getElementById('iv-wish-feedback');
        wishesForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = wishesForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            try {
                const fd = new FormData(wishesForm);
                const res = await fetch(wishesForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });
                const json = await res.json().catch(() => ({}));
                if (res.ok && wishesList) {
                    const card = document.createElement('article');
                    card.className = 'iv-card iv-wish-card';
                    card.setAttribute('data-wish-item', '');
                    const head = document.createElement('div');
                    head.className = 'iv-wish-head';
                    const left = document.createElement('div');
                    const strong = document.createElement('strong');
                    strong.className = 'iv-wish-author';
                    strong.textContent = String(fd.get('name') || '');
                    left.appendChild(strong);
                    const originVal = String(fd.get('origin') || '').trim();
                    if (originVal) {
                        const span = document.createElement('span');
                        span.className = 'iv-wish-origin';
                        span.textContent = ' · ' + originVal;
                        left.appendChild(span);
                    }
                    const timeEl = document.createElement('time');
                    timeEl.className = 'iv-wish-time';
                    timeEl.textContent = 'baru saja';
                    head.appendChild(left);
                    head.appendChild(timeEl);
                    const p = document.createElement('p');
                    p.className = 'iv-wish-body';
                    p.textContent = String(fd.get('message') || '');
                    card.appendChild(head);
                    card.appendChild(p);
                    wishesList.prepend(card);
                    currentWishPage = 1;
                    renderWishPagination();
                    wishesForm.reset();
                }
                if (feedback) {
                    feedback.hidden = false;
                    feedback.textContent = json.message || (res.ok ? 'Ucapan Anda telah ditambahkan.' : 'Mohon periksa kembali isian Anda.');
                }
            } catch {
                if (feedback) {
                    feedback.hidden = false;
                    feedback.textContent = 'Terjadi gangguan jaringan.';
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        });
    }

    // Gift Reveal & Copy
    document.querySelectorAll('[data-gift-panel]').forEach(function (panel) {
        const revealBtn = panel.querySelector('[data-gift-reveal-btn]');
        const endpoint = panel.getAttribute('data-gifts-endpoint');
        const labelCopy = panel.getAttribute('data-label-copy') || 'Salin';
        const labelCopied = panel.getAttribute('data-label-copied') || 'Tersalin';

        if (revealBtn && endpoint) {
            revealBtn.addEventListener('click', async function () {
                revealBtn.disabled = true;
                try {
                    const res = await fetch(endpoint, { headers: { Accept: 'application/json' } });
                    const data = await res.json();
                    (data.accounts || []).forEach(function (acc) {
                        const card = panel.querySelector('[data-gift-account-id="' + acc.id + '"]');
                        if (!card) {
                            return;
                        }
                        const numEl = card.querySelector('[data-gift-number]');
                        const copyBtn = card.querySelector('[data-copy-btn]');
                        if (numEl) {
                            numEl.textContent = acc.account_number;
                        }
                        if (copyBtn) {
                            copyBtn.disabled = false;
                            copyBtn.addEventListener('click', function () {
                                navigator.clipboard?.writeText(acc.account_number).then(() => {
                                    copyBtn.textContent = labelCopied;
                                    setTimeout(() => {
                                        copyBtn.textContent = labelCopy;
                                    }, 1800);
                                });
                            });
                        }
                    });
                    (data.addresses || []).forEach(function (addr) {
                        const card = panel.querySelector('[data-gift-address-id="' + addr.id + '"]');
                        const textEl = card?.querySelector('[data-gift-address-text]');
                        if (textEl) {
                            textEl.textContent = addr.address_text;
                        }
                    });
                    revealBtn.hidden = true;
                } catch {
                    revealBtn.disabled = false;
                }
            });
        }
    });

    // Modal Kredit
    const creditsModal = document.getElementById('iv-credits-modal');
    const creditsBtn = document.getElementById('iv-credits-btn');
    if (creditsModal && creditsBtn) {
        creditsBtn.addEventListener('click', function () {
            creditsModal.hidden = false;
        });
        creditsModal.querySelectorAll('[data-credits-close]').forEach(function (el) {
            el.addEventListener('click', function () {
                creditsModal.hidden = true;
            });
        });
        document.addEventListener('keydown', function (e) {
            if (!creditsModal.hidden && e.key === 'Escape') {
                creditsModal.hidden = true;
            }
        });
    }

    // Sinkronisasi pratinjau langsung dari panel admin
    window.addEventListener('message', function (event) {
        const data = event.data;
        if (!data || data.type !== 'theme-preview-update' || !data.config) {
            return;
        }

        const config = data.config;
        if (config.preset) {
            root.setAttribute('data-preset', String(config.preset));
        }

        if (config.relief) {
            if (config.relief.depth !== undefined) {
                const depth = Math.max(0, Math.min(1.6, Number(config.relief.depth) || 1));
                root.style.setProperty('--iv-relief-depth', String(depth));
                root.setAttribute('data-relief-depth', String(depth));
            }
            if (config.relief.ornamentDensity) {
                root.setAttribute('data-ornament-density', String(config.relief.ornamentDensity));
            }
        }

        if (config.sections && typeof config.sections === 'object') {
            Object.entries(config.sections).forEach(function ([key, enabled]) {
                document.querySelectorAll('[data-preview-section="' + key + '"]').forEach(function (el) {
                    el.hidden = !enabled;
                });
            });
        }
    });
})();
