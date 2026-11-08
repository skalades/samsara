<div class="relative overflow-hidden rounded-2xl bg-[#0F1A13] border border-slate-800 text-white p-6 sm:p-8 mb-12 shadow-lg">
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        <!-- Left: Text & Badges -->
        <div class="max-w-2xl">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] flex items-center gap-1.5">
                    <i class="fas fa-satellite"></i> Halaman Khusus Geospasial
                </span>
                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800/80">
                    836 Titik GPS Riil Lapangan
                </span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-950 text-amber-300 border border-amber-800/80">
                    Elevasi Gain +1.576 M
                </span>
            </div>

            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight">
                Peta Satelit GPS & Trek Rute Cikuray
            </h2>

            <p class="text-xs sm:text-sm text-slate-300 mt-3 leading-relaxed">
                Akses halaman terpisah dengan peta satelit resolusi tinggi, profil grafik elevasi dari Basecamp Cigedug (1.245m) hingga Puncak (2.821m), titik sumber air Pos 3, dan dokumentasi pos 1–4.
            </p>

            <!-- 4 Quick Fact Chips -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-5">
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/10 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">Puncak Tertinggi</span>
                    <span class="font-heading font-bold text-sm text-amber-300 mt-0.5 block">2.821 MDPL</span>
                </div>
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/10 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">Panjang Trek</span>
                    <span class="font-heading font-bold text-sm text-white mt-0.5 block">6.8 KM</span>
                </div>
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/10 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">Estimasi Waktu</span>
                    <span class="font-heading font-bold text-sm text-emerald-400 mt-0.5 block">4.5 &ndash; 6 Jam</span>
                </div>
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/10 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">Mata Air Terakhir</span>
                    <span class="font-heading font-bold text-sm text-sky-400 mt-0.5 block">Pos 3 Lembah</span>
                </div>
            </div>
        </div>

        <!-- Right: Primary CTA Action Box -->
        <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0 lg:w-72">
            <a href="{{ route('trail.map') }}"
               class="touch-target px-6 py-4 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white text-sm font-bold transition flex items-center justify-center gap-2.5 shadow-md group">
                <i class="fas fa-map-marked-alt text-base group-hover:scale-110 transition"></i>
                <span>Buka Peta Satelit & Trek</span>
                <i class="fas fa-arrow-right text-xs opacity-80 group-hover:translate-x-1 transition"></i>
            </a>

            <a href="{{ route('trail.map') }}#pos-explorer"
               class="touch-target px-5 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-slate-200 hover:text-white text-xs font-bold transition flex items-center justify-center gap-2 border border-white/15">
                <i class="fas fa-route text-emerald-400"></i>
                <span>Lihat Detail Pos 1 s/d 4</span>
            </a>

            <a href="/Trek%201.kml" download
               class="touch-target px-5 py-3 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center justify-center gap-2 border border-slate-700/80">
                <i class="fas fa-download text-amber-400"></i>
                <span>Unduh File KML GPS Offline</span>
            </a>
        </div>
    </div>
</div>
