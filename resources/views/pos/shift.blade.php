<x-layouts.admin title="Rekonsiliasi Shift Kasir">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Kasir POS
            </a>
            <h1 class="text-2xl font-heading font-extrabold text-slate-900">Rekonsiliasi & Tutup Shift Kasir</h1>
            <p class="text-xs text-slate-500">Pencocokan saldo fisik laci kasir dengan catatan sistem SAMSARA SIMS.</p>
        </div>

        @if ($activeShift)
            <!-- ACTIVE SHIFT RECONCILIATION CARD -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            SHIFT SEDANG AKTIF #{{ $activeShift->shift_number }}
                        </span>
                        <div class="text-xs text-slate-500 mt-2">
                            Dibuka pada: <span class="font-bold text-slate-800">{{ $activeShift->opened_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-xs text-slate-400">Modal Kas Awal:</span>
                        <div class="font-heading font-bold text-base text-slate-800">
                            Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- FORM TUTUP SHIFT -->
                <form action="{{ route('pos.shift.close', $activeShift->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jumlah Uang Tunai Fisik di Laci Kasir (Hasil Hitung Manual)
                        </label>
                        <x-ui.currency-input
                            name="closing_cash_physical"
                            :value="0"
                            placeholder="0"
                            required
                            class="text-base font-bold text-slate-900 py-2"
                        />
                        <p class="text-[11px] text-slate-500 mt-1">Sistem akan otomatis menghitung selisih (Lebih/Kurang/Pas) terhadap mutasi kas masuk.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Kasir / Keterangan Shift</label>
                        <textarea name="notes" rows="2" placeholder="Catatan operasional, uang receh, atau kendala loket..."
                            class="w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900"></textarea>
                    </div>

                    <button type="submit" class="touch-target w-full rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i> Selesaikan & Tutup Shift Kasir
                    </button>
                </form>
            </div>
        @else
            <!-- OPEN NEW SHIFT CARD -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 mx-auto flex items-center justify-center text-xl mb-3">
                    <i class="fas fa-door-open"></i>
                </div>
                <h3 class="font-heading font-bold text-base text-slate-900">Tidak Ada Shift Kasir Aktif</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto mb-4">
                    Masukkan modal uang tunai awal di laci untuk membuka shift kasir baru loket basecamp.
                </p>

                <form action="{{ route('pos.shift.open') }}" method="POST" class="max-w-xs mx-auto space-y-3">
                    @csrf
                    <div>
                        <x-ui.currency-input
                            name="opening_cash"
                            :value="500000"
                            placeholder="500.000"
                            align="center"
                            required
                            class="text-sm font-bold text-slate-900 text-center py-2"
                        />
                    </div>
                    <button type="submit" class="touch-target w-full rounded-xl bg-[#1E3A2B] text-white font-bold text-xs shadow transition">
                        Buka Shift Baru
                    </button>
                </form>
            </div>
        @endif

        <!-- HISTORICAL SHIFTS TABLE -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-5">
            <h3 class="font-heading font-bold text-sm text-slate-900 mb-3">Riwayat Tutup Shift Terakhir</h3>
            <div class="rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 text-xs">
                @foreach ($recentShifts as $s)
                    <div class="p-3 bg-white flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <div class="font-bold text-slate-900">Shift #{{ $s->shift_number }} &bull; {{ $s->cashier->name }}</div>
                            <div class="text-[11px] text-slate-400">
                                {{ $s->opened_at->format('d/m H:i') }} - {{ $s->closed_at ? $s->closed_at->format('H:i') : 'Aktif' }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-mono font-bold text-slate-900">
                                Fisik: Rp {{ number_format($s->closing_cash_physical, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] {{ $s->cash_difference == 0 ? 'text-emerald-700' : ($s->cash_difference > 0 ? 'text-sky-700' : 'text-rose-700 font-bold') }}">
                                Selisih: {{ $s->cash_difference >= 0 ? '+' : '' }}Rp {{ number_format($s->cash_difference, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
