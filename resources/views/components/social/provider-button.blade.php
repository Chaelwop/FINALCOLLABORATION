@props([
    'provider',
    'label' => null,
    'compact' => false,
])

@php
    $configured = $provider->isConfigured();
    $text = $label ?? 'Continue with '.$provider->label();
@endphp

@if ($configured)
    <a href="{{ route('social.redirect', ['provider' => $provider->value]) }}"
        class="group relative flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md"
        style="--brand-color: {{ $provider->color() }}">
        <span class="grid size-6 shrink-0 place-items-center transition-transform duration-200 group-hover:scale-110"
            style="color: {{ $provider->color() }}">
            <x-icon :name="$provider->icon()" class="size-[18px]" />
        </span>
        <span class="flex-1 text-center">{{ $text }}</span>
    </a>
@else
    <span class="flex cursor-not-allowed items-center gap-3 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-400 opacity-70"
        title="Add the client credentials to your .env file to enable {{ $provider->label() }} sign-in.">
        <span class="grid size-6 shrink-0 place-items-center" style="color: {{ $provider->color() }}">
            <x-icon :name="$provider->icon()" class="size-[18px]" />
        </span>
        <span class="flex-1 text-center">{{ $text }}</span>
        <x-icon name="lock-closed" class="size-3.5" />
    </span>
@endif
