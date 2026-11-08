<div x-data="{ open: false, query: '' }"
     @open-ticket-lookup.window="open = true; $nextTick(() => $refs.searchInput.focus())"
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
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

    <!-- Modal Box -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 p-6 sm:p-7">

            <!-- Close Button -->
            <button type="button" @click="open = false"
                    class="absolute top-4 right-4 w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 transition flex items-center justify-center cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>

            <!-- Header -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-100 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-qrcode text-emerald-700"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-lg text-slate-900 leading-tight">Cari E-Ticket & Status Pesanan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Masukkan Kode Booking SIMAKSI atau No. WhatsApp Ketua</p>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('simaksi.check') }}" method="GET" class="mt-4 space-y-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kode Booking / No. WhatsApp:
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                        <input type="text" name="code" x-ref="searchInput" x-model="query"
                               placeholder="Contoh: SMX-202610-001 atau 0812..."
                               class="block w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B] text-sm font-medium">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button type="submit"
                            class="touch-target flex-1 py-2.5 rounded-lg bg-[#1E3A2B] hover:bg-[#12241B] text-white font-semibold text-xs sm:text-sm transition flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                        <i class="fas fa-arrow-right text-[#D97736]"></i> Cari Data Tiket
                    </button>
                    <button type="button" @click="open = false"
                            class="touch-target px-4 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs transition cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>

            <!-- Quick Demo Helper -->
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Ingin mencoba fitur?</span>
                <a href="{{ route('simaksi.check', ['code' => 'A101']) }}"
                   class="font-bold text-emerald-700 hover:underline flex items-center gap-1">
                    <i class="fas fa-play text-[10px]"></i> Buka Tiket Demo (A101)
                </a>
            </div>
        </div>
    </div>
</div>
