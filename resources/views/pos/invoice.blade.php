<x-layouts.admin title="Struk Pembayaran {{ $order->invoice_number }}">
    <div class="max-w-md mx-auto">
        <!-- THERMAL RECEIPT SLIP CARD (58mm/80mm Realistic Layout) -->
        <div id="receipt-area" class="bg-white rounded-3xl border border-slate-300 shadow-md p-6 font-mono text-xs text-slate-800 space-y-3">
            <!-- Header Basecamp -->
            <div class="text-center pb-3 border-b border-dashed border-slate-300 space-y-1">
                <div class="font-heading font-black text-base tracking-wider text-slate-900">BASECAMP SAMSARA</div>
                <div class="text-[11px] text-slate-600">Satuan Anak Muda Sadar Alam</div>
                <div class="text-[10px] text-slate-500">Cigedug, Garut, Jawa Barat &bull; 2.821 MDPL</div>
                <div class="text-[10px] text-slate-500">WA Loket: 0812-2345-6789</div>
            </div>

            <!-- Invoice Meta -->
            <div class="space-y-1 text-[11px] pb-2 border-b border-dashed border-slate-300">
                <div class="flex justify-between">
                    <span>No. Struk:</span>
                    <span class="font-bold">{{ $order->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Waktu:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }} WIB</span>
                </div>
                <div class="flex justify-between">
                    <span>Kasir:</span>
                    <span>{{ $order->cashier?->name ?? 'Kasir Basecamp' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pelanggan:</span>
                    <span class="font-bold">{{ $order->customer_name }}</span>
                </div>
                @if ($order->booking)
                    <div class="flex justify-between text-emerald-800 font-bold">
                        <span>SIMAKSI:</span>
                        <span>{{ $order->booking->booking_code }} ({{ $order->booking->total_hikers }} Org)</span>
                    </div>
                @endif
            </div>

            <!-- Items Table -->
            <div class="py-2 border-b border-dashed border-slate-300 space-y-2">
                @foreach ($order->items as $item)
                    <div>
                        <div class="font-bold text-slate-900">{{ $item->item_name }}</div>
                        <div class="flex justify-between text-[11px] text-slate-600">
                            <span>{{ $item->qty }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Totals & Payment -->
            <div class="space-y-1 text-[11px] pt-1">
                @if ($order->subtotal_simaksi > 0)
                    <div class="flex justify-between">
                        <span>Tiket SIMAKSI:</span>
                        <span>Rp {{ number_format($order->subtotal_simaksi, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($order->subtotal_store > 0)
                    <div class="flex justify-between">
                        <span>Toko & Logistik:</span>
                        <span>Rp {{ number_format($order->subtotal_store, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($order->eco_deposit > 0)
                    <div class="flex justify-between text-emerald-800 font-semibold">
                        <span>Jaminan Sampah (Deposit):</span>
                        <span>Rp {{ number_format($order->eco_deposit, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($order->rental_deposit > 0)
                    <div class="flex justify-between text-sky-800 font-semibold">
                        <span>Jaminan Sewa Alat:</span>
                        <span>Rp {{ number_format($order->rental_deposit, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-sm font-bold pt-2 border-t border-slate-800 text-slate-900">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between pt-1">
                    <span>Metode Bayar:</span>
                    <span class="font-bold">{{ $order->payment_method->label() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Bayar (Diterima):</span>
                    <span>Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-slate-900">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Eco-Deposit Refund Reminder -->
            <div class="pt-3 border-t border-dashed border-slate-300 text-center text-[10px] text-slate-600 leading-tight">
                <p class="font-bold text-emerald-800">🌱 JAGA KELESTARIAN HUTAN CIKURAY 🌱</p>
                <p class="mt-1">Bawa kembali seluruh sampah logistik Anda ke basecamp untuk mencairkan jaminan sampah (Eco-Deposit).</p>
                <p class="mt-2 text-slate-400">--- Terima Kasih & Salam Lestari ---</p>
            </div>
        </div>

        <!-- ACTION BUTTONS (Bluetooth Thermal Print & Nav) -->
        <div class="mt-5 grid grid-cols-2 gap-3">
            <button type="button" onclick="window.printThermalReceipt()" class="touch-target rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-bold flex items-center justify-center gap-2 shadow-md transition">
                <i class="fab fa-bluetooth-b text-sky-400"></i> Cetak Thermal Bluetooth
            </button>

            <a href="{{ route('pos.index') }}" class="touch-target rounded-2xl bg-[#D97736] hover:bg-[#B25A20] text-white text-xs font-bold flex items-center justify-center gap-2 shadow-md transition">
                <i class="fas fa-plus"></i> Transaksi Baru
            </a>
        </div>
    </div>
</x-layouts.admin>
