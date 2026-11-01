<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1E3A2B">
    <meta name="description" content="Portal Resmi Basecamp SAMSARA - Jalur Pendakian Gunung Cikuray 2.821 MDPL via Cigedug, Garut. Booking SIMAKSI online, cek kuota harian, sewa alat outdoor, dan monitoring keselamatan jalur.">
    <meta property="og:title" content="{{ $title ?? 'Portal Resmi Gunung Cikuray 2.821 MDPL' }} | Basecamp SAMSARA">
    <meta property="og:description" content="Booking SIMAKSI online, cek kuota harian, sewa alat camp, dan panduan jalur resmi via Cigedug Garut.">
    <meta property="og:image" content="/assets/hero-cikuray.jpg">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <title>{{ $title ?? 'SAMSARA SIMS' }} | Basecamp Gunung Cikuray 2.821 MDPL</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/LOGO.jpeg">

    <!-- Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-slate-800 bg-[#F8FAFC] antialiased selection:bg-[#D97736] selection:text-white pb-20 md:pb-0">

    <!-- TOP NAVIGATION BAR -->
    <header class="sticky top-0 z-40 bg-[#1E3A2B]/95 backdrop-blur-md text-white shadow-md border-b border-[#2D5841]/80 transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18">
                <!-- Brand & Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="relative">
                        <img src="/LOGO.jpeg" alt="Logo SAMSARA Cigedug" class="h-10 w-10 sm:h-11 sm:w-11 rounded-2xl object-cover ring-2 ring-[#D97736]/70 shadow-md group-hover:scale-105 transition duration-200">
                        <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-[#1E3A2B]" title="Sistem Aktif"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-extrabold text-lg sm:text-xl tracking-wider text-white group-hover:text-amber-200 transition">SAMSARA</span>
                            <span class="text-[9px] sm:text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-gradient-to-r from-[#D97736] to-amber-500 text-white tracking-widest uppercase shadow-xs">SIMS</span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-emerald-200/80 -mt-0.5 hidden sm:block">Gunung Cikuray 2.821 MDPL via Cigedug &bull; Garut</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Public Portal) -->
                <nav class="hidden md:flex items-center gap-1.5 font-medium text-sm">
                    <a href="{{ route('trail.map') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('trail.*') || request()->routeIs('peta.*') ? 'bg-[#2D5841] text-white font-semibold shadow-xs' : 'text-emerald-100/90 hover:bg-[#2D5841]/60 hover:text-white' }}">
                        <i class="fas fa-map-marked-alt mr-1.5 text-emerald-400"></i> Peta & Trek
                    </a>
                    <a href="{{ route('simaksi.index') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('simaksi.index') || request()->routeIs('simaksi.create') ? 'bg-[#2D5841] text-white font-semibold shadow-xs' : 'text-emerald-100/90 hover:bg-[#2D5841]/60 hover:text-white' }}">
                        <i class="fas fa-ticket-alt mr-1.5 text-[#D97736]"></i> SIMAKSI
                    </a>
                    <a href="{{ route('rental.catalog') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('rental.catalog') ? 'bg-[#2D5841] text-white font-semibold shadow-xs' : 'text-emerald-100/90 hover:bg-[#2D5841]/60 hover:text-white' }}">
                        <i class="fas fa-campground mr-1.5 text-emerald-400"></i> Sewa Alat
                    </a>
                    <a href="{{ route('store.index') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('store.*') ? 'bg-[#2D5841] text-white font-semibold shadow-xs' : 'text-emerald-100/90 hover:bg-[#2D5841]/60 hover:text-white' }}">
                        <i class="fas fa-store mr-1.5 text-amber-400"></i> Pre-Order & Toko
                    </a>
                    <a href="{{ route('ojek.public.index') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('ojek.*') ? 'bg-[#2D5841] text-white font-semibold shadow-xs' : 'text-emerald-100/90 hover:bg-[#2D5841]/60 hover:text-white' }}">
                        <i class="fas fa-motorcycle mr-1.5 text-amber-400"></i> Ojek
                    </a>
                    <a href="{{ route('simaksi.check') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('simaksi.check') ? 'bg-[#2D5841] text-white font-semibold shadow-xs' : 'text-emerald-100/90 hover:bg-[#2D5841]/60 hover:text-white' }}">
                        <i class="fas fa-qrcode mr-1.5 text-sky-400"></i> Cek Tiket
                    </a>
                </nav>

                <!-- Right Side: Trail Badge & Auth Action -->
                <div class="flex items-center gap-2.5">
                    @php
                        $setting = \Illuminate\Support\Facades\Cache::remember('trail_setting_current', 120, function() {
                            return \App\Modules\Simaksi\Models\TrailSetting::current();
                        });
                    @endphp
                    <div class="hidden sm:inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-500/40 bg-emerald-950/60 text-emerald-200 backdrop-blur-xs shadow-xs">
                        <span class="w-2 h-2 rounded-full {{ $setting->trail_status->value === 'OPEN' ? 'bg-emerald-400 animate-pulse' : ($setting->trail_status->value === 'WARNING' ? 'bg-amber-400' : 'bg-rose-500') }}"></span>
                        <span>Jalur: <strong class="text-white font-bold">{{ $setting->trail_status->label() }}</strong></span>
                    </div>

                    @guest
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs tracking-wide transition border border-white/20 shadow-xs">
                            <i class="fas fa-lock text-xs text-amber-300"></i>
                            <span class="hidden sm:inline">Login</span> Staf
                        </a>
                    @else
                        @php
                            $role = auth()->user()->role?->value;
                            $adminRoute = match($role) {
                                'KASIR' => route('pos.index'),
                                'PETUGAS_POS' => route('fieldops.index'),
                                default => route('admin.index'),
                            };
                        @endphp

                        <!-- Dedicated Admin Panel Shortcut Button -->
                        <a href="{{ $adminRoute }}"
                           class="inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl bg-gradient-to-r from-[#D97736] to-amber-500 hover:from-[#c26528] hover:to-amber-600 text-white font-extrabold text-xs tracking-wide transition shadow-md group">
                            <i class="fas fa-gauge-high text-xs group-hover:rotate-12 transition"></i>
                            <span>Panel Admin</span>
                        </a>

                        <!-- Staff Profile & Logout -->
                        <div class="flex items-center gap-2">
                            <div class="text-right hidden lg:block">
                                <span class="text-xs font-bold text-white block leading-tight">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-emerald-300">{{ auth()->user()->role->label() }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Keluar dari Sistem"
                                    class="p-2 sm:px-3 sm:py-2 rounded-xl bg-rose-900/60 hover:bg-rose-800 text-rose-200 hover:text-white text-xs font-bold transition flex items-center gap-1.5 border border-rose-700/50">
                                    <i class="fas fa-right-from-bracket"></i>
                                    <span class="hidden sm:inline">Keluar</span>
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-start justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-start justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-rose-600 text-lg"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-rose-600 hover:text-rose-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if (session('warning'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-800 flex items-start justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-amber-600 text-lg"></i>
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                </div>
                <button @click="show = false" class="text-amber-600 hover:text-amber-800"><i class="fas fa-times"></i></button>
            </div>
        @endif
    </div>

    <!-- MAIN BODY CONTENT -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- PREMIUM OUTDOOR FOOTER -->
    <footer class="mt-12 bg-gradient-to-b from-[#1E3A2B] via-[#12241B] to-[#0A140F] text-white border-t border-[#2D5841]/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-emerald-900/60">
                <!-- Col 1: Brand & Identity -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="/LOGO.jpeg" alt="Logo SAMSARA" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-[#D97736] shadow-lg">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-heading font-extrabold text-xl tracking-wider text-white">SAMSARA</span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-[#D97736] text-white tracking-widest uppercase">SIMS v1.4.0</span>
                            </div>
                            <p class="text-xs text-emerald-200/90 font-medium">Satuan Anak Muda Sadar Alam &bull; Basecamp Cigedug</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-300/80 leading-relaxed max-w-md">
                        Pintu resmi pendakian Gunung Cikuray (2.821 MDPL) via Cigedug, Garut. Mengelola reservasi SIMAKSI online, persewaan perlengkapan gunung terinspeksi, dan sistem tanggap darurat SAR terpadu.
                    </p>
                    <div class="flex items-center gap-3 pt-1">
                        <a href="https://instagram.com" target="_blank" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-emerald-200 hover:text-white flex items-center justify-center text-sm transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/6281223456789" target="_blank" class="w-8 h-8 rounded-xl bg-emerald-600/50 hover:bg-emerald-600 text-white flex items-center justify-center text-sm transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://youtube.com" target="_blank" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-emerald-200 hover:text-white flex items-center justify-center text-sm transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Layanan Mandiri Pendaki -->
                <div>
                    <h4 class="font-heading font-bold text-sm text-amber-300 uppercase tracking-wider mb-3">Layanan Pendaki</h4>
                    <ul class="space-y-2 text-xs text-slate-300">
                        <li><a href="{{ route('trail.map') }}" class="hover:text-amber-300 transition flex items-center gap-1.5"><i class="fas fa-chevron-right text-[9px] text-[#D97736]"></i> Peta Satelit & Rute GPS</a></li>
                        <li><a href="{{ route('simaksi.index') }}" class="hover:text-amber-300 transition flex items-center gap-1.5"><i class="fas fa-chevron-right text-[9px] text-[#D97736]"></i> Booking Kuota SIMAKSI</a></li>
                        <li><a href="{{ route('rental.catalog') }}" class="hover:text-amber-300 transition flex items-center gap-1.5"><i class="fas fa-chevron-right text-[9px] text-[#D97736]"></i> Katalog Sewa Tenda & Alat</a></li>
                        <li><a href="{{ route('store.index') }}" class="hover:text-amber-300 transition flex items-center gap-1.5"><i class="fas fa-chevron-right text-[9px] text-[#D97736]"></i> Pre-Order Logistik UMKM</a></li>
                        <li><a href="{{ route('simaksi.check') }}" class="hover:text-amber-300 transition flex items-center gap-1.5"><i class="fas fa-chevron-right text-[9px] text-[#D97736]"></i> Cek E-Ticket & Posisi</a></li>
                        <li><a href="/Trek%201.kml" download class="hover:text-amber-300 transition flex items-center gap-1.5"><i class="fas fa-download text-[9px] text-emerald-400"></i> Unduh File GPS KML</a></li>
                    </ul>
                </div>

                <!-- Col 3: Informasi Lapangan & Mitra -->
                <div>
                    <h4 class="font-heading font-bold text-sm text-amber-300 uppercase tracking-wider mb-3">Pos & Lokasi</h4>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Desa Cigedug, Kec. Cigedug<br>
                        Kab. Garut, Jawa Barat 44116<br>
                        <span class="text-emerald-300 font-semibold block mt-1">Koordinat: -7.3194° S, 107.8631° E</span>
                    </p>
                    <div class="mt-4 pt-3 border-t border-emerald-900/60 text-[11px] text-slate-400">
                        <span>Bekerjasama resmi dengan:</span>
                        <div class="mt-1 font-semibold text-emerald-200">Perhutani KPH Garut &bull; LMDH Giri Wargi</div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Staff Link -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                <div>
                    &copy; {{ date('Y') }} <strong>Basecamp SAMSARA Cigedug</strong>. Seluruh Hak Cipta Dilindungi.
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-emerald-400 font-medium"><i class="fas fa-leaf mr-1"></i> Zero-Waste Hiking Certified</span>
                    @guest
                        <a href="{{ route('login') }}" class="text-slate-400 hover:text-amber-300 transition flex items-center gap-1">
                            <i class="fas fa-lock text-[10px]"></i> Login Staf Basecamp
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </footer>

    <!-- MOBILE-FIRST BOTTOM NAVIGATION BAR (Thumb-Zone Friendly) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#1E3A2B]/95 backdrop-blur-lg border-t border-[#2D5841] shadow-2xl safe-area-inset-bottom">
        <div class="grid grid-cols-6 h-16">
            @guest
                <!-- GUEST MOBILE BOTTOM NAV (All 5 core hiker services + Home) -->
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('home') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                    <i class="fas fa-mountain text-base {{ request()->routeIs('home') ? 'text-amber-400' : '' }}"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('simaksi.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('simaksi.*') && !request()->routeIs('simaksi.check') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                    <i class="fas fa-ticket-alt text-base {{ request()->routeIs('simaksi.*') && !request()->routeIs('simaksi.check') ? 'text-amber-400' : '' }}"></i>
                    <span class="text-[9px] mt-0.5 font-medium">SIMAKSI</span>
                </a>

                <a href="{{ route('rental.catalog') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('rental.catalog') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                    <i class="fas fa-campground text-base {{ request()->routeIs('rental.catalog') ? 'text-amber-400' : '' }}"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Sewa</span>
                </a>

                <a href="{{ route('store.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('store.*') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                    <i class="fas fa-store text-base {{ request()->routeIs('store.*') ? 'text-amber-400' : '' }}"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Toko</span>
                </a>

                <a href="{{ route('ojek.public.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('ojek.*') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                    <i class="fas fa-motorcycle text-base {{ request()->routeIs('ojek.*') ? 'text-amber-400' : '' }}"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Ojek</span>
                </a>

                <a href="{{ route('simaksi.check') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('simaksi.check') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                    <i class="fas fa-qrcode text-base {{ request()->routeIs('simaksi.check') ? 'text-amber-400' : '' }}"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Cek Tiket</span>
                </a>
            @else
                <!-- AUTHENTICATED STAFF MOBILE BOTTOM NAV -->
                @php $role = auth()->user()->role; @endphp

                <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center text-center text-emerald-200/80 hover:text-white">
                    <i class="fas fa-mountain text-lg"></i>
                    <span class="text-[10px] mt-1 font-medium">Portal</span>
                </a>

                @if ($role->value === 'SUPERADMIN' || $role->value === 'ADMIN_SAR')
                    <a href="{{ route('admin.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('admin.*') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                        <i class="fas fa-gauge-high text-lg {{ request()->routeIs('admin.*') ? 'text-amber-400' : '' }}"></i>
                        <span class="text-[10px] mt-1 font-medium">Admin</span>
                    </a>
                    <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('pos.*') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                        <div class="w-10 h-10 rounded-full bg-[#D97736] text-white flex items-center justify-center shadow-lg -mt-4 border-2 border-white">
                            <i class="fas fa-cash-register text-sm"></i>
                        </div>
                        <span class="text-[10px] mt-0.5 font-medium">Kasir</span>
                    </a>
                    <a href="{{ route('fieldops.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('fieldops.*') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                        <i class="fas fa-qrcode text-lg {{ request()->routeIs('fieldops.*') ? 'text-amber-400' : '' }}"></i>
                        <span class="text-[10px] mt-1 font-medium">Pos 1-4</span>
                    </a>
                @elseif ($role->value === 'KASIR')
                    <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('pos.index') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                        <div class="w-10 h-10 rounded-full bg-[#D97736] text-white flex items-center justify-center shadow-lg -mt-4 border-2 border-white">
                            <i class="fas fa-cash-register text-sm"></i>
                        </div>
                        <span class="text-[10px] mt-0.5 font-medium">Kasir POS</span>
                    </a>
                    <a href="{{ route('pos.shift') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('pos.shift') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                        <i class="fas fa-vault text-lg"></i>
                        <span class="text-[10px] mt-1 font-medium">Shift</span>
                    </a>
                    <a href="{{ route('rental.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('rental.index') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                        <i class="fas fa-screwdriver-wrench text-lg"></i>
                        <span class="text-[10px] mt-1 font-medium">Rental QC</span>
                    </a>
                @elseif ($role->value === 'PETUGAS_POS')
                    <a href="{{ route('fieldops.index') }}" class="inline-flex flex-col items-center justify-center text-center {{ request()->routeIs('fieldops.*') ? 'text-amber-300 font-bold' : 'text-emerald-200/80 hover:text-white' }}">
                        <div class="w-10 h-10 rounded-full bg-[#D97736] text-white flex items-center justify-center shadow-lg -mt-4 border-2 border-white">
                            <i class="fas fa-qrcode text-sm"></i>
                        </div>
                        <span class="text-[10px] mt-0.5 font-medium">Scanner</span>
                    </a>
                    <div class="col-span-2"></div>
                @endif

                <!-- Logout Trigger -->
                <form action="{{ route('logout') }}" method="POST" class="inline-flex flex-col items-center justify-center">
                    @csrf
                    <button type="submit" class="inline-flex flex-col items-center justify-center text-rose-300 hover:text-rose-100">
                        <i class="fas fa-right-from-bracket text-lg"></i>
                        <span class="text-[10px] mt-1 font-medium">Logout</span>
                    </button>
                </form>
            @endguest
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
