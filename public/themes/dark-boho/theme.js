(() => {
    'use strict';

    const root = document.querySelector('.theme-dark-boho');
    if (!root) {
        return;
    }

    root.classList.add('db-js', 'is-locked');

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const openButton = document.getElementById('db-open-invitation');
    const coverEl = document.getElementById('db-cover');
    const mainEl = document.getElementById('db-main');
    const audioEl = document.getElementById('db-bgm');
    const musicButton = document.getElementById('db-music-toggle');

    const MUTE_STORAGE_KEY = 'dark_boho_music_muted';

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
        if (!window.DarkBohoTheme?.opened) {
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

    // Animasi masuk ringan pada cover sebelum dibuka
    const runCoverEntrance = () => {
        if (prefersReducedMotion || typeof window.gsap === 'undefined') {
            return;
        }
        const gsap = window.gsap;
        gsap.from(
            ['.db-cover-celestial', '.db-cover-monogram', '.db-cover-subtitle', '.db-cover-names', '.db-cover-date', '.db-cover-guest', '.db-cover-open'],
            {
                opacity: 0,
                y: 22,
                duration: 0.9,
                stagger: 0.09,
                ease: 'power3.out',
                clearProps: 'transform,opacity',
            }
        );
    };

    let openedAnimationsStarted = false;

    const finishOpening = () => {
        root.classList.remove('is-locked');
        root.classList.add('is-opened');

        if (coverEl) {
            coverEl.classList.add('is-opened');
            coverEl.setAttribute('data-opened', 'true');
        }

        if (bottomNavEl) {
            bottomNavEl.hidden = false;
        }

        if (giftFabEl) {
            giftFabEl.hidden = false;
        }

        startOpenedAnimations();

        if (mainEl) {
            mainEl.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
        }

        if (typeof window.ScrollTrigger !== 'undefined') {
            window.ScrollTrigger.refresh();
        }

        document.dispatchEvent(new CustomEvent('dark-boho:opened'));

        // Pulihkan teks cover secara halus bila pengunjung menggulir kembali ke atas
        if (!prefersReducedMotion && typeof window.gsap !== 'undefined') {
            window.gsap.set('.db-cover-inner', { clearProps: 'transform,opacity,willChange' });
        }
    };

    const bottomNavEl = document.getElementById('db-bottom-nav');
    const giftFabEl = document.getElementById('db-gift-fab');

    const initCountdown = () => {
        const countdownEl = document.getElementById('db-countdown');
        if (!countdownEl || !countdownEl.dataset.target) {
            return;
        }

        const targetMs = Date.parse(countdownEl.dataset.target);
        if (Number.isNaN(targetMs)) {
            return;
        }

        const gridEl = document.getElementById('db-countdown-grid');
        const elapsedEl = document.getElementById('db-countdown-elapsed');
        const units = {
            days: countdownEl.querySelector('[data-unit="days"]'),
            hours: countdownEl.querySelector('[data-unit="hours"]'),
            minutes: countdownEl.querySelector('[data-unit="minutes"]'),
            seconds: countdownEl.querySelector('[data-unit="seconds"]'),
        };

        const pad2 = (value) => String(Math.max(0, value)).padStart(2, '0');
        let timerId = null;

        const update = () => {
            const diff = targetMs - Date.now();
            if (diff <= 0) {
                if (gridEl) {
                    gridEl.hidden = true;
                }
                if (elapsedEl) {
                    elapsedEl.hidden = false;
                }
                if (timerId !== null) {
                    window.clearInterval(timerId);
                    timerId = null;
                }
                return;
            }

            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            const nextValues = {
                days: pad2(days),
                hours: pad2(hours),
                minutes: pad2(minutes),
                seconds: pad2(seconds),
            };

            Object.keys(nextValues).forEach((key) => {
                const node = units[key];
                if (!node || node.textContent === nextValues[key]) {
                    return;
                }
                node.textContent = nextValues[key];
                if (!prefersReducedMotion && typeof window.gsap !== 'undefined') {
                    window.gsap.fromTo(
                        node,
                        { y: -8, opacity: 0.45, rotateX: 35, transformPerspective: 320 },
                        { y: 0, opacity: 1, rotateX: 0, duration: 0.32, ease: 'power2.out', clearProps: 'transform,opacity' },
                    );
                }
            });
        };

        update();
        if (targetMs > Date.now()) {
            timerId = window.setInterval(update, 1000);
        }
    };

    const initVideoFacade = () => {
        const facades = document.querySelectorAll('.db-video-facade[data-youtube-id]');
        facades.forEach((facade) => {
            const triggerLoad = () => {
                const videoId = (facade.dataset.youtubeId || '').trim();
                if (!/^[A-Za-z0-9_-]{6,20}$/.test(videoId) || facade.dataset.loaded === '1') {
                    return;
                }
                facade.dataset.loaded = '1';

                const iframe = document.createElement('iframe');
                iframe.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(videoId)}?autoplay=1&rel=0`;
                iframe.title = 'Video Prewedding';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                iframe.loading = 'lazy';

                facade.innerHTML = '';
                facade.appendChild(iframe);
            };

            facade.addEventListener('click', triggerLoad);
        });
    };

    const initGalleryLightbox = () => {
        const gridEl = document.getElementById('db-gallery-grid');
        const lightboxEl = document.getElementById('db-lightbox');
        if (!gridEl || !lightboxEl) {
            return;
        }

        const items = Array.from(gridEl.querySelectorAll('.db-gallery-item'));
        if (items.length === 0) {
            return;
        }

        const imgEl = document.getElementById('db-lightbox-img');
        const captionEl = document.getElementById('db-lightbox-caption');
        const counterEl = document.getElementById('db-lightbox-counter');
        const closeBtn = document.getElementById('db-lightbox-close');
        const prevBtn = document.getElementById('db-lightbox-prev');
        const nextBtn = document.getElementById('db-lightbox-next');
        const backdropEl = lightboxEl.querySelector('.db-lightbox-backdrop');
        const stageEl = lightboxEl.querySelector('.db-lightbox-stage');

        let currentIndex = 0;
        let lastTriggerEl = null;
        let touchStartX = null;

        const renderSlide = (index) => {
            const normalized = ((index % items.length) + items.length) % items.length;
            currentIndex = normalized;

            const item = items[normalized];
            const fullSrc = item.dataset.full || '';
            const caption = item.dataset.caption || '';
            const thumbImg = item.querySelector('img');

            if (imgEl) {
                imgEl.src = fullSrc;
                imgEl.alt = caption || (thumbImg ? thumbImg.alt : '');
            }
            if (captionEl) {
                captionEl.textContent = caption;
                captionEl.hidden = caption.length === 0;
            }
            if (counterEl) {
                counterEl.textContent = `${normalized + 1} / ${items.length}`;
            }

            const multi = items.length > 1;
            if (prevBtn) {
                prevBtn.hidden = !multi;
            }
            if (nextBtn) {
                nextBtn.hidden = !multi;
            }
        };

        const openLightbox = (index, trigger) => {
            lastTriggerEl = trigger || items[index] || null;
            renderSlide(index);
            lightboxEl.hidden = false;
            document.documentElement.style.overflow = 'hidden';

            if (!prefersReducedMotion && typeof window.gsap !== 'undefined' && imgEl) {
                window.gsap.fromTo(imgEl, { opacity: 0, scale: 0.96 }, { opacity: 1, scale: 1, duration: 0.25, ease: 'power2.out' });
            }

            if (closeBtn) {
                closeBtn.focus();
            }
        };

        const closeLightbox = () => {
            if (lightboxEl.hidden) {
                return;
            }
            lightboxEl.hidden = true;
            document.documentElement.style.overflow = '';
            if (imgEl) {
                imgEl.removeAttribute('src');
            }
            if (lastTriggerEl && typeof lastTriggerEl.focus === 'function') {
                lastTriggerEl.focus();
            }
        };

        items.forEach((btn, idx) => {
            btn.addEventListener('click', () => openLightbox(idx, btn));
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', closeLightbox);
        }
        if (backdropEl) {
            backdropEl.addEventListener('click', closeLightbox);
        }
        if (prevBtn) {
            prevBtn.addEventListener('click', () => renderSlide(currentIndex - 1));
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => renderSlide(currentIndex + 1));
        }

        if (stageEl) {
            stageEl.addEventListener('touchstart', (e) => {
                if (e.changedTouches && e.changedTouches[0]) {
                    touchStartX = e.changedTouches[0].clientX;
                }
            }, { passive: true });

            stageEl.addEventListener('touchend', (e) => {
                if (touchStartX === null || !e.changedTouches || !e.changedTouches[0]) {
                    return;
                }
                const deltaX = e.changedTouches[0].clientX - touchStartX;
                touchStartX = null;
                if (Math.abs(deltaX) > 40) {
                    renderSlide(deltaX > 0 ? currentIndex - 1 : currentIndex + 1);
                }
            }, { passive: true });
        }

        document.addEventListener('keydown', (e) => {
            if (lightboxEl.hidden) {
                return;
            }

            if (e.key === 'Escape') {
                e.preventDefault();
                closeLightbox();
                return;
            }

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                renderSlide(currentIndex - 1);
                return;
            }

            if (e.key === 'ArrowRight') {
                e.preventDefault();
                renderSlide(currentIndex + 1);
                return;
            }

            if (e.key === 'Tab') {
                const focusables = Array.from(
                    lightboxEl.querySelectorAll('button:not([hidden]):not([disabled])'),
                );
                if (focusables.length === 0) {
                    return;
                }
                const first = focusables[0];
                const last = focusables[focusables.length - 1];

                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });
    };

    const initBottomNav = () => {
        if (!bottomNavEl) {
            return;
        }

        const indicatorEl = document.getElementById('db-nav-indicator');
        const navLinks = Array.from(bottomNavEl.querySelectorAll('[data-nav-target]'));

        const moveIndicatorTo = (linkEl) => {
            if (!indicatorEl) {
                return;
            }
            if (!linkEl || linkEl.classList.contains('db-nav-monogram')) {
                indicatorEl.style.opacity = '0';
                return;
            }
            const indicatorWidth = Math.max(22, Math.round(linkEl.offsetWidth * 0.56));
            const targetX = linkEl.offsetLeft + Math.round((linkEl.offsetWidth - indicatorWidth) / 2);
            indicatorEl.style.width = `${indicatorWidth}px`;
            indicatorEl.style.transform = `translate3d(${targetX}px, 0, 0)`;
            indicatorEl.style.opacity = '1';
        };

        const setActiveTarget = (targetId) => {
            let activeItem = null;
            navLinks.forEach((link) => {
                const isActive = link.dataset.navTarget === targetId;
                link.classList.toggle('is-active', isActive);
                if (isActive) {
                    activeItem = link;
                }
            });
            moveIndicatorTo(activeItem);
        };

        navLinks.forEach((link) => {
            link.addEventListener('click', (e) => {
                const targetId = link.dataset.navTarget;
                const targetEl = targetId ? document.getElementById(targetId) : null;
                if (!targetEl) {
                    return;
                }
                e.preventDefault();
                targetEl.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
                setActiveTarget(targetId);
            });
        });

        window.addEventListener('resize', () => {
            const current = bottomNavEl.querySelector('.db-nav-item.is-active');
            moveIndicatorTo(current);
        }, { passive: true });

        document.addEventListener('dark-boho:opened', () => {
            const current = bottomNavEl.querySelector('.db-nav-item.is-active') || navLinks[0];
            moveIndicatorTo(current);
        });

        if (typeof window.IntersectionObserver === 'undefined') {
            return;
        }

        const observedTargets = navLinks
            .map((link) => document.getElementById(link.dataset.navTarget || ''))
            .filter(Boolean);

        if (observedTargets.length === 0) {
            return;
        }

        const observer = new window.IntersectionObserver(
            (entries) => {
                const visible = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort((a, b) => b.intersectionRatio - a.intersectionRatio);
                if (visible.length > 0) {
                    setActiveTarget(visible[0].target.id);
                }
            },
            {
                rootMargin: '-28% 0px -52% 0px',
                threshold: [0.1, 0.35, 0.65],
            },
        );

        observedTargets.forEach((sectionEl) => observer.observe(sectionEl));
    };

    const splitTitleWordsForMask = (titleEl) => {
        if (!titleEl || titleEl.dataset.masked === '1') {
            return [];
        }
        titleEl.dataset.masked = '1';

        const wordInners = [];
        const childNodes = Array.from(titleEl.childNodes);

        childNodes.forEach((node) => {
            if (node.nodeType === Node.TEXT_NODE) {
                const parts = (node.textContent || '').split(/(\s+)/);
                const fragment = document.createDocumentFragment();
                parts.forEach((part) => {
                    if (!part) {
                        return;
                    }
                    if (/^\s+$/.test(part)) {
                        fragment.appendChild(document.createTextNode(part));
                        return;
                    }
                    const mask = document.createElement('span');
                    mask.className = 'db-word-mask';
                    const inner = document.createElement('span');
                    inner.className = 'db-word-inner';
                    inner.textContent = part;
                    mask.appendChild(inner);
                    fragment.appendChild(mask);
                    wordInners.push(inner);
                });
                titleEl.replaceChild(fragment, node);
            } else if (node.nodeType === Node.ELEMENT_NODE) {
                const mask = document.createElement('span');
                mask.className = 'db-word-mask';
                const inner = document.createElement('span');
                inner.className = 'db-word-inner';
                node.parentNode?.insertBefore(mask, node);
                inner.appendChild(node);
                mask.appendChild(inner);
                wordInners.push(inner);
            }
        });

        return wordInners;
    };

    const startOpenedAnimations = () => {
        if (openedAnimationsStarted || prefersReducedMotion || typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') {
            return;
        }
        openedAnimationsStarted = true;

        const gsap = window.gsap;
        const ScrollTrigger = window.ScrollTrigger;
        if (typeof gsap.registerPlugin === 'function') {
            gsap.registerPlugin(ScrollTrigger);
        }

        // Animasi 4 & 5: Scroll reveal bertahap per section + masking judul
        const revealSelector = [
            '.db-kicker',
            '.db-opening-copy',
            '.db-arabic',
            '.db-verse-quote',
            '.db-countdown',
            '.db-person-card',
            '.db-video-facade',
            '.db-gallery-quote',
            '.db-event-card',
            '.db-map-embed',
            '.db-livestream-card',
            '.db-form-card',
            '.db-gift-note',
            '.db-gift-card',
            '.db-gift-address-card',
            '.db-wish-card',
            '.db-actions-row',
            '.db-closing-text',
            '.db-closing-names',
            '.db-co-inviters',
        ].join(', ');

        document.querySelectorAll('.db-reveal-section').forEach((section) => {
            const titleEl = section.querySelector('.db-section-title');
            if (titleEl) {
                const words = splitTitleWordsForMask(titleEl);
                if (words.length > 0) {
                    gsap.from(words, {
                        yPercent: 110,
                        opacity: 0,
                        duration: 0.68,
                        stagger: 0.06,
                        ease: 'power3.out',
                        clearProps: 'transform,opacity,willChange',
                        scrollTrigger: {
                            trigger: titleEl,
                            start: 'top 90%',
                            once: true,
                        },
                    });
                }
            }

            const items = Array.from(section.querySelectorAll(revealSelector))
                .filter((el) => !el.closest('[hidden]'));

            if (items.length > 0) {
                gsap.from(items, {
                    y: 28,
                    opacity: 0,
                    duration: 0.72,
                    stagger: 0.09,
                    ease: 'power2.out',
                    clearProps: 'transform,opacity,willChange',
                    scrollTrigger: {
                        trigger: section,
                        start: 'top 86%',
                        once: true,
                    },
                });
            }
        });

        // Animasi 6: Foto mempelai clip-path lengkung dari bawah ke atas + parallax lembut
        document.querySelectorAll('.db-arch-portrait-media').forEach((mediaEl) => {
            gsap.fromTo(
                mediaEl,
                {
                    clipPath: 'inset(100% 0% 0% 0% round 999px 999px 14px 14px)',
                    opacity: 0.4,
                },
                {
                    clipPath: 'inset(0% 0% 0% 0% round 999px 999px 14px 14px)',
                    opacity: 1,
                    duration: 1.05,
                    ease: 'power3.out',
                    clearProps: 'clipPath,opacity,willChange',
                    scrollTrigger: {
                        trigger: mediaEl,
                        start: 'top 88%',
                        once: true,
                    },
                },
            );

            const photoEl = mediaEl.querySelector('.db-person-photo');
            if (photoEl) {
                gsap.fromTo(
                    photoEl,
                    { yPercent: 4 },
                    {
                        yPercent: -6,
                        ease: 'none',
                        scrollTrigger: {
                            trigger: mediaEl,
                            start: 'top bottom',
                            end: 'bottom top',
                            scrub: 0.6,
                        },
                    },
                );
            }
        });

        // Animasi 7: Divider SVG tergambar (stroke-dashoffset) saat masuk viewport
        document.querySelectorAll('.db-svg-divider').forEach((svgEl) => {
            const drawPaths = Array.from(svgEl.querySelectorAll('.db-draw-path'));
            if (drawPaths.length === 0) {
                return;
            }
            drawPaths.forEach((path) => {
                if (typeof path.getTotalLength === 'function') {
                    const len = path.getTotalLength();
                    path.style.strokeDasharray = `${len}`;
                    path.style.strokeDashoffset = `${len}`;
                }
            });
            gsap.to(drawPaths, {
                strokeDashoffset: 0,
                duration: 0.9,
                stagger: 0.08,
                ease: 'power2.out',
                clearProps: 'strokeDasharray,strokeDashoffset',
                scrollTrigger: {
                    trigger: svgEl,
                    start: 'top 92%',
                    once: true,
                },
            });
        });

        // Animasi 9: Galeri foto muncul bertahap (stagger)
        const galleryItems = document.querySelectorAll('#db-gallery-grid .db-gallery-item');
        if (galleryItems.length > 0) {
            gsap.from(galleryItems, {
                y: 26,
                scale: 0.96,
                opacity: 0,
                duration: 0.65,
                stagger: 0.09,
                ease: 'power2.out',
                clearProps: 'transform,opacity,willChange',
                scrollTrigger: {
                    trigger: '#db-gallery-grid',
                    start: 'top 88%',
                    once: true,
                },
            });
        }
    };

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') || '' : '';
    };

    const showFeedback = (el, type, message) => {
        if (!el) {
            return;
        }
        el.classList.remove('is-success', 'is-error');
        if (type === 'success') {
            el.classList.add('is-success');
        } else if (type === 'error') {
            el.classList.add('is-error');
        }
        el.textContent = message;
        el.hidden = !message;
    };

    const copyText = async (text) => {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return;
        }
        const helper = document.createElement('textarea');
        helper.value = text;
        helper.setAttribute('readonly', '');
        helper.style.position = 'fixed';
        helper.style.opacity = '0';
        document.body.appendChild(helper);
        helper.select();
        const copied = document.execCommand('copy');
        document.body.removeChild(helper);
        if (!copied) {
            throw new Error('Copy failed');
        }
    };

    const initGiftSection = () => {
        const panels = Array.from(document.querySelectorAll('[data-gift-panel]'));
        let hydrated = false;
        let hydrating = false;

        const hydrateGifts = async () => {
            if (hydrated || hydrating || panels.length === 0) {
                return;
            }
            const endpoint = panels[0].dataset.giftsEndpoint;
            if (!endpoint) {
                return;
            }
            hydrating = true;

            try {
                const response = await window.fetch(endpoint, {
                    headers: { Accept: 'application/json' },
                });
                if (!response.ok) {
                    return;
                }
                const data = await response.json();
                const accountMap = new Map((data.accounts || []).map((acc) => [String(acc.id), acc]));
                const addressMap = new Map((data.addresses || []).map((addr) => [String(addr.id), addr]));

                panels.forEach((panel) => {
                    panel.querySelectorAll('[data-gift-account-id]').forEach((card) => {
                        const account = accountMap.get(card.dataset.giftAccountId || '');
                        if (!account) {
                            return;
                        }
                        const numEl = card.querySelector('[data-gift-number]');
                        const copyBtn = card.querySelector('[data-copy-btn]');
                        if (numEl) {
                            numEl.textContent = account.account_number;
                        }
                        if (copyBtn) {
                            copyBtn.dataset.copyText = account.account_number;
                            copyBtn.disabled = false;
                        }
                    });

                    panel.querySelectorAll('[data-gift-address-id]').forEach((card) => {
                        const addr = addressMap.get(card.dataset.giftAddressId || '');
                        const textEl = card.querySelector('[data-gift-address-text]');
                        if (addr && textEl) {
                            textEl.textContent = addr.address;
                        }
                    });

                    const revealBtn = panel.querySelector('[data-gift-reveal-btn]');
                    if (revealBtn) {
                        revealBtn.hidden = true;
                    }
                });

                hydrated = true;
            } catch {
                // Biarkan pengguna dapat mencoba klik tombol tampilkan ulang secara manual
            } finally {
                hydrating = false;
            }
        };

        panels.forEach((panel) => {
            const revealBtn = panel.querySelector('[data-gift-reveal-btn]');
            if (revealBtn) {
                revealBtn.addEventListener('click', hydrateGifts);
            }

            const statusEl = panel.querySelector('[data-gift-status]');
            const copyLabel = panel.dataset.labelCopy || 'Salin';
            const copiedLabel = panel.dataset.labelCopied || 'Tersalin';
            const failedLabel = panel.dataset.labelCopyFailed || 'Gagal menyalin otomatis.';

            panel.addEventListener('click', async (event) => {
                const btn = event.target instanceof Element ? event.target.closest('[data-copy-btn]') : null;
                if (!btn || btn.disabled) {
                    return;
                }
                const text = btn.dataset.copyText || '';
                if (!text) {
                    return;
                }

                try {
                    await copyText(text);
                    btn.textContent = copiedLabel;
                    showFeedback(statusEl, 'success', `${copiedLabel}: ${text}`);
                    window.setTimeout(() => {
                        btn.textContent = copyLabel;
                    }, 2200);
                } catch {
                    showFeedback(statusEl, 'error', failedLabel);
                }
            });
        });

        if (panels.length > 0) {
            document.addEventListener('dark-boho:opened', hydrateGifts, { once: true });
        }

        const modalEl = document.getElementById('db-gift-modal');
        const closeBtn = document.getElementById('db-gift-modal-close');
        if (!giftFabEl || !modalEl) {
            return;
        }

        const closeGiftModal = () => {
            if (modalEl.hidden) {
                return;
            }
            modalEl.hidden = true;
            document.documentElement.style.overflow = '';
            giftFabEl.focus();
        };

        const openGiftModal = () => {
            hydrateGifts();
            modalEl.hidden = false;
            document.documentElement.style.overflow = 'hidden';
            if (closeBtn) {
                closeBtn.focus();
            }
        };

        giftFabEl.addEventListener('click', openGiftModal);
        if (closeBtn) {
            closeBtn.addEventListener('click', closeGiftModal);
        }
        modalEl.querySelectorAll('[data-gift-modal-close]').forEach((el) => {
            el.addEventListener('click', closeGiftModal);
        });

        document.addEventListener('keydown', (e) => {
            if (modalEl.hidden) {
                return;
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                closeGiftModal();
                return;
            }
            if (e.key === 'Tab') {
                const focusables = Array.from(
                    modalEl.querySelectorAll('a, button:not([hidden]):not([disabled]), input, textarea'),
                );
                if (focusables.length === 0) {
                    return;
                }
                const first = focusables[0];
                const last = focusables[focusables.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });
    };

    const initRsvpForm = () => {
        const form = document.getElementById('db-rsvp-form');
        if (!form) {
            return;
        }

        const paxWrap = document.getElementById('db-rsvp-pax-wrap');
        const paxInput = document.getElementById('db-rsvp-pax');
        const nameInput = document.getElementById('db-rsvp-name');
        const feedbackEl = document.getElementById('db-rsvp-feedback');
        const submitBtn = document.getElementById('db-rsvp-submit');
        const attendingRadios = Array.from(form.querySelectorAll('input[name="attending"]'));

        const errorNodes = {
            name: document.getElementById('db-rsvp-name-error'),
            pax: document.getElementById('db-rsvp-pax-error'),
            message: document.getElementById('db-rsvp-message-error'),
        };

        const syncPaxVisibility = () => {
            const selected = attendingRadios.find((radio) => radio.checked);
            const isAttending = !selected || selected.value === '1';
            if (paxWrap) {
                paxWrap.hidden = !isAttending;
            }
            if (paxInput) {
                paxInput.disabled = !isAttending;
            }
        };

        attendingRadios.forEach((radio) => radio.addEventListener('change', syncPaxVisibility));
        syncPaxVisibility();

        const clearErrors = () => {
            Object.values(errorNodes).forEach((node) => {
                if (node) {
                    node.textContent = '';
                    node.hidden = true;
                }
            });
            showFeedback(feedbackEl, '', '');
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearErrors();

            const nameValue = nameInput ? nameInput.value.trim() : '';
            if (nameValue.length < 2) {
                if (errorNodes.name) {
                    errorNodes.name.textContent = 'Nama lengkap minimal 2 karakter.';
                    errorNodes.name.hidden = false;
                }
                if (nameInput) {
                    nameInput.focus();
                }
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.setAttribute('aria-busy', 'true');
                submitBtn.textContent = form.dataset.labelSubmitting || 'Menyimpan...';
            }

            try {
                const formData = new window.FormData(form);
                const response = await window.fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: formData,
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 422 && payload.errors) {
                        Object.entries(payload.errors).forEach(([field, messages]) => {
                            const node = errorNodes[field];
                            if (node && Array.isArray(messages) && messages.length > 0) {
                                node.textContent = messages[0];
                                node.hidden = false;
                            }
                        });
                    }
                    const fallbackError = response.status === 429
                        ? 'Terlalu banyak percobaan. Silakan tunggu sebentar sebelum mengirim ulang.'
                        : (payload.message || 'Gagal menyimpan konfirmasi kehadiran.');
                    showFeedback(feedbackEl, 'error', fallbackError);
                    return;
                }

                showFeedback(feedbackEl, 'success', payload.message || 'Konfirmasi kehadiran tersimpan.');
                if (submitBtn && form.dataset.labelUpdate) {
                    submitBtn.dataset.savedOnce = '1';
                }
            } catch {
                showFeedback(feedbackEl, 'error', 'Koneksi terganggu. Silakan coba beberapa saat lagi.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.removeAttribute('aria-busy');
                    submitBtn.textContent = submitBtn.dataset.savedOnce === '1'
                        ? (form.dataset.labelUpdate || 'Perbarui Konfirmasi')
                        : (form.dataset.labelSubmit || 'Kirim Konfirmasi');
                }
            }
        });
    };

    const createWishCardElement = (wish) => {
        const card = document.createElement('article');
        card.className = 'db-card db-wish-card';

        const header = document.createElement('div');
        header.className = 'db-wish-header';

        const nameEl = document.createElement('strong');
        nameEl.className = 'db-wish-name';
        nameEl.textContent = wish.name || '';
        header.appendChild(nameEl);

        if (wish.origin) {
            const originEl = document.createElement('span');
            originEl.className = 'db-wish-origin';
            originEl.textContent = wish.origin;
            header.appendChild(originEl);
        }

        const timeEl = document.createElement('time');
        timeEl.className = 'db-wish-time';
        timeEl.textContent = wish.created_at_human || 'Baru saja';
        header.appendChild(timeEl);

        const messageEl = document.createElement('p');
        messageEl.className = 'db-wish-message';
        messageEl.textContent = wish.message || '';

        card.appendChild(header);
        card.appendChild(messageEl);

        return card;
    };

    const initWishesSection = () => {
        const form = document.getElementById('db-wishes-form');
        const listEl = document.getElementById('db-wishes-list');
        const loadMoreBtn = document.getElementById('db-wishes-load-more');
        const messageInput = document.getElementById('db-wish-message');
        const charCountEl = document.getElementById('db-wish-char-count');

        if (messageInput && charCountEl) {
            const updateCount = () => {
                charCountEl.textContent = `${messageInput.value.length} / 500`;
            };
            messageInput.addEventListener('input', updateCount);
            updateCount();
        }

        if (form && listEl) {
            const nameInput = document.getElementById('db-wish-name');
            const feedbackEl = document.getElementById('db-wish-feedback');
            const submitBtn = document.getElementById('db-wish-submit');
            const errorNodes = {
                name: document.getElementById('db-wish-name-error'),
                origin: document.getElementById('db-wish-origin-error'),
                message: document.getElementById('db-wish-message-error'),
            };

            const clearErrors = () => {
                Object.values(errorNodes).forEach((node) => {
                    if (node) {
                        node.textContent = '';
                        node.hidden = true;
                    }
                });
                showFeedback(feedbackEl, '', '');
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearErrors();

                const nameVal = nameInput ? nameInput.value.trim() : '';
                const msgVal = messageInput ? messageInput.value.trim() : '';

                if (nameVal.length < 2) {
                    if (errorNodes.name) {
                        errorNodes.name.textContent = 'Nama minimal 2 karakter.';
                        errorNodes.name.hidden = false;
                    }
                    if (nameInput) {
                        nameInput.focus();
                    }
                    return;
                }

                if (msgVal.length < 3) {
                    if (errorNodes.message) {
                        errorNodes.message.textContent = 'Ucapan dan doa minimal 3 karakter.';
                        errorNodes.message.hidden = false;
                    }
                    if (messageInput) {
                        messageInput.focus();
                    }
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.setAttribute('aria-busy', 'true');
                    submitBtn.textContent = form.dataset.labelSubmitting || 'Mengirim...';
                }

                try {
                    const formData = new window.FormData(form);
                    const response = await window.fetch(form.action, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                        },
                        body: formData,
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        if (response.status === 422 && payload.errors) {
                            Object.entries(payload.errors).forEach(([field, messages]) => {
                                const node = errorNodes[field];
                                if (node && Array.isArray(messages) && messages.length > 0) {
                                    node.textContent = messages[0];
                                    node.hidden = false;
                                }
                            });
                        }
                        const fallbackError = response.status === 429
                            ? 'Terlalu sering mengirim ucapan. Mohon tunggu sebentar.'
                            : (payload.message || 'Gagal mengirim ucapan.');
                        showFeedback(feedbackEl, 'error', fallbackError);
                        return;
                    }

                    if (payload.wish) {
                        const emptyEl = document.getElementById('db-wishes-empty');
                        if (emptyEl) {
                            emptyEl.remove();
                        }
                        const card = createWishCardElement(payload.wish);
                        listEl.prepend(card);
                        if (!prefersReducedMotion && typeof window.gsap !== 'undefined') {
                            window.gsap.from(card, { y: 14, opacity: 0, duration: 0.4, ease: 'power2.out', clearProps: 'transform,opacity' });
                        }
                    }

                    if (messageInput) {
                        messageInput.value = '';
                        if (charCountEl) {
                            charCountEl.textContent = '0 / 500';
                        }
                    }

                    showFeedback(feedbackEl, 'success', payload.message || 'Ucapan berhasil ditambahkan.');
                } catch {
                    showFeedback(feedbackEl, 'error', 'Koneksi terganggu. Silakan coba beberapa saat lagi.');
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.removeAttribute('aria-busy');
                        submitBtn.textContent = form.dataset.labelSubmit || 'Kirim Ucapan';
                    }
                }
            });
        }

        if (loadMoreBtn && listEl) {
            loadMoreBtn.addEventListener('click', async () => {
                const endpoint = loadMoreBtn.dataset.endpoint;
                const nextPage = loadMoreBtn.dataset.nextPage;
                if (!endpoint || !nextPage) {
                    return;
                }

                loadMoreBtn.disabled = true;
                loadMoreBtn.textContent = loadMoreBtn.dataset.labelLoading || 'Memuat ucapan...';

                try {
                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set('page', nextPage);
                    const response = await window.fetch(url.toString(), {
                        headers: { Accept: 'application/json' },
                    });
                    if (!response.ok) {
                        return;
                    }
                    const payload = await response.json();
                    (payload.items || []).forEach((item) => {
                        listEl.appendChild(createWishCardElement(item));
                    });

                    if (payload.has_more && payload.next_page) {
                        loadMoreBtn.dataset.nextPage = String(payload.next_page);
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.textContent = loadMoreBtn.dataset.labelDefault || 'Muat Lebih Banyak';
                    } else {
                        const wrap = loadMoreBtn.parentElement;
                        if (wrap) {
                            wrap.remove();
                        } else {
                            loadMoreBtn.remove();
                        }
                    }
                } catch {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.textContent = loadMoreBtn.dataset.labelDefault || 'Muat Lebih Banyak';
                }
            });
        }
    };

    const initAdminPreviewBridge = () => {
        if (root.dataset.previewMode !== 'true') {
            return;
        }

        const isSafeHex = (val) => typeof val === 'string' && /^#[0-9a-fA-F]{3,8}$/.test(val.trim());
        const toBool = (val, fallback = true) => {
            if (typeof val === 'boolean') {
                return val;
            }
            if (val === 'false' || val === '0' || val === 0) {
                return false;
            }
            if (val === 'true' || val === '1' || val === 1) {
                return true;
            }
            return fallback;
        };

        const applyPreviewConfig = (config) => {
            if (!config || typeof config !== 'object') {
                return;
            }

            if (typeof config.preset === 'string' && config.preset) {
                root.dataset.preset = config.preset;
            }

            if (config.cover && typeof config.cover === 'object') {
                const style = config.cover.style === 'new' ? 'new' : 'default';
                root.dataset.coverStyle = style;
                if (coverEl) {
                    coverEl.classList.toggle('db-cover--default', style === 'default');
                    coverEl.classList.toggle('db-cover--new', style === 'new');
                }

                const usePhoto = toBool(config.cover.usephoto, true);
                root.dataset.coverUsePhoto = usePhoto ? 'true' : 'false';
                const coverPhoto = document.querySelector('.db-cover-photo');
                if (coverPhoto) {
                    coverPhoto.hidden = !usePhoto;
                }

                if (isSafeHex(config.cover.overlayColor)) {
                    root.style.setProperty('--db-cover-overlay-color', config.cover.overlayColor.trim());
                }
                if (config.cover.overlayOpacity !== undefined && !Number.isNaN(Number(config.cover.overlayOpacity))) {
                    const opacity = Math.max(0, Math.min(0.95, Number(config.cover.overlayOpacity)));
                    root.style.setProperty('--db-cover-overlay-opacity', String(opacity));
                }
                if (isSafeHex(config.cover.textColor)) {
                    root.style.setProperty('--db-cover-text-color', config.cover.textColor.trim());
                }

                const showGuest = toBool(config.cover.showGuestName, true);
                const guestEl = document.querySelector('.db-cover-guest');
                if (guestEl) {
                    guestEl.hidden = !showGuest;
                }
            }

            const layout = config.layout && typeof config.layout === 'object' ? config.layout : {};
            const sections = config.sections && typeof config.sections === 'object' ? config.sections : {};

            if (layout.illustrationMode) {
                const mode = layout.illustrationMode === 'minimal' ? 'minimal' : 'illustrated';
                root.dataset.illustrationMode = mode;
                document.querySelectorAll('.db-cover-pampas, .db-closing-pampas').forEach((el) => {
                    el.hidden = mode === 'minimal';
                });
            }

            const brideFirst = toBool(layout.brideFirst, true);
            root.dataset.brideFirst = brideFirst ? 'true' : 'false';
            const coupleGrid = document.querySelector('#section-couple .db-couple-grid');
            if (coupleGrid) {
                const brideCard = coupleGrid.querySelector('.db-person-card[data-role="bride"]');
                const groomCard = coupleGrid.querySelector('.db-person-card[data-role="groom"]');
                const sep = coupleGrid.querySelector('.db-couple-separator');
                if (brideCard && groomCard) {
                    const ordered = brideFirst ? [brideCard, sep, groomCard] : [groomCard, sep, brideCard];
                    ordered.filter(Boolean).forEach((node) => coupleGrid.appendChild(node));
                }
            }

            if (layout.mapsAndRsvpBelowEvents !== undefined) {
                root.dataset.mapsBelow = toBool(layout.mapsAndRsvpBelowEvents, true) ? 'true' : 'false';
            }

            const giftEnabled = toBool(sections.gift, true);
            const giftInline = toBool(layout.giftInline, true);
            root.dataset.giftInline = giftInline ? 'true' : 'false';

            const sectionMap = {
                verse: toBool(sections.verse, true),
                video: toBool(sections.video, false),
                gallery: toBool(sections.gallery, true),
                livestream: toBool(sections.livestream, false),
                'gift-inline': giftEnabled && giftInline,
                wishes: toBool(sections.wishes, true),
            };

            Object.entries(sectionMap).forEach(([key, visible]) => {
                const wrapper = document.querySelector(`[data-preview-section="${key}"]`);
                if (wrapper) {
                    wrapper.hidden = !visible;
                }
            });

            if (giftFabEl) {
                giftFabEl.hidden = !(window.DarkBohoTheme.opened && giftEnabled && !giftInline);
            }

            if (typeof window.ScrollTrigger !== 'undefined') {
                window.ScrollTrigger.refresh();
            }
        };

        window.addEventListener('message', (event) => {
            if (event.origin !== window.location.origin || !event.data || typeof event.data !== 'object') {
                return;
            }

            if (event.data.type === 'dark-boho:preview-config') {
                applyPreviewConfig(event.data.config);
            } else if (event.data.type === 'dark-boho:preview-toggle-cover') {
                if (!window.DarkBohoTheme.opened) {
                    window.DarkBohoTheme.openInvitation();
                } else {
                    window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
                }
            } else if (event.data.type === 'dark-boho:preview-scroll' && typeof event.data.target === 'string') {
                if (!window.DarkBohoTheme.opened) {
                    window.DarkBohoTheme.openInvitation();
                }
                const targetEl = document.getElementById(event.data.target);
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
                }
            }
        });

        if (window.parent && window.parent !== window) {
            window.parent.postMessage({ type: 'dark-boho:preview-ready' }, window.location.origin);
        }
    };

    window.DarkBohoTheme = {
        version: '6.0.0-fase6',
        reducedMotion: prefersReducedMotion,
        opened: false,
        music: musicController,

        openInvitation() {
            if (this.opened) {
                if (mainEl) {
                    mainEl.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
                }
                return;
            }
            this.opened = true;
            musicController.startAfterOpen();

            if (prefersReducedMotion || typeof window.gsap === 'undefined') {
                finishOpening();
                return;
            }

            const gsap = window.gsap;
            const leftDoor = document.querySelector('.db-arch-door--left');
            const rightDoor = document.querySelector('.db-arch-door--right');
            const coverInner = document.querySelector('.db-cover-inner');
            const coverNames = document.querySelector('.db-cover-names');

            const tl = gsap.timeline({
                onComplete: finishOpening,
            });

            if (coverNames) {
                tl.to(coverNames, {
                    y: -36,
                    opacity: 0,
                    duration: 0.7,
                    ease: 'power2.in',
                }, 0);
            }

            if (coverInner) {
                tl.to(coverInner, {
                    y: -24,
                    opacity: 0,
                    duration: 0.85,
                    ease: 'power2.inOut',
                }, 0.15);
            }

            if (leftDoor && rightDoor) {
                tl.to(leftDoor, {
                    xPercent: -102,
                    duration: 1.4,
                    ease: 'power3.inOut',
                }, 0.08);
                tl.to(rightDoor, {
                    xPercent: 102,
                    duration: 1.4,
                    ease: 'power3.inOut',
                }, 0.08);
            }
        },
    };

    if (openButton) {
        openButton.addEventListener('click', () => {
            window.DarkBohoTheme.openInvitation();
        });
    }

    const bootstrap = () => {
        runCoverEntrance();
        initCountdown();
        initVideoFacade();
        initGalleryLightbox();
        initGiftSection();
        initRsvpForm();
        initWishesSection();
        initBottomNav();
        initAdminPreviewBridge();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
})();
