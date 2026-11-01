<div x-data="{ open: false, src: '', title: '', desc: '' }"
     @open-lightbox.window="src = $event.detail.src; title = $event.detail.title; desc = $event.detail.desc; open = true;"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-slate-950/90 backdrop-blur-md transition-opacity"></div>

    <!-- Modal Box -->
    <div class="flex min-h-full items-center justify-center p-3 sm:p-6 text-center">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 text-white text-left shadow-2xl transition-all w-full max-w-4xl border border-white/20">

            <!-- Close Button -->
            <button type="button" @click="open = false"
                    class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-black/60 hover:bg-black/90 text-white transition flex items-center justify-center cursor-pointer border border-white/20">
                <i class="fas fa-times text-base"></i>
            </button>

            <!-- Large Photo -->
            <div class="relative max-h-[70vh] overflow-hidden bg-black flex items-center justify-center">
                <img :src="src" :alt="title" class="max-h-[70vh] w-full object-contain">
            </div>

            <!-- Details Caption Bar -->
            <div class="p-5 sm:p-6 bg-slate-950 border-t border-white/10">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="text-xs font-bold text-amber-300 uppercase tracking-wider font-mono">Dokumentasi Resmi Jalur SAMSARA Cigedug</span>
                </div>
                <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-white" x-text="title"></h3>
                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed" x-text="desc"></p>
            </div>
        </div>
    </div>
</div>
