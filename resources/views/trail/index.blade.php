<x-layouts.app title="Peta Satelit GPS & Trek Jalur Gunung Cikuray 2.821 MDPL | Basecamp SAMSARA">

    <!-- BREADCRUMB & HEADER SECTION -->
    <div class="mb-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-3 overflow-x-auto no-scrollbar py-1">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition flex items-center gap-1.5 shrink-0">
                <i class="fas fa-home"></i> Beranda
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-bold shrink-0">Peta & Trek GPS Cikuray</span>
        </nav>

        <!-- Main Heading & Status Badges -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] flex items-center gap-1.5">
                        <i class="fas fa-satellite"></i> Sistem Geospasial Resmi SAMSARA
                    </span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $trailSetting->trail_status->badgeClass() }} shadow-2xs">
                        Jalur: {{ $trailSetting->trail_status->label() }}
                    </span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 border border-sky-200">
                        <i class="fas fa-cloud-sun text-sky-600 mr-1"></i> Cuaca: {{ $trailSetting->weather_status->label() }}
                    </span>
                </div>
                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl lg:text-4xl text-slate-900 tracking-tight">
                    Peta Satelit GPS & Trek Jalur Gunung Cikuray
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-3xl leading-relaxed">
                    Panduan geospasial resmi jalur pendakian via Cigedug (2.821 MDPL). Dilengkapi rekaman 836 titik GPS riil, visualisasi profil elevasi +1.576m, titik mata air Pos 3, dan dokumentasi pos 1–4.
                </p>
            </div>

            <!-- Header Quick Action CTAs -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="#download-kml" class="touch-target px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition inline-flex items-center gap-2 border border-slate-300/80 shadow-xs">
                    <i class="fas fa-download text-[#D97736]"></i>
                    <span>Unduh File KML GPS</span>
                </a>
                <a href="{{ route('simaksi.create') }}" class="touch-target px-4 py-2.5 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white text-xs font-bold transition inline-flex items-center gap-2 shadow-sm">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Booking SIMAKSI</span>
                </a>
            </div>
        </div>

        <!-- Announcement Alert (if active) -->
        @if ($trailSetting->announcement)
            <div class="mt-4 p-3.5 sm:p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3 shadow-2xs">
                <i class="fas fa-bullhorn text-amber-600 text-sm mt-0.5 shrink-0"></i>
                <div class="flex-1">
                    <strong class="font-bold">Info Lapangan Terkini Basecamp:</strong> {{ $trailSetting->announcement }}
                </div>
            </div>
        @endif
    </div>

    <!-- TECHNICAL STATS CARDS (5 METRICS) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-8">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Puncak Tertinggi</span>
                <span class="font-heading font-extrabold text-2xl text-[#D97736] mt-1 block">2.821 <span class="text-xs font-semibold text-slate-600">MDPL</span></span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Atap Garut &bull; Panorama 360°</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Panjang Trek</span>
                <span class="font-heading font-extrabold text-2xl text-slate-800 mt-1 block">6.8 <span class="text-xs font-semibold text-slate-600">KM</span></span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Basecamp s/d Tugu Puncak</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Elevasi Gain</span>
                <span class="font-heading font-extrabold text-2xl text-emerald-700 mt-1 block">+1.576 <span class="text-xs font-semibold text-slate-600">M</span></span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Gradien tanjakan konstan 28°</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Estimasi Waktu</span>
                <span class="font-heading font-extrabold text-2xl text-slate-800 mt-1 block">4.5 &ndash; 6 <span class="text-xs font-semibold text-slate-600">Jam</span></span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Ritme trekking normal</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Titik Sumber Air</span>
                <span class="font-heading font-extrabold text-sm sm:text-base text-sky-700 mt-1 block flex items-center gap-1.5">
                    <i class="fas fa-droplet text-sky-500"></i> Basecamp & Pos 3
                </span>
            </div>
            <p class="text-[11px] text-amber-700 font-semibold mt-2">
                <i class="fas fa-exclamation-triangle text-amber-500 mr-1"></i> Pos 4 Kering (Wajib Ambil di Pos 3)
            </p>
        </div>
    </div>

    <!-- MAIN LEAFLET MAP & INTERACTIVE CONTROLS CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-10 scroll-mt-24" id="peta-gps">
        <!-- Map Control Bar (Layers & Checkpoint Quick Focus) -->
        <div class="p-3.5 sm:p-4 bg-slate-950 text-white flex flex-col lg:flex-row lg:items-center justify-between gap-3 border-b border-slate-800">
            <!-- Left: Map Layer Switcher -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar text-xs">
                <span class="text-[10px] uppercase font-mono font-bold text-slate-400 mr-1 shrink-0 hidden sm:inline">Lapisan:</span>
                <button type="button" class="map-layer-btn active px-3 py-1.5 rounded-xl bg-[#D97736] text-white font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer shadow-xs" data-layer="satellite">
                    <i class="fas fa-satellite text-[11px]"></i> Satelit HD
                </button>
                <button type="button" class="map-layer-btn px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer" data-layer="topo">
                    <i class="fas fa-mountain text-[11px]"></i> Topografi
                </button>
                <button type="button" class="map-layer-btn px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer" data-layer="dark">
                    <i class="fas fa-moon text-[11px]"></i> Mode Gelap
                </button>
                <button type="button" class="map-layer-btn px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer" data-layer="osm">
                    <i class="fas fa-map text-[11px]"></i> OpenStreetMap
                </button>
            </div>

            <!-- Right: Checkpoint Quick Zoom Buttons -->
            <div class="flex items-center gap-1 overflow-x-auto no-scrollbar text-xs">
                <span class="text-[10px] uppercase font-mono font-bold text-slate-400 mr-1 shrink-0 hidden lg:inline">Fokus Pos:</span>
                <button type="button" class="map-quick-btn active px-2.5 py-1.5 rounded-lg bg-white/20 text-white font-bold transition text-[11px] shrink-0 whitespace-nowrap cursor-pointer" data-pos="all">
                    Semua
                </button>
                <button type="button" class="map-quick-btn px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition text-[11px] shrink-0 whitespace-nowrap cursor-pointer" data-pos="basecamp">
                    BC
                </button>
                <button type="button" class="map-quick-btn px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition text-[11px] shrink-0 whitespace-nowrap cursor-pointer" data-pos="pos1">
                    Pos 1
                </button>
                <button type="button" class="map-quick-btn px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition text-[11px] shrink-0 whitespace-nowrap cursor-pointer" data-pos="pos2">
                    Pos 2
                </button>
                <button type="button" class="map-quick-btn px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition text-[11px] shrink-0 whitespace-nowrap cursor-pointer" data-pos="pos3">
                    Pos 3
                </button>
                <button type="button" class="map-quick-btn px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition text-[11px] shrink-0 whitespace-nowrap cursor-pointer" data-pos="pos4">
                    Pos 4
                </button>
                <button type="button" class="map-quick-btn px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition text-[11px] shrink-0 whitespace-nowrap cursor-pointer" data-pos="puncak">
                    Puncak
                </button>

                <!-- Reset Zoom Icon -->
                <button type="button" id="btn-reset-map" title="Reset Pandangan Peta" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition text-xs shrink-0 ml-1">
                    <i class="fas fa-compress-arrows-alt"></i>
                </button>
            </div>
        </div>

        <!-- The Leaflet Map Canvas -->
        <div class="relative">
            <div id="samsara-leaflet-map" class="w-full h-[450px] sm:h-[560px] lg:h-[620px] bg-slate-950 z-0"></div>

            <!-- Floating Legend Overlay -->
            <div class="absolute bottom-4 left-4 z-10 bg-slate-950/85 backdrop-blur-md border border-white/15 p-3 rounded-2xl text-[11px] text-white shadow-xl space-y-1.5 hidden sm:block pointer-events-none">
                <div class="font-bold text-amber-300 text-xs mb-1 flex items-center gap-1.5">
                    <i class="fas fa-layer-group"></i> Legenda Jalur Cigedug
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-1 bg-amber-400 rounded-full inline-block"></span>
                    <span>Trek GPS Riil Lapangan (836 Titik)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 border border-white inline-block"></span>
                    <span>Pos Checkpoint Resmi (BC, 1, 2, 3)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#D97736] border border-white inline-block"></span>
                    <span>Pos 4 Camp & Tugu Puncak (2.821 M)</span>
                </div>
                <div class="flex items-center gap-2 text-sky-400">
                    <i class="fas fa-droplet text-xs"></i>
                    <span>Sumber Air (BC & Pos 3 Lembah)</span>
                </div>
            </div>

            <!-- Mobile Quick Helper Badge -->
            <div class="sm:hidden absolute top-3 right-3 z-10 bg-black/75 backdrop-blur-md text-white text-[10px] px-2.5 py-1 rounded-full border border-white/20 pointer-events-none">
                <i class="fas fa-hand-pointer mr-1 text-amber-400"></i> Ketuk pin untuk detail pos
            </div>
        </div>

        <!-- Elevation Profile Canvas Section -->
        <div class="p-5 sm:p-7 bg-slate-950 text-white border-t border-slate-800" id="elevasi">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-2 gap-2 text-xs border-b border-slate-800">
                <div class="flex items-center gap-2 font-bold text-slate-200">
                    <i class="fas fa-chart-area text-amber-400 text-sm"></i>
                    <span>Grafik Elevasi: Basecamp (1.245 MDPL) &rarr; Puncak Cikuray (2.821 MDPL)</span>
                </div>
                <div class="text-[11px] text-amber-300 font-semibold flex items-center gap-3">
                    <span><i class="fas fa-arrows-up-down mr-1"></i> Total Gain: +1.576 M</span>
                    <span><i class="fas fa-angle-right mr-1"></i> Sudut Rata-rata: 28°</span>
                </div>
            </div>

            <div class="w-full h-44 sm:h-56 relative mt-2">
                <canvas id="elevation-canvas" class="w-full h-full block"></canvas>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between text-[11px] text-slate-400">
                <span>*Grafik dihitung dari rekaman koordinat altimeter GPS riil Basecamp SAMSARA via Cigedug.</span>
                <span class="text-emerald-400 font-semibold"><i class="fas fa-check-circle mr-1"></i> Tanjakan terjal konstan &bull; Tanpa bonus datar</span>
            </div>
        </div>
    </div>

    <!-- CHECKPOINT & POS EXPLORER SECTION (AUTHENTIC FIELD DOCUMENTATION) -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-9 shadow-sm mb-12 scroll-mt-24" id="pos-explorer"
         x-data="{
             activePos: '{{ $targetPos !== 'all' ? $targetPos : 'pos4' }}',
             checkpoints: {{ json_encode($checkpoints) }},
             getActive() {
                 return this.checkpoints.find(c => c.id === this.activePos) || this.checkpoints[0];
             },
             focusOnMap(posId) {
                 if (typeof window.focusCheckpointOnMap === 'function') {
                     window.focusCheckpointOnMap(posId);
                 }
             }
         }">

        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736]">
                        <i class="fas fa-route"></i> Profil Pos Resmi
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                        Dokumentasi Asli Lapangan
                    </span>
                </div>
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">
                    Eksplorasi Detail Pos 1 s/d Puncak
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                    Ketahui karakteristik medan, ketersediaan air, kekuatan sinyal operator, dan kapasitas pendirian tenda di setiap checkpoint.
                </p>
            </div>

            <!-- Pos Selection Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1">
                <template x-for="cp in checkpoints" :key="cp.id">
                    <button type="button"
                            @click="activePos = cp.id"
                            :class="activePos === cp.id ? 'bg-[#1E3A2B] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span x-text="cp.number" class="text-[11px] opacity-80"></span>
                        <span x-text="cp.id === 'basecamp' ? 'BC' : cp.name.split(' ')[0]" class="hidden sm:inline"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Selected Pos Detail Viewer -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left: Pos Photo Box -->
            <div class="lg:col-span-5 rounded-2xl overflow-hidden border border-slate-200 shadow-sm relative group bg-slate-900">
                <img :src="getActive().image" :alt="getActive().name" class="w-full h-64 sm:h-80 object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                <!-- Floating Badge -->
                <div class="absolute top-4 left-4 flex items-center gap-2">
                    <span class="px-3 py-1 rounded-xl bg-[#D97736] text-white font-heading font-extrabold text-xs shadow-md" x-text="getActive().number"></span>
                    <span class="px-3 py-1 rounded-xl bg-black/60 backdrop-blur-md text-amber-300 font-bold text-xs border border-white/20" x-text="getActive().elevation + ' MDPL'"></span>
                </div>

                <!-- Bottom Photo Caption -->
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <h3 class="font-heading font-extrabold text-lg sm:text-xl text-white drop-shadow-sm" x-text="getActive().name"></h3>
                    <p class="text-xs text-slate-300 mt-1 line-clamp-2" x-text="getActive().tagline"></p>
                </div>
            </div>

            <!-- Right: Technical Pos Specs & Info -->
            <div class="lg:col-span-7 flex flex-col justify-between h-full space-y-5">
                <div>
                    <!-- Distance & Time Banner -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Jarak Tempuh</span>
                            <span class="font-bold text-xs sm:text-sm text-slate-800 mt-0.5 block" x-text="getActive().distance"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Estimasi Waktu</span>
                            <span class="font-bold text-xs sm:text-sm text-emerald-700 mt-0.5 block" x-text="getActive().time"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Kapasitas Tenda</span>
                            <span class="font-bold text-xs sm:text-sm text-slate-800 mt-0.5 block" x-text="getActive().tentCapacity"></span>
                        </div>
                    </div>

                    <!-- Detailed Description -->
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="getActive().desc"></p>

                    <!-- Key Field Checklist -->
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-xl border" :class="getActive().waterAvailable ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-rose-50/70 border-rose-200 text-rose-900'">
                            <div class="font-bold flex items-center gap-1.5 mb-1">
                                <i :class="getActive().waterAvailable ? 'fas fa-droplet text-emerald-600' : 'fas fa-droplet-slash text-rose-600'"></i>
                                <span>Sumber Mata Air:</span>
                            </div>
                            <span x-text="getActive().water" class="font-medium"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800">
                            <div class="font-bold flex items-center gap-1.5 mb-1 text-slate-700">
                                <i class="fas fa-tower-broadcast text-sky-600"></i>
                                <span>Sinyal Seluler:</span>
                            </div>
                            <span x-text="getActive().signal" class="font-medium text-slate-600"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 sm:col-span-2">
                            <div class="font-bold flex items-center gap-1.5 mb-1 text-slate-700">
                                <i class="fas fa-mountain-sun text-[#D97736]"></i>
                                <span>Karakteristik Medan & Vegetasi:</span>
                            </div>
                            <span x-text="getActive().terrain" class="font-medium text-slate-600"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Button: Center Map to this Checkpoint -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <span class="text-xs text-slate-500 flex items-center gap-1.5">
                        <i class="fas fa-satellite text-[#D97736]"></i>
                        <span>Tersinkronisasi otomatis dengan titik koordinat satelit GPS.</span>
                    </span>
                    <button type="button" @click="focusOnMap(activePos)"
                            class="touch-target px-4 py-2.5 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-bold transition flex items-center gap-2 shadow-sm cursor-pointer">
                        <i class="fas fa-location-crosshairs text-amber-400"></i>
                        <span>Fokuskan Posisi di Peta</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DOWNLOAD GPS FILES CENTER SECTION (3 KML/GPX FILES) -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-9 shadow-lg mb-12 scroll-mt-24 border border-slate-800" id="download-kml">
        <div class="max-w-3xl mb-8">
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] flex items-center gap-1.5 mb-2">
                <i class="fas fa-cloud-arrow-down"></i> Pusat Unduh Geospasial SAMSARA
            </span>
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-white">
                Download Berkas GPS (KML & GPX Riil)
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                Gunakan berkas navigasi resmi untuk panduan offline di aplikasi ponsel tanpa koneksi internet saat mendaki (Avenza Maps, Garmin BaseCamp, OsmAnd, Locus Map, Google Earth).
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <!-- Trek 1: Checkpoints / Placemarks -->
            <div class="p-5 rounded-2xl bg-white/5 border border-white/10 hover:border-amber-400/50 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg mb-3">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <h3 class="font-heading font-bold text-base text-white">Trek 1: Placemarks Pos</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Titik koordinat akurat Basecamp, Jembatan, Pos 1 Tanjakan EOD, Pos 2 Batu Gendrung, Pos 3 Alam Rimba, Pos 4 Samudra Awan, hingga Puncak.
                    </p>
                    <div class="mt-3 text-[11px] text-slate-500 font-mono">Format: KML &bull; Ukuran: ±4.5 KB</div>
                </div>
                <a href="/Trek%201.kml" download class="touch-target mt-5 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                    <i class="fas fa-download"></i> Unduh Trek 1.kml
                </a>
            </div>

            <!-- Trek 2: 836 Points Main Track Log -->
            <div class="p-5 rounded-2xl bg-white/5 border border-white/10 hover:border-emerald-400/50 transition flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-3 right-3 text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    UTAMA
                </div>
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg mb-3">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3 class="font-heading font-bold text-base text-white">Trek 2: Track Log GPS Riil</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Log lintasan rekaman altimeter GPS lengkap dengan 836 titik koordinat pendakian dari pos registrasi awal sampai titik puncak 2.821 MDPL.
                    </p>
                    <div class="mt-3 text-[11px] text-slate-500 font-mono">Format: KML &bull; Ukuran: ±271 KB</div>
                </div>
                <a href="/Trek%202.kml" download class="touch-target mt-5 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                    <i class="fas fa-download"></i> Unduh Trek 2.kml
                </a>
            </div>

            <!-- Trek 3: Descent Log & Alternate Route -->
            <div class="p-5 rounded-2xl bg-white/5 border border-white/10 hover:border-sky-400/50 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-lg mb-3">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h3 class="font-heading font-bold text-base text-white">Trek 3: Log Turun / Alternatif</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Data log lintasan turun dan variasi kontur punggungan lereng barat daya Cikuray untuk evaluasi kecepatan turun dan navigasi kabut.
                    </p>
                    <div class="mt-3 text-[11px] text-slate-500 font-mono">Format: KML &bull; Ukuran: ±85 KB</div>
                </div>
                <a href="/Trek%203.kml" download class="touch-target mt-5 px-4 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-slate-950 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                    <i class="fas fa-download"></i> Unduh Trek 3.kml
                </a>
            </div>
        </div>

        <!-- How To Use Guide -->
        <div class="p-5 rounded-2xl bg-white/5 border border-white/10">
            <h4 class="font-heading font-bold text-sm text-amber-300 uppercase tracking-wider mb-2 flex items-center gap-2">
                <i class="fas fa-mobile-screen-button"></i> Cara Membuka di Smartphone (Offline Tanpa Sinyal):
            </h4>
            <ol class="list-decimal list-inside space-y-1.5 text-xs text-slate-300 leading-relaxed">
                <li>Unduh salah satu atau ketiga file KML di atas ke penyimpanan smartphone Anda sebelum berangkat.</li>
                <li>Pasang aplikasi navigasi offline outdoor seperti <strong>Avenza Maps</strong>, <strong>OsmAnd</strong>, atau <strong>Google Earth</strong> dari Play Store / App Store.</li>
                <li>Buka aplikasi &rarr; pilih menu <em>Import / Open File</em> &rarr; pilih berkas <code>Trek 1.kml</code> atau <code>Trek 2.kml</code>.</li>
                <li>Aktifkan GPS (Location) smartphone Anda saat berada di jalur Cigedug untuk memantau posisi realtime Anda terhadap titik trek resmi.</li>
            </ol>
        </div>
    </div>

    <!-- SAFETY RULES & SOP GUIDE -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-9 shadow-sm mb-12">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736]">
                <i class="fas fa-shield-halved"></i> Standar Operasional Jalur
            </span>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800">
                Wajib Dipatuhi
            </span>
        </div>
        <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 mb-6">
            Pedoman Keselamatan Pendakian Gunung Cikuray
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xs shrink-0 font-bold">1</div>
                <div>
                    <h4 class="font-heading font-bold text-sm text-slate-900">Batas Waktu Summit Attack (Cut-Off Time)</h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Summit attack dari Pos 4 Samudra Awan idealnya dimulai pukul <strong>03.30 &ndash; 04.30 WIB</strong>. Pendaki tidak disarankan memulai summit setelah pukul 06.00 WIB guna menghindari terik terpaan matahari di vegetasi terbuka.
                    </p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-sky-500 text-white flex items-center justify-center text-xs shrink-0 font-bold">2</div>
                <div>
                    <h4 class="font-heading font-bold text-sm text-slate-900">Manajemen Air (Pos 3 Adalah Sumber Terakhir)</h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Pastikan seluruh botol/jerigen diisi penuh di mata air lembah Pos 3. Di Pos 4 Samudra Awan dan Puncak <strong>sama sekali tidak ada sumber air</strong>. Siapkan minimal 3 liter air per orang untuk kebutuhan camp & summit.
                    </p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs shrink-0 font-bold">3</div>
                <div>
                    <h4 class="font-heading font-bold text-sm text-slate-900">Larangan Memotong Jalur (Switchback)</h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Tetap berjalan pada lintasan GPS resmi. Dilarang keras memotong jalur karena lereng Cikuray memiliki kemiringan 45°&ndash;60° yang sangat rentan longsor serta berisiko fatal tersesat di jurang hutan lumut.
                    </p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xs shrink-0 font-bold">4</div>
                <div>
                    <h4 class="font-heading font-bold text-sm text-slate-900">Hotline Darurat SAR Basecamp SAMSARA (24 Jam)</h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Jika terjadi cedera, hipotermia, atau kondisi darurat di jalur, segera lapor ke Pos Pantau Ranger SAMSARA di Pos 4 atau hubungi hotline SAR: <strong class="text-rose-700">0812-2345-6789</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM CTA BANNER -->
    <div class="p-8 sm:p-12 rounded-3xl bg-gradient-to-r from-[#1E3A2B] to-[#12241B] text-white border border-[#2D5841] shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
        <div>
            <span class="text-xs font-bold text-amber-300 uppercase tracking-widest block mb-1">
                Basecamp SAMSARA Cigedug
            </span>
            <h3 class="font-heading font-extrabold text-2xl sm:text-3xl text-white">
                Siap Menapaki Puncak Gunung Cikuray?
            </h3>
            <p class="text-xs sm:text-sm text-emerald-200/90 mt-1 max-w-xl">
                Cek ketersediaan kuota harian, amankan izin SIMAKSI online resmi, dan sewa tenda berkualitas terinspeksi langsung dari basecamp.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="{{ route('simaksi.create') }}" class="touch-target px-6 py-3.5 rounded-2xl bg-[#D97736] hover:bg-[#B25A20] text-white text-sm font-bold transition flex items-center gap-2 shadow-md">
                <i class="fas fa-ticket-alt"></i> Booking SIMAKSI Sekarang
            </a>
            <a href="{{ route('rental.catalog') }}" class="touch-target px-5 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-sm font-bold transition flex items-center gap-2 border border-white/20 shadow-xs">
                <i class="fas fa-campground text-emerald-400"></i> Sewa Perlengkapan
            </a>
        </div>
    </div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .dark-mode-tiles .leaflet-tile {
            filter: brightness(0.65) invert(1) contrast(3) hue-rotate(200deg) saturate(0.2) brightness(0.8);
        }
        .leaflet-popup-content-wrapper {
            background: #0f172a !important;
            color: #f8fafc !important;
            border-radius: 1rem !important;
            padding: 4px !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6) !important;
        }
        .leaflet-popup-tip {
            background: #0f172a !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="/js/track-data.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Initialize Map
            const mapElement = document.getElementById('samsara-leaflet-map');
            if (!mapElement || typeof L === 'undefined' || typeof SAMSARA_TRAIL_DATA === 'undefined') {
                return;
            }

            const centerLat = -7.3275;
            const centerLng = 107.8350;

            const samsaraMap = L.map('samsara-leaflet-map', {
                center: [centerLat, centerLng],
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: true
            });

            window.samsaraLeafletMap = samsaraMap;
            window.checkpointMarkers = {};

            const baseLayers = {
                satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS',
                    maxZoom: 19
                }),
                topo: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Topo Map',
                    maxZoom: 19
                }),
                dark: L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }),
                osm: L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                })
            };

            let currentLayerKey = 'satellite';
            baseLayers.satellite.addTo(samsaraMap);

            // Layer Switcher
            document.querySelectorAll('.map-layer-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const selected = btn.getAttribute('data-layer');
                    if (selected === currentLayerKey) return;

                    document.querySelectorAll('.map-layer-btn').forEach(b => {
                        b.classList.remove('active', 'bg-[#D97736]', 'text-white');
                        b.classList.add('bg-slate-800', 'text-slate-300');
                    });
                    btn.classList.add('active', 'bg-[#D97736]', 'text-white');
                    btn.classList.remove('bg-slate-800', 'text-slate-300');

                    samsaraMap.removeLayer(baseLayers[currentLayerKey]);

                    if (selected === 'dark') {
                        mapElement.classList.add('dark-mode-tiles');
                    } else {
                        mapElement.classList.remove('dark-mode-tiles');
                    }

                    baseLayers[selected].addTo(samsaraMap);
                    currentLayerKey = selected;
                });
            });

            // Draw GPS Polyline Track
            let trailLine = null;
            if (SAMSARA_TRAIL_DATA.gpsTrack && SAMSARA_TRAIL_DATA.gpsTrack.length > 0) {
                const latLngs = SAMSARA_TRAIL_DATA.gpsTrack.map(coord => [coord[0], coord[1]]);

                // Outer Glow Track
                L.polyline(latLngs, {
                    color: '#f59e0b',
                    weight: 6,
                    opacity: 0.5,
                    smoothFactor: 1
                }).addTo(samsaraMap);

                // Main Dashed Track Line
                trailLine = L.polyline(latLngs, {
                    color: '#10b981',
                    weight: 3.5,
                    opacity: 0.95,
                    dashArray: '6, 6',
                    smoothFactor: 1
                }).addTo(samsaraMap);

                samsaraMap.fitBounds(trailLine.getBounds(), { padding: [40, 40] });
            }

            // Place Interactive Checkpoint Markers
            if (SAMSARA_TRAIL_DATA.checkpoints) {
                SAMSARA_TRAIL_DATA.checkpoints.forEach(cp => {
                    const isPuncak = cp.id === 'puncak';
                    const isBC = cp.id === 'basecamp';

                    const iconHtml = `
                        <div style="
                            background: ${isPuncak ? 'linear-gradient(135deg, #f59e0b, #d97706)' : isBC ? 'linear-gradient(135deg, #38bdf8, #0284c7)' : 'linear-gradient(135deg, #10b981, #059669)'};
                            color: #ffffff;
                            border: 2px solid #ffffff;
                            border-radius: 50%;
                            width: 34px;
                            height: 34px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-family: 'Outfit', sans-serif;
                            font-weight: 800;
                            font-size: 10px;
                            box-shadow: 0 4px 14px rgba(0,0,0,0.65);
                            cursor: pointer;
                        ">
                            ${cp.posNumber}
                        </div>
                    `;

                    const customIcon = L.divIcon({
                        html: iconHtml,
                        className: 'samsara-marker',
                        iconSize: [34, 34],
                        iconAnchor: [17, 17]
                    });

                    const marker = L.marker([cp.lat, cp.lng], { icon: customIcon }).addTo(samsaraMap);

                    const popupContent = `
                        <div style="font-family: 'Plus Jakarta Sans', sans-serif; min-width: 230px; color: #f1f5f3; padding: 4px;">
                            <img src="${cp.image}" alt="${cp.name}" style="width: 100%; height: 110px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                            <div style="font-weight: 800; font-size: 14px; color: #fff; margin-bottom: 2px;">${cp.name}</div>
                            <div style="font-size: 11px; font-weight: 700; color: #f59e0b; margin-bottom: 4px;">Elevasi: ${cp.elevation} MDPL</div>
                            <p style="font-size: 11px; color: #94a3b8; line-height: 1.4; margin: 0 0 6px;">${cp.tagline}</p>
                            <div style="display: flex; justify-content: space-between; font-size: 10.5px; color: #34d399; font-weight: 600;">
                                <span>⏱ ${cp.estimatedTime}</span>
                                <span>${cp.campArea ? '⛺ Camp Area' : '🚫 No Camp'}</span>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent, { offset: [0, -10] });
                    window.checkpointMarkers[cp.id] = marker;
                });
            }

            // Quick Zoom Buttons
            document.querySelectorAll('.map-quick-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.map-quick-btn').forEach(b => {
                        b.classList.remove('active', 'bg-white/20', 'text-white');
                        b.classList.add('bg-slate-800', 'text-slate-300');
                    });
                    btn.classList.add('active', 'bg-white/20', 'text-white');
                    btn.classList.remove('bg-slate-800', 'text-slate-300');

                    const target = btn.getAttribute('data-pos');
                    if (target === 'all') {
                        if (trailLine) {
                            samsaraMap.fitBounds(trailLine.getBounds(), { padding: [40, 40] });
                        }
                    } else if (window.checkpointMarkers[target]) {
                        const m = window.checkpointMarkers[target];
                        samsaraMap.setView(m.getLatLng(), 15, { animate: true });
                        m.openPopup();
                    }
                });
            });

            // Reset Zoom Button
            const btnReset = document.getElementById('btn-reset-map');
            if (btnReset) {
                btnReset.addEventListener('click', () => {
                    if (trailLine) {
                        samsaraMap.fitBounds(trailLine.getBounds(), { padding: [40, 40] });
                    }
                });
            }

            // Global helper to focus checkpoint
            window.focusCheckpointOnMap = function(posId) {
                const mapSec = document.getElementById('peta-gps');
                if (mapSec) {
                    mapSec.scrollIntoView({ behavior: 'smooth' });
                }
                setTimeout(() => {
                    if (window.samsaraLeafletMap && window.checkpointMarkers[posId]) {
                        const m = window.checkpointMarkers[posId];
                        window.samsaraLeafletMap.setView(m.getLatLng(), 15, { animate: true });
                        m.openPopup();
                    }
                }, 400);
            };

            // Check if URL has ?pos= query parameter
            const urlParams = new URLSearchParams(window.location.search);
            const initialPos = urlParams.get('pos');
            if (initialPos && window.checkpointMarkers[initialPos]) {
                setTimeout(() => {
                    window.focusCheckpointOnMap(initialPos);
                }, 500);
            }

            // 2. Initialize Elevation Canvas Chart
            const canvas = document.getElementById('elevation-canvas');
            if (canvas) {
                function renderChart() {
                    const ctx = canvas.getContext('2d');
                    const dpr = window.devicePixelRatio || 1;
                    const rect = canvas.getBoundingClientRect();
                    if (rect.width === 0 || rect.height === 0) return;

                    canvas.width = rect.width * dpr;
                    canvas.height = rect.height * dpr;
                    ctx.scale(dpr, dpr);

                    const milestones = [
                        { name: 'BC Cigedug', pos: 'BC', elev: 1245, dist: 0 },
                        { name: 'Pos 1 Tanjakan EOD', pos: 'POS 1', elev: 1424, dist: 1.5 },
                        { name: 'Pos 2 Batu Gendrung', pos: 'POS 2', elev: 1682, dist: 2.8 },
                        { name: 'Jembatan Rimba', pos: 'JMB', elev: 1942, dist: 3.9 },
                        { name: 'Pos 3 Alam Rimba', pos: 'POS 3', elev: 2096, dist: 4.6 },
                        { name: 'Pos 4 Samudra Awan', pos: 'POS 4', elev: 2257, dist: 5.4 },
                        { name: 'Puncak Cikuray', pos: 'PUNCAK', elev: 2821, dist: 6.8 }
                    ];

                    const minElev = 1100;
                    const maxElev = 2950;
                    const width = rect.width;
                    const height = rect.height;
                    const paddingX = 45;
                    const paddingY = 32;

                    function getY(elev) {
                        return height - paddingY - ((elev - minElev) / (maxElev - minElev)) * (height - 2 * paddingY);
                    }

                    function getX(dist) {
                        const maxDist = milestones[milestones.length - 1].dist;
                        return paddingX + (dist / maxDist) * (width - 2 * paddingX);
                    }

                    ctx.clearRect(0, 0, width, height);

                    // Grid lines & labels
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
                    ctx.lineWidth = 1;
                    [1250, 1600, 2000, 2400, 2821].forEach(level => {
                        const y = getY(level);
                        ctx.beginPath();
                        ctx.moveTo(paddingX, y);
                        ctx.lineTo(width - paddingX, y);
                        ctx.stroke();

                        ctx.fillStyle = level === 2821 ? '#f59e0b' : '#64748b';
                        ctx.font = '10px "Plus Jakarta Sans", sans-serif';
                        ctx.fillText(`${level}m`, 8, y + 3);
                    });

                    // Fill gradient under curve
                    const gradient = ctx.createLinearGradient(0, getY(maxElev), 0, height);
                    gradient.addColorStop(0, 'rgba(245, 158, 11, 0.45)');
                    gradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.25)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

                    ctx.beginPath();
                    ctx.moveTo(getX(milestones[0].dist), height - paddingY);
                    ctx.lineTo(getX(milestones[0].dist), getY(milestones[0].elev));

                    for (let i = 1; i < milestones.length; i++) {
                        ctx.lineTo(getX(milestones[i].dist), getY(milestones[i].elev));
                    }
                    ctx.lineTo(getX(milestones[milestones.length - 1].dist), height - paddingY);
                    ctx.closePath();
                    ctx.fillStyle = gradient;
                    ctx.fill();

                    // Stroke line
                    ctx.beginPath();
                    ctx.moveTo(getX(milestones[0].dist), getY(milestones[0].elev));
                    for (let i = 1; i < milestones.length; i++) {
                        ctx.lineTo(getX(milestones[i].dist), getY(milestones[i].elev));
                    }
                    ctx.strokeStyle = '#f59e0b';
                    ctx.lineWidth = 3;
                    ctx.stroke();

                    // Nodes
                    milestones.forEach((m) => {
                        const cx = getX(m.dist);
                        const cy = getY(m.elev);

                        // Outer ring
                        ctx.beginPath();
                        ctx.arc(cx, cy, 7, 0, Math.PI * 2);
                        ctx.fillStyle = m.pos === 'PUNCAK' ? 'rgba(245, 158, 11, 0.3)' : 'rgba(16, 185, 129, 0.25)';
                        ctx.fill();

                        // Dot
                        ctx.beginPath();
                        ctx.arc(cx, cy, 4, 0, Math.PI * 2);
                        ctx.fillStyle = m.pos === 'PUNCAK' ? '#f59e0b' : '#10b981';
                        ctx.fill();
                        ctx.lineWidth = 1.5;
                        ctx.strokeStyle = '#ffffff';
                        ctx.stroke();

                        // Pos label
                        ctx.fillStyle = '#f3f5f4';
                        ctx.font = 'bold 9.5px "Outfit", sans-serif';
                        ctx.textAlign = 'center';
                        ctx.fillText(m.pos, cx, cy - 12);

                        // Elevation
                        ctx.fillStyle = '#94a3b8';
                        ctx.font = '8.5px "Plus Jakarta Sans", sans-serif';
                        ctx.fillText(`${m.elev}m`, cx, cy + 16);
                    });
                }

                renderChart();
                window.addEventListener('resize', renderChart);
            }
        });
    </script>
@endpush

</x-layouts.app>
