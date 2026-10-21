<x-layouts.app title="{{ $portfolio->full_name }}">
    <div class="section py-10">
        <div data-preview-canvas data-preview-template-source="{{ $portfolio->template }}">
            <x-portfolio.fragment :template="$portfolio->template" :portfolio="$payload" />
        </div>
    </div>

    <div class="no-print fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/90 backdrop-blur-xl">
        <div class="section flex flex-col items-center justify-between gap-3 py-3.5 sm:flex-row">
            <p class="text-sm text-slate-600">
                Like what you see? Build your own in minutes.
            </p>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Explore templates</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                    <x-icon name="sparkles" class="size-4" /> Create your portfolio
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
