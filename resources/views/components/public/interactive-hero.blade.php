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

<div class="relative overflow-hidden rounded-3xl text-white shadow-2xl mb-8 border border-[#2D5841]/80 group">
    <!-- Scenic Mountain Photo Background -->
    <div class="absolute inset-0 z-0">
        <img src="/assets/hero-cikuray.jpg" alt="Panorama Gunung Cikuray 2.821 MDPL" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-1000">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-[#1E3A2B]/90 to-slate-950/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-black/30"></div>
    </div>

    <div class="relative z-10 max-w-5xl p-6 sm:p-10 lg:p-12">
        <!-- Status Bar -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 mb-5">
            <!-- Trail Status Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 text-xs font-semibold backdrop-blur-md">
                <span class="w-2 h-2 rounded-full {{ $statusColor }} {{ $trailSetting->trail_status->value === 'OPEN' ? 'animate-pulse' : '' }}"></span>
                <span>STATUS JALUR: <strong class="text-white">{{ $trailSetting->trail_status->label() }}</strong></span>
            </div>

            <!-- Weather Status Pill -->
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-amber-200 text-xs font-medium backdrop-blur-md">
                <i class="fas fa-cloud-sun text-amber-300"></i>
                <span>Cuaca Puncak: <strong class="text-white">{{ $trailSetting->weather_status->label() }}</strong></span>
            </div>
        </div>

        <!-- Main Heading -->
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-heading font-extrabold tracking-tight text-white leading-tight">
            Gerbang Resmi Pendakian <br>
            <span class="bg-gradient-to-r from-amber-200 via-amber-300 to-orange-400 bg-clip-text text-transparent">Gunung Cikuray 2.821 MDPL</span>
        </h1>

        <p class="mt-4 text-sm sm:text-base lg:text-lg text-emerald-100/90 leading-relaxed max-w-2xl font-normal">
            Jalur Resmi via Cigedug &mdash; Dikelola secara profesional oleh <strong>Basecamp SAMSARA</strong>. Rasakan sensasi jalur menantang berakar, vegetasi hutan lumut lebat, dan kemah magis Samudra Awan di Pos 4.
        </p>

        <!-- Quota Card & CTA -->
        <div class="mt-8 p-5 sm:p-6 rounded-2xl bg-black/30 border border-white/15 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <!-- Quota Info -->
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0 mt-0.5 {{ $remainingQuota > 0 ? 'animate-pulse' : '' }}"></span>
                <div>
                    <div class="text-xs text-slate-400 font-medium">
                        <i class="fas fa-calendar-day text-amber-400 mr-1"></i> Kuota Hari Ini &bull; {{ today()->translatedFormat('l, d M Y') }}
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-extrabold text-white mt-0.5">
                        <span class="text-amber-300">{{ $remainingQuota }}</span>
                        <span class="text-sm font-normal text-slate-400"> / {{ $maxQuota }} Slot Tersedia</span>
                    </div>
                    <div class="w-48 sm:w-60 bg-black/40 h-1.5 rounded-full overflow-hidden mt-1.5">
                        <div class="bg-gradient-to-r from-emerald-400 to-amber-400 h-full rounded-full transition-all duration-500" style="width: {{ $quotaPercent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('simaksi.create') }}" class="touch-target px-5 py-3 rounded-xl bg-gradient-to-r from-[#D97736] to-amber-600 hover:from-[#B25A20] hover:to-[#D97736] text-white font-bold text-xs sm:text-sm tracking-wide transition shadow-lg hover:shadow-orange-500/25 flex items-center gap-2 active:scale-95">
                    <i class="fas fa-ticket-alt"></i> Booking SIMAKSI
                </a>
                <button type="button" @click="$dispatch('open-ticket-lookup')" class="touch-target px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm transition border border-white/20 flex items-center gap-2 active:scale-95">
                    <i class="fas fa-qrcode text-amber-300"></i> Cek Tiket Saya
                </button>
                <a href="{{ route('trail.map') }}" class="touch-target px-3.5 py-3 rounded-xl bg-black/40 hover:bg-black/60 text-slate-300 hover:text-white font-semibold text-xs sm:text-sm transition border border-white/10 flex items-center gap-1.5">
                    <i class="fas fa-map-marked-alt text-emerald-400"></i> Peta & Trek
                </a>
            </div>
        </div>

        @if ($trailSetting->announcement)
            <div class="mt-4 p-3.5 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-100 text-xs flex items-start gap-3">
                <i class="fas fa-bullhorn text-amber-300 mt-0.5 shrink-0"></i>
                <span class="leading-relaxed">{{ $trailSetting->announcement }}</span>
            </div>
        @endif
    </div>
</div>
