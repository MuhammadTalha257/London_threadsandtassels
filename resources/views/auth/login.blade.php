<x-layouts.store title="Sign in — London Threads & Tassels">
    <section class="mx-auto max-w-md px-5 py-20">
        <p class="mono-label text-signal-orange" data-motion>Welcome back</p>
        <h1 class="display mt-3 text-3xl" data-motion>SIGN IN</h1>
        <form class="mt-9 space-y-5" method="POST" data-motion>
            @csrf
            <input class="field" type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
            <input class="field" type="password" name="password" placeholder="Password" required>
            <label class="flex items-center gap-2 text-xs text-ink/60"><input type="checkbox" name="remember"> Keep me signed in</label>
            @error('email')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
            <button class="button-dark w-full">Sign in</button>
        </form>
        <p class="mt-6 text-xs">New here? <a class="underline" href="{{ route('register') }}">Create an account</a></p>
    </section>
</x-layouts.store>
