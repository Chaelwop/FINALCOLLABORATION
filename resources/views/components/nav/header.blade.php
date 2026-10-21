@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-50 border-b border-slate-200/60 bg-white/85 backdrop-blur-2xl transition-shadow duration-200" data-header>
    <div class="section flex h-[4.5rem] items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
            <span class="grid size-8 place-items-center rounded-lg bg-brand-600 text-white shadow-md shadow-brand-600/25">
                <x-icon name="sparkles" class="size-[18px]" />
            </span>
            <span class="font-display text-lg font-extrabold tracking-tight text-slate-900">
                Portfolio<span class="text-gradient">Craft</span>
            </span>
        </a>

        <div class="flex items-center gap-5 sm:gap-6" style="gap: 1.5rem">
            @auth
                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle aria-expanded="false" aria-haspopup="true"
                        class="flex min-h-11 items-center gap-2.5 rounded-2xl border border-slate-200 bg-white py-1.5 pr-3 pl-1.5 shadow-sm shadow-slate-900/5 transition hover:border-slate-300 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">
                        <span class="grid size-8 shrink-0 place-items-center overflow-hidden rounded-full bg-linear-to-br from-brand-500 to-violet-600 text-[11px] font-bold text-white ring-2 ring-white">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar }}" alt="" class="size-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </span>
                        <span class="hidden max-w-40 truncate text-sm font-semibold text-slate-700 sm:block">{{ $user->name }}</span>
                        <x-icon name="chevron-down" class="hidden size-4 shrink-0 text-slate-400 transition-transform sm:block" />
                    </button>

                    <div data-dropdown-menu
                        class="absolute right-0 z-50 mt-3 hidden w-72 overflow-hidden rounded-2xl border border-slate-200/80 bg-white/95 shadow-xl shadow-slate-900/10 backdrop-blur-xl">
                        <div class="border-b border-slate-100 bg-slate-50/70 px-4 py-3.5">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <div class="space-y-0.5 p-2">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                <x-icon name="squares-2x2" class="size-4 text-slate-400" /> Dashboard
                            </a>
                            <a href="{{ route('portfolios.create') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                <x-icon name="plus-circle" class="size-4 text-slate-400" /> New portfolio
                            </a>
                            <a href="{{ route('settings') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                <x-icon name="cog-6-tooth" class="size-4 text-slate-400" /> Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                                    <x-icon name="arrow-right-on-rectangle" class="size-4" /> Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hidden sm:inline-flex">Sign in</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" class="size-4" />
                    <span class="hidden sm:inline">Get started</span>
                </a>
            @endauth

        </div>
    </div>
</header>
