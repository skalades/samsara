<x-layouts.app title="Katalog Toko & Pre-Order Basecamp SAMSARA">
    <div x-data="{
        cart: [],
        showCartModal: false,
        search: '',
        customerName: '',
        customerPhone: '',
        bookingCode: '',
        paymentMethod: 'QRIS',
        getItemQty(productId) {
            const item = this.cart.find(i => i.id === productId);
            return item ? item.qty : 0;
        },
        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: '+1 ' + product.name + ' ditambahkan ke keranjang', type: 'success' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: 'Stok maksimal telah tercapai (' + product.stock + ' unit)', type: 'warning' }
                    }));
                }
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    type: product.type,
                    stock: product.stock,
                    qty: 1
                });
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: product.name + ' ditambahkan ke keranjang', type: 'success' }
                }));
            }
        },
        removeFromCart(id) {
            this.cart = this.cart.filter(i => i.id !== id);
        },
        updateQty(id, delta) {
            const item = this.cart.find(i => i.id === id);
            if (item) {
                const newQty = item.qty + delta;
                if (newQty <= 0) {
                    this.removeFromCart(id);
                } else if (newQty <= item.stock) {
                    item.qty = newQty;
                }
            }
        },
        get cartTotal() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
        },
        get cartCount() {
            return this.cart.reduce((sum, i) => sum + i.qty, 0);
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
        }
    }">
        <!-- HEADER & OMNICHANNEL EXPLANATION -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                    <i class="fas fa-box-open"></i> Omnichannel Pre-Order & Toko Basecamp
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">Perlengkapan & Produk Lokal</h1>
                <p class="text-xs sm:text-sm text-slate-500">Pesan dari ponsel sebelum hari-H pendakian, ambil cepat di kasir loket basecamp (Click & Collect).</p>
            </div>

            <!-- Header Action Controls -->
            <div class="flex items-center gap-3">
                <button type="button" @click="showCartModal = true"
                        class="touch-target px-4 py-2.5 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white font-semibold text-xs sm:text-sm shadow-xs transition flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Keranjang</span>
                    <span x-show="cartCount > 0" class="px-1.5 py-0.5 rounded-full bg-white text-[#D97736] text-[11px] font-bold shadow-2xs" x-text="cartCount"></span>
                </button>
            </div>
        </div>

        <!-- SEARCH BAR & CATEGORY PILLS -->
        <div class="mb-6 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="search" placeholder="Cari gas, logistik, madu, kopi..."
                           class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#1E3A2B] shadow-2xs">
                    <button type="button" x-show="search" @click="search = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <div class="text-xs text-slate-500 hidden sm:block">
                    <span>*Semua pesanan diambil di kasir loket Basecamp Cigedug.</span>
                </div>
            </div>

            <!-- CATEGORY & INVENTORY TYPE FILTER PILLS -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar text-xs font-bold">
                <a href="{{ route('store.index') }}"
                   class="px-4 py-2.5 rounded-xl shrink-0 whitespace-nowrap transition shadow-2xs {{ $category === 'ALL' && $inventoryType === 'ALL' ? 'bg-[#1E3A2B] text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    <i class="fas fa-border-all mr-1.5"></i> Semua Produk
                </a>
                <a href="{{ route('store.index', ['type' => 'KONSINYASI']) }}"
                   class="px-4 py-2.5 rounded-xl shrink-0 whitespace-nowrap transition shadow-2xs {{ $inventoryType === 'KONSINYASI' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    <i class="fas fa-handshake mr-1.5 text-amber-300"></i> UMKM Cigedug (Titip Jual)
                </a>
                <a href="{{ route('store.index', ['type' => 'BELI_PUTUS']) }}"
                   class="px-4 py-2.5 rounded-xl shrink-0 whitespace-nowrap transition shadow-2xs {{ $inventoryType === 'BELI_PUTUS' ? 'bg-emerald-800 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    <i class="fas fa-shield-alt mr-1.5 text-emerald-300"></i> Beli Putus (SAMSARA)
                </a>
                <a href="{{ route('store.index', ['category' => 'GAS_BAHAN_BAKAR']) }}"
                   class="px-4 py-2.5 rounded-xl shrink-0 whitespace-nowrap transition shadow-2xs {{ $category === 'GAS_BAHAN_BAKAR' ? 'bg-[#1E3A2B] text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    <i class="fas fa-fire mr-1.5 text-orange-400"></i> Gas Portable
                </a>
                <a href="{{ route('store.index', ['category' => 'PERLENGKAPAN']) }}"
                   class="px-4 py-2.5 rounded-xl shrink-0 whitespace-nowrap transition shadow-2xs {{ $category === 'PERLENGKAPAN' ? 'bg-[#1E3A2B] text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    <i class="fas fa-hiking mr-1.5 text-emerald-500"></i> Perlengkapan Jalur
                </a>
                <a href="{{ route('store.index', ['category' => 'LOGISTIK']) }}"
                   class="px-4 py-2.5 rounded-xl shrink-0 whitespace-nowrap transition shadow-2xs {{ $category === 'LOGISTIK' ? 'bg-[#1E3A2B] text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    <i class="fas fa-utensils mr-1.5 text-amber-500"></i> Logistik Makanan
                </a>
            </div>
        </div>

        <!-- PRODUCT CARDS (Mobile-First 2 Col on Phone, 3-4 Col on Tablet/Desktop) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mb-20 md:mb-8">
            @forelse ($products as $product)
                <div x-show="!search || '{{ strtolower($product->name . ' ' . $product->sku . ' ' . ($product->mitra ? $product->mitra->name : '')) }}'.includes(search.toLowerCase())"
                     class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col justify-between p-4 hover:shadow-md hover:border-slate-300 transition duration-150">
                    <div>
                        <!-- Type Badge & Category -->
                        <div class="flex items-center justify-between gap-1 mb-2">
                            @if ($product->isConsignment())
                                <x-ui.badge color="amber" size="sm">
                                    <i class="fas fa-handshake mr-1"></i> UMKM
                                </x-ui.badge>
                            @else
                                <x-ui.badge color="emerald" size="sm">
                                    Beli Putus
                                </x-ui.badge>
                            @endif

                            <span class="text-[10px] font-bold text-slate-400 font-mono">
                                {{ $product->sku }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h3 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 leading-snug line-clamp-2">
                            {{ $product->name }}
                        </h3>

                        @if ($product->mitra)
                            <div class="text-[10px] text-amber-700 font-medium mt-0.5 truncate">
                                Mitra: {{ $product->mitra->name }}
                            </div>
                        @endif

                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                            {{ $product->description }}
                        </p>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-100">
                        <div class="flex items-baseline justify-between mb-2">
                            <span class="text-xs sm:text-sm font-heading font-extrabold text-[#D97736]">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </span>

                            <span class="text-[10px] font-medium {{ $product->isLowStock() ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                Stok: {{ $product->stock }}
                            </span>
                        </div>

                        @if ($product->stock > 0)
                            <!-- Dynamic Cart Interactive Stepper / Add Button -->
                            <div class="w-full">
                                <!-- When not in cart -->
                                <button type="button"
                                        x-show="getItemQty({{ $product->id }}) === 0"
                                        @click="addToCart({
                                            id: {{ $product->id }},
                                            name: '{{ addslashes($product->name) }}',
                                            price: {{ $product->selling_price }},
                                            type: '{{ $product->inventory_type->value }}',
                                            stock: {{ $product->stock }}
                                        })"
                                        class="touch-target w-full rounded-xl bg-emerald-50 hover:bg-[#1E3A2B] text-[#1E3A2B] hover:text-white border border-emerald-200 text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95 shadow-2xs cursor-pointer">
                                    <i class="fas fa-plus-circle text-[#D97736]"></i> Tambah
                                </button>

                                <!-- When already in cart: Stepper -->
                                <div x-show="getItemQty({{ $product->id }}) > 0"
                                     class="flex items-center justify-between rounded-xl bg-[#1E3A2B] text-white p-1 border border-[#2D5841]">
                                    <button type="button" @click="updateQty({{ $product->id }}, -1)"
                                            class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 text-white font-bold flex items-center justify-center transition cursor-pointer text-xs">
                                        -
                                    </button>
                                    <span class="font-heading font-extrabold text-xs text-amber-300" x-text="getItemQty({{ $product->id }}) + ' di Keranjang'"></span>
                                    <button type="button" @click="updateQty({{ $product->id }}, 1)"
                                            class="w-8 h-8 rounded-lg bg-[#D97736] hover:bg-[#B25A20] text-white font-bold flex items-center justify-center transition cursor-pointer text-xs">
                                        +
                                    </button>
                                </div>
                            </div>
                        @else
                            <button disabled class="touch-target w-full rounded-xl bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed">
                                Stok Habis
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <x-ui.empty-state
                        icon="fas fa-box-open"
                        title="Produk Tidak Ditemukan"
                        description="Tidak ada perlengkapan yang cocok dengan kategori atau pencarian yang dipilih."
                    >
                        <a href="{{ route('store.index') }}" class="touch-target px-4 py-2 rounded-xl bg-[#1E3A2B] text-white text-xs font-bold shadow-xs">
                            Reset Semua Filter
                        </a>
                    </x-ui.empty-state>
                </div>
            @endforelse
        </div>

        <!-- FLOATING MOBILE BOTTOM CART PILL (Thumb-Zone Friendly) -->
        <div x-show="cartCount > 0" x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="md:hidden fixed bottom-20 left-4 right-4 z-40">
            <button type="button" @click="showCartModal = true"
                    class="w-full p-3.5 rounded-2xl bg-gradient-to-r from-[#D97736] to-amber-600 text-white font-bold text-xs sm:text-sm shadow-2xl flex items-center justify-between active:scale-95 transition border border-white/20 backdrop-blur-md cursor-pointer">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-xs">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <span><strong x-text="cartCount"></strong> Barang di Keranjang</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-amber-200 font-mono" x-text="formatRupiah(cartTotal)"></span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </button>
        </div>

        <!-- REUSABLE MODULAR BOTTOM SHEET FOR CART CHECKOUT (PRD 2.5) -->
        <x-ui.bottom-sheet show="showCartModal" title="Keranjang Pre-Order" subtitle="Ambil Cepat di Kasir Basecamp Cigedug" icon="fas fa-shopping-basket">
            <form action="{{ route('store.preorder') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <!-- Items List -->
                    <div class="space-y-2 max-h-52 overflow-y-auto slim-scrollbar pr-1">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs">
                                <div class="flex-1 pr-2">
                                    <div class="font-bold text-slate-800" x-text="item.name"></div>
                                    <div class="text-[11px] text-[#D97736] font-semibold" x-text="formatRupiah(item.price)"></div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" @click="updateQty(item.id, -1)" class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-600 flex items-center justify-center font-bold hover:bg-slate-100 cursor-pointer text-xs">-</button>
                                    <span class="font-bold text-slate-900 w-6 text-center" x-text="item.qty"></span>
                                    <button type="button" @click="updateQty(item.id, 1)" class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-600 flex items-center justify-center font-bold hover:bg-slate-100 cursor-pointer text-xs">+</button>
                                    <button type="button" @click="removeFromCart(item.id)" class="text-rose-500 hover:text-rose-700 ml-1 cursor-pointer p-1">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="cart.length === 0" class="text-center py-6 text-slate-400 text-xs">
                            Keranjang Anda masih kosong. Silakan pilih produk terlebih dahulu.
                        </div>
                    </div>

                    <!-- Customer Input Fields -->
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemesan <span class="text-rose-500">*</span></label>
                            <input type="text" name="customer_name" required placeholder="Nama lengkap pendaki"
                                   class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B]">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp <span class="text-rose-500">*</span></label>
                                <input type="tel" name="customer_phone" required placeholder="08xxxxxxxxxx"
                                       class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kode Booking (Opsional)</label>
                                <input type="text" name="booking_code" placeholder="SMX-xxxxxx"
                                       class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 uppercase focus:outline-none focus:ring-2 focus:ring-[#1E3A2B]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B]">
                                <option value="QRIS">QRIS Dinamis (Instan Semua E-Wallet & Bank)</option>
                                <option value="CASH">Bayar Tunai di Loket Kasir saat Ambil</option>
                                <option value="TRANSFER">Transfer Rekening</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hidden Form inputs for items -->
                    <template x-for="(item, idx) in cart" :key="item.id">
                        <div>
                            <input type="hidden" :name="'items['+idx+'][product_id]'" :value="item.id">
                            <input type="hidden" :name="'items['+idx+'][qty]'" :value="item.qty">
                        </div>
                    </template>

                    <!-- Total & Submit -->
                    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-xs text-slate-500">Total Pre-Order:</span>
                            <div class="font-heading font-extrabold text-lg text-[#D97736]" x-text="formatRupiah(cartTotal)"></div>
                        </div>

                        <button type="submit" :disabled="cart.length === 0"
                                class="touch-target px-5 py-2.5 rounded-xl bg-[#D97736] hover:bg-[#B25A20] disabled:bg-slate-300 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
                            <i class="fas fa-qrcode"></i> Terbitkan Barcode Pickup
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.bottom-sheet>
    </div>
</x-layouts.app>
