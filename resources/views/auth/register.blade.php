@php
    $configured = \App\Enums\SocialProvider::configured();
@endphp

<x-layouts.auth title="Create your account">
    <div class="mb-8 text-center">
        <h1 class="font-display text-3xl font-extrabold text-slate-900">Create your account</h1>
        <p class="mt-2 text-sm text-slate-500">Free forever. Your portfolios stay in your dashboard.</p>
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
                <x-social.provider-button :provider="$provider" label="Sign up with {{ $provider->label() }}" />
            @endforeach
        </div>

        <div class="my-6 flex items-center gap-4">
            <span class="h-px flex-1 bg-slate-200"></span>
            <span class="text-xs font-semibold tracking-wide text-slate-400 uppercase">or use email</span>
            <span class="h-px flex-1 bg-slate-200"></span>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4" novalidate>
        @csrf

        <x-ui.input
            name="name"
            label="Full name"
            type="text"
            icon="user"
            placeholder="Alex Morgan"
            autocomplete="name"
            :value="old('name')"
            required />

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
            <label for="password" class="label">Password <span class="text-rose-600">*</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-slate-400">
                    <x-icon name="lock-closed" class="size-[18px]" />
                </span>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    placeholder="At least 8 characters"
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

        <x-ui.input
            name="password_confirmation"
            label="Confirm password"
            type="password"
            icon="key"
            placeholder="Repeat your password"
            autocomplete="new-password"
            required />

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading-button>
            <span data-loading-label>Create account</span>
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 transition hover:text-brand-700">Sign in</a>
    </p>
</x-layouts.auth>
