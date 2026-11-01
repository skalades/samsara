@props([
    'trailSetting',
    'remainingQuota',
    'maxQuota'
])

@php
    $quotaPercent = $maxQuota > 0 ? round(($remainingQuota / $maxQuota) * 100) : 0;
    $statusColor = match($trailSetting->trail_status->value) {
        'OPEN' => 'bg-emerald-400',
        'WARNING' => 'bg-amber-400',
        default => 'bg-rose-500',
    };
@endphp

<div class="relative overflow-hidden rounded-3xl text-white shadow-2xl mb-8 border border-[#2D5841]/80 group"
     x-data="{
         currentTime: '',
         init() {
             this.updateTime();
             setInterval(() => this.updateTime(), 1000);
         },
         updateTime() {
             const now = new Date();
             this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
         }
     }">
    <!-- Scenic Mountain Photo Background with Smooth Gradient Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="/assets/hero-cikuray.jpg" alt="Panorama Gunung Cikuray 2.821 MDPL" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-1000">
        <!-- Multi-layer Gradient: Deep Forest Green to Midnight Dark Vignette -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-[#1E3A2B]/90 to-slate-950/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-black/30"></div>
    </div>

    <div class="relative z-10 max-w-5xl p-6 sm:p-10 lg:p-12">
        <!-- Live Atmospheric Status Bar -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 mb-5">
            <!-- Trail Status Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 text-xs font-semibold backdrop-blur-md shadow-xs">
                <span class="w-2.5 h-2.5 rounded-full {{ $statusColor }} {{ $trailSetting->trail_status->value === 'OPEN' ? 'animate-pulse' : '' }}"></span>
                <span>STATUS JALUR: <strong class="text-white">{{ $trailSetting->trail_status->label() }}</strong></span>
            </div>

            <!-- Weather Status Pill -->
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/15 border border-white/20 text-amber-200 text-xs font-medium backdrop-blur-md shadow-xs">
                <i class="fas fa-cloud-sun text-amber-300"></i>
                <span>Cuaca Puncak: <strong class="text-white">{{ $trailSetting->weather_status->label() }}</strong></span>
            </div>

            <!-- Live Clock Widget -->
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-black/40 border border-white/15 text-slate-200 text-xs font-mono backdrop-blur-md">
                <i class="far fa-clock text-emerald-400"></i>
                <span x-text="currentTime || 'Memuat waktu...'"></span>
            </div>

            <!-- Elevation Pill -->
            <div class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-black/40 border border-white/15 text-slate-200 text-xs font-mono backdrop-blur-md">
                <i class="fas fa-mountain text-amber-400"></i>
                <span>Atap Garut &bull; 2.821 MDPL</span>
            </div>
        </div>

        <!-- Main Heading with Golden Sunlit Gradient Accent -->
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-heading font-extrabold tracking-tight text-white leading-tight">
            Gerbang Resmi Pendakian <br>
            <span class="bg-gradient-to-r from-amber-200 via-amber-300 to-orange-400 bg-clip-text text-transparent">Gunung Cikuray 2.821 MDPL</span>
        </h1>

        <p class="mt-4 text-sm sm:text-base lg:text-lg text-emerald-100/90 leading-relaxed max-w-2xl font-normal drop-shadow-xs">
            Jalur Resmi via Cigedug &mdash; Dikelola secara profesional oleh <strong>Basecamp SAMSARA</strong>. Rasakan sensasi jalur menantang berakar, vegetasi hutan lumut lebat, dan kemah magis Samudra Awan di Pos 4.
        </p>

        <!-- Temperature & Environmental Micro-Bar -->
        <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-slate-300 font-medium">
            <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl backdrop-blur-xs border border-white/10">
                <i class="fas fa-temperature-low text-sky-400"></i>
                <span>Basecamp (1.300M): <strong>19°C &ndash; 23°C</strong></span>
            </div>
            <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl backdrop-blur-xs border border-white/10">
                <i class="fas fa-snowflake text-indigo-300"></i>
                <span>Pos 4 & Puncak: <strong>8°C &ndash; 13°C</strong></span>
            </div>
            <div class="hidden md:flex items-center gap-1.5 text-emerald-300/80 font-mono">
                <i class="fas fa-shield-halved text-[#D97736]"></i>
                <span>SAMSARA Integrated Management System (SIMS) &bull; Jalur Berasuransi Resmi</span>
            </div>
        </div>

        <!-- Quota Quick Bar & Live Action Center (Glassmorphic Interactive Card) -->
        <div class="mt-8 p-5 sm:p-6 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#D97736] to-amber-600 flex items-center justify-center text-white text-2xl shadow-lg ring-2 ring-white/20 shrink-0">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-xs text-emerald-200 font-medium flex items-center gap-1.5">
                        <i class="fas fa-calendar-day text-amber-300"></i> Kuota Hari Ini &bull; {{ today()->translatedFormat('l, d M Y') }}
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-extrabold text-white mt-0.5">
                        <span class="text-amber-300 font-black">{{ $remainingQuota }}</span> <span class="text-sm font-normal text-slate-300">/ {{ $maxQuota }} Slot Tersedia</span>
                    </div>
                    <!-- Mini Progress Bar -->
                    <div class="w-48 sm:w-60 bg-black/30 h-1.5 rounded-full overflow-hidden mt-1.5">
                        <div class="bg-gradient-to-r from-emerald-400 to-amber-400 h-full rounded-full transition-all duration-500" style="width: {{ $quotaPercent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Direct Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('simaksi.create') }}" class="touch-target px-5 py-3 rounded-xl bg-gradient-to-r from-[#D97736] to-amber-600 hover:from-[#B25A20] hover:to-[#D97736] text-white font-bold text-xs sm:text-sm tracking-wide transition shadow-lg hover:shadow-orange-500/25 flex items-center gap-2 active:scale-95">
                    <i class="fas fa-ticket-alt"></i> Booking SIMAKSI
                </a>
                <button type="button" @click="$dispatch('open-ticket-lookup')" class="touch-target px-4 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs sm:text-sm transition border border-white/25 backdrop-blur-md flex items-center gap-2 active:scale-95">
                    <i class="fas fa-qrcode text-amber-300"></i> Cek Tiket Saya
                </button>
                <a href="{{ route('trail.map') }}" class="touch-target px-3.5 py-3 rounded-xl bg-black/40 hover:bg-black/60 text-slate-200 hover:text-white font-semibold text-xs sm:text-sm transition border border-white/15 backdrop-blur-md flex items-center gap-1.5">
                    <i class="fas fa-map-marked-alt text-emerald-400"></i> Peta Satelit & Trek
                </a>
            </div>
        </div>

        @if ($trailSetting->announcement)
            <div class="mt-4 p-3.5 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-100 text-xs flex items-start gap-3 backdrop-blur-md">
                <i class="fas fa-bullhorn text-amber-300 mt-0.5 shrink-0 text-sm"></i>
                <span class="leading-relaxed">{{ $trailSetting->announcement }}</span>
            </div>
        @endif
    </div>
</div>
