<x-layouts.auth title="Verify your identity">
    <div class="mb-8 text-center">
        <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-brand-50 text-brand-600">
            <x-icon name="device-phone-mobile" class="size-7" />
        </span>
        <h1 class="mt-5 font-display text-3xl font-extrabold text-slate-900">Verify it's you</h1>
        <p class="mt-2 text-sm text-slate-500">
            We sent a {{ config('security.two_factor.code_length') }}-digit code to
            <span class="font-semibold text-slate-700">{{ $user->maskedPhone() }}</span>.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6">
            <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4" novalidate>
        @csrf

        <div>
            <label for="code" class="label">Verification code</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                maxlength="{{ config('security.two_factor.code_length') }}" placeholder="123456" required
                class="input text-center text-lg font-bold tracking-[0.45em] @error('code') input-error @enderror">
            @error('code')
                <p class="field-error"><x-icon name="exclamation-triangle" class="size-3.5" /> {{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading-button>
            <span data-loading-label>Verify and sign in</span>
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 text-sm">
        <form method="POST" action="{{ route('two-factor.resend') }}">
            @csrf
            <button type="submit" class="font-semibold text-brand-600 transition hover:text-brand-700 disabled:pointer-events-none disabled:opacity-50"
                @disabled($resendIn > 0)>
                Send a new code
            </button>
        </form>

        <a href="{{ route('login') }}" class="text-slate-500 transition hover:text-slate-700">
            Use a different account
        </a>
    </div>

    @if ($resendIn > 0)
        <p class="mt-2 text-right text-xs text-slate-400">You can request another code in {{ $resendIn }} seconds.</p>
    @endif
</x-layouts.auth>
