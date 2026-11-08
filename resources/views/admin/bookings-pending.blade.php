<x-layouts.admin title="Konfirmasi Pembayaran SIMAKSI">
    <div x-data="{
        showWalkInModal: false,
        members: [{ full_name: '', nik: '', gender: 'L', age: 20, medical_history: 'Sehat' }],
        addMember() {
            this.members.push({ full_name: '', nik: '', gender: 'L', age: 20, medical_history: 'Sehat' });
        },
        removeMember(index) {
            if (this.members.length > 1) {
                this.members.splice(index, 1);
            }
        }
    }">
        <!-- PAGE HEADER -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                    <i class="fas fa-money-check-alt"></i> Sub-Modul Administrasi
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight flex items-center gap-3">
                    Konfirmasi Pembayaran SIMAKSI
                    @if ($bookings->total() > 0)
                        <x-ui.badge color="amber" size="lg">{{ $bookings->total() }} Menunggu</x-ui.badge>
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">Verifikasi dan konfirmasi pembayaran booking pendakian yang masuk secara manual.</p>
            </div>

            <div class="flex items-center gap-2 self-start">
                <button type="button" @click="showWalkInModal = true" class="touch-target px-4 py-2.5 rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-heading font-extrabold text-xs flex items-center gap-2 transition shadow-sm cursor-pointer">
                    <i class="fas fa-user-plus text-amber-300"></i>
                    <span>+ Registrasi Walk-In Manual</span>
                </button>

                <a href="{{ route('admin.index') }}" class="touch-target px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs flex items-center gap-2 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Command Center</span>
                </a>
            </div>
        </div>

        <!-- SEARCH BAR -->
        <form method="GET" action="{{ route('admin.bookings.pending') }}" class="mb-5">
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari kode booking, nama ketua, atau nomor HP..."
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-300 bg-white text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B]/30 focus:border-[#1E3A2B] transition"
                >
                <button type="submit" class="absolute inset-y-0 right-2 my-1.5 px-4 rounded-xl bg-[#1E3A2B] text-white text-xs font-bold flex items-center gap-1 hover:bg-[#12241B] transition">
                    <i class="fas fa-filter"></i>
                    <span class="hidden sm:inline">Filter</span>
                </button>
            </div>
        </form>

        <!-- BOOKING LIST -->
        @if ($bookings->isEmpty())
            <!-- EMPTY STATE -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-10 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-emerald-600 text-2xl"></i>
                </div>
                <h3 class="font-heading font-extrabold text-lg text-slate-900 mb-1">
                    Semua Bersih!
                </h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto">
                    Tidak ada booking yang menunggu konfirmasi pembayaran saat ini.
                    @if ($search)
                        <br><span class="text-xs">Coba hapus filter pencarian untuk melihat semua data.</span>
                    @endif
                </p>
                @if ($search)
                    <a href="{{ route('admin.bookings.pending') }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-[#D97736] hover:text-[#B25A20]">
                        <i class="fas fa-times-circle"></i> Hapus Filter
                    </a>
                @endif
            </div>
        @else
            <!-- DESKTOP TABLE (hidden on mobile) -->
            <div class="hidden lg:block bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-[#1E3A2B] text-white">
                                <th class="px-4 py-3.5 text-left font-bold tracking-wide">Kode Booking</th>
                                <th class="px-4 py-3.5 text-left font-bold tracking-wide">Nama Ketua</th>
                                <th class="px-4 py-3.5 text-left font-bold tracking-wide">Tanggal Naik</th>
                                <th class="px-4 py-3.5 text-center font-bold tracking-wide">Jumlah</th>
                                <th class="px-4 py-3.5 text-right font-bold tracking-wide">Total Bayar</th>
                                <th class="px-4 py-3.5 text-center font-bold tracking-wide">Metode</th>
                                <th class="px-4 py-3.5 text-center font-bold tracking-wide">Bukti Bayar</th>
                                <th class="px-4 py-3.5 text-left font-bold tracking-wide">Waktu Submit</th>
                                <th class="px-4 py-3.5 text-center font-bold tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($bookings as $booking)
                                <tr class="hover:bg-slate-50/70 transition" x-data="{ showRejectForm: false }">
                                    <td class="px-4 py-3.5">
                                        <span class="font-mono font-black text-slate-900">{{ $booking->booking_code }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-semibold text-slate-900">{{ $booking->leader_name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $booking->leader_phone }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-700 font-medium">
                                        {{ $booking->hiking_date->locale('id')->isoFormat('D MMM Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <x-ui.badge color="sky">{{ $booking->total_hikers }} Orang</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-slate-900">
                                        Rp {{ number_format($booking->total_ticket_amount + $booking->eco_deposit_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-slate-600">
                                        {{ $booking->payment_method->label() }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if ($booking->hasPaymentProof())
                                            <a href="{{ $booking->payment_proof_url }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-[10px] transition shadow-2xs">
                                                <i class="fas fa-file-image text-emerald-600"></i> Cek Struk
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 italic">
                                                <i class="fas fa-clock text-slate-300"></i> Belum ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-500">
                                        {{ $booking->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex flex-col gap-2 min-w-[180px]">
                                            {{-- Konfirmasi --}}
                                            <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST"
                                                onsubmit="return confirm('Konfirmasi pembayaran booking {{ $booking->booking_code }} atas nama {{ addslashes($booking->leader_name) }}?\n\nAksi ini tidak dapat dibatalkan.')">
                                                @csrf
                                                <button type="submit" class="w-full touch-target rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3 py-2 flex items-center justify-center gap-1.5 transition">
                                                    <i class="fas fa-check-circle"></i> Konfirmasi Bayar
                                                </button>
                                            </form>

                                            {{-- Batalkan --}}
                                            <div>
                                                <button type="button" @click="showRejectForm = !showRejectForm"
                                                    class="w-full touch-target rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-3 py-2 flex items-center justify-center gap-1.5 transition">
                                                    <i class="fas fa-ban"></i>
                                                    <span x-text="showRejectForm ? 'Tutup' : 'Batalkan Booking'"></span>
                                                </button>

                                                <div x-show="showRejectForm" x-collapse class="mt-2">
                                                    <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST"
                                                        onsubmit="return confirm('Yakin ingin membatalkan booking {{ $booking->booking_code }}?\n\nKuota akan dikembalikan ke sistem.')">
                                                        @csrf
                                                        <div class="flex gap-1.5">
                                                            <input type="text" name="reason" placeholder="Alasan pembatalan..." required maxlength="255"
                                                                class="flex-1 rounded-xl border border-slate-300 px-2.5 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-rose-400 focus:border-rose-400">
                                                            <button type="submit" class="px-3 py-2 rounded-xl bg-rose-700 text-white text-xs font-bold hover:bg-rose-800 transition flex-shrink-0">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MOBILE CARD LIST (visible on mobile only) -->
            <div class="lg:hidden space-y-3 mb-5">
                @foreach ($bookings as $booking)
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden" x-data="{ showRejectForm: false }">
                        <!-- Card Header -->
                        <div class="bg-[#1E3A2B] px-4 py-3 flex items-center justify-between">
                            <span class="font-mono font-black text-white text-sm">{{ $booking->booking_code }}</span>
                            <x-ui.badge color="amber" size="sm">Menunggu Bayar</x-ui.badge>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 space-y-3">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-500 block">Ketua Rombongan</span>
                                    <span class="font-bold text-slate-900">{{ $booking->leader_name }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $booking->leader_phone }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Tanggal Naik</span>
                                    <span class="font-bold text-slate-900">{{ $booking->hiking_date->locale('id')->isoFormat('D MMM Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Jumlah Pendaki</span>
                                    <span class="font-bold text-slate-900">{{ $booking->total_hikers }} Orang</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Total Bayar</span>
                                    <span class="font-bold text-slate-900 text-sm">Rp {{ number_format($booking->total_ticket_amount + $booking->eco_deposit_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] border-t border-slate-100 pt-2">
                                <div>
                                    <span class="text-slate-500">{{ $booking->payment_method->label() }}</span>
                                    <span class="text-slate-400 block text-[10px]">Submit: {{ $booking->created_at->locale('id')->isoFormat('D MMM, HH:mm') }}</span>
                                </div>
                                <div>
                                    @if ($booking->hasPaymentProof())
                                        <a href="{{ $booking->payment_proof_url }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs shadow-2xs hover:bg-emerald-200 transition">
                                            <i class="fas fa-file-image text-emerald-600"></i> Cek Struk
                                        </a>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Belum unggah bukti</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2 pt-1">
                                <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST"
                                    onsubmit="return confirm('Konfirmasi pembayaran booking {{ $booking->booking_code }}?')">
                                    @csrf
                                    <button type="submit" class="w-full touch-target rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm flex items-center justify-center gap-2 transition">
                                        <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
                                    </button>
                                </form>

                                <button type="button" @click="showRejectForm = !showRejectForm"
                                    class="w-full touch-target rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-sm border border-rose-200 flex items-center justify-center gap-2 transition">
                                    <i class="fas fa-ban"></i>
                                    <span x-text="showRejectForm ? 'Tutup Form Pembatalan' : 'Batalkan Booking'"></span>
                                </button>

                                <div x-show="showRejectForm" x-collapse>
                                    <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin membatalkan booking ini? Kuota akan dikembalikan.')">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" name="reason" placeholder="Alasan pembatalan..." required maxlength="255"
                                                class="flex-1 rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-rose-400 focus:border-rose-400">
                                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-600 text-white font-bold text-sm hover:bg-rose-700 transition flex-shrink-0">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-rose-600 mt-1.5">
                                            <i class="fas fa-exclamation-triangle mr-0.5"></i>
                                            Kuota {{ $booking->total_hikers }} orang akan dikembalikan ke sistem.
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINATION -->
            @if ($bookings->hasPages())
                <div class="flex justify-center">
                    {{ $bookings->links() }}
                </div>
            @endif
        @endif

        <!-- ADMIN WALK-IN REGISTRATION MODAL -->
        <div x-show="showWalkInModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showWalkInModal" @click="showWalkInModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="showWalkInModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl transform transition-all max-w-2xl w-full border border-slate-200 relative z-10 max-h-[90vh] flex flex-col">

                    <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#1E3A2B] text-white flex items-center justify-center text-lg shrink-0">
                                <i class="fas fa-hiking text-amber-300"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-extrabold text-base sm:text-lg text-slate-900">Registrasi SIMAKSI Manual / Walk-In</h3>
                                <p class="text-xs text-slate-500">Pendaftaran on-the-spot oleh staf / admin basecamp.</p>
                            </div>
                        </div>
                        <button type="button" @click="showWalkInModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.bookings.walkin') }}" method="POST" class="overflow-y-auto pr-1 space-y-4 flex-1">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-200/80">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pendakian</label>
                                <input type="date" name="hiking_date" value="{{ today()->toDateString() }}" required class="touch-target w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 font-semibold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Durasi</label>
                                <select name="duration_days" class="touch-target w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 font-semibold">
                                    <option value="1">1 Hari (Tek-Tok)</option>
                                    <option value="2" selected>2 Hari 1 Malam</option>
                                    <option value="3">3 Hari 2 Malam</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Status Pembayaran</label>
                                <select name="payment_status" class="touch-target w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 font-semibold">
                                    <option value="CONFIRMED">Langsung Lunas (Dikonfirmasi)</option>
                                    <option value="PENDING_PAYMENT">Bayar di Kasir POS (Pending)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-extrabold text-slate-900 block mb-2 flex items-center gap-1.5">
                                <i class="fas fa-user-shield text-[#1E3A2B]"></i> Data Ketua Rombongan
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Lengkap Ketua *</label>
                                    <input type="text" name="leader_name" required placeholder="Sesuai KTP" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">NIK Ketua (16 Digit) *</label>
                                    <input type="text" name="leader_nik" required maxlength="16" placeholder="320501..." class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. WhatsApp / HP *</label>
                                    <input type="tel" name="leader_phone" required placeholder="08..." class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Alamat Asal / Kota</label>
                                    <input type="text" name="leader_address" placeholder="Contoh: Garut / Bandung" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Kontak Darurat Keluarga *</label>
                                    <input type="text" name="emergency_contact_name" required placeholder="Nama Keluarga" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. HP Kontak Darurat *</label>
                                    <input type="tel" name="emergency_contact_phone" required placeholder="08..." class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                            </div>
                        </div>

                        <!-- Members List -->
                        <div class="pt-2 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                                    <i class="fas fa-users text-[#1E3A2B]"></i> Manifest Anggota Rombongan (<span x-text="members.length"></span> Orang)
                                </span>
                                <button type="button" @click="addMember()" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-900 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-plus"></i> Tambah Anggota
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(m, idx) in members" :key="idx">
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-wrap sm:flex-nowrap items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                                        <div class="flex-1 min-w-[140px]">
                                            <input type="text" :name="'members[' + idx + '][full_name]'" x-model="m.full_name" required placeholder="Nama Lengkap *" class="w-full text-xs rounded-lg border border-slate-300 px-2 py-1.5 bg-white">
                                        </div>
                                        <div class="w-32">
                                            <input type="text" :name="'members[' + idx + '][nik]'" x-model="m.nik" maxlength="16" placeholder="NIK (opsional)" class="w-full text-xs font-mono rounded-lg border border-slate-300 px-2 py-1.5 bg-white">
                                        </div>
                                        <div class="w-16">
                                            <select :name="'members[' + idx + '][gender]'" x-model="m.gender" class="w-full text-xs rounded-lg border border-slate-300 px-1 py-1.5 bg-white">
                                                <option value="L">L</option>
                                                <option value="P">P</option>
                                            </select>
                                        </div>
                                        <div class="w-16">
                                            <input type="number" :name="'members[' + idx + '][age]'" x-model.number="m.age" min="10" max="80" placeholder="Usia" class="w-full text-xs rounded-lg border border-slate-300 px-1 py-1.5 bg-white">
                                        </div>
                                        <div class="flex-1 min-w-[120px]">
                                            <input type="text" :name="'members[' + idx + '][medical_history]'" x-model="m.medical_history" placeholder="Riwayat Medis (Sehat)" class="w-full text-xs rounded-lg border border-slate-300 px-2 py-1.5 bg-white">
                                        </div>
                                        <button type="button" x-show="members.length > 1" @click="removeMember(idx)" class="text-rose-500 hover:text-rose-700 p-1.5 shrink-0 cursor-pointer">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-2">
                            <button type="button" @click="showWalkInModal = false" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-600 font-bold text-xs cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-heading font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                                <i class="fas fa-save text-amber-300"></i>
                                <span>Simpan Data Booking</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
