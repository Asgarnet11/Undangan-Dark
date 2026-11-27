/**
 * Ivory Emboss Theme JS — Fase 1: Fondasi & Pratinjau Admin
 */
(function () {
    'use strict';

    const root = document.body;
    if (!root || !root.classList.contains('theme-ivory-emboss')) {
        return;
    }

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

    const openBtn = document.getElementById('iv-open-invitation');
    if (openBtn) {
        openBtn.addEventListener('click', function () {
            if (musicBtn) {
                musicBtn.hidden = false;
            }
            if (bgm && bgm.paused) {
                bgm.play().then(() => setMusicState(true)).catch(() => setMusicState(false));
            }
            const main = document.getElementById('iv-main');
            if (main) {
                main.scrollIntoView({ behavior: 'smooth' });
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
