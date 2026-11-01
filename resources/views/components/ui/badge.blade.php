@props([
    'color' => 'emerald',
    'size' => 'md',
])

@php
    $colorClasses = match ($color) {
        'emerald', 'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'amber', 'yellow' => 'bg-amber-50 text-amber-700 border-amber-200',
        'rose', 'red' => 'bg-rose-50 text-rose-700 border-rose-200',
        'sky', 'blue' => 'bg-sky-50 text-sky-700 border-sky-200',
        'indigo', 'purple' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'earth' => 'bg-[#FBEFE7] text-[#D97736] border-[#F38C4C]/40',
        'forest' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $sizeClasses = match ($size) {
        'sm' => 'text-[10px] px-2 py-0.5',
        'lg' => 'text-sm px-3.5 py-1',
        default => 'text-xs px-2.5 py-1',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full border shadow-2xs {$colorClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
