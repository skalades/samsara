@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $baseClasses = 'touch-target inline-flex items-center justify-center font-semibold rounded-xl transition duration-150 active:scale-98 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer';

    $variantClasses = match ($variant) {
        'primary' => 'bg-[#1E3A2B] hover:bg-[#12241B] text-white focus:ring-[#1E3A2B]',
        'accent', 'earth' => 'bg-[#D97736] hover:bg-[#B25A20] text-white focus:ring-[#D97736]',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 focus:ring-slate-400',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500',
        'outline' => 'bg-transparent hover:bg-[#1E3A2B]/10 text-[#1E3A2B] border border-[#1E3A2B] focus:ring-[#1E3A2B]',
        default => 'bg-[#1E3A2B] text-white',
    };

    $sizeClasses = match ($size) {
        'sm' => 'text-xs px-3 py-2 min-h-[38px]',
        'lg' => 'text-base px-6 py-3.5 min-h-[50px]',
        default => 'text-sm px-4 py-2.5 min-h-[44px]',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</button>
