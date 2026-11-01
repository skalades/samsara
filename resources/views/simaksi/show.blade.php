<x-layouts.app title="E-Ticket SIMAKSI {{ $booking->booking_code }}">
    <div class="max-w-2xl mx-auto">
        <!-- E-TICKET BOARDING PASS CARD -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-lg overflow-hidden mb-6">
            <!-- Ticket Header -->
            <div class="bg-[#1E3A2B] text-white p-5 sm:p-6 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 opacity-15 text-white text-9xl pointer-events-none">
                    <i class="fas fa-ticket-alt"></i>
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-extrabold text-lg text-white">SAMSARA CIKURAY</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#D97736] text-white uppercase">E-TICKET</span>
                        </div>
                        <p class="text-xs text-emerald-200 mt-0.5">Jalur Pendakian Resmi via Cigedug &bull; 2.821 MDPL</p>
                    </div>

                    <x-ui.badge :color="$booking->status->color()" size="lg">
                        {{ $booking->status->label() }}
                    </x-ui.badge>
                </div>
            </div>

            @php
                $isPendingPayment = $booking->status === \App\Shared\Enums\BookingStatus::PENDING_PAYMENT;
                $isCancelled      = $booking->status === \App\Shared\Enums\BookingStatus::CANCELLED;
                $showQr           = in_array($booking->status, [
                    \App\Shared\Enums\BookingStatus::CONFIRMED,
                    \App\Shared\Enums\BookingStatus::CHECKED_IN,
                    \App\Shared\Enums\BookingStatus::ON_TRAIL,
                    \App\Shared\Enums\BookingStatus::CHECKED_OUT,
                ], true);
            @endphp

            {{-- PENDING PAYMENT: Instruksi Transfer --}}
            @if ($isPendingPayment)
                <div class="mx-5 sm:mx-6 mt-5 p-4 rounded-2xl bg-amber-50 border border-amber-300 space-y-3">
                    <div class="flex items-center gap-2 text-amber-900">
                        <span class="text-xl">⏳</span>
                        <h2 class="font-heading font-extrabold text-sm">Menunggu Konfirmasi Pembayaran Admin</h2>
                    </div>

                    <div class="text-xs text-amber-800 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span>Total Pembayaran</span>
                            <span class="text-base text-amber-900">
                                Rp {{ number_format($booking->total_ticket_amount + $booking->eco_deposit_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Metode Pembayaran</span>
                            <span class="font-semibold">{{ $booking->payment_method->label() }}</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-white border border-amber-200 text-xs space-y-1.5 text-slate-700">
                        <p class="font-bold text-slate-900 text-[11px] uppercase tracking-wide">Informasi Transfer</p>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Bank BRI</span>
                            <span class="font-bold font-mono">1234-5678-9012</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Atas Nama</span>
                            <span class="font-semibold">SAMSARA CIGEDUG</span>
                        </div>
                        <div class="mt-1 pt-1 border-t border-amber-100 flex justify-between items-center">
                            <span class="text-slate-500">QRIS (scan QR statis)</span>
                            <span class="text-[10px] text-slate-400 italic">Scan di aplikasi dompet digital Anda</span>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-amber-100 border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                        <i class="fas fa-info-circle mt-0.5 text-amber-600 flex-shrink-0"></i>
                        <div>
                            <span class="font-bold">Berita Transfer:</span>
                            <span class="font-mono font-black ml-1">{{ $booking->booking_code }}</span>
                            <br>
                            <span class="text-[11px] text-amber-700">Cantumkan kode booking di atas sebagai keterangan/berita transfer.</span>
                        </div>
                    </div>

                    {{-- Form Upload Bukti Transfer --}}
                    <div class="p-3.5 rounded-xl bg-white border border-amber-300 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 uppercase tracking-wide text-[11px] flex items-center gap-1.5">
                                <i class="fas fa-receipt text-[#D97736]"></i> Bukti Pembayaran / Struk Transfer
                            </span>
                            @if ($booking->hasPaymentProof())
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                    <i class="fas fa-check"></i> Sudah Diunggah
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                                    Belum Diunggah
                                </span>
                            @endif
                        </div>

                        @if ($booking->hasPaymentProof())
                            <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-image text-emerald-600 text-base"></i>
                                    <div>
                                        <div class="font-bold text-[11px]">Bukti transfer Anda telah tersimpan</div>
                                        <div class="text-[10px] text-emerald-700">Sedang diverifikasi oleh admin loket</div>
                                    </div>
                                </div>
                                <a href="{{ $booking->payment_proof_url }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-emerald-300 text-emerald-800 text-[11px] font-bold hover:bg-emerald-100 transition flex items-center gap-1">
                                    <i class="fas fa-eye"></i> Lihat File
                                </a>
                            </div>
                        @endif

                        <form action="{{ route('simaksi.upload-proof', $booking->booking_code) }}" method="POST" enctype="multipart/form-data" class="space-y-2 pt-1">
                            @csrf
                            <label class="block text-[11px] font-semibold text-slate-600">
                                {{ $booking->hasPaymentProof() ? 'Ganti / Unggah Ulang Bukti Transfer:' : 'Unggah Foto/Screenshot Struk Pembayaran:' }}
                            </label>
                            <input type="file" name="payment_proof" accept="image/*,.pdf" required
                                class="w-full text-xs text-slate-700 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1E3A2B] file:text-white hover:file:bg-[#12241B] file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1">
                            <button type="submit" class="touch-target w-full rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white font-bold text-xs py-2 shadow-xs transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-upload"></i> Kirim Bukti Transfer ke Admin
                            </button>
                        </form>
                    </div>

                    <p class="text-[11px] text-amber-700 text-center">
                        <i class="fas fa-clock mr-1"></i>
                        Admin akan memverifikasi pembayaran Anda dalam <strong>1×24 jam</strong> setelah transfer dilakukan.
                    </p>
                </div>
            @endif

            {{-- CANCELLED: Pesan Pembatalan --}}
            @if ($isCancelled)
                <div class="mx-5 sm:mx-6 mt-5 p-4 rounded-2xl bg-rose-50 border border-rose-300">
                    <div class="flex items-center gap-2 text-rose-900 mb-2">
                        <i class="fas fa-ban text-rose-600"></i>
                        <h2 class="font-heading font-extrabold text-sm">Booking Dibatalkan</h2>
                    </div>
                    <p class="text-xs text-rose-800">
                        Booking ini telah dibatalkan. Kuota yang telah dipesan sudah dikembalikan ke sistem.
                        @if ($booking->notes)
                            <br><span class="font-semibold mt-1 block">Keterangan: {{ $booking->notes }}</span>
                        @endif
                    </p>
                </div>
            @endif

            {{-- QR CODE SECTION — hanya tampil jika CONFIRMED/CHECKED_IN/ON_TRAIL/CHECKED_OUT --}}
            @if ($showQr)
                <!-- QR Code & Scanner Centerpiece (Thumb-friendly mobile display) -->
                <div class="p-6 bg-slate-50/60 border-b border-dashed border-slate-300 text-center flex flex-col items-center justify-center">
                    <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-200 inline-block">
                        <div id="qrcode" class="flex items-center justify-center"></div>
                    </div>

                    <div class="mt-3">
                        <span class="text-xs text-slate-500 font-medium">KODE BOOKING</span>
                        <div class="text-2xl font-heading font-black tracking-widest text-slate-900 mt-0.5 font-mono">
                            {{ $booking->booking_code }}
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 max-w-sm">
                            Tunjukkan QR Code ini kepada petugas Basecamp saat Check-in dan kepada petugas Pos 1 s/d Pos 4 di jalur.
                        </p>
                    </div>
                </div>
            @else
                {{-- Tampilkan kode booking saja tanpa QR --}}
                <div class="p-5 bg-slate-50/60 border-b border-dashed border-slate-300 text-center">
                    <span class="text-xs text-slate-500 font-medium">KODE BOOKING</span>
                    <div class="text-2xl font-heading font-black tracking-widest text-slate-900 mt-0.5 font-mono">
                        {{ $booking->booking_code }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        QR Code akan tersedia setelah pembayaran dikonfirmasi oleh admin.
                    </p>
                </div>
            @endif

            <!-- Booking Highlights -->
            <div class="p-5 sm:p-6 space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 block">Tanggal Naik</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                            {{ $booking->hiking_date->locale('id')->isoFormat('D MMMM Y') }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 block">Total Pendaki</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                            {{ $booking->total_hikers }} Orang
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 col-span-2 sm:col-span-1">
                        <span class="text-slate-500 block">Posisi Saat Ini</span>
                        <span class="font-bold text-[#D97736] text-sm mt-0.5 block">
                            <i class="fas fa-map-pin mr-1"></i> {{ $booking->current_pos }}
                        </span>
                    </div>
                </div>

                <!-- Leader & Emergency -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ketua Rombongan</span>
                        <span class="font-bold text-slate-900">{{ $booking->leader_name }} ({{ $booking->leader_phone }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kontak Darurat</span>
                        <span class="font-semibold text-rose-700">{{ $booking->emergency_contact_name }} &bull; {{ $booking->emergency_contact_phone }}</span>
                    </div>
                    @if ($booking->tent_color_details)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Warna/Jenis Tenda</span>
                            <span class="font-semibold text-slate-800">{{ $booking->tent_color_details }}</span>
                        </div>
                    @endif
                </div>

                <!-- Manifest Personil Table -->
                <div>
                    <h4 class="font-heading font-bold text-xs text-slate-700 uppercase tracking-wider mb-2">
                        Daftar Anggota Rombongan ({{ $booking->members->count() }} Orang)
                    </h4>
                    <div class="rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 text-xs">
                        @foreach ($booking->members as $idx => $m)
                            <div class="p-2.5 bg-white flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">{{ $idx + 1 }}</span>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $m->full_name }}</div>
                                        <div class="text-[10px] text-slate-400">NIK: {{ substr($m->nik, 0, 6) }}****** &bull; {{ $m->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $m->age }} th</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] {{ $m->medical_history && !str_contains(strtolower($m->medical_history), 'tidak') ? 'text-amber-700 font-semibold' : 'text-slate-400' }}">
                                        {{ $m->medical_history ?: 'Sehat' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Eco-deposit Status -->
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between text-emerald-900">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-recycle text-emerald-600 text-base"></i>
                        <div>
                            <span class="font-bold block">Status Eco-Deposit Sampah: Rp {{ number_format($booking->eco_deposit_amount, 0, ',', '.') }}</span>
                            <span class="text-[11px] text-emerald-700">Dapat direfund di Basecamp saat sampah dibawa turun.</span>
                        </div>
                    </div>
                    <x-ui.badge color="emerald" size="sm">
                        {{ $booking->ecoDeposit?->deposit_status ?? 'HELD' }}
                    </x-ui.badge>
                </div>

                <!-- Quick Action Buttons (Thumb-Zone Friendly) -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="window.print()" class="touch-target rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition">
                        <i class="fas fa-print"></i> Cetak / Simpan PDF
                    </button>
                    <a href="{{ route('store.index') }}" class="touch-target rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white text-xs font-bold flex items-center justify-center gap-2 shadow-md transition">
                        <i class="fas fa-cart-plus"></i> Pre-Order Logistik
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($showQr)
        @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Generate QR Code with qrcode.js
                new QRCode(document.getElementById("qrcode"), {
                    text: "{{ $booking->qr_token }}",
                    width: 170,
                    height: 170,
                    colorDark: "#1E3A2B",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });
        </script>
        @endpush
    @endif
</x-layouts.app>

