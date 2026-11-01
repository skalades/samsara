<div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-12 scroll-mt-24" id="peta-gps">
    <!-- Header Section -->
    <div class="p-6 sm:p-8 pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] flex items-center gap-1.5">
                    <i class="fas fa-satellite"></i> Navigasi Geospasial Interaktif
                </span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    836 Titik GPS Riil
                </span>
            </div>
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">
                Peta Satelit GPS & Profil Elevasi
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Eksplorasi kontur jalur resmi via Cigedug. Klik pin pos untuk melihat foto, elevasi, dan ketersediaan air.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="/Trek%201.kml" download class="touch-target px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold flex items-center gap-2 transition border border-slate-200 shadow-xs">
                <i class="fas fa-download text-[#D97736]"></i>
                <span>Unduh File KML GPS</span>
            </a>
        </div>
    </div>

    <!-- Technical Stats Ribbon -->
    <div class="grid grid-cols-2 sm:grid-cols-5 divide-x divide-y sm:divide-y-0 divide-slate-100 bg-slate-50/70 border-b border-slate-100 text-center text-xs">
        <div class="p-3 sm:p-4">
            <span class="text-[11px] text-slate-400 font-medium block">Puncak Tertinggi</span>
            <span class="font-heading font-extrabold text-base text-[#D97736] mt-0.5 block">2.821 MDPL</span>
        </div>
        <div class="p-3 sm:p-4">
            <span class="text-[11px] text-slate-400 font-medium block">Panjang Trek</span>
            <span class="font-heading font-extrabold text-base text-slate-800 mt-0.5 block">6.8 KM</span>
        </div>
        <div class="p-3 sm:p-4">
            <span class="text-[11px] text-slate-400 font-medium block">Elevasi Gain</span>
            <span class="font-heading font-extrabold text-base text-emerald-700 mt-0.5 block">+1.576 M</span>
        </div>
        <div class="p-3 sm:p-4">
            <span class="text-[11px] text-slate-400 font-medium block">Estimasi Waktu</span>
            <span class="font-heading font-extrabold text-base text-slate-800 mt-0.5 block">4.5 &ndash; 6 Jam</span>
        </div>
        <div class="p-3 sm:p-4 col-span-2 sm:col-span-1">
            <span class="text-[11px] text-slate-400 font-medium block">Tingkat Kesulitan</span>
            <span class="font-heading font-extrabold text-xs sm:text-sm text-amber-700 mt-1 inline-flex items-center gap-1">
                <i class="fas fa-mountain text-amber-500"></i> Menantang
            </span>
        </div>
    </div>

    <!-- Map Control Bar (Layers & Pos Quick Zoom) -->
    <div class="p-3 sm:p-4 bg-slate-900 text-white flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-800">
        <!-- Layer Switcher -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar text-xs">
            <span class="text-[10px] uppercase font-mono font-bold text-slate-400 mr-1.5 shrink-0 hidden sm:inline">Lapisan:</span>
            <button type="button" class="map-layer-btn active px-3 py-1.5 rounded-lg bg-[#D97736] text-white font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer" data-layer="satellite">
                <i class="fas fa-satellite text-[11px]"></i> Satelit HD
            </button>
            <button type="button" class="map-layer-btn px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer" data-layer="topo">
                <i class="fas fa-mountain text-[11px]"></i> Topografi
            </button>
            <button type="button" class="map-layer-btn px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer" data-layer="dark">
                <i class="fas fa-moon text-[11px]"></i> Mode Gelap
            </button>
        </div>

        <!-- Quick Focus Checkpoint Buttons -->
        <div class="flex items-center gap-1 overflow-x-auto no-scrollbar text-xs">
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
        </div>
    </div>

    <!-- Leaflet Map Container -->
    <div class="relative">
        <div id="samsara-leaflet-map" class="w-full h-[400px] sm:h-[500px] lg:h-[540px] bg-slate-950 z-0"></div>

        <!-- Legend Overlay Badge -->
        <div class="absolute bottom-4 left-4 z-10 bg-slate-950/85 backdrop-blur-md border border-white/15 p-3 rounded-2xl text-[11px] text-white shadow-xl space-y-1.5 hidden sm:block pointer-events-none">
            <div class="font-bold text-amber-300 text-xs mb-1 flex items-center gap-1.5">
                <i class="fas fa-layer-group"></i> Legenda Jalur SAMSARA
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-1 bg-amber-400 rounded-full inline-block"></span>
                <span>Trek GPS Riil Cigedug</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 border border-white inline-block"></span>
                <span>Pos Checkpoint Resmi</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-[#D97736] border border-white inline-block"></span>
                <span>Pos 4 & Puncak Cikuray</span>
            </div>
        </div>
    </div>

    <!-- Elevation Profile Canvas Section -->
    <div class="p-5 sm:p-7 bg-slate-950 text-white border-t border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-2 gap-2 text-xs border-b border-slate-800">
            <div class="flex items-center gap-2 font-bold text-slate-200">
                <i class="fas fa-chart-area text-amber-400 text-sm"></i>
                <span>Profil Elevasi: Basecamp (1.245 MDPL) &rarr; Puncak Cikuray (2.821 MDPL)</span>
            </div>
            <div class="text-[11px] text-amber-300 font-semibold flex items-center gap-3">
                <span><i class="fas fa-arrows-up-down mr-1"></i> Elevasi Gain: +1.576 M</span>
                <span><i class="fas fa-angle-right mr-1"></i> Sudut Rata-rata: 28°</span>
            </div>
        </div>

        <div class="w-full h-44 sm:h-52 relative mt-2">
            <canvas id="elevation-canvas" class="w-full h-full block"></canvas>
        </div>

        <div class="mt-3 flex flex-wrap items-center justify-between text-[11px] text-slate-400">
            <span>*Grafik dihitung berdasarkan pembacaan altimeter GPS koordinat resmi Basecamp SAMSARA.</span>
            <span class="text-emerald-400 font-semibold">Tanjakan terjal konstan &bull; Tanpa bonus datar</span>
        </div>
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
                scrollWheelZoom: false
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

            // Draw GPS Polyline
            if (SAMSARA_TRAIL_DATA.gpsTrack && SAMSARA_TRAIL_DATA.gpsTrack.length > 0) {
                const latLngs = SAMSARA_TRAIL_DATA.gpsTrack.map(coord => [coord[0], coord[1]]);

                // Glow track line
                L.polyline(latLngs, {
                    color: '#f59e0b',
                    weight: 6,
                    opacity: 0.5,
                    smoothFactor: 1
                }).addTo(samsaraMap);

                // Main track line
                const trailLine = L.polyline(latLngs, {
                    color: '#10b981',
                    weight: 3.5,
                    opacity: 0.95,
                    dashArray: '6, 6',
                    smoothFactor: 1
                }).addTo(samsaraMap);

                samsaraMap.fitBounds(trailLine.getBounds(), { padding: [40, 40] });
            }

            // Place Markers
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
                            width: 32px;
                            height: 32px;
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
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    });

                    const marker = L.marker([cp.lat, cp.lng], { icon: customIcon }).addTo(samsaraMap);

                    const popupContent = `
                        <div style="font-family: 'Plus Jakarta Sans', sans-serif; min-width: 220px; color: #f1f5f3; padding: 4px;">
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
                        if (SAMSARA_TRAIL_DATA.gpsTrack) {
                            const latLngs = SAMSARA_TRAIL_DATA.gpsTrack.map(coord => [coord[0], coord[1]]);
                            samsaraMap.fitBounds(latLngs, { padding: [40, 40] });
                        }
                    } else if (window.checkpointMarkers[target]) {
                        const m = window.checkpointMarkers[target];
                        samsaraMap.setView(m.getLatLng(), 15, { animate: true });
                        m.openPopup();
                    }
                });
            });

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
