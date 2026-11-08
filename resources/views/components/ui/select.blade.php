@props([
    'name' => null,
    'label' => null,
    'options' => null,
    'selected' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'hint' => null,
    'size' => 'md',
    'id' => null,
    'icon' => null,
])

@php
    $selectId = $id ?? ($name ? 'select-' . $name : 'select-' . uniqid());
    $hasError = $error || ($name && $errors->has($name));
    $errorMessage = $error ?? ($name ? $errors->first($name) : null);

    $sizeClasses = match ($size) {
        'sm' => 'py-2 ' . ($icon ? 'pl-9' : 'pl-3') . ' pr-8 text-xs',
        'lg' => 'py-3.5 ' . ($icon ? 'pl-11' : 'pl-4') . ' pr-10 text-base font-semibold',
        default => 'py-2.5 ' . ($icon ? 'pl-10' : 'pl-3.5') . ' pr-9 text-sm',
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
        <label for="{{ $selectId }}" class="block text-xs font-bold text-slate-700">
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

        <select
            @if ($name) name="{{ $name }}" @endif
            id="{{ $selectId }}"
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            {{ $attributes->merge([
                'class' => "touch-target w-full rounded-xl border appearance-none {$borderClasses} focus:outline-none focus:ring-3 transition cursor-pointer {$sizeClasses}"
            ]) }}
        >
            @if (is_array($options) || is_iterable($options))
                @foreach ($options as $val => $lbl)
                    <option value="{{ $val }}" {{ (string) old($name, $selected) === (string) $val ? 'selected' : '' }}>
                        {{ $lbl }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>

        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400 group-focus-within:text-[#1E3A2B]">
            <i class="fas fa-chevron-down text-xs"></i>
        </div>
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
