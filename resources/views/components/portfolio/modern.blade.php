@props([
    'portfolio' => [],
])

@php
    $p = $portfolio;
    $skills = array_values(array_filter($p['skills'] ?? []));
    $projects = array_values(array_filter($p['projects'] ?? []));
    $experience = array_values(array_filter($p['experience'] ?? []));
    $initials = collect(preg_split('/\s+/', trim($p['fullName'] ?? 'P')))->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
@endphp

<article class="relative overflow-hidden rounded-3xl bg-ink-1000 text-slate-100 shadow-2xl ring-1 ring-white/10">
    <div class="pointer-events-none absolute -top-32 -right-24 size-96 rounded-full bg-brand-600/25 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-24 size-96 rounded-full bg-violet-600/20 blur-3xl"></div>

    <div class="relative flex flex-col gap-8 p-6 sm:p-10">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
            <div class="shrink-0">
                @if (! empty($p['profilePic']))
                    <img src="{{ $p['profilePic'] }}" alt="{{ $p['fullName'] ?? 'Profile' }}"
                        class="size-28 rounded-2xl object-cover shadow-2xl ring-2 ring-brand-500/40 sm:size-32">
                @else
                    <span class="grid size-28 place-items-center rounded-2xl bg-linear-to-br from-brand-500 to-brand-700 text-3xl font-bold text-white shadow-2xl ring-2 ring-brand-500/40 sm:size-32">
                        {{ $initials ?: 'YN' }}
                    </span>
                @endif
            </div>

            <div class="min-w-0">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-bold tracking-wide text-emerald-300 uppercase">
                    <span class="size-1.5 animate-pulse rounded-full bg-emerald-400"></span> Available for work
                </span>
                <h1 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    {{ $p['fullName'] ?? 'Your Name' }}
                </h1>
                <p class="mt-1 text-lg font-semibold text-brand-300">{{ $p['jobTitle'] ?? 'Your Role' }}</p>
                @if (! empty($p['aboutMe']))
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-400">{{ $p['aboutMe'] }}</p>
                @endif

                <div class="mt-5 flex flex-wrap gap-2.5 text-xs">
                    @if (! empty($p['email']))
                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-slate-300"><x-icon name="envelope" class="size-3.5 text-brand-300" />{{ $p['email'] }}</span>
                    @endif
                    @if (! empty($p['address']))
                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-slate-300"><x-icon name="map-pin" class="size-3.5 text-brand-300" />{{ $p['address'] }}</span>
                    @endif
                    @if (! empty($p['phone']))
                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-slate-300"><x-icon name="phone" class="size-3.5 text-brand-300" />{{ $p['phone'] }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2.5">
            @if (! empty($p['socialGithub']))
                <a href="{{ $p['socialGithub'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3.5 py-2 text-xs font-bold text-slate-200 transition hover:border-brand-400/40 hover:bg-brand-500/10 hover:text-white">
                    <x-icon name="github" class="size-4" /> GitHub
                </a>
            @endif
            @if (! empty($p['socialLinkedin']))
                <a href="{{ $p['socialLinkedin'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3.5 py-2 text-xs font-bold text-slate-200 transition hover:border-brand-400/40 hover:bg-brand-500/10 hover:text-white">
                    <x-icon name="linkedin" class="size-4" /> LinkedIn
                </a>
            @endif
            @if (! empty($p['socialWebsite']))
                <a href="{{ $p['socialWebsite'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3.5 py-2 text-xs font-bold text-slate-200 transition hover:border-brand-400/40 hover:bg-brand-500/10 hover:text-white">
                    <x-icon name="globe-alt" class="size-4" /> Website
                </a>
            @endif
        </div>
    </div>

    <div class="relative grid gap-6 border-t border-white/10 p-6 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] sm:p-10">
        <div class="space-y-6">
            @if ($skills)
                <section class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                    <h2 class="flex items-center gap-2 text-xs font-extrabold tracking-[0.2em] text-brand-300 uppercase">
                        <x-icon name="code-bracket" class="size-4" /> Tech stack
                    </h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($skills as $skill)
                            <span class="rounded-lg border border-white/10 bg-slate-800/60 px-2.5 py-1 text-xs font-semibold text-brand-200">{{ $skill }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (! empty($p['eduDegree']) || ! empty($p['eduSchool']))
                <section class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                    <h2 class="flex items-center gap-2 text-xs font-extrabold tracking-[0.2em] text-brand-300 uppercase">
                        <x-icon name="academic-cap" class="size-4" /> Education
                    </h2>
                    <p class="mt-3 font-display font-bold text-white">{{ $p['eduDegree'] ?? '' }}</p>
                    <p class="text-xs text-slate-400">{{ $p['eduSchool'] ?? '' }}</p>
                    @if (! empty($p['eduYear']))
                        <p class="mt-1 font-mono text-xs text-brand-300">{{ $p['eduYear'] }}</p>
                    @endif
                </section>
            @endif
        </div>

        <div class="space-y-6">
            @if ($experience)
                <section class="rounded-2xl border border-white/10 bg-white/[0.04] p-6">
                    <h2 class="flex items-center gap-2 text-xs font-extrabold tracking-[0.2em] text-brand-300 uppercase">
                        <x-icon name="briefcase" class="size-4" /> Work experience
                    </h2>
                    <ol class="mt-5 space-y-6">
                        @foreach ($experience as $item)
                            <li class="relative border-l border-brand-500/40 pl-5">
                                <span class="absolute -left-[4.5px] top-1.5 size-2.5 rounded-full bg-brand-400"></span>
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <h3 class="font-display text-base font-bold text-white">{{ $item['role'] ?? '' }}</h3>
                                    @if (! empty($item['duration']))
                                        <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-[11px] font-semibold text-slate-400">{{ $item['duration'] }}</span>
                                    @endif
                                </div>
                                @if (! empty($item['company']))
                                    <p class="mt-0.5 text-xs font-semibold text-brand-300">{{ $item['company'] }}</p>
                                @endif
                                @if (! empty($item['desc']))
                                    <p class="mt-1.5 text-xs leading-relaxed text-slate-400">{{ $item['desc'] }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            @if ($projects)
                <section class="rounded-2xl border border-white/10 bg-white/[0.04] p-6">
                    <h2 class="flex items-center gap-2 text-xs font-extrabold tracking-[0.2em] text-brand-300 uppercase">
                        <x-icon name="rocket-launch" class="size-4" /> Featured builds
                    </h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        @foreach ($projects as $project)
                            <div class="rounded-xl border border-white/10 bg-slate-900/60 p-4 transition hover:border-brand-400/30">
                                <h3 class="font-display text-sm font-bold text-white">{{ $project['title'] ?? '' }}</h3>
                                @if (! empty($project['desc']))
                                    <p class="mt-1.5 text-xs leading-relaxed text-slate-400">{{ $project['desc'] }}</p>
                                @endif
                                @if (! empty($project['link']))
                                    <a href="{{ $project['link'] }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-brand-300 transition hover:gap-1.5">
                                        Open <x-icon name="arrow-up-right" class="size-3.5" />
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</article>
