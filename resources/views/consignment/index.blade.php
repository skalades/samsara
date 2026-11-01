<x-layouts.admin title="Konsinyasi & Kemitraan UMKM Cigedug">
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                    <i class="fas fa-handshake"></i> Kemitraan Ekonomi Warga Lokal
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">Konsinyasi UMKM Cigedug</h1>
                <p class="text-xs sm:text-sm text-slate-500">Pemberdayaan warga sekitar basecamp dengan pemisahan akuntansi dan bagi hasil otomatis.</p>
            </div>
        </div>

        <!-- MITRA CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($mitras as $mitra)
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg font-bold">
                                <i class="fas fa-user-tag"></i>
                            </span>
                            <x-ui.badge color="amber" size="sm">Mitra Aktif</x-ui.badge>
                        </div>

                        <h3 class="font-heading font-bold text-base text-slate-900">{{ $mitra->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5"><i class="fas fa-map-marker-alt text-amber-600 mr-1"></i> {{ $mitra->village_address }}</p>
                        <p class="text-xs text-slate-500 mt-1"><i class="fab fa-whatsapp text-emerald-600 mr-1"></i> {{ $mitra->phone_wa }}</p>

                        <!-- Products List by this Mitra -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-2">Produk Titip Jual ({{ $mitra->products->count() }}):</span>
                            <div class="space-y-1.5 text-xs">
                                @foreach ($mitra->products as $p)
                                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/70 flex justify-between items-center">
                                        <span class="font-medium text-slate-800 line-clamp-1">{{ $p->name }}</span>
                                        <div class="text-right whitespace-nowrap pl-2">
                                            <div class="font-bold text-[#D97736]">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-slate-400">Setor: Rp {{ number_format($p->cost_price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Generate Settlement Modal / Form -->
                    <div class="mt-5 pt-3 border-t border-slate-100">
                        <form action="{{ route('consignment.settlement') }}" method="POST">
                            @csrf
                            <input type="hidden" name="mitra_id" value="{{ $mitra->id }}">
                            <input type="hidden" name="period_start" value="{{ today()->startOfMonth()->toDateString() }}">
                            <input type="hidden" name="period_end" value="{{ today()->toDateString() }}">
                            <button type="submit" class="touch-target w-full rounded-xl bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-900 border border-amber-300 text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
                                <i class="fas fa-file-invoice-dollar text-[#D97736]"></i> Generate Settlement Periode Ini
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- HISTORICAL SETTLEMENTS -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-5 sm:p-6">
            <h3 class="font-heading font-bold text-base text-slate-900 mb-3">Daftar Settlement & Rekonsiliasi Terakhir</h3>
            <div class="rounded-2xl border border-slate-200 overflow-hidden divide-y divide-slate-100 text-xs">
                @forelse ($recentSettlements as $st)
                    <div class="p-4 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-slate-900">{{ $st->settlement_code }}</span>
                                @if ($st->status === 'PAID')
                                    <x-ui.badge color="emerald" size="sm">Lunas</x-ui.badge>
                                @else
                                    <x-ui.badge color="amber" size="sm">Draft (Pending)</x-ui.badge>
                                @endif
                            </div>
                            <div class="font-bold text-sm text-slate-800 mt-1">{{ $st->mitra->name }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Periode: {{ $st->period_start->format('d M') }} - {{ $st->period_end->format('d M Y') }} &bull; {{ $st->total_items_sold }} Barang Terjual
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <span class="text-[11px] text-slate-500">Hak Pembayaran Mitra:</span>
                                <div class="font-heading font-extrabold text-sm text-emerald-800">
                                    Rp {{ number_format($st->mitra_earnings_amount, 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-slate-400">Margin Kas SAMSARA: Rp {{ number_format($st->samsara_margin_amount, 0, ',', '.') }}</span>
                            </div>

                            <a href="{{ route('consignment.settlement.show', $st->id) }}" class="touch-target px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-800 font-bold border border-emerald-200 transition flex items-center gap-1.5">
                                <i class="fab fa-whatsapp text-emerald-500"></i> Buka Slip WA
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">Belum ada settlement konsinyasi yang diterbitkan.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
