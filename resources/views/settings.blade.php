<x-layouts.app title="Account settings">
    <div class="section py-10 sm:py-14">
        <div class="mx-auto max-w-3xl">
            <p class="eyebrow">
                <x-icon name="cog-6-tooth" class="size-3.5" /> Settings
            </p>
            <h1 class="mt-3 font-display text-3xl font-extrabold text-slate-900 sm:text-4xl">
                Account settings
            </h1>
            <p class="mt-2 text-sm text-slate-500">
                Manage your profile and the providers you can sign in with.
            </p>

            @if (session('status'))
                <div class="mt-6">
                    <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
                </div>
            @endif

            <section class="card mt-8 p-6">
                <h2 class="font-display text-lg font-bold text-slate-900">Profile</h2>

                <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-center">
                    <span class="grid size-16 shrink-0 place-items-center overflow-hidden rounded-2xl bg-linear-to-br from-brand-500 to-violet-600 text-xl font-bold text-white shadow-lg shadow-brand-500/25">
                        @if ($user->avatar)
                            <img src="{{ $user->avatar }}" alt="" class="size-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </span>

                    <div class="min-w-0">
                        <p class="truncate font-display text-lg font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            Member since {{ $user->created_at?->format('F Y') ?? 'today' }}
                        </p>
                    </div>
                </div>
            </section>

            @if ($twoFactorAvailable)
                <section class="card mt-6 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="font-display text-lg font-bold text-slate-900">Two factor authentication</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Require a code sent to your phone every time you sign in.
                            </p>
                        </div>
                        <span class="badge {{ $twoFactorStatus->badge() }} shrink-0">{{ $twoFactorStatus->label() }}</span>
                    </div>

                @if ($twoFactorStatus === \App\Enums\TwoFactorStatus::Enabled)
                    <div class="mt-5 flex flex-col gap-4 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                                <x-icon name="shield-check" class="size-5" />
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $user->maskedPhone() }}</p>
                                <p class="text-xs text-slate-500">
                                    A fresh code is sent to this number on every sign-in.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('settings.two-factor.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary btn-md">Turn off</button>
                        </form>
                    </div>
                @else
                    @if ($twoFactorStatus === \App\Enums\TwoFactorStatus::Pending)
                        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50/60 p-4">
                            <p class="text-sm font-semibold text-amber-800">
                                Enter the code we sent to {{ $user->maskedPhone() }} to finish.
                            </p>

                            <form method="POST" action="{{ route('settings.two-factor.confirm') }}" class="mt-3 flex flex-col gap-3 sm:flex-row">
                                @csrf
                                <input name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                                    maxlength="{{ config('security.two_factor.code_length') }}" placeholder="123456" required
                                    class="input sm:max-w-[11rem] @error('code') input-error @enderror">
                                <button type="submit" class="btn btn-primary btn-md" data-loading-button>
                                    <span data-loading-label>Confirm code</span>
                                </button>
                            </form>

                            @error('code')
                                <p class="field-error"><x-icon name="exclamation-triangle" class="size-3.5" /> {{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.two-factor.store') }}" class="mt-5 space-y-4">
                        @csrf

                        <x-ui.input
                            name="phone"
                            label="Phone number"
                            type="tel"
                            icon="phone"
                            placeholder="+1 555 010 9999"
                            autocomplete="tel"
                            :value="$user->phone"
                            hint="We only use this number to send verification codes."
                            required />

                        <button type="submit" class="btn btn-primary btn-md" data-loading-button>
                            <span data-loading-label>Send verification code</span>
                            <x-icon name="arrow-right" class="size-4" />
                        </button>
                    </form>
                @endif
                </section>
            @endif

            <section class="card mt-6 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-display text-lg font-bold text-slate-900">Connected providers</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Connect a provider to sign in with one click next time.
                        </p>
                    </div>
                    <span class="badge badge-brand shrink-0">{{ $connected->count() }} connected</span>
                </div>

                <div class="mt-5 space-y-2.5">
                    @foreach ($available as $provider)
                        @php
                            $account = $connected->firstWhere('provider', $provider->value);
                        @endphp

                        <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="grid size-10 shrink-0 place-items-center rounded-xl" style="color: {{ $provider->color() }}">
                                    <x-icon :name="$provider->icon()" class="size-5" />
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-900">{{ $provider->label() }}</p>
                                    @if ($account)
                                        <p class="truncate text-xs text-slate-500">
                                            Connected{{ $account->name ? ' as '.$account->name : '' }}
                                        </p>
                                    @elseif ($provider->isConfigured())
                                        <p class="truncate text-xs text-slate-500">Ready to connect</p>
                                    @else
                                        <p class="truncate text-xs text-amber-700">Add credentials to .env to enable</p>
                                    @endif
                                </div>
                            </div>

                            @if ($account)
                                <form method="POST" action="{{ route('settings.social.destroy', $account) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50">
                                        <x-icon name="trash" class="size-4" /> Disconnect
                                    </button>
                                </form>
                            @elseif ($provider->isConfigured())
                                <a href="{{ route('social.redirect', ['provider' => $provider->value]) }}" class="btn btn-secondary btn-sm">
                                    <x-icon name="link" class="size-4" /> Connect
                                </a>
                            @else
                                <span class="badge badge-neutral">Not configured</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="card mt-6 p-6">
                <h2 class="font-display text-lg font-bold text-slate-900">Session</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Sign out of this device. You can sign back in with your password or any connected provider.
                </p>

                <form method="POST" action="{{ route('logout') }}" class="mt-5">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-md">
                        <x-icon name="arrow-left-on-rectangle" class="size-4" /> Sign out
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
