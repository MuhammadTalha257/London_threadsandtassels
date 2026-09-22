import * as Motion from 'motion';
import { createMorphSlider } from './morph-slider';

const animate = Motion.animate;
const inView = Motion.inView;
const scroll = Motion.scroll;

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(pointer: fine)').matches;

/* Things created per page that must be torn down before the next page. */
let teardown = [];
const onTeardown = (fn) => teardown.push(fn);

/* ==================================================================
 * GLOBAL — created once, survives client-side navigation
 * ================================================================== */

(function mobileMenu() {
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    if (!toggle || !menu) return;

    const setOpen = (open) => {
        menu.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        menu.setAttribute('aria-hidden', open ? 'false' : 'true');
        document.body.style.overflow = open ? 'hidden' : '';
    };
    toggle.addEventListener('click', () => setOpen(!menu.classList.contains('is-open')));
    menu.addEventListener('click', (e) => { if (e.target.closest('a')) setOpen(false); });
    window.__closeMenu = () => setOpen(false);
})();

(function preloader() {
    const loader = document.getElementById('page-loader');
    if (!loader) return;
    const countEl = document.getElementById('loader-count');
    const fillEl = document.getElementById('loader-fill');
    let pct = 0;
    let done = false;

    const finish = () => {
        if (done) return;
        done = true;
        if (countEl) countEl.textContent = '100%';
        if (fillEl) fillEl.style.width = '100%';
        setTimeout(() => {
            loader.classList.add('is-done');
            setTimeout(() => loader.remove(), 900);
        }, 300);
    };
    if (reducedMotion) { loader.remove(); return; }

    const tick = setInterval(() => {
        pct = Math.min(96, pct + Math.random() * 13 + 4);
        if (countEl) countEl.textContent = `${Math.round(pct)}%`;
        if (fillEl) fillEl.style.width = `${pct}%`;
    }, 140);
    const stop = () => { clearInterval(tick); finish(); };
    window.addEventListener('load', () => setTimeout(stop, 350));
    setTimeout(stop, 4000);
})();

/* ================================================================== */
/* Offer popup: the promotion, shown as a dismissible modal on open    */
/* ================================================================== */
(function offerPopup() {
    const popup = document.getElementById('offer-popup');
    if (!popup) return;
    const closeBtn = document.getElementById('offer-popup-close');
    const cta = document.getElementById('offer-popup-cta');
    const KEY = 'ltt_offer_dismissed';

    const open = () => {
        if (sessionStorage.getItem(KEY)) return;
        popup.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    };
    const close = () => {
        popup.classList.remove('is-open');
        document.body.style.overflow = '';
        try { sessionStorage.setItem(KEY, '1'); } catch (e) { /* private mode */ }
    };

    closeBtn?.addEventListener('click', close);
    cta?.addEventListener('click', close);
    popup.addEventListener('click', (event) => { if (event.target === popup) close(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && popup.classList.contains('is-open')) close(); });

    const start = Date.now();
    window.addEventListener('load', () => setTimeout(open, Math.max(600, 1400 - (Date.now() - start))));
    setTimeout(open, 3200);
})();

(function topbar() {
    const bar = document.getElementById('site-topbar');
    if (!bar) return;
    const setHeight = () => document.documentElement.style.setProperty('--topbar-h', `${bar.offsetHeight}px`);
    setHeight();
    window.addEventListener('resize', setHeight);
    window.addEventListener('load', setHeight);

    let lastY = window.scrollY;
    window.addEventListener('scroll', () => {
        const y = window.scrollY;
        const header = document.getElementById('site-header');
        if (header && header.classList.contains('site-header--transparent')) {
            header.classList.toggle('is-scrolled', y > 60);
        }
        const menu = document.getElementById('mobile-menu');
        const open = menu && menu.classList.contains('is-open');
        bar.classList.toggle('is-hidden', y > lastY && y > bar.offsetHeight + 40 && !open);
        lastY = y;
    }, { passive: true });
})();

/* macOS-dock magnification on the desktop nav — nearest link grows,
   neighbours grow less, everything eases back on leave. */
(function dockNav() {
    if (!finePointer || reducedMotion) return;
    const nav = document.querySelector('[data-dock]');
    if (!nav) return;
    const links = Array.from(nav.querySelectorAll('.nav-link'));
    if (!links.length) return;

    const MAX = 0.42;      // peak extra scale
    const RANGE = 130;     // px of influence

    nav.addEventListener('pointermove', (e) => {
        links.forEach((link) => {
            const r = link.getBoundingClientRect();
            const d = Math.abs(e.clientX - (r.left + r.width / 2));
            const f = Math.max(0, 1 - d / RANGE);
            const eased = f * f * (3 - 2 * f);           // smoothstep
            link.style.transform = `scale(${1 + MAX * eased}) translateY(${-6 * eased}px)`;
            link.style.opacity = String(0.65 + 0.35 * eased);
        });
    });
    nav.addEventListener('pointerleave', () => {
        links.forEach((l) => { l.style.transform = ''; l.style.opacity = ''; });
    });
})();

/* Cursor-following glow on anything marked [data-glow]. */
(function glowEffect() {
    if (!finePointer || reducedMotion) return;
    document.addEventListener('pointermove', (e) => {
        document.querySelectorAll('[data-glow]').forEach((el) => {
            const r = el.getBoundingClientRect();
            if (e.clientX < r.left - 120 || e.clientX > r.right + 120 ||
                e.clientY < r.top - 120 || e.clientY > r.bottom + 120) {
                el.style.setProperty('--glow-o', '0');
                return;
            }
            el.style.setProperty('--glow-x', `${e.clientX - r.left}px`);
            el.style.setProperty('--glow-y', `${e.clientY - r.top}px`);
            el.style.setProperty('--glow-o', '1');
        });
    }, { passive: true });
})();

(function cursorView() {
    const cursor = document.getElementById('cursor-view');
    if (!cursor || !finePointer || reducedMotion) return;
    document.addEventListener('pointermove', (e) => {
        cursor.style.left = `${e.clientX}px`;
        cursor.style.top = `${e.clientY}px`;
    }, { passive: true });
    document.addEventListener('pointerover', (e) => {
        if (e.target.closest('[data-cursor-view]')) cursor.classList.add('is-visible');
    });
    document.addEventListener('pointerout', (e) => {
        if (e.target.closest('[data-cursor-view]')) cursor.classList.remove('is-visible');
    });
})();

/* ==================================================================
 * PER-PAGE — re-runs after every client-side navigation
 * ================================================================== */

function initReveals() {
    const targets = () => Array.from(document.querySelectorAll('[data-motion]'));

    // Reveal everything, unconditionally. Used as the failsafe and the
    // reduced-motion path — content must NEVER be left invisible.
    const revealAll = () => targets().forEach((el) => el.classList.add('is-in-view'));

    if (reducedMotion || typeof inView !== 'function') {
        document.body.classList.remove('motion-ready');
        revealAll();
        return;
    }

    try {
        document.body.classList.add('motion-ready');
        document.querySelectorAll('.site-main > section, .site-main > *').forEach((el) => {
            el.dataset.motion ??= 'section';
        });

        const els = targets();

        // Anything already on screen at load shows straight away.
        els.forEach((el) => {
            const r = el.getBoundingClientRect();
            if (r.top < window.innerHeight * 0.9) el.classList.add('is-in-view');
        });

        els.forEach((el, i) => {
            inView(el, () => {
                setTimeout(() => el.classList.add('is-in-view'), (i % 4) * 100);
            }, { amount: 0.08 });
        });

        // Failsafe: if an observer never fires (mobile quirks, aborted scroll,
        // a swapped page), reveal everything rather than showing a blank page.
        clearTimeout(window.__revealFailsafe);
        window.__revealFailsafe = setTimeout(revealAll, 2500);
        window.addEventListener('pageshow', revealAll, { once: true });
    } catch (e) {
        document.body.classList.remove('motion-ready');
        revealAll();
    }
}

function initHero() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    // --- WebGL morph slider as the hero background ---
    const stage = hero.querySelector('[data-morph]');
    if (stage) {
        let images = [];
        try { images = JSON.parse(stage.dataset.morph || '[]'); } catch (e) { images = []; }
        if (images.length) {
            const slider = createMorphSlider(stage, {
                images,
                transition: stage.dataset.transition || 'melt',
                duration: 1.25,
                intensity: 0.55,
                scale: 2.4,
                aberration: 0.35,
                drift: 0.4,
                overlayColor: '#042a2b',
                autoplay: true,
                autoplayDelay: 5,
                loop: true,
            });
            if (slider) {
                onTeardown(() => slider.destroy());

                const counter = hero.querySelector('[data-hero-counter]');
                const dots = Array.from(hero.querySelectorAll('[data-hero-dot]'));
                const pad = (n) => String(n).padStart(2, '0');
                slider.onChange((i, total) => {
                    if (counter) counter.textContent = `${pad(i + 1)} / ${pad(total)}`;
                    dots.forEach((d, di) => d.classList.toggle('is-active', di === i));
                });
                dots.forEach((d, di) => d.addEventListener('click', () => slider.goTo(di)));
                hero.querySelector('[data-hero-next]')?.addEventListener('click', () => slider.next());
                hero.querySelector('[data-hero-prev]')?.addEventListener('click', () => slider.prev());

                // drag / swipe
                let sx = null;
                stage.addEventListener('pointerdown', (e) => { sx = e.clientX; });
                stage.addEventListener('pointerup', (e) => {
                    if (sx === null) return;
                    const dx = e.clientX - sx;
                    if (Math.abs(dx) > 60) (dx < 0 ? slider.next() : slider.prev());
                    sx = null;
                });
            }
        }
    }

    // --- scroll choreography ---
    if (reducedMotion || typeof scroll !== 'function' || typeof animate !== 'function') return;
    try {
        const article = hero.querySelector('[data-hero-article]');
        if (article) {
            scroll(
                animate(article, {
                    transform: ['translateY(0) scale(1)', 'translateY(18vh) scale(.94)'],
                    opacity: [1, 0],
                }, { easing: 'linear' }),
                { target: hero, offset: ['start start', 'end start'] }
            );
        }
        // Centre image descends with a 3D tilt, Revolut-style.
        const figure = document.querySelector('[data-feature-figure]');
        if (figure) {
            scroll(
                animate(figure, {
                    transform: [
                        'perspective(1200px) translate3d(0, 0, 0) rotateX(0deg) scale(1)',
                        'perspective(1200px) translate3d(0, 46vh, -200px) rotateX(22deg) scale(.58)',
                    ],
                    opacity: [1, 1, 0],
                }, { easing: 'linear' }),
                { target: hero, offset: ['start start', 'end start'] }
            );
        }
    } catch (e) { /* scroll-linked motion unsupported */ }
}

function initProductGalleries() {
    document.querySelectorAll('[data-gallery]').forEach((card) => {
        let images;
        try { images = JSON.parse(card.dataset.gallery); } catch { return; }
        if (!Array.isArray(images) || images.length < 2) return;
        const img = card.querySelector('img');
        if (!img) return;
        const original = img.src;
        let idx = 0;
        let timer = null;
        img.style.transition = 'opacity .28s ease';
        const swap = (src) => {
            img.style.opacity = '0';
            setTimeout(() => { img.src = src; img.style.opacity = '1'; }, 140);
        };
        card.addEventListener('mouseenter', () => {
            timer = setInterval(() => { idx = (idx + 1) % images.length; swap(images[idx]); }, 900);
        });
        card.addEventListener('mouseleave', () => {
            clearInterval(timer); idx = 0; swap(original);
        });
        onTeardown(() => clearInterval(timer));
    });
}

/* ==================================================================
 * PRODUCT DETAIL GALLERY — thumbnails, prev/next arrows, hover-zoom
 * on the main image, and a click-to-open zoomable lightbox.
 * ================================================================== */
function initProductDetailGallery() {
    const wrap = document.querySelector('[data-product-gallery]');
    const frame = document.querySelector('[data-gallery-frame]');
    const main = document.getElementById('product-main-image');
    if (!wrap || !frame || !main) return;

    let images;
    try { images = JSON.parse(wrap.dataset.galleryImages || '[]'); } catch { images = []; }
    if (!Array.isArray(images) || images.length === 0) images = [main.src];

    const strip = document.querySelector('[data-thumb-strip]');
    const counter = document.querySelector('[data-gallery-counter]');
    const prevBtns = document.querySelectorAll('[data-gallery-prev]');
    const nextBtns = document.querySelectorAll('[data-gallery-next]');
    const zoomBtn = document.querySelector('[data-gallery-zoom]');

    const lightbox = document.getElementById('image-lightbox');
    const lightboxImg = document.getElementById('lightbox-image');
    const lightboxStage = document.querySelector('[data-lightbox-stage]');

    let index = 0;
    main.style.transition = 'opacity .25s ease';

    const setIndex = (next, { silent } = {}) => {
        index = (next + images.length) % images.length;
        if (!silent) {
            main.style.opacity = '0';
            setTimeout(() => { main.src = images[index]; main.style.opacity = '1'; }, 130);
        } else {
            main.src = images[index];
        }
        if (strip) {
            strip.querySelectorAll('[data-thumb]').forEach((t) => {
                t.classList.toggle('is-active', Number(t.dataset.thumbIndex) === index);
            });
        }
        if (counter) counter.textContent = `${index + 1} / ${images.length}`;
        if (lightbox?.classList.contains('is-open')) showInLightbox(index);
    };

    strip?.querySelectorAll('[data-thumb]').forEach((thumb) => {
        thumb.addEventListener('click', () => setIndex(Number(thumb.dataset.thumbIndex)));
    });
    prevBtns.forEach((btn) => btn.addEventListener('click', () => setIndex(index - 1)));
    nextBtns.forEach((btn) => btn.addEventListener('click', () => setIndex(index + 1)));

    /* Hover-zoom: the main photo magnifies and follows the cursor within its frame. */
    if (finePointer && !reducedMotion) {
        frame.classList.add('is-zoom-ready');
        frame.addEventListener('mousemove', (event) => {
            const r = frame.getBoundingClientRect();
            const x = ((event.clientX - r.left) / r.width) * 100;
            const y = ((event.clientY - r.top) / r.height) * 100;
            frame.style.setProperty('--zx', `${Math.max(0, Math.min(100, x))}%`);
            frame.style.setProperty('--zy', `${Math.max(0, Math.min(100, y))}%`);
        });
        frame.addEventListener('mouseenter', () => frame.classList.add('is-hovering'));
        frame.addEventListener('mouseleave', () => frame.classList.remove('is-hovering'));
    }

    /* Click-to-zoom lightbox, with left/right arrows and pan-to-zoom. */
    if (lightbox && lightboxImg) {
        const openLightbox = () => {
            showInLightbox(index);
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        };
        const closeLightbox = () => {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            lightboxImg.classList.remove('is-zoomed');
            lightboxImg.style.width = '';
            document.body.style.overflow = '';
        };
        function showInLightbox(i) {
            lightboxImg.src = images[i];
            lightboxImg.classList.remove('is-zoomed');
            lightboxImg.style.width = '';
        }

        frame.addEventListener('click', (event) => {
            if (event.target.closest('[data-gallery-prev], [data-gallery-next]')) return;
            openLightbox();
        });
        zoomBtn?.addEventListener('click', (event) => { event.stopPropagation(); openLightbox(); });

        lightbox.querySelector('[data-lightbox-close]')?.addEventListener('click', closeLightbox);
        lightbox.querySelectorAll('[data-lightbox-prev]').forEach((btn) => btn.addEventListener('click', () => setIndex(index - 1)));
        lightbox.querySelectorAll('[data-lightbox-next]').forEach((btn) => btn.addEventListener('click', () => setIndex(index + 1)));
        lightbox.addEventListener('click', (event) => { if (event.target === lightbox) closeLightbox(); });
        document.addEventListener('keydown', (event) => {
            if (!lightbox.classList.contains('is-open')) return;
            if (event.key === 'Escape') closeLightbox();
            if (event.key === 'ArrowLeft') setIndex(index - 1);
            if (event.key === 'ArrowRight') setIndex(index + 1);
        });

        /* Click the zoomed-in image itself: toggle 2x magnification centred
           on the click point; the stage scrolls natively to pan around it. */
        lightboxImg.addEventListener('click', (event) => {
            event.stopPropagation();
            if (lightboxImg.classList.contains('is-zoomed')) {
                lightboxImg.classList.remove('is-zoomed');
                lightboxImg.style.width = '';
                return;
            }
            const r = lightboxImg.getBoundingClientRect();
            const px = (event.clientX - r.left) / r.width;
            const py = (event.clientY - r.top) / r.height;
            lightboxImg.classList.add('is-zoomed');
            const zoomedWidth = r.width * 2;
            lightboxImg.style.width = `${zoomedWidth}px`;
            requestAnimationFrame(() => {
                if (!lightboxStage) return;
                lightboxStage.scrollLeft = zoomedWidth * px - lightboxStage.clientWidth / 2;
                lightboxStage.scrollTop = (r.height * 2) * py - lightboxStage.clientHeight / 2;
            });
        });
    }

    setIndex(0, { silent: true });
}

function initColorPicker() {
    const picker = document.querySelector('[data-color-picker]');
    if (!picker) return;
    const nameOut = document.getElementById('selected-color-name');
    const input = document.getElementById('selected-color-input');
    picker.querySelectorAll('.color-swatch').forEach((sw) => {
        sw.addEventListener('click', () => {
            picker.querySelectorAll('.color-swatch').forEach((s) => s.classList.remove('is-active'));
            sw.classList.add('is-active');
            const name = sw.dataset.colorName || '';
            if (nameOut) nameOut.textContent = name;
            if (input) input.value = name;
        });
    });
}

function initDashboardTabs() {
    const tabs = document.querySelectorAll('[data-dash-tab]');
    const panels = document.querySelectorAll('[data-dash-panel]');
    if (!tabs.length || !panels.length) return;
    const show = (name) => {
        tabs.forEach((t) => t.classList.toggle('is-active', t.dataset.dashTab === name));
        panels.forEach((p) => p.classList.toggle('hidden', p.dataset.dashPanel !== name));
        const url = new URL(window.location.href);
        url.searchParams.set('tab', name);
        history.replaceState(history.state, '', url);
    };
    tabs.forEach((t) => t.addEventListener('click', () => show(t.dataset.dashTab)));
}

function initShopFilters() {
    const grid = document.querySelector('[data-shop-grid]');
    if (!grid) return;

    const items = Array.from(grid.querySelectorAll('[data-shop-item]'));
    const pills = Array.from(document.querySelectorAll('[data-filter]'));
    const colorSel = document.querySelector('[data-color-filter]');
    const categorySel = document.querySelector('[data-category-filter]');
    const sortSel = document.querySelector('[data-sort]');
    const countEl = document.querySelector('[data-shop-count]');
    const emptyEl = document.querySelector('[data-shop-empty]');
    const resetBtn = document.querySelector('[data-shop-reset]');
    const state = { filter: 'all', color: '', category: '', sort: 'newest' };

    const matches = (el) => {
        if (state.filter === 'new' && el.dataset.new !== '1') return false;
        if (state.filter === 'sale' && el.dataset.sale !== '1') return false;
        if (state.filter === 'instock' && el.dataset.instock !== '1') return false;
        if (state.color && !(el.dataset.colors || '').split(' ').includes(state.color)) return false;
        if (state.category && el.dataset.category !== state.category) return false;
        return true;
    };
    const sorters = {
        newest: null,
        'price-asc': (a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price),
        'price-desc': (a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price),
        name: (a, b) => (a.dataset.name || '').localeCompare(b.dataset.name || ''),
    };
    const syncUrl = () => {
        const url = new URL(window.location.href);
        state.filter === 'all' ? url.searchParams.delete('filter') : url.searchParams.set('filter', state.filter);
        state.color ? url.searchParams.set('color', state.color) : url.searchParams.delete('color');
        state.category ? url.searchParams.set('category', state.category) : url.searchParams.delete('category');
        state.sort === 'newest' ? url.searchParams.delete('sort') : url.searchParams.set('sort', state.sort);
        history.replaceState(history.state, '', url);
    };
    const apply = () => {
        let shown = 0;
        items.forEach((el) => {
            const ok = matches(el);
            el.classList.toggle('is-hidden', !ok);
            if (ok) { shown++; el.classList.remove('is-entering'); void el.offsetWidth; el.classList.add('is-entering'); }
        });
        const s = sorters[state.sort];
        (s ? items.slice().sort(s) : items).forEach((el) => grid.appendChild(el));
        if (countEl) countEl.textContent = `${shown} ${shown === 1 ? 'style' : 'styles'}`;
        if (emptyEl) emptyEl.classList.toggle('hidden', shown !== 0);
        syncUrl();
    };

    pills.forEach((p) => p.addEventListener('click', () => {
        pills.forEach((x) => x.classList.remove('is-active'));
        p.classList.add('is-active');
        state.filter = p.dataset.filter;
        apply();
    }));
    colorSel?.addEventListener('change', () => { state.color = colorSel.value; apply(); });
    categorySel?.addEventListener('change', () => { state.category = categorySel.value; apply(); });
    sortSel?.addEventListener('change', () => { state.sort = sortSel.value; apply(); });
    resetBtn?.addEventListener('click', () => {
        state.filter = 'all'; state.color = ''; state.category = ''; state.sort = 'newest';
        pills.forEach((p) => p.classList.toggle('is-active', p.dataset.filter === 'all'));
        if (colorSel) colorSel.value = '';
        if (categorySel) categorySel.value = '';
        if (sortSel) sortSel.value = 'newest';
        apply();
    });

    // Stagger the grid in on first view.
    if (!reducedMotion && typeof inView === 'function') {
        items.forEach((el, i) => {
            el.style.opacity = '0';
            inView(el, () => {
                setTimeout(() => {
                    el.style.opacity = '';
                    el.classList.add('is-entering');
                }, (i % 8) * 60);
            }, { amount: 0.05 });
        });
        // never leave the grid invisible
        setTimeout(() => items.forEach((el) => { el.style.opacity = ''; }), 2500);
    }

    const params = new URLSearchParams(window.location.search);
    if (params.get('filter')) {
        state.filter = params.get('filter');
        pills.forEach((p) => p.classList.toggle('is-active', p.dataset.filter === state.filter));
    }
    if (params.get('color') && colorSel) { state.color = params.get('color'); colorSel.value = state.color; }
    if (params.get('category') && categorySel) { state.category = params.get('category'); categorySel.value = state.category; }
    if (params.get('sort') && sortSel) { state.sort = params.get('sort'); sortSel.value = state.sort; }
    if (params.toString()) apply();
}

function initTrendingRail() {
    const rail = document.querySelector('[data-trend-rail]');
    if (!rail) return;
    const step = () => Math.min(rail.clientWidth * 0.86, 360);
    document.querySelector('[data-trend-prev]')?.addEventListener('click', () => {
        rail.scrollBy({ left: -step(), behavior: 'smooth' });
    });
    document.querySelector('[data-trend-next]')?.addEventListener('click', () => {
        rail.scrollBy({ left: step(), behavior: 'smooth' });
    });
}

function initPage() {
    initReveals();
    initHero();
    initProductGalleries();
    initProductDetailGallery();
    initColorPicker();
    initDashboardTabs();
    initShopFilters();
    initTrendingRail();
}

/* ================================================================== */
/* Magnetic hover — buttons and icon links drift gently toward the cursor */
/* ================================================================== */
(function magneticHover() {
    if (reducedMotion || !finePointer) return;
    const SELECTOR = '.button-dark, .button-light, .header-icon-link, .social-icon';
    let current = null;
    const release = (el) => { if (el) el.style.transform = ''; };
    document.addEventListener('mousemove', (event) => {
        const el = event.target.closest(SELECTOR);
        if (el !== current) { release(current); current = el; }
        if (!el) return;
        const r = el.getBoundingClientRect();
        const dx = (event.clientX - (r.left + r.width / 2)) * 0.25;
        const dy = (event.clientY - (r.top + r.height / 2)) * 0.25;
        el.style.transform = `translate(${dx}px, ${dy}px)`;
    });
    document.addEventListener('mouseout', (event) => {
        if (current && !event.relatedTarget?.closest?.(SELECTOR)) { release(current); current = null; }
    });
})();

/* ==================================================================
 * CLIENT-SIDE ROUTER — navigate without reloading the page
 * ================================================================== */
(function router() {
    const main = () => document.querySelector('.site-main');
    if (!main()) return;

    const sameOrigin = (url) => {
        try { return new URL(url, location.href).origin === location.origin; }
        catch { return false; }
    };

    // Anything that must hit the server normally.
    const isPlainNav = (a) =>
        a.target === '_blank' ||
        a.hasAttribute('download') ||
        a.getAttribute('href')?.startsWith('#') ||
        a.getAttribute('href')?.startsWith('mailto:') ||
        a.getAttribute('href')?.startsWith('tel:') ||
        a.dataset.noRouter !== undefined ||
        a.closest('[data-no-router]') !== null ||
        /\/(admin|login|logout|register)(\/|$)/.test(new URL(a.href, location.href).pathname);

    const cache = new Map();
    let navToken = 0;

    const veil = document.getElementById('route-veil');
    const veilMinDelay = () => new Promise((resolve) => setTimeout(resolve, reducedMotion ? 0 : 460));

    async function load(url, { push = true, scrollTop = true } = {}) {
        const token = ++navToken;
        document.body.classList.add('is-navigating');
        if (veil) veil.dataset.phase = 'cover';

        try {
            const fetchPromise = (async () => {
                let html = cache.get(url);
                if (!html) {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'fetch' }, credentials: 'same-origin' });
                    if (!res.ok) throw new Error(String(res.status));
                    html = await res.text();
                    cache.set(url, html);
                }
                return html;
            })();

            const [html] = await Promise.all([fetchPromise, veilMinDelay()]);
            if (token !== navToken) return;

            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nextMain = doc.querySelector('.site-main');
            if (!nextMain) { window.location.href = url; return; }

            // Tear down anything the old page created (WebGL contexts, timers).
            teardown.forEach((fn) => { try { fn(); } catch (e) {} });
            teardown = [];

            const cur = main();
            cur.innerHTML = nextMain.innerHTML;
            cur.className = nextMain.className;
            document.title = doc.title;

            // The header is transparent on the homepage, solid elsewhere.
            const nextHeader = doc.getElementById('site-header');
            const header = document.getElementById('site-header');
            if (nextHeader && header) header.className = nextHeader.className;

            if (push) history.pushState({ url }, '', url);
            if (scrollTop) window.scrollTo({ top: 0, behavior: 'instant' in window ? 'instant' : 'auto' });

            document.body.classList.remove('motion-ready');
            initPage();
            document.body.classList.remove('is-navigating');

            requestAnimationFrame(() => { if (veil) veil.dataset.phase = 'reveal'; });
        } catch (e) {
            window.location.href = url;   // fall back to a real navigation
        }
    }

    document.addEventListener('click', (e) => {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const a = e.target.closest('a[href]');
        if (!a || !sameOrigin(a.href) || isPlainNav(a)) return;

        const url = new URL(a.href, location.href);
        if (url.pathname === location.pathname && url.search === location.search) return;

        e.preventDefault();
        window.__closeMenu?.();
        load(url.pathname + url.search);
    });

    window.addEventListener('popstate', (e) => {
        if (e.state?.url) load(e.state.url, { push: false });
    });

    history.replaceState({ url: location.pathname + location.search }, '', location.href);
})();

/* first paint */
initPage();
