/* ==================================================================
 * MorphSlider — WebGL image-morph transitions.
 * Ported from the React/ogl/gsap component to dependency-free WebGL 1
 * so it drops straight into a Blade + vanilla-JS project.
 * Modes: melt (0) · ripple (1) · shear (2) · swirl (3)
 * ================================================================== */

const TRANSITIONS = { melt: 0, ripple: 1, shear: 2, swirl: 3 };

const VERT = `
attribute vec2 position;
attribute vec2 uv;
varying vec2 vUv;
void main() {
  vUv = uv;
  gl_Position = vec4(position, 0.0, 1.0);
}
`;

const FRAG = `
precision highp float;

uniform sampler2D tCurrent;
uniform sampler2D tNext;
uniform vec2 uResolution;
uniform vec2 uCurrentSize;
uniform vec2 uNextSize;
uniform float uProgress;
uniform float uDir;
uniform int uMode;
uniform float uIntensity;
uniform float uScale;
uniform float uAberration;
uniform float uDrift;
uniform float uTime;
uniform float uReduce;
uniform vec2 uPointer;
uniform vec3 uOverlay;

varying vec2 vUv;

const float PI = 3.14159265359;

float hash11(float p) {
  p = fract(p * 0.1031);
  p *= p + 33.33;
  p *= p + p;
  return fract(p);
}

float hash21(vec2 p) {
  vec3 p3 = fract(vec3(p.xyx) * 0.1031);
  p3 += dot(p3, p3.yzx + 33.33);
  return fract((p3.x + p3.y) * p3.z);
}

float noise(vec2 p) {
  vec2 i = floor(p);
  vec2 f = fract(p);
  vec2 u = f * f * (3.0 - 2.0 * f);
  float a = hash21(i);
  float b = hash21(i + vec2(1.0, 0.0));
  float c = hash21(i + vec2(0.0, 1.0));
  float d = hash21(i + vec2(1.0, 1.0));
  return mix(mix(a, b, u.x), mix(c, d, u.x), u.y);
}

float fbm(vec2 p) {
  float v = 0.0;
  float a = 0.5;
  for (int i = 0; i < 5; i++) {
    v += a * noise(p);
    p *= 2.0;
    a *= 0.5;
  }
  return v;
}

mat2 rot(float a) {
  float s = sin(a);
  float c = cos(a);
  return mat2(c, -s, s, c);
}

vec2 coverUV(vec2 uv, vec2 res, vec2 img) {
  float rA = res.x / max(res.y, 1.0);
  float iA = img.x / max(img.y, 1.0);
  vec2 s = vec2(1.0);
  float ratio = rA / max(iA, 0.0001);
  if (ratio > 1.0) {
    s.y = 1.0 / ratio;
  } else {
    s.x = ratio;
  }
  return (uv - 0.5) * s + 0.5;
}

void main() {
  float p = clamp(uProgress, 0.0, 1.0);
  float env = sin(p * PI);

  vec2 uv = vUv;

  uv += vec2(sin(uTime * 0.25 + uv.y * 4.0), cos(uTime * 0.22 + uv.x * 4.0)) * uDrift * 0.008;
  uv = (uv - 0.5) * (1.0 - uDrift * 0.02 * sin(uTime * 0.4)) + 0.5;

  vec2 uvC = uv;
  vec2 uvN = uv;
  float m = smoothstep(0.0, 1.0, p);

  if (uReduce < 0.5) {
    if (uMode == 3) {
      vec2 c = uv - 0.5;
      float r = length(c);
      float ang = env * uIntensity * 3.5 * (1.0 - r);
      uvC = rot(ang) * c + 0.5;
      uvN = rot(-ang) * c + 0.5;
      m = smoothstep(0.0, 1.0, p);
    } else if (uMode == 1) {
      float d = distance(uv, uPointer);
      float ring = p * 1.6;
      float wave = sin((d - ring) * 30.0) * env;
      vec2 dir = normalize(uv - uPointer + 1e-4);
      vec2 disp = dir * wave * uIntensity * 0.25;
      uvC = uv + disp;
      uvN = uv + disp * 0.6;
      m = 1.0 - smoothstep(ring - 0.03, ring + 0.03, d);
    } else if (uMode == 2) {
      float slices = 14.0;
      float row = floor(uv.y * slices);
      float rnd = hash11(row);
      vec2 disp = vec2((rnd - 0.5) * env * uIntensity * 0.6, 0.0);
      uvC = uv + disp;
      uvN = uv + disp;
      float localX = uDir > 0.0 ? uv.x : 1.0 - uv.x;
      float th = p * 1.5 - 0.25 + (rnd - 0.5) * 0.25;
      m = 1.0 - smoothstep(th - 0.06, th + 0.06, localX);
    } else {
      float nn = fbm(uv * uScale + uTime * 0.03);
      float warp = fbm(uv * uScale * 1.7 - uTime * 0.02);
      vec2 g = vec2(nn, warp) - 0.5;
      uvC = uv + g * uIntensity * 0.5 * p;
      uvN = uv - g * uIntensity * 0.5 * (1.0 - p);
      m = smoothstep(nn - 0.15, nn + 0.15, p);
    }
  }

  vec2 sC = coverUV(uvC, uResolution, uCurrentSize);
  vec2 sN = coverUV(uvN, uResolution, uNextSize);

  float ca = uReduce < 0.5 ? uAberration * env * 0.03 : 0.0;

  vec3 colC = vec3(
    texture2D(tCurrent, sC + vec2(ca, 0.0)).r,
    texture2D(tCurrent, sC).g,
    texture2D(tCurrent, sC - vec2(ca, 0.0)).b
  );
  vec3 colN = vec3(
    texture2D(tNext, sN + vec2(ca, 0.0)).r,
    texture2D(tNext, sN).g,
    texture2D(tNext, sN - vec2(ca, 0.0)).b
  );

  vec3 col = mix(colC, colN, m);

  float vig = smoothstep(1.25, 0.25, length(uv - 0.5));
  col = mix(col, uOverlay, (1.0 - vig) * 0.28);

  gl_FragColor = vec4(col, 1.0);
}
`;

function hexToRgb(hex) {
    let h = (hex || '#000000').replace('#', '');
    if (h.length === 3) h = h.split('').map((c) => c + c).join('');
    const n = parseInt(h, 16);
    return [((n >> 16) & 255) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255];
}

function compile(gl, type, src) {
    const sh = gl.createShader(type);
    gl.shaderSource(sh, src);
    gl.compileShader(sh);
    if (!gl.getShaderParameter(sh, gl.COMPILE_STATUS)) {
        console.warn('[morph] shader error:', gl.getShaderInfoLog(sh));
        gl.deleteShader(sh);
        return null;
    }
    return sh;
}

// power2.inOut, matching the gsap default used in the original component
const easeInOut = (t) => (t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2);

export function createMorphSlider(container, options = {}) {
    const opts = {
        images: [],
        transition: 'melt',
        duration: 1.1,
        intensity: 0.55,
        scale: 2.4,
        aberration: 0.35,
        drift: 0.4,
        overlayColor: '#05060a',
        autoplay: true,
        autoplayDelay: 5,
        loop: true,
        ...options,
    };

    const images = opts.images.filter(Boolean);
    if (!container || images.length === 0) return null;

    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const canvas = document.createElement('canvas');
    canvas.className = 'morph-canvas';
    container.appendChild(canvas);

    const gl = canvas.getContext('webgl', { alpha: false, antialias: true, powerPreference: 'high-performance' })
        || canvas.getContext('experimental-webgl');

    // No WebGL? Fall back to a plain cross-fade so the hero still works.
    if (!gl) {
        canvas.remove();
        return fallbackFade(container, images, opts);
    }

    const vs = compile(gl, gl.VERTEX_SHADER, VERT);
    const fs = compile(gl, gl.FRAGMENT_SHADER, FRAG);
    if (!vs || !fs) {
        canvas.remove();
        return fallbackFade(container, images, opts);
    }

    const prog = gl.createProgram();
    gl.attachShader(prog, vs);
    gl.attachShader(prog, fs);
    gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) {
        canvas.remove();
        return fallbackFade(container, images, opts);
    }
    gl.useProgram(prog);

    // Full-screen triangle
    const posBuf = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, posBuf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    const aPos = gl.getAttribLocation(prog, 'position');
    gl.enableVertexAttribArray(aPos);
    gl.vertexAttribPointer(aPos, 2, gl.FLOAT, false, 0, 0);

    const uvBuf = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, uvBuf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0, 0, 2, 0, 0, 2]), gl.STATIC_DRAW);
    const aUv = gl.getAttribLocation(prog, 'uv');
    gl.enableVertexAttribArray(aUv);
    gl.vertexAttribPointer(aUv, 2, gl.FLOAT, false, 0, 0);

    const U = {};
    ['tCurrent', 'tNext', 'uResolution', 'uCurrentSize', 'uNextSize', 'uProgress', 'uDir',
        'uMode', 'uIntensity', 'uScale', 'uAberration', 'uDrift', 'uTime', 'uReduce',
        'uPointer', 'uOverlay'].forEach((n) => { U[n] = gl.getUniformLocation(prog, n); });

    function makeTex() {
        const t = gl.createTexture();
        gl.bindTexture(gl.TEXTURE_2D, t);
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, 1, 1, 0, gl.RGBA, gl.UNSIGNED_BYTE,
            new Uint8Array([18, 18, 20, 255]));
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
        return t;
    }

    const textures = images.map(makeTex);
    const sizes = images.map(() => [1, 1]);

    images.forEach((src, i) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
            gl.bindTexture(gl.TEXTURE_2D, textures[i]);
            gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
            gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, img);
            gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
            gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
            gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
            gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
            sizes[i] = [img.naturalWidth || 1, img.naturalHeight || 1];
        };
        img.onerror = () => {};
        img.src = src;
    });

    let current = 0;
    let progress = 0;
    let animating = false;
    let dir = 1;
    let nextIndex = 0;
    let raf = null;
    let autoTimer = null;
    let destroyed = false;
    let paused = false;
    const pointer = [0.5, 0.5];
    const listeners = [];

    const wrap = (i) => ((i % images.length) + images.length) % images.length;

    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const r = container.getBoundingClientRect();
        const w = Math.max(1, Math.round(r.width * dpr));
        const h = Math.max(1, Math.round(r.height * dpr));
        if (canvas.width !== w || canvas.height !== h) {
            canvas.width = w;
            canvas.height = h;
            gl.viewport(0, 0, w, h);
        }
    }

    const ro = new ResizeObserver(resize);
    ro.observe(container);
    resize();

    function render(tMs) {
        if (destroyed) return;
        const t = tMs * 0.001;

        gl.useProgram(prog);
        gl.activeTexture(gl.TEXTURE0);
        gl.bindTexture(gl.TEXTURE_2D, textures[current]);
        gl.uniform1i(U.tCurrent, 0);
        gl.activeTexture(gl.TEXTURE1);
        gl.bindTexture(gl.TEXTURE_2D, textures[animating ? nextIndex : current]);
        gl.uniform1i(U.tNext, 1);

        gl.uniform2f(U.uResolution, canvas.width, canvas.height);
        gl.uniform2f(U.uCurrentSize, sizes[current][0], sizes[current][1]);
        const ns = sizes[animating ? nextIndex : current];
        gl.uniform2f(U.uNextSize, ns[0], ns[1]);
        gl.uniform1f(U.uProgress, progress);
        gl.uniform1f(U.uDir, dir);
        gl.uniform1i(U.uMode, TRANSITIONS[opts.transition] ?? 0);
        gl.uniform1f(U.uIntensity, opts.intensity);
        gl.uniform1f(U.uScale, opts.scale);
        gl.uniform1f(U.uAberration, opts.aberration);
        gl.uniform1f(U.uDrift, opts.drift);
        gl.uniform1f(U.uTime, t);
        gl.uniform1f(U.uReduce, reduce ? 1 : 0);
        gl.uniform2f(U.uPointer, pointer[0], pointer[1]);
        const o = hexToRgb(opts.overlayColor);
        gl.uniform3f(U.uOverlay, o[0], o[1], o[2]);

        gl.drawArrays(gl.TRIANGLES, 0, 3);
        raf = requestAnimationFrame(render);
    }
    raf = requestAnimationFrame(render);

    function go(d) {
        if (animating || destroyed || images.length < 2) return;
        if (!opts.loop) {
            const raw = current + d;
            if (raw < 0 || raw > images.length - 1) return;
        }
        dir = d;
        nextIndex = wrap(current + d);
        animating = true;
        const dur = (reduce ? Math.min(opts.duration, 0.4) : opts.duration) * 1000;
        const start = performance.now();

        (function step(now) {
            if (destroyed) return;
            const k = Math.min(1, (now - start) / dur);
            progress = easeInOut(k);
            if (k < 1) {
                requestAnimationFrame(step);
            } else {
                current = nextIndex;
                progress = 0;
                animating = false;
                emit();
            }
        })(start);
    }

    function emit() {
        listeners.forEach((fn) => fn(current, images.length));
    }

    function scheduleAuto() {
        if (!opts.autoplay || reduce) return;
        clearTimeout(autoTimer);
        autoTimer = setTimeout(function tick() {
            if (!destroyed && !paused) go(1);
            autoTimer = setTimeout(tick, Math.max(1, opts.autoplayDelay) * 1000);
        }, Math.max(1, opts.autoplayDelay) * 1000);
    }
    scheduleAuto();

    // Pause while off-screen or hovered, so it never burns cycles unseen.
    const io = new IntersectionObserver((es) => {
        es.forEach((e) => { paused = !e.isIntersecting; });
    }, { threshold: 0.1 });
    io.observe(container);

    const onMove = (e) => {
        const r = container.getBoundingClientRect();
        pointer[0] = (e.clientX - r.left) / Math.max(r.width, 1);
        pointer[1] = 1 - (e.clientY - r.top) / Math.max(r.height, 1);
    };
    container.addEventListener('pointermove', onMove);

    return {
        next: () => { go(1); scheduleAuto(); },
        prev: () => { go(-1); scheduleAuto(); },
        goTo: (i) => { if (i !== current) { go(i > current ? 1 : -1); scheduleAuto(); } },
        onChange: (fn) => { listeners.push(fn); fn(current, images.length); },
        get index() { return current; },
        get length() { return images.length; },
        destroy() {
            destroyed = true;
            cancelAnimationFrame(raf);
            clearTimeout(autoTimer);
            ro.disconnect();
            io.disconnect();
            container.removeEventListener('pointermove', onMove);
            textures.forEach((t) => gl.deleteTexture(t));
            gl.deleteProgram(prog);
            const ext = gl.getExtension('WEBGL_lose_context');
            if (ext) ext.loseContext();
            canvas.remove();
        },
    };
}

/* Plain cross-fade used when WebGL is unavailable. */
function fallbackFade(container, images, opts) {
    const layers = images.map((src, i) => {
        const d = document.createElement('div');
        d.className = 'morph-fallback' + (i === 0 ? ' is-active' : '');
        d.style.backgroundImage = `url('${src}')`;
        container.appendChild(d);
        return d;
    });
    let current = 0;
    let timer = null;
    const listeners = [];
    const show = (i) => {
        layers[current]?.classList.remove('is-active');
        current = ((i % layers.length) + layers.length) % layers.length;
        layers[current]?.classList.add('is-active');
        listeners.forEach((fn) => fn(current, layers.length));
    };
    const auto = () => {
        if (!opts.autoplay) return;
        clearInterval(timer);
        timer = setInterval(() => show(current + 1), Math.max(1, opts.autoplayDelay) * 1000);
    };
    auto();
    return {
        next: () => { show(current + 1); auto(); },
        prev: () => { show(current - 1); auto(); },
        goTo: (i) => { show(i); auto(); },
        onChange: (fn) => { listeners.push(fn); fn(current, layers.length); },
        get index() { return current; },
        get length() { return layers.length; },
        destroy() { clearInterval(timer); layers.forEach((l) => l.remove()); },
    };
}
