<x-layouts.admin title="Data Anggota Driver Ojek Cigedug">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8"
         x-data="{ showAddModal: false }">

        <!-- HEADER & BREADCRUMB -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700">Home</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <a href="{{ route('ojek.index') }}" class="hover:text-emerald-700">Antrean Ojek</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-800 font-bold">Data Driver</span>
                </nav>
                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fas fa-id-card text-[#D97736]"></i>
                    Paguyuban Driver Ojek Cigedug Mandiri
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Data induk driver resmi mitra Basecamp SAMSARA untuk jalur pendakian Gunung Cikuray.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('ojek.index') }}"
                   class="touch-target px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold border border-slate-300 flex items-center gap-2 shadow-xs transition">
                    <i class="fas fa-arrow-left"></i> Kembali ke Antrean
                </a>
                <button type="button" @click="showAddModal = true"
                        class="touch-target px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold flex items-center gap-2 shadow-md transition active:scale-95">
                    <i class="fas fa-user-plus"></i> + Tambah Driver
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- STATS -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Anggota</span>
                <span class="font-heading font-extrabold text-2xl text-slate-900 mt-1 block">{{ $drivers->count() }} Orang</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Driver Aktif</span>
                <span class="font-heading font-extrabold text-2xl text-emerald-600 mt-1 block">{{ $drivers->where('is_active', true)->count() }} Driver</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs col-span-2 sm:col-span-1">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Trip Tercatat</span>
                <span class="font-heading font-extrabold text-2xl text-[#D97736] mt-1 block">{{ $drivers->sum('total_trips') }} Trip</span>
            </div>
        </div>

        <!-- DRIVERS TABLE -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-bold text-base text-slate-900">Daftar Anggota Driver Terdaftar</h3>
                <span class="text-xs text-slate-500">Standar Keselamatan SAMSARA</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">No. Anggota</th>
                            <th class="py-3.5 px-4">Nama Lengkap</th>
                            <th class="py-3.5 px-4">Plat Motor</th>
                            <th class="py-3.5 px-4">Kendaraan</th>
                            <th class="py-3.5 px-4">No. WhatsApp</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Total Trip</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($drivers as $d)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 sm:px-6 font-mono font-bold text-slate-700">
                                    {{ $d->driver_number }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $d->name }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-[#D97736]">
                                    {{ $d->license_plate }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $d->motorcycle_model ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-mono">
                                    {{ $d->phone }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($d->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                    {{ $d->total_trips }}x
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-right">
                                    <form action="{{ route('ojek.drivers.toggle', $d->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Ubah status aktif driver {{ $d->name }}?')"
                                                class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition {{ $d->is_active ? 'border-rose-200 text-rose-700 hover:bg-rose-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $d->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-slate-400 text-xs">
                                    Belum ada data driver paguyuban.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: TAMBAH DRIVER -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showAddModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.stop>
                    <h3 class="font-heading font-extrabold text-xl text-slate-900 mb-1">+ Daftarkan Anggota Driver</h3>
                    <p class="text-xs text-slate-500 mb-4">Input data pengemudi resmi warga lokal Cigedug.</p>

                    <form action="{{ route('ojek.drivers.store') }}" method="POST">
                        @csrf
                        <div class="space-y-3.5 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Driver:</label>
                                <input type="text" name="name" required placeholder="Misal: Kang Asep Kurnia"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP:</label>
                                <input type="text" name="phone" required placeholder="08xxxxxxxxxx"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor Plat Motor:</label>
                                <input type="text" name="license_plate" required placeholder="Z 4821 SG"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm uppercase font-mono focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Jenis / Model Motor:</label>
                                <input type="text" name="motorcycle_model" placeholder="Misal: Honda CRF 150 / Beat Street"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Paguyuban / Komunitas:</label>
                                <input type="text" name="community_name" value="Paguyuban Ojek Cigedug Mandiri"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6">
                            <button type="button" @click="showAddModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">
                                Daftarkan Driver
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.admin>
