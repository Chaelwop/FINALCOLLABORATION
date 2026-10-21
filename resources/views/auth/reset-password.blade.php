<x-layouts.auth title="Choose a new password">
    <div class="mb-8 text-center">
        <h1 class="font-display text-3xl font-extrabold text-slate-900">Set a new password</h1>
        <p class="mt-2 text-sm text-slate-500">Choose something strong and unique.</p>
    </div>

    @if (session('status'))
        <div class="mb-6">
            <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-ui.input
            name="email"
            label="Email address"
            type="email"
            icon="envelope"
            :value="old('email', $email)"
            autocomplete="email"
            required />

        <x-ui.input
            name="password"
            label="New password"
            type="password"
            icon="lock-closed"
            placeholder="At least 8 characters"
            autocomplete="new-password"
            required />

        <x-ui.input
            name="password_confirmation"
            label="Confirm new password"
            type="password"
            icon="key"
            placeholder="Repeat your password"
            autocomplete="new-password"
            required />

        <button type="submit" class="btn btn-primary btn-lg w-full" data-loading-button>
            <span data-loading-label>Update password</span>
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>
</x-layouts.auth>
