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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        <!-- FLASH NOTIFICATIONS -->
        <div class="px-4 sm:px-6 lg:px-8 w-full mt-4">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-start justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <p class="text-xs sm:text-sm font-semibold">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-start justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-rose-600 text-lg"></i>
                        <p class="text-xs sm:text-sm font-semibold">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if (session('warning'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-amber-800 flex items-start justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-info-circle text-amber-600 text-lg"></i>
                        <p class="text-xs sm:text-sm font-semibold">{{ session('warning') }}</p>
                    </div>
                    <button @click="show = false" class="text-amber-600 hover:text-amber-800"><i class="fas fa-times"></i></button>
                </div>
            @endif
        </div>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6">
            {{ $slot }}
        </main>

        <!-- MINIMAL ADMIN FOOTER -->
        <footer class="px-4 sm:px-6 lg:px-8 py-4 border-t border-slate-200 bg-white text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
            <div>
                &copy; {{ date('Y') }} <strong>Basecamp SAMSARA</strong> &bull; Sistem Informasi Manajemen SIMS v1.4.0
            </div>
            <div class="flex items-center gap-3 text-[11px] text-emerald-800 font-semibold">
                <span class="inline-flex items-center gap-1.5"><i class="fas fa-mountain text-[#D97736]"></i> Gunung Cikuray 2.821 MDPL</span>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1.5"><i class="fas fa-shield-halved text-emerald-600"></i> Operasional Resmi Cigedug</span>
            </div>
        </footer>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Quick thumb switch on phone) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#12241B]/95 backdrop-blur-md border-t border-[#2D5841] shadow-2xl">
        <div class="grid grid-cols-4 h-15">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center text-slate-400 hover:text-white">
                <i class="fas fa-mountain text-base"></i>
                <span class="text-[9px] mt-0.5 font-medium">Portal</span>
            </a>

            @php $role = auth()->user()?->role?->value; @endphp
            @if ($role === 'SUPERADMIN' || $role === 'ADMIN_SAR')
                <a href="{{ route('admin.index') }}" class="inline-flex flex-col items-center justify-center {{ request()->routeIs('admin.index') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-gauge-high text-base"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Command</span>
                </a>
                <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center {{ request()->routeIs('pos.*') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-cash-register text-base"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Kasir</span>
                </a>
            @elseif ($role === 'KASIR')
                <a href="{{ route('pos.index') }}" class="inline-flex flex-col items-center justify-center {{ request()->routeIs('pos.index') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-cash-register text-base"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Kasir</span>
                </a>
                <a href="{{ route('pos.shift') }}" class="inline-flex flex-col items-center justify-center {{ request()->routeIs('pos.shift') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-vault text-base"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Shift</span>
                </a>
            @else
                <a href="{{ route('fieldops.index') }}" class="inline-flex flex-col items-center justify-center {{ request()->routeIs('fieldops.*') ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-white' }}">
                    <i class="fas fa-qrcode text-base"></i>
                    <span class="text-[9px] mt-0.5 font-medium">Scanner</span>
                </a>
                <div class="col-span-1"></div>
            @endif

            <button type="button" @click="mobileSidebarOpen = true" class="inline-flex flex-col items-center justify-center text-amber-400 hover:text-amber-300">
                <i class="fas fa-bars text-base"></i>
                <span class="text-[9px] mt-0.5 font-medium">Menu</span>
            </button>
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
