<x-layouts.admin title="Sistem Antrian Ojek SAMSARA">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8"
         x-data="ojekCoordinator()">

        <!-- HEADER & LIVE STATUS -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-800 text-xs font-bold border border-emerald-500/20 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Pangkalan Resmi Paguyuban Ojek Cigedug Mandiri
                </div>
                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fas fa-motorcycle text-[#D97736]"></i>
                    Sistem Antrian Ojek Basecamp
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Manajemen rotasi giliran driver adil (Fair FIFO), pemanggilan tiket terpadu, dan monitoring trip.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('ojek.public.display') }}" target="_blank"
                   class="touch-target px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-tv text-amber-400"></i>
                    Buka Display TV
                </a>
                <a href="{{ route('ojek.drivers') }}"
                   class="touch-target px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold border border-slate-300 flex items-center gap-2 shadow-xs transition">
                    <i class="fas fa-id-card text-emerald-600"></i>
                    Data Driver
                </a>
                <button type="button" @click="showWalkInModal = true"
                        class="touch-target px-4 py-2.5 rounded-xl bg-[#D97736] hover:bg-[#b85e25] text-white text-xs sm:text-sm font-bold flex items-center gap-2 shadow-md transition active:scale-95">
                    <i class="fas fa-plus-circle"></i>
                    + Tiket Walk-In
                </button>
            </div>
        </div>

        <!-- STATS BAR -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-[#D97736] flex items-center justify-center text-lg">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Menunggu Driver</span>
                    <span class="font-heading font-extrabold text-xl text-slate-900" x-text="stats.waiting_count">0</span>
                    <span class="text-[10px] text-slate-500">penumpang antre</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Driver Siaga (FIFO)</span>
                    <span class="font-heading font-extrabold text-xl text-slate-900" x-text="stats.standby_drivers_count">0</span>
                    <span class="text-[10px] text-slate-500">driver siap jalan</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fas fa-road"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Sedang Mengantar</span>
                    <span class="font-heading font-extrabold text-xl text-slate-900" x-text="stats.on_trip_count">0</span>
                    <span class="text-[10px] text-slate-500">di jalur Cigedug</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Selesai Hari Ini</span>
                    <span class="font-heading font-extrabold text-xl text-slate-900" x-text="stats.total_completed_trips">0</span>
                    <span class="text-[10px] text-slate-500">trip terlaksana</span>
                </div>
            </div>
        </div>

        <!-- HERO CALL ACTION PANEL -->
        <div class="bg-gradient-to-br from-[#1E3A2B] via-[#162e22] to-[#0c1c14] text-white rounded-3xl p-5 sm:p-7 border border-[#2D5841] shadow-xl mb-8">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6">
                <!-- Status Panggilan Aktif -->
                <div class="flex-1">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-bold border border-amber-400/30 mb-2.5">
                        <i class="fas fa-bullhorn text-amber-300 animate-pulse"></i> Panggilan Aktif Saat Ini
                    </span>

                    <template x-if="latestCalled">
                        <div>
                            <div class="flex flex-wrap items-baseline gap-3">
                                <h2 class="font-heading font-black text-3xl sm:text-4xl text-amber-300 tracking-wider" x-text="latestCalled.ticket_number"></h2>
                                <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-400/30" x-text="latestCalled.route?.route_name"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3 text-xs sm:text-sm">
                                <div class="bg-white/10 p-2.5 rounded-xl border border-white/10">
                                    <span class="text-emerald-300 text-[11px] block">Penumpang:</span>
                                    <span class="font-bold text-white text-base" x-text="latestCalled.passenger_name"></span>
                                </div>
                                <div class="bg-white/10 p-2.5 rounded-xl border border-white/10">
                                    <span class="text-amber-300 text-[11px] block">Driver Bertugas:</span>
                                    <span class="font-bold text-white text-base" x-text="latestCalled.driver?.name + ' (' + latestCalled.driver?.license_plate + ')'"></span>
                                </div>
                            </div>

                            <!-- Action Buttons for Called Ticket -->
                            <div class="flex flex-wrap items-center gap-2.5 mt-4">
                                <button type="button" @click="startTrip(latestCalled.id)"
                                        class="touch-target px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm shadow-md flex items-center gap-2 transition active:scale-95">
                                    <i class="fas fa-play"></i> Mulai Berangkat (On Trip)
                                </button>
                                <button type="button" @click="completeTrip(latestCalled.id)"
                                        class="touch-target px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-md flex items-center gap-2 transition active:scale-95">
                                    <i class="fas fa-check-double"></i> Selesai Trip (Driver Kembali)
                                </button>
                                <button type="button" @click="cancelTicket(latestCalled.id)"
                                        class="touch-target px-3 py-2.5 rounded-xl bg-rose-600/30 hover:bg-rose-600/50 text-rose-200 border border-rose-500/30 font-semibold text-xs flex items-center gap-1.5 transition">
                                    <i class="fas fa-times"></i> Batalkan
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="!latestCalled">
                        <div class="py-3 text-slate-300">
                            <p class="font-medium text-base text-slate-200">Belum ada tiket antrean yang sedang aktif dipanggil.</p>
                            <p class="text-xs text-emerald-200/70 mt-1">Klik tombol di samping untuk memanggil pasangan penumpang & driver giliran pertama berikutnya.</p>
                        </div>
                    </template>
                </div>

                <!-- Big Call Next Button -->
                <div class="shrink-0 flex flex-col justify-center border-t lg:border-t-0 lg:border-l border-white/10 pt-4 lg:pt-0 lg:pl-6">
                    <button type="button" @click="callNext()"
                            :disabled="callingLoading || stats.waiting_count === 0 || stats.standby_drivers_count === 0"
                            class="touch-target px-6 py-4 rounded-2xl bg-gradient-to-r from-[#D97736] to-amber-500 hover:from-[#c26527] hover:to-amber-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-heading font-black text-base sm:text-lg shadow-xl hover:shadow-amber-500/20 flex items-center justify-center gap-3 transition active:scale-95">
                        <i class="fas fa-bullhorn text-xl" :class="{ 'animate-bounce': !callingLoading }"></i>
                        <span>PANGGIL GILIRAN BERIKUTNYA</span>
                    </button>
                    <span class="text-[11px] text-center text-emerald-200/70 mt-2">
                        Memasangkan otomatis FIFO 1 Penumpang & 1 Driver Standby
                    </span>
                </div>
            </div>
        </div>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- LEFT COLUMN: ANTREAN DRIVER HARI INI (FIFO RING) -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 flex items-center gap-2">
                            <i class="fas fa-users-gear text-emerald-600"></i>
                            Antrean Giliran Driver Hari Ini
                        </h3>
                        <p class="text-xs text-slate-500">Urutan paling atas adalah giliran pertama dipanggil (FIFO).</p>
                    </div>
                    <button type="button" @click="showCheckInModal = true"
                            class="touch-target px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition">
                        + Check-in Driver
                    </button>
                </div>

                <div class="space-y-2.5 max-h-[480px] overflow-y-auto pr-1">
                    <template x-for="(att, idx) in standbyDrivers" :key="att.id">
                        <div class="p-3.5 rounded-2xl border transition flex items-center justify-between gap-3"
                             :class="idx === 0 ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-400/30' : 'bg-slate-50/80 border-slate-200'">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl font-heading font-black text-sm flex items-center justify-center shrink-0"
                                     :class="idx === 0 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700'"
                                     x-text="'#' + (idx + 1)">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-slate-900" x-text="att.driver?.name"></span>
                                        <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-slate-200 text-slate-800" x-text="att.driver?.license_plate"></span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5" x-text="(att.driver?.motorcycle_model || 'Motor') + ' • Trip hari ini: ' + att.daily_trips_count + 'x'"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    Standby
                                </span>
                                <button type="button" @click="setDriverStatus(att.id, 'BREAK')"
                                        title="Istirahat"
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-600 hover:text-amber-600 hover:border-amber-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-coffee"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="standbyDrivers.length === 0">
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 text-xs">
                            <i class="fas fa-user-slash text-2xl text-slate-300 mb-2 block"></i>
                            Tidak ada driver yang standby saat ini. Klik tombol <strong>+ Check-in Driver</strong> di atas.
                        </div>
                    </template>
                </div>
            </div>

            <!-- RIGHT COLUMN: PENUMPANG MENUNGGU & SEDANG DI JALUR -->
            <div class="space-y-6">

                <!-- WAITING TICKETS -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-heading font-bold text-lg text-slate-900 flex items-center gap-2">
                                <i class="fas fa-ticket text-amber-500"></i>
                                Tiket Penumpang Menunggu
                            </h3>
                            <p class="text-xs text-slate-500">Daftar penumpang yang sudah bayar tiket dan antre di loket/pangkalan.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-extrabold text-xs" x-text="waitingTickets.length + ' Orang'"></span>
                    </div>

                    <div class="space-y-2.5 max-h-[220px] overflow-y-auto pr-1">
                        <template x-for="(ticket, idx) in waitingTickets" :key="ticket.id">
                            <div class="p-3 rounded-2xl bg-amber-50/40 border border-amber-200/70 flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-heading font-extrabold text-sm text-slate-900" x-text="ticket.ticket_number"></span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-200/70 text-amber-900" x-text="ticket.source"></span>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-700 mt-0.5" x-text="ticket.passenger_name"></p>
                                    <p class="text-[11px] text-slate-500" x-text="ticket.route?.route_name + ' • Rp ' + Number(ticket.fare_amount).toLocaleString('id-ID')"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="callNext(ticket.id)"
                                            class="touch-target px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                                        <i class="fas fa-bullhorn text-[10px]"></i> Panggil
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-if="waitingTickets.length === 0">
                            <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 text-xs">
                                Tidak ada antrean penumpang yang menunggu.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ON TRIP TICKETS -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-heading font-bold text-lg text-slate-900 flex items-center gap-2">
                                <i class="fas fa-motorcycle text-blue-600"></i>
                                Driver Sedang di Perjalanan (On Trip)
                            </h3>
                            <p class="text-xs text-slate-500">Driver yang sedang mengantar penumpang ke pos/pasar.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-extrabold text-xs" x-text="onTripDrivers.length + ' Trip'"></span>
                    </div>

                    <div class="space-y-2.5 max-h-[200px] overflow-y-auto pr-1">
                        <template x-for="att in onTripDrivers" :key="att.id">
                            <div class="p-3 rounded-2xl bg-blue-50/50 border border-blue-200 flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-slate-900" x-text="att.driver?.name"></span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-200 text-blue-900" x-text="att.driver?.license_plate"></span>
                                    </div>
                                    <template x-if="att.tickets && att.tickets.length > 0">
                                        <p class="text-xs text-slate-600 mt-0.5" x-text="'Antar: ' + att.tickets[0].passenger_name + ' → ' + (att.tickets[0].route?.route_name || 'Tujuan')"></p>
                                    </template>
                                </div>
                                <template x-if="att.tickets && att.tickets.length > 0">
                                    <button type="button" @click="completeTrip(att.tickets[0].id)"
                                            class="touch-target px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs flex items-center gap-1">
                                        <i class="fas fa-check"></i> Selesai
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-if="onTripDrivers.length === 0">
                            <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 text-xs">
                                Tidak ada driver yang sedang di perjalanan.
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL: CHECK-IN DRIVER -->
        <div x-show="showCheckInModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showCheckInModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.stop>
                    <h3 class="font-heading font-extrabold text-xl text-slate-900 mb-1">Check-in Driver Masuk Antrean</h3>
                    <p class="text-xs text-slate-500 mb-4">Pilih nama driver paguyuban Cigedug yang hadir hari ini.</p>

                    <form action="{{ route('ojek.checkin') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Driver Resmi:</label>
                            <select name="driver_id" required class="w-full rounded-xl border border-slate-300 p-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- Pilih Driver --</option>
                                @foreach ($all_drivers as $d)
                                    <option value="{{ $d->id }}">
                                        {{ $d->name }} ({{ $d->license_plate }} - {{ $d->motorcycle_model }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6">
                            <button type="button" @click="showCheckInModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">
                                Masukkan ke Antrean
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: WALK-IN TICKET -->
        <div x-show="showWalkInModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showWalkInModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.stop>
                    <h3 class="font-heading font-extrabold text-xl text-slate-900 mb-1">+ Tiket Walk-in Pangkalan</h3>
                    <p class="text-xs text-slate-500 mb-4">Buat tiket penumpang langsung di pos pangkalan ojek.</p>

                    <div class="space-y-3.5 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Rute Ojek Resmi:</label>
                            <select x-model="walkInForm.ojek_route_id" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D97736]">
                                <template x-for="r in routes" :key="r.id">
                                    <option :value="r.id" x-text="r.route_name + ' (Rp ' + Number(r.fare_amount).toLocaleString('id-ID') + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Penumpang:</label>
                            <input type="text" x-model="walkInForm.passenger_name" placeholder="Misal: Andi / Rombongan Jakarta"
                                   class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D97736]">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">No. WhatsApp/HP (Opsional):</label>
                            <input type="text" x-model="walkInForm.passenger_phone" placeholder="08xxxxxxxxxx"
                                   class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D97736]">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Metode Pembayaran:</label>
                            <select x-model="walkInForm.payment_method" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D97736]">
                                <option value="CASH">Tunai (Cash di Pos Ojek)</option>
                                <option value="QRIS">QRIS Basecamp</option>
                                <option value="TRANSFER">Transfer Bank</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 mt-6">
                        <button type="button" @click="showWalkInModal = false"
                                class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="button" @click="submitWalkIn()"
                                :disabled="!walkInForm.passenger_name"
                                class="touch-target px-5 py-2.5 rounded-xl bg-[#D97736] hover:bg-[#b85e25] disabled:opacity-50 text-white text-xs font-bold shadow-md">
                            Simpan & Cetak Antrean
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function ojekCoordinator() {
            return {
                showCheckInModal: false,
                showWalkInModal: false,
                callingLoading: false,

                // Initial Data
                latestCalled: {{ Js::from($latest_called) }},
                standbyDrivers: {{ Js::from($standby_drivers) }},
                onTripDrivers: {{ Js::from($on_trip_drivers) }},
                waitingTickets: {{ Js::from($waiting_tickets) }},
                stats: {{ Js::from($summary) }},
                routes: {{ Js::from($routes) }},

                walkInForm: {
                    ojek_route_id: {{ $routes->first()?->id ?? 1 }},
                    passenger_name: '',
                    passenger_phone: '',
                    payment_method: 'CASH',
                },

                init() {
                    // Auto-poll real-time data every 4 seconds
                    setInterval(() => {
                        this.fetchData();
                    }, 4000);
                },

                async fetchData() {
                    try {
                        const res = await fetch('{{ route("ojek.api.realtime") }}');
                        if (res.ok) {
                            const data = await res.json();
                            this.latestCalled = data.latest_called;
                            this.standbyDrivers = data.standby_drivers;
                            this.onTripDrivers = data.on_trip_drivers;
                            this.waitingTickets = data.waiting_tickets;
                            this.stats = data.summary;
                        }
                    } catch (e) {
                        console.error('Fetch error:', e);
                    }
                },

                async callNext(ticketId = null, attendanceId = null) {
                    this.callingLoading = true;
                    try {
                        const res = await fetch('{{ route("ojek.call-next") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ticket_id: ticketId, attendance_id: attendanceId })
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.fetchData();
                        } else {
                            alert(data.message || 'Gagal memanggil antrean.');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.callingLoading = false;
                    }
                },

                async startTrip(ticketId) {
                    try {
                        const res = await fetch(`/ojek/trip/${ticketId}/start`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.fetchData();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Gagal memulai perjalanan.');
                    }
                },

                async completeTrip(ticketId) {
                    try {
                        const res = await fetch(`/ojek/trip/${ticketId}/complete`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.fetchData();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Gagal menyelesaikan perjalanan.');
                    }
                },

                async cancelTicket(ticketId) {
                    if (!confirm('Yakin ingin membatalkan tiket antrean ini?')) return;
                    try {
                        const res = await fetch(`/ojek/ticket/${ticketId}/cancel`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.fetchData();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Gagal membatalkan tiket.');
                    }
                },

                async setDriverStatus(attId, status) {
                    try {
                        const res = await fetch(`/ojek/attendance/${attId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ status })
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.fetchData();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Gagal memperbarui status driver.');
                    }
                },

                async submitWalkIn() {
                    try {
                        const res = await fetch('{{ route("ojek.walkin") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.walkInForm)
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.showWalkInModal = false;
                            this.walkInForm.passenger_name = '';
                            this.walkInForm.passenger_phone = '';
                            await this.fetchData();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Gagal membuat tiket.');
                    }
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>
