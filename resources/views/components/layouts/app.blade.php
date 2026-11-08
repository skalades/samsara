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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-slate-800 bg-[#F8FAFC] antialiased selection:bg-[#D97736] selection:text-white pb-20 md:pb-0"
      x-data="{ mobileMenuOpen: false }">

    <!-- TOP NAVIGATION BAR -->
    <header class="sticky top-0 z-40 bg-[#1E3A2B] text-white border-b border-[#2D5841]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <!-- Brand & Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <img src="/LOGO.jpeg" alt="Logo SAMSARA Cigedug" class="h-9 w-9 rounded-xl object-cover ring-1 ring-[#D97736]/60 group-hover:ring-[#D97736] transition duration-200">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-heading font-extrabold text-base tracking-wider text-white group-hover:text-amber-200 transition leading-none">SAMSARA</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#D97736]/90 text-white tracking-widest uppercase leading-none">SIMS</span>
                        </div>
                        <p class="text-[10px] text-white/50 mt-0.5 hidden lg:block leading-none">Gunung Cikuray 2.821 MDPL &bull; Cigedug, Garut</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-0.5 text-sm font-medium">
                    <a href="{{ route('trail.map') }}" class="px-3 py-1.5 rounded-lg transition text-sm {{ request()->routeIs('trail.*') || request()->routeIs('peta.*') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-map-marked-alt mr-1.5 opacity-80"></i>Peta & Trek
                    </a>
                    <a href="{{ route('simaksi.index') }}" class="px-3 py-1.5 rounded-lg transition text-sm {{ request()->routeIs('simaksi.index') || request()->routeIs('simaksi.create') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-ticket-alt mr-1.5 opacity-80"></i>SIMAKSI
                    </a>
                    <a href="{{ route('rental.catalog') }}" class="px-3 py-1.5 rounded-lg transition text-sm {{ request()->routeIs('rental.catalog') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-campground mr-1.5 opacity-80"></i>Sewa Alat
                    </a>
                    <a href="{{ route('store.index') }}" class="px-3 py-1.5 rounded-lg transition text-sm {{ request()->routeIs('store.*') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-store mr-1.5 opacity-80"></i>Toko
                    </a>
                    <a href="{{ route('ojek.public.index') }}" class="px-3 py-1.5 rounded-lg transition text-sm {{ request()->routeIs('ojek.*') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-motorcycle mr-1.5 opacity-80"></i>Ojek
                    </a>
                    <a href="{{ route('simaksi.check') }}" class="px-3 py-1.5 rounded-lg transition text-sm {{ request()->routeIs('simaksi.check') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-qrcode mr-1.5 opacity-80"></i>Cek Tiket
                    </a>
                </nav>

                <!-- Right Side: Trail Status & Auth -->
                <div class="flex items-center gap-2">
                    @php
                        $setting = \Illuminate\Support\Facades\Cache::remember('trail_setting_current', 120, function() {
                            return \App\Modules\Simaksi\Models\TrailSetting::current();
                        });
                    @endphp

                    <!-- Trail Status (desktop only, simplified) -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs text-white/70">
                        <span class="w-1.5 h-1.5 rounded-full {{ $setting->trail_status->value === 'OPEN' ? 'bg-emerald-400 animate-pulse' : ($setting->trail_status->value === 'WARNING' ? 'bg-amber-400' : 'bg-rose-500') }}"></span>
                        <span>Jalur: <strong class="text-white">{{ $setting->trail_status->label() }}</strong></span>
                    </div>

                    @guest
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition border border-white/20">
                            <i class="fas fa-lock text-[11px] text-amber-300/90"></i>
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

                        <a href="{{ $adminRoute }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#D97736] hover:bg-[#B25A20] text-white font-bold text-xs tracking-wide transition">
                            <i class="fas fa-gauge-high text-[11px]"></i>
                            <span>Panel Admin</span>
                        </a>

                        <div class="flex items-center gap-1.5">
                            <div class="text-right hidden lg:block">
                                <span class="text-xs font-bold text-white block leading-tight">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-white/50">{{ auth()->user()->role->label() }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Keluar dari Sistem"
                                    class="p-1.5 sm:px-2.5 sm:py-1.5 rounded-lg bg-white/10 hover:bg-rose-900/60 text-white/70 hover:text-rose-200 text-xs transition flex items-center gap-1.5 border border-white/10">
                                    <i class="fas fa-right-from-bracket"></i>
                                    <span class="hidden sm:inline text-xs">Keluar</span>
                                </button>
                            </form>
                        </div>
                    @endguest

                    <!-- Mobile Menu Toggle -->
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm focus:outline-none transition border border-white/10">
                        <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- MOBILE SLIDE-DOWN DRAWER -->
        <div x-cloak x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-[#2D5841] bg-[#111E17] px-4 pt-3 pb-4">

            <!-- Mobile Trail Status -->
            <div class="flex items-center justify-between text-xs text-white/70 mb-3 py-2 border-b border-white/10">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $setting->trail_status->value === 'OPEN' ? 'bg-emerald-400 animate-pulse' : ($setting->trail_status->value === 'WARNING' ? 'bg-amber-400' : 'bg-rose-500') }}"></span>
                    <span>Status Jalur Cikuray:</span>
                </span>
                <strong class="text-white">{{ $setting->trail_status->label() }}</strong>
            </div>

            <div class="grid grid-cols-2 gap-1.5 text-xs font-semibold">
                <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#1E3A2B] text-white flex items-center gap-2 border border-white/10">
                    <i class="fas fa-home text-amber-400/80 w-4"></i> Beranda
                </a>
                <a href="{{ route('trail.map') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#1E3A2B] text-white flex items-center gap-2 border border-white/10">
                    <i class="fas fa-map-marked-alt text-emerald-400/80 w-4"></i> Peta & Trek
                </a>
                <a href="{{ route('simaksi.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#1E3A2B] text-white flex items-center gap-2 border border-white/10">
                    <i class="fas fa-ticket-alt text-[#D97736] w-4"></i> SIMAKSI Online
                </a>
                <a href="{{ route('rental.catalog') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#1E3A2B] text-white flex items-center gap-2 border border-white/10">
                    <i class="fas fa-campground text-emerald-400/80 w-4"></i> Sewa Alat
                </a>
                <a href="{{ route('store.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#1E3A2B] text-white flex items-center gap-2 border border-white/10">
                    <i class="fas fa-store text-amber-400/80 w-4"></i> Toko Basecamp
                </a>
                <a href="{{ route('ojek.public.index') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#1E3A2B] text-white flex items-center gap-2 border border-white/10">
                    <i class="fas fa-motorcycle text-amber-400/80 w-4"></i> Ojek Cigedug
                </a>
                <a href="{{ route('simaksi.check') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#1E3A2B] text-white flex items-center gap-2 border border-white/10">
                    <i class="fas fa-qrcode text-sky-400/80 w-4"></i> Cek E-Ticket
                </a>
                @guest
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-[#D97736] text-white flex items-center gap-2">
                        <i class="fas fa-lock w-4"></i> Login Staf
                    </a>
                @endguest
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-3 space-y-2.5">
        @if (session('success'))
            <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
        @endif
        @if (session('error'))
            <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
        @endif
        @if (session('warning'))
            <x-ui.alert type="warning">{{ session('warning') }}</x-ui.alert>
        @endif
        @if (session('info'))
            <x-ui.alert type="info">{{ session('info') }}</x-ui.alert>
        @endif
    </div>

    <!-- MAIN BODY CONTENT -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <x-public.footer />

    <!-- MOBILE BOTTOM NAVIGATION BAR -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#111E17] border-t border-white/10 safe-area-inset-bottom">
        <div class="grid grid-cols-6 h-16">
            @guest
                <!-- GUEST MOBILE BOTTOM NAV -->
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('home') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                    <i class="fas fa-mountain text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Home</span>
                </a>

                <a href="{{ route('simaksi.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('simaksi.*') && !request()->routeIs('simaksi.check') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                    <i class="fas fa-ticket-alt text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">SIMAKSI</span>
                </a>

                <a href="{{ route('rental.catalog') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('rental.catalog') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                    <i class="fas fa-campground text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Sewa</span>
                </a>

                <a href="{{ route('store.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('store.*') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                    <i class="fas fa-store text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Toko</span>
                </a>

                <a href="{{ route('ojek.public.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('ojek.*') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                    <i class="fas fa-motorcycle text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Ojek</span>
                </a>

                <a href="{{ route('simaksi.check') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('simaksi.check') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                    <i class="fas fa-qrcode text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Cek Tiket</span>
                </a>
            @else
                <!-- AUTHENTICATED STAFF MOBILE BOTTOM NAV -->
                @php $role = auth()->user()->role; @endphp

                <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center text-center text-white/50 hover:text-white transition active:scale-95">
                    <i class="fas fa-mountain text-lg"></i>
                    <span class="text-[10px] mt-1 font-semibold">Portal</span>
                </a>

                @if ($role->value === 'SUPERADMIN' || $role->value === 'ADMIN_SAR')
                    <a href="{{ route('admin.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('admin.*') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                        <i class="fas fa-gauge-high text-lg"></i>
                        <span class="text-[10px] mt-1 font-semibold">Admin</span>
                    </a>
                    <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('pos.*') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                        <div class="w-10 h-10 rounded-full bg-[#D97736] text-white flex items-center justify-center shadow-lg -mt-4 border-2 border-[#111E17]">
                            <i class="fas fa-cash-register text-sm"></i>
                        </div>
                        <span class="text-[10px] mt-0.5 font-semibold">Kasir</span>
                    </a>
                    <a href="{{ route('fieldops.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('fieldops.*') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                        <i class="fas fa-qrcode text-lg"></i>
                        <span class="text-[10px] mt-1 font-semibold">Pos 1-4</span>
                    </a>
                @elseif ($role->value === 'KASIR')
                    <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('pos.index') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                        <div class="w-10 h-10 rounded-full bg-[#D97736] text-white flex items-center justify-center shadow-lg -mt-4 border-2 border-[#111E17]">
                            <i class="fas fa-cash-register text-sm"></i>
                        </div>
                        <span class="text-[10px] mt-0.5 font-semibold">Kasir POS</span>
                    </a>
                    <a href="{{ route('pos.shift') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('pos.shift') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                        <i class="fas fa-vault text-lg"></i>
                        <span class="text-[10px] mt-1 font-semibold">Shift</span>
                    </a>
                    <a href="{{ route('rental.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('rental.index') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                        <i class="fas fa-screwdriver-wrench text-lg"></i>
                        <span class="text-[10px] mt-1 font-semibold">Rental QC</span>
                    </a>
                @elseif ($role->value === 'PETUGAS_POS')
                    <a href="{{ route('fieldops.index') }}" class="inline-flex flex-col items-center justify-center text-center transition active:scale-95 {{ request()->routeIs('fieldops.*') ? 'text-amber-400' : 'text-white/50 hover:text-white' }}">
                        <div class="w-10 h-10 rounded-full bg-[#D97736] text-white flex items-center justify-center shadow-lg -mt-4 border-2 border-[#111E17]">
                            <i class="fas fa-qrcode text-sm"></i>
                        </div>
                        <span class="text-[10px] mt-0.5 font-semibold">Scanner</span>
                    </a>
                    <div class="col-span-2"></div>
                @endif

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="inline-flex flex-col items-center justify-center">
                    @csrf
                    <button type="submit" class="inline-flex flex-col items-center justify-center text-white/40 hover:text-rose-300 transition active:scale-95">
                        <i class="fas fa-right-from-bracket text-lg"></i>
                        <span class="text-[10px] mt-1 font-semibold">Logout</span>
                    </button>
                </form>
            @endguest
        </div>
    </nav>

    <!-- GLOBAL TOAST NOTIFICATION CONTAINER -->
    <x-ui.toast />

    @stack('scripts')
</body>
</html>
