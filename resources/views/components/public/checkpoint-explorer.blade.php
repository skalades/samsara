@props(['checkpoints' => null, 'defaultPos' => 'pos4'])

@php
    if (! $checkpoints || (method_exists($checkpoints, 'isEmpty') && $checkpoints->isEmpty()) || (is_array($checkpoints) && empty($checkpoints))) {
        $checkpoints = \App\Modules\Simaksi\Models\Checkpoint::with('photos')->where('is_active', true)->orderBy('order')->get();
    }

    $formattedCheckpoints = collect($checkpoints)->map(function ($cp) {
        if ($cp instanceof \App\Modules\Simaksi\Models\Checkpoint) {
            $primary = $cp->primaryPhoto();
            $primaryUrl = $primary ? $primary->resolved_url : ($cp->default_image ?: '/assets/hero-cikuray.jpg');

            return [
                'id' => $cp->code,
                'number' => $cp->number,
                'title' => $cp->title,
                'elevation' => $cp->elevation,
                'distance' => $cp->distance,
                'time' => $cp->time_estimate,
                'water' => $cp->water_info,
                'waterAvailable' => (bool) $cp->water_available,
                'signal' => $cp->signal_info,
                'tentCapacity' => $cp->tent_capacity,
                'terrain' => $cp->terrain,
                'image' => $primaryUrl,
                'tagline' => $cp->tagline,
                'desc' => $cp->description,
                'photos' => $cp->photos->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'desc' => $p->description,
                    'url' => $p->resolved_url,
                    'is_primary' => (bool) $p->is_primary,
                ])->values()->all(),
            ];
        }

        if (is_array($cp)) {
            return [
                'id' => $cp['id'] ?? '',
                'number' => $cp['number'] ?? '',
                'title' => $cp['name'] ?? ($cp['title'] ?? ''),
                'elevation' => is_numeric($cp['elevation'] ?? '') ? $cp['elevation'] . ' MDPL' : ($cp['elevation'] ?? ''),
                'distance' => $cp['distance'] ?? '',
                'time' => $cp['time'] ?? '',
                'water' => $cp['water'] ?? '',
                'waterAvailable' => (bool) ($cp['waterAvailable'] ?? false),
                'signal' => $cp['signal'] ?? '',
                'tentCapacity' => $cp['tentCapacity'] ?? '',
                'terrain' => $cp['terrain'] ?? '',
                'image' => $cp['image'] ?? '/assets/hero-cikuray.jpg',
                'tagline' => $cp['tagline'] ?? '',
                'desc' => $cp['desc'] ?? '',
                'photos' => $cp['photos'] ?? [],
            ];
        }

        return (array) $cp;
    })->values()->all();

    $jsonCheckpoints = json_encode($formattedCheckpoints, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
@endphp

<!-- Checkpoint JSON Data Island -->
<script type="application/json" id="checkpoint-explorer-data">
    {!! $jsonCheckpoints !!}
</script>

<div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-9 shadow-sm mb-12 scroll-mt-24" id="pos-explorer"
     x-data="checkpointExplorerComponent('{{ $defaultPos }}')">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] block mb-1">
                <i class="fas fa-mountain-sun"></i> Panduan Teknis Jalur Cikuray via Cigedug
            </span>
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">
                Karakteristik & Profil Tiap Checkpoint
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pilih pos di bawah ini untuk melihat detail foto lapangan, elevasi, ketersediaan air, dan tantangan medan.
            </p>
        </div>

        <div class="hidden md:flex items-center gap-2 text-xs font-semibold text-emerald-800 bg-emerald-50 px-3.5 py-2 rounded-2xl border border-emerald-200">
            <i class="fas fa-camera text-[#D97736]"></i>
            <span>Visual Terverifikasi Petugas Lapangan</span>
        </div>
    </div>

    <!-- Interactive Pos Tab Pills (Horizontal Stepper) -->
    <div class="mt-6 mb-8 flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
        <template x-for="cp in checkpoints" :key="cp.id">
            <button type="button" @click="selectPos(cp.id)"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 shrink-0 whitespace-nowrap border cursor-pointer min-h-[44px]"
                    :class="activePos === cp.id
                        ? 'bg-[#1E3A2B] text-white border-[#1E3A2B] shadow-md ring-2 ring-emerald-500/20'
                        : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200/90'">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shrink-0"
                      :class="activePos === cp.id ? 'bg-[#D97736] text-white' : 'bg-slate-200 text-slate-700'"
                      x-text="cp.number"></span>
                <span x-text="cp.number === 'BC' ? 'Basecamp' : (cp.number === 'PUNCAK' ? 'Puncak' : cp.number)"></span>
                <span class="text-[10px] opacity-75 font-mono" x-text="'(' + cp.elevation + ')'"></span>
            </button>
        </template>
    </div>

    <!-- Active Checkpoint Detailed Visual Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 bg-slate-50/70 p-5 sm:p-7 rounded-3xl border border-slate-200/90 transition-all duration-300">
        <!-- Photo with Lightbox Zoom Trigger & Gallery Strip -->
        <div class="lg:col-span-5 flex flex-col justify-between">
            <div class="relative group">
                <div class="relative h-64 sm:h-80 rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 shadow-sm">
                    <img :src="currentImage" :alt="currentTitle"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/20"></div>

                    <!-- Badge Pills on Photo -->
                    <div class="absolute top-3 left-3 flex items-center gap-1.5">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-[#D97736] text-white shadow-xs tracking-wider" x-text="current.number"></span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-950/70 text-amber-300 backdrop-blur-xs border border-white/10" x-text="current.elevation"></span>
                    </div>

                    <!-- Zoom Fullscreen Action (Alpine Lightbox Integration) -->
                    <button type="button"
                            @click="$dispatch('open-lightbox', { src: currentImage, title: currentTitle, desc: currentDesc })"
                            class="absolute bottom-3 right-3 px-3 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-900 text-white text-xs font-semibold backdrop-blur-md border border-white/20 transition flex items-center gap-1.5 shadow-md cursor-pointer">
                        <i class="fas fa-expand text-[#D97736]"></i> Perbesar Foto
                    </button>
                </div>
            </div>

            <!-- Visual Photo Thumbnails Strip (Admin Uploaded Documentation) -->
            <template x-if="current.photos && current.photos.length > 0">
                <div class="mt-4 pt-3 border-t border-slate-200">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-2">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-images text-[#D97736]"></i>
                            <span>Dokumentasi Pos (<span x-text="current.photos.length"></span> Foto)</span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-normal">Klik untuk lihat / perbesar</span>
                    </div>

                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                        <template x-for="p in current.photos" :key="p.id">
                            <div class="relative shrink-0 cursor-pointer group/thumb"
                                 @click="selectPhoto(p); $dispatch('open-lightbox', { src: p.url, title: p.title, desc: p.desc || current.desc })">
                                <img :src="p.url" :alt="p.title"
                                     class="w-16 h-12 sm:w-20 sm:h-14 rounded-xl object-cover border-2 transition duration-200"
                                     :class="currentImage === p.url ? 'border-[#D97736] ring-2 ring-[#D97736]/30' : 'border-white shadow-xs group-hover/thumb:border-emerald-500'">
                                <div class="absolute inset-0 bg-black/0 group-hover/thumb:bg-black/30 rounded-xl transition flex items-center justify-center">
                                    <i class="fas fa-expand text-white text-[10px] opacity-0 group-hover/thumb:opacity-100 transition"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Info & Technical Specs -->
        <div class="lg:col-span-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs font-bold text-[#D97736] uppercase tracking-wider font-mono" x-text="current.distance"></span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500" x-text="current.time"></span>
                </div>

                <h3 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900" x-text="current.title"></h3>
                <p class="text-xs sm:text-sm font-semibold text-emerald-800 mt-1" x-text="current.tagline"></p>
                <p class="text-xs sm:text-sm text-slate-600 mt-3 leading-relaxed" x-text="current.desc"></p>

                <!-- Technical Specs Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-5">
                    <div class="p-3 rounded-2xl bg-white border border-slate-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Kondisi Air</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <i class="fas" :class="current.waterAvailable ? 'fa-droplet text-sky-500' : 'fa-droplet-slash text-rose-400'"></i>
                            <span class="text-xs font-bold" :class="current.waterAvailable ? 'text-sky-800' : 'text-rose-700'" x-text="current.waterAvailable ? 'Tersedia' : 'Kering'"></span>
                        </div>
                    </div>

                    <div class="p-3 rounded-2xl bg-white border border-slate-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Kapasitas Tenda</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <i class="fas fa-campground text-emerald-600"></i>
                            <span class="text-xs font-bold text-slate-800 truncate" x-text="current.tentCapacity"></span>
                        </div>
                    </div>

                    <div class="p-3 rounded-2xl bg-white border border-slate-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Sinyal HP</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <i class="fas fa-signal text-amber-500"></i>
                            <span class="text-xs font-bold text-slate-800 truncate" x-text="current.signal"></span>
                        </div>
                    </div>

                    <div class="p-3 rounded-2xl bg-white border border-slate-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Karakter Medan</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <i class="fas fa-person-hiking text-[#D97736]"></i>
                            <span class="text-xs font-bold text-slate-800 truncate" x-text="current.terrain"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Focus on Satellite Map Button -->
            <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500 flex items-center gap-1.5">
                    <i class="fas fa-satellite text-[#D97736]"></i>
                    <span>Tersinkronisasi dengan rute GPS dan peta satelit interaktif terpisah.</span>
                </div>

                <button type="button" @click="typeof window.focusCheckpointOnMap === 'function' ? window.focusCheckpointOnMap(current.id) : window.location.href = '{{ route('trail.map') }}?pos=' + current.id"
                        class="touch-target px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-2 shadow-xs active:scale-95 cursor-pointer">
                    <i class="fas fa-map-location-dot text-amber-400"></i>
                    <span>Buka & Fokus di Peta Satelit</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof window.checkpointExplorerComponent !== 'function') {
        window.checkpointExplorerComponent = function(defaultPos = 'pos4') {
            let data = [];
            try {
                const el = document.getElementById('checkpoint-explorer-data');
                if (el && el.textContent) {
                    data = JSON.parse(el.textContent.trim());
                }
            } catch (err) {
                console.error('[CheckpointExplorer] Gagal mem-parse data JSON checkpoint:', err);
                data = [];
            }

            const fallbackCp = {
                id: '',
                number: '-',
                title: 'Data Checkpoint Tidak Tersedia',
                elevation: '-',
                distance: '-',
                time: '-',
                water: '-',
                waterAvailable: false,
                signal: '-',
                tentCapacity: '-',
                terrain: '-',
                image: '/assets/hero-cikuray.jpg',
                tagline: '',
                desc: 'Informasi teknis pos sedang disinkronkan oleh tim lapangan.',
                photos: []
            };

            return {
                activePos: defaultPos || 'pos4',
                checkpoints: data,
                activePhotoUrl: null,
                activePhotoTitle: null,
                activePhotoDesc: null,

                get current() {
                    if (!this.checkpoints || this.checkpoints.length === 0) {
                        return fallbackCp;
                    }
                    return this.checkpoints.find(c => c.id === this.activePos) || this.checkpoints[0] || fallbackCp;
                },

                get currentImage() {
                    return this.activePhotoUrl || this.current.image || '/assets/hero-cikuray.jpg';
                },

                get currentTitle() {
                    return this.activePhotoTitle || this.current.title || '';
                },

                get currentDesc() {
                    return this.activePhotoDesc || this.current.desc || '';
                },

                selectPos(id) {
                    this.activePos = id;
                    this.activePhotoUrl = null;
                    this.activePhotoTitle = null;
                    this.activePhotoDesc = null;
                },

                selectPhoto(photo) {
                    if (!photo) return;
                    this.activePhotoUrl = photo.url;
                    this.activePhotoTitle = photo.title;
                    this.activePhotoDesc = photo.desc;
                }
            };
        };
    }
</script>
