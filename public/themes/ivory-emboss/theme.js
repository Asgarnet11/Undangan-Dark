/**
 * Ivory Emboss Theme JS — Fase 3: Cover Opening, Countdown, Bottom Nav, Music & Preview
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

    // Jika preview mode, buka navigasi
    if (root.getAttribute('data-preview-mode') === 'true' && bottomNav) {
        bottomNav.hidden = false;
    }

    // Kembali ke sampul dari tombol tengah navigasi bawah
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
