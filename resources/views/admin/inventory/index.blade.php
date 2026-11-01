<x-layouts.admin title="Katalog Produk & Alat Sewa">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
         x-data="{
            tab: '{{ $tab }}',
            showAddProductModal: false,
            showEditProductModal: false,
            editProduct: {
                id: null,
                sku: '',
                name: '',
                category: 'LOGISTIK',
                inventory_type: 'BELI_PUTUS',
                consignment_mitra_id: '',
                cost_price: 0,
                selling_price: 0,
                stock: 0,
                minimum_stock_alert: 5,
                is_preorder_available: true,
                active: true,
                description: ''
            },
            openEditProduct(p) {
                this.editProduct = {
                    id: p.id,
                    sku: p.sku,
                    name: p.name,
                    category: p.category,
                    inventory_type: p.inventory_type,
                    consignment_mitra_id: p.consignment_mitra_id || '',
                    cost_price: p.cost_price,
                    selling_price: p.selling_price,
                    stock: p.stock,
                    minimum_stock_alert: p.minimum_stock_alert,
                    is_preorder_available: !!p.is_preorder_available,
                    active: !!p.active,
                    description: p.description || ''
                };
                this.showEditProductModal = true;
            },
            showAddRentalModal: false,
            showEditRentalModal: false,
            editRental: {
                id: null,
                asset_code: '',
                name: '',
                category: 'TENDA',
                rental_rate_per_day: 0,
                deposit_rate: 0,
                status: 'TERSEDIA',
                condition_notes: ''
            },
            openEditRental(r) {
                this.editRental = {
                    id: r.id,
                    asset_code: r.asset_code,
                    name: r.name,
                    category: r.category,
                    rental_rate_per_day: r.rental_rate_per_day,
                    deposit_rate: r.deposit_rate,
                    status: r.status,
                    condition_notes: r.condition_notes || ''
                };
                this.showEditRentalModal = true;
            }
         }">

        <!-- HEADER & BREADCRUMB -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1.5">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700">Home</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <a href="{{ route('admin.index') }}" class="hover:text-emerald-700">Command Center</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-800 font-bold">Katalog Produk & Alat Sewa</span>
                </nav>
                <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fas fa-boxes-stacked text-[#D97736]"></i>
                    Master Inventaris: Toko & Rental
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Input dan kelola barang yang dijual (Toko & Konsinyasi UMKM) serta alat outdoor yang disewakan.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('store.index') }}" target="_blank"
                   class="touch-target px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-300 flex items-center gap-2 shadow-xs transition">
                    <i class="fas fa-store text-emerald-600"></i> Lihat Toko Publik
                </a>
                <a href="{{ route('rental.catalog') }}" target="_blank"
                   class="touch-target px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-300 flex items-center gap-2 shadow-xs transition">
                    <i class="fas fa-campground text-amber-600"></i> Katalog Sewa Publik
                </a>
            </div>
        </div>

        <!-- STATS BAR -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Produk Toko</span>
                <span class="font-heading font-extrabold text-2xl text-slate-900 mt-1 block">{{ $stats['total_products'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-rose-500 uppercase tracking-wider block">Stok Menipis</span>
                <span class="font-heading font-extrabold text-2xl text-rose-600 mt-1 block">{{ $stats['low_stock_products'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-amber-500 uppercase tracking-wider block">Konsinyasi UMKM</span>
                <span class="font-heading font-extrabold text-2xl text-amber-600 mt-1 block">{{ $stats['consignment_products'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Aset Sewa</span>
                <span class="font-heading font-extrabold text-2xl text-slate-900 mt-1 block">{{ $stats['total_rentals'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-500 uppercase tracking-wider block">Sewa Tersedia</span>
                <span class="font-heading font-extrabold text-2xl text-emerald-600 mt-1 block">{{ $stats['available_rentals'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-sky-500 uppercase tracking-wider block">Sedang Disewa</span>
                <span class="font-heading font-extrabold text-2xl text-sky-600 mt-1 block">{{ $stats['rented_rentals'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-amber-500 uppercase tracking-wider block">Perlu QC / Cuci</span>
                <span class="font-heading font-extrabold text-2xl text-amber-600 mt-1 block">{{ $stats['need_qc_rentals'] }}</span>
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="flex items-center gap-2 border-b border-slate-200 mb-6 pb-2">
            <button type="button" @click="tab = 'products'"
                    :class="tab === 'products' ? 'bg-[#2D5841] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 rounded-xl font-heading font-bold text-xs sm:text-sm flex items-center gap-2 transition">
                <i class="fas fa-shopping-basket" :class="tab === 'products' ? 'text-amber-300' : 'text-slate-400'"></i>
                <span>Barang Dijual (Toko & Konsinyasi)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                      :class="tab === 'products' ? 'bg-[#1E3A2B] text-amber-200' : 'bg-slate-100 text-slate-600'">
                    {{ $stats['total_products'] }}
                </span>
            </button>

            <button type="button" @click="tab = 'rentals'"
                    :class="tab === 'rentals' ? 'bg-[#2D5841] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 rounded-xl font-heading font-bold text-xs sm:text-sm flex items-center gap-2 transition">
                <i class="fas fa-campground" :class="tab === 'rentals' ? 'text-amber-300' : 'text-slate-400'"></i>
                <span>Barang Disewa (Alat Outdoor Rental)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                      :class="tab === 'rentals' ? 'bg-[#1E3A2B] text-amber-200' : 'bg-slate-100 text-slate-600'">
                    {{ $stats['total_rentals'] }}
                </span>
            </button>
        </div>

        <!-- TAB 1: PRODUK TOKO & KONSINYASI -->
        <div x-show="tab === 'products'" x-cloak>
            <!-- ACTION & FILTER BAR -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <form action="{{ route('admin.inventory.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto flex-1">
                    <input type="hidden" name="tab" value="products">

                    <div class="relative flex-1 min-w-[200px]">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ $tab === 'products' ? $search : '' }}"
                               placeholder="Cari SKU, nama produk, deskripsi..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                    </div>

                    <select name="type" onchange="this.form.submit()" class="py-2.5 px-3 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#2D5841]">
                        <option value="">Semua Tipe Stok</option>
                        <option value="BELI_PUTUS" {{ request('type') === 'BELI_PUTUS' ? 'selected' : '' }}>Beli Putus (SAMSARA)</option>
                        <option value="KONSINYASI" {{ request('type') === 'KONSINYASI' ? 'selected' : '' }}>Konsinyasi (UMKM)</option>
                    </select>

                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                        Filter
                    </button>
                </form>

                <button type="button" @click="showAddProductModal = true"
                        class="touch-target px-4 py-2.5 rounded-xl bg-[#2D5841] hover:bg-[#1E3A2B] text-white text-xs sm:text-sm font-bold flex items-center gap-2 shadow-md transition shrink-0">
                    <i class="fas fa-plus text-amber-300"></i> + Tambah Produk Baru
                </button>
            </div>

            <!-- TABLE: PRODUK -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6">SKU & Produk</th>
                                <th class="py-3.5 px-4">Tipe Inventaris</th>
                                <th class="py-3.5 px-4">Harga Beli / Modal</th>
                                <th class="py-3.5 px-4">Harga Jual</th>
                                <th class="py-3.5 px-4">Margin Kas</th>
                                <th class="py-3.5 px-4 text-center">Stok</th>
                                <th class="py-3.5 px-4 text-center">Pre-Order</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($products as $p)
                                @php
                                    $isLow = $p->stock <= $p->minimum_stock_alert;
                                    $isConsign = $p->inventory_type->value === 'KONSINYASI';
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 sm:px-6">
                                        <div class="font-mono font-bold text-[#D97736] text-[11px]">{{ $p->sku }}</div>
                                        <div class="font-bold text-slate-900 mt-0.5">{{ $p->name }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $p->category->label() }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if ($isConsign)
                                            <span class="inline-flex flex-col">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    <i class="fas fa-handshake text-[9px]"></i> Konsinyasi
                                                </span>
                                                <span class="text-[10px] text-slate-500 mt-1 font-medium truncate max-w-[140px]">
                                                    {{ $p->mitra->name ?? 'Mitra Warga' }}
                                                </span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <i class="fas fa-building text-[9px]"></i> Beli Putus
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-600">
                                        Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                        Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-emerald-600 font-semibold">
                                        +Rp {{ number_format($p->margin_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-xl font-mono font-bold text-xs {{ $isLow ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-slate-100 text-slate-800' }}">
                                            {{ $p->stock }} pcs
                                        </span>
                                        @if($isLow)
                                            <div class="text-[9px] text-rose-600 font-bold mt-0.5">Min Alert: {{ $p->minimum_stock_alert }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($p->is_preorder_available)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                    @click="openEditProduct({
                                                        id: {{ $p->id }},
                                                        sku: '{{ addslashes($p->sku) }}',
                                                        name: '{{ addslashes($p->name) }}',
                                                        category: '{{ $p->category->value }}',
                                                        inventory_type: '{{ $p->inventory_type->value }}',
                                                        consignment_mitra_id: '{{ $p->consignment_mitra_id ?? '' }}',
                                                        cost_price: {{ (float)$p->cost_price }},
                                                        selling_price: {{ (float)$p->selling_price }},
                                                        stock: {{ $p->stock }},
                                                        minimum_stock_alert: {{ $p->minimum_stock_alert }},
                                                        is_preorder_available: {{ $p->is_preorder_available ? 'true' : 'false' }},
                                                        active: {{ $p->active ? 'true' : 'false' }},
                                                        description: '{{ addslashes($p->description ?? '') }}'
                                                    })"
                                                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition"
                                                    title="Edit Produk">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <form action="{{ route('admin.inventory.products.destroy', $p->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus produk {{ $p->name }} dari katalog?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-slate-400 text-xs">
                                        Belum ada produk yang didaftarkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 2: ALAT RENTAL OUTDOOR -->
        <div x-show="tab === 'rentals'" x-cloak>
            <!-- ACTION & FILTER BAR -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <form action="{{ route('admin.inventory.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto flex-1">
                    <input type="hidden" name="tab" value="rentals">

                    <div class="relative flex-1 min-w-[200px]">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ $tab === 'rentals' ? $search : '' }}"
                               placeholder="Cari kode aset, nama tenda, carrier..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                    </div>

                    <select name="status" onchange="this.form.submit()" class="py-2.5 px-3 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#2D5841]">
                        <option value="">Semua Status Aset</option>
                        @foreach($rentalStatuses as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                        Filter
                    </button>
                </form>

                <button type="button" @click="showAddRentalModal = true"
                        class="touch-target px-4 py-2.5 rounded-xl bg-[#2D5841] hover:bg-[#1E3A2B] text-white text-xs sm:text-sm font-bold flex items-center gap-2 shadow-md transition shrink-0">
                    <i class="fas fa-plus text-amber-300"></i> + Tambah Alat Sewa
                </button>
            </div>

            <!-- TABLE: ALAT RENTAL -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6">Kode Aset</th>
                                <th class="py-3.5 px-4">Nama Alat & Kategori</th>
                                <th class="py-3.5 px-4">Tarif Sewa / Hari</th>
                                <th class="py-3.5 px-4">Jaminan / Deposit</th>
                                <th class="py-3.5 px-4 text-center">Status Aset</th>
                                <th class="py-3.5 px-4 text-center">Pernah Disewa</th>
                                <th class="py-3.5 px-4">Catatan Kondisi</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($rentals as $r)
                                @php
                                    $stVal = $r->status instanceof \App\Shared\Enums\RentalAssetStatus ? $r->status->value : (string)$r->status;
                                    $stLabel = $r->status instanceof \App\Shared\Enums\RentalAssetStatus ? $r->status->label() : $stVal;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 sm:px-6 font-mono font-bold text-slate-800">
                                        {{ $r->asset_code }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $r->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium">{{ $rentalCategories[$r->category] ?? $r->category }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-emerald-700">
                                        Rp {{ number_format($r->rental_rate_per_day, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-600">
                                        Rp {{ number_format($r->deposit_rate, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @switch($stVal)
                                            @case('TERSEDIA')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    Tersedia di Rak
                                                </span>
                                                @break
                                            @case('DISEWA')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                                    Sedang Disewa
                                                </span>
                                                @break
                                            @case('PERLU_QC_CUCI')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    Perlu QC / Cuci
                                                </span>
                                                @break
                                            @case('DALAM_PERBAIKAN')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                    Dalam Perbaikan
                                                </span>
                                                @break
                                            @default
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                                    {{ $stLabel }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                        {{ $r->times_rented_count ?? 0 }}x
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-500 max-w-[200px] truncate">
                                        {{ $r->condition_notes ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                    @click="openEditRental({
                                                        id: {{ $r->id }},
                                                        asset_code: '{{ addslashes($r->asset_code) }}',
                                                        name: '{{ addslashes($r->name) }}',
                                                        category: '{{ $r->category }}',
                                                        rental_rate_per_day: {{ (float)$r->rental_rate_per_day }},
                                                        deposit_rate: {{ (float)$r->deposit_rate }},
                                                        status: '{{ $stVal }}',
                                                        condition_notes: '{{ addslashes($r->condition_notes ?? '') }}'
                                                    })"
                                                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition"
                                                    title="Edit Alat">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <form action="{{ route('admin.inventory.rentals.destroy', $r->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus alat {{ $r->name }} ({{ $r->asset_code }})?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-slate-400 text-xs">
                                        Belum ada alat rental yang didaftarkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($rentals->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $rentals->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODALS UNTUK PRODUK -->
        <!-- ============================================== -->

        <!-- MODAL: TAMBAH PRODUK -->
        <div x-show="showAddProductModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showAddProductModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200" @click.stop
                     x-data="{ invType: 'BELI_PUTUS' }">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                                <i class="fas fa-plus-circle text-[#D97736]"></i> + Tambah Produk Toko
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Input barang dagangan Basecamp atau titipan mitra UMKM lokal.</p>
                        </div>
                        <button type="button" @click="showAddProductModal = false" class="p-2 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.inventory.products.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kode SKU Produk:</label>
                                    <input type="text" name="sku" required placeholder="Misal: GAS-002"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono uppercase focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kategori Produk:</label>
                                    <select name="category" required class="w-full rounded-xl border border-slate-300 p-2.5 font-medium focus:ring-2 focus:ring-[#2D5841]">
                                        @foreach($productCategories as $cat)
                                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Produk:</label>
                                <input type="text" name="name" required placeholder="Misal: Gas Canister Dhaulagiri 230g"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Tipe Inventaris:</label>
                                    <select name="inventory_type" x-model="invType" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold focus:ring-2 focus:ring-[#2D5841]">
                                        <option value="BELI_PUTUS">Beli Putus (Milik SAMSARA)</option>
                                        <option value="KONSINYASI">Konsinyasi (Titipan UMKM Warga)</option>
                                    </select>
                                </div>
                                <div x-show="invType === 'KONSINYASI'">
                                    <label class="block font-bold text-slate-700 mb-1">Pilih Mitra UMKM Cigedug:</label>
                                    <select name="consignment_mitra_id" class="w-full rounded-xl border border-amber-300 bg-amber-50/50 p-2.5 font-medium focus:ring-2 focus:ring-amber-500">
                                        <option value="">-- Pilih Mitra --</option>
                                        @foreach($mitras as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Harga Beli / Modal (Rp):</label>
                                    <input type="number" name="cost_price" required min="0" placeholder="17000"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                    <p class="text-[10px] text-slate-400 mt-0.5">Jika konsinyasi: bagian hak mitra.</p>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Harga Jual ke Pendaki (Rp):</label>
                                    <input type="number" name="selling_price" required min="0" placeholder="24000"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Jumlah Stok Awal:</label>
                                    <input type="number" name="stock" required min="0" value="10"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Batas Minimum Alert Stok:</label>
                                    <input type="number" name="minimum_stock_alert" required min="0" value="5"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div class="flex items-center gap-6 pt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_preorder_available" value="1" checked
                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-bold text-slate-700">Tersedia untuk Pre-Order Online</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" @click="showAddProductModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-[#2D5841] hover:bg-[#1E3A2B] text-white font-bold shadow-md">
                                Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDIT PRODUK -->
        <div x-show="showEditProductModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showEditProductModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200" @click.stop>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                                <i class="fas fa-edit text-amber-500"></i> Edit Data Produk
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Perbarui harga, stok, atau informasi produk.</p>
                        </div>
                        <button type="button" @click="showEditProductModal = false" class="p-2 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/inventory/products') }}/' + editProduct.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kode SKU Produk:</label>
                                    <input type="text" name="sku" x-model="editProduct.sku" required
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono uppercase focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kategori:</label>
                                    <select name="category" x-model="editProduct.category" required class="w-full rounded-xl border border-slate-300 p-2.5 font-medium focus:ring-2 focus:ring-[#2D5841]">
                                        @foreach($productCategories as $cat)
                                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Produk:</label>
                                <input type="text" name="name" x-model="editProduct.name" required
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Tipe Inventaris:</label>
                                    <select name="inventory_type" x-model="editProduct.inventory_type" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold focus:ring-2 focus:ring-[#2D5841]">
                                        <option value="BELI_PUTUS">Beli Putus (SAMSARA)</option>
                                        <option value="KONSINYASI">Konsinyasi (UMKM)</option>
                                    </select>
                                </div>
                                <div x-show="editProduct.inventory_type === 'KONSINYASI'">
                                    <label class="block font-bold text-slate-700 mb-1">Mitra UMKM Cigedug:</label>
                                    <select name="consignment_mitra_id" x-model="editProduct.consignment_mitra_id" class="w-full rounded-xl border border-amber-300 bg-amber-50/50 p-2.5 font-medium focus:ring-2 focus:ring-amber-500">
                                        <option value="">-- Pilih Mitra --</option>
                                        @foreach($mitras as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Harga Beli / Modal (Rp):</label>
                                    <input type="number" name="cost_price" x-model="editProduct.cost_price" required min="0"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Harga Jual (Rp):</label>
                                    <input type="number" name="selling_price" x-model="editProduct.selling_price" required min="0"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Stok:</label>
                                    <input type="number" name="stock" x-model="editProduct.stock" required min="0"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Min Alert:</label>
                                    <input type="number" name="minimum_stock_alert" x-model="editProduct.minimum_stock_alert" required min="0"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div class="flex items-center gap-6 pt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_preorder_available" value="1" :checked="editProduct.is_preorder_available"
                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-bold text-slate-700">Tersedia untuk Pre-Order</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="active" value="1" :checked="editProduct.active"
                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-bold text-slate-700">Status Aktif Dijual</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" @click="showEditProductModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-md">
                                Perbarui Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODALS UNTUK ALAT RENTAL -->
        <!-- ============================================== -->

        <!-- MODAL: TAMBAH ALAT RENTAL -->
        <div x-show="showAddRentalModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showAddRentalModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200" @click.stop>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                                <i class="fas fa-campground text-[#D97736]"></i> + Tambah Alat Sewa Outdoor
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan unit tenda, carrier, matras, atau alat pendakian lainnya.</p>
                        </div>
                        <button type="button" @click="showAddRentalModal = false" class="p-2 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.inventory.rentals.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kode Aset / Barcode:</label>
                                    <input type="text" name="asset_code" required placeholder="Misal: TND-02"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono uppercase focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kategori Alat:</label>
                                    <select name="category" required class="w-full rounded-xl border border-slate-300 p-2.5 font-semibold focus:ring-2 focus:ring-[#2D5841]">
                                        @foreach($rentalCategories as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Alat Sewa:</label>
                                <input type="text" name="name" required placeholder="Misal: Tenda Dome 2P Consina Magnum"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Tarif Sewa / Hari (Rp):</label>
                                    <input type="number" name="rental_rate_per_day" required min="0" placeholder="35000"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Uang Jaminan / Deposit (Rp):</label>
                                    <input type="number" name="deposit_rate" required min="0" placeholder="50000"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Status Awal Aset:</label>
                                <select name="status" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold focus:ring-2 focus:ring-[#2D5841]">
                                    <option value="TERSEDIA">TERSEDIA - Siap Disewakan di Rak</option>
                                    <option value="PERLU_QC_CUCI">PERLU_QC_CUCI - Masih Perlu Dicuci</option>
                                    <option value="DALAM_PERBAIKAN">DALAM_PERBAIKAN - Dalam Perbaikan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Catatan Kondisi / Kelengkapan:</label>
                                <textarea name="condition_notes" rows="2" placeholder="Misal: Frame aluminium utuh, pasak 8 buah, pasang cover rapat."
                                          class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:ring-2 focus:ring-[#2D5841]"></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" @click="showAddRentalModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-[#2D5841] hover:bg-[#1E3A2B] text-white font-bold shadow-md">
                                Daftarkan Alat Sewa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDIT ALAT RENTAL -->
        <div x-show="showEditRentalModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showEditRentalModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200" @click.stop>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                                <i class="fas fa-edit text-amber-500"></i> Edit Alat Sewa
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Perbarui tarif, jaminan, atau kondisi fisik aset.</p>
                        </div>
                        <button type="button" @click="showEditRentalModal = false" class="p-2 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/inventory/rentals') }}/' + editRental.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kode Aset / Barcode:</label>
                                    <input type="text" name="asset_code" x-model="editRental.asset_code" required
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono uppercase focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kategori Alat:</label>
                                    <select name="category" x-model="editRental.category" required class="w-full rounded-xl border border-slate-300 p-2.5 font-semibold focus:ring-2 focus:ring-[#2D5841]">
                                        @foreach($rentalCategories as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Alat Sewa:</label>
                                <input type="text" name="name" x-model="editRental.name" required
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Tarif Sewa / Hari (Rp):</label>
                                    <input type="number" name="rental_rate_per_day" x-model="editRental.rental_rate_per_day" required min="0"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Uang Jaminan / Deposit (Rp):</label>
                                    <input type="number" name="deposit_rate" x-model="editRental.deposit_rate" required min="0"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Status Aset:</label>
                                <select name="status" x-model="editRental.status" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold focus:ring-2 focus:ring-[#2D5841]">
                                    @foreach($rentalStatuses as $st)
                                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Catatan Kondisi / Kelengkapan:</label>
                                <textarea name="condition_notes" x-model="editRental.condition_notes" rows="2"
                                          class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:ring-2 focus:ring-[#2D5841]"></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" @click="showEditRentalModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-md">
                                Perbarui Alat Sewa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.admin>
