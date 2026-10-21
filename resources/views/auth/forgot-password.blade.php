<x-layouts.auth title="Reset your password">
    <div class="mb-8 text-center">
        <h1 class="font-display text-3xl font-extrabold text-slate-900">Forgot your password?</h1>
        <p class="mt-2 text-sm text-slate-500">Enter your email and we will send you a reset link.</p>
    </div>

    @if (session('status'))
        <div class="mb-6">
            <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading-button>
            <span data-loading-label>Email reset link</span>
            <x-icon name="paper-airplane" class="size-4" />
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 transition hover:text-brand-700">Back to sign in</a>
    </p>
</x-layouts.auth>
