<x-layouts.app title="Tarif & Cek Antrean Ojek Resmi Gunung Cikuray Cigedug">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12"
         x-data="publicOjekChecker()">

        <!-- HERO HEADER -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#1E3A2B]/10 text-[#1E3A2B] text-xs font-bold border border-[#1E3A2B]/20 mb-3 shadow-xs">
                <i class="fas fa-motorcycle text-[#D97736]"></i> Paguyuban Ojek Resmi Basecamp SAMSARA
            </div>
            <h1 class="font-heading font-black text-3xl sm:text-4xl text-slate-900 tracking-tight">
                Layanan & Tarif Ojek Jalur Cigedug
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                Transportasi lokal resmi terintegrasi loket basecamp. Adil bagi pendaki, transparan bagi pengemudi warga lokal Cigedug.
            </p>
        </div>

        <!-- TICKET CHECKER CARD -->
        <div class="bg-[#111E17] text-white rounded-2xl p-6 sm:p-8 border border-[#2D5841] shadow-sm mb-10">
            <div class="max-w-xl mx-auto text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-amber-400/20 text-amber-300 text-xs font-semibold mb-2">
                    <i class="fas fa-search text-amber-400"></i> Cek Status Antrean Saya
                </span>
                <h2 class="font-heading font-extrabold text-2xl text-white">
                    Punya Tiket Ojek dari Kasir / Web?
                </h2>
                <p class="text-xs text-white/70 mt-1 mb-5">
                    Masukkan nomor tiket ojek Anda (misal: <strong class="text-amber-300 font-mono">OJ-261101-001</strong>) untuk melihat posisi antrean & driver yang menjemput.
                </p>

                <form @submit.prevent="checkTicket()" class="flex flex-col sm:flex-row gap-2">
                    <input type="text" x-model="ticketCode" placeholder="Contoh: OJ-261101-002" required
                           class="flex-1 rounded-xl bg-white/10 border border-white/20 px-3.5 py-2.5 text-sm text-white placeholder-slate-400 font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-[#D97736]">
                    <button type="submit" :disabled="loading"
                            class="touch-target px-5 py-2.5 rounded-xl bg-[#D97736] hover:bg-[#c26527] disabled:opacity-50 text-white font-semibold text-xs sm:text-sm shadow-xs transition active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-right" :class="{ 'animate-spin': loading }"></i>
                        <span>Cek Antrean</span>
                    </button>
                </form>

                <!-- TICKET RESULT CARD -->
                <div x-show="ticketResult" x-cloak class="mt-6 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-left" style="display: none;">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/10">
                        <div>
                            <span class="text-[11px] text-slate-300 uppercase tracking-wider block">Nomor Tiket:</span>
                            <span class="font-heading font-black text-xl text-amber-300" x-text="ticketResult?.ticket_number"></span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold"
                              :class="ticketResult?.status_badge"
                              x-text="ticketResult?.status_label"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400 block">Penumpang:</span>
                            <span class="font-bold text-white text-sm" x-text="ticketResult?.passenger_name"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Rute Tujuan:</span>
                            <span class="font-bold text-white text-sm" x-text="ticketResult?.route_name"></span>
                        </div>
                        <template x-if="ticketResult?.driver_name">
                            <div class="col-span-1 sm:col-span-2 pt-2 border-t border-white/10 bg-emerald-950/60 p-3 rounded-xl border border-emerald-500/30">
                                <span class="text-emerald-300 text-[11px] font-bold uppercase tracking-wider block">Driver Bertugas:</span>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="font-bold text-white text-base" x-text="ticketResult?.driver_name + ' (' + (ticketResult?.driver_motorcycle || 'Motor') + ')'"></span>
                                    <span class="font-mono font-black text-amber-300 text-sm px-2.5 py-1 rounded-lg bg-black/40 border border-white/10" x-text="ticketResult?.driver_plate"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="ticketResult?.status === 'WAITING'">
                            <div class="col-span-1 sm:col-span-2 bg-amber-500/20 p-3 rounded-xl border border-amber-400/30 text-amber-200">
                                <i class="fas fa-clock mr-1"></i>
                                Terdapat <strong><span x-text="ticketResult?.ahead_count"></span> antrean</strong> di depan Anda. Mohon bersiap di area tunggu pangkalan basecamp.
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="errorMessage" x-cloak class="mt-4 p-3 rounded-xl bg-rose-500/20 border border-rose-400/30 text-rose-200 text-xs text-center" style="display: none;">
                    <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="errorMessage"></span>
                </div>
            </div>
        </div>

        <!-- TARIFF TABLE & ROUTES -->
        <div class="mb-12">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <h2 class="font-heading font-extrabold text-2xl text-slate-900 flex items-center gap-2">
                        <i class="fas fa-tags text-[#D97736]"></i>
                        Tarif Resmi Ojek Jalur Cigedug
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tarif seragam paguyuban yang telah disepakati bersama pengelola SAMSARA.</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                    <i class="fas fa-check-shield text-emerald-600"></i> Bebas Pungli / Tarif Baku
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($routes as $r)
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-xs hover:border-[#D97736]/40 transition group">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold mb-2">
                                    <i class="fas fa-clock text-slate-400"></i> ±{{ $r->estimated_duration_minutes }} Menit
                                </span>
                                <h3 class="font-heading font-extrabold text-base sm:text-lg text-slate-900 group-hover:text-emerald-900 transition">
                                    {{ $r->route_name }}
                                </h3>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                    <span>{{ $r->origin }}</span>
                                    <i class="fas fa-arrow-right text-[10px] text-slate-400"></i>
                                    <span>{{ $r->destination }}</span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Tarif:</span>
                                <span class="font-heading font-black text-xl sm:text-2xl text-[#D97736] block">
                                    Rp {{ number_format($r->fare_amount, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400">per orang/motor</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span><i class="fas fa-id-badge text-emerald-600 mr-1"></i> Termasuk jaminan keselamatan</span>
                            <span class="font-medium text-emerald-700">Bayar di Loket / Driver</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SOP & SAFETY GUIDELINES -->
        <div class="bg-amber-50/60 rounded-3xl p-6 sm:p-8 border border-amber-200/80">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg">
                    <i class="fas fa-helmet-safety"></i>
                </div>
                <div>
                    <h3 class="font-heading font-extrabold text-lg text-slate-900">SOP & Keselamatan Naik Ojek Cikuray</h3>
                    <p class="text-xs text-slate-600">Patuhi aturan demi keselamatan bersama di medan tanjakan berbatu kebun teh.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 text-xs text-slate-700">
                <div class="bg-white p-4 rounded-2xl border border-amber-200">
                    <strong class="font-bold text-slate-900 block mb-1">1. Wajib Gunakan Helm</strong>
                    Driver resmi paguyuban Cigedug menyediakan helm bersih untuk seluruh penumpang.
                </div>
                <div class="bg-white p-4 rounded-2xl border border-amber-200">
                    <strong class="font-bold text-slate-900 block mb-1">2. Batas Ransel Carrier</strong>
                    Ransel maksimal 60L di punggung penumpang demi menjaga keseimbangan motor trail di tanjakan terjal.
                </div>
                <div class="bg-white p-4 rounded-2xl border border-amber-200">
                    <strong class="font-bold text-slate-900 block mb-1">3. Tiket Resmi Kasir</strong>
                    Simpan bukti pembayaran tiket ojek Anda hingga sampai di tujuan sebagai jaminan resmi.
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function publicOjekChecker() {
            return {
                ticketCode: '',
                loading: false,
                ticketResult: null,
                errorMessage: '',

                async checkTicket() {
                    this.loading = true;
                    this.ticketResult = null;
                    this.errorMessage = '';

                    try {
                        const res = await fetch(`/ojek/api/ticket/check?code=${encodeURIComponent(this.ticketCode)}`);
                        const data = await res.json();

                        if (data.success) {
                            this.ticketResult = data.ticket;
                        } else {
                            this.errorMessage = data.message || 'Tiket tidak ditemukan.';
                        }
                    } catch (e) {
                        this.errorMessage = 'Terjadi kesalahan jaringan saat mengecek tiket.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-layouts.app>
