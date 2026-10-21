<x-layouts.app title="PortfolioCraft — Build a portfolio people remember">
    <section class="relative isolate overflow-hidden border-b border-slate-200 bg-white">
        <div class="mesh-bg pointer-events-none absolute inset-0 -z-10 opacity-70"></div>
        <div class="grid-bg pointer-events-none absolute inset-0 -z-10 opacity-40"></div>
        <div class="aurora -top-40 -left-32 size-[30rem] bg-brand-500/10"></div>
        <div class="aurora -right-40 -bottom-52 size-[34rem] bg-violet-500/10"></div>

        <div class="section pt-16 pb-20 sm:pt-24 sm:pb-28">
            <div class="grid items-center gap-14 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)]">
                <div>
                    <span class="eyebrow animate-fade-in">
                        <x-icon name="sparkles" class="size-3.5" />
                        {{ collect($providers)->where('isConfigured')->count() ? 'Sign in with Google, GitHub & more' : 'Three templates · Zero friction' }}
                    </span>

                    <h1 class="mt-6 font-display text-4xl leading-[1.08] font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                        Build a portfolio<br class="hidden sm:block">
                        that <span class="text-gradient">people remember</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-relaxed text-slate-600 sm:text-lg">
                        Fill in your details once, pick one of three handcrafted templates, and publish a shareable link in minutes. Every version is saved to your dashboard.
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ auth()->check() ? route('portfolios.create') : route('register') }}" class="btn btn-primary btn-lg w-full sm:w-auto">
                            <x-icon name="sparkles" class="size-5" />
                            {{ auth()->check() ? 'Start a new portfolio' : 'Create your free account' }}
                        </a>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-600">
                        <span class="inline-flex items-center gap-2"><x-icon name="check-circle" class="size-4 text-brand-500" /> Free forever</span>
                        <span class="inline-flex items-center gap-2"><x-icon name="check-circle" class="size-4 text-brand-500" /> No credit card</span>
                        <span class="inline-flex items-center gap-2"><x-icon name="check-circle" class="size-4 text-brand-500" /> Export to PNG &amp; PDF</span>
                    </div>
                </div>

                <div class="relative animate-fade-in" style="animation-delay: 120ms">
                    <div class="absolute -inset-8 rounded-[3rem] bg-brand-500/10 blur-3xl"></div>

                    <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-2xl shadow-slate-200/70">
                        <div class="flex items-center gap-2 border-b border-slate-200 bg-slate-50 px-5 py-3.5">
                            <span class="size-3 rounded-full bg-rose-400"></span>
                            <span class="size-3 rounded-full bg-amber-400"></span>
                            <span class="size-3 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 hidden text-xs font-medium text-slate-500 sm:block">portfoliocraft.app/dashboard</span>
                        </div>

                        <div class="grid gap-4 p-5 sm:grid-cols-3 sm:p-7">
                            @foreach ([
                                ['swatch', 'Simple', 'Editorial and ATS friendly', 'from-brand-500/20 to-brand-500/5', 'text-brand-600'],
                                ['code-bracket', 'Modern', 'Dark developer aesthetic', 'from-violet-500/20 to-violet-500/5', 'text-violet-600'],
                                ['sparkles', 'Creative', 'Vibrant and expressive', 'from-accent-400/25 to-accent-400/5', 'text-brand-500'],
                            ] as [$icon, $title, $blurb, $gradient, $tone])
                                <div class="group rounded-2xl border border-slate-200 bg-linear-to-br {{ $gradient }} p-5 transition duration-300 group-hover:-translate-y-1 group-hover:border-brand-300 group-hover:shadow-lg">
                                    <span class="grid size-10 place-items-center rounded-xl bg-white {{ $tone }} shadow-sm ring-1 ring-slate-200">
                                        <x-icon :name="$icon" class="size-5" />
                                    </span>
                                    <p class="mt-4 font-display font-bold text-slate-900">{{ $title }}</p>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $blurb }}</p>
                                    <div class="mt-4 space-y-2">
                                        <span class="block h-2 w-3/4 rounded-full bg-slate-900/10"></span>
                                        <span class="block h-2 w-1/2 rounded-full bg-slate-900/8"></span>
                                        <span class="block h-2 w-5/6 rounded-full bg-slate-900/8"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                            <span class="grid size-9 place-items-center rounded-xl bg-brand-600 text-xs font-bold text-white">SJ</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">Sarah Jenkins</p>
                                <p class="truncate text-xs text-slate-500">Full-Stack Software Engineer</p>
                            </div>
                            <span class="badge badge-brand">Published</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-slate-200 bg-white py-16">
        <div class="section">
            <div class="mx-auto max-w-2xl text-center">
                <p class="eyebrow">Why PortfolioCraft</p>
                <h2 class="mt-4 font-display text-3xl font-extrabold text-slate-900 sm:text-4xl">
                    Everything you need, nothing you don't
                </h2>
                <p class="mt-3 text-slate-600">
                    A focused toolkit for people who would rather spend their time on the work than on the formatting.
                </p>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['key', 'One-click social sign-in', 'Google, GitHub, LinkedIn and more — your account is ready before the page finishes loading.', 'bg-brand-50 text-brand-600'],
                    ['bolt', 'Live template preview', 'Watch your content update in real time as you type, then switch between all three layouts instantly.', 'bg-amber-50 text-amber-600'],
                    ['cloud-arrow-up', 'Autosaved drafts', 'Every edit is stored in your dashboard, so a refresh never costs you your work.', 'bg-emerald-50 text-emerald-600'],
                    ['share', 'Publish a public link', 'Flip one switch to share a live, responsive portfolio with anyone — no account needed to view.', 'bg-cyan-50 text-cyan-600'],
                    ['document-arrow-down', 'Export anywhere', 'Download a high resolution snapshot card or print a clean, vector PDF of your portfolio.', 'bg-violet-50 text-violet-600'],
                    ['cog-6-tooth', 'Built for you', 'Your dashboard, your templates and your public links stay under your control.', 'bg-rose-50 text-rose-600'],
                ] as [$icon, $title, $blurb, $tone])
                    <div class="card card-hover reveal p-6">
                        <span class="grid size-11 place-items-center rounded-xl {{ $tone }}">
                            <x-icon :name="$icon" class="size-5" />
                        </span>
                        <h3 class="mt-4 font-display text-lg font-bold text-slate-900">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $blurb }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-20">
        <div class="section">
            <div class="grid gap-5 sm:grid-cols-3">
                @foreach ([
                    ['shield-check', 'Your data stays yours', 'Every portfolio is linked to your account, and public links are only created when you publish them.', 'bg-brand-50 text-brand-600'],
                    ['bolt', 'Ready in minutes', 'One guided form, three crafted templates and a live preview that updates as you type.', 'bg-violet-50 text-violet-600'],
                    ['share', 'Share anywhere', 'Publish a responsive public link and share it on social media, your CV or your email signature.', 'bg-cyan-50 text-cyan-600'],
                ] as [$icon, $title, $blurb, $tone])
                    <div class="card reveal p-6">
                        <span class="grid size-11 place-items-center rounded-xl {{ $tone }}">
                            <x-icon :name="$icon" class="size-5" />
                        </span>
                        <h3 class="mt-4 font-display text-lg font-bold text-slate-900">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $blurb }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
