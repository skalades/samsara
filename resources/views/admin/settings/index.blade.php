<x-layouts.admin title="Pengaturan Tarif SIMAKSI & Jalur">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
         x-data="{
            ticketFee: {{ (int)($trailSetting->ticket_fee_per_person ?? 25000) }},
            ecoDeposit: {{ (int)($trailSetting->eco_deposit_fee ?? 20000) }},
            insuranceFee: {{ (int)($trailSetting->insurance_fee ?? 5000) }},
            padDesaFee: {{ (int)($trailSetting->pad_desa_fee ?? 5000) }},
            trailStatus: '{{ $trailSetting->trail_status->value ?? 'OPEN' }}',
            weatherStatus: '{{ $trailSetting->weather_status->value ?? 'CERAH' }}',
            announcement: '{{ addslashes($trailSetting->announcement ?? '') }}',

            formatRupiah(num) {
                return 'Rp ' + Number(num).toLocaleString('id-ID');
            }
         }">

        <!-- HEADER & BREADCRUMB -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1.5">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700">Home</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <a href="{{ route('admin.index') }}" class="hover:text-emerald-700">Command Center</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-800 font-bold">Pengaturan Tarif SIMAKSI & Jalur</span>
                </nav>
                <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fas fa-sliders text-[#D97736]"></i>
                    Konfigurasi Tarif & Operasional Jalur
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Atur tarif resmi SIMAKSI, deposit sampah (*eco deposit*), status darurat jalur, dan kondisi cuaca Cikuray.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.index') }}"
                   class="touch-target px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold border border-slate-300 flex items-center gap-2 shadow-xs transition">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- MAIN FORM (LEFT 2 COLS) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- CARD 1: TARIF RESMI SIMAKSI -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg">
                                <i class="fas fa-ticket-simple"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-black text-lg text-slate-900">Tarif Resmi Masuk & SIMAKSI</h3>
                                <p class="text-xs text-slate-500">Kalkulasi biaya otomatis pada booking online & kasir loket.</p>
                            </div>
                        </div>

                        <div class="space-y-4 text-xs sm:text-sm">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">
                                    Harga Tiket SIMAKSI per Orang (Rp):
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none font-bold text-slate-400">
                                        Rp
                                    </span>
                                    <input type="number" name="ticket_fee_per_person" x-model.number="ticketFee" required min="0" step="1000"
                                           class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-300 font-mono font-black text-base text-slate-900 focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Tarif resmi pendakian per orang untuk durasi normal (termasuk asuransi).</p>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">
                                    Deposit Jaminan Sampah / Eco Deposit (Rp per Rombongan):
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none font-bold text-slate-400">
                                        Rp
                                    </span>
                                    <input type="number" name="eco_deposit_fee" x-model.number="ecoDeposit" required min="0" step="1000"
                                           class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-300 font-mono font-black text-base text-emerald-700 focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Uang jaminan 100% dikembalikan jika rombongan membawa turun sampah di Pos 1/Basecamp.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Alokasi Premi Asuransi (Rp):</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none font-bold text-slate-400 text-xs">Rp</span>
                                        <input type="number" name="insurance_fee" x-model.number="insuranceFee" required min="0"
                                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 font-mono text-xs focus:ring-2 focus:ring-[#2D5841]">
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Komponen premi proteksi SAR / Jiwa.</p>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Alokasi PAD Desa Cigedug (Rp):</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none font-bold text-slate-400 text-xs">Rp</span>
                                        <input type="number" name="pad_desa_fee" x-model.number="padDesaFee" required min="0"
                                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 font-mono text-xs focus:ring-2 focus:ring-[#2D5841]">
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Retribusi pembangunan desa lokal.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: STATUS JALUR & CUACA -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-lg">
                                <i class="fas fa-mountain-sun"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-black text-lg text-slate-900">Operasional Jalur & Cuaca</h3>
                                <p class="text-xs text-slate-500">Live banner di portal pengunjung dan peringatan petugas pos.</p>
                            </div>
                        </div>

                        <div class="space-y-4 text-xs sm:text-sm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Status Operasional Jalur:</label>
                                    <select name="trail_status" x-model="trailStatus" required
                                            class="w-full py-2.5 px-3 rounded-xl border border-slate-300 font-bold text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                        <option value="OPEN">🟢 OPEN - Buka Normal</option>
                                        <option value="WARNING">🟡 WARNING - Waspada Cuaca / Angin</option>
                                        <option value="CLOSED">🔴 CLOSED - Tutup Darurat</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Kondisi Cuaca Lapangan:</label>
                                    <select name="weather_status" x-model="weatherStatus" required
                                            class="w-full py-2.5 px-3 rounded-xl border border-slate-300 font-bold text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                        <option value="CERAH">☀️ CERAH - Langit Bersih & Terik</option>
                                        <option value="BERAWAN">⛅ BERAWA - Sejuk Berkabut Tipis</option>
                                        <option value="HUJAN_RINGAN">🌦️ HUJAN RINGAN - Gerimis di Jalur</option>
                                        <option value="HUJAN_LEBAT">🌧️ HUJAN LEBAT - Vegetasi Basah Licin</option>
                                        <option value="BADAI">⛈️ BADAI - Angin Kencang & Petir</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Pengumuman Resmi Basecamp (Announcement):</label>
                                <textarea name="announcement" x-model="announcement" rows="3"
                                          placeholder="Contoh: Jalur pendakian Cikuray via Cigedug BUKA NORMAL. Angin sejuk di Pos 4. Wajib bawa trashbag!"
                                          class="w-full rounded-2xl border border-slate-300 p-3 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Teks ini akan muncul sebagai running text / banner darurat di halaman utama.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="touch-target px-8 py-3.5 rounded-2xl bg-[#2D5841] hover:bg-[#1E3A2B] text-white font-heading font-black text-sm shadow-xl flex items-center gap-2 transition active:scale-95">
                            <i class="fas fa-floppy-disk text-amber-300"></i> Simpan Seluruh Pengaturan
                        </button>
                    </div>

                </div>

                <!-- PREVIEW SIDEBAR (RIGHT 1 COL) -->
                <div class="space-y-6">

                    <!-- REAL-TIME PREVIEW CARD -->
                    <div class="bg-gradient-to-b from-[#12241B] to-[#1E3A2B] text-white rounded-3xl p-6 shadow-xl border border-[#2D5841]">
                        <div class="flex items-center justify-between pb-3 border-b border-[#2D5841]/70 mb-4">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400">Live Preview Publik</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>

                        <!-- TRAIL STATUS BADGE -->
                        <div class="mb-4">
                            <div class="text-[11px] text-emerald-200/80 mb-1">Status Jalur:</div>
                            <template x-if="trailStatus === 'OPEN'">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-check"></i> JALUR BUKA NORMAL
                                </span>
                            </template>
                            <template x-if="trailStatus === 'WARNING'">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1.5">
                                    <i class="fas fa-triangle-exclamation"></i> WASPADA CUACA
                                </span>
                            </template>
                            <template x-if="trailStatus === 'CLOSED'">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-500/20 text-rose-300 border border-rose-500/40 inline-flex items-center gap-1.5">
                                    <i class="fas fa-ban"></i> JALUR DITUTUP
                                </span>
                            </template>
                        </div>

                        <!-- SIMULASI BIAYA ROMBONGAN -->
                        <div class="bg-white/10 rounded-2xl p-4 mb-4 backdrop-blur-xs border border-white/10">
                            <div class="text-[10px] font-bold uppercase text-amber-300 tracking-wider mb-2">Simulasi 1 Rombongan (3 Orang)</div>
                            <div class="space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-slate-300">
                                    <span>Tiket 3 Orang:</span>
                                    <span class="font-mono font-bold text-white" x-text="formatRupiah(ticketFee * 3)"></span>
                                </div>
                                <div class="flex items-center justify-between text-slate-300">
                                    <span>Eco Deposit (Sampah):</span>
                                    <span class="font-mono font-bold text-emerald-300" x-text="formatRupiah(ecoDeposit)"></span>
                                </div>
                                <div class="pt-2 border-t border-white/20 flex items-center justify-between font-bold text-amber-200 text-sm">
                                    <span>Total Bayar:</span>
                                    <span class="font-mono font-black" x-text="formatRupiah((ticketFee * 3) + ecoDeposit)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- ANNOUNCEMENT PREVIEW -->
                        <div>
                            <div class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1">Banner Pengumuman:</div>
                            <p class="text-xs text-emerald-100/90 italic bg-black/20 p-3 rounded-xl border border-white/5"
                               x-text="announcement || 'Tidak ada pengumuman saat ini.'"></p>
                        </div>
                    </div>

                    <!-- PANDUAN PENGATURAN -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-xs text-xs text-slate-600 space-y-3">
                        <div class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-lightbulb text-amber-500"></i> Catatan Kebijakan SAMSARA
                        </div>
                        <p class="leading-relaxed">
                            Setiap perubahan tarif tiket otomatis langsung berlaku bagi calon pendaki yang melakukan <strong>booking baru</strong> dan transaksi kasir loket. Booking lama yang sudah diterbitkan tetap memakai nominal saat dibuat.
                        </p>
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-semibold">
                            <i class="fas fa-info-circle mr-1"></i> Data di-*cache* selama 5 menit untuk menjaga performa server di bawah lonjakan akhir pekan.
                        </div>
                    </div>

                </div>

            </div>
        </form>

    </div>
</x-layouts.admin>
