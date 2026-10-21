@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconAfter' => null,
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $classes = 'btn btn-'.$variant.' btn-'.$size;
    $iconSize = $size === 'lg' ? 'size-5' : 'size-4';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="{{ $iconSize }}" />@endif
        {{ $slot }}
        @if ($iconAfter)<x-icon :name="$iconAfter" class="{{ $iconSize }}" />@endif
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="{{ $iconSize }}" />@endif
        {{ $slot }}
        @if ($iconAfter)<x-icon :name="$iconAfter" class="{{ $iconSize }}" />@endif
    </button>
@endif
