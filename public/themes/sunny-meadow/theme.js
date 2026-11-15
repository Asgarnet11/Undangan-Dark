/**
 * Sunny Meadow Theme Scripts
 */
document.addEventListener('DOMContentLoaded', () => {
    const bgm = document.getElementById('sm-bgm');
    const musicToggle = document.getElementById('sm-music-toggle');
    const openBtn = document.getElementById('sm-open-invitation');
    const cover = document.getElementById('sm-cover');
    const confetti = document.getElementById('sm-confetti-container');
    const bottomNav = document.getElementById('sm-bottom-nav');
    const giftFabEl = document.getElementById('sm-gift-fab');
    const giftModalEl = document.getElementById('sm-gift-modal');
    const body = document.body;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const motionConfetti = body.dataset.motionConfetti !== 'false' && !prefersReducedMotion;
    const motionDecor = body.dataset.motionDecor !== 'false' && !prefersReducedMotion;

    if (!motionDecor) {
        document.querySelectorAll('.sm-anim-bobbing').forEach(el => el.classList.remove('sm-anim-bobbing'));
    }

    function unlockInvitation() {
        if (cover) {
            cover.classList.add('is-opening');
        }

        if (confetti && motionConfetti) {
            confetti.classList.add('is-bursting');
            setTimeout(() => {
                confetti.classList.remove('is-bursting');
            }, 1200);
        }

        setTimeout(() => {
            body.classList.remove('is-locked');
            if (musicToggle) {
                musicToggle.hidden = false;
            }
            if (bottomNav) {
                bottomNav.hidden = false;
            }
            if (giftFabEl && body.dataset.giftInline === 'false') {
                giftFabEl.hidden = false;
            }
            hydrateGifts();
            if (bgm && bgm.paused) {
                bgm.play().then(() => {
                    if (musicToggle) {
                        musicToggle.classList.add('is-playing');
                        musicToggle.setAttribute('aria-pressed', 'true');
                    }
                }).catch(() => {
                    // Autoplay policy prevented playback
                });
            }
            const main = document.getElementById('sm-main');
            if (main) {
                main.scrollIntoView({ behavior: 'smooth' });
            }
        }, prefersReducedMotion ? 50 : 400);
    }

    if (openBtn) {
        openBtn.addEventListener('click', (e) => {
            e.preventDefault();
            unlockInvitation();
        });
    }

    if (musicToggle && bgm) {
        musicToggle.addEventListener('click', () => {
            if (bgm.paused) {
                bgm.play().then(() => {
                    musicToggle.classList.add('is-playing');
                    musicToggle.setAttribute('aria-pressed', 'true');
                });
            } else {
                bgm.pause();
                musicToggle.classList.remove('is-playing');
                musicToggle.setAttribute('aria-pressed', 'false');
            }
        });
    }

    /* Countdown */
    const countdownEl = document.getElementById('sm-countdown');
    if (countdownEl && countdownEl.dataset.target) {
        const targetDate = new Date(countdownEl.dataset.target).getTime();
        const daysEl = countdownEl.querySelector('[data-unit="days"]');
        const hoursEl = countdownEl.querySelector('[data-unit="hours"]');
        const minutesEl = countdownEl.querySelector('[data-unit="minutes"]');
        const secondsEl = countdownEl.querySelector('[data-unit="seconds"]');
        const elapsedEl = document.getElementById('sm-countdown-elapsed');
        const gridEl = document.getElementById('sm-countdown-grid');

        let lastSec = '';
        let lastMin = '';
        let lastHour = '';
        let lastDay = '';

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance <= 0) {
                if (gridEl) gridEl.hidden = true;
                if (elapsedEl) {
                    elapsedEl.hidden = false;
                    elapsedEl.textContent = countdownEl.dataset.elapsedText || 'Acara telah berlangsung';
                }
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const dStr = String(days).padStart(2, '0');
            const hStr = String(hours).padStart(2, '0');
            const mStr = String(minutes).padStart(2, '0');
            const sStr = String(seconds).padStart(2, '0');

            if (daysEl && daysEl.textContent !== dStr) {
                daysEl.textContent = dStr;
                if (lastDay !== '') animateBox(daysEl);
                lastDay = dStr;
            }
            if (hoursEl && hoursEl.textContent !== hStr) {
                hoursEl.textContent = hStr;
                if (lastHour !== '') animateBox(hoursEl);
                lastHour = hStr;
            }
            if (minutesEl && minutesEl.textContent !== mStr) {
                minutesEl.textContent = mStr;
                if (lastMin !== '') animateBox(minutesEl);
                lastMin = mStr;
            }
            if (secondsEl && secondsEl.textContent !== sStr) {
                secondsEl.textContent = sStr;
                if (lastSec !== '') animateBox(secondsEl);
                lastSec = sStr;
            }
        }

        function animateBox(numEl) {
            if (prefersReducedMotion) return;
            const box = numEl.closest('.sm-countdown-box');
            if (box) {
                box.classList.remove('is-updating');
                void box.offsetWidth;
                box.classList.add('is-updating');
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    /* Gallery Lightbox */
    const lightbox = document.getElementById('sm-lightbox');
    const galleryItems = Array.from(document.querySelectorAll('.sm-gallery-item'));
    if (lightbox && galleryItems.length > 0) {
        const lbImg = document.getElementById('sm-lightbox-img');
        const lbCaption = document.getElementById('sm-lightbox-caption');
        const lbCounter = document.getElementById('sm-lightbox-counter');
        const lbClose = document.getElementById('sm-lightbox-close');
        const lbPrev = document.getElementById('sm-lightbox-prev');
        const lbNext = document.getElementById('sm-lightbox-next');
        let currentIndex = 0;

        function showLightbox(index) {
            if (index < 0) index = galleryItems.length - 1;
            if (index >= galleryItems.length) index = 0;
            currentIndex = index;

            const item = galleryItems[currentIndex];
            if (!item) return;

            const fullUrl = item.dataset.full;
            const caption = item.dataset.caption || '';

            if (lbImg) {
                lbImg.src = fullUrl;
                lbImg.alt = caption;
            }
            if (lbCaption) {
                lbCaption.textContent = caption;
            }
            if (lbCounter) {
                lbCounter.textContent = `${currentIndex + 1} / ${galleryItems.length}`;
            }

            lightbox.hidden = false;
            document.body.style.overflow = 'hidden';
        }

        function hideLightbox() {
            lightbox.hidden = true;
            document.body.style.overflow = '';
        }

        galleryItems.forEach((btn, idx) => {
            btn.addEventListener('click', () => showLightbox(idx));
        });

        if (lbClose) lbClose.addEventListener('click', hideLightbox);
        const backdrop = lightbox.querySelector('[data-lightbox-close]');
        if (backdrop) backdrop.addEventListener('click', hideLightbox);
        if (lbPrev) lbPrev.addEventListener('click', () => showLightbox(currentIndex - 1));
        if (lbNext) lbNext.addEventListener('click', () => showLightbox(currentIndex + 1));

        document.addEventListener('keydown', (e) => {
            if (!lightbox.hidden) {
                if (e.key === 'Escape') hideLightbox();
                if (e.key === 'ArrowLeft') showLightbox(currentIndex - 1);
                if (e.key === 'ArrowRight') showLightbox(currentIndex + 1);
            }
        });

        // Touch swipe support for lightbox
        let touchStartX = 0;
        let touchEndX = 0;
        lightbox.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        lightbox.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                showLightbox(currentIndex + 1);
            } else if (touchEndX - touchStartX > 50) {
                showLightbox(currentIndex - 1);
            }
        }, { passive: true });
    }

    /* Gift Section & Copy */
    const panels = Array.from(document.querySelectorAll('[data-gift-panel]'));
    let giftsHydrated = false;

    async function hydrateGifts() {
        if (giftsHydrated || panels.length === 0) return;
        const endpoint = panels[0].dataset.giftsEndpoint;
        if (!endpoint) return;

        try {
            const res = await fetch(endpoint, {
                headers: { Accept: 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const accountMap = new Map((data.accounts || []).map(a => [String(a.id), a]));
            const addressMap = new Map((data.addresses || []).map(a => [String(a.id), a]));

            panels.forEach(panel => {
                panel.querySelectorAll('[data-gift-account-id]').forEach(card => {
                    const acc = accountMap.get(card.dataset.giftAccountId);
                    if (!acc) return;
                    const numEl = card.querySelector('[data-gift-number]');
                    const copyBtn = card.querySelector('[data-copy-btn]');
                    if (numEl) numEl.textContent = acc.account_number;
                    if (copyBtn) {
                        copyBtn.dataset.copyText = acc.account_number;
                        copyBtn.disabled = false;
                    }
                });

                panel.querySelectorAll('[data-gift-address-id]').forEach(card => {
                    const addr = addressMap.get(card.dataset.giftAddressId);
                    const textEl = card.querySelector('[data-gift-address-text]');
                    if (addr && textEl) textEl.textContent = addr.address;
                });

                const revealBtn = panel.querySelector('[data-gift-reveal-btn]');
                if (revealBtn) revealBtn.hidden = true;
            });

            giftsHydrated = true;
        } catch {
            // allow user retry
        }
    }

    panels.forEach(panel => {
        const revealBtn = panel.querySelector('[data-gift-reveal-btn]');
        if (revealBtn) {
            revealBtn.addEventListener('click', hydrateGifts);
        }

        const copyLabel = panel.dataset.labelCopy || 'Salin';
        const copiedLabel = panel.dataset.labelCopied || 'Tersalin';
        const failedLabel = panel.dataset.labelCopyFailed || 'Gagal menyalin otomatis.';

        panel.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-copy-btn]');
            if (!btn || btn.disabled) return;
            const text = btn.dataset.copyText;
            if (!text) return;

            try {
                await navigator.clipboard.writeText(text);
                btn.textContent = copiedLabel;
                setTimeout(() => {
                    btn.textContent = copyLabel;
                }, 2000);
            } catch {
                alert(failedLabel);
            }
        });
    });

    /* Gift Modal */
    if (giftFabEl && giftModalEl) {
        const closeBtn = document.getElementById('sm-gift-modal-close');
        function openGiftModal() {
            hydrateGifts();
            giftModalEl.hidden = false;
            document.body.style.overflow = 'hidden';
            if (closeBtn) closeBtn.focus();
        }

        function closeGiftModal() {
            giftModalEl.hidden = true;
            document.body.style.overflow = '';
            giftFabEl.focus();
        }

        giftFabEl.addEventListener('click', openGiftModal);
        if (closeBtn) closeBtn.addEventListener('click', closeGiftModal);
        giftModalEl.querySelectorAll('[data-gift-modal-close]').forEach(el => {
            el.addEventListener('click', closeGiftModal);
        });

        document.addEventListener('keydown', (e) => {
            if (!giftModalEl.hidden && e.key === 'Escape') {
                closeGiftModal();
            }
        });
    }

    /* RSVP Form Pax Toggle */
    const rsvpForm = document.getElementById('sm-rsvp-form');
    if (rsvpForm) {
        const yesRadio = document.getElementById('sm-rsvp-yes');
        const noRadio = document.getElementById('sm-rsvp-no');
        const paxWrap = document.getElementById('sm-rsvp-pax-wrap');
        const paxInput = document.getElementById('sm-rsvp-pax');

        function togglePax() {
            if (noRadio && noRadio.checked) {
                if (paxWrap) paxWrap.hidden = true;
                if (paxInput) paxInput.disabled = true;
            } else {
                if (paxWrap) paxWrap.hidden = false;
                if (paxInput) paxInput.disabled = false;
            }
        }

        if (yesRadio) yesRadio.addEventListener('change', togglePax);
        if (noRadio) noRadio.addEventListener('change', togglePax);
    }

    /* Wishes Char Count & Load More */
    const wishMsg = document.getElementById('sm-wish-message');
    const wishCount = document.getElementById('sm-wish-char-count');
    if (wishMsg && wishCount) {
        wishMsg.addEventListener('input', () => {
            wishCount.textContent = `${wishMsg.value.length} / 500`;
        });
    }

    const loadMoreBtn = document.getElementById('sm-wishes-load-more');
    const wishesList = document.getElementById('sm-wishes-list');
    if (loadMoreBtn && wishesList) {
        loadMoreBtn.addEventListener('click', async () => {
            const endpoint = loadMoreBtn.dataset.endpoint;
            const nextPage = loadMoreBtn.dataset.nextPage;
            if (!endpoint || !nextPage) return;

            const defaultLabel = loadMoreBtn.dataset.labelDefault || 'Muat Lebih Banyak';
            const loadingLabel = loadMoreBtn.dataset.labelLoading || 'Memuat ucapan...';

            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = loadingLabel;

            try {
                const res = await fetch(`${endpoint}?page=${nextPage}`, {
                    headers: { Accept: 'application/json' }
                });
                if (!res.ok) throw new Error();
                const data = await res.json();

                const items = data.data || [];
                const accentClasses = ['sm-wish-card--sun', 'sm-wish-card--coral', 'sm-wish-card--mint', 'sm-wish-card--sky'];

                items.forEach((w, idx) => {
                    const card = document.createElement('article');
                    card.className = `sm-card sm-wish-card ${accentClasses[idx % accentClasses.length]}`;
                    card.innerHTML = `
                        <div class="sm-wish-header">
                            <strong class="sm-wish-name">${escapeHtml(w.name)}</strong>
                            ${w.origin ? `<span class="sm-wish-origin">${escapeHtml(w.origin)}</span>` : ''}
                            <time class="sm-wish-time">${escapeHtml(w.created_at_human || '')}</time>
                        </div>
                        <p class="sm-wish-message">${escapeHtml(w.message)}</p>
                    `;
                    wishesList.appendChild(card);
                });

                if (data.next_page_url) {
                    loadMoreBtn.dataset.nextPage = String(Number(nextPage) + 1);
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.textContent = defaultLabel;
                } else {
                    loadMoreBtn.parentElement.remove();
                }
            } catch {
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = defaultLabel;
            }
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /* Bottom Navigation Active Section Tracking */
    const navItems = document.querySelectorAll('.sm-nav-item');
    if (navItems.length > 0 && 'IntersectionObserver' in window) {
        const sections = Array.from(navItems)
            .map(item => document.getElementById(item.dataset.navTarget))
            .filter(Boolean);

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.id;
                    navItems.forEach(item => {
                        if (item.dataset.navTarget === id) {
                            item.classList.add('is-active');
                        } else {
                            item.classList.remove('is-active');
                        }
                    });
                }
            });
        }, {
            threshold: 0.25,
            rootMargin: '-10% 0px -40% 0px'
        });

        sections.forEach(sec => observer.observe(sec));
    }
});
