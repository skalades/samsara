<x-layouts.app title="Pre-Order Berhasil {{ $order->invoice_number }}">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-lg overflow-hidden p-6 text-center">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-[#1E3A2B] mx-auto flex items-center justify-center text-2xl mb-3">
                <i class="fas fa-check-double"></i>
            </div>

            <h1 class="text-xl sm:text-2xl font-heading font-extrabold text-slate-900">Pre-Order Dikonfirmasi!</h1>
            <p class="text-xs text-slate-500 mt-1">
                Barang Anda telah disiapkan oleh staf basecamp. Tunjukkan barcode ini saat tiba di loket kasir SAMSARA.
            </p>

            <!-- QR BARCODE FOR FAST PICKUP SCAN -->
            <div class="p-5 my-5 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col items-center justify-center">
                <div id="pickup-qrcode" class="p-3 bg-white rounded-xl shadow-xs border border-slate-200"></div>
                <div class="font-mono font-black text-lg text-slate-900 tracking-wider mt-3">
                    {{ $order->invoice_number }}
                </div>
                <span class="text-[10px] text-slate-400 mt-0.5">Barcode Pengambilan Cepat (Click & Collect)</span>
            </div>

            <!-- ORDER SUMMARY -->
            <div class="text-left bg-slate-50/60 rounded-2xl p-4 border border-slate-200 space-y-2 text-xs mb-5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Pemesan:</span>
                    <span class="font-bold text-slate-900">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">No. WhatsApp:</span>
                    <span class="font-bold text-slate-900">{{ $order->customer_phone ?: '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-bold text-emerald-800">{{ $order->payment_method->label() }}</span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between font-heading font-bold text-sm">
                    <span class="text-slate-700">Total Pembayaran:</span>
                    <span class="text-[#D97736]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- ITEMS RECAP -->
            <div class="text-left mb-6">
                <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Item Belanja ({{ $order->items->count() }}):</div>
                <div class="space-y-1.5 divide-y divide-slate-100 text-xs">
                    @foreach ($order->items as $item)
                        <div class="pt-1.5 flex justify-between">
                            <span class="text-slate-700">{{ $item->qty }}x {{ $item->item_name }}</span>
                            <span class="font-semibold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('store.index') }}" class="touch-target rounded-xl border border-slate-300 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 hover:bg-slate-50">
                    <i class="fas fa-arrow-left"></i> Belanja Lagi
                </a>
                <a href="{{ route('home') }}" class="touch-target rounded-xl bg-[#1E3A2B] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-md hover:bg-[#12241B]">
                    <i class="fas fa-home"></i> Beranda SIMS
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new QRCode(document.getElementById("pickup-qrcode"), {
                text: "{{ $order->invoice_number }}",
                width: 150,
                height: 150,
                colorDark: "#1E3A2B",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
    @endpush
</x-layouts.app>
