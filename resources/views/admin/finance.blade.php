<x-layouts.admin title="Bagi Hasil Multi-Pihak">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Mountain Pulse
                </a>
                <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900">Bagi Hasil Multi-Pihak & Audit</h1>
                <p class="text-xs sm:text-sm text-slate-500">Pemisahan otomatis pendapatan tiket SIMAKSI, retribusi perhutani, PAD desa, asuransi, dan UMKM.</p>
            </div>

            <!-- Date Filter Form -->
            <form action="{{ route('admin.finance') }}" method="GET" class="flex items-center gap-2 text-xs">
                <input type="date" name="start_date" value="{{ $startStr }}" class="rounded-xl border border-slate-300 p-2 text-xs font-bold text-slate-800">
                <span class="text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ $endStr }}" class="rounded-xl border border-slate-300 p-2 text-xs font-bold text-slate-800">
                <button type="submit" class="touch-target px-4 py-2 rounded-xl bg-[#1E3A2B] text-white font-bold transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- TOTAL GROSS METRICS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <x-ui.stat-card
                title="Total Omset Kotor"
                value="Rp {{ number_format($report['total_gross_revenue'], 0, ',', '.') }}"
                subtext="Periode {{ $report['period'] }}"
                icon="fas fa-coins"
                color="earth"
            />

            <x-ui.stat-card
                title="Tiket Terjual"
                value="{{ $report['total_tickets_sold'] }} Tiket"
                subtext="SIMAKSI Cikuray"
                icon="fas fa-ticket-alt"
                color="forest"
            />

            <x-ui.stat-card
                title="Pendapatan Toko & Sewa"
                value="Rp {{ number_format($report['store_revenue'] + $report['rental_revenue'], 0, ',', '.') }}"
                subtext="Toko + Konsinyasi + Rental"
                icon="fas fa-store"
                color="sky"
            />

            <x-ui.stat-card
                title="Kas Bersih SAMSARA"
                value="Rp {{ number_format($report['allocation']['samsara_kas'], 0, ',', '.') }}"
                subtext="Setelah alokasi stakeholder"
                icon="fas fa-vault"
                color="emerald"
            />
        </div>

        <!-- REVENUE ALLOCATION BREAKDOWN CARD -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 space-y-6">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-extrabold text-base text-slate-900">Alokasi Pembagian Dana Stakeholder</h3>
                    <p class="text-xs text-slate-500">Transparansi tata kelola pendapatan Basecamp SAMSARA Cigedug.</p>
                </div>
                <button type="button" onclick="window.print()" class="touch-target px-3.5 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-print"></i> Cetak Rekap
                </button>
            </div>

            <div class="space-y-4">
                <!-- 1. KAS SAMSARA -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span class="font-bold text-sm text-slate-900">Kas Organisasi SAMSARA Cigedug</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5">Operasional basecamp, gaji ranger jalur, pemeliharaan shelter, rescue SAR & kebersihan.</p>
                    </div>
                    <div class="text-right">
                        <div class="font-heading font-extrabold text-base text-emerald-800">
                            Rp {{ number_format($report['allocation']['samsara_kas'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- 2. PERHUTANI -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="font-bold text-sm text-slate-900">Retribusi Kawasan Hutan / Perhutani (35%)</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5">PNBP dan kontribusi pelestarian kawasan hutan lindung Gunung Cikuray.</p>
                    </div>
                    <div class="text-right">
                        <div class="font-heading font-extrabold text-base text-slate-900">
                            Rp {{ number_format($report['allocation']['perhutani'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- 3. PAD DESA CIGEDUG -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="font-bold text-sm text-slate-900">Pendapatan Asli Desa (PAD) Cigedug (15%)</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5">Pembangunan fasilitas desa, penerangan jalan umum, dan pembinaan pemuda lokal.</p>
                    </div>
                    <div class="text-right">
                        <div class="font-heading font-extrabold text-base text-slate-900">
                            Rp {{ number_format($report['allocation']['pad_cigedug'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- 4. ASURANSI PENDAKI -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <span class="font-bold text-sm text-slate-900">Premi Asuransi Jiwa & Kecelakaan (Rp 5.000/orang)</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5">Penjaminan evakuasi kecelakaan pendaki dan santunan rawat inap.</p>
                    </div>
                    <div class="text-right">
                        <div class="font-heading font-extrabold text-base text-slate-900">
                            Rp {{ number_format($report['allocation']['asuransi'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- 5. MITRA KONSINYASI UMKM -->
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                            <span class="font-bold text-sm text-slate-900">Hak Bagi Hasil Titip Jual Mitra Warga Cigedug</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5">Pembayaran hasil penjualan produk Mang Asep, Bi Enok, dan Kang Jajang.</p>
                    </div>
                    <div class="text-right">
                        <div class="font-heading font-extrabold text-base text-amber-900">
                            Rp {{ number_format($report['allocation']['mitra_konsinyasi'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- 6. HAK DRIVER OJEK CIGEDUG & KAS PAGUYUBAN -->
                @if (($report['allocation']['driver_ojek'] ?? 0) > 0 || ($report['allocation']['kas_paguyuban_ojek'] ?? 0) > 0)
                <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-600"></span>
                            <span class="font-bold text-sm text-slate-900">Bagi Hasil Paguyuban Ojek Warga Cigedug</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5">
                            Hak Driver: <strong>Rp {{ number_format($report['allocation']['driver_ojek'] ?? 0, 0, ',', '.') }}</strong> &bull;
                            Kas Paguyuban: <strong>Rp {{ number_format($report['allocation']['kas_paguyuban_ojek'] ?? 0, 0, ',', '.') }}</strong>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="font-heading font-extrabold text-base text-sky-900">
                            Rp {{ number_format(($report['allocation']['driver_ojek'] ?? 0) + ($report['allocation']['kas_paguyuban_ojek'] ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
