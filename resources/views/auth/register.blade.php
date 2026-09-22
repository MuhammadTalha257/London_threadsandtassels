<x-layouts.store title="Create an account — London Threads & Tassels">
    <section class="mx-auto max-w-md px-5 py-20">
        <p class="mono-label text-signal-orange" data-motion>Join us</p>
        <h1 class="display mt-3 text-3xl" data-motion>CREATE AN ACCOUNT</h1>
        <form class="mt-9 space-y-5" method="POST" data-motion>
            @csrf
            <input class="field" name="name" value="{{ old('name') }}" placeholder="Full name" required autofocus>
            <input class="field" type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required>
            <input class="field" type="password" name="password" placeholder="Password (8+ characters)" required>
            <input class="field" type="password" name="password_confirmation" placeholder="Confirm password" required>
            @error('email')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
            <button class="button-dark w-full">Create account</button>
        </form>
        <p class="mt-6 text-xs">Already shopping with us? <a class="underline" href="{{ route('login') }}">Sign in</a></p>
    </section>
</x-layouts.store>
