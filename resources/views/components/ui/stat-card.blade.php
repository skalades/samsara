@props([
    'title',
    'value',
    'icon' => null,
    'color' => 'forest',
    'subtext' => null,
    'trend' => null,
])

@php
    $iconBg = match ($color) {
        'earth' => 'bg-[#FBEFE7] text-[#D97736]',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'sky' => 'bg-sky-50 text-sky-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'amber' => 'bg-amber-50 text-amber-600',
        default => 'bg-emerald-950 text-emerald-300',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs relative overflow-hidden flex flex-col justify-between']) }}>
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $title }}</span>
        @if ($icon)
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm {{ $iconBg }}">
                <i class="{{ $icon }}"></i>
            </div>
        @endif
    </div>

    <div class="mt-2.5">
        <div class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 tracking-tight">
            {{ $value }}
        </div>
        @if ($subtext)
            <div class="mt-1 text-xs text-slate-500 flex items-center gap-1.5 font-medium">
                {{ $subtext }}
            </div>
        @endif
    </div>
</div>
