<x-layouts.admin title="Live Pos Tracker & SAR">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Mountain Pulse
                </a>
                <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900">Live Pos Tracker SAMSARA</h1>
                <p class="text-xs sm:text-sm text-slate-500">Pemantauan elevasi dan sebaran rombongan pendaki dari Basecamp hingga Puncak 2.821 MDPL.</p>
            </div>
        </div>

        <!-- TRAIL ELEVATION TRACKER (Vertical Stacking / Elevation Flow) -->
        <div class="space-y-4">
            @php
                $posMeta = [
                    'Puncak Cikuray' => ['alt' => '2.821 MDPL', 'icon' => 'fa-flag', 'bg' => 'bg-emerald-950 text-white'],
                    'Pos 4 (Samudra Awan)' => ['alt' => '2.500 MDPL', 'icon' => 'fa-campground', 'bg' => 'bg-emerald-800 text-white'],
                    'Pos 3 (Alam Rimba)' => ['alt' => '2.200 MDPL', 'icon' => 'fa-tree', 'bg' => 'bg-emerald-700 text-white'],
                    'Pos 2 (Batu Gendrung)' => ['alt' => '1.850 MDPL', 'icon' => 'fa-cubes-stacked', 'bg' => 'bg-emerald-600 text-white'],
                    'Pos 1 (Tanjakan EOD)' => ['alt' => '1.500 MDPL', 'icon' => 'fa-person-hiking', 'bg' => 'bg-emerald-500 text-white'],
                    'Basecamp' => ['alt' => '1.300 MDPL', 'icon' => 'fa-house-chimney-window', 'bg' => 'bg-slate-700 text-white'],
                ];
            @endphp

            @foreach ($posDistribution as $posName => $groups)
                @php
                    $meta = $posMeta[$posName] ?? ['alt' => 'Jalur', 'icon' => 'fa-map-pin', 'bg' => 'bg-slate-700 text-white'];
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl {{ $meta['bg'] }} flex items-center justify-center text-lg shadow">
                            <i class="fas {{ $meta['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-heading font-extrabold text-base text-slate-900">{{ $posName }}</h3>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-[#D97736] text-white">{{ $meta['alt'] }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ count($groups) }} Rombongan &bull; {{ collect($groups)->sum('total_hikers') }} Pendaki berada di area ini.
                            </p>
                        </div>
                    </div>

                    <!-- Groups List -->
                    <div class="flex flex-wrap items-center gap-2">
                        @forelse ($groups as $group)
                            <div class="p-2.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-2.5 text-xs">
                                <div>
                                    <div class="font-bold text-slate-800">{{ $group->leader_name }} ({{ $group->total_hikers }} Org)</div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $group->booking_code }} &bull; {{ $group->tent_color_details ?: 'Tenda Standar' }}
                                    </div>
                                </div>

                                <a href="{{ route('admin.sar-export', $group->id) }}" class="touch-target px-2.5 py-1 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-800 border border-rose-200 text-[10px] font-bold flex items-center gap-1 transition">
                                    <i class="fas fa-file-pdf"></i> SAR 1-Click
                                </a>
                            </div>
                        @empty
                            <span class="text-xs text-slate-400 italic">Tidak ada rombongan aktif di titik ini.</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
