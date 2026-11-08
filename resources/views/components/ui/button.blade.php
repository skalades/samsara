@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'fullWidth' => false,
])

@php
    $baseClasses = 'touch-target inline-flex items-center justify-center font-bold rounded-xl transition duration-150 active:scale-98 select-none focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer gap-2 disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed';

    $variantClasses = match ($variant) {
        'primary' => 'bg-[#1E3A2B] hover:bg-[#12241B] text-white focus:ring-[#1E3A2B] shadow-sm',
        'accent', 'earth' => 'bg-[#D97736] hover:bg-[#B25A20] text-white focus:ring-[#D97736] shadow-sm',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 focus:ring-slate-400 shadow-2xs',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 shadow-sm',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white focus:ring-emerald-500 shadow-sm',
        'outline' => 'bg-transparent hover:bg-[#1E3A2B]/10 text-[#1E3A2B] border border-[#1E3A2B] focus:ring-[#1E3A2B]',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-700 focus:ring-slate-300',
        default => 'bg-[#1E3A2B] text-white',
    };

    $sizeClasses = match ($size) {
        'xs' => 'text-[11px] px-2.5 py-1.5 min-h-[36px]',
        'sm' => 'text-xs px-3 py-2 min-h-[40px]',
        'lg' => 'text-base px-6 py-3.5 min-h-[50px]',
        default => 'text-sm px-4 py-2.5 min-h-[44px]',
    };

    $widthClass = $fullWidth ? 'w-full' : '';
    $allClasses = "{$baseClasses} {$variantClasses} {$sizeClasses} {$widthClass}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $allClasses]) }}>
        @if ($loading)
            <i class="fas fa-spinner fa-spin text-sm"></i>
        @elseif ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} text-sm"></i>
        @endif

        {{ $slot }}

        @if (!$loading && $icon && $iconPosition === 'right')
            <i class="{{ $icon }} text-sm"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $allClasses]) }}>
        @if ($loading)
            <i class="fas fa-spinner fa-spin text-sm"></i>
        @elseif ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} text-sm"></i>
        @endif

        {{ $slot }}

        @if (!$loading && $icon && $iconPosition === 'right')
            <i class="{{ $icon }} text-sm"></i>
        @endif
    </button>
@endif
