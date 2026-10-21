@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'icon' => null,
    'hint' => null,
    'placeholder' => null,
    'required' => false,
    'autocomplete' => null,
    'rows' => null,
    'error' => null,
])

@php
    $errorMessage = $error ?? $errors->get($name)[0] ?? null;
    $inputId = $attributes->get('id', $name);
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    @if ($label)
        <label for="{{ $inputId }}" class="label">
            {{ $label }}
            @if ($required)<span class="text-rose-600">*</span>@endif
        </label>
    @endif

    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-slate-400">
                <x-icon :name="$icon" class="size-[18px]" />
            </span>
        @endif

        @if ($type === 'textarea')
            <textarea id="{{ $inputId }}" name="{{ $name }}" rows="{{ $rows ?? 4 }}"
                @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                @if ($required) required @endif
                @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                {!! $attributes->except('class')->merge(['class' => 'input '.($icon ? 'pl-10' : '').($errorMessage ? ' input-error' : '')]) !!}
                >{{ $value }}</textarea>
        @else
            <input id="{{ $inputId }}" type="{{ $type }}" name="{{ $name }}"
                value="{{ $value }}"
                @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                @if ($required) required @endif
                @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                {!! $attributes->except('class')->merge(['class' => 'input '.($icon ? 'pl-10' : '').($errorMessage ? ' input-error' : '')]) !!}>
        @endif
    </div>

    @if ($errorMessage)
        <p class="field-error">
            <x-icon name="exclamation-triangle" class="size-3.5" /> {{ $errorMessage }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
