/**
 * Sunny Meadow Theme Scripts
 */
document.addEventListener('DOMContentLoaded', () => {
    const bgm = document.getElementById('sm-bgm');
    const musicToggle = document.getElementById('sm-music-toggle');
    const openBtn = document.getElementById('sm-open-invitation');
    const cover = document.getElementById('sm-cover');
    const body = document.body;

    function unlockInvitation() {
        body.classList.remove('is-locked');
        if (musicToggle) {
            musicToggle.hidden = false;
        }
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
});
