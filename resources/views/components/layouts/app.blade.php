@props([
    'title' => null,
    'bodyClass' => '',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PortfolioCraft — build a polished, shareable portfolio in minutes and sign in with Google, GitHub and more.">

    <title>{{ $title ? $title.' · PortfolioCraft' : 'PortfolioCraft — Portfolio Builder' }}</title>

    <script>
        (function () {
            localStorage.removeItem('portfoliocraft-theme');
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        })();
    </script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased {{ $bodyClass }}">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-brand-600 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Skip to content
    </a>

    <x-nav.header />

    <main id="main" class="flex min-h-[calc(100vh-4rem)] flex-col">
        {{ $slot }}
    </main>

    <x-nav.footer />

    <div id="toast-stack" class="pointer-events-none fixed right-4 bottom-4 z-[60] flex w-full max-w-sm flex-col gap-2" style="width: calc(100vw - 2rem)" aria-live="polite" aria-atomic="true"></div>

    @stack('scripts')
</body>
</html>
