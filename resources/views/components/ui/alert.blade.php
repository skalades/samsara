@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
    'icon' => null,
])

@php
    $typeConfig = match ($type) {
        'success' => [
            'bg' => 'bg-emerald-50/90 border-emerald-200 text-emerald-900',
            'icon' => 'fas fa-check-circle text-emerald-600',
            'btn' => 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100',
        ],
        'error', 'danger' => [
            'bg' => 'bg-rose-50/90 border-rose-200 text-rose-900',
            'icon' => 'fas fa-triangle-exclamation text-rose-600',
            'btn' => 'text-rose-600 hover:text-rose-800 hover:bg-rose-100',
        ],
        'warning' => [
            'bg' => 'bg-amber-50/90 border-amber-200 text-amber-900',
            'icon' => 'fas fa-circle-exclamation text-amber-600',
            'btn' => 'text-amber-600 hover:text-amber-800 hover:bg-amber-100',
        ],
        default => [
            'bg' => 'bg-sky-50/90 border-sky-200 text-sky-900',
            'icon' => 'fas fa-info-circle text-sky-600',
            'btn' => 'text-sky-600 hover:text-sky-800 hover:bg-sky-100',
        ],
    };

    $activeIcon = $icon ?? $typeConfig['icon'];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     {{ $attributes->merge(['class' => "p-4 rounded-2xl border shadow-2xs backdrop-blur-xs flex items-start justify-between gap-3 text-xs sm:text-sm {$typeConfig['bg']}"]) }}>

    <div class="flex items-start gap-3 flex-1">
        <div class="shrink-0 mt-0.5 text-base sm:text-lg">
            <i class="{{ $activeIcon }}"></i>
        </div>

        <div class="flex-1">
            @if ($title)
                <h4 class="font-heading font-extrabold text-sm sm:text-base leading-tight mb-0.5">
                    {{ $title }}
                </h4>
            @endif
            <div class="leading-relaxed">
                {{ $slot }}
            </div>
        </div>
    </div>

    @if ($dismissible)
        <button type="button"
                @click="show = false"
                class="touch-target -mr-1.5 -mt-1.5 p-1.5 rounded-lg transition {{ $typeConfig['btn'] }}"
                title="Tutup pemberitahuan">
            <i class="fas fa-times text-xs sm:text-sm"></i>
        </button>
    @endif
</div>
