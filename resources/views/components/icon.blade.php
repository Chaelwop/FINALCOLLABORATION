@props([
    'name' => 'sparkles',
])

<svg
    {{ $attributes->merge(['class' => 'size-5 shrink-0']) }}
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
    fill="{{ $isBrand ? 'currentColor' : 'none' }}"
    stroke="{{ $isBrand ? 'none' : 'currentColor' }}"
    stroke-width="1.5"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
>{!! $content !!}</svg>
