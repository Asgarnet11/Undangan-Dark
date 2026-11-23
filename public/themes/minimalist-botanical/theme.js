/**
 * Minimalist Botanical Theme Script
 * Zero external CDN dependencies.
 */
document.addEventListener('DOMContentLoaded', function () {
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const cover = document.getElementById('mb-cover');
    const openBtn = document.getElementById('mb-open-btn');
    const audio = document.getElementById('mb-bgm');
    const musicToggle = document.getElementById('mb-music-toggle');
    const bottomNav = document.getElementById('mb-bottom-nav');
    const invitationKey = 'mb_opened_' + window.location.pathname;

    let isPlaying = false;

    function openInvitation(smooth = true) {
        if (!cover) return;
        document.body.classList.remove('is-locked');
        cover.classList.add('is-opened');
        if (bottomNav) bottomNav.hidden = false;
        sessionStorage.setItem(invitationKey, '1');

        if (smooth) {
            window.scrollTo({ top: 0, behavior: 'instant' });
        }

        if (audio && !isPlaying) {
            audio.play().then(() => {
                isPlaying = true;
                if (musicToggle) musicToggle.classList.add('is-playing');
            }).catch(() => {
                // Autoplay blocked by browser policy
            });
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', function () {
            openInvitation(true);
        });
    }

    // Check if opened previously in session
    if (sessionStorage.getItem(invitationKey) === '1') {
        openInvitation(false);
        const mainSection = document.getElementById('mb-countdown') || document.getElementById('mb-verse') || document.getElementById('mb-couple');
        if (mainSection) {
            mainSection.scrollIntoView({ behavior: 'auto', block: 'start' });
        }
    }

    // Music toggle
    if (musicToggle && audio) {
        musicToggle.addEventListener('click', function () {
            if (audio.paused) {
                audio.play().then(() => {
                    isPlaying = true;
                    musicToggle.classList.add('is-playing');
                });
            } else {
                audio.pause();
                isPlaying = false;
                musicToggle.classList.remove('is-playing');
            }
        });
    }

    // Countdown Timer
    const countdownEl = document.getElementById('mb-countdown');
    if (countdownEl) {
        const targetTimestamp = parseInt(countdownEl.getAttribute('data-timestamp'), 10) * 1000;
        const daysEl = document.getElementById('mb-days');
        const hoursEl = document.getElementById('mb-hours');
        const minutesEl = document.getElementById('mb-minutes');
        const secondsEl = document.getElementById('mb-seconds');

        function updateCountdown() {
            const now = Date.now();
            const diff = targetTimestamp - now;

            if (diff <= 0) {
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minutesEl) minutesEl.textContent = '00';
                if (secondsEl) secondsEl.textContent = '00';
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            if (daysEl) daysEl.textContent = String(d).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(h).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(m).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(s).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // Scroll reveal observer
    const reveals = document.querySelectorAll('.mb-reveal');
    if ('IntersectionObserver' in window && reveals.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        reveals.forEach(el => observer.observe(el));
    } else {
        reveals.forEach(el => el.classList.add('is-visible'));
    }

    // Copy to clipboard buttons in gift panel
    document.querySelectorAll('[data-copy-number]').forEach(button => {
        button.addEventListener('click', function () {
            const num = this.getAttribute('data-copy-number');
            if (!num) return;
            navigator.clipboard.writeText(num).then(() => {
                const orig = this.textContent;
                this.textContent = 'Tersalin!';
                setTimeout(() => { this.textContent = orig; }, 2000);
            });
        });
    });

    // Reveal gift address
    const giftRevealBtn = document.querySelector('[data-gift-reveal-btn]');
    if (giftRevealBtn) {
        giftRevealBtn.addEventListener('click', function () {
            const panel = document.querySelector('[data-gift-panel]');
            const endpoint = panel ? panel.getAttribute('data-gifts-endpoint') : null;
            if (!endpoint) return;

            giftRevealBtn.disabled = true;
            giftRevealBtn.textContent = 'Memuat...';

            fetch(endpoint, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    if (data.accounts) {
                        data.accounts.forEach(acc => {
                            const numEl = document.querySelector(`[data-gift-account-id="${acc.id}"] [data-gift-number]`);
                            const copyBtn = document.querySelector(`[data-gift-account-id="${acc.id}"] [data-copy-btn]`);
                            if (numEl) numEl.textContent = acc.account_number;
                            if (copyBtn) {
                                copyBtn.disabled = false;
                                copyBtn.setAttribute('data-copy-number', acc.account_number);
                            }
                        });
                    }
                    if (data.addresses) {
                        data.addresses.forEach(addr => {
                            const textEl = document.querySelector(`[data-gift-address-id="${addr.id}"] [data-gift-address-text]`);
                            if (textEl) textEl.textContent = addr.address;
                        });
                    }
                    giftRevealBtn.style.display = 'none';
                })
                .catch(() => {
                    giftRevealBtn.disabled = false;
                    giftRevealBtn.textContent = 'Gagal memuat. Coba lagi.';
                });
        });
    }

    // Active bottom navigation spy
    const navItems = document.querySelectorAll('.mb-nav-item');
    const navSections = Array.from(navItems).map(item => {
        const targetId = item.getAttribute('href')?.replace('#', '');
        return targetId ? document.getElementById(targetId) : null;
    }).filter(Boolean);

    if ('IntersectionObserver' in window && navSections.length > 0) {
        const navObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.id;
                    navItems.forEach(item => {
                        item.classList.toggle('is-active', item.getAttribute('href') === '#' + id);
                    });
                }
            });
        }, { rootMargin: '-20% 0px -70% 0px' });

        navSections.forEach(sec => navObserver.observe(sec));
    }
});
