@props([
    'icon' => 'fas fa-inbox',
    'title' => 'Belum Ada Data',
    'description' => 'Data yang Anda cari belum tersedia atau belum ada entri yang ditambahkan.',
    'compact' => false,
])

<div {{ $attributes->merge(['class' => 'text-center ' . ($compact ? 'py-8 px-4' : 'py-12 px-6 sm:py-16 sm:px-8') . ' flex flex-col items-center justify-center']) }}>
    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl sm:text-3xl mb-3.5 shadow-2xs">
        <i class="{{ $icon }}"></i>
    </div>

    <h3 class="font-heading font-extrabold text-base sm:text-lg text-slate-800 tracking-tight">
        {{ $title }}
    </h3>

    @if ($description)
        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
            {{ $description }}
        </p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-5 flex flex-wrap items-center justify-center gap-2.5">
            {{ $slot }}
        </div>
    @endif
</div>
