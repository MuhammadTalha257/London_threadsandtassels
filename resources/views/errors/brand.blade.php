{{-- Shared page for maintenance (503) and error pages. Fully self-contained
     (inline styles, no build assets) so it still shows when the rest of the
     site is broken. --}}
@php($refresh = $refresh ?? null)
@php($showRetry = $showRetry ?? false)
@php($showHome = $showHome ?? $showRetry)
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    @if($refresh)<meta http-equiv="refresh" content="{{ $refresh }}">@endif
    <title>{{ $heading }} — London Threads &amp; Tassels</title>
    <link rel="icon" type="image/png" href="/images/brand/logo-mark.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; }
        html, body { height: 100%; }
        body {
            min-height: 100svh; display: flex; align-items: center; justify-content: center; padding: 32px 20px;
            font-family: Inter, system-ui, -apple-system, 'Segoe UI', sans-serif; color: #fffcf9; text-align: center;
            background:
                radial-gradient(60% 50% at 50% 40%, rgba(176,23,47,.7), transparent 75%),
                repeating-linear-gradient(90deg, rgba(0,0,0,.12) 0 2px, transparent 2px 22px, rgba(255,255,255,.03) 22px 24px, transparent 24px 46px),
                linear-gradient(180deg, #3a0711, #5c0e1c 50%, #2a0610);
        }
        .ribbon { position: fixed; inset: 0 0 auto; height: 10px; background: radial-gradient(circle at 9px -2px, #d6a84e 0 6px, transparent 6.5px) 0 0 / 18px 10px repeat-x; }
        main { max-width: 560px; display: flex; flex-direction: column; align-items: center; }
        .mark { position: relative; width: min(70vw, 300px); aspect-ratio: 1; display: flex; align-items: center; justify-content: center; color: #efd08e; }
        .mark svg { position: absolute; inset: 0; width: 100%; height: 100%; }
        .spin { animation: spin 40s linear infinite; opacity: .7; }
        .spin-rev { animation: spin 28s linear infinite reverse; opacity: .5; }
        .mark::before { content: ''; position: absolute; inset: 20%; border-radius: 50%; background: radial-gradient(circle, rgba(239,208,142,.22), transparent 70%); }
        .mark img { position: relative; width: 62%; height: auto; filter: drop-shadow(0 0 14px rgba(239,208,142,.35)); }
        h1 { margin-top: 18px; font-family: 'Cormorant Garamond', Georgia, serif; font-weight: 300; font-size: clamp(34px, 7vw, 54px); line-height: 1.05; letter-spacing: -.01em; }
        p.msg { margin-top: 14px; max-width: 44ch; font-size: 16px; line-height: 1.6; color: rgba(255,252,249,.8); }
        .actions { margin-top: 28px; display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .btn { display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; padding: 13px 26px; font-size: 14px; font-weight: 600; text-decoration: none; transition: transform .3s ease; }
        .btn:hover { transform: translateY(-2px); }
        .btn-gold { color: #3a0711; background: linear-gradient(180deg, #f6dfa5, #d6a84e); box-shadow: 0 10px 30px rgba(0,0,0,.3); }
        .btn-line { color: #fff; border: 1px solid rgba(239,208,142,.6); }
        .btn:focus-visible { outline: 2px solid #fff; outline-offset: 3px; }
        .note { margin-top: 26px; font-size: 12px; letter-spacing: .06em; color: rgba(239,208,142,.7); }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) { .spin, .spin-rev { animation: none; } }
    </style>
</head>
<body>
    <span class="ribbon" aria-hidden="true"></span>
    <main>
        <div class="mark">
            <svg class="spin" viewBox="0 0 400 400" aria-hidden="true">
                <circle cx="200" cy="200" r="190" fill="none" stroke="currentColor" stroke-dasharray="2 9" stroke-linecap="round"/>
                @for($petal = 0; $petal < 24; $petal++)
                    <path d="M200 22 C206 34 206 44 200 54 C194 44 194 34 200 22Z" fill="currentColor" opacity=".55" transform="rotate({{ $petal * 15 }} 200 200)"/>
                @endfor
            </svg>
            <svg class="spin-rev" viewBox="0 0 400 400" aria-hidden="true">
                @for($dot = 0; $dot < 48; $dot++)
                    <circle cx="200" cy="58" r="{{ $dot % 2 ? 1.6 : 3 }}" fill="currentColor" transform="rotate({{ $dot * 7.5 }} 200 200)"/>
                @endfor
            </svg>
            <img src="/images/brand/logo-light.png" alt="London Threads &amp; Tassels">
        </div>
        <h1>{{ $heading }}</h1>
        <p class="msg">{{ $message }}</p>
        <div class="actions">
            @if($showRetry)
                <a class="btn btn-gold" href="javascript:location.reload()">Try again</a>
            @endif
            @if($showHome)
                <a class="btn {{ $showRetry ? 'btn-line' : 'btn-gold' }}" href="/">Back to the homepage</a>
            @endif
            @if(! $showRetry && ! $showHome)
                <a class="btn btn-gold" href="https://wa.me/442012345678" target="_blank" rel="noopener">Message us on WhatsApp</a>
            @endif
        </div>
        @if($refresh)
            <p class="note">This page checks again every minute and opens the shop as soon as we're back.</p>
        @endif
    </main>
</body>
</html>
