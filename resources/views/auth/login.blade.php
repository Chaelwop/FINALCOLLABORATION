@php
    $configured = \App\Enums\SocialProvider::configured();
@endphp

<x-layouts.auth title="Sign in">
    <div class="mb-8 text-center">
        <h1 class="font-display text-3xl font-extrabold text-slate-900">Welcome back</h1>
        <p class="mt-2 text-sm text-slate-500">Sign in to keep building your portfolio.</p>
    </div>

    @if (session('status'))
        <div class="mb-6">
            <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    @if ($errors->has('social'))
        <div class="mb-6">
            <x-ui.alert type="error" title="Sign-in problem">{{ $errors->first('social') }}</x-ui.alert>
        </div>
    @endif

    @if (count($configured) > 0)
        <div class="grid gap-2.5">
            @foreach ($configured as $provider)
                <x-social.provider-button :provider="$provider" />
            @endforeach
        </div>

        <div class="my-6 flex items-center gap-4">
            <span class="h-px flex-1 bg-slate-200"></span>
            <span class="text-xs font-semibold tracking-wide text-slate-400 uppercase">or use email</span>
            <span class="h-px flex-1 bg-slate-200"></span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4" novalidate>
        @csrf

        <x-ui.input
            name="email"
            label="Email address"
            type="email"
            icon="envelope"
            placeholder="you@example.com"
            autocomplete="email"
            :value="old('email')"
            required />

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="label mb-0">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 transition hover:text-brand-700">Forgot password?</a>
            </div>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-slate-400">
                    <x-icon name="lock-closed" class="size-[18px]" />
                </span>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    placeholder="••••••••"
                    class="input pl-10 pr-11 @error('password') input-error @enderror">
                <button type="button" data-password-toggle="password"
                    class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 transition hover:text-slate-600"
                    aria-label="Show password">
                    <x-icon name="eye" class="size-[18px] data-eye-open" />
                    <x-icon name="eye-slash" class="hidden size-[18px] data-eye-closed" />
                </button>
            </div>
            @error('password')
                <p class="field-error"><x-icon name="exclamation-triangle" class="size-3.5" /> {{ $message }}</p>
            @enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1" class="checkbox rounded-md">
            Keep me signed in
        </label>

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading-button>
            <span data-loading-label>Sign in</span>
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        New to PortfolioCraft?
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 transition hover:text-brand-700">Create an account</a>
    </p>

</x-layouts.auth>
