@props([
    'portfolio',
    'viewUrl' => null,
    'editUrl' => null,
])

@php
    $data = $portfolio->payload();
    $initials = collect(preg_split('/\s+/', trim($data['fullName'] ?: 'P')))->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');

    $tones = [
        'simple' => ['from-brand-500 to-violet-500', 'bg-brand-50 text-brand-600'],
        'modern' => ['from-ink-1000 to-brand-950', 'bg-slate-100 text-slate-700'],
        'creative' => ['from-violet-500 via-brand-600 to-brand-500', 'bg-violet-50 text-violet-600'],
    ];

    [$gradient, $tone] = $tones[$data['template']] ?? $tones['simple'];
    $shareUrl = $data['isPublic'] ? route('portfolios.show', $portfolio) : null;
@endphp

<article class="card card-hover group overflow-hidden" data-portfolio-card data-template="{{ $data['template'] }}" data-public="{{ $data['isPublic'] ? '1' : '0' }}" data-title="{{ $data['fullName'] }}">
    <div class="relative h-32 overflow-hidden bg-linear-to-br {{ $gradient }}">
        <div class="absolute inset-0 opacity-25" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,.55) 0, transparent 45%);"></div>

        <div class="absolute inset-x-4 bottom-3 flex items-end gap-3">
            <span class="grid size-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-white/95 text-sm font-bold text-slate-800 shadow-lg">
                @if (! empty($data['profilePic']))
                    <img src="{{ $data['profilePic'] }}" alt="" class="size-full object-cover">
                @else
                    {{ $initials ?: 'YN' }}
                @endif
            </span>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-white drop-shadow">{{ $data['fullName'] ?: 'Untitled portfolio' }}</p>
                <p class="truncate text-xs text-white/80">{{ $data['jobTitle'] ?: 'Add your role' }}</p>
            </div>
        </div>

        <span class="absolute top-3 right-3 badge {{ $tone }} backdrop-blur">{{ ucfirst($data['template']) }}</span>

        @if ($data['isPublic'])
            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full bg-emerald-500/95 px-2.5 py-1 text-[11px] font-bold text-white shadow">
                <span class="size-1.5 rounded-full bg-white"></span> Public
            </span>
        @endif
    </div>

    <div class="p-5">
        <p class="truncate text-xs text-slate-500">{{ $data['email'] ?: 'No email yet' }}</p>

        <div class="mt-3 flex flex-wrap gap-1.5">
            @foreach (array_slice($data['skills'], 0, 3) as $skill)
                <span class="badge badge-neutral">{{ $skill }}</span>
            @endforeach
            @if (count($data['skills']) > 3)
                <span class="badge badge-neutral">+{{ count($data['skills']) - 3 }}</span>
            @endif
        </div>

        <div class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-4">
            <a href="{{ $editUrl }}" class="btn btn-secondary btn-sm flex-1">
                <x-icon name="pencil-square" class="size-4" /> Edit
            </a>
            <a href="{{ $viewUrl }}" class="btn btn-secondary btn-sm flex-1">
                <x-icon name="eye" class="size-4" /> Preview
            </a>

            @if ($shareUrl)
                <button type="button" class="btn btn-ghost btn-icon btn-sm" data-copy-link="{{ $shareUrl }}" title="Copy public link">
                    <x-icon name="link" class="size-4" />
                </button>
            @endif

            <button type="button" class="btn btn-ghost btn-icon btn-sm text-rose-600 transition-colors hover:bg-rose-600 hover:text-white focus-visible:bg-rose-600 focus-visible:text-white active:bg-rose-700"
                data-delete-portfolio="{{ route('api.portfolios.destroy', $portfolio) }}"
                data-portfolio-name="{{ $data['fullName'] ?: 'this portfolio' }}" title="Delete portfolio">
                <x-icon name="trash" class="size-4" />
            </button>
        </div>
    </div>
</article>
