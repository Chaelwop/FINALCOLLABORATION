@props([
    'type' => 'info',
    'title' => null,
])

@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'error' => 'border-rose-200 bg-rose-50 text-rose-900',
        'info' => 'border-sky-200 bg-sky-50 text-sky-900',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
    ];

@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border px-4 py-3 text-sm '.$styles[$type]]) }}>
    <div class="min-w-0 flex-1">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div class="{{ $title ? 'mt-0.5 opacity-90' : '' }}">{{ $slot }}</div>
    </div>
</div>
