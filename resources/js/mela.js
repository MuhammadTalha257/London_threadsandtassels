/* ==================================================================
 * OFFER SLIDER + LANDING COLLAGE
 *
 * First screen: a full-bleed slider of the admin's offer banners
 * (diagonal wipe, tabs with progress, arrows, swipe, arrow keys).
 *
 * Once the visitor scrolls, scroll progress through the pinned section
 * (0 → 1) drives a Revolut-style landing — scroll scrubs time, so it
 * reverses cleanly:
 *
 *   0.01 – 0.58  the current slide shrinks from full screen into the
 *                tall slot in the middle of a collage
 *   0.10 – 0.75  the collage photos rise up from below around it,
 *                outer columns a little later than inner ones
 *   0.50 – 0.72  the headline and buttons arrive above
 *   0.80 – 1.00  everything drifts gently upward as the page moves on
 * ================================================================== */

const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
const seg = (p, a, b) => clamp((p - a) / (b - a));
const easeInOutCubic = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

const SLIDE_MS = 6500;
const WIPE_MS = 1250;

export function initMela({ onTeardown, reducedMotion }) {
    const section = document.querySelector('[data-mela]');
    if (!section) return;

    const sticky = section.querySelector('.mela__sticky');
    const slider = section.querySelector('[data-mela-slider]');
    const slides = Array.from(section.querySelectorAll('[data-mela-slide]'));
    const tabs = Array.from(section.querySelectorAll('[data-mela-dot]'));

    const cleanups = [];
    const listen = (target, type, fn, opts) => { target.addEventListener(type, fn, opts); cleanups.push(() => target.removeEventListener(type, fn, opts)); };

    /* ================= the offer slider ================= */

    let current = 0;
    let timer = 0;
    let leaveTimer = 0;
    let paused = false;
    let scrolledAway = false;
    section.style.setProperty('--slide-ms', `${SLIDE_MS}ms`);

    function show(index) {
        if (!slides.length) return;
        const next = (index + slides.length) % slides.length;
        if (next === current && slides[next].classList.contains('is-active')) { schedule(); return; }

        const prev = slides[current];
        clearTimeout(leaveTimer);
        slides.forEach((s) => s.classList.remove('is-leaving'));
        if (prev && prev !== slides[next]) {
            prev.classList.remove('is-active');
            prev.classList.add('is-leaving');
            leaveTimer = setTimeout(() => prev.classList.remove('is-leaving'), WIPE_MS + 50);
        }
        slides.forEach((slide, i) => {
            const active = i === next;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            slide.querySelectorAll('a, button').forEach((el) => { el.tabIndex = active ? 0 : -1; });
        });
        tabs.forEach((tab, i) => {
            tab.classList.toggle('is-active', i === next);
            tab.classList.toggle('is-done', i < next);
            tab.setAttribute('aria-selected', i === next ? 'true' : 'false');
            const bar = tab.querySelector('i');
            if (bar && i === next) { bar.style.animation = 'none'; void bar.offsetWidth; bar.style.animation = ''; }
        });
        current = next;
        schedule();
    }

    function schedule() {
        clearTimeout(timer);
        if (reducedMotion || paused || scrolledAway || slides.length < 2) return;
        timer = setTimeout(() => show(current + 1), SLIDE_MS);
    }

    function setPaused(value) {
        paused = value;
        slider.classList.toggle('is-paused', paused || scrolledAway);
        schedule();
    }

    section.querySelector('[data-mela-next]')?.addEventListener('click', () => show(current + 1));
    section.querySelector('[data-mela-prev]')?.addEventListener('click', () => show(current - 1));
    tabs.forEach((tab, i) => tab.addEventListener('click', () => show(i)));

    // Keyboard users get the slider held still while they're inside it.
    slider.addEventListener('focusin', (e) => { if (e.target.matches(':focus-visible')) setPaused(true); });
    slider.addEventListener('focusout', () => setPaused(false));
    slider.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') show(current + 1);
        if (e.key === 'ArrowLeft') show(current - 1);
    });

    let touchX = null;
    let touchY = null;
    slider.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; touchY = e.touches[0].clientY; }, { passive: true });
    slider.addEventListener('touchend', (e) => {
        if (touchX === null) return;
        const dx = e.changedTouches[0].clientX - touchX;
        const dy = e.changedTouches[0].clientY - touchY;
        if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.4) show(current + (dx < 0 ? 1 : -1));
        touchX = null;
    }, { passive: true });

    listen(document, 'visibilitychange', () => setPaused(document.hidden));

    slides[0]?.classList.add('is-active');
    schedule();

    if (reducedMotion) {
        section.classList.add('is-static');
        onTeardown(() => { clearTimeout(timer); clearTimeout(leaveTimer); cleanups.forEach((fn) => fn()); });
        return;
    }

    /* ================= the landing collage ================= */

    const collage = section.querySelector('[data-mela-collage]');
    const tiles = Array.from(section.querySelectorAll('[data-mela-tile]'));
    const lander = section.querySelector('[data-mela-lander]');
    const landerImg = section.querySelector('[data-mela-lander-img]');
    const headline = section.querySelector('[data-mela-headline]');

    let W = 0;
    let H = 0;
    let isMobile = false;
    let slot = { x: 0, y: 0, w: 0, h: 0 };
    let placed = [];
    let headlineTop = 0;
    let lastP = -1;
    let landerArmed = false;
    let startZoom = 1;

    /* Lay the collage out: a wide portrait slot in the middle for the
       slide to land in, with columns of two photos either side, each
       column sitting at a slightly different height. */
    function layout() {
        W = sticky.clientWidth;
        H = sticky.clientHeight;
        isMobile = W < 768;

        const gap = isMobile ? 8 : 14;
        const sideCols = isMobile ? 1 : 2;            // columns each side of the slot
        const units = sideCols * 2 + 1.55;            // the slot column is 1.55× wider
        const maxW = Math.min(W - (isMobile ? 24 : 64), 1240);

        // Measure the headline to leave room above the collage.
        headline.style.top = '0px';
        const headH = headline.offsetHeight;
        headlineTop = Math.max(H * 0.07, isMobile ? 64 : 72);
        const top = headlineTop + headH + (isMobile ? 22 : 30);
        const availH = H - top - (isMobile ? 34 : 40);

        const stagger = isMobile ? 26 : 46;
        let colW = (maxW - (sideCols * 2) * gap) / units;
        const neededH = (c) => 2 * c * 1.3 + gap + stagger * 2;
        if (neededH(colW) > availH) colW = (availH - gap - stagger * 2) / 2.6;
        const tileH = colW * 1.3;
        const slotW = colW * 1.55;
        const slotH = tileH * 2 + gap;
        const totalW = slotW + sideCols * 2 * (colW + gap);
        const left0 = (W - totalW) / 2;
        const top0 = top + stagger;

        // columns: left side (outer → inner), slot, right side (inner → outer)
        const cols = [];
        let x = left0;
        for (let i = sideCols; i >= 1; i--) { cols.push({ x, w: colW, dist: i }); x += colW + gap; }
        slot = { x, y: top0, w: slotW, h: slotH };
        x += slotW + gap;
        for (let i = 1; i <= sideCols; i++) { cols.push({ x, w: colW, dist: i }); x += colW + gap; }

        // Fill each side column with two tiles, alternating heights.
        placed = [];
        const order = [];
        cols.forEach((col, ci) => order.push({ col, ci }));
        let t = 0;
        for (let row = 0; row < 2; row++) {
            order.forEach(({ col, ci }) => {
                if (t >= tiles.length) return;
                const offset = (ci % 2 === 0 ? -1 : 1) * stagger * (col.dist === 1 ? 0.6 : 1);
                placed.push({
                    el: tiles[t],
                    x: col.x, y: top0 + offset + row * (tileH + gap), w: col.w, h: tileH,
                    dist: col.dist,
                    rise: H * (0.75 + col.dist * 0.18) + row * H * 0.12,
                });
                t++;
            });
        }
        tiles.forEach((el, i) => {
            const pl = placed.find((pp) => pp.el === el);
            el.style.display = pl ? '' : 'none';
            if (!pl) return;
            Object.assign(el.style, { width: `${pl.w}px`, height: `${pl.h}px` });
        });

        lastP = -1;
        render(progress());
    }

    /* Copy the current slide (and its slow zoom) onto the lander. */
    function armLander() {
        const slide = slides[current];
        if (!slide) return;
        const img = slide.querySelector('.mela__slide-img');
        landerImg.src = img?.currentSrc || img?.src || slide.dataset.image;
        const m = img ? getComputedStyle(img).transform : 'none';
        startZoom = 1;
        if (m && m !== 'none') {
            const v = m.match(/matrix\(([^,]+)/);
            if (v) startZoom = parseFloat(v[1]) || 1;
        }
        landerArmed = true;
    }

    function progress() {
        const rect = section.getBoundingClientRect();
        const total = section.offsetHeight - window.innerHeight;
        return total > 0 ? clamp(-rect.top / total) : 0;
    }

    function render(p) {
        if (Math.abs(p - lastP) < 0.0003) return;
        lastP = p;
        section.style.setProperty('--p', p.toFixed(4));

        const away = p > 0.008;
        if (away !== scrolledAway) {
            scrolledAway = away;
            slider.classList.toggle('is-paused', paused || scrolledAway);
            schedule();
        }

        // 1. Hand the slide over to the lander the moment scrolling starts.
        if (away && !landerArmed) armLander();
        if (!away) landerArmed = false;
        slider.style.opacity = away ? '0' : '';
        slider.style.pointerEvents = away ? 'none' : '';
        lander.style.visibility = away ? 'visible' : 'hidden';

        // 2. The lander shrinks from full screen into the slot.
        const d = easeInOutCubic(seg(p, 0.01, 0.58));
        const lx = slot.x * d;
        const ly = slot.y * d;
        const lw = W + (slot.w - W) * d;
        const lh = H + (slot.h - H) * d;
        Object.assign(lander.style, { left: `${lx}px`, top: `${ly}px`, width: `${lw}px`, height: `${lh}px`, borderRadius: `${d * 4}px` });
        landerImg.style.transform = `scale(${startZoom + (1 - startZoom) * seg(p, 0.01, 0.2)})`;
        lander.style.setProperty('--veil', (1 - seg(p, 0.01, 0.25)).toFixed(3));

        // 3. The collage rises up from below and settles around it.
        const settle = seg(p, 0.8, 1);
        placed.forEach((pl) => {
            const start = 0.1 + pl.dist * 0.05;
            const t = easeOutCubic(seg(p, start, start + 0.5));
            const y = pl.y + (1 - t) * pl.rise - settle * 20 * pl.dist;
            pl.el.style.opacity = String(seg(t, 0, 0.3));
            pl.el.style.transform = `translate3d(${pl.x}px, ${y}px, 0)`;
            const live = t > 0.95;
            pl.el.style.pointerEvents = live ? 'auto' : 'none';
            pl.el.tabIndex = live ? 0 : -1;
        });
        lander.style.transform = `translateY(${-settle * 10}px)`;

        // 4. The headline arrives above.
        const h = easeOutCubic(seg(p, 0.5, 0.72));
        headline.style.opacity = String(h);
        headline.style.transform = `translateY(${headlineTop + (1 - h) * 30}px)`;
        headline.style.pointerEvents = h > 0.6 ? 'auto' : 'none';
    }

    /* ---------- wiring ---------- */

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => { ticking = false; render(progress()); });
    };

    let lastW = window.innerWidth;
    const onResize = () => {
        // Ignore the phone URL bar showing/hiding (height-only changes).
        if (window.innerWidth === lastW && Math.abs(sticky.clientHeight - H) < 120) { onScroll(); return; }
        lastW = window.innerWidth;
        layout();
    };

    layout();
    if (document.fonts?.ready) document.fonts.ready.then(layout);
    listen(window, 'scroll', onScroll, { passive: true });
    listen(window, 'resize', onResize);

    onTeardown(() => {
        clearTimeout(timer);
        clearTimeout(leaveTimer);
        cleanups.forEach((fn) => fn());
    });
}

/* Festival offer countdown in the header ribbon. */
export function initCountdowns() {
    const timers = Array.from(document.querySelectorAll('[data-countdown]'));
    if (!timers.length) return;
    const pad = (n) => String(n).padStart(2, '0');

    const tick = () => {
        const now = Date.now();
        timers.forEach((timer) => {
            const end = Date.parse(timer.dataset.countdown);
            if (Number.isNaN(end)) return;
            let left = Math.max(0, Math.floor((end - now) / 1000));
            if (left === 0) { timer.closest('[data-festive]')?.setAttribute('hidden', ''); return; }
            const d = Math.floor(left / 86400); left -= d * 86400;
            const h = Math.floor(left / 3600); left -= h * 3600;
            const m = Math.floor(left / 60);
            const s = left - m * 60;
            const set = (key, v) => { const el = timer.querySelector(`[data-cd="${key}"]`); if (el && el.textContent !== v) el.textContent = v; };
            set('d', pad(d)); set('h', pad(h)); set('m', pad(m)); set('s', pad(s));
        });
    };
    tick();
    setInterval(tick, 1000);
}
