@php
    $setting = \Illuminate\Support\Facades\Cache::remember('trail_setting_current', 120, function() {
        return \App\Modules\Simaksi\Models\TrailSetting::current();
    });
@endphp

<div class="space-y-2">
    <!-- Trail Status Badge -->
    <div class="px-3 py-2 rounded-xl bg-emerald-950/80 border border-emerald-500/30 flex items-center justify-between text-xs">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full {{ $setting->trail_status->value === 'OPEN' ? 'bg-emerald-400 animate-pulse' : ($setting->trail_status->value === 'WARNING' ? 'bg-amber-400' : 'bg-rose-500') }}"></span>
            <span class="text-slate-300 text-[11px]">Jalur Cikuray:</span>
        </div>
        <span class="font-bold text-white text-[11px]">{{ $setting->trail_status->label() }}</span>
    </div>

    <!-- Quick External Link to Public Website -->
    <a href="{{ route('home') }}" target="_blank"
       class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-emerald-200 hover:text-white text-xs font-semibold border border-white/10 transition group">
        <span class="flex items-center gap-2">
            <i class="fas fa-globe text-amber-400 group-hover:rotate-12 transition"></i>
            <span>Lihat Portal Publik</span>
        </span>
        <i class="fas fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-white"></i>
    </a>

    <!-- Logout Action Button -->
    <form action="{{ route('logout') }}" method="POST" class="w-full">
        @csrf
        <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-rose-950/60 hover:bg-rose-900 text-rose-200 hover:text-white text-xs font-bold border border-rose-800/40 transition">
            <i class="fas fa-right-from-bracket"></i>
            <span>Keluar Akun</span>
        </button>
    </form>
</div>
