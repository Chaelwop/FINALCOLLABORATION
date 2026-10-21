@php
    $isEdit = $mode === 'edit';
    $formAction = $isEdit ? route('api.portfolios.update', $portfolio) : route('api.portfolios.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
    $skills = $payload['skills'] ?? [];
    $projects = $payload['projects'] ?? [];
    $experience = $payload['experience'] ?? [];
@endphp

<x-layouts.app title="{{ $isEdit ? 'Edit portfolio' : 'New portfolio' }}">
    <div class="border-b border-slate-200 bg-white/70 backdrop-blur">
        <div class="section flex flex-wrap items-center justify-between gap-3 py-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-md shrink-0" aria-label="Back to dashboard">
                    <span>Back to dashboard</span>
                    <x-icon name="arrow-left" class="size-3" />
                </a>
                <div>
                    <h1 class="font-display text-lg font-extrabold text-slate-900">
                        {{ $isEdit ? 'Edit portfolio' : 'Create your portfolio' }}
                    </h1>
                    <p class="text-xs text-slate-500" data-save-status>Changes preview live as you type</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-secondary btn-md" data-fill-demo>
                    <x-icon name="bolt" class="size-4 text-amber-500" /> Demo data
                </button>

                <button type="button" class="btn btn-primary btn-md" data-save-portfolio
                    data-url="{{ $formAction }}"
                    data-method="{{ $formMethod }}"
                    data-portfolio-id="{{ $portfolio?->id }}"
                    data-loading-button>
                    <span data-loading-label>Save portfolio</span>
                    <x-icon name="check" class="size-4" />
                </button>
            </div>
        </div>
    </div>

    <div class="section grid grid-cols-1 gap-6 py-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)] lg:items-start" data-editor-layout>
        <form id="portfolio-form" data-editor-panel="editor" class="space-y-5" data-preview-endpoint="{{ route('portfolios.fragment') }}" novalidate>
            @csrf
            <input type="hidden" name="_method" value="{{ $isEdit ? 'PUT' : '' }}">

            <section class="card p-6">
                <header class="mb-5 flex items-center gap-3">
                    <span class="grid size-9 place-items-center rounded-xl bg-brand-50 text-brand-600">
                        <x-icon name="user" class="size-4" />
                    </span>
                    <div>
                        <h2 class="font-display text-base font-bold text-slate-900">Personal information</h2>
                        <p class="text-xs text-slate-500">The essentials people see first</p>
                    </div>
                </header>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.input name="fullName" label="Full name" :value="$payload['fullName']" placeholder="Alex Morgan" required icon="user" />
                    <x-ui.input name="jobTitle" label="Job title" :value="$payload['jobTitle']" placeholder="Senior Product Designer" required icon="briefcase" />
                    <x-ui.input name="email" label="Email" type="email" :value="$payload['email']" placeholder="alex@example.com" required icon="envelope" />
                    <x-ui.input name="phone" label="Phone" :value="$payload['phone']" placeholder="+1 (555) 019-2834" icon="phone" />
                    <div class="sm:col-span-2">
                        <x-ui.input name="address" label="Location" :value="$payload['address']" placeholder="San Francisco, CA, USA" icon="map-pin" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.input name="profilePic" label="Profile image URL" :value="$payload['profilePic']" placeholder="https://images.unsplash.com/photo-…" icon="photo"
                            hint="Paste a direct image link. Leave it empty to use a generated monogram." />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.input name="aboutMe" label="About me" type="textarea" :rows="4" :value="$payload['aboutMe']"
                            placeholder="A short summary of your background, strengths and what you are looking for next." required />
                    </div>
                </div>
            </section>

            <section class="card p-6">
                <header class="mb-5 flex items-center gap-3">
                    <span class="grid size-9 place-items-center rounded-xl bg-violet-50 text-violet-600">
                        <x-icon name="academic-cap" class="size-4" />
                    </span>
                    <div>
                        <h2 class="font-display text-base font-bold text-slate-900">Education</h2>
                        <p class="text-xs text-slate-500">Where you studied</p>
                    </div>
                </header>

                <div class="grid gap-4 sm:grid-cols-3">
                    <x-ui.input name="eduSchool" label="School" :value="$payload['eduSchool']" placeholder="Stanford University" />
                    <x-ui.input name="eduDegree" label="Degree" :value="$payload['eduDegree']" placeholder="B.S. Computer Science" />
                    <x-ui.input name="eduYear" label="Years" :value="$payload['eduYear']" placeholder="2018 - 2022" />
                </div>
            </section>

            <section class="card p-6">
                <header class="mb-5 flex items-center gap-3">
                    <span class="grid size-9 place-items-center rounded-xl bg-cyan-50 text-cyan-600">
                        <x-icon name="code-bracket" class="size-4" />
                    </span>
                    <div>
                        <h2 class="font-display text-base font-bold text-slate-900">Skills</h2>
                        <p class="text-xs text-slate-500">Type and press Enter to add</p>
                    </div>
                </header>

                <div data-skill-input>
                    <div class="flex flex-wrap gap-2 rounded-xl border border-slate-300 bg-white p-2 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/15">
                        <div data-skill-list class="flex flex-wrap gap-2 empty:hidden"></div>
                        <input type="text" data-skill-field placeholder="Add a skill…"
                            class="min-w-40 flex-1 bg-transparent px-2 py-1.5 text-sm text-slate-900 placeholder:text-slate-500 focus:outline-none" />
                    </div>
                    <input type="hidden" name="skills" data-skill-values value="{{ implode(',', $skills) }}">
                </div>
            </section>

            <section class="card p-6">
                <header class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="grid size-9 place-items-center rounded-xl bg-fuchsia-50 text-fuchsia-600">
                            <x-icon name="rocket-launch" class="size-4" />
                        </span>
                        <div>
                            <h2 class="font-display text-base font-bold text-slate-900">Projects</h2>
                            <p class="text-xs text-slate-500">Highlight your best work</p>
                        </div>
                    </div>
                    <button type="button" data-add-project class="btn btn-secondary btn-sm">
                        <x-icon name="plus" class="size-4" /> Add
                    </button>
                </header>

                <div data-project-list class="space-y-3">
                    @foreach ($projects as $index => $project)
                        <div data-project-row class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-xs font-bold tracking-wide text-slate-500 uppercase">Project {{ $index + 1 }}</span>
                                <button type="button" data-remove-row class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50">
                                    <x-icon name="trash" class="size-4" />
                                </button>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <x-ui.input name="projects[{{ $index }}][title]" label="Title" :value="$project['title'] ?? ''" placeholder="CloudMetrics Dashboard" />
                                <x-ui.input name="projects[{{ $index }}][link]" label="Link" type="url" :value="$project['link'] ?? ''" placeholder="https://github.com/…" />
                                <div class="sm:col-span-2">
                                    <x-ui.input name="projects[{{ $index }}][desc]" label="Description" type="textarea" :rows="2" :value="$project['desc'] ?? ''" placeholder="What did you build and why does it matter?" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="mt-3 text-xs text-slate-500" data-empty-hint="projects">
                    No projects yet — add one to make this section shine.
                </p>
            </section>

            <section class="card p-6">
                <header class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-icon name="briefcase" class="size-4" />
                        </span>
                        <div>
                            <h2 class="font-display text-base font-bold text-slate-900">Experience</h2>
                            <p class="text-xs text-slate-500">Your career so far</p>
                        </div>
                    </div>
                    <button type="button" data-add-experience class="btn btn-secondary btn-sm">
                        <x-icon name="plus" class="size-4" /> Add
                    </button>
                </header>

                <div data-experience-list class="space-y-3">
                    @foreach ($experience as $index => $job)
                        <div data-experience-row class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-xs font-bold tracking-wide text-slate-500 uppercase">Role {{ $index + 1 }}</span>
                                <button type="button" data-remove-row class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50">
                                    <x-icon name="trash" class="size-4" />
                                </button>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <x-ui.input name="experience[{{ $index }}][role]" label="Role" :value="$job['role'] ?? ''" placeholder="Senior Frontend Developer" />
                                <x-ui.input name="experience[{{ $index }}][company]" label="Company" :value="$job['company'] ?? ''" placeholder="TechNova Solutions" />
                                <x-ui.input name="experience[{{ $index }}][duration]" label="Duration" :value="$job['duration'] ?? ''" placeholder="2022 - Present" />
                                <div class="sm:col-span-3">
                                    <x-ui.input name="experience[{{ $index }}][desc]" label="Highlights" type="textarea" :rows="2" :value="$job['desc'] ?? ''" placeholder="What did you achieve?" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="mt-3 text-xs text-slate-500" data-empty-hint="experience">
                    No experience yet — add your most recent role.
                </p>
            </section>

            <section class="card p-6">
                <header class="mb-5 flex items-center gap-3">
                    <span class="grid size-9 place-items-center rounded-xl bg-amber-50 text-amber-600">
                        <x-icon name="link" class="size-4" />
                    </span>
                    <div>
                        <h2 class="font-display text-base font-bold text-slate-900">Links</h2>
                        <p class="text-xs text-slate-500">Where people can find you</p>
                    </div>
                </header>

                <div class="grid gap-4 sm:grid-cols-3">
                    <x-ui.input name="socialGithub" label="GitHub" type="url" :value="$payload['socialGithub']" placeholder="https://github.com/you" />
                    <x-ui.input name="socialLinkedin" label="LinkedIn" type="url" :value="$payload['socialLinkedin']" placeholder="https://linkedin.com/in/you" />
                    <x-ui.input name="socialWebsite" label="Website" type="url" :value="$payload['socialWebsite']" placeholder="https://yourdomain.com" />
                </div>
            </section>

            <section id="template-zone" class="card scroll-mt-24 p-6">
                <header class="mb-5 flex items-center gap-3">
                    <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-600">
                        <x-icon name="swatch" class="size-4" />
                    </span>
                    <div>
                        <h2 class="font-display text-base font-bold text-slate-900">Template</h2>
                        <p class="text-xs text-slate-500">Switch any time — the preview updates instantly</p>
                    </div>
                </header>

                <input type="hidden" name="template" value="{{ $payload['template'] ?? 'simple' }}" data-template-input>

                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ([
                        ['simple', 'Simple', 'Editorial, ATS friendly', 'from-brand-500/40 to-brand-100/60'],
                        ['modern', 'Modern', 'Dark developer aesthetic', 'from-slate-800 to-slate-950'],
                        ['creative', 'Creative', 'Vibrant and expressive', 'from-fuchsia-500/50 to-violet-500/30'],
                    ] as [$key, $title, $blurb, $gradient])
                        <button type="button" data-template-option="{{ $key }}"
                            class="group rounded-xl border-2 border-slate-200 p-3 text-left transition hover:border-brand-300 {{ ($payload['template'] ?? 'simple') === $key ? 'ring-2 ring-brand-500 ring-offset-2 ring-offset-white' : '' }}">
                            <span class="block h-16 rounded-lg bg-linear-to-br {{ $gradient }} shadow-inner"></span>
                            <span class="mt-2.5 block text-sm font-bold text-slate-900">{{ $title }}</span>
                            <span class="block text-[11px] text-slate-500">{{ $blurb }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <div>
                        <p class="text-sm font-bold text-slate-900">Publish to a public link</p>
                        <p class="mt-0.5 text-xs text-slate-500">Anyone with the link can view this portfolio without signing in.</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="isPublic" value="1" class="peer sr-only" data-publish-input @checked($payload['isPublic'] ?? false)>
                        <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-600 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-500/20"></span>
                        <span class="absolute left-0.5 size-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                    </label>
                </div>
            </section>
        </form>

        <aside data-editor-panel="preview" class="lg:sticky lg:top-24">
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <span class="size-2 animate-pulse rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-bold tracking-wide text-slate-500 uppercase">Live preview</span>
                    </div>
                    <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-0.5">
                        @foreach ([
                            ['computer-desktop', 'desktop'],
                            ['device-phone-mobile', 'mobile'],
                        ] as [$icon, $device])
                            <button type="button" data-device="{{ $device }}" aria-label="{{ ucfirst($device) }} preview" aria-pressed="{{ $device === 'desktop' ? 'true' : 'false' }}"
                                class="grid size-7 place-items-center rounded-md text-slate-500 transition hover:text-slate-900 data-[active=true]:bg-white data-[active=true]:text-brand-600 data-[active=true]:shadow-sm">
                                <x-icon :name="$icon" class="size-4" />
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="overflow-x-auto bg-slate-100/70 p-4">
                    <div data-preview-frame class="mx-auto w-full transition-all duration-300">
                        <div data-preview-canvas class="origin-top">
                            <x-portfolio.fragment :template="$payload['template'] ?? 'simple'" :portfolio="$payload" />
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-3 text-center text-xs text-slate-500">
                The preview is rendered on the server — exactly what visitors will see.
            </p>
        </aside>
    </div>
</x-layouts.app>

@push('scripts')
    <script>
        const previewWidths = { desktop: '100%', mobile: '375px' };
        const previewFrame = document.querySelector('[data-preview-frame]');
        const deviceButtons = document.querySelectorAll('[data-device]');

        if (previewFrame) previewFrame.style.width = previewWidths.desktop;
        deviceButtons.forEach((button) => {
            const active = button.dataset.device === 'desktop';
            button.dataset.active = String(active);
            button.setAttribute('aria-pressed', String(active));
        });

        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-device]');
            if (!button) return;

            deviceButtons.forEach((item) => {
                const active = item === button;
                item.dataset.active = String(active);
                item.setAttribute('aria-pressed', String(active));
            });

            if (previewFrame) {
                previewFrame.style.width = previewWidths[button.dataset.device] ?? previewWidths.desktop;
            }
        });

    </script>
@endpush
