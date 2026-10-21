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

<article class="relative overflow-hidden rounded-3xl bg-linear-to-br from-brand-950 via-[#241a5c] to-violet-800 p-6 text-white shadow-2xl sm:p-10">
    <div class="pointer-events-none absolute -top-24 -left-16 size-80 rounded-full bg-sky-400/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-20 -bottom-24 size-96 rounded-full bg-amber-300/20 blur-3xl"></div>

    <div class="relative rounded-3xl border border-white/60 bg-white/95 p-7 text-slate-900 shadow-2xl backdrop-blur-xl sm:p-9">
        <div class="flex flex-wrap items-center gap-6">
            <div class="shrink-0">
                @if (! empty($p['profilePic']))
                    <img src="{{ $p['profilePic'] }}" alt="{{ $p['fullName'] ?? 'Profile' }}"
                        class="size-28 -rotate-3 rounded-3xl object-cover shadow-xl transition duration-300 hover:rotate-0 sm:size-32">
                @else
                    <span class="grid size-28 -rotate-3 place-items-center rounded-3xl bg-linear-to-br from-violet-500 to-brand-600 text-3xl font-black text-white shadow-xl transition duration-300 hover:rotate-0 sm:size-32">
                        {{ $initials ?: 'YN' }}
                    </span>
                @endif
            </div>

            <div class="min-w-0 flex-1" style="flex-basis: 18rem">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-100 px-3 py-1 text-[11px] font-black tracking-[0.15em] text-violet-700 uppercase">
                    <x-icon name="sparkles" class="size-3" /> Creative mind
                </span>
                <h1 class="mt-2.5 break-words font-display text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                    {{ $p['fullName'] ?? 'Your Name' }}
                </h1>
                <p class="mt-1 bg-linear-to-r from-brand-600 to-violet-600 bg-clip-text text-xl font-extrabold text-transparent">
                    {{ $p['jobTitle'] ?? 'Your Role' }}
                </p>
                @if (! empty($p['aboutMe']))
                    <p class="mt-4 max-w-xl break-words text-sm leading-7 text-slate-600">{{ $p['aboutMe'] }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="relative mt-6 grid gap-6" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 20rem), 1fr))">
        <div class="space-y-6">
            @if ($skills)
                <section class="rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl">
                    <h2 class="flex items-center gap-2 font-display text-lg font-black text-white">
                        <x-icon name="sparkles" class="size-5 text-amber-300" /> Superpowers
                    </h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($skills as $skill)
                            <span class="rounded-xl bg-linear-to-r from-violet-500 to-brand-500 px-3 py-1.5 text-xs font-bold text-white shadow-lg shadow-violet-950/40">{{ $skill }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl">
                <h2 class="flex items-center gap-2 font-display text-lg font-black text-white">
                        <x-icon name="information-circle" class="size-5 text-cyan-300" /> Details
                </h2>
                <dl class="mt-4 space-y-3 text-sm">
                    @if (! empty($p['email']))
                        <div class="flex items-start gap-3">
                            <x-icon name="envelope" class="mt-0.5 size-4 shrink-0 text-white/60" />
                            <dd class="min-w-0 break-words text-sm leading-relaxed text-white/85">{{ $p['email'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($p['phone']))
                        <div class="flex items-start gap-3">
                            <x-icon name="phone" class="mt-0.5 size-4 shrink-0 text-white/60" />
                            <dd class="min-w-0 break-words text-sm leading-relaxed text-white/85">{{ $p['phone'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($p['address']))
                        <div class="flex items-start gap-3">
                            <x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-white/60" />
                            <dd class="min-w-0 break-words text-sm leading-relaxed text-white/85">{{ $p['address'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($p['eduDegree']) || ! empty($p['eduSchool']))
                        <div class="flex items-start gap-3">
                            <x-icon name="academic-cap" class="mt-0.5 size-4 shrink-0 text-white/60" />
                            <dd class="min-w-0 break-words text-sm leading-relaxed text-white/85">{{ $p['eduDegree'] ?? '' }}@if (! empty($p['eduSchool']))<span class="block text-xs text-white/55">{{ $p['eduSchool'] }}</span>@endif</dd>
                        </div>
                    @endif
                </dl>
            </section>

            @if (! empty($p['socialGithub']) || ! empty($p['socialLinkedin']) || ! empty($p['socialWebsite']))
                <section class="rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl">
                    <h2 class="flex items-center gap-2 font-display text-lg font-black text-white">
                        <x-icon name="link" class="size-5 text-accent-300" /> Find me
                    </h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if (! empty($p['socialGithub']))
                            <a href="{{ $p['socialGithub'] }}" target="_blank" rel="noopener noreferrer" class="grid size-10 place-items-center rounded-xl bg-white/10 text-white transition hover:-translate-y-0.5 hover:bg-white/20" title="GitHub"><x-icon name="github" class="size-5" /></a>
                        @endif
                        @if (! empty($p['socialLinkedin']))
                            <a href="{{ $p['socialLinkedin'] }}" target="_blank" rel="noopener noreferrer" class="grid size-10 place-items-center rounded-xl bg-white/10 text-white transition hover:-translate-y-0.5 hover:bg-white/20" title="LinkedIn"><x-icon name="linkedin" class="size-5" /></a>
                        @endif
                        @if (! empty($p['socialWebsite']))
                            <a href="{{ $p['socialWebsite'] }}" target="_blank" rel="noopener noreferrer" class="grid size-10 place-items-center rounded-xl bg-white/10 text-white transition hover:-translate-y-0.5 hover:bg-white/20" title="Website"><x-icon name="globe-alt" class="size-5" /></a>
                        @endif
                    </div>
                </section>
            @endif
        </div>

        <div class="space-y-6">
            @if ($projects)
                <section class="rounded-3xl border border-white/20 bg-white/95 p-6 text-slate-900 shadow-xl sm:p-7">
                    <h2 class="flex items-center gap-2 font-display text-xl font-black text-slate-900">
                        <x-icon name="swatch" class="size-5 text-violet-600" /> Featured work
                    </h2>
                    <div class="mt-5 grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 15rem), 1fr))">
                        @foreach ($projects as $project)
                            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                                <h3 class="font-display text-base font-black text-slate-900">{{ $project['title'] ?? '' }}</h3>
                                @if (! empty($project['desc']))
                                    <p class="mt-2 break-words text-sm leading-relaxed text-slate-600">{{ $project['desc'] }}</p>
                                @endif
                                @if (! empty($project['link']))
                                    <a href="{{ $project['link'] }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-brand-500">
                                        Explore <x-icon name="arrow-up-right" class="size-3.5" />
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($experience)
                <section class="rounded-3xl border border-white/20 bg-white/95 p-6 text-slate-900 shadow-xl sm:p-7">
                    <h2 class="flex items-center gap-2 font-display text-xl font-black text-slate-900">
                        <x-icon name="fire" class="size-5 text-amber-500" /> Experience journey
                    </h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($experience as $item)
                            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h3 class="font-display text-base font-black text-slate-900">{{ $item['role'] ?? '' }}</h3>
                                    @if (! empty($item['duration']))
                                        <span class="rounded-full bg-violet-100 px-2.5 py-1 text-[11px] font-black text-violet-700">{{ $item['duration'] }}</span>
                                    @endif
                                </div>
                                @if (! empty($item['company']))
                                    <p class="mt-0.5 text-xs font-bold text-brand-600">{{ $item['company'] }}</p>
                                @endif
                                @if (! empty($item['desc']))
                                    <p class="mt-2 break-words text-sm leading-relaxed text-slate-600">{{ $item['desc'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</article>
