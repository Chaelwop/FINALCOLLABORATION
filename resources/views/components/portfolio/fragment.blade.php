@props([
    'template' => 'simple',
    'portfolio' => [],
])

@if ($template === 'modern')
    <x-portfolio.modern :portfolio="$portfolio" />
@elseif ($template === 'creative')
    <x-portfolio.creative :portfolio="$portfolio" />
@else
    <x-portfolio.simple :portfolio="$portfolio" />
@endif
