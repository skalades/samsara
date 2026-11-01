@props([
    'padding' => 'p-5',
])

<div {{ $attributes->merge(['class' => "bg-white border border-slate-200/80 rounded-2xl shadow-xs transition duration-150 {$padding}"]) }}>
    {{ $slot }}
</div>
