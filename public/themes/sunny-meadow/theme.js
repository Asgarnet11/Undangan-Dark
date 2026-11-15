/**
 * Sunny Meadow Theme Scripts
 */
document.addEventListener('DOMContentLoaded', () => {
    const bgm = document.getElementById('sm-bgm');
    const musicToggle = document.getElementById('sm-music-toggle');
    const openBtn = document.getElementById('sm-open-invitation');
    const cover = document.getElementById('sm-cover');
    const confetti = document.getElementById('sm-confetti-container');
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
});
