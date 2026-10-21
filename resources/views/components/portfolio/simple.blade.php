@props([
    'portfolio' => [],
])

@php
    $p = $portfolio;
    $skills = array_values(array_filter($p['skills'] ?? []));
    $projects = array_values(array_filter($p['projects'] ?? []));
    $experience = array_values(array_filter($p['experience'] ?? []));
    $initials = collect(preg_split('/\s+/', trim($p['fullName'] ?? 'P')))->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    $links = array_filter([
        $p['socialGithub'] ?? null,
        $p['socialLinkedin'] ?? null,
        $p['socialWebsite'] ?? null,
    ]);
@endphp

<article class="mx-auto max-w-3xl overflow-hidden rounded-3xl bg-white text-slate-800 shadow-2xl ring-1 ring-slate-900/5">
    <header class="relative overflow-hidden border-b border-slate-100 px-8 pt-10 pb-8 sm:px-12">
        <div class="pointer-events-none absolute -top-24 -right-16 size-64 rounded-full bg-brand-50 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="font-display text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    {{ $p['fullName'] ?? 'Your Name' }}
                </h1>
                <p class="mt-1.5 text-lg font-semibold text-brand-600">{{ $p['jobTitle'] ?? 'Your Role' }}</p>

                <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">
                    @if (! empty($p['email']))
                        <span class="inline-flex items-center gap-1.5"><x-icon name="envelope" class="size-4 text-slate-500" />{{ $p['email'] }}</span>
                    @endif
                    @if (! empty($p['phone']))
                        <span class="inline-flex items-center gap-1.5"><x-icon name="phone" class="size-4 text-slate-500" />{{ $p['phone'] }}</span>
                    @endif
                    @if (! empty($p['address']))
                        <span class="inline-flex items-center gap-1.5"><x-icon name="map-pin" class="size-4 text-slate-500" />{{ $p['address'] }}</span>
                    @endif
                </div>
            </div>

            <div class="shrink-0">
                @if (! empty($p['profilePic']))
                    <img src="{{ $p['profilePic'] }}" alt="{{ $p['fullName'] ?? 'Profile' }}"
                        class="size-24 rounded-2xl object-cover shadow-lg ring-1 ring-slate-900/5">
                @else
                    <span class="grid size-24 place-items-center rounded-2xl bg-linear-to-br from-brand-500 to-violet-600 text-2xl font-bold text-white shadow-lg">
                        {{ $initials ?: 'YN' }}
                    </span>
                @endif
            </div>
        </div>
    </header>

    <div class="space-y-10 px-8 py-10 sm:px-12">
        @if (! empty($p['aboutMe']))
            <section>
                <h2 class="text-xs font-bold tracking-[0.2em] text-slate-500 uppercase">About</h2>
                <p class="mt-3 leading-relaxed text-slate-600">{{ $p['aboutMe'] }}</p>
            </section>
        @endif

        @if ($experience)
            <section>
                <h2 class="text-xs font-bold tracking-[0.2em] text-slate-500 uppercase">Experience</h2>
                <ol class="mt-5 space-y-6">
                    @foreach ($experience as $item)
                        <li class="relative border-l-2 border-slate-200 pl-6">
                            <span class="absolute -left-[7px] top-1.5 size-3 rounded-full border-2 border-white bg-brand-500"></span>
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4">
                                <h3 class="font-display text-lg font-bold text-slate-900">
                                    {{ $item['role'] ?? '' }}@if (! empty($item['company']))<span class="font-normal text-slate-500"> · {{ $item['company'] }}</span>@endif
                                </h3>
                                @if (! empty($item['duration']))
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">{{ $item['duration'] }}</span>
                                @endif
                            </div>
                            @if (! empty($item['desc']))
                                <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $item['desc'] }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif

        @if ($projects)
            <section>
                <h2 class="text-xs font-bold tracking-[0.2em] text-slate-500 uppercase">Selected work</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @foreach ($projects as $project)
                        <div class="group rounded-2xl border border-slate-100 bg-slate-50/70 p-5 transition hover:border-brand-200 hover:bg-white hover:shadow-md">
                            <h3 class="font-display font-bold text-slate-900">{{ $project['title'] ?? '' }}</h3>
                            @if (! empty($project['desc']))
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $project['desc'] }}</p>
                            @endif
                            @if (! empty($project['link']))
                                <a href="{{ $project['link'] }}" target="_blank" rel="noopener noreferrer"
                                    class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-brand-600 transition hover:gap-1.5">
                                    View project <x-icon name="arrow-up-right" class="size-3.5" />
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="grid gap-8 border-t border-slate-100 pt-8 sm:grid-cols-2">
            <section>
                <h2 class="text-xs font-bold tracking-[0.2em] text-slate-500 uppercase">Education</h2>
                @if (! empty($p['eduDegree']) || ! empty($p['eduSchool']))
                    <p class="mt-3 font-display font-bold text-slate-900">{{ $p['eduDegree'] ?? '' }}</p>
                    <p class="text-sm text-slate-600">{{ $p['eduSchool'] ?? '' }}@if (! empty($p['eduYear'])) <span class="text-slate-500">· {{ $p['eduYear'] }}</span>@endif</p>
                @else
                    <p class="mt-3 text-sm text-slate-500">Add your education details in the editor.</p>
                @endif
            </section>

            <section>
                <h2 class="text-xs font-bold tracking-[0.2em] text-slate-500 uppercase">Skills</h2>
                @if ($skills)
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ($skills as $skill)
                            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $skill }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">Add a few skills to make this section shine.</p>
                @endif
            </section>
        </div>

        @if ($links)
            <section class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-8">
                @if (! empty($p['socialGithub']))
                    <a href="{{ $p['socialGithub'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-600">
                        <x-icon name="github" class="size-4" /> GitHub
                    </a>
                @endif
                @if (! empty($p['socialLinkedin']))
                    <a href="{{ $p['socialLinkedin'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-600">
                        <x-icon name="linkedin" class="size-4" /> LinkedIn
                    </a>
                @endif
                @if (! empty($p['socialWebsite']))
                    <a href="{{ $p['socialWebsite'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-600">
                        <x-icon name="globe-alt" class="size-4" /> Website
                    </a>
                @endif
            </section>
        @endif
    </div>
</article>
