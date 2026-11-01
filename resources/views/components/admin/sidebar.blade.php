@php
    $user = auth()->user();
    $role = $user?->role?->value ?? 'GUEST';

    $setting = \Illuminate\Support\Facades\Cache::remember('trail_setting_current', 120, function() {
        return \App\Modules\Simaksi\Models\TrailSetting::current();
    });

    $pendingCount = \Illuminate\Support\Facades\Cache::remember('pending_simaksi_count', 30, function() {
        try {
            return \App\Modules\Simaksi\Models\SimaksiBooking::where('status', \App\Shared\Enums\BookingStatus::PENDING_PAYMENT)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    });

    $dashboardRoute = match($role) {
        'KASIR' => route('pos.index'),
        'PETUGAS_POS' => route('fieldops.index'),
        default => route('admin.index'),
    };
@endphp

<!-- MOBILE BACKDROP & DRAWER OVERLAY -->
<div x-show="mobileSidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs md:hidden"
     @click="mobileSidebarOpen = false"
     style="display: none;">
</div>

<aside x-show="mobileSidebarOpen"
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-[#12241B] text-white flex flex-col md:hidden border-r border-[#2D5841]/80 shadow-2xl"
       style="display: none;">
    <!-- Mobile Drawer Header -->
    <div class="p-4 flex items-center justify-between border-b border-[#2D5841]/70">
        <a href="{{ $dashboardRoute }}" class="flex items-center gap-3">
            <img src="/LOGO.jpeg" alt="SAMSARA Logo" class="h-9 w-9 rounded-xl object-cover ring-2 ring-[#D97736]/70 shadow-sm">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="font-heading font-extrabold text-base tracking-wider text-white">SAMSARA</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-gradient-to-r from-[#D97736] to-amber-500 text-white tracking-widest uppercase">SIMS</span>
                </div>
                <p class="text-[10px] text-emerald-200/80">Panel Staf Basecamp</p>
            </div>
        </a>
        <button type="button" @click="mobileSidebarOpen = false" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition">
            <i class="fas fa-times text-base"></i>
        </button>
    </div>

    <!-- Navigation Scroll Area -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        @include('components.admin.sidebar-nav-links')
    </div>

    <!-- Sidebar Footer -->
    <div class="p-3 border-t border-[#2D5841]/70 bg-[#0E1A14]">
        @include('components.admin.sidebar-footer')
    </div>
</aside>

<!-- DESKTOP PERSISTENT FIXED SIDEBAR -->
<aside class="hidden md:flex flex-col w-64 lg:w-72 fixed inset-y-0 left-0 z-40 bg-[#12241B] text-white border-r border-[#2D5841]/80 shadow-xl transition-all duration-300">
    <!-- Desktop Brand & Logo -->
    <div class="h-18 px-5 flex items-center justify-between border-b border-[#2D5841]/70 shrink-0 bg-[#0E1A14]/60">
        <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 group">
            <div class="relative">
                <img src="/LOGO.jpeg" alt="Logo SAMSARA" class="h-10 w-10 lg:h-11 lg:w-11 rounded-2xl object-cover ring-2 ring-[#D97736]/70 shadow-md group-hover:scale-105 transition duration-200">
                <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-[#12241B]" title="Sistem Aktif"></span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-heading font-black text-base lg:text-lg tracking-wider text-white group-hover:text-amber-200 transition">SAMSARA</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md bg-gradient-to-r from-[#D97736] to-amber-500 text-white tracking-widest uppercase shadow-xs">SIMS</span>
                </div>
                <p class="text-[10px] text-emerald-200/80 -mt-0.5 font-medium">Cikuray 2.821 MDPL via Cigedug</p>
            </div>
        </a>
    </div>

    <!-- Staff Identity Card -->
    <div class="px-4 py-3.5 bg-gradient-to-b from-[#162C21] to-[#12241B] border-b border-[#2D5841]/50 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#D97736] to-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-md ring-2 ring-emerald-500/30">
                {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="font-bold text-xs text-white truncate leading-tight">{{ $user->name ?? 'Staf SAMSARA' }}</div>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[10px] font-medium text-emerald-300 truncate">{{ $user->role->label() ?? 'Petugas' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Navigation Scroll Area -->
    <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-6 no-scrollbar">
        @include('components.admin.sidebar-nav-links')
    </div>

    <!-- Desktop Sidebar Footer -->
    <div class="p-3.5 border-t border-[#2D5841]/70 bg-[#0E1A14]/90 shrink-0">
        @include('components.admin.sidebar-footer')
    </div>
</aside>
