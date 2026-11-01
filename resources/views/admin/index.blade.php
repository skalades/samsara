<x-layouts.admin title="Command Center SAMSARA">
    <div x-data="{
        showEmergencyModal: false,
        trailStatus: '{{ $pulse['trail_setting']->trail_status->value }}',
        weatherStatus: '{{ $pulse['trail_setting']->weather_status->value }}',
        announcement: '{{ addslashes($pulse['trail_setting']->announcement ?? '') }}'
    }">
        <!-- TOP ADAPTIVE BAR -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                    <i class="fas fa-satellite-dish"></i> Pusat Kendali Terpadu (Command Center)
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">Mountain Pulse Live</h1>
                <p class="text-xs sm:text-sm text-slate-500">Monitoring real-time aktivitas pendakian, kuota harian, SAR darurat, dan audit finansial.</p>
            </div>

            <!-- Emergency Quick Actions Buttons (Ergonomic Thumb Targets) -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.analytics') }}" class="touch-target px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fas fa-chart-line"></i>
                    <span>Analitik & Tren</span>
                </a>

                <button type="button" @click="showEmergencyModal = true" class="touch-target px-4 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>Kendali Darurat</span>
                </button>

                <a href="{{ route('admin.tracker') }}" class="touch-target px-4 py-2.5 rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fas fa-map-location-dot text-[#D97736]"></i>
                    <span class="hidden sm:inline">Live Pos Tracker</span>
                </a>
            </div>
        </div>

        <!-- MOUNTAIN PULSE KPI METRICS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <x-ui.stat-card
                title="Pendaki di Jalur"
                value="{{ $pulse['hikers_on_trail'] }} Orang"
                subtext="{{ $pulse['groups_on_trail'] }} Rombongan Aktif"
                icon="fas fa-hiking"
                color="forest"
            />

            <x-ui.stat-card
                title="Sisa Kuota Hari Ini"
                value="{{ $pulse['remaining_quota'] }} / {{ $pulse['max_quota'] }}"
                subtext="Terisi {{ $pulse['booked_count'] }} Pendaki"
                icon="fas fa-users"
                color="earth"
            />

            <x-ui.stat-card
                title="Omset Kasir Hari Ini"
                value="Rp {{ number_format($pulse['today_revenue'], 0, ',', '.') }}"
                subtext="Loket, Toko & Pre-Order"
                icon="fas fa-wallet"
                color="emerald"
            />

            <x-ui.stat-card
                title="Status Jalur"
                value="{{ $pulse['trail_setting']->trail_status->label() }}"
                subtext="Cuaca: {{ $pulse['trail_setting']->weather_status->label() }}"
                icon="fas fa-mountain"
                color="{{ $pulse['trail_setting']->trail_status->value === 'OPEN' ? 'emerald' : 'rose' }}"
            />
        </div>

        {{-- KPI Tambahan: Pending Payments --}}
        @if ($pulse['pending_payments_count'] > 0)
            <div class="grid grid-cols-1 mb-6">
                <a href="{{ route('admin.bookings.pending') }}" class="block">
                    <x-ui.stat-card
                        title="Menunggu Konfirmasi Pembayaran"
                        value="{{ $pulse['pending_payments_count'] }} Booking"
                        subtext="Klik untuk verifikasi & konfirmasi"
                        icon="fas fa-money-check-alt"
                        color="amber"
                    />
                </a>
            </div>
        @endif

        @if ($pulse['pending_payments_count'] > 0)
            <!-- PENDING PAYMENT ALERT -->
            <div class="mb-6 p-4 rounded-3xl bg-amber-50 border-2 border-amber-300 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2.5 text-amber-900">
                        <span class="w-8 h-8 rounded-xl bg-amber-400 text-white flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fas fa-money-check-alt"></i>
                        </span>
                        <div>
                            <div class="font-heading font-extrabold text-sm">
                                Ada {{ $pendingBookingsCount }} Booking Menunggu Konfirmasi Pembayaran
                            </div>
                            <p class="text-[11px] text-amber-700">
                                Pendaki sudah submit form namun pembayaran belum diverifikasi. Segera tinjau dan konfirmasi.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.bookings.pending') }}" class="touch-target flex-shrink-0 px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-sm transition flex items-center gap-2 self-start sm:self-center">
                        <i class="fas fa-arrow-right"></i> Verifikasi Sekarang
                    </a>
                </div>
            </div>
        @endif

        @if ($activeSosList->isNotEmpty())
            <!-- ACTIVE SAR ALERTS LIST -->
            <div class="mb-6 p-4 rounded-3xl bg-rose-50 border-2 border-rose-300 shadow-sm space-y-2">
                <div class="flex items-center justify-between pb-2 border-b border-rose-200">
                    <div class="flex items-center gap-2 text-rose-900 font-heading font-extrabold text-sm">
                        <i class="fas fa-broadcast-tower text-rose-600 animate-ping"></i>
                        <span>ALERT SAR DARURAT PERLU TINDAKAN ({{ $activeSosList->count() }})</span>
                    </div>
                </div>

                <div class="space-y-2">
                    @foreach ($activeSosList as $sos)
                        <div class="p-3 bg-white rounded-2xl border border-rose-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                            <div>
                                <span class="font-bold text-rose-700 uppercase">[{{ $sos->alert_type->label() }}]</span>
                                <span class="font-bold text-slate-800 ml-1">di {{ $sos->pos_location }}</span>
                                <p class="text-slate-600 mt-0.5">"{{ $sos->description }}" &bull; Pelapor: {{ $sos->reporter_name }} ({{ $sos->reporter_phone }})</p>
                            </div>

                            @if ($sos->booking_id)
                                <a href="{{ route('admin.sar-export', $sos->booking_id) }}" class="touch-target px-3 py-1.5 rounded-xl bg-rose-600 text-white font-bold text-xs flex items-center justify-center gap-1">
                                    <i class="fas fa-file-pdf"></i> Manifest SAR
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 2-COL ADAPTIVE DASHBOARD (Tracker Preview + Quick Modules) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-8">
            <!-- LIVE POS DISTRIBUTION PREVIEW (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900">Distribusi Rombongan di Jalur</h3>
                        <p class="text-xs text-slate-500">Pemindaian terakhir berdasarkan checkpoint petugas lapangan.</p>
                    </div>

                    <a href="{{ route('admin.tracker') }}" class="text-xs font-bold text-[#D97736] hover:text-[#B25A20] flex items-center gap-1">
                        Lihat Peta Penuh <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach ($posDistribution as $posName => $groups)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-[#1E3A2B] text-emerald-300 flex items-center justify-center font-bold text-xs">
                                    <i class="fas fa-campground"></i>
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $posName }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ count($groups) }} Rombongan &bull; {{ collect($groups)->sum('total_hikers') }} Pendaki
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5">
                                @foreach ($groups as $g)
                                    <a href="{{ route('admin.sar-export', $g->id) }}" title="Lihat Manifest SAR Rombongan"
                                        class="px-2.5 py-1 rounded-lg bg-white border border-slate-300 hover:border-emerald-500 text-slate-800 text-[11px] font-semibold flex items-center gap-1">
                                        <span>{{ $g->leader_name }} ({{ $g->total_hikers }}p)</span>
                                        <i class="fas fa-id-card text-[10px] text-[#D97736]"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ADMIN QUICK NAVIGATION & AUDIT (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 space-y-3">
                    <h3 class="font-heading font-extrabold text-sm text-slate-900 pb-2 border-b border-slate-100">
                        Sub-Modul Administrasi
                    </h3>

                    <div class="space-y-2 text-xs font-semibold">
                        <a href="{{ route('admin.analytics') }}" class="touch-target w-full rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 hover:from-amber-100 hover:to-orange-100 p-3 text-slate-800 flex items-center justify-between border border-amber-200 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-chart-line text-[#D97736]"></i>
                                <span>Analitik & Tren Pendaki</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-[#D97736] text-white">Chart.js</span>
                        </a>

                        <a href="{{ route('admin.checkpoints.index') }}" class="touch-target w-full rounded-2xl bg-slate-50 hover:bg-slate-100 p-3 text-slate-800 flex items-center justify-between border border-slate-200/80 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-camera text-emerald-600"></i> Dokumentasi Foto Checkpoint
                            </span>
                            <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
                        </a>

                        <a href="{{ route('admin.tracker') }}" class="touch-target w-full rounded-2xl bg-slate-50 hover:bg-slate-100 p-3 text-slate-800 flex items-center justify-between border border-slate-200/80 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-compass text-[#D97736]"></i> Live Pos Tracker & SAR
                            </span>
                            <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
                        </a>

                        <a href="{{ route('admin.bookings.pending') }}" class="touch-target w-full rounded-2xl {{ $pendingBookingsCount > 0 ? 'bg-amber-50 hover:bg-amber-100 border-amber-200' : 'bg-slate-50 hover:bg-slate-100 border-slate-200/80' }} p-3 text-slate-800 flex items-center justify-between border transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-money-check-alt {{ $pendingBookingsCount > 0 ? 'text-amber-600' : 'text-slate-400' }}"></i> Konfirmasi Pembayaran
                            </span>
                            <div class="flex items-center gap-2">
                                @if ($pendingBookingsCount > 0)
                                    <x-ui.badge color="amber" size="sm">{{ $pendingBookingsCount }}</x-ui.badge>
                                @endif
                                <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
                            </div>
                        </a>

                        <a href="{{ route('admin.quota') }}" class="touch-target w-full rounded-2xl bg-slate-50 hover:bg-slate-100 p-3 text-slate-800 flex items-center justify-between border border-slate-200/80 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-calendar-day text-emerald-600"></i> Kelola Kuota & Blackout
                            </span>
                            <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
                        </a>

                        <a href="{{ route('admin.finance') }}" class="touch-target w-full rounded-2xl bg-slate-50 hover:bg-slate-100 p-3 text-slate-800 flex items-center justify-between border border-slate-200/80 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-chart-pie text-sky-600"></i> Bagi Hasil Multi-Pihak
                            </span>
                            <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
                        </a>

                        <a href="{{ route('pos.shift') }}" class="touch-target w-full rounded-2xl bg-slate-50 hover:bg-slate-100 p-3 text-slate-800 flex items-center justify-between border border-slate-200/80 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-cash-register text-amber-600"></i> Audit Shift Kasir Loket
                            </span>
                            <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
                        </a>

                        <a href="{{ route('consignment.index') }}" class="touch-target w-full rounded-2xl bg-slate-50 hover:bg-slate-100 p-3 text-slate-800 flex items-center justify-between border border-slate-200/80 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-handshake text-yellow-600"></i> Settlement Mitra UMKM
                            </span>
                            <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Low Stock Alert Box -->
                @if ($pulse['low_stock_products_count'] > 0)
                    <div class="p-4 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <i class="fas fa-box-open text-amber-600"></i>
                            <span>Peringatan Stok Menipis</span>
                        </div>
                        <p class="text-[11px] text-amber-800">
                            Ada {{ $pulse['low_stock_products_count'] }} barang habis pakai (gas/logistik/baterai) yang stoknya di bawah batas aman.
                        </p>
                        <a href="{{ route('store.index') }}" class="font-bold text-[#D97736] underline mt-1 block">Cek Katalog Toko & Restock</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- EMERGENCY TRAIL CONTROL MODAL -->
        <div x-show="showEmergencyModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showEmergencyModal" @click="showEmergencyModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="showEmergencyModal" class="inline-block bg-white rounded-3xl p-6 text-left shadow-2xl max-w-md w-full border border-slate-200 relative z-10 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm font-black">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <h3 class="font-heading font-black text-base text-slate-900">Kendali Status Jalur Operasional</h3>
                        </div>
                        <button type="button" @click="showEmergencyModal = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.trail-control') }}" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Operasional Jalur Cikuray</label>
                            <select name="trail_status" x-model="trailStatus" class="touch-target w-full rounded-xl border border-slate-300 p-2 font-bold text-slate-900">
                                <option value="OPEN">BUKA NORMAL (Penjualan kuota online aktif)</option>
                                <option value="WARNING">WASPADA (Hati-hati cuaca/jalur licin)</option>
                                <option value="CLOSED">TUTUP DARURAT (Hentikan seluruh booking kuota)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Cuaca Puncak</label>
                            <select name="weather_status" x-model="weatherStatus" class="touch-target w-full rounded-xl border border-slate-300 p-2 text-slate-900">
                                <option value="CERAH">Cerah Berawan</option>
                                <option value="BERAWAN">Mendung / Berkabut Tebal</option>
                                <option value="HUJAN_RINGAN">Hujan Ringan</option>
                                <option value="HUJAN_LEBAT">Hujan Lebat & Angin Kencang</option>
                                <option value="BADAI">Badai Ekstrem & Petir</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Broadcast Pengumuman / Peringatan</label>
                            <textarea name="announcement" x-model="announcement" rows="3" class="w-full rounded-xl border border-slate-300 p-3 text-slate-900"></textarea>
                        </div>

                        <button type="submit" class="touch-target w-full rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-heading font-black text-xs shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-save"></i>
                            <span>Simpan & Broadcast Status Jalur</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
