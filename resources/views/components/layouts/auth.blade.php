@props([
    'title' => null,
    'subtitle' => null,
])

@php
    $providers = \App\Enums\SocialProvider::configured();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' · PortfolioCraft' : 'PortfolioCraft' }}</title>

    <script>
        (function () {
            localStorage.removeItem('portfoliocraft-theme');
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        })();
    </script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        <aside class="relative hidden overflow-hidden border-r border-slate-200 bg-slate-50 p-10 text-slate-900 lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute inset-0 opacity-80" style="background:
                radial-gradient(40rem 30rem at 10% 10%, rgba(9,96,194,.12), transparent 60%),
                radial-gradient(35rem 28rem at 90% 20%, rgba(118,68,255,.10), transparent 60%),
                radial-gradient(30rem 25rem at 60% 100%, rgba(126,188,226,.18), transparent 60%);"></div>
            <div class="pointer-events-none absolute -top-24 -left-16 size-96 rounded-full bg-brand-500/10 blur-3xl"></div>
            <div class="pointer-events-none absolute right-0 bottom-0 size-80 rounded-full bg-violet-500/10 blur-3xl"></div>

            <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-xl bg-linear-to-br from-brand-400 to-brand-600 text-white shadow-lg shadow-brand-600/30">
                    <x-icon name="sparkles" class="size-[18px]" />
                </span>
                <span class="font-display text-lg font-extrabold tracking-tight">Portfolio<span class="text-brand-600">Craft</span></span>
            </a>

            <div class="relative z-10 max-w-md">
                <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-brand-500/20 bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
                    <x-icon name="bolt" class="size-3.5 text-amber-500" /> One account, every template
                </p>
                <h2 class="font-display text-4xl leading-tight font-extrabold">
                    Your work deserves a <span class="text-gradient">showcase</span> that feels like you.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">
                    Build a polished portfolio, switch between three handcrafted templates, and publish a shareable link — all from one calm workspace.
                </p>

                <ul class="mt-8 space-y-3 text-sm text-slate-700">
                    @foreach ([
                        ['sparkles', 'Three crafted templates'],
                        ['cloud-arrow-up', 'Autosaved drafts & instant preview'],
                        ['share', 'Publish a public link in one click'],
                    ] as [$icon, $text])
                        <li class="flex items-center gap-3">
                            <span class="grid size-7 place-items-center rounded-lg bg-white text-brand-600 shadow-sm ring-1 ring-slate-200">
                                <x-icon :name="$icon" class="size-4" />
                            </span>
                            {{ $text }}
                        </li>
                    @endforeach
                </ul>
            </div>

        </aside>

        <main class="flex flex-col">
            <div class="flex items-center justify-between px-6 py-5 lg:justify-end">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 lg:hidden">
                    <span class="grid size-9 place-items-center rounded-xl bg-linear-to-br from-brand-500 to-brand-700 text-white">
                        <x-icon name="sparkles" class="size-[18px]" />
                    </span>
                    <span class="font-display text-lg font-extrabold tracking-tight text-slate-900">Portfolio<span class="text-gradient">Craft</span></span>
                </a>
            </div>

            <div class="flex flex-1 items-center justify-center px-5 pb-14">
                <div class="w-full max-w-[26rem]">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <div id="toast-stack" class="pointer-events-none fixed right-4 bottom-4 z-[60] flex w-full max-w-sm flex-col gap-2" style="width: calc(100vw - 2rem)" aria-live="polite"></div>
</body>
</html>
