@props([
    'id' => 'modal',
    'title' => null,
    'maxWidth' => 'max-w-2xl',
])

<div id="{{ $id }}" data-modal
    class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
    <div class="w-full {{ $maxWidth }} scale-95 rounded-2xl border border-slate-200 bg-white p-6 opacity-0 shadow-2xl transition-all duration-200"
        data-modal-panel role="dialog" aria-modal="true">
        <div class="mb-4 flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <h3 class="font-display text-lg font-bold text-slate-900">{{ $title }}</h3>
            <button type="button" data-modal-close="{{ $id }}" class="grid size-8 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                <x-icon name="x-mark" class="size-5" />
            </button>
        </div>

        {{ $slot }}
    </div>
</div>
