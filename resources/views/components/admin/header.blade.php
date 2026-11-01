@props(['title' => null])

@php
    $user = auth()->user();
    $setting = \Illuminate\Support\Facades\Cache::remember('trail_setting_current', 120, function() {
        return \App\Modules\Simaksi\Models\TrailSetting::current();
    });
@endphp

<header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-2xs">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-18">
            <!-- Left Side: Mobile Menu Button & Breadcrumbs / Title -->
            <div class="flex items-center gap-3 min-w-0">
                <button type="button"
                        @click="mobileSidebarOpen = true"
                        class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition touch-target"
                        aria-label="Buka Navigasi">
                    <i class="fas fa-bars-staggered text-lg text-emerald-800"></i>
                </button>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400">
                        <span class="text-emerald-700 font-bold uppercase tracking-wider">SAMSARA SIMS</span>
                        <span>&bull;</span>
                        <span class="truncate">{{ $user?->role?->label() ?? 'Panel Staf' }}</span>
                    </div>
                    <h1 class="font-heading font-black text-lg sm:text-xl text-slate-900 tracking-tight truncate leading-tight">
                        {{ $title ?? 'Pusat Operasional Basecamp' }}
                    </h1>
                </div>
            </div>

            <!-- Right Side: Fast Actions, Trail Status & User Menu -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Trail Status Pill -->
                <div class="hidden sm:inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full border border-slate-200 bg-slate-50 text-slate-700 shadow-2xs">
                    <span class="w-2 h-2 rounded-full {{ $setting->trail_status->value === 'OPEN' ? 'bg-emerald-500 animate-pulse' : ($setting->trail_status->value === 'WARNING' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                    <span>Jalur: <strong class="text-slate-900 font-bold">{{ $setting->trail_status->label() }}</strong></span>
                </div>

                <!-- Portal Pendaki Quick Shortcut -->
                <a href="{{ route('home') }}" target="_blank"
                   title="Buka Portal Pendaki di Tab Baru"
                   class="hidden lg:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition">
                    <i class="fas fa-arrow-up-right-from-square text-[#D97736]"></i>
                    <span>Portal Publik</span>
                </a>

                <!-- User Profile Pill -->
                <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                    <div class="text-right hidden sm:block">
                        <span class="text-xs font-bold text-slate-900 block leading-tight">{{ $user?->name ?? 'Staf' }}</span>
                        <span class="text-[10px] font-medium text-emerald-600 block">{{ $user?->role?->value ?? 'Staf' }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                title="Keluar dari Sistem"
                                class="p-2 sm:px-3 sm:py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition flex items-center gap-1.5 border border-rose-200">
                            <i class="fas fa-right-from-bracket"></i>
                            <span class="hidden md:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
