<x-layouts.admin title="Scanner Lapangan Pos 1 - 4 PWA">
    <div x-data="{
        currentPos: '{{ $staffPos }}',
        direction: 'ASCENDING',
        qrData: '',
        notes: '',
        offlineQueueCount: 0,
        scanResult: null,
        errorMessage: null,
        isScanning: false,

        // SOS Modal
        showSosModal: false,
        sosReporter: 'Petugas Jalur SAMSARA',
        sosPhone: '0812-9876-5432',
        sosType: 'ACCIDENT',
        sosDesc: '',

        // Waste Modal
        showWasteModal: false,
        wasteBookingId: '',
        wastePlastics: 6,
        wasteCans: 2,
        wasteBottles: 4,
        wasteResult: null,

        init() {
            this.offlineQueueCount = window.FieldOpsOfflineQueue ? window.FieldOpsOfflineQueue.count() : 0;
        },

        async submitScan() {
            if (!this.qrData.trim()) return;
            this.errorMessage = null;
            this.scanResult = null;

            const payload = {
                qr_data: this.qrData.trim(),
                pos_name: this.currentPos,
                direction: this.direction,
                notes: this.notes
            };

            // Check if online or offline
            if (!navigator.onLine) {
                window.FieldOpsOfflineQueue.add(payload);
                this.offlineQueueCount = window.FieldOpsOfflineQueue.count();
                this.scanResult = {
                    success: true,
                    message: `[MODE OFFLINE] Data ${payload.qr_data} tersimpan di antrean offline HP petugas (${this.offlineQueueCount} antrean). Akan disinkronkan saat sinyal tersedia.`
                };
                this.qrData = '';
                return;
            }

            try {
                const res = await axios.post('{{ route('fieldops.scan') }}', payload);
                if (res.data.success) {
                    this.scanResult = res.data;
                    this.qrData = '';
                    this.notes = '';
                }
            } catch (err) {
                if (err.response?.status === 422) {
                    this.errorMessage = 'Peringatan Jalur: ' + (err.response?.data?.message || err.message);
                } else {
                    // Hanya simpan ke antrean offline jika jaringan HP putus / server offline
                    window.FieldOpsOfflineQueue.add(payload);
                    this.offlineQueueCount = window.FieldOpsOfflineQueue.count();
                    this.errorMessage = (err.response?.data?.message || err.message) + ' -> Disimpan ke Antrean Offline lokal HP.';
                }
            }
        },

        async syncOfflineQueue() {
            const queue = window.FieldOpsOfflineQueue.getQueue();
            if (queue.length === 0) {
                alert('Tidak ada antrean scan offline.');
                return;
            }

            try {
                const res = await axios.post('{{ route('fieldops.sync') }}', { queue });
                if (res.data.success) {
                    window.FieldOpsOfflineQueue.clear();
                    this.offlineQueueCount = 0;
                    alert(`Berhasil sinkronisasi ${res.data.synced_count} pemindaian ke server pusat Basecamp!`);
                    location.reload();
                }
            } catch (err) {
                alert('Gagal sinkronisasi: ' + err.message);
            }
        },

        async submitSos() {
            if (!this.sosDesc) return;
            try {
                const res = await axios.post('{{ route('fieldops.sos') }}', {
                    reporter_name: this.sosReporter,
                    reporter_phone: this.sosPhone,
                    pos_location: this.currentPos,
                    alert_type: this.sosType,
                    description: this.sosDesc
                });
                if (res.data.success) {
                    alert('ALERT DARURAT SOS TELAH DISEBARKAN KE SELURUH POS & SAR!');
                    this.showSosModal = false;
                    this.sosDesc = '';
                    location.reload();
                }
            } catch (err) {
                alert('Gagal mengirim SOS: ' + err.message);
            }
        },

        async submitWaste() {
            try {
                const res = await axios.post('{{ route('fieldops.waste') }}', {
                    booking_id: this.wasteBookingId,
                    returned_plastics_count: this.wastePlastics,
                    returned_cans_count: this.wasteCans,
                    returned_bottles_count: this.wasteBottles
                });
                this.wasteResult = res.data;
            } catch (err) {
                alert('Gagal verifikasi sampah: ' + err.message);
            }
        }
    }">

        <!-- POS BAR TOP ACTION -->
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-3xl border border-slate-200 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-[#1E3A2B] text-white flex items-center justify-center text-xl shadow-xs">
                    <i class="fas fa-qrcode text-[#D97736]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-heading font-black text-base sm:text-lg text-slate-900 leading-tight">Scanner Petugas Jalur</h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">PWA Offline-First</span>
                    </div>
                    <p class="text-xs text-slate-500">
                        Check-in & Tracking Manifest Rombongan Pos 1 s/d Pos 4
                    </p>
                </div>
            </div>

            <!-- Emergency SOS & Offline Queue Status -->
            <div class="flex items-center gap-2">
                <!-- Offline Queue Pill -->
                <button type="button" @click="syncOfflineQueue()" class="touch-target px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                    :class="offlineQueueCount > 0 ? 'bg-amber-100 text-amber-900 border-amber-300 animate-pulse' : 'bg-slate-50 text-slate-600 border-slate-200'">
                    <i class="fas fa-cloud-arrow-up text-[#D97736]"></i>
                    <span>Antrean Offline (<span x-text="offlineQueueCount"></span>)</span>
                </button>

                <!-- SOS Emergency Red Button -->
                <button type="button" @click="showSosModal = true" class="touch-target px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-md transition flex items-center gap-1.5 active:scale-95">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>SOS DARURAT</span>
                </button>
            </div>
        </div>

        @if ($activeSos->isNotEmpty())
            <!-- ACTIVE SOS NOTIFICATION BAR -->
            <div class="mb-5 p-4 rounded-3xl bg-rose-500 text-white shadow-lg border border-rose-600 animate-pulse">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-ambulance text-2xl text-amber-300"></i>
                        <div>
                            <div class="font-heading font-extrabold text-sm uppercase tracking-wide">PERINGATAN SAR AKTIF DI JALUR</div>
                            <p class="text-xs text-rose-100 mt-0.5">
                                {{ $activeSos->first()->alert_type->label() }} di {{ $activeSos->first()->pos_location }} &bull; Pelapor: {{ $activeSos->first()->reporter_name }} ({{ $activeSos->first()->reporter_phone }})
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.index') }}" class="touch-target px-3 py-1.5 rounded-xl bg-white text-rose-800 text-xs font-bold shadow">
                        Buka SAR Console
                    </a>
                </div>
            </div>
        @endif

        <!-- SCANNER WORKSPACE -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- SCAN CONTROLLER CARD (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
                    <!-- Current Pos & Direction Switcher (Mobile Thumb-Zone) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Lokasi Pos Penugasan</label>
                            <select x-model="currentPos" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold text-slate-900 bg-slate-50">
                                <option value="Basecamp">Basecamp SAMSARA</option>
                                <option value="Pos 1 (Tanjakan EOD)">Pos 1 (Tanjakan EOD - 1.500m)</option>
                                <option value="Pos 2 (Batu Gendrung)">Pos 2 (Batu Gendrung - 1.850m)</option>
                                <option value="Pos 3 (Alam Rimba)">Pos 3 (Alam Rimba - 2.200m)</option>
                                <option value="Pos 4 (Samudra Awan)">Pos 4 (Samudra Awan - 2.500m)</option>
                                <option value="Puncak Cikuray">Puncak Cikuray (2.821 MDPL)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Arah Gerak Rombongan</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="button" @click="direction = 'ASCENDING'" :class="direction === 'ASCENDING' ? 'bg-[#1E3A2B] text-white shadow' : 'bg-slate-100 text-slate-700'" class="touch-target py-2 rounded-xl text-xs font-bold transition">
                                    <i class="fas fa-arrow-up mr-1 text-emerald-400"></i> Naik
                                </button>
                                <button type="button" @click="direction = 'DESCENDING'" :class="direction === 'DESCENDING' ? 'bg-[#D97736] text-white shadow' : 'bg-slate-100 text-slate-700'" class="touch-target py-2 rounded-xl text-xs font-bold transition">
                                    <i class="fas fa-arrow-down mr-1"></i> Turun
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Scan Input Bar (Accepts Camera QR string or typing booking code) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5 flex items-center justify-between">
                            <span>Scan QR / Masukkan Kode Booking</span>
                            <span class="text-[10px] text-slate-400 font-normal">Contoh: QR-DEMO-SMX-A101 atau SMX-xxxxx</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" x-model="qrData" @keydown.enter.prevent="submitScan()" placeholder="Arahkan kamera atau ketik kode..."
                                class="touch-target flex-1 rounded-2xl border-2 border-slate-300 focus:border-[#1E3A2B] px-4 py-3 text-sm font-mono font-bold text-slate-900 bg-slate-50/50">

                            <button type="button" @click="submitScan()" class="touch-target px-5 py-3 rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-paper-plane text-[#D97736]"></i>
                                <span>Proses</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Catatan Lapangan (Opsional)</label>
                        <input type="text" x-model="notes" placeholder="Contoh: Rombongan sehat, membawa 1 inhaler, istirahat 15 menit..."
                            class="touch-target w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs text-slate-800">
                    </div>

                    <!-- Quick Demo QR Buttons for Fast Testing in Chat/Browser -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs">
                        <span class="text-[11px] font-bold text-slate-500 block mb-1.5">Shortcut Uji Coba Cepat (Tap untuk Input):</span>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="qrData = 'QR-DEMO-SMX-A101'; submitScan()" class="px-2.5 py-1 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 text-[11px] font-mono font-bold">
                                SMX-A101 (Bayu Pratama - 4 Org)
                            </button>
                            <button type="button" @click="qrData = 'QR-DEMO-SMX-B202'; submitScan()" class="px-2.5 py-1 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 text-[11px] font-mono font-bold">
                                SMX-B202 (Dimas Arya - 3 Org)
                            </button>
                        </div>
                    </div>

                    <!-- Scan Feedback Result Box -->
                    <div x-show="scanResult" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 space-y-2 text-xs">
                        <div class="flex items-center gap-2 font-bold text-emerald-800">
                            <i class="fas fa-check-circle text-base text-emerald-600"></i>
                            <span x-text="scanResult?.message"></span>
                        </div>

                        <template x-if="scanResult?.booking">
                            <div class="pt-2 border-t border-emerald-200 text-xs space-y-1">
                                <div>Ketua: <b x-text="scanResult.booking.leader_name"></b> (<span x-text="scanResult.booking.total_hikers"></span> orang)</div>
                                <div>Tenda: <span x-text="scanResult.booking.tent_color_details || 'Tidak ada catatan'"></span></div>
                                <div>Emergency: <span x-text="scanResult.booking.emergency_contact_name + ' (' + scanResult.booking.emergency_contact_phone + ')'"></span></div>
                            </div>
                        </template>
                    </div>

                    <div x-show="errorMessage" class="p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-600"></i>
                        <span x-text="errorMessage"></span>
                    </div>
                </div>

                <!-- ECO-DEPOSIT REFUND VERIFICATION CARD (For Pos Basecamp / Turun) -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-trash-arrow-up"></i>
                            </span>
                            <h3 class="font-heading font-extrabold text-sm text-slate-900">Verifikasi Sampah (Eco-Deposit)</h3>
                        </div>
                        <span class="text-xs text-emerald-700 font-bold">Basecamp Loket Turun</span>
                    </div>

                    <p class="text-xs text-slate-500">Hitung jumlah sampah anorganik yang dibawa turun oleh rombongan untuk mengembalikan uang jaminan Rp 20.000.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Booking ID</label>
                            <input type="number" x-model="wasteBookingId" placeholder="ID (cth: 1)" class="w-full text-xs rounded-lg border border-slate-300 p-2">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Plastik / Bungkus</label>
                            <input type="number" x-model="wastePlastics" class="w-full text-xs rounded-lg border border-slate-300 p-2 text-center font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Kaleng Gas / Logistik</label>
                            <input type="number" x-model="wasteCans" class="w-full text-xs rounded-lg border border-slate-300 p-2 text-center font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Botol Minum</label>
                            <input type="number" x-model="wasteBottles" class="w-full text-xs rounded-lg border border-slate-300 p-2 text-center font-bold">
                        </div>
                    </div>

                    <button type="button" @click="submitWaste()" class="touch-target w-full rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                        <i class="fas fa-check"></i> Cek & Setujui Refund Jaminan Sampah
                    </button>

                    <div x-show="wasteResult" class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold" x-text="wasteResult?.message"></div>
                </div>
            </div>

            <!-- RIGHT AREA: LIVE MANIFEST LOG & RECENT SCANS (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5">
                    <h3 class="font-heading font-extrabold text-sm text-slate-900 mb-3 flex items-center justify-between">
                        <span>Aktivitas Pemindaian Jalur</span>
                        <span class="text-xs text-slate-400 font-normal">Real-Time Log</span>
                    </h3>

                    <div class="space-y-2.5 max-h-[520px] overflow-y-auto pr-1">
                        @forelse ($recentCheckpoints as $cp)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900">{{ $cp->booking->leader_name }}</span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full {{ $cp->direction === 'ASCENDING' ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $cp->direction === 'ASCENDING' ? '↑ Naik' : '↓ Turun' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                    <span><i class="fas fa-map-marker-alt text-[#D97736] mr-1"></i> {{ $cp->pos_name }}</span>
                                    <span>{{ $cp->created_at->format('H:i') }} WIB</span>
                                </div>

                                @if ($cp->notes)
                                    <div class="text-[10px] text-slate-400 italic">"{{ $cp->notes }}"</div>
                                @endif
                            </div>
                        @empty
                            <div class="py-8 text-center text-slate-400 text-xs">Belum ada aktivitas checkpoint hari ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- SOS EMERGENCY TRIGGER MODAL -->
        <div x-show="showSosModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showSosModal" @click="showSosModal = false" class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs"></div>

                <div x-show="showSosModal" class="inline-block bg-white rounded-3xl p-6 text-left shadow-2xl max-w-md w-full border border-rose-300 relative z-10 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-rose-100">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm font-black">
                                <i class="fas fa-triangle-exclamation"></i>
                            </span>
                            <h3 class="font-heading font-black text-base text-rose-900">Trigger Alert Darurat (SOS)</h3>
                        </div>
                        <button type="button" @click="showSosModal = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Keadaan Darurat</label>
                        <select x-model="sosType" class="touch-target w-full rounded-xl border border-slate-300 p-2 text-xs font-bold text-slate-900">
                            <option value="ACCIDENT">Kecelakaan Fisik / Cedera Jalur</option>
                            <option value="HYPOTHERMIA">Hipotermia Akut / Drop Suhu</option>
                            <option value="MEDICAL">Kondisi Medis Kritis / Sakit Berat</option>
                            <option value="LOST">Tersesat / Terpisah dari Kelompok</option>
                            <option value="WEATHER_TRAPPED">Terjebak Cuaca Ekstrem / Pohon Tumbang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Kondisi & Korban</label>
                        <textarea x-model="sosDesc" rows="3" required placeholder="Sebutkan ciri-ciri pakaian, titik koordinat perkiraan, kondisi sadar/tidak..."
                            class="w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900"></textarea>
                    </div>

                    <button type="button" @click="submitSos()" class="touch-target w-full rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-heading font-black text-xs shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-broadcast-tower"></i>
                        <span>BROADCAST KE SELURUH POS & TIM SAR SEKARANG</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
