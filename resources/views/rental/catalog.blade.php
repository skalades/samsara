<x-layouts.app :title="'Katalog Sewa Perlengkapan Outdoor'">
    <div class="space-y-6">

        <!-- HERO HEADER -->
        <div class="bg-gradient-to-r from-[#1E3A2B] via-[#244735] to-[#162B20] text-white rounded-2xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                <i class="fas fa-campground text-[180px]"></i>
            </div>
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#D97736] text-white uppercase tracking-wider mb-3 shadow-sm">
                    <i class="fas fa-shield-check"></i> Standard QC Basecamp SAMSARA
                </span>
                <h1 class="text-2xl sm:text-4xl font-extrabold font-heading tracking-tight leading-tight">
                    Sewa Perlengkapan Outdoor Gunung Cikuray
                </h1>
                <p class="mt-2 text-sm sm:text-base text-emerald-100/90 leading-relaxed">
                    Daki lebih ringan tanpa ribet bawa beban berat dari rumah. Seluruh alat sewa telah melewati inspeksi kebersihan dan kelayakan teknis sebelum diserahkan.
                </p>

                <!-- Quick Stats Badges -->
                <div class="mt-5 flex flex-wrap gap-3 text-xs">
                    <div class="bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/15 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Unit Siap Sewa: <strong class="text-white font-bold">{{ $totalAvailable }} dari {{ $totalItems }}</strong></span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/15 flex items-center gap-2">
                        <i class="fas fa-id-card text-[#D97736]"></i>
                        <span>Jaminan: <strong class="text-white font-bold">KTP Asli / Deposit</strong></span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/15 flex items-center gap-2">
                        <i class="fas fa-location-dot text-amber-400"></i>
                        <span>Pengambilan: <strong class="text-white font-bold">Loket Basecamp Cigedug</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEARCH & CATEGORY FILTER TABS -->
        <div x-data="{
            search: '',
            showHowToRentModal: false,
            activeItem: null,
            openRentModal(item) {
                this.activeItem = item;
                this.showHowToRentModal = true;
            }
        }" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none flex-1">
                    <a href="{{ route('rental.catalog', ['category' => 'ALL']) }}"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold whitespace-nowrap transition shadow-sm {{ $selectedCategory === 'ALL' ? 'bg-[#1E3A2B] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                        <i class="fas fa-border-all mr-1.5"></i> Semua Kategori
                    </a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('rental.catalog', ['category' => $cat]) }}"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold whitespace-nowrap transition shadow-sm {{ $selectedCategory === $cat ? 'bg-[#1E3A2B] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                            @if($cat === 'TENDA') <i class="fas fa-campground mr-1 text-[#D97736]"></i>
                            @elseif($cat === 'KOMPOR') <i class="fas fa-fire mr-1 text-orange-500"></i>
                            @elseif($cat === 'NESTING') <i class="fas fa-utensils mr-1 text-amber-500"></i>
                            @elseif($cat === 'SLEEPING_BAG') <i class="fas fa-bed mr-1 text-indigo-400"></i>
                            @elseif($cat === 'HEADLAMP') <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>
                            @else <i class="fas fa-hiking mr-1 text-emerald-500"></i>
                            @endif
                            {{ str_replace('_', ' ', $cat) }}
                        </a>
                    @endforeach
                </div>

                <div class="relative w-full sm:w-64 shrink-0">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="search" placeholder="Cari nama alat / kode..."
                           class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#1E3A2B]">
                    <button type="button" x-show="search" @click="search = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- PRODUCT GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($items as $item)
                    @php
                        $isReady = $item->status->value === 'TERSEDIA';
                    @endphp
                    <div x-show="!search || '{{ strtolower($item->name . ' ' . $item->category . ' ' . $item->asset_code) }}'.includes(search.toLowerCase())"
                         class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between">
                        <div class="p-5">
                            <!-- Top Badges -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 tracking-wider">
                                    {{ $item->category }}
                                </span>
                                @if($isReady)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Tersedia di Rak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $item->status->label() }}
                                    </span>
                                @endif
                            </div>

                            <!-- Item Icon & Name -->
                            <div class="flex items-start gap-3.5 mb-3">
                                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-[#1E3A2B] shrink-0">
                                    @if($item->category === 'TENDA') <i class="fas fa-campground text-xl text-[#1E3A2B]"></i>
                                    @elseif($item->category === 'KOMPOR') <i class="fas fa-fire text-xl text-[#D97736]"></i>
                                    @elseif($item->category === 'NESTING') <i class="fas fa-utensils text-xl text-amber-600"></i>
                                    @elseif($item->category === 'SLEEPING_BAG') <i class="fas fa-bed text-xl text-indigo-600"></i>
                                    @elseif($item->category === 'HEADLAMP') <i class="fas fa-lightbulb text-xl text-yellow-600"></i>
                                    @else <i class="fas fa-hiking text-xl text-emerald-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-base text-slate-900 leading-snug">
                                        {{ $item->name }}
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Kode Aset: <code class="font-mono text-slate-600">{{ $item->asset_code }}</code></p>
                                </div>
                            </div>

                            <!-- Highlights Specs -->
                            <div class="bg-slate-50 rounded-xl p-3 text-xs space-y-1.5 text-slate-600 mb-4 border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span>Kondisi Teknis:</span>
                                    <span class="font-semibold text-emerald-700 flex items-center gap-1">
                                        <i class="fas fa-circle-check text-xs"></i> Lolos Uji Basecamp
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Uang Jaminan:</span>
                                    <span class="font-semibold text-slate-800">Rp {{ number_format($item->deposit_rate, 0, ',', '.') }}</span>
                                </div>
                                @if($item->last_cleaned_at)
                                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                                        <span>Steril & Cuci:</span>
                                        <span>{{ $item->last_cleaned_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer / Price & Action -->
                        <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Tarif Sewa</span>
                                <div class="font-heading font-extrabold text-base sm:text-lg text-[#1E3A2B]">
                                    Rp {{ number_format($item->rental_rate_per_day, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-500">/ malam</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <a href="https://wa.me/6281223456789?text={{ urlencode('Halo Basecamp SAMSARA, saya ingin tanya sewa alat: ' . $item->name . ' (Kode: ' . $item->asset_code . ')') }}"
                                   target="_blank"
                                   class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition"
                                   title="Tanya ketersediaan via WhatsApp">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </a>

                                @if($isReady)
                                    <button type="button"
                                        @click="openRentModal({
                                            id: {{ $item->id }},
                                            name: '{{ addslashes($item->name) }}',
                                            code: '{{ $item->asset_code }}',
                                            rate: '{{ number_format($item->rental_rate_per_day, 0, ',', '.') }}',
                                            deposit: '{{ number_format($item->deposit_rate, 0, ',', '.') }}'
                                        })"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white text-xs font-bold transition shadow-sm active:scale-95 cursor-pointer">
                                        <i class="fas fa-plus-circle"></i> Sewa
                                    </button>
                                @else
                                    <button disabled class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-200 text-slate-400 text-xs font-medium cursor-not-allowed">
                                        <i class="fas fa-clock"></i> Dipakai
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200">
                        <i class="fas fa-campground text-slate-300 text-4xl mb-3"></i>
                        <p class="text-sm font-semibold text-slate-700">Tidak ada perlengkapan pada kategori ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- HOW TO RENT / RESERVATION MODAL -->
            <div x-show="showHowToRentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen p-4 text-center">
                    <div x-show="showHowToRentModal" @click="showHowToRentModal = false"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                    <div x-show="showHowToRentModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 relative z-10">

                        <div class="flex items-start justify-between pb-4 border-b border-slate-100 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#D97736] border border-amber-200 flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-campground"></i>
                                </div>
                                <div>
                                    <h3 class="font-heading font-extrabold text-base text-slate-900 leading-tight">
                                        Reservasi Sewa Alat
                                    </h3>
                                    <p class="text-xs text-slate-500" x-text="activeItem ? activeItem.name + ' (' + activeItem.code + ')' : ''"></p>
                                </div>
                            </div>
                            <button type="button" @click="showHowToRentModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                                <i class="fas fa-times text-base"></i>
                            </button>
                        </div>

                        <!-- Price Info Card -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 mb-4 text-xs">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-slate-500">Tarif Sewa:</span>
                                <span class="font-extrabold text-[#1E3A2B]" x-text="'Rp ' + (activeItem ? activeItem.rate : 0) + ' / malam'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Deposit / Jaminan:</span>
                                <span class="font-semibold text-slate-700" x-text="'Rp ' + (activeItem ? activeItem.deposit : 0) + ' (atau KTP Asli)'"></span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                            Pilih cara peminjaman yang paling nyaman untuk pendakian Anda:
                        </p>

                        <!-- Action Options -->
                        <div class="space-y-3">
                            <!-- Option 1: Buat Booking SIMAKSI Baru -->
                            <a href="{{ route('simaksi.create') }}" class="block p-3.5 rounded-2xl border-2 border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 transition group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-xs shrink-0">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div>
                                            <div class="font-heading font-bold text-xs text-slate-900 group-hover:text-emerald-900">
                                                Booking Bersama Tiket SIMAKSI
                                            </div>
                                            <div class="text-[11px] text-slate-500">Belum punya izin simaksi? Daftar online sekaligus sewa alat.</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-700"></i>
                                </div>
                            </a>

                            <!-- Option 2: Chat WhatsApp Admin Basecamp -->
                            <a :href="'https://wa.me/6281223456789?text=' + encodeURIComponent('Halo Basecamp Cigedug SAMSARA, saya ingin reservasi/sewa alat ' + (activeItem ? activeItem.name + ' (Kode ' + activeItem.code + ')' : '') + '. Saya sudah ada jadwal pendakian.')"
                               target="_blank"
                               class="block p-3.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-[#25D366] text-white flex items-center justify-center text-xs shrink-0">
                                            <i class="fab fa-whatsapp text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-heading font-bold text-xs text-slate-900">
                                                Reservasi Cepat via WhatsApp
                                            </div>
                                            <div class="text-[11px] text-slate-500">Sudah daftar SIMAKSI? Konfirmasi ketersediaan alat langsung ke petugas.</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-[#25D366]"></i>
                                </div>
                            </a>

                            <!-- Option 3: Petugas Kasir / Basecamp -->
                            @auth
                                @if(auth()->user()->role?->value === 'STAFF_BASECAMP' || auth()->user()->role?->value === 'SUPERADMIN' || auth()->user()->role?->value === 'PENGELOLA')
                                    <a href="{{ route('pos.index') }}" class="block p-3 rounded-xl border border-dashed border-amber-300 bg-amber-50/60 hover:bg-amber-100/60 transition text-center text-xs font-bold text-amber-900">
                                        <i class="fas fa-cash-register mr-1.5 text-[#D97736]"></i> Proses di Layar Kasir POS (Staf Basecamp)
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROCEDURES & RULES (SOP SEWA ALAT) -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mt-8">
            <h2 class="text-lg sm:text-xl font-heading font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fas fa-book-open-reader text-[#D97736]"></i> SOP & Syarat Sewa Alat Basecamp SAMSARA
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs sm:text-sm">
                <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center mb-2.5">
                            1
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">Booking / Reservasi</h4>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Pilih perlengkapan saat mengisi reservasi SIMAKSI online atau hubungi loket basecamp sebelum kedatangan.
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-100 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-lg bg-amber-600 text-white font-bold flex items-center justify-center mb-2.5">
                            2
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">Jaminan Identitas</h4>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Ketua rombongan wajib menitipkan 1 identitas fisik asli (KTP/SIM) atau uang jaminan yang dapat di-refund saat checkout.
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-sky-50/60 border border-sky-100 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-lg bg-sky-600 text-white font-bold flex items-center justify-center mb-2.5">
                            3
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">Cek Fisik Bersama</h4>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Petugas kasir basecamp bersama pendaki akan memeriksa keutuhan frame, pasak, resleting, dan kain tenda sebelum naik.
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-100 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-lg bg-purple-600 text-white font-bold flex items-center justify-center mb-2.5">
                            4
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">Pengembalian & QC</h4>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Setelah turun dari Cikuray, kembalikan alat ke loket. Jaminan identitas akan dikembalikan 100% setelah inspeksi selesai.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
