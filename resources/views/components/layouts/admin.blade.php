@props(['title' => null])
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#12241B">
    <title>{{ $title ?? 'Panel Admin' }} | Basecamp SAMSARA Cikuray</title>

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
<body class="min-h-full font-sans text-slate-800 bg-[#F8FAFC] antialiased selection:bg-[#D97736] selection:text-white pb-20 md:pb-0"
      x-data="{ mobileSidebarOpen: false }">

    <!-- MODULAR SIDEBAR (Fixed Desktop & Slide-Over Mobile) -->
    <x-admin.sidebar />

    <!-- CONTENT WRAPPER (Shifted on Desktop to Accommodate Sidebar) -->
    <div class="md:pl-64 lg:pl-72 flex flex-col min-h-screen">
        <!-- TOPBAR HEADER -->
        <x-admin.header :title="$title" />

        <!-- FLASH NOTIFICATIONS (Modular Alert Component) -->
        <div class="px-4 sm:px-6 lg:px-8 w-full mt-4 space-y-3">
            @if (session('success'))
                <x-ui.alert type="success">
                    {{ session('success') }}
                </x-ui.alert>
            @endif

            @if (session('error'))
                <x-ui.alert type="error">
                    {{ session('error') }}
                </x-ui.alert>
            @endif

            @if (session('warning'))
                <x-ui.alert type="warning">
                    {{ session('warning') }}
                </x-ui.alert>
            @endif

            @if (session('info'))
                <x-ui.alert type="info">
                    {{ session('info') }}
                </x-ui.alert>
            @endif
        </div>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6">
            {{ $slot }}
        </main>

        <!-- MINIMAL ADMIN FOOTER -->
        <footer class="px-4 sm:px-6 lg:px-8 py-4 border-t border-slate-200 bg-white text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
            <div>
                &copy; {{ date('Y') }} <strong>Basecamp SAMSARA</strong> &bull; SIMS v1.4.0
                <span class="text-slate-300 mx-1">&bull;</span>
                Dibuat oleh <a href="https://www.google.com/maps/place/NADIR+%7C+JASA+PENGUKURAN+LAHAN,+TOPOGRAFI,+PEMETAAN,+SEWA+ALAT+UKUR,+SEWA+DRONE,+PEMBUATAN+WEBSITE+%7C+NADIR/@-7.1668405,107.9054155,1403m/data=!3m2!1e3!4b1!4m6!3m5!1s0x2e68b1bcd5258c47:0x3c5a9d384d5b572f!8m2!3d-7.1668458!4d107.9079904!16s%2Fg%2F11z8rp2yc2!5m1!1e1?entry=ttu&g_ep=EgoyMDI2MTEwMy4wIKXMDSoASAFQAw%3D%3D" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-900 font-semibold hover:underline inline-flex items-center gap-1">NADIR <i class="fas fa-arrow-up-right-from-square text-[9px]"></i></a>
            </div>
            <div class="flex items-center gap-3 text-[11px] text-emerald-800 font-semibold">
                <span class="inline-flex items-center gap-1.5"><i class="fas fa-mountain text-[#D97736]"></i> Gunung Cikuray 2.821 MDPL</span>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1.5"><i class="fas fa-shield-halved text-emerald-600"></i> Operasional Resmi Cigedug</span>
            </div>
        </footer>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Quick thumb switch on phone) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#12241B]/95 backdrop-blur-md border-t border-[#2D5841] shadow-2xl safe-area-inset-bottom">
        <div class="grid grid-cols-4 h-16">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center text-slate-400 hover:text-white transition active:scale-95">
                <i class="fas fa-mountain text-base"></i>
                <span class="text-[9px] mt-0.5 font-semibold">Portal</span>
            </a>

            @php $role = auth()->user()?->role?->value; @endphp
            @if ($role === 'SUPERADMIN' || $role === 'ADMIN_SAR')
                <a href="{{ route('admin.index') }}" class="inline-flex flex-col items-center justify-center transition active:scale-95 {{ request()->routeIs('admin.index') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-gauge-high text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Command</span>
                </a>
                <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center transition active:scale-95 {{ request()->routeIs('pos.*') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-cash-register text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Kasir</span>
                </a>
            @elseif ($role === 'KASIR')
                <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center transition active:scale-95 {{ request()->routeIs('pos.index') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-cash-register text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Kasir</span>
                </a>
                <a href="{{ route('pos.shift') }}" class="inline-flex flex-col items-center justify-center transition active:scale-95 {{ request()->routeIs('pos.shift') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-vault text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Shift</span>
                </a>
            @else
                <a href="{{ route('fieldops.index') }}" class="inline-flex flex-col items-center justify-center transition active:scale-95 {{ request()->routeIs('fieldops.*') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-qrcode text-base"></i>
                    <span class="text-[9px] mt-0.5 font-semibold">Scanner</span>
                </a>
                <div class="col-span-1"></div>
            @endif

            <button type="button" @click="mobileSidebarOpen = true" class="inline-flex flex-col items-center justify-center text-amber-400 hover:text-amber-300 transition active:scale-95">
                <i class="fas fa-bars text-base"></i>
                <span class="text-[9px] mt-0.5 font-semibold">Menu</span>
            </button>
        </div>
    </nav>

    <!-- GLOBAL TOAST NOTIFICATION CONTAINER -->
    <x-ui.toast />

    @stack('scripts')
</body>
</html>
