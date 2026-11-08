@props([
    'show' => 'false',
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'maxWidth' => 'max-w-xl',
    'footer' => null,
])

<div x-cloak
     x-show="{{ $show }}"
     @keydown.escape.window="{{ $show }} = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     style="display: none;">

    <!-- BACKDROP BLUR -->
    <div x-show="{{ $show }}"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="{{ $show }} = false"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity">
    </div>

    <!-- WRAPPER -->
    <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4 text-center sm:p-0">
        <!-- MODAL CARD -->
        <div x-show="{{ $show }}"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full {{ $maxWidth }} transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-200 my-8">

            <!-- Header -->
            @if ($title || $icon)
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if ($icon)
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg shrink-0">
                                <i class="{{ $icon }}"></i>
                            </div>
                        @endif
                        <div>
                            @if ($title)
                                <h3 class="font-heading font-extrabold text-lg text-slate-900 leading-tight" id="modal-title">
                                    {{ $title }}
                                </h3>
                            @endif
                            @if ($subtitle)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>

                    <button type="button"
                            @click="{{ $show }} = false"
                            class="touch-target text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 p-2 transition">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>
            @endif

            <!-- Body -->
            <div class="px-6 py-5 max-h-[75vh] overflow-y-auto slim-scrollbar text-slate-700 text-xs sm:text-sm">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if ($footer)
                <div class="px-6 py-4 bg-slate-50/90 border-t border-slate-100 flex flex-wrap items-center justify-end gap-2.5">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
