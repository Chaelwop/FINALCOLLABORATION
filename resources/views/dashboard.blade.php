<x-layouts.app title="Dashboard">
    <div class="section py-8 sm:py-10">
        {{-- Welcome header --}}
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <span class="grid size-14 shrink-0 place-items-center overflow-hidden rounded-2xl bg-linear-to-br from-brand-500 to-violet-600 text-lg font-bold text-white shadow-lg shadow-brand-500/30">
                    @if (auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="" class="size-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </span>
                <div>
                    <p class="eyebrow">
                        <x-icon name="calendar" class="size-3.5" />
                        @php
                            $hour = (int) now()->format('G');
                            $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
                        @endphp
                        {{ $greeting }}, {{ auth()->user()->name }}
                    </p>
                    <h1 class="mt-1 font-display text-3xl font-extrabold text-slate-900 sm:text-4xl">
                        Your portfolios
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $totalCount === 0 ? 'Nothing here yet — create your first one in under a minute.' : 'Everything you have created, all in one place.' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('portfolios.create') }}#template-zone" class="btn btn-secondary btn-md w-full sm:w-auto">
                    <x-icon name="eye" class="size-4" /> View templates
                </a>
                <a href="{{ route('portfolios.create') }}" class="btn btn-primary btn-md w-full sm:w-auto">
                    <x-icon name="plus" class="size-4" /> New portfolio
                </a>
            </div>
        </div>

        @if ($totalCount > 0)
            {{-- Stats --}}
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ([
                    ['squares-2x2', 'Total', $totalCount, 'bg-brand-50 text-brand-600', 'brand'],
                    ['share', 'Published', $publicCount, 'bg-emerald-50 text-emerald-600', 'emerald'],
                    ['document-text', 'Drafts', $draftCount, 'bg-amber-50 text-amber-600', 'amber'],
                    ['swatch', 'Templates', $templateCount, 'bg-violet-50 text-violet-600', 'violet'],
                ] as [$icon, $label, $value, $tone, $color])
                    <div class="card flex items-center gap-3 p-4 transition hover:-translate-y-0.5 hover:shadow-lift">
                        <span class="grid size-10 shrink-0 place-items-center rounded-xl {{ $tone }}">
                            <x-icon :name="$icon" class="size-5" />
                        </span>
                        <div class="min-w-0">
                            <p class="font-display text-xl font-extrabold text-slate-900">{{ $value }}</p>
                            <p class="text-xs text-slate-500">{{ $label }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Search + filter --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative w-full sm:max-w-sm">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-slate-400">
                        <x-icon name="magnifying-glass" class="size-[18px]" />
                    </span>
                    <input id="portfolio-search" type="search" placeholder="Search your portfolios…"
                        class="input pl-10" autocomplete="off">
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" data-filter="all" class="btn btn-secondary btn-sm filter-chip is-active">All</button>
                    <button type="button" data-filter="published" class="btn btn-secondary btn-sm filter-chip">Published</button>
                    <button type="button" data-filter="draft" class="btn btn-secondary btn-sm filter-chip">Drafts</button>
                </div>
            </div>

            {{-- Grid --}}
            <div id="portfolio-grid" class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($portfolios as $portfolio)
                    <x-portfolio.card
                        :portfolio="$portfolio"
                        :view-url="route('portfolios.preview', $portfolio)"
                        :edit-url="route('portfolios.edit', $portfolio)" />
                @endforeach
            </div>

            <div data-empty-state class="card mt-6 hidden p-8 text-center">
                <x-icon name="magnifying-glass" class="mx-auto size-8 text-slate-300" />
                <p class="mt-3 font-display text-lg font-bold text-slate-900">No matching portfolios</p>
                <p class="mt-1 text-sm text-slate-500">Try a different search term or filter.</p>
            </div>
        @else
            <div class="card mt-10 overflow-hidden">
                <div class="grid gap-8 p-8 sm:p-12 lg:grid-cols-2 lg:items-center">
                    <div>
                        <span class="grid size-14 place-items-center rounded-2xl bg-linear-to-br from-brand-500 to-violet-600 text-white shadow-lg shadow-brand-500/30">
                            <x-icon name="folder-open" class="size-6" />
                        </span>
                        <h2 class="mt-5 font-display text-2xl font-extrabold text-slate-900">
                            Your dashboard is ready
                        </h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">
                            Create a portfolio, choose a template, and publish a shareable link. Everything you save lives here, synced to your account.
                        </p>

                        <div class="mt-6 flex flex-col gap-2.5 sm:flex-row">
                            <a href="{{ route('portfolios.create') }}" class="btn btn-primary btn-md">
                                <x-icon name="plus" class="size-4" /> Create portfolio
                            </a>
                            <a href="{{ route('portfolios.create') }}#template-zone" class="btn btn-secondary btn-md">See the templates</a>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        @foreach ([
                            ['sparkles', 'Fill in your details', 'One guided form for your story, skills, projects and experience.'],
                            ['swatch', 'Pick a template', 'Switch between three crafted layouts whenever you like.'],
                            ['share', 'Publish and share', 'Flip one switch and send your public link anywhere.'],
                        ] as [$icon, $title, $blurb])
                            <div class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-white text-brand-600 shadow-sm">
                                    <x-icon :name="$icon" class="size-4" />
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $title }}</p>
                                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ $blurb }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($totalCount > 0)
        <script type="application/json" id="dashboard-portfolios">
            {!! json_encode($portfolios->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->full_name,
                'jobTitle' => $p->data['jobTitle'] ?? '',
                'template' => $p->template,
                'public' => $p->is_public,
                'updated' => $p->updated_at?->diffForHumans(),
            ])->values()) !!}
        </script>
    @endif

    <style>
        [data-delete-portfolio]:not(:disabled):hover,
        [data-delete-portfolio]:not(:disabled):focus-visible,
        [data-delete-portfolio]:not(:disabled):active {
            background-color: #e11d48 !important;
            color: #fff !important;
        }

        [data-delete-portfolio-confirm]:not(:disabled):hover,
        [data-delete-portfolio-confirm]:not(:disabled):focus-visible,
        [data-delete-portfolio-confirm]:not(:disabled):active {
            background-image: linear-gradient(to right, #e11d48, #be123c) !important;
            box-shadow: 0 10px 20px rgb(225 29 72 / 25%);
        }
    </style>

    <x-ui.modal id="delete-portfolio-modal" title="Delete portfolio?" max-width="max-w-md">
        <div class="space-y-5">
            <p class="text-sm leading-relaxed text-slate-600">
                You’re about to delete <span class="font-semibold text-slate-900" data-delete-portfolio-name>this portfolio</span>. This action cannot be undone.
            </p>
            <p class="hidden rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700" data-delete-portfolio-error></p>
            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" class="btn btn-secondary btn-md" data-modal-close="delete-portfolio-modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-md" data-delete-portfolio-confirm>
                    <span data-delete-confirm-label>Delete portfolio</span>
                    <x-icon name="trash" class="size-4" />
                </button>
            </div>
        </div>
    </x-ui.modal>

    <script>
        let portfolioToDelete = null;
        const deleteModal = document.getElementById('delete-portfolio-modal');
        const deleteName = document.querySelector('[data-delete-portfolio-name]');
        const deleteError = document.querySelector('[data-delete-portfolio-error]');
        const deleteConfirm = document.querySelector('[data-delete-portfolio-confirm]');
        const deleteConfirmLabel = document.querySelector('[data-delete-confirm-label]');

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-delete-portfolio]');
            if (!trigger) return;

            event.preventDefault();
            event.stopImmediatePropagation();
            portfolioToDelete = trigger.dataset.deletePortfolio;
            deleteName.textContent = trigger.dataset.portfolioName || 'this portfolio';
            deleteError.classList.add('hidden');
            deleteError.textContent = '';

            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => deleteModal.querySelector('[data-modal-panel]')?.classList.remove('scale-95', 'opacity-0'));
        }, true);

        deleteConfirm.addEventListener('click', async () => {
            if (!portfolioToDelete) return;

            deleteConfirm.disabled = true;
            deleteConfirmLabel.textContent = 'Deleting…';
            deleteError.classList.add('hidden');

            try {
                const response = await fetch(portfolioToDelete, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                });

                const result = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(result.message || 'Could not delete this portfolio.');

                window.PortfolioCraft?.toast('Portfolio deleted.', 'success');
                setTimeout(() => window.location.reload(), 600);
            } catch (error) {
                deleteError.textContent = error.message || 'Could not delete this portfolio.';
                deleteError.classList.remove('hidden');
            } finally {
                deleteConfirm.disabled = false;
                deleteConfirmLabel.textContent = 'Delete portfolio';
                portfolioToDelete = null;
            }
        });
    </script>
</x-layouts.app>
