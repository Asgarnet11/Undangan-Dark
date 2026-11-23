(() => {
    'use strict';

    const root = document.querySelector('.theme-bugis-heritage');
    if (!root) {
        return;
    }

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const OPEN_STORAGE_KEY = 'bugis_heritage_invitation_opened_' + (window.location.pathname.replace(/\/+$/, '') || 'home');
    let wasAlreadyOpened = false;
    try {
        wasAlreadyOpened = sessionStorage.getItem(OPEN_STORAGE_KEY) === '1';
    } catch (_) {
        wasAlreadyOpened = false;
    }

    if (wasAlreadyOpened) {
        root.classList.add('bh-js', 'is-opened');
    } else {
        root.classList.add('bh-js', 'is-locked');
        window.scrollTo(0, 0);
    }

    window.addEventListener('scroll', () => {
        if (!wasAlreadyOpened && root.classList.contains('is-locked') && window.scrollY > 40) {
            window.scrollTo(0, 0);
        }
    }, { passive: true });

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const openButton = document.getElementById('bh-open-invitation');
    const coverEl = document.getElementById('bh-cover');
    const mainEl = document.getElementById('bh-main');
    const audioEl = document.getElementById('bh-bgm');
    const musicButton = document.getElementById('bh-music-toggle');
    const bottomNavEl = document.getElementById('bh-bottom-nav');

    const MUTE_STORAGE_KEY = 'bugis_heritage_music_muted';

    const musicController = {
        playing: false,
        userMuted: sessionStorage.getItem(MUTE_STORAGE_KEY) === '1',

        syncButton() {
            if (!musicButton) {
                return;
            }
            const labelPlay = musicButton.dataset.labelPlay || 'Putar musik latar';
            const labelPause = musicButton.dataset.labelPause || 'Jeda musik latar';
            musicButton.setAttribute('aria-pressed', this.playing ? 'true' : 'false');
            musicButton.setAttribute('aria-label', this.playing ? labelPause : labelPlay);
        },

        revealButton() {
            if (musicButton) {
                musicButton.hidden = false;
                this.syncButton();
            }
        },

        play() {
            if (!audioEl || !audioEl.getAttribute('src')) {
                return;
            }
            audioEl.volume = 0.55;
            const promise = audioEl.play();
            if (promise && typeof promise.then === 'function') {
                promise
                    .then(() => {
                        this.playing = true;
                        this.syncButton();
                    })
                    .catch(() => {
                        this.playing = false;
                        this.syncButton();
                    });
            } else {
                this.playing = true;
                this.syncButton();
            }
        },

        pause(temporary = false) {
            if (!audioEl) {
                return;
            }
            audioEl.pause();
            if (!temporary) {
                this.playing = false;
                this.syncButton();
            }
        },

        startAfterOpen() {
            this.revealButton();
            if (!this.userMuted) {
                this.play();
            }
        },

        toggle() {
            if (this.playing) {
                this.userMuted = true;
                sessionStorage.setItem(MUTE_STORAGE_KEY, '1');
                this.pause(false);
            } else {
                this.userMuted = false;
                sessionStorage.setItem(MUTE_STORAGE_KEY, '0');
                this.play();
            }
        },
    };

    if (musicButton) {
        musicButton.addEventListener('click', () => musicController.toggle());
    }

    document.addEventListener('visibilitychange', () => {
        if (!window.BugisHeritageTheme?.opened) {
            return;
        }
        if (document.hidden) {
            if (musicController.playing) {
                musicController.pause(true);
            }
        } else if (!musicController.userMuted && musicController.playing) {
            musicController.play();
        }
    });

    const finishOpening = () => {
        try {
            sessionStorage.setItem(OPEN_STORAGE_KEY, '1');
        } catch (_) {}

        window.BugisHeritageTheme.opened = true;
        root.classList.remove('is-locked');
        root.classList.add('is-opened');

        if (coverEl) {
            coverEl.classList.add('is-opened');
            coverEl.setAttribute('data-opened', 'true');
        }

        if (bottomNavEl) {
            bottomNavEl.hidden = false;
        }

        musicController.startAfterOpen();

        if (mainEl) {
            mainEl.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
        }

        if (typeof window.ScrollTrigger !== 'undefined') {
            window.ScrollTrigger.refresh();
        }

        document.dispatchEvent(new CustomEvent('bugis-heritage:opened'));
    };

    window.BugisHeritageTheme = {
        opened: wasAlreadyOpened,
        musicController,
        openInvitation() {
            finishOpening();
        },
    };

    if (openButton) {
        openButton.addEventListener('click', () => {
            window.BugisHeritageTheme.openInvitation();
        });
    }

    const bootstrap = () => {
        if (wasAlreadyOpened) {
            window.BugisHeritageTheme.opened = true;
            root.classList.remove('is-locked');
            root.classList.add('is-opened');

            if (coverEl) {
                coverEl.classList.add('is-opened');
                coverEl.setAttribute('data-opened', 'true');
            }

            if (bottomNavEl) {
                bottomNavEl.hidden = false;
            }

            musicController.revealButton();
            if (mainEl) {
                mainEl.scrollIntoView({ behavior: 'auto', block: 'start' });
            }
            if (typeof window.ScrollTrigger !== 'undefined') {
                window.ScrollTrigger.refresh();
            }
            document.dispatchEvent(new CustomEvent('bugis-heritage:opened'));
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
})();
