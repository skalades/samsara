<x-layouts.app :title="'Cek Booking & E-Ticket Pendaki'">
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- HEADER & SEARCH CARD -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#1E3A2B] border border-emerald-100 flex items-center justify-center mx-auto mb-3 shadow-sm">
                <i class="fas fa-qrcode text-2xl text-[#D97736]"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 tracking-tight">
                Cek Status Tiket & Pre-Order
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1.5 max-w-lg mx-auto">
                Buka e-ticket SIMAKSI, QR Check-in Jalur, dan Barcode Pengambilan Pre-order Logistik di Basecamp SAMSARA Cikuray via Cigedug.
            </p>

            <!-- Search Form -->
            <form action="{{ route('simaksi.check') }}" method="GET" class="mt-6 max-w-xl mx-auto space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-ticket text-sm"></i>
                        </div>
                        <input type="text" name="code" value="{{ $code }}"
                            placeholder="Kode Booking (misal: SMX-...)"
                            class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B] text-sm">
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </div>
                        <input type="text" name="phone" value="{{ $phone }}"
                            placeholder="No. WhatsApp Ketua"
                            class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B] text-sm">
                    </div>
                </div>

                <button type="submit"
                    class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-[#1E3A2B] hover:bg-[#162B20] text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2 mx-auto active:scale-95">
                    <i class="fas fa-search text-[#D97736]"></i> Cari E-Ticket & Pesanan
                </button>
            </form>

            <!-- DEMO SHORTCUT PILL -->
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-center gap-2 text-xs text-slate-500">
                <span>Coba Demo Cepat:</span>
                <a href="{{ route('simaksi.check', ['code' => 'A101']) }}"
                    class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold border border-emerald-200 transition">
                    Kode Demo: A101
                </a>
            </div>
        </div>

        <!-- SEARCH RESULTS -->
        @if ($notFound)
            <div class="bg-white rounded-2xl p-8 border border-rose-200 text-center shadow-sm">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-search-minus text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-800">Data Reservasi Tidak Ditemukan</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Pastikan kombinasi Kode Booking (misal: <code>SMX-...</code> atau <code>PO-...</code>) atau Nomor WhatsApp sesuai dengan yang diisi saat registrasi online.
                </p>
                <div class="mt-4">
                    <a href="{{ route('simaksi.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#D97736] text-white text-xs font-bold hover:bg-[#B25A20] transition">
                        <i class="fas fa-ticket-alt"></i> Buat Booking Baru
                    </a>
                </div>
            </div>
        @endif

        @if ($booking)
            <!-- DIGITAL BOARDING PASS CARD -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
                <!-- Top Header Band -->
                <div class="bg-[#1E3A2B] text-white p-5 sm:p-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="/LOGO.jpeg" alt="Logo SAMSARA" class="w-10 h-10 rounded-xl object-cover ring-2 ring-[#D97736]/60">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-emerald-300 tracking-wider">E-Ticket Pendakian Resmi</span>
                            <h2 class="text-lg sm:text-xl font-heading font-extrabold text-white leading-tight">
                                {{ $booking->booking_code }}
                            </h2>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $booking->status->badgeClass() }} shadow-sm">
                        {{ $booking->status->label() }}
                    </span>
                </div>

                @php
                    $canShowQr = in_array($booking->status, [
                        \App\Shared\Enums\BookingStatus::CONFIRMED,
                        \App\Shared\Enums\BookingStatus::CHECKED_IN,
                        \App\Shared\Enums\BookingStatus::ON_TRAIL,
                        \App\Shared\Enums\BookingStatus::CHECKED_OUT,
                    ], true);
                @endphp

                <div class="p-5 sm:p-8 space-y-6">
                    @if ($canShowQr)
                        <!-- QR Code & Quick Scan Instructions -->
                        <div class="flex flex-col sm:flex-row items-center gap-6 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200 shrink-0">
                                <div id="qrcode-booking" class="w-36 h-36 flex items-center justify-center"></div>
                            </div>
                            <div class="text-center sm:text-left space-y-2">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                    <i class="fas fa-check-circle"></i> Token Terverifikasi
                                </div>
                                <h3 class="font-heading font-bold text-base text-slate-800">
                                    Tunjukkan QR Code Ini di Jalur
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Petugas di Loket Basecamp dan Pos 1 s/d Pos 4 (Samudra Awan) akan memindai QR ini untuk mencatat posisi rombongan Anda demi keselamatan.
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">Token: {{ $booking->qr_token }}</p>
                            </div>
                        </div>
                    @elseif ($booking->status === \App\Shared\Enums\BookingStatus::PENDING_PAYMENT)
                        <!-- Pending Alert (No QR Code) -->
                        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-300 space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-hourglass-half text-amber-600"></i>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-heading font-extrabold text-sm text-amber-900">
                                        Pembayaran Menunggu Konfirmasi Admin
                                    </h3>
                                    <p class="text-xs text-amber-800 leading-relaxed">
                                        QR Code E-Ticket Anda belum aktif. QR akan otomatis muncul dan dapat dipindai petugas setelah pembayaran berhasil diverifikasi oleh admin Basecamp SAMSARA.
                                    </p>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-amber-200 flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xs text-amber-900 font-semibold">
                                    Total: <strong>Rp {{ number_format($booking->total_ticket_amount + $booking->eco_deposit_amount, 0, ',', '.') }}</strong>
                                </span>
                                <a href="{{ route('simaksi.show', $booking->booking_code) }}"
                                    class="px-4 py-2 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                                    <i class="fas fa-upload"></i> Buka Halaman Bayar & Upload Struk
                                </a>
                            </div>
                        </div>
                    @elseif ($booking->status === \App\Shared\Enums\BookingStatus::CANCELLED)
                        <!-- Cancelled Alert -->
                        <div class="p-5 rounded-2xl bg-rose-50 border border-rose-300 flex items-center gap-3 text-rose-900">
                            <i class="fas fa-ban text-2xl text-rose-600 shrink-0"></i>
                            <div>
                                <h3 class="font-heading font-extrabold text-sm">Booking Ini Telah Dibatalkan</h3>
                                <p class="text-xs text-rose-700 mt-0.5">Reservasi tidak berlaku dan tidak dapat digunakan untuk pendakian.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Hiker & Trip Summary -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block mb-0.5">Ketua Rombongan</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $booking->leader_name }}</span>
                            <span class="text-[11px] text-slate-500 block">{{ $booking->leader_phone }}</span>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block mb-0.5">Tanggal Pendakian</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $booking->hiking_date->translatedFormat('d M Y') }}</span>
                            <span class="text-[11px] text-slate-500 block">Durasi: {{ $booking->duration_days }} Hari</span>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block mb-0.5">Jumlah Anggota</span>
                            <span class="font-bold text-[#1E3A2B] text-sm">{{ $booking->total_hikers }} Pendaki</span>
                            <span class="text-[11px] text-slate-500 block">Manifest Lengkap</span>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block mb-0.5">Posisi Terakhir</span>
                            <span class="font-bold text-[#D97736] text-sm">{{ $booking->current_pos }}</span>
                            <span class="text-[11px] text-slate-500 block">Live Tracker</span>
                        </div>
                    </div>

                    <!-- Checkpoint Progression Bar -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fas fa-route text-[#D97736]"></i> Tracking Posisi di Jalur Cikuray
                        </h4>
                        <div class="grid grid-cols-5 text-center text-[10px] font-bold gap-1">
                            @php
                                $posList = [
                                    'Basecamp' => 'Basecamp',
                                    'Pos 1' => 'Pos 1 (Tanjakan EOD)',
                                    'Pos 2' => 'Pos 2 (Batu Gendrung)',
                                    'Pos 3' => 'Pos 3 (Alam Rimba)',
                                    'Pos 4' => 'Pos 4 (Samudra Awan)',
                                ];
                                $currentPosStr = $booking->current_pos;
                            @endphp
                            @foreach ($posList as $short => $full)
                                @php
                                    $isPassed = str_contains($currentPosStr, $short) || ($short === 'Basecamp');
                                @endphp
                                <div class="p-2 rounded-xl {{ $isPassed ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-200 text-slate-500' }}">
                                    <i class="fas {{ $isPassed ? 'fa-check' : 'fa-location-dot' }} block mb-1"></i>
                                    <span>{{ $short }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Manifest Members -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <i class="fas fa-users text-emerald-700"></i> Manifest Rombongan ({{ $booking->members->count() }} Orang)
                        </h4>
                        <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden text-xs">
                            @foreach ($booking->members as $idx => $m)
                                <div class="p-3 bg-white flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px]">
                                            {{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <span class="font-bold text-slate-800">{{ $m->full_name }}</span>
                                            <span class="text-[11px] text-slate-400 block">NIK: {{ substr($m->nik, 0, 6) }}******</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-semibold">
                                            {{ $m->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $m->age }} th
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Financial Summary & Action -->
                    <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <span class="text-xs text-slate-500 block">Total Biaya (SIMAKSI + Deposit Sampah)</span>
                            <span class="font-heading font-extrabold text-lg text-[#1E3A2B]">
                                Rp {{ number_format($booking->total_ticket_amount + $booking->eco_deposit_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('simaksi.show', $booking->booking_code) }}"
                            class="px-4 py-2.5 rounded-xl bg-[#1E3A2B] hover:bg-[#162B20] text-white text-xs font-bold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-external-link-alt text-[#D97736]"></i> Halaman Tiket Lengkap
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if ($order)
            <!-- PRE-ORDER TOKO / LOGISTIK CARD -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-lg"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-amber-600 tracking-wider">Pre-Order Logistik Basecamp</span>
                            <h3 class="font-heading font-extrabold text-base text-slate-900">{{ $order->invoice_number }}</h3>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $order->is_paid ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $order->is_paid ? 'Lunas' : 'Bayar di Loket' }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    @foreach ($order->items as $it)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">{{ $it->item_name ?: ($it->product->name ?? 'Produk Basecamp') }}</span>
                                <span class="text-slate-400 block text-[11px]">{{ $it->qty }}x @ Rp {{ number_format($it->unit_price, 0, ',', '.') }}</span>
                            </div>
                            <span class="font-bold text-slate-800">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-600">Total Pre-Order:</span>
                    <span class="font-heading font-extrabold text-base text-[#1E3A2B]">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>

                <div class="text-center pt-2">
                    <a href="{{ route('store.success', $order->invoice_number) }}" class="text-xs text-[#1E3A2B] hover:text-[#D97736] font-bold inline-flex items-center gap-1">
                        <i class="fas fa-barcode"></i> Tampilkan Barcode Pengambilan Kasir →
                    </a>
                </div>
            </div>
        @endif

    </div>

    @push('scripts')
    @if ($booking && $canShowQr)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var qrContainer = document.getElementById("qrcode-booking");
            if (qrContainer) {
                qrContainer.innerHTML = "";
                new QRCode(qrContainer, {
                    text: "{{ $booking->qr_token }}",
                    width: 140,
                    height: 140,
                    colorDark: "#1E3A2B",
                    colorLight: "#FFFFFF",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        });
    </script>
    @endif
    @endpush
</x-layouts.app>
