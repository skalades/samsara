<x-layouts.admin title="Rental & QC Alat Outdoor">
    <div x-data="{
        selectedOrder: null,
        showQcModal: false,
        openQc(order) {
            this.selectedOrder = order;
            this.showQcModal = true;
        }
    }">
        <!-- HEADER -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                    <i class="fas fa-campground"></i> Manajemen Aset & Quality Control
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">Siklus Aset Sewa Basecamp</h1>
                <p class="text-xs sm:text-sm text-slate-500">Pantau ketersediaan tenda, kompor, sleeping bag, dan riwayat kesehatan alat.</p>
            </div>
        </div>

        <!-- KANBAN BOARD (4 COLUMNS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- 1. TERSEDIA DI RAK -->
            <div class="bg-slate-100/70 rounded-3xl p-4 border border-slate-200/90 flex flex-col">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <h3 class="font-heading font-bold text-xs text-slate-900 uppercase">Tersedia di Rak</h3>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-white text-emerald-800 shadow-2xs">
                        {{ count($kanban['TERSEDIA']) }}
                    </span>
                </div>

                <div class="space-y-2.5 flex-1 max-h-[500px] overflow-y-auto pr-1">
                    @forelse ($kanban['TERSEDIA'] as $asset)
                        <div class="p-3.5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[10px] font-mono font-bold text-[#D97736]">{{ $asset->asset_code }}</span>
                                    <div class="font-bold text-xs text-slate-900">{{ $asset->name }}</div>
                                </div>
                                <span class="text-[10px] text-slate-400">Sewa: {{ $asset->times_rented_count }}x</span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                                <span class="text-slate-500">Tarif/Hari:</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($asset->rental_rate_per_day, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">Kosong</div>
                    @endforelse
                </div>
            </div>

            <!-- 2. SEDANG DIPAKAI PENDAKI -->
            <div class="bg-slate-100/70 rounded-3xl p-4 border border-slate-200/90 flex flex-col">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                        <h3 class="font-heading font-bold text-xs text-slate-900 uppercase">Dipakai Pendaki</h3>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-white text-sky-800 shadow-2xs">
                        {{ count($kanban['DISEWA']) }}
                    </span>
                </div>

                <div class="space-y-2.5 flex-1 max-h-[500px] overflow-y-auto pr-1">
                    @forelse ($kanban['DISEWA'] as $asset)
                        <div class="p-3.5 bg-white rounded-2xl border border-sky-200 shadow-2xs space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[10px] font-mono font-bold text-sky-700">{{ $asset->asset_code }}</span>
                                    <div class="font-bold text-xs text-slate-900">{{ $asset->name }}</div>
                                </div>
                                <span class="text-[10px] font-bold text-sky-600 bg-sky-50 px-1.5 py-0.5 rounded">Di Jalur</span>
                            </div>

                            <form action="{{ route('rental.update-status', $asset->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="PERLU_QC_CUCI">
                                <button type="submit" class="touch-target w-full rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 text-[11px] font-bold py-1.5 border border-amber-200 transition">
                                    <i class="fas fa-undo mr-1"></i> Tandai Sudah Kembali
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">Tidak ada aset di jalur</div>
                    @endforelse
                </div>
            </div>

            <!-- 3. PERLU QC & CUCI -->
            <div class="bg-slate-100/70 rounded-3xl p-4 border border-slate-200/90 flex flex-col">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <h3 class="font-heading font-bold text-xs text-slate-900 uppercase">Perlu Cuci / QC</h3>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-white text-amber-800 shadow-2xs">
                        {{ count($kanban['PERLU_QC_CUCI']) }}
                    </span>
                </div>

                <div class="space-y-2.5 flex-1 max-h-[500px] overflow-y-auto pr-1">
                    @forelse ($kanban['PERLU_QC_CUCI'] as $asset)
                        <div class="p-3.5 bg-white rounded-2xl border border-amber-200 shadow-2xs space-y-2">
                            <div>
                                <span class="text-[10px] font-mono font-bold text-amber-700">{{ $asset->asset_code }}</span>
                                <div class="font-bold text-xs text-slate-900">{{ $asset->name }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Catatan: {{ $asset->condition_notes ?? 'Selesai pendakian' }}</div>
                            </div>

                            <form action="{{ route('rental.update-status', $asset->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="TERSEDIA">
                                <button type="submit" class="touch-target w-full rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-800 text-[11px] font-bold py-1.5 border border-emerald-200 transition">
                                    <i class="fas fa-check-double mr-1"></i> Selesai Cuci / Lolos QC
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">Bersih semua</div>
                    @endforelse
                </div>
            </div>

            <!-- 4. DALAM PERBAIKAN -->
            <div class="bg-slate-100/70 rounded-3xl p-4 border border-slate-200/90 flex flex-col">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <h3 class="font-heading font-bold text-xs text-slate-900 uppercase">Dalam Perbaikan</h3>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-white text-rose-800 shadow-2xs">
                        {{ count($kanban['DALAM_PERBAIKAN']) }}
                    </span>
                </div>

                <div class="space-y-2.5 flex-1 max-h-[500px] overflow-y-auto pr-1">
                    @forelse ($kanban['DALAM_PERBAIKAN'] as $asset)
                        <div class="p-3.5 bg-white rounded-2xl border border-rose-200 shadow-2xs space-y-2">
                            <div>
                                <span class="text-[10px] font-mono font-bold text-rose-700">{{ $asset->asset_code }}</span>
                                <div class="font-bold text-xs text-slate-900">{{ $asset->name }}</div>
                                <div class="text-[10px] text-rose-600 font-medium mt-0.5">Kerusakan: {{ $asset->condition_notes ?? 'Frame bengkok / sobek' }}</div>
                            </div>

                            <form action="{{ route('rental.update-status', $asset->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="TERSEDIA">
                                <button type="submit" class="touch-target w-full rounded-xl bg-slate-50 hover:bg-emerald-600 hover:text-white text-slate-700 text-[11px] font-bold py-1.5 border border-slate-200 transition">
                                    <i class="fas fa-wrench mr-1"></i> Selesai Diperbaiki
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">Tidak ada alat rusak</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
