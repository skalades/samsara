<div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs mb-12 scroll-mt-24" id="kalkulator-biaya"
     x-data="{
         climbers: 4,
         isWna: false,
         motorCount: 2,
         mobilCount: 0,
         hasPorter: false,
         hasGuide: false,
         rentTent4p: 1,
         rentSleepingBag: 0,

         get ticketPerPerson() {
             return this.isWna ? 100000 : 25000;
         },
         get totalTickets() {
             return this.climbers * this.ticketPerPerson;
         },
         get ecoDeposit() {
             return 20000; // Rp 20.000 per rombongan (refundable)
         },
         get totalParking() {
             return (this.motorCount * 10000) + (this.mobilCount * 25000);
         },
         get totalServices() {
             let total = 0;
             if (this.hasPorter) total += 350000;
             if (this.hasGuide) total += 300000;
             total += (this.rentTent4p * 50000);
             total += (this.rentSleepingBag * 15000);
             return total;
         },
         get grandTotal() {
             return this.totalTickets + this.ecoDeposit + this.totalParking + this.totalServices;
         },
         get perPersonEstimate() {
             return this.climbers > 0 ? Math.round(this.grandTotal / this.climbers) : 0;
         },
         formatRp(num) {
             return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
         },
         generateWaLink() {
             const porterText = this.hasPorter ? 'Ya (1 Porter)' : 'Tidak';
             const guideText = this.hasGuide ? 'Ya (1 Guide)' : 'Tidak';
             const kewarganegaraan = this.isWna ? 'WNA' : 'WNI';

             const text = `Salam Lestari Basecamp SAMSARA Cigedug!\n` +
                          `Saya ingin konfirmasi kuota dan estimasi biaya pendakian Cikuray:\n\n` +
                          `📋 *DATA ESTIMASI ROMBONGAN*\n` +
                          `• Jumlah Pendaki: ${this.climbers} Orang (${kewarganegaraan})\n` +
                          `• Tiket SIMAKSI: ${this.formatRp(this.totalTickets)}\n` +
                          `• Jaminan Sampah (Refundable): ${this.formatRp(this.ecoDeposit)}\n` +
                          `• Parkir Kendaraan: Motor ${this.motorCount} unit, Mobil ${this.mobilCount} unit (${this.formatRp(this.totalParking)})\n` +
                          `• Jasa Porter: ${porterText}\n` +
                          `• Jasa Guide: ${guideText}\n` +
                          `• Sewa Tenda 4P: ${this.rentTent4p} Unit\n` +
                          `• Sewa Sleeping Bag: ${this.rentSleepingBag} Unit\n\n` +
                          `💰 *ESTIMASI TOTAL BIAYA*: ${this.formatRp(this.grandTotal)}\n` +
                          `(Patungan sekitar ${this.formatRp(this.perPersonEstimate)} / orang)\n\n` +
                          `Mohon info ketersediaan slot tanggal pendakian. Terima kasih!`;

             return 'https://wa.me/6281223456789?text=' + encodeURIComponent(text);
         }
     }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4 mb-6">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] block mb-1">
                <i class="fas fa-calculator"></i> Transparansi Retribusi Resmi
            </span>
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">
                Kalkulator Biaya Pendakian Interaktif
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Simulasikan biaya tiket SIMAKSI, jaminan sampah, parkir kendaraan, dan layanan sewa sebelum berangkat ke Garut.
            </p>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 shrink-0">
            <i class="fas fa-check-shield text-emerald-600"></i> Bebas Pungli 100%
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Interactive Inputs Column (7 Cols) -->
        <div class="lg:col-span-7 space-y-5">
            <!-- 1. Jumlah Pendaki & Kewarganegaraan -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i class="fas fa-users text-[#D97736]"></i> 1. Jumlah Pendaki & Kewarganegaraan
                    </label>
                    <span class="text-xs font-bold text-slate-500 font-mono" x-text="climbers + ' Orang'"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Stepper -->
                    <div>
                        <span class="text-[11px] text-slate-500 block mb-1">Jumlah Rombongan</span>
                        <div class="flex items-center">
                            <button type="button" @click="if (climbers > 1) climbers--"
                                    class="w-10 h-10 rounded-l-xl bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold transition flex items-center justify-center cursor-pointer">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <input type="number" min="1" max="50" x-model.number="climbers"
                                   class="w-full h-10 border-y border-slate-300 text-center font-bold text-sm bg-white focus:outline-none">
                            <button type="button" @click="if (climbers < 50) climbers++"
                                    class="w-10 h-10 rounded-r-xl bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold transition flex items-center justify-center cursor-pointer">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Citizenship Switcher -->
                    <div>
                        <span class="text-[11px] text-slate-500 block mb-1">Kewarganegaraan</span>
                        <div class="grid grid-cols-2 gap-1.5 p-1 bg-white border border-slate-300 rounded-xl">
                            <button type="button" @click="isWna = false"
                                    class="py-1.5 rounded-lg text-xs font-bold transition text-center cursor-pointer"
                                    :class="!isWna ? 'bg-[#1E3A2B] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-50'">
                                WNI (25rb)
                            </button>
                            <button type="button" @click="isWna = true"
                                    class="py-1.5 rounded-lg text-xs font-bold transition text-center cursor-pointer"
                                    :class="isWna ? 'bg-[#1E3A2B] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-50'">
                                WNA (100rb)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Kendaraan Parkir Inap -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-3">
                <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <i class="fas fa-square-parking text-purple-600"></i> 2. Parkir Kendaraan Menginap di Basecamp
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Motor -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Sepeda Motor</span>
                            <span class="text-[10px] text-slate-400">Rp 10.000 / unit</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="if (motorCount > 0) motorCount--" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">-</button>
                            <span class="w-6 text-center text-xs font-bold" x-text="motorCount"></span>
                            <button type="button" @click="motorCount++" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">+</button>
                        </div>
                    </div>

                    <!-- Mobil -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Mobil Pribadi</span>
                            <span class="text-[10px] text-slate-400">Rp 25.000 / unit</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="if (mobilCount > 0) mobilCount--" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">-</button>
                            <span class="w-6 text-center text-xs font-bold" x-text="mobilCount"></span>
                            <button type="button" @click="mobilCount++" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Porter, Guide & Unit Sewa Alat -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-3">
                <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <i class="fas fa-campground text-teal-600"></i> 3. Tambahan Opsional (Porter & Sewa Alat)
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Porter -->
                    <label class="p-3 bg-white rounded-xl border border-slate-200 flex items-center gap-3 cursor-pointer hover:border-slate-300 transition">
                        <input type="checkbox" x-model="hasPorter" class="rounded text-[#D97736] focus:ring-[#D97736] w-4 h-4">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Porter Lokal</span>
                            <span class="text-[10px] text-slate-400">+Rp 350.000 (Maks 20kg)</span>
                        </div>
                    </label>

                    <!-- Guide -->
                    <label class="p-3 bg-white rounded-xl border border-slate-200 flex items-center gap-3 cursor-pointer hover:border-slate-300 transition">
                        <input type="checkbox" x-model="hasGuide" class="rounded text-[#D97736] focus:ring-[#D97736] w-4 h-4">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Guide Pendamping</span>
                            <span class="text-[10px] text-slate-400">+Rp 300.000 / trip</span>
                        </div>
                    </label>

                    <!-- Tenda 4P -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Tenda Dome 4P</span>
                            <span class="text-[10px] text-slate-400">+Rp 50.000 / unit</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="if (rentTent4p > 0) rentTent4p--" class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">-</button>
                            <span class="w-5 text-center text-xs font-bold" x-text="rentTent4p"></span>
                            <button type="button" @click="rentTent4p++" class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">+</button>
                        </div>
                    </div>

                    <!-- Sleeping Bag -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Sleeping Bag Polar</span>
                            <span class="text-[10px] text-slate-400">+Rp 15.000 / unit</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="if (rentSleepingBag > 0) rentSleepingBag--" class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">-</button>
                            <span class="w-5 text-center text-xs font-bold" x-text="rentSleepingBag"></span>
                            <button type="button" @click="rentSleepingBag++" class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Receipt Breakdown Column (5 Cols) -->
        <div class="lg:col-span-5 bg-[#111E17] text-white p-6 rounded-2xl border border-[#2D5841] shadow-md space-y-4">
            <div class="flex items-center justify-between border-b border-white/15 pb-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-300">Rincian Estimasi Biaya</span>
                    <h4 class="font-heading font-extrabold text-lg text-white">Ringkasan Retribusi</h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-amber-300 text-lg">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>

            <!-- Breakdown Lines -->
            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-300">
                        Tiket SIMAKSI (<span x-text="climbers"></span> org &times; <span x-text="formatRp(ticketPerPerson)"></span>):
                    </span>
                    <span class="font-bold text-white font-mono" x-text="formatRp(totalTickets)"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-emerald-300 flex items-center gap-1">
                        <i class="fas fa-shield-halved text-[10px]"></i> Eco-Deposit Sampah:
                    </span>
                    <span class="font-bold text-emerald-300 font-mono" x-text="formatRp(ecoDeposit)"></span>
                </div>
                <span class="text-[10px] text-emerald-200/70 -mt-1 block italic">*100% Refundable saat turun membawa sampah</span>

                <div class="flex items-center justify-between" x-show="totalParking > 0">
                    <span class="text-slate-300">Parkir Kendaraan:</span>
                    <span class="font-bold text-white font-mono" x-text="formatRp(totalParking)"></span>
                </div>

                <div class="flex items-center justify-between" x-show="totalServices > 0">
                    <span class="text-slate-300">Layanan & Sewa Alat:</span>
                    <span class="font-bold text-amber-300 font-mono" x-text="formatRp(totalServices)"></span>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="pt-4 border-t border-white/15">
                <div class="flex items-end justify-between">
                    <div>
                        <span class="text-[11px] text-slate-300 font-medium">Estimasi Total Rombongan:</span>
                        <div class="font-heading font-extrabold text-2xl sm:text-3xl text-amber-300" x-text="formatRp(grandTotal)"></div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block">Patungan per orang:</span>
                        <span class="text-xs font-bold text-emerald-400 font-mono" x-text="'~ ' + formatRp(perPersonEstimate)"></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 space-y-2">
                <a href="{{ route('simaksi.create') }}"
                   class="touch-target w-full py-3 rounded-lg bg-[#D97736] hover:bg-[#B25A20] text-white font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 active:scale-95">
                    <i class="fas fa-ticket-alt"></i> Langsung Booking SIMAKSI
                </a>

                <a :href="generateWaLink()" target="_blank"
                   class="touch-target w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-xs active:scale-95">
                    <i class="fab fa-whatsapp text-sm"></i> Kirim Estimasi via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
