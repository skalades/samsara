<div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-9 shadow-sm mb-12 scroll-mt-24" id="sop-checklist"
     x-data="{
         items: [
             { id: 0, name: 'Sepatu gunung dengan sol bergerigi tebal (Grip tanah akar)', tag: 'Wajib', tagColor: 'bg-rose-100 text-rose-800 border-rose-200', checked: true },
             { id: 1, name: 'Jaket gunung windproof & thermal (Suhu Pos 4 drop 7-12°C)', tag: 'Wajib', tagColor: 'bg-rose-100 text-rose-800 border-rose-200', checked: true },
             { id: 2, name: 'Headlamp / senter + baterai cadangan (Summit dini hari)', tag: 'Wajib', tagColor: 'bg-rose-100 text-rose-800 border-rose-200', checked: true },
             { id: 3, name: 'Trash bag tebal khusus sampah bawa turun (Eco-Deposit)', tag: 'Wajib', tagColor: 'bg-rose-100 text-rose-800 border-rose-200', checked: true },
             { id: 4, name: 'Air minum minimal 3 Liter / orang dari Basecamp (Jalur minim air)', tag: 'Vital', tagColor: 'bg-amber-100 text-amber-800 border-amber-200', checked: true },
             { id: 5, name: 'Kotak P3K standar & obat pribadi (Waspada hipotermia / kram)', tag: 'Medis', tagColor: 'bg-sky-100 text-sky-800 border-sky-200', checked: false },
             { id: 6, name: 'Tenda double-layer berpasak kokoh (Tahan angin Pos 4)', tag: 'Camp', tagColor: 'bg-emerald-100 text-emerald-800 border-emerald-200', checked: false },
             { id: 7, name: 'Sleeping bag polar tebal + matras aluminium foil', tag: 'Camp', tagColor: 'bg-emerald-100 text-emerald-800 border-emerald-200', checked: false }
         ],
         init() {
             const saved = localStorage.getItem('samsara_hiker_checklist_v1');
             if (saved) {
                 try {
                     const parsed = JSON.parse(saved);
                     this.items.forEach(i => {
                         if (parsed[i.id] !== undefined) i.checked = parsed[i.id];
                     });
                 } catch (e) {}
             }
         },
         toggle(item) {
             item.checked = !item.checked;
             this.save();
         },
         save() {
             const state = {};
             this.items.forEach(i => state[i.id] = i.checked);
             localStorage.setItem('samsara_hiker_checklist_v1', JSON.stringify(state));
         },
         reset() {
             this.items.forEach(i => i.checked = false);
             this.save();
         },
         get checkedCount() {
             return this.items.filter(i => i.checked).length;
         },
         get totalCount() {
             return this.items.length;
         },
         get percentage() {
             return Math.round((this.checkedCount / this.totalCount) * 100);
         }
     }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4 mb-6">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736] block mb-1">
                <i class="fas fa-clipboard-check"></i> Persiapan & Keselamatan Lapangan
            </span>
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">
                Checklist Perlengkapan Wajib Pendaki
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Centang barang yang sudah masuk keril. Data tersimpan otomatis di browser ponsel Anda.
            </p>
        </div>

        <!-- Progress Badge & Reset Button -->
        <div class="flex items-center gap-3 shrink-0">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border transition-colors"
                  :class="percentage >= 80 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200'">
                <i class="fas" :class="percentage >= 80 ? 'fa-check-circle text-emerald-600' : 'fa-hourglass-half text-amber-600'"></i>
                <span x-text="checkedCount + '/' + totalCount + ' Siap (' + percentage + '%)'"></span>
            </span>

            <button type="button" @click="reset()"
                    class="text-xs text-slate-400 hover:text-rose-600 font-semibold transition cursor-pointer" title="Reset Semua Checklist">
                <i class="fas fa-rotate-left mr-1"></i> Reset
            </button>
        </div>
    </div>

    <!-- Dynamic Animated Progress Bar -->
    <div class="mb-6">
        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-300"
                 :class="percentage >= 80 ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-amber-500 to-[#D97736]'"
                 :style="'width: ' + percentage + '%'"></div>
        </div>
        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
            <span>Standar Inspeksi Meja Registrasi Basecamp</span>
            <span x-show="percentage < 80" class="text-amber-600 font-medium">Lengkapi perlengkapan demi keselamatan Anda</span>
            <span x-show="percentage >= 80" class="text-emerald-700 font-bold">Kesiapan perlengkapan sangat baik!</span>
        </div>
    </div>

    <!-- Checklist Items Grid (2 Columns) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <template x-for="item in items" :key="item.id">
            <div @click="toggle(item)"
                 class="p-4 rounded-2xl border transition-all duration-200 flex items-center justify-between gap-3 cursor-pointer select-none"
                 :class="item.checked ? 'bg-emerald-50/60 border-emerald-300 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200/90'">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center transition border"
                         :class="item.checked ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 bg-white'">
                        <i class="fas fa-check text-xs" x-show="item.checked"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-semibold transition leading-tight"
                          :class="item.checked ? 'text-emerald-950 font-bold' : 'text-slate-700'"
                          x-text="item.name"></span>
                </div>

                <span class="text-[10px] font-black px-2 py-0.5 rounded-md border shrink-0 uppercase tracking-wider"
                      :class="item.tagColor"
                      x-text="item.tag"></span>
            </div>
        </template>
    </div>

    <!-- Rental Upsell Helper Card -->
    <div class="mt-6 p-4 rounded-2xl bg-teal-50/70 border border-teal-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-campground"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-teal-950">Ada alat yang belum lengkap atau tertinggal di rumah?</h4>
                <p class="text-[11px] text-teal-700">Tenda dome, sleeping bag tebal, kompor, dan matras tersedia untuk disewa langsung di Basecamp SAMSARA.</p>
            </div>
        </div>

        <a href="{{ route('rental.catalog') }}" class="touch-target px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs transition whitespace-nowrap shrink-0 shadow-2xs">
            <i class="fas fa-arrow-right mr-1"></i> Buka Katalog Sewa
        </a>
    </div>
</div>
