<div class="sticky top-14 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-2 bg-white border-b border-slate-200 mb-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-3 overflow-x-auto no-scrollbar py-0.5">
        <div class="flex items-center gap-2 shrink-0 text-xs font-semibold">
            <a href="{{ route('trail.map') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 transition flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <i class="fas fa-map-marked-alt text-[#D97736]"></i>
                <span>Peta & Trek GPS</span>
            </a>

            <a href="#pos-explorer" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 transition flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <i class="fas fa-route text-emerald-600"></i>
                <span>Profil Pos 1–4</span>
            </a>

            <a href="#kalkulator-biaya" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 transition flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <i class="fas fa-calculator text-amber-500"></i>
                <span>Estimasi Biaya</span>
            </a>

            <a href="#sop-checklist" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 transition flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <i class="fas fa-clipboard-check text-sky-600"></i>
                <span>Checklist SOP</span>
            </a>

            <a href="{{ route('rental.catalog') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 transition flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <i class="fas fa-campground text-teal-600"></i>
                <span>Sewa Alat Outdoor</span>
            </a>

            <a href="{{ route('store.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 transition flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <i class="fas fa-store text-orange-500"></i>
                <span>Pre-Order Toko</span>
            </a>

            <a href="{{ route('ojek.public.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 transition flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <i class="fas fa-motorcycle text-[#D97736]"></i>
                <span>Ojek Cigedug</span>
            </a>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="$dispatch('open-ticket-lookup')" class="px-3 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 text-xs font-semibold transition flex items-center gap-1.5 whitespace-nowrap">
                <i class="fas fa-qrcode text-sky-600"></i>
                <span>Cek Tiket Saya</span>
            </button>
            <a href="{{ route('simaksi.create') }}" class="px-3 py-1.5 rounded-lg bg-[#D97736] hover:bg-[#B25A20] text-white text-xs font-semibold transition flex items-center gap-1.5 whitespace-nowrap">
                <i class="fas fa-ticket-alt"></i>
                <span>Booking SIMAKSI</span>
            </a>
        </div>
    </div>
</div>
