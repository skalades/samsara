@props([
    'color' => 'emerald',
    'size' => 'md',
    'dot' => false,
    'pulse' => false,
    'icon' => null,
])

@php
    $colorClasses = match ($color) {
        'emerald', 'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'amber', 'yellow' => 'bg-amber-50 text-amber-700 border-amber-200',
        'rose', 'red' => 'bg-rose-50 text-rose-700 border-rose-200',
        'sky', 'blue' => 'bg-sky-50 text-sky-700 border-sky-200',
        'indigo', 'purple' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'teal' => 'bg-teal-50 text-teal-700 border-teal-200',
        'earth' => 'bg-[#FBEFE7] text-[#D97736] border-[#F38C4C]/40',
        'forest' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
        'dark' => 'bg-slate-900 text-slate-100 border-slate-700',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $dotColor = match ($color) {
        'emerald', 'green' => 'bg-emerald-500',
        'amber', 'yellow' => 'bg-amber-500',
        'rose', 'red' => 'bg-rose-500',
        'sky', 'blue' => 'bg-sky-500',
        'indigo', 'purple' => 'bg-indigo-500',
        'teal' => 'bg-teal-500',
        'earth' => 'bg-[#D97736]',
        'forest' => 'bg-emerald-400',
        default => 'bg-slate-400',
    };

    $sizeClasses = match ($size) {
        'xs' => 'text-[9px] px-1.5 py-0.5 gap-1',
        'sm' => 'text-[10px] px-2 py-0.5 gap-1.5',
        'lg' => 'text-sm px-3.5 py-1 gap-2',
        default => 'text-xs px-2.5 py-0.5 gap-1.5',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-bold rounded-full border shadow-2xs {$colorClasses} {$sizeClasses} select-none"]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} {{ $pulse ? 'animate-pulse' : '' }}"></span>
    @endif
    @if ($icon)
        <i class="{{ $icon }} text-[10px]"></i>
    @endif
    <span>{{ $slot }}</span>
</span>
