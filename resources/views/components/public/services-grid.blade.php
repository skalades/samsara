<div class="mb-14">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 px-1 gap-2">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] flex items-center gap-1.5">
                <i class="fas fa-compass"></i> Layanan Mandiri Pendaki
            </span>
            <h2 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 mt-1">
                Persiapkan Pendakian Anda dalam Hitungan Menit
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Layanan instan tanpa registrasi akun yang rumit &mdash; cukup pesan langsung dari browser ponsel Anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">
        <!-- 1. Booking SIMAKSI -->
        <a href="{{ route('simaksi.index') }}" class="group bg-white hover:bg-emerald-50/40 p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-400 transition-all duration-300 flex flex-col justify-between">
            <div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-600 to-[#1E3A2B] text-white flex items-center justify-center text-lg mb-4 group-hover:scale-110 transition duration-300 shadow-md">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="inline-block text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-0.5 rounded-full mb-2">
                    Resmi & Berasuransi
                </div>
                <h3 class="font-heading font-extrabold text-lg text-slate-900 group-hover:text-[#1E3A2B]">SIMAKSI & Kuota</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Pilih tanggal pendakian, cek kalender sisa kuota harian, isi manifest rombongan, dan terbitkan QR E-Ticket resmi.</p>
            </div>
            <div class="mt-5 pt-3.5 border-t border-slate-100 text-xs font-bold text-[#D97736] flex items-center justify-between">
                <span>Mulai Reservasi</span>
                <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1.5 transition duration-200"></i>
            </div>
        </a>

        <!-- 2. Sewa Alat Outdoor -->
        <a href="{{ route('rental.catalog') }}" class="group bg-white hover:bg-teal-50/40 p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-teal-400 transition-all duration-300 flex flex-col justify-between">
            <div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-600 to-teal-900 text-white flex items-center justify-center text-lg mb-4 group-hover:scale-110 transition duration-300 shadow-md">
                    <i class="fas fa-campground"></i>
                </div>
                <div class="inline-block text-[10px] font-bold text-teal-700 bg-teal-100/80 px-2.5 py-0.5 rounded-full mb-2">
                    QC Standar Basecamp
                </div>
                <h3 class="font-heading font-extrabold text-lg text-slate-900 group-hover:text-teal-900">Sewa Alat Outdoor</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Katalog tenda dome (2P/4P), sleeping bag polar tebal, matras, nesting & kompor siap pakai di Basecamp SAMSARA.</p>
            </div>
            <div class="mt-5 pt-3.5 border-t border-slate-100 text-xs font-bold text-[#D97736] flex items-center justify-between">
                <span>Lihat Unit Sewa</span>
                <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1.5 transition duration-200"></i>
            </div>
        </a>

        <!-- 3. Pre-Order & Toko Logistik -->
        <a href="{{ route('store.index') }}" class="group bg-white hover:bg-amber-50/40 p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-400 transition-all duration-300 flex flex-col justify-between">
            <div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#D97736] to-amber-700 text-white flex items-center justify-center text-lg mb-4 group-hover:scale-110 transition duration-300 shadow-md">
                    <i class="fas fa-store"></i>
                </div>
                <div class="inline-block text-[10px] font-bold text-amber-800 bg-amber-100/80 px-2.5 py-0.5 rounded-full mb-2">
                    UMKM Warga Cigedug
                </div>
                <h3 class="font-heading font-extrabold text-lg text-slate-900 group-hover:text-amber-900">Pre-Order & Toko</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Pesan gas Hi-Cook, logistik pangan, kopi Robusta Cikuray & madu odeng asli. Ambil cepat di kasir loket (*Click & Collect*).</p>
            </div>
            <div class="mt-5 pt-3.5 border-t border-slate-100 text-xs font-bold text-[#D97736] flex items-center justify-between">
                <span>Belanja Logistik</span>
                <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1.5 transition duration-200"></i>
            </div>
        </a>

        <!-- 4. Ojek Paguyuban Cigedug -->
        <a href="{{ route('ojek.public.index') }}" class="group bg-white hover:bg-orange-50/40 p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-orange-400 transition-all duration-300 flex flex-col justify-between">
            <div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-600 to-orange-700 text-white flex items-center justify-center text-lg mb-4 group-hover:scale-110 transition duration-300 shadow-md">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div class="inline-block text-[10px] font-bold text-amber-800 bg-amber-100/80 px-2.5 py-0.5 rounded-full mb-2">
                    Paguyuban Warga
                </div>
                <h3 class="font-heading font-extrabold text-lg text-slate-900 group-hover:text-orange-900">Ojek Cigedug</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Tarif resmi rute Pasar - Basecamp - Pos 1. Cek antrean giliran driver dan TV display pangkalan.</p>
            </div>
            <div class="mt-5 pt-3.5 border-t border-slate-100 text-xs font-bold text-[#D97736] flex items-center justify-between">
                <span>Cek Antrean Ojek</span>
                <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1.5 transition duration-200"></i>
            </div>
        </a>

        <!-- 5. Cek Booking & Tiket Saya -->
        <button type="button" @click="$dispatch('open-ticket-lookup')" class="text-left group bg-white hover:bg-sky-50/40 p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-sky-400 transition-all duration-300 flex flex-col justify-between cursor-pointer">
            <div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-600 to-sky-900 text-white flex items-center justify-center text-lg mb-4 group-hover:scale-110 transition duration-300 shadow-md">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="inline-block text-[10px] font-bold text-sky-800 bg-sky-100/80 px-2.5 py-0.5 rounded-full mb-2">
                    Self-Service Digital
                </div>
                <h3 class="font-heading font-extrabold text-lg text-slate-900 group-hover:text-sky-900">Cek Tiket & Pesanan</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Buka e-ticket QR boarding pass, cek barcode pickup logistik, dan pantau posisi rombongan via Kode Booking atau WhatsApp.</p>
            </div>
            <div class="mt-5 pt-3.5 border-t border-slate-100 text-xs font-bold text-[#D97736] flex items-center justify-between w-full">
                <span>Buka Tiket Saya</span>
                <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1.5 transition duration-200"></i>
            </div>
        </button>
    </div>
</div>
