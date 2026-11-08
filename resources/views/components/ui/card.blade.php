@props([
    'padding' => 'p-5 sm:p-6',
    'variant' => 'default',
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconBg' => 'bg-emerald-50 text-emerald-700',
    'hoverable' => false,
    'headerAction' => null,
    'footer' => null,
])

@php
    $variantClasses = match ($variant) {
        'elevated' => 'bg-white border border-slate-200/90 shadow-md',
        'outline' => 'bg-transparent border border-slate-300 shadow-none',
        'subtle' => 'bg-slate-50 border border-slate-200/70 shadow-2xs',
        'dark' => 'bg-slate-900 border border-slate-800 text-white shadow-xl',
        default => 'bg-white border border-slate-200/80 shadow-xs',
    };

    $hoverClass = $hoverable ? 'hover:shadow-md hover:border-slate-300 transition duration-200' : 'transition duration-150';
@endphp

<div {{ $attributes->merge(['class' => "rounded-3xl {$variantClasses} {$hoverClass} overflow-hidden"]) }}>
    @if ($title || $icon || $headerAction)
        <div class="px-5 sm:px-6 pt-5 pb-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                @if ($icon)
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-base {{ $iconBg }} shrink-0 shadow-2xs">
                        <i class="{{ $icon }}"></i>
                    </div>
                @endif
                <div>
                    @if ($title)
                        <h3 class="font-heading font-extrabold text-base sm:text-lg text-slate-900 leading-tight">
                            {{ $title }}
                        </h3>
                    @endif
                    @if ($subtitle)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            @if ($headerAction)
                <div class="shrink-0">
                    {{ $headerAction }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-5 sm:px-6 py-3.5 bg-slate-50/80 border-t border-slate-100 text-xs">
            {{ $footer }}
        </div>
    @endif
</div>
