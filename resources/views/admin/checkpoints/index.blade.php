<x-layouts.admin title="Kelola Dokumentasi Foto Checkpoint | SAMSARA Admin">
    <div class="space-y-6">
        <!-- TOP ADAPTIVE HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                    <i class="fas fa-camera"></i> Visual Terrain Documentation
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                    Dokumentasi Foto Lapangan Checkpoint
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Kelola foto visual resmi tiap pos pendakian Cikuray via Cigedug untuk panduan visual pendaki di public portal & lightbox.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}#pos-explorer" target="_blank" class="touch-target px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-xs border border-slate-200 shadow-xs transition flex items-center gap-2">
                    <i class="fas fa-arrow-up-right-from-square text-[#D97736]"></i>
                    <span>Tinjau Public Portal</span>
                </a>

                <a href="{{ route('admin.index') }}" class="touch-target px-4 py-2.5 rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Command Center</span>
                </a>
            </div>
        </div>

        <!-- FLASH NOTIFICATION -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- CHECKPOINT CARDS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($checkpoints as $cp)
                @php
                    $primaryPhoto = $cp->primaryPhoto();
                    $photosCount = $cp->photos->count();
                    $displayImg = $primaryPhoto ? $primaryPhoto->resolved_url : ($cp->default_image ?: '/assets/hero-cikuray.jpg');
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between hover:border-slate-300 transition duration-300 group">
                    <!-- Checkpoint Header Image & Badge -->
                    <div>
                        <div class="relative h-48 bg-slate-900 overflow-hidden">
                            <img src="{{ $displayImg }}" alt="{{ $cp->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/20"></div>

                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-[#D97736] text-white shadow-xs">
                                    {{ $cp->number }}
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-950/70 text-amber-300 border border-white/10 backdrop-blur-xs">
                                    {{ $cp->elevation }}
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $photosCount > 0 ? 'bg-emerald-600 text-white' : 'bg-slate-700 text-slate-300' }} shadow-xs">
                                    <i class="fas fa-images mr-1"></i> {{ $photosCount }} Foto
                                </span>
                            </div>

                            <!-- Bottom Tagline on Image -->
                            <div class="absolute bottom-3 left-3 right-3 text-white">
                                <div class="text-[11px] font-mono text-emerald-300 font-semibold">{{ $cp->distance }}</div>
                                <h3 class="font-heading font-extrabold text-lg text-white leading-tight truncate">
                                    {{ $cp->title }}
                                </h3>
                            </div>
                        </div>

                        <!-- Technical Specs Bar -->
                        <div class="p-5 space-y-3">
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $cp->tagline ?? $cp->description }}
                            </p>

                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-2">
                                    <i class="fas {{ $cp->water_available ? 'fa-droplet text-sky-500' : 'fa-droplet-slash text-rose-400' }}"></i>
                                    <span class="truncate font-semibold text-slate-700">{{ $cp->water_available ? 'Air Ada' : 'Kering' }}</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-2">
                                    <i class="fas fa-campground text-emerald-600"></i>
                                    <span class="truncate font-semibold text-slate-700">{{ $cp->tent_capacity }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button Footer -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('admin.checkpoints.show', $cp->id) }}"
                           class="touch-target w-full rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-heading font-bold text-xs py-3 px-4 transition flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-images text-[#D97736]"></i>
                            <span>Kelola Foto & Upload ({{ $photosCount }})</span>
                            <i class="fas fa-arrow-right text-[10px] ml-auto"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
