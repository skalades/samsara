@props([
    'name' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'icon' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'hint' => null,
    'size' => 'md',
    'id' => null,
])

@php
    $inputId = $id ?? ($name ? 'input-' . $name : 'input-' . uniqid());
    $hasError = $error || ($name && $errors->has($name));
    $errorMessage = $error ?? ($name ? $errors->first($name) : null);

    $sizeClasses = match ($size) {
        'sm' => 'py-2 ' . ($icon ? 'pl-9 pr-3' : 'px-3') . ' text-xs',
        'lg' => 'py-3.5 ' . ($icon ? 'pl-11 pr-4' : 'px-4') . ' text-base font-semibold',
        default => 'py-2.5 ' . ($icon ? 'pl-10 pr-3.5' : 'px-3.5') . ' text-sm',
    };

    $iconPosClasses = match ($size) {
        'sm' => 'left-2.5 text-xs',
        'lg' => 'left-4 text-base',
        default => 'left-3.5 text-sm',
    };

    $borderClasses = $hasError
        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20 text-rose-950 bg-rose-50/30'
        : 'border-slate-300 focus:border-[#1E3A2B] focus:ring-[#1E3A2B]/20 text-slate-900 bg-white';
@endphp

<div class="w-full space-y-1.5">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500 font-bold ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-2xs group">
        @if ($icon)
            <div class="absolute inset-y-0 {{ $iconPosClasses }} flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#1E3A2B] transition">
                <i class="{{ $icon }}"></i>
            </div>
        @endif

        <input
            type="{{ $type }}"
            @if ($name) name="{{ $name }}" @endif
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            @if ($readonly) readonly @endif
            {{ $attributes->merge([
                'class' => "touch-target w-full rounded-xl border {$borderClasses} placeholder:text-slate-400 focus:outline-none focus:ring-3 transition {$sizeClasses}"
            ]) }}
        >
    </div>

    @if ($hasError)
        <p class="text-[11px] font-semibold text-rose-600 flex items-center gap-1.5 mt-1">
            <i class="fas fa-exclamation-circle text-xs"></i>
            <span>{{ $errorMessage }}</span>
        </p>
    @elseif ($hint)
        <p class="text-[11px] text-slate-400 mt-1">{{ $hint }}</p>
    @endif
</div>
