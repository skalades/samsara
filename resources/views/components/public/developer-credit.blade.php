@props([
    'variant' => 'card', // 'card' (default featured box) or 'compact'
])

@php
    $mapsUrl = 'https://www.google.com/maps/place/NADIR+%7C+JASA+PENGUKURAN+LAHAN,+TOPOGRAFI,+PEMETAAN,+SEWA+ALAT+UKUR,+SEWA+DRONE,+PEMBUATAN+WEBSITE+%7C+NADIR/@-7.1668405,107.9054155,1403m/data=!3m2!1e3!4b1!4m6!3m5!1s0x2e68b1bcd5258c47:0x3c5a9d384d5b572f!8m2!3d-7.1668458!4d107.9079904!16s%2Fg%2F11z8rp2yc2!5m1!1e1?entry=ttu&g_ep=EgoyMDI2MTEwMy4wIKXMDSoASAFQAw%3D%3D';

    $services = [
        [
            'icon' => 'fa-ruler-combined',
            'title' => 'Pengukuran Lahan & Topografi',
            'desc' => 'Survei terestris batas kavling, kontur elevasi tanah, perhitungan volume cut & fill proyek.',
        ],
        [
            'icon' => 'fa-map-location-dot',
            'title' => 'Pemetaan Spasial & SIG',
            'desc' => 'Pengolahan peta digital, ortofoto, zonasi kawasan, dan integrasi WebGIS sistem informasi.',
        ],
        [
            'icon' => 'fa-satellite-dish',
            'title' => 'Sewa Alat Ukur Presisi',
            'desc' => 'Rental Total Station, Theodolite digital, Automatic Level (Waterpass), & GPS Geodetik GNSS RTK.',
        ],
        [
            'icon' => 'fa-crosshairs',
            'title' => 'Sewa Drone & Aerial Mapping',
            'desc' => 'Fotogrametri udara UAV resolusi tinggi, foto udara 3D, serta inspeksi area medan luas.',
        ],
        [
            'icon' => 'fa-laptop-code',
            'title' => 'Pembuatan Website & Sistem',
            'desc' => 'Pengembangan web profesional, portal reservasi pendakian, e-commerce, & aplikasi kustom.',
        ],
    ];
@endphp

@if ($variant === 'compact')
    <!-- Compact developer bar for smaller contexts -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-xl bg-[#12241B] border border-[#2D5841] text-xs">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#D97736] text-white flex items-center justify-center font-bold font-heading text-sm shadow-xs">
                N
            </div>
            <div>
                <div class="text-white font-semibold">Dibuat oleh <strong class="text-amber-400">NADIR</strong></div>
                <div class="text-[11px] text-white/50">Jasa Pengukuran Lahan, Pemetaan, Sewa Drone & Pembuatan Website</div>
            </div>
        </div>
        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600/30 hover:bg-emerald-600/50 text-emerald-300 border border-emerald-500/40 text-xs font-semibold transition">
            <i class="fas fa-map-location-dot"></i>
            <span>Google Maps</span>
            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
        </a>
    </div>
@else
    <!-- Full Featured Developer Segment Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#12241B] via-[#0E1A14] to-[#162B20] border border-[#2D5841] p-5 sm:p-6 lg:p-7 shadow-xl">
        <!-- Subtle Ambient Background Glows -->
        <div class="pointer-events-none absolute -top-12 -right-12 w-56 h-56 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 w-56 h-56 rounded-full bg-[#D97736]/10 blur-3xl"></div>

        <div class="relative z-10 space-y-6">
            <!-- Top Row: Badge, Title & Direct CTA to Google Maps -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-[#D97736]/20 text-[#D97736] border border-[#D97736]/30">
                            <i class="fas fa-code"></i> Developer & Geospasial Partner
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400/90 font-medium">
                            <i class="fas fa-location-dot text-emerald-400"></i> Tarogong Kaler, Garut &bull; Jawa Barat
                        </span>
                    </div>

                    <div class="flex items-center gap-3 pt-0.5">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-[#D97736] to-[#B25A20] text-white flex items-center justify-center font-heading font-black text-xl shadow-md ring-1 ring-amber-400/40 shrink-0">
                            N
                        </div>
                        <div>
                            <h3 class="font-heading font-extrabold text-lg sm:text-xl text-white tracking-wide flex items-center gap-2">
                                Aplikasi Ini Dibuat Oleh <span class="text-amber-400 underline decoration-amber-400/40 decoration-2 underline-offset-4">NADIR</span>
                            </h3>
                            <p class="text-xs text-white/60 font-medium">
                                Jasa Pengukuran Lahan, Topografi, Pemetaan, Sewa Alat Ukur, Sewa Drone & Pembuatan Website
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Button Link to Google Maps -->
                <div class="flex flex-col sm:flex-row lg:flex-col items-start lg:items-end gap-2 shrink-0">
                    <a href="{{ $mapsUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="touch-target inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#D97736] to-[#B25A20] hover:from-[#B25A20] hover:to-[#924214] text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-amber-900/40 border border-amber-400/30 transition-all duration-200 active:scale-95 group">
                        <i class="fas fa-map-location-dot text-amber-200 group-hover:scale-110 transition duration-200"></i>
                        <span>Buka Profil NADIR di Google Maps</span>
                        <i class="fas fa-arrow-up-right-from-square text-[11px] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition duration-200"></i>
                    </a>
                    <span class="text-[11px] text-white/40 font-mono">
                        Koordinat: -7.1668° S, 107.9080° E
                    </span>
                </div>
            </div>

            <!-- About NADIR Summary -->
            <p class="text-xs sm:text-sm text-white/70 leading-relaxed max-w-4xl">
                NADIR merupakan penyedia layanan profesional terintegrasi yang berfokus pada teknologi geodesi, survey pemetaan topografi lahan, penyewaan instrumen pengukuran modern, armada drone fotogrametri udara, serta perancangan aplikasi digital dan pembuatan sistem website seperti <strong>Sistem Informasi Manajemen SIMS SAMSARA</strong> Gunung Cikuray.
            </p>

            <!-- 5 Core Services Grid -->
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-amber-400/90 mb-3 flex items-center gap-1.5">
                    <i class="fas fa-layer-group"></i> Ruang Lingkup Layanan & Keahlian NADIR
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                    @foreach ($services as $service)
                        <div class="p-3.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-emerald-500/50 transition duration-200 flex flex-col justify-between group">
                            <div>
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xs mb-2.5 group-hover:scale-110 transition duration-200">
                                    <i class="fas {{ $service['icon'] }}"></i>
                                </div>
                                <h5 class="font-heading font-bold text-xs text-white group-hover:text-amber-300 transition">
                                    {{ $service['title'] }}
                                </h5>
                                <p class="text-[11px] text-white/50 mt-1.5 leading-relaxed">
                                    {{ $service['desc'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Meta Chips / Quick Tags -->
            <div class="pt-3 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-white/50 font-medium">
                    <span class="text-amber-400/80 font-bold mr-1">Spesialisasi:</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10">#PengukuranLahan</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10">#Topografi</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10">#PemetaanGIS</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10">#SewaTotalStation</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10">#SewaDroneMapping</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10">#PembuatanWebsite</span>
                </div>
                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-emerald-400 hover:text-emerald-300 font-semibold inline-flex items-center gap-1 transition">
                    <span>Lihat Ulasan & Petunjuk Arah</span>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                </a>
            </div>
        </div>
    </div>
@endif
