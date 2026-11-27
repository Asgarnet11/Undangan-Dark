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

    const initCountdown = () => {
        const countdownEl = document.getElementById('bh-countdown');
        if (!countdownEl || !countdownEl.dataset.timestamp) {
            return;
        }

        const targetMs = parseInt(countdownEl.dataset.timestamp, 10) * 1000;
        if (!targetMs || isNaN(targetMs)) {
            return;
        }

        const elapsedEl = document.getElementById('bh-countdown-elapsed');
        const daysEl = document.getElementById('bh-days');
        const hoursEl = document.getElementById('bh-hours');
        const minutesEl = document.getElementById('bh-minutes');
        const secondsEl = document.getElementById('bh-seconds');

        const pad2 = (val) => String(Math.max(0, Math.floor(val)));

        const updateTimer = () => {
            const diff = targetMs - Date.now();
            if (diff <= 0) {
                countdownEl.hidden = true;
                if (elapsedEl) {
                    elapsedEl.hidden = false;
                }
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            if (daysEl) daysEl.textContent = pad2(days);
            if (hoursEl) hoursEl.textContent = pad2(hours);
            if (minutesEl) minutesEl.textContent = pad2(minutes);
            if (secondsEl) secondsEl.textContent = pad2(seconds);
        };

        updateTimer();
        setInterval(updateTimer, 1000);
    };

    const initGalleryLightbox = () => {
        const lightbox = document.getElementById('bh-gallery-lightbox');
        if (!lightbox) {
            return;
        }

        const targetImg = document.getElementById('bh-lightbox-target');
        const captionEl = document.getElementById('bh-lightbox-caption');
        const closeBtn = lightbox.querySelector('.bh-lightbox-close');
        const backdrop = lightbox.querySelector('.bh-lightbox-backdrop');

        const openLightbox = (src, caption) => {
            if (targetImg) targetImg.src = src;
            if (captionEl) captionEl.textContent = caption || '';
            lightbox.hidden = false;
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        };

        const closeLightbox = () => {
            lightbox.hidden = true;
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            if (targetImg) targetImg.src = '';
            document.body.style.overflow = '';
        };

        document.querySelectorAll('.bh-gallery-item').forEach((item) => {
            item.addEventListener('click', () => {
                const img = item.querySelector('img');
                if (img) {
                    const fullSrc = img.dataset.full || img.src;
                    const caption = item.querySelector('.bh-gallery-caption')?.textContent || img.alt || '';
                    openLightbox(fullSrc, caption);
                }
            });
        });

        if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
        if (backdrop) backdrop.addEventListener('click', closeLightbox);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !lightbox.hidden) {
                closeLightbox();
            }
        });
    };

    const initBottomNav = () => {
        if (!bottomNavEl) {
            return;
        }

        const navItems = bottomNavEl.querySelectorAll('.bh-nav-item');
        const sectionMap = new Map();

        navItems.forEach((item) => {
            const sectionId = item.dataset.section || item.getAttribute('href')?.replace('#', '');
            if (sectionId) {
                const target = document.getElementById(sectionId);
                if (target) {
                    sectionMap.set(target, item);
                }
            }
        });

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        navItems.forEach((n) => n.classList.remove('is-active'));
                        const matchedItem = sectionMap.get(entry.target);
                        if (matchedItem) {
                            matchedItem.classList.add('is-active');
                        }
                    }
                });
            }, { threshold: 0.35 });

            sectionMap.forEach((_, target) => {
                observer.observe(target);
            });
        }
        window.addEventListener('scroll', () => {
            if (bottomNavEl && root.classList.contains('is-opened')) {
                const coverBottom = coverEl ? coverEl.getBoundingClientRect().bottom : 0;
                bottomNavEl.style.transform = coverBottom > 80 ? 'translateX(-50%) translateY(120%)' : 'translateX(-50%) translateY(0)';
            }
        }, { passive: true });
    };

    const initGlossaryModal = () => {
        const modal = document.getElementById('bh-glossary-modal');
        if (!modal) {
            return;
        }

        const trigger = document.getElementById('bh-glossary-btn');
        const closeBtn = document.getElementById('bh-glossary-close');
        const backdrop = modal.querySelector('.bh-modal-backdrop');

        const openModal = () => {
            modal.hidden = false;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            if (closeBtn) closeBtn.focus();
        };

        const closeModal = () => {
            modal.hidden = true;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (trigger) trigger.focus();
        };

        if (trigger) trigger.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });
    };

    const initCreditsModal = () => {
        const modal = document.getElementById('bh-credits-modal');
        if (!modal) {
            return;
        }

        const trigger = document.getElementById('bh-credits-btn');
        const closeBtn = document.getElementById('bh-credits-close');
        const backdrop = modal.querySelector('.bh-modal-backdrop');

        const openModal = () => {
            modal.hidden = false;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            if (closeBtn) closeBtn.focus();
        };

        const closeModal = () => {
            modal.hidden = true;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (trigger) trigger.focus();
        };

        if (trigger) trigger.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });
    };

    const initGiftPanel = () => {
        const panel = document.querySelector('[data-gift-panel]');
        if (!panel) {
            return;
        }

        const endpoint = panel.getAttribute('data-gifts-endpoint');
        const revealBtn = panel.querySelector('[data-gift-reveal-btn]');
        const labelCopy = panel.getAttribute('data-label-copy') || 'Salin';
        const labelCopied = panel.getAttribute('data-label-copied') || 'Tersalin!';

        if (revealBtn && endpoint) {
            revealBtn.addEventListener('click', () => {
                revealBtn.disabled = true;
                revealBtn.textContent = 'Memuat...';

                fetch(endpoint, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.accounts) {
                            data.accounts.forEach((acc) => {
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
                            data.addresses.forEach((addr) => {
                                const textEl = document.querySelector(`[data-gift-address-id="${addr.id}"] [data-gift-address-text]`);
                                if (textEl) textEl.textContent = addr.address;
                            });
                        }
                        revealBtn.style.display = 'none';
                    })
                    .catch(() => {
                        revealBtn.disabled = false;
                        revealBtn.textContent = 'Gagal memuat. Coba lagi.';
                    });
            });
        }

        panel.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-copy-btn]');
            if (!btn) {
                return;
            }
            const num = btn.getAttribute('data-copy-number');
            if (num && navigator.clipboard) {
                navigator.clipboard.writeText(num).then(() => {
                    const originalText = btn.textContent;
                    btn.textContent = labelCopied;
                    setTimeout(() => {
                        btn.textContent = originalText;
                    }, 2000);
                }).catch(() => {});
            }
        });
    };

    let openedAnimationsStarted = false;

    const startOpenedAnimations = () => {
        if (openedAnimationsStarted || prefersReducedMotion || typeof window.gsap === 'undefined') {
            return;
        }
        openedAnimationsStarted = true;

        const gsap = window.gsap;
        const ScrollTrigger = window.ScrollTrigger;
        if (ScrollTrigger && typeof gsap.registerPlugin === 'function') {
            gsap.registerPlugin(ScrollTrigger);
        }

        // 1. Scroll reveal for titles and cards
        document.querySelectorAll('.bh-section').forEach((section) => {
            const titleEl = section.querySelector('.bh-section-title');
            if (titleEl && ScrollTrigger) {
                gsap.from(titleEl, {
                    y: 24,
                    opacity: 0,
                    duration: 0.85,
                    ease: 'power3.out',
                    clearProps: 'transform,opacity',
                    scrollTrigger: {
                        trigger: titleEl,
                        start: 'top 88%',
                        once: true,
                    },
                });
            }

            const items = section.querySelectorAll('.bh-card, .bh-story-step, .bh-gallery-item, .bh-countdown-box');
            if (items.length > 0 && ScrollTrigger) {
                gsap.from(items, {
                    y: 28,
                    opacity: 0,
                    duration: 0.85,
                    stagger: 0.1,
                    ease: 'power2.out',
                    clearProps: 'transform,opacity',
                    scrollTrigger: {
                        trigger: section,
                        start: 'top 86%',
                        once: true,
                    },
                });
            }
        });

        // 2. Photo frames entrance
        document.querySelectorAll('.bh-photo-frame').forEach((frame) => {
            if (ScrollTrigger) {
                gsap.from(frame, {
                    scale: 0.94,
                    opacity: 0,
                    duration: 1.0,
                    ease: 'power2.out',
                    clearProps: 'transform,opacity',
                    scrollTrigger: {
                        trigger: frame,
                        start: 'top 86%',
                        once: true,
                    },
                });
            }
        });

        // 3. Silhouette rumah panggung gentle rise
        const silhouette = document.querySelector('.bh-closing-silhouette');
        if (silhouette && ScrollTrigger) {
            gsap.from(silhouette, {
                y: 30,
                opacity: 0,
                duration: 1.1,
                ease: 'power2.out',
                clearProps: 'transform,opacity',
                scrollTrigger: {
                    trigger: silhouette,
                    start: 'top 90%',
                    once: true,
                },
            });
        }
    };

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

        startOpenedAnimations();
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
        initCountdown();
        initGalleryLightbox();
        initBottomNav();
        initGlossaryModal();
        initCreditsModal();
        initGiftPanel();

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

            startOpenedAnimations();
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
