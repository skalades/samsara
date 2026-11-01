@props([
    'name' => null,
    'value' => null,
    'model' => null,
    'placeholder' => '0',
    'prefix' => 'Rp',
    'size' => 'md',
    'align' => 'left',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'id' => null,
])

@php
    // Detect either x-model or model attribute
    $modelBinding = $attributes->get('x-model') ?? $attributes->get('model') ?? $model ?? null;
    $filteredAttributes = $attributes->except([
        'x-model', 'model', 'class', 'placeholder', 'value', 'name', 
        'required', 'disabled', 'readonly', 'id', 'prefix', 'size', 'align'
    ]);

    // Pre-format initial display server-side so display is immediate without flash
    $cleanInitial = ($value !== null && $value !== '') ? preg_replace('/\D/', '', (string) $value) : '';
    $formattedInitial = $cleanInitial !== '' ? number_format((float) $cleanInitial, 0, ',', '.') : '';

    $sizeClasses = match ($size) {
        'sm' => 'py-1 ' . ($prefix ? 'pl-7 pr-2' : 'px-2') . ' text-xs',
        'lg' => 'py-3.5 ' . ($prefix ? 'pl-11 pr-4' : 'px-4') . ' text-lg font-black',
        default => 'py-2.5 ' . ($prefix ? 'pl-10 pr-3.5' : 'px-3.5') . ' text-base font-extrabold',
    };

    $prefixPosClasses = match ($size) {
        'sm' => 'left-2 top-1.5 text-[10px]',
        'lg' => 'left-4 top-4 text-sm font-black',
        default => 'left-3.5 top-3 text-xs font-bold',
    };

    $alignClass = match ($align) {
        'right' => 'text-right',
        'center' => 'text-center',
        default => 'text-left',
    };
@endphp

<div class="relative rounded-xl shadow-2xs group inline-block w-full">
    @if ($prefix)
        <span class="absolute {{ $prefixPosClasses }} font-bold text-slate-400 select-none pointer-events-none transition group-focus-within:text-[#1E3A2B] z-10">
            {{ $prefix }}
        </span>
    @endif

    <input
        type="text"
        inputmode="numeric"
        @if ($id) id="{{ $id }}" @endif
        value="{{ $formattedInitial }}"
        placeholder="{{ $placeholder }}"
        @if ($modelBinding) x-currency="{{ $modelBinding }}" @else x-currency @endif
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        @if ($readonly) readonly @endif
        data-currency-display
        {{ $filteredAttributes->merge([
            'class' => "touch-target w-full rounded-xl border border-slate-300 bg-white text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B] focus:border-[#1E3A2B] transition {$alignClass} {$sizeClasses}"
        ]) }}
    >

    @if ($name)
        <input
            type="hidden"
            name="{{ $name }}"
            value="{{ $cleanInitial }}"
            data-currency-hidden
            @if ($modelBinding) :value="{{ $modelBinding }}" @endif
        >
    @endif
</div>
