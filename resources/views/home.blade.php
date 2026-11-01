<x-layouts.app title="Portal Resmi Pendakian Gunung Cikuray 2.821 MDPL | Basecamp SAMSARA">
    
    <!-- IF AUTHENTICATED STAFF: SHOW COMPACT STAFF QUICK BAR (NOT SHOWN TO PUBLIC) -->
    @auth
        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-[#1E3A2B] to-[#12241B] text-white border border-[#2D5841] shadow-lg flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#D97736] to-amber-600 flex items-center justify-center text-white text-base shadow-md">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div>
                    <div class="text-xs text-emerald-200">Sesi Staf Aktif: <strong class="text-white">{{ auth()->user()->name }}</strong></div>
                    <div class="text-xs font-bold text-amber-300">{{ auth()->user()->role->label() }}</div>
                </div>
            </div>
            <div>
                @php $role = auth()->user()->role->value; @endphp
                @if ($role === 'SUPERADMIN' || $role === 'ADMIN_SAR')
                    <a href="{{ route('admin.index') }}" class="touch-target px-4 py-2 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white text-xs font-bold transition inline-flex items-center gap-2 shadow-sm">
                        <i class="fas fa-gauge-high"></i> Buka Command Center Admin
                    </a>
                @elseif ($role === 'KASIR')
                    <a href="{{ route('pos.index') }}" class="touch-target px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold transition inline-flex items-center gap-2 shadow-sm">
                        <i class="fas fa-cash-register"></i> Buka Kasir POS Basecamp
                    </a>
                @elseif ($role === 'PETUGAS_POS')
                    <a href="{{ route('fieldops.index') }}" class="touch-target px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition inline-flex items-center gap-2 shadow-sm">
                        <i class="fas fa-qrcode"></i> Buka Scanner PWA Pos 1-4
                    </a>
                @endif
            </div>
        </div>
    @endauth

    <!-- 1. HERO OFFICIAL PORTAL BANNER (ATMOSPHERIC SCENIC OUTDOOR SHOWCASE) -->
    <x-public.interactive-hero
        :trailSetting="$trailSetting"
        :remainingQuota="$remainingQuota"
        :maxQuota="$maxQuota"
    />

    <!-- 2. STICKY QUICK NAVIGATION BAR (THUMB-ZONE FRIENDLY) -->
    <x-public.quick-nav />

    <!-- 3. LAYANAN MANDIRI PENDAKI (4 PILAR AKSES PUBLIK TANPA REPOT) -->
    <x-public.services-grid />

    <!-- 4. PETA SATELIT GPS & TREK PREVIEW SHOWCASE (HALAMAN TERPISAH) -->
    <x-public.trail-preview-card />

    <!-- 5. CHECKPOINT & POS EXPLORER INTERAKTIF (DOKUMENTASI ASLI LAPANGAN) -->
    <x-public.checkpoint-explorer :checkpoints="$checkpoints ?? null" />

    <!-- 6. KALKULATOR BIAYA SIMAKSI & PATUNGAN ROMBONGAN INTERAKTIF -->
    <x-public.cost-calculator />

    <!-- 7. INTERACTIVE GEAR & SOP CHECKLIST (LOCALSTORAGE PERSISTED) -->
    <x-public.gear-checklist />

    <!-- 8. TRANSPARANSI TARIF RESMI & FASILITAS LENGKAP BASECAMP -->
    <x-public.tariffs-facilities />

    <!-- 9. FAQ INTERAKTIF DENGAN KATEGORI & ACCORDION ANIMASI -->
    <x-public.faq-accordion />

    <!-- 10. HOTLINE DARURAT 24 JAM & KONTAK SAR BASECAMP SAMSARA -->
    <x-public.emergency-banner />

    <!-- GLOBAL INTERACTIVE MODALS -->
    <x-public.ticket-lookup-modal />
    <x-public.photo-lightbox />

</x-layouts.app>
