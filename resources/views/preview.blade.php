<x-layouts.app title="Preview — {{ $portfolio->full_name }}">
    <div class="no-print sticky top-16 z-40 border-b border-slate-200 bg-white/85 backdrop-blur-xl">
        <div class="section flex flex-wrap items-center justify-between gap-3 py-3">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-icon btn-sm" aria-label="Back to dashboard">
                    <x-icon name="arrow-left" class="size-4" />
                </a>
                <div class="min-w-0">
                    <p class="truncate font-display text-sm font-bold text-slate-900">{{ $portfolio->full_name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $portfolio->data['jobTitle'] ?? '' }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center rounded-xl bg-slate-100 p-1" role="group" aria-label="Template">
                    @foreach (['simple' => 'Simple', 'modern' => 'Modern', 'creative' => 'Creative'] as $key => $label)
                        <button type="button" data-preview-template="{{ $key }}"
                            class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ $portfolio->template === $key ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <button type="button" class="btn btn-secondary btn-sm" data-copy-link="{{ $portfolio->is_public ? route('portfolios.show', $portfolio) : '' }}"
                    @disabled(! $portfolio->is_public)
                    title="{{ $portfolio->is_public ? 'Copy public link' : 'Publish this portfolio to share it' }}">
                    <x-icon name="link" class="size-4" />
                    <span class="hidden sm:inline">{{ $portfolio->is_public ? 'Copy link' : 'Not published' }}</span>
                </button>

                <a href="{{ route('portfolios.edit', $portfolio) }}" class="btn btn-secondary btn-sm">
                    <x-icon name="pencil-square" class="size-4" /> Edit
                </a>

                <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
                    <x-icon name="printer" class="size-4" /> PDF
                </button>

                <button type="button" class="btn btn-primary btn-sm" data-export-card
                    data-name="{{ $portfolio->full_name }}"
                    data-title="{{ $portfolio->data['jobTitle'] ?? '' }}"
                    data-email="{{ $portfolio->email }}"
                    data-location="{{ $portfolio->data['address'] ?? '' }}"
                    data-skills="{{ implode(',', $portfolio->data['skills'] ?? []) }}"
                    data-template="{{ $portfolio->template }}">
                    <x-icon name="document-arrow-down" class="size-4" /> PNG
                </button>
            </div>
        </div>
    </div>

    <div class="section py-8">
        <script type="application/json" id="portfolio-data">{!! json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

        <div data-preview-canvas data-preview-template-source="{{ $portfolio->template }}" data-portfolio-id="{{ $portfolio->id }}">
            <x-portfolio.fragment :template="$portfolio->template" :portfolio="$payload" />
        </div>
    </div>

    <x-ui.modal id="export-modal" title="Download snapshot card">
        <p class="text-sm text-slate-600">
            Your portfolio is rendered on a high resolution canvas. Download it as a PNG to share on social media.
        </p>

        <div class="mt-4 flex justify-center overflow-x-auto rounded-xl border border-slate-200 bg-slate-100 p-4">
            <canvas data-export-canvas class="h-auto max-w-full rounded-lg shadow-md"></canvas>
        </div>

        <div class="mt-5 flex justify-end gap-3">
            <button type="button" class="btn btn-secondary btn-sm" data-modal-close="export-modal">Close</button>
            <a href="#" download="portfolio-card.png" data-export-download class="btn btn-primary btn-sm">
                <x-icon name="document-arrow-down" class="size-4" /> Download PNG
            </a>
        </div>
    </x-ui.modal>
</x-layouts.app>
