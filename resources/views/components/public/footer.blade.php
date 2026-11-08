@php
    $nadirMapsUrl = 'https://www.google.com/maps/place/NADIR+%7C+JASA+PENGUKURAN+LAHAN,+TOPOGRAFI,+PEMETAAN,+SEWA+ALAT+UKUR,+SEWA+DRONE,+PEMBUATAN+WEBSITE+%7C+NADIR/@-7.1668405,107.9054155,1403m/data=!3m2!1e3!4b1!4m6!3m5!1s0x2e68b1bcd5258c47:0x3c5a9d384d5b572f!8m2!3d-7.1668458!4d107.9079904!16s%2Fg%2F11z8rp2yc2!5m1!1e1?entry=ttu&g_ep=EgoyMDI2MTEwMy4wIKXMDSoASAFQAw%3D%3D';
@endphp

<footer class="mt-14 bg-[#111E17] text-white border-t border-[#2D5841]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-24 md:pb-12 space-y-10">
        <!-- 1. SAMSARA BASECAMP INFO & LINKS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-white/10">
            <!-- Col 1: Brand & Profil Basecamp -->
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="/LOGO.jpeg" alt="Logo SAMSARA" class="w-12 h-12 rounded-xl object-cover ring-1 ring-[#D97736]/60 shadow-md">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-extrabold text-lg tracking-wider text-white">SAMSARA</span>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-[#D97736] text-white tracking-widest uppercase shadow-xs">SIMS v1.4</span>
                        </div>
                        <p class="text-xs text-white/50 font-medium mt-0.5">Satuan Anak Muda Sadar Alam &bull; Basecamp Cigedug</p>
                    </div>
                </div>
                <p class="text-xs text-white/60 leading-relaxed max-w-md">
                    Pintu resmi pendakian Gunung Cikuray (2.821 MDPL) via Cigedug, Garut. Mengelola reservasi SIMAKSI online, persewaan perlengkapan gunung, logistik UMKM lokal, dan sistem tanggap darurat SAR terintegrasi.
                </p>
                <div class="flex items-center gap-2 pt-1">
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="touch-target w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 text-white/70 hover:text-white flex items-center justify-center text-sm transition" title="Instagram SAMSARA">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/6281223456789" target="_blank" rel="noopener noreferrer" class="touch-target w-9 h-9 rounded-lg bg-emerald-700/60 hover:bg-emerald-600 text-white flex items-center justify-center text-sm transition shadow-xs" title="WhatsApp Basecamp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="touch-target w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 text-white/70 hover:text-white flex items-center justify-center text-sm transition" title="YouTube Dokumentasi">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Col 2: Layanan Pendaki -->
            <div>
                <h4 class="font-heading font-semibold text-xs text-amber-400/90 uppercase tracking-wider mb-3.5 flex items-center gap-1.5">
                    <i class="fas fa-compass text-amber-400"></i> Layanan Pendaki
                </h4>
                <ul class="space-y-2.5 text-xs text-white/60">
                    <li>
                        <a href="{{ route('trail.map') }}" class="hover:text-amber-300 transition flex items-center gap-1.5">
                            <i class="fas fa-chevron-right text-[9px] text-emerald-400"></i> Peta Satelit & Rute GPS
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('simaksi.index') }}" class="hover:text-amber-300 transition flex items-center gap-1.5">
                            <i class="fas fa-chevron-right text-[9px] text-emerald-400"></i> Booking Kuota SIMAKSI
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rental.catalog') }}" class="hover:text-amber-300 transition flex items-center gap-1.5">
                            <i class="fas fa-chevron-right text-[9px] text-emerald-400"></i> Katalog Sewa Tenda & Alat
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('store.index') }}" class="hover:text-amber-300 transition flex items-center gap-1.5">
                            <i class="fas fa-chevron-right text-[9px] text-emerald-400"></i> Pre-Order Logistik UMKM
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('simaksi.check') }}" class="hover:text-amber-300 transition flex items-center gap-1.5">
                            <i class="fas fa-chevron-right text-[9px] text-emerald-400"></i> Cek E-Ticket & Posisi
                        </a>
                    </li>
                    <li>
                        <a href="/Trek%201.kml" download class="hover:text-amber-300 transition flex items-center gap-1.5">
                            <i class="fas fa-download text-[9px] text-emerald-400"></i> Unduh File GPS KML
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 3: Pos & Lokasi -->
            <div>
                <h4 class="font-heading font-semibold text-xs text-amber-400/90 uppercase tracking-wider mb-3.5 flex items-center gap-1.5">
                    <i class="fas fa-map-pin text-amber-400"></i> Pos & Lokasi Basecamp
                </h4>
                <p class="text-xs text-white/60 leading-relaxed">
                    Desa Cigedug, Kec. Cigedug<br>
                    Kab. Garut, Jawa Barat 44116<br>
                    <span class="text-emerald-400 font-mono font-medium block mt-1.5">-7.3194° S, 107.8631° E</span>
                </p>
                <div class="mt-4 pt-3.5 border-t border-white/10 text-[11px] text-white/50">
                    <span class="text-white/40 block text-[10px] uppercase tracking-wider font-semibold">Mitra Operasional Resmi:</span>
                    <div class="mt-1 text-white/70 font-medium">Perhutani KPH Garut &bull; LMDH Giri Wargi</div>
                </div>
            </div>
        </div>

        <!-- 2. SPECIAL SEGMENT: APLIKASI INI DIBUAT OLEH NADIR -->
        <x-public.developer-credit variant="card" />

        <!-- 3. BOTTOM COPYRIGHT & ACCESS ROW -->
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/50">
            <div class="text-center sm:text-left">
                &copy; {{ date('Y') }} <strong class="text-white/80">Basecamp SAMSARA Cigedug</strong>. Seluruh Hak Cipta Dilindungi.
                <span class="hidden md:inline mx-1.5 text-white/30">&bull;</span>
                <span class="block md:inline mt-1 md:mt-0 text-[11px] text-white/60">
                    Aplikasi ini dirancang & dikembangkan oleh
                    <a href="{{ $nadirMapsUrl }}" target="_blank" rel="noopener noreferrer" class="text-amber-400 hover:text-amber-300 font-bold hover:underline inline-flex items-center gap-1.5">
                        <img src="{{ asset('assets/logo-nadir.png') }}" alt="NADIR Logo" class="w-4 h-4 object-contain inline-block">
                        <span>NADIR</span>
                        <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>
                </span>
            </div>
            <div class="flex items-center gap-5">
                <span class="text-emerald-400 font-medium inline-flex items-center gap-1.5">
                    <i class="fas fa-leaf text-emerald-500"></i> Zero-Waste Hiking
                </span>
                @guest
                    <a href="{{ route('login') }}" class="text-white/50 hover:text-amber-300 transition inline-flex items-center gap-1.5">
                        <i class="fas fa-lock text-[10px]"></i> Login Staf
                    </a>
                @endguest
            </div>
        </div>
    </div>
</footer>
