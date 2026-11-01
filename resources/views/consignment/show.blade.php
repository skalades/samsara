<x-layouts.admin title="Slip Settlement {{ $settlement->settlement_code }}">
    <div class="max-w-lg mx-auto">
        <div class="mb-4">
            <a href="{{ route('consignment.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Konsinyasi
            </a>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-md p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">SLIP KONSINYASI</span>
                    <h2 class="font-heading font-extrabold text-base text-slate-900 mt-1">{{ $settlement->settlement_code }}</h2>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400">Status Pembayaran:</span>
                    <div class="font-bold {{ $settlement->status === 'PAID' ? 'text-emerald-700' : 'text-amber-600' }}">
                        {{ $settlement->status === 'PAID' ? 'LUNAS DITRANSFER' : 'MENUNGGU TRANSFER (DRAFT)' }}
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Mitra:</span>
                    <span class="font-bold text-slate-900">{{ $settlement->mitra->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Alamat:</span>
                    <span class="text-slate-800">{{ $settlement->mitra->village_address }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Rekening Transfer:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $settlement->mitra->bank_info }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Periode Penjualan:</span>
                    <span class="font-semibold text-slate-800">{{ $settlement->period_start->format('d M') }} s/d {{ $settlement->period_end->format('d M Y') }}</span>
                </div>
            </div>

            <!-- Figures -->
            <div class="p-4 rounded-2xl bg-emerald-950 text-white space-y-2 text-xs">
                <div class="flex justify-between text-emerald-200">
                    <span>Total Barang Terjual:</span>
                    <span class="font-bold text-white">{{ $settlement->total_items_sold }} Unit</span>
                </div>
                <div class="flex justify-between text-emerald-200">
                    <span>Total Omset Penjualan:</span>
                    <span class="font-bold text-white">Rp {{ number_format($settlement->total_sales_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-emerald-200">
                    <span>Porsi Margin Kas SAMSARA:</span>
                    <span class="font-bold text-emerald-400">Rp {{ number_format($settlement->samsara_margin_amount, 0, ',', '.') }}</span>
                </div>

                <div class="pt-2 border-t border-emerald-800 flex justify-between font-heading font-extrabold text-sm sm:text-base text-amber-300">
                    <span>HAK DITERIMA MITRA:</span>
                    <span>Rp {{ number_format($settlement->mitra_earnings_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- WhatsApp Pre-filled Text Preview -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pesan Slip Digital (WhatsApp Ready):</label>
                <textarea readonly rows="8" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs font-mono text-slate-700 leading-relaxed">{{ $whatsappText }}</textarea>
            </div>

            <!-- 1-Click Send WA Button -->
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $settlement->mitra->phone_wa);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
                $waUrl = 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($whatsappText);
            @endphp
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" onclick="navigator.clipboard.writeText(`{{ addslashes($whatsappText) }}`); alert('Teks slip berhasil disalin!')"
                    class="touch-target rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 transition">
                    <i class="fas fa-copy"></i> Salin Teks Slip
                </button>

                <a href="{{ $waUrl }}" target="_blank" class="touch-target rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-md transition">
                    <i class="fab fa-whatsapp text-base"></i> Kirim ke WhatsApp Mitra
                </a>
            </div>

            @if ($settlement->status !== 'PAID')
                <div class="pt-3 border-t border-slate-100">
                    <form action="{{ route('consignment.settlement.confirm', $settlement->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Konfirmasi bahwa pembayaran hak mitra sebesar Rp {{ number_format($settlement->mitra_earnings_amount, 0, ',', '.') }} telah berhasil ditransfer?')"
                            class="w-full touch-target rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-md transition py-3">
                            <i class="fas fa-check-circle text-base"></i> Konfirmasi Pembayaran Telah Ditransfer ke Mitra
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
