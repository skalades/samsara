<x-layouts.admin title="Kasir Terpadu Basecamp POS">
    <div x-data="{
        cart: [],
        searchQuery: '',
        activeCategory: 'ALL',
        customerName: 'Pendaki Walk-in',
        customerPhone: '',
        bookingId: null,
        bookingCode: '',
        attachedBooking: null,
        showBookingLookupModal: false,
        showWalkInModal: false,
        bookingSearchQuery: '',
        bookingSearchResults: [],
        isSearchingBooking: false,
        walkInSubmitting: false,
        ticketPricePerPerson: {{ (int) ($trailSetting->ticket_fee_per_person ?? 25000) }},
        ecoDepositFee: {{ (int) ($trailSetting->eco_deposit_fee ?? 20000) }},
        walkInForm: {
            hiking_date: '{{ today()->toDateString() }}',
            duration_days: 2,
            leader_name: '',
            leader_nik: '',
            leader_phone: '',
            leader_email: '',
            leader_address: 'Cigedug, Garut',
            emergency_contact_name: '',
            emergency_contact_phone: '',
            tent_color_details: '',
            notes: '',
            members: [
                { full_name: '', nik: '', gender: 'L', age: 20, medical_history: 'Sehat' }
            ]
        },
        paymentMethod: 'CASH',
        paidAmount: 0,
        showCheckoutModal: false,
        showPickupModal: false,
        showOpenShiftModal: {{ (!$activeShift && $isKasir) ? 'true' : 'false' }},
        hasActiveShift: {{ $activeShift ? 'true' : 'false' }},
        isKasir: {{ $isKasir ? 'true' : 'false' }},
        openingCashInput: 500000,
        shiftAlertMessage: '{{ (!$activeShift && $isKasir) ? "Anda belum membuka shift kasir. Silakan buka shift kasir terlebih dahulu untuk memulai transaksi POS." : "" }}',
        pickupCode: '',
        pickupResult: null,
        selectedOjekRoute: '',
        ojekRoutes: {{ Js::from($ojekRoutes ?? []) }},
        selectedRentals: [],
        allRentals: {{ Js::from($availableRentals->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'code' => $r->asset_code,
            'rate' => (float) $r->rental_rate_per_day,
            'deposit' => (float) $r->deposit_rate,
            'category' => $r->category,
        ])) }},
        showRentalModal: false,

        // Products from server
        allProducts: {{ Js::from($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'category' => $p->category->value,
            'price' => (float) $p->selling_price,
            'stock' => $p->stock,
            'is_consignment' => $p->isConsignment(),
            'mitra' => $p->mitra?->name
        ])) }},

        get filteredProducts() {
            return this.allProducts.filter(p => {
                const matchCat = (this.activeCategory === 'ALL') || (p.category === this.activeCategory);
                const matchSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || p.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                return matchCat && matchSearch;
            });
        },

        addToCart(product) {
            if (this.isKasir && !this.hasActiveShift) {
                this.shiftAlertMessage = 'Shift kasir belum dibuka! Anda harus membuka shift kasir terlebih dahulu sebelum menambahkan barang.';
                this.showOpenShiftModal = true;
                return;
            }

            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                }
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    stock: product.stock,
                    qty: 1
                });
            }
            this.updateTotal();
        },

        updateQty(id, delta) {
            const item = this.cart.find(i => i.id === id);
            if (item) {
                const newQty = item.qty + delta;
                if (newQty <= 0) {
                    this.cart = this.cart.filter(i => i.id !== id);
                } else if (newQty <= item.stock) {
                    item.qty = newQty;
                }
            }
            this.updateTotal();
        },

        addRental(rental) {
            if (this.selectedRentals.some(r => r.id === rental.id)) {
                return;
            }
            this.selectedRentals.push({
                id: rental.id,
                name: rental.name,
                code: rental.code,
                rate: rental.rate,
                deposit: rental.deposit,
                days: 1
            });
            this.updateTotal();
        },

        removeRental(id) {
            this.selectedRentals = this.selectedRentals.filter(r => r.id !== id);
            this.updateTotal();
        },

        updateRentalDays(id, delta) {
            const r = this.selectedRentals.find(x => x.id === id);
            if (r) {
                r.days = Math.max(1, r.days + delta);
                this.updateTotal();
            }
        },

        get rentalTotal() {
            return this.selectedRentals.reduce((sum, r) => sum + (r.rate * r.days) + r.deposit, 0);
        },

        get bookingSimaksiTotal() {
            if (!this.attachedBooking) return 0;
            return (parseFloat(this.attachedBooking.total_ticket_amount) || 0) + (parseFloat(this.attachedBooking.eco_deposit_amount) || 0);
        },

        get walkInEstimasiTotal() {
            const count = Math.max(1, this.walkInForm.members.length);
            return (count * this.ticketPricePerPerson) + this.ecoDepositFee;
        },

        clearCart() {
            this.cart = [];
            this.selectedRentals = [];
            this.selectedOjekRoute = '';
            this.bookingId = null;
            this.bookingCode = '';
            this.attachedBooking = null;
            this.paidAmount = 0;
        },

        attachBooking(booking) {
            this.attachedBooking = booking;
            this.bookingId = booking.id;
            this.bookingCode = booking.booking_code;
            this.customerName = booking.leader_name;
            if (booking.leader_phone) {
                this.customerPhone = booking.leader_phone;
            }
            this.showBookingLookupModal = false;
            this.bookingSearchResults = [];
            this.bookingSearchQuery = '';
            this.updateTotal();
        },

        detachBooking() {
            this.attachedBooking = null;
            this.bookingId = null;
            this.bookingCode = '';
            this.updateTotal();
        },

        async searchBooking() {
            const q = this.bookingSearchQuery.trim();
            if (q.length < 2) return;
            this.isSearchingBooking = true;
            try {
                const res = await axios.get('{{ route('pos.simaksi.search') }}', { params: { q: q } });
                this.bookingSearchResults = res.data.bookings || [];
            } catch (err) {
                alert('Gagal mencari SIMAKSI: ' + (err.response?.data?.message || err.message));
            } finally {
                this.isSearchingBooking = false;
            }
        },

        addWalkInMember() {
            this.walkInForm.members.push({
                full_name: '',
                nik: '',
                gender: 'L',
                age: 20,
                medical_history: 'Sehat'
            });
        },

        removeWalkInMember(index) {
            if (this.walkInForm.members.length > 1) {
                this.walkInForm.members.splice(index, 1);
            }
        },

        async submitWalkIn() {
            if (!this.walkInForm.leader_name || !this.walkInForm.leader_nik || !this.walkInForm.leader_phone) {
                alert('Nama Ketua, NIK (16 digit), dan No. HP wajib diisi.');
                return;
            }
            if (this.walkInForm.leader_nik.length !== 16) {
                alert('NIK Ketua wajib 16 digit angka.');
                return;
            }
            for (let i = 0; i < this.walkInForm.members.length; i++) {
                if (!this.walkInForm.members[i].full_name) {
                    alert('Nama lengkap seluruh anggota rombongan wajib diisi.');
                    return;
                }
            }
            this.walkInSubmitting = true;
            try {
                const res = await axios.post('{{ route('pos.simaksi.walkin') }}', this.walkInForm);
                if (res.data.success) {
                    this.attachBooking(res.data.booking);
                    this.showWalkInModal = false;
                    this.walkInForm = {
                        hiking_date: '{{ today()->toDateString() }}',
                        duration_days: 2,
                        leader_name: '',
                        leader_nik: '',
                        leader_phone: '',
                        leader_email: '',
                        leader_address: 'Cigedug, Garut',
                        emergency_contact_name: '',
                        emergency_contact_phone: '',
                        tent_color_details: '',
                        notes: '',
                        members: [{ full_name: '', nik: '', gender: 'L', age: 20, medical_history: 'Sehat' }]
                    };
                }
            } catch (err) {
                alert('Gagal mendaftarkan walk-in SIMAKSI: ' + (err.response?.data?.message || err.message));
            } finally {
                this.walkInSubmitting = false;
            }
        },

        get ojekPrice() {
            if (!this.selectedOjekRoute) return 0;
            const r = this.ojekRoutes.find(x => x.id == this.selectedOjekRoute);
            return r ? parseFloat(r.fare_amount) : 0;
        },

        get subtotal() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0) + this.ojekPrice + this.rentalTotal + this.bookingSimaksiTotal;
        },

        get changeAmount() {
            return Math.max(0, this.paidAmount - this.subtotal);
        },

        updateTotal() {
            if (this.paidAmount < this.subtotal) {
                this.paidAmount = this.subtotal;
            }
        },

        setPaid(amount) {
            this.paidAmount = amount;
        },

        async submitCheckout() {
            if (this.isKasir && !this.hasActiveShift) {
                this.shiftAlertMessage = 'Shift kasir belum dibuka! Masukkan modal awal laci untuk membuka shift.';
                this.showOpenShiftModal = true;
                return;
            }

            if (this.cart.length === 0 && !this.bookingId && !this.attachedBooking && !this.selectedOjekRoute && this.selectedRentals.length === 0) {
                alert('Keranjang kasir masih kosong!');
                return;
            }

            const payload = {
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
                booking_id: this.attachedBooking ? this.attachedBooking.id : this.bookingId,
                payment_method: this.paymentMethod,
                paid_amount: this.paidAmount,
                ojek_route_id: this.selectedOjekRoute || null,
                ojek_qty: this.selectedOjekRoute ? 1 : 0,
                items: this.cart.map(i => ({ product_id: i.id, qty: i.qty })),
                rentals: this.selectedRentals.map(r => ({ rental_item_id: r.id, rental_days: r.days }))
            };

            try {
                const res = await axios.post('{{ route('pos.checkout') }}', payload);
                if (res.data.success) {
                    window.location.href = res.data.invoice_url;
                }
            } catch (err) {
                if (err.response?.data?.requires_shift) {
                    this.hasActiveShift = false;
                    this.shiftAlertMessage = err.response.data.message;
                    this.showOpenShiftModal = true;
                    return;
                }
                alert('Gagal memproses transaksi: ' + (err.response?.data?.message || err.message));
            }
        },

        async submitPickup() {
            if (!this.pickupCode) return;
            try {
                const res = await axios.post('{{ route('pos.pickup') }}', { code: this.pickupCode });
                this.pickupResult = res.data;
            } catch (err) {
                alert('Gagal validasi pengambilan: ' + (err.response?.data?.message || err.message));
            }
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
        }
    }">

        <!-- POS BAR TOP ACTION -->
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#1E3A2B] text-white flex items-center justify-center text-lg shadow-xs">
                    <i class="fas fa-cash-register text-[#D97736]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-heading font-black text-base sm:text-lg text-slate-900 leading-tight">Mobile / Tablet POS</h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Online Basecamp</span>
                    </div>
                    <p class="text-xs text-slate-500">
                        @if ($activeShift)
                            Shift Aktif: <span class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">#{{ $activeShift->shift_number }}</span> &bull;
                        @else
                            Shift: <span class="font-bold text-rose-800 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200 cursor-pointer hover:bg-rose-100" @click="showOpenShiftModal = true"><i class="fas fa-lock mr-1"></i>Belum Dibuka</span> &bull;
                        @endif
                        Kasir: <span class="font-semibold text-slate-700">{{ auth()->user()?->name ?? 'Kasir Loket 1' }}</span>
                    </p>
                </div>
            </div>

            <!-- Quick Shift & Pickup Action Buttons -->
            <div class="flex items-center flex-wrap gap-2">
                <button type="button" @click="showBookingLookupModal = true" class="touch-target px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-search text-emerald-600"></i>
                    <span>Cari / Scan SIMAKSI</span>
                </button>

                <button type="button" @click="showWalkInModal = true" class="touch-target px-3 py-2 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                    <i class="fas fa-user-plus text-amber-300"></i>
                    <span>+ Walk-In SIMAKSI</span>
                </button>

                <button type="button" @click="showPickupModal = true" class="touch-target px-3 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-barcode text-[#D97736]"></i>
                    <span class="hidden sm:inline">Scan Pickup Pre-Order</span>
                    <span class="sm:hidden">Pickup</span>
                </button>

                @if ($activeShift)
                    <a href="{{ route('pos.shift') }}" class="touch-target px-3.5 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fas fa-calculator text-slate-500"></i>
                        <span>Tutup Shift Kasir</span>
                    </a>
                @else
                    <button type="button" @click="showOpenShiftModal = true" class="touch-target px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                        <i class="fas fa-door-open text-amber-200"></i>
                        <span>Buka Shift Kasir</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- PERSISTENT WARNING BANNER WHEN SHIFT IS NOT OPEN -->
        <div x-show="isKasir && !hasActiveShift" x-cloak class="mb-4 p-4 rounded-2xl bg-gradient-to-r from-amber-50 via-rose-50 to-amber-50 border-2 border-amber-300 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#D97736] text-white flex items-center justify-center text-lg shrink-0 shadow-xs">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <div class="font-heading font-extrabold text-sm text-slate-900 flex items-center gap-2">
                        <span>Shift Kasir Belum Dibuka — Transaksi POS Terkunci</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-200 text-amber-900 uppercase">Perlu Buka Shift</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Sebagai kasir, Anda harus membuka shift dan mencatat modal awal kas sebelum melayani pembelian ataupun scan barang.
                    </p>
                </div>
            </div>
            <button type="button" @click="showOpenShiftModal = true" class="px-4 py-2.5 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-heading font-extrabold transition flex items-center gap-2 shadow-sm whitespace-nowrap cursor-pointer">
                <i class="fas fa-key text-amber-400"></i>
                <span>Buka Shift Sekarang</span>
            </button>
        </div>

        <!-- MAIN POS WORKSPACE (2 Column Grid on Desktop, Stack on Mobile) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- LEFT AREA: CATALOG & FAST TAP ITEMS (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                <!-- Search & Filter Controls -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs space-y-2.5">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari barang atau SKU (Gas, Kopi, Ponco, Blanket)..."
                            class="touch-target w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 text-xs text-slate-900 bg-slate-50/50 focus:bg-white focus:border-[#1E3A2B]">
                    </div>

                    <!-- Category Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar text-[11px] font-semibold">
                        <button type="button" @click="activeCategory = 'ALL'" :class="activeCategory === 'ALL' ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                            Semua ({{ count($products) }})
                        </button>
                        <button type="button" @click="activeCategory = 'GAS_BAHAN_BAKAR'" :class="activeCategory === 'GAS_BAHAN_BAKAR' ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                            <i class="fas fa-fire mr-1 text-orange-400"></i> Gas Portable
                        </button>
                        <button type="button" @click="activeCategory = 'PRODUK_LOKAL'" :class="activeCategory === 'PRODUK_LOKAL' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                            <i class="fas fa-handshake mr-1 text-amber-300"></i> UMKM Cigedug
                        </button>
                        <button type="button" @click="activeCategory = 'PERLENGKAPAN'" :class="activeCategory === 'PERLENGKAPAN' ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                            Perlengkapan
                        </button>
                        <button type="button" @click="activeCategory = 'LOGISTIK'" :class="activeCategory === 'LOGISTIK' ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                            Logistik Makanan
                        </button>
                    </div>
                </div>

                <!-- Product Grid (Fast Touch Tap) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-[580px] overflow-y-auto pr-1">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <button type="button" @click="addToCart(p)"
                            class="text-left p-3 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:border-[#1E3A2B] active:scale-97 transition flex flex-col justify-between min-h-[110px] group cursor-pointer">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span x-show="p.is_consignment" class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800">UMKM</span>
                                    <span x-show="!p.is_consignment" class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">SAMSARA</span>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="p.sku"></span>
                                </div>
                                <div class="font-heading font-bold text-xs text-slate-800 line-clamp-2 group-hover:text-[#1E3A2B]" x-text="p.name"></div>
                            </div>

                            <div class="mt-2 pt-1 border-t border-slate-100 flex items-center justify-between">
                                <span class="font-heading font-extrabold text-xs text-[#D97736]" x-text="formatRupiah(p.price)"></span>
                                <span class="text-[10px] text-slate-400" x-text="'Stok: ' + p.stock"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- RIGHT AREA: UNIFIED CART & INSTANT CHECKOUT (5 cols) -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-4 sm:p-5 flex flex-col justify-between min-h-[550px]">
                    <div>
                        <!-- Cart Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="font-heading font-extrabold text-sm text-slate-900">Keranjang Transaksi</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 font-bold text-slate-600" x-text="cart.length + ' item'"></span>
                            </div>

                            <button type="button" @click="clearCart()" class="text-xs font-semibold text-rose-500 hover:text-rose-700">
                                Reset
                            </button>
                        </div>

                        <!-- Customer Info Row -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Nama Pelanggan</label>
                                <input type="text" x-model="customerName" class="w-full text-xs rounded-lg border border-slate-300 px-2 py-1.5">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">No. HP / WA</label>
                                <input type="tel" x-model="customerPhone" placeholder="08..." class="w-full text-xs rounded-lg border border-slate-300 px-2 py-1.5">
                            </div>
                        </div>

                        <!-- SIMAKSI BOOKING ATTACHMENT SECTION -->
                        <div class="mb-3 p-2.5 rounded-xl border transition" :class="attachedBooking ? 'bg-emerald-50/80 border-emerald-300' : 'bg-slate-50/70 border-slate-200/80'">
                            <div class="flex items-center justify-between text-xs font-bold mb-1" :class="attachedBooking ? 'text-emerald-950' : 'text-slate-700'">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-ticket-alt" :class="attachedBooking ? 'text-emerald-700' : 'text-slate-400'"></i>
                                    <span>SIMAKSI Pendakian:</span>
                                </span>
                                <div>
                                    <template x-if="!attachedBooking">
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="showBookingLookupModal = true" class="text-[10px] font-bold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 px-2 py-0.5 rounded transition cursor-pointer">
                                                <i class="fas fa-search mr-1"></i>Cari
                                            </button>
                                            <button type="button" @click="showWalkInModal = true" class="text-[10px] font-bold text-white bg-[#1E3A2B] hover:bg-[#12241B] px-2 py-0.5 rounded transition cursor-pointer">
                                                <i class="fas fa-plus mr-1"></i>Walk-In
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="attachedBooking">
                                        <button type="button" @click="detachBooking()" class="text-[10px] text-rose-600 hover:underline font-bold cursor-pointer">
                                            <i class="fas fa-times mr-0.5"></i>Lepas
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <template x-if="attachedBooking">
                                <div class="mt-1.5 pt-1.5 border-t border-emerald-200/60 text-xs">
                                    <div class="flex items-center justify-between font-bold text-slate-900">
                                        <span class="font-mono text-emerald-800" x-text="attachedBooking.booking_code"></span>
                                        <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-emerald-200 text-emerald-900" x-text="attachedBooking.total_hikers + ' Pendaki'"></span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 mt-0.5">
                                        Ketua: <strong class="text-slate-800" x-text="attachedBooking.leader_name"></strong> &bull; Tgl: <span x-text="attachedBooking.hiking_date"></span>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] mt-1 pt-1 border-t border-dashed border-emerald-200 text-slate-700">
                                        <span>Tiket SIMAKSI (<span x-text="attachedBooking.total_hikers"></span> org):</span>
                                        <strong x-text="formatRupiah(attachedBooking.total_ticket_amount)"></strong>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] text-emerald-800">
                                        <span>Eco-Deposit (Jaminan Sampah):</span>
                                        <strong x-text="formatRupiah(attachedBooking.eco_deposit_amount)"></strong>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!attachedBooking">
                                <p class="text-[11px] text-slate-400 italic">Belum ada booking SIMAKSI. Klik 'Cari' atau '+ Walk-In' untuk mendaftarkan pendaki.</p>
                            </template>
                        </div>

                        <!-- OJEK ROUTE TICKET SELECTOR -->
                        <div class="mb-3 p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/80">
                            <div class="flex items-center justify-between text-xs font-bold text-amber-900 mb-1">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-motorcycle text-[#D97736]"></i> Layanan Ojek Paguyuban:
                                </span>
                                <button type="button" x-show="selectedOjekRoute" @click="selectedOjekRoute = ''; updateTotal()" class="text-[10px] text-rose-600 hover:underline">Hapus</button>
                            </div>
                            <select x-model="selectedOjekRoute" @change="updateTotal()" class="w-full text-xs rounded-lg border border-amber-300 bg-white p-1.5 text-slate-800 focus:ring-1 focus:ring-[#D97736]">
                                <option value="">-- Tanpa Tiket Ojek --</option>
                                <template x-for="r in ojekRoutes" :key="r.id">
                                    <option :value="r.id" x-text="r.route_name + ' (+Rp ' + Number(r.fare_amount).toLocaleString('id-ID') + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <!-- RENTAL ITEMS SECTION -->
                        <div class="mb-3 p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
                            <div class="flex items-center justify-between text-xs font-bold text-emerald-900 mb-1">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-campground text-emerald-700"></i> Sewa Alat Outdoor:
                                </span>
                                <button type="button" @click="showRentalModal = true" class="text-[11px] font-extrabold text-[#1E3A2B] bg-emerald-200/80 hover:bg-emerald-300 px-2 py-0.5 rounded-md transition cursor-pointer">
                                    <i class="fas fa-plus mr-1"></i>Pilih Alat
                                </button>
                            </div>

                            <template x-if="selectedRentals.length > 0">
                                <div class="space-y-1.5 mt-2 pt-2 border-t border-emerald-200/60 max-h-36 overflow-y-auto">
                                    <template x-for="item in selectedRentals" :key="item.id">
                                        <div class="text-xs bg-white/90 p-2 rounded-lg border border-emerald-100 flex items-center justify-between">
                                            <div class="flex-1 pr-2">
                                                <div class="font-bold text-slate-800" x-text="item.name"></div>
                                                <div class="text-[10px] text-slate-500">
                                                    Tarif: <span class="font-semibold text-emerald-700" x-text="formatRupiah(item.rate) + '/hari'"></span> &bull; Deposit: <span class="font-semibold" x-text="formatRupiah(item.deposit)"></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <div class="flex items-center gap-1 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                                    <button type="button" @click="updateRentalDays(item.id, -1)" class="text-xs font-bold text-slate-700 px-1 hover:text-rose-600">-</button>
                                                    <span class="text-[11px] font-bold text-slate-900" x-text="item.days + ' hr'"></span>
                                                    <button type="button" @click="updateRentalDays(item.id, 1)" class="text-xs font-bold text-slate-700 px-1 hover:text-emerald-600">+</button>
                                                </div>
                                                <span class="font-bold text-slate-900 text-right min-w-16" x-text="formatRupiah((item.rate * item.days) + item.deposit)"></span>
                                                <button type="button" @click="removeRental(item.id)" class="text-rose-500 hover:text-rose-700 ml-1 p-1">
                                                    <i class="fas fa-trash-alt text-[11px]"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="selectedRentals.length === 0">
                                <p class="text-[11px] text-emerald-700/80 italic">Belum ada sewa alat dipilih.</p>
                            </template>
                        </div>

                        <!-- Cart Items List -->
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1 divide-y divide-slate-100">
                            <template x-for="item in cart" :key="item.id">
                                <div class="pt-2 flex items-center justify-between text-xs">
                                    <div class="flex-1 pr-2">
                                        <div class="font-bold text-slate-800" x-text="item.name"></div>
                                        <div class="text-[11px] text-[#D97736] font-semibold" x-text="formatRupiah(item.price)"></div>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="updateQty(item.id, -1)" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center">-</button>
                                        <span class="w-5 text-center font-bold text-slate-900" x-text="item.qty"></span>
                                        <button type="button" @click="updateQty(item.id, 1)" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center">+</button>
                                        <span class="font-bold text-slate-900 min-w-16 text-right" x-text="formatRupiah(item.price * item.qty)"></span>
                                    </div>
                                </div>
                            </template>

                            <div x-show="cart.length === 0" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fas fa-shopping-basket text-2xl text-slate-300 mb-1"></i>
                                <p>Keranjang kosong. Tap barang di katalog untuk menambahkan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Checkout Panel -->
                    <div class="pt-4 border-t border-slate-200 mt-4">
                        <div x-show="bookingSimaksiTotal > 0" class="flex items-center justify-between text-xs text-emerald-800 mb-1 font-semibold">
                            <span>SIMAKSI & Jaminan Sampah:</span>
                            <span class="font-bold" x-text="formatRupiah(bookingSimaksiTotal)"></span>
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span>Subtotal Belanja & Lainnya:</span>
                            <span class="font-bold text-slate-800" x-text="formatRupiah(subtotal - bookingSimaksiTotal)"></span>
                        </div>

                        <div class="flex items-center justify-between font-heading font-black text-lg text-slate-900 mb-3">
                            <span>TOTAL TAGIHAN:</span>
                            <span class="text-[#D97736]" x-text="formatRupiah(subtotal)"></span>
                        </div>

                        <!-- Payment Method Toggle -->
                        <div class="grid grid-cols-3 gap-1.5 mb-3">
                            <button type="button" @click="paymentMethod = 'CASH'; setPaid(subtotal)" :class="paymentMethod === 'CASH' ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 text-slate-700'" class="touch-target py-2 rounded-xl text-xs font-bold transition">
                                <i class="fas fa-money-bill-wave mr-1"></i> Tunai
                            </button>
                            <button type="button" @click="paymentMethod = 'QRIS'; setPaid(subtotal)" :class="paymentMethod === 'QRIS' ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 text-slate-700'" class="touch-target py-2 rounded-xl text-xs font-bold transition">
                                <i class="fas fa-qrcode mr-1"></i> QRIS
                            </button>
                            <button type="button" @click="paymentMethod = 'TRANSFER'; setPaid(subtotal)" :class="paymentMethod === 'TRANSFER' ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 text-slate-700'" class="touch-target py-2 rounded-xl text-xs font-bold transition">
                                <i class="fas fa-university mr-1"></i> Transfer
                            </button>
                        </div>

                        <!-- Cash Denomination Quick Buttons (Only when CASH) -->
                        <div x-show="paymentMethod === 'CASH'" class="space-y-2 mb-3">
                            <div class="flex items-center gap-1.5 text-xs">
                                <button type="button" @click="setPaid(subtotal)" class="flex-1 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold">Uang Pas</button>
                                <button type="button" @click="setPaid(50000)" class="flex-1 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold">50k</button>
                                <button type="button" @click="setPaid(100000)" class="flex-1 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold">100k</button>
                                <button type="button" @click="setPaid(200000)" class="flex-1 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold">200k</button>
                            </div>

                            <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-500 font-medium">Uang Diterima:</span>
                                <div class="w-32">
                                    <x-ui.currency-input
                                        model="paidAmount"
                                        :value="0"
                                        size="sm"
                                        align="right"
                                        class="font-bold text-slate-900"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs px-2">
                                <span class="text-slate-500 font-medium">Kembalian:</span>
                                <span class="font-extrabold text-emerald-700" x-text="formatRupiah(changeAmount)"></span>
                            </div>
                        </div>

                        <!-- SUBMIT CHECKOUT BUTTON -->
                        <button type="button" @click="submitCheckout()"
                            :disabled="(cart.length === 0 && !bookingId && !attachedBooking && !selectedOjekRoute && selectedRentals.length === 0) && (!isKasir || hasActiveShift)"
                            class="touch-target w-full rounded-2xl font-heading font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer"
                            :class="(isKasir && !hasActiveShift) ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-[#D97736] hover:bg-[#B25A20] disabled:bg-slate-300 text-white'">
                            <template x-if="isKasir && !hasActiveShift">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-lock"></i>
                                    <span>Buka Shift untuk Transaksi</span>
                                </span>
                            </template>
                            <template x-if="!isKasir || hasActiveShift">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-print"></i>
                                    <span>Bayar & Cetak Struk Kasir</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- OPEN SHIFT POPUP MODAL (KASIR LEVEL LOCK) -->
        <div x-show="showOpenShiftModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showOpenShiftModal" @click="showOpenShiftModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="showOpenShiftModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 relative z-10">

                    <!-- Modal Header -->
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#D97736] border border-amber-200 flex items-center justify-center text-xl shrink-0 shadow-2xs">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#D97736] block">SOP Kasir Basecamp</span>
                                <h3 class="font-heading font-extrabold text-lg text-slate-900 leading-tight">
                                    Buka Shift Kasir Terlebih Dahulu
                                </h3>
                            </div>
                        </div>
                        <button type="button" @click="showOpenShiftModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>

                    <!-- Alert message if triggered by action -->
                    <template x-if="shiftAlertMessage">
                        <div class="mb-4 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-rose-600 shrink-0"></i>
                            <span x-text="shiftAlertMessage" class="font-medium"></span>
                        </div>
                    </template>

                    <p class="text-xs text-slate-600 mb-5 leading-relaxed">
                        Sesuai standar operasional POS SAMSARA, petugas kasir wajib mencatat saldo kas awal fisik di laci kasir sebelum melayani transaksi penjualan atau sewa alat.
                    </p>

                    <!-- Open Shift Form -->
                    <form action="{{ route('pos.shift.open') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Modal Kas Awal di Laci (Uang Kembalian)
                            </label>
                            <x-ui.currency-input
                                name="opening_cash"
                                model="openingCashInput"
                                :value="500000"
                                placeholder="500.000"
                                required
                                class="text-base font-extrabold text-slate-900"
                            />

                            <!-- Quick Preset Denomination Chips -->
                            <div class="grid grid-cols-4 gap-2 mt-2.5">
                                <button type="button" @click="openingCashInput = 200000"
                                    :class="openingCashInput === 200000 ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                    class="py-1.5 px-2 rounded-xl text-xs font-bold transition text-center cursor-pointer">
                                    200k
                                </button>
                                <button type="button" @click="openingCashInput = 300000"
                                    :class="openingCashInput === 300000 ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                    class="py-1.5 px-2 rounded-xl text-xs font-bold transition text-center cursor-pointer">
                                    300k
                                </button>
                                <button type="button" @click="openingCashInput = 500000"
                                    :class="openingCashInput === 500000 ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                    class="py-1.5 px-2 rounded-xl text-xs font-bold transition text-center cursor-pointer">
                                    500k
                                </button>
                                <button type="button" @click="openingCashInput = 1000000"
                                    :class="openingCashInput === 1000000 ? 'bg-[#1E3A2B] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                    class="py-1.5 px-2 rounded-xl text-xs font-bold transition text-center cursor-pointer">
                                    1 Juta
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-2">
                                *Jumlah kas awal akan menjadi acuan rekonsiliasi saat tutup shift di akhir hari.
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="showOpenShiftModal = false"
                                class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-600 font-bold text-xs transition cursor-pointer">
                                Batal (Hanya Lihat)
                            </button>
                            <button type="submit"
                                class="flex-1 px-5 py-2.5 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-heading font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-key text-amber-400"></i>
                                <span>Buka Shift & Aktifkan Kasir</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RENTAL ITEM SELECTION MODAL -->
        <div x-show="showRentalModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showRentalModal" @click="showRentalModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="showRentalModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl transform transition-all max-w-2xl w-full border border-slate-200 relative z-10">

                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#1E3A2B] flex items-center justify-center text-lg">
                                <i class="fas fa-campground"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-extrabold text-base text-slate-900">Pilih Alat Rental Tersedia</h3>
                                <p class="text-xs text-slate-500">Peralatan camping Cigedug Basecamp yang siap disewa</p>
                            </div>
                        </div>
                        <button type="button" @click="showRentalModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>

                    <div class="max-h-96 overflow-y-auto pr-1 divide-y divide-slate-100">
                        <template x-for="r in allRentals" :key="r.id">
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-bold text-sm text-slate-800" x-text="r.name"></div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                        <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-[10px]" x-text="r.code"></span>
                                        <span>Tarif: <strong class="text-emerald-700" x-text="formatRupiah(r.rate) + '/hari'"></strong></span>
                                        <span>&bull; Deposit: <strong class="text-slate-700" x-text="formatRupiah(r.deposit)"></strong></span>
                                    </div>
                                </div>
                                <div>
                                    <button type="button"
                                        x-show="!selectedRentals.some(x => x.id === r.id)"
                                        @click="addRental(r)"
                                        class="px-3 py-1.5 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                        <i class="fas fa-plus"></i> Tambahkan
                                    </button>
                                    <span x-show="selectedRentals.some(x => x.id === r.id)"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold inline-flex items-center gap-1">
                                        <i class="fas fa-check"></i> Sudah Dipilih
                                    </span>
                                </div>
                            </div>
                        </template>
                        <div x-show="allRentals.length === 0" class="py-8 text-center text-slate-400 text-xs">
                            <i class="fas fa-box-open text-2xl mb-1 text-slate-300"></i>
                            <p>Tidak ada alat rental yang berstatus READY saat ini.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="button" @click="showRentalModal = false" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 cursor-pointer">
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PICKUP SCANNER MODAL -->
        <div x-show="showPickupModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showPickupModal" @click="showPickupModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="showPickupModal" class="inline-block bg-white rounded-3xl p-6 text-left shadow-2xl transform transition-all max-w-md w-full border border-slate-200 relative z-10">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="font-heading font-extrabold text-base text-slate-900 flex items-center gap-2">
                            <i class="fas fa-barcode text-[#D97736]"></i> Scan Pengambilan Pre-Order
                        </h3>
                        <button type="button" @click="showPickupModal = false; pickupResult = null" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Input / Scan Barcode Pre-Order</label>
                            <input type="text" x-model="pickupCode" placeholder="Scan barcode invoice atau kode booking..."
                                class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm uppercase font-mono">
                        </div>

                        <button type="button" @click="submitPickup()" class="touch-target w-full rounded-xl bg-[#1E3A2B] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Verifikasi Pengambilan Barang
                        </button>

                        <!-- Pickup Result Box -->
                        <div x-show="pickupResult" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs">
                            <div class="font-bold mb-1" x-text="pickupResult?.message"></div>
                            <div class="text-[11px] text-emerald-700">Barang siap diserahkan ke pendaki. Catatan otomatis tercatat di sistem.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIMAKSI BOOKING LOOKUP MODAL -->
        <div x-show="showBookingLookupModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showBookingLookupModal" @click="showBookingLookupModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="showBookingLookupModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl transform transition-all max-w-xl w-full border border-slate-200 relative z-10">

                    <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#1E3A2B] flex items-center justify-center text-lg shrink-0">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-extrabold text-base text-slate-900">Cari & Hubungkan SIMAKSI</h3>
                                <p class="text-xs text-slate-500">Cari booking online/mandiri pendaki yang berstatus Menunggu Pembayaran.</p>
                            </div>
                        </div>
                        <button type="button" @click="showBookingLookupModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Booking, Nama Ketua, atau No. HP</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <i class="fas fa-qrcode absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                                <input type="text" x-model="bookingSearchQuery" @keydown.enter.prevent="searchBooking()" @input.debounce.350ms="searchBooking()"
                                    placeholder="Contoh: SMX-2611... atau nama pendaki..."
                                    class="touch-target w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 text-xs uppercase font-mono bg-slate-50 focus:bg-white focus:border-[#1E3A2B]">
                            </div>
                            <button type="button" @click="searchBooking()" :disabled="isSearchingBooking"
                                class="px-4 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer disabled:bg-slate-300">
                                <template x-if="!isSearchingBooking">
                                    <span class="flex items-center gap-1"><i class="fas fa-search"></i> Cari</span>
                                </template>
                                <template x-if="isSearchingBooking">
                                    <span class="flex items-center gap-1"><i class="fas fa-circle-notch fa-spin"></i></span>
                                </template>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">*Bisa langsung scan barcode e-ticket pendaki dari kamera atau barcode scanner USB.</p>
                    </div>

                    <!-- Results List -->
                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        <template x-for="b in bookingSearchResults" :key="b.id">
                            <div class="p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50/70 border border-slate-200 hover:border-emerald-300 transition flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-black text-xs text-emerald-800" x-text="b.booking_code"></span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800">Menunggu Bayar</span>
                                    </div>
                                    <div class="font-bold text-slate-800 text-xs mt-0.5" x-text="b.leader_name + ' (' + b.total_hikers + ' Orang)'"></div>
                                    <div class="text-[10px] text-slate-500">
                                        Tgl Naik: <strong x-text="b.hiking_date"></strong> &bull; HP: <span x-text="b.leader_phone"></span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-[#D97736] mt-1">
                                        Total: <span x-text="formatRupiah((parseFloat(b.total_ticket_amount) || 0) + (parseFloat(b.eco_deposit_amount) || 0))"></span>
                                        <span class="text-[10px] text-slate-400 font-normal">(Termasuk Eco-Deposit)</span>
                                    </div>
                                </div>
                                <button type="button" @click="attachBooking(b)"
                                    class="px-3.5 py-2 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer whitespace-nowrap">
                                    <i class="fas fa-check"></i>
                                    <span>Pilih Ini</span>
                                </button>
                            </div>
                        </template>

                        <div x-show="bookingSearchResults.length === 0 && !isSearchingBooking" class="py-8 text-center text-slate-400 text-xs">
                            <i class="fas fa-ticket-alt text-2xl mb-1 text-slate-300"></i>
                            <p>Masukkan kode booking atau nama ketua untuk melihat daftar tiket pending.</p>
                            <p class="text-[11px] text-slate-400 mt-1">Jika pendaki belum pernah mendaftar online, gunakan tombol <strong>+ Walk-In</strong> di bawah.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-3">
                        <button type="button" @click="showBookingLookupModal = false; showWalkInModal = true" class="text-xs font-bold text-[#D97736] hover:underline flex items-center gap-1">
                            <i class="fas fa-user-plus"></i> Pendaki belum daftar? Buka Walk-In
                        </button>
                        <button type="button" @click="showBookingLookupModal = false" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIMAKSI WALK-IN (ON-THE-SPOT) REGISTRATION MODAL -->
        <div x-show="showWalkInModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showWalkInModal" @click="showWalkInModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="showWalkInModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl transform transition-all max-w-2xl w-full border border-slate-200 relative z-10 max-h-[90vh] flex flex-col">

                    <!-- Modal Header -->
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-[#1E3A2B] text-white flex items-center justify-center text-lg shrink-0 shadow-xs">
                                <i class="fas fa-hiking text-amber-300"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-heading font-extrabold text-base sm:text-lg text-slate-900">Registrasi SIMAKSI Walk-In (OTS)</h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">On The Spot Basecamp</span>
                                </div>
                                <p class="text-xs text-slate-500">Input cepat untuk pendaki yang datang langsung tanpa booking online.</p>
                            </div>
                        </div>
                        <button type="button" @click="showWalkInModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>

                    <!-- Scrollable Form Body -->
                    <div class="overflow-y-auto pr-1 space-y-4 flex-1">
                        <!-- Hiking Date & Duration -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-200/80">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai Pendakian</label>
                                <input type="date" x-model="walkInForm.hiking_date" class="touch-target w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 font-semibold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Durasi Pendakian</label>
                                <select x-model.number="walkInForm.duration_days" class="touch-target w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 font-semibold">
                                    <option value="1">1 Hari (Tek-Tok Langsung Turun)</option>
                                    <option value="2">2 Hari 1 Malam (Standar Camp)</option>
                                    <option value="3">3 Hari 2 Malam (Santai)</option>
                                    <option value="4">4 Hari 3 Malam (Maksimal)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Leader Info -->
                        <div>
                            <span class="text-xs font-extrabold text-slate-900 block mb-2 flex items-center gap-1.5">
                                <i class="fas fa-user-shield text-[#1E3A2B]"></i> Data Ketua Rombongan
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Lengkap Ketua <span class="text-rose-500">*</span></label>
                                    <input type="text" x-model="walkInForm.leader_name" placeholder="Sesuai KTP" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">NIK Ketua (16 Digit) <span class="text-rose-500">*</span></label>
                                    <input type="text" x-model="walkInForm.leader_nik" maxlength="16" placeholder="320501..." class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                                    <input type="tel" x-model="walkInForm.leader_phone" placeholder="08..." class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Alamat Asal / Kota</label>
                                    <input type="text" x-model="walkInForm.leader_address" placeholder="Contoh: Bandung / Garut" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Kontak Darurat Keluarga <span class="text-rose-500">*</span></label>
                                    <input type="text" x-model="walkInForm.emergency_contact_name" placeholder="Nama Orang Tua / Kerabat" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. HP Kontak Darurat <span class="text-rose-500">*</span></label>
                                    <input type="tel" x-model="walkInForm.emergency_contact_phone" placeholder="08..." class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                </div>
                            </div>
                        </div>

                        <!-- Tent details & notes -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Warna / Ciri Tenda (Opsional SAR)</label>
                                <input type="text" x-model="walkInForm.tent_color_details" placeholder="Contoh: Dome Biru Kapasitas 4" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Catatan Tambahan Loket</label>
                                <input type="text" x-model="walkInForm.notes" placeholder="Catatan rombongan..." class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                            </div>
                        </div>

                        <!-- Members Manifest Section -->
                        <div class="pt-2 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                                    <i class="fas fa-users text-[#1E3A2B]"></i> Manifest Anggota Rombongan (<span x-text="walkInForm.members.length"></span> Orang)
                                </span>
                                <button type="button" @click="addWalkInMember()" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-900 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-plus"></i> Tambah Anggota
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(m, idx) in walkInForm.members" :key="idx">
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-wrap sm:flex-nowrap items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                                        <div class="flex-1 min-w-[140px]">
                                            <input type="text" x-model="m.full_name" placeholder="Nama Lengkap Anggota *" class="w-full text-xs rounded-lg border border-slate-300 px-2 py-1.5 bg-white">
                                        </div>
                                        <div class="w-32">
                                            <input type="text" x-model="m.nik" maxlength="16" placeholder="NIK (opsional)" class="w-full text-xs font-mono rounded-lg border border-slate-300 px-2 py-1.5 bg-white">
                                        </div>
                                        <div class="w-16">
                                            <select x-model="m.gender" class="w-full text-xs rounded-lg border border-slate-300 px-1 py-1.5 bg-white">
                                                <option value="L">L</option>
                                                <option value="P">P</option>
                                            </select>
                                        </div>
                                        <div class="w-16">
                                            <input type="number" x-model.number="m.age" min="10" max="80" placeholder="Usia" class="w-full text-xs rounded-lg border border-slate-300 px-1 py-1.5 bg-white">
                                        </div>
                                        <div class="flex-1 min-w-[120px]">
                                            <input type="text" x-model="m.medical_history" placeholder="Riwayat Sakit (Sehat)" class="w-full text-xs rounded-lg border border-slate-300 px-2 py-1.5 bg-white">
                                        </div>
                                        <button type="button" x-show="walkInForm.members.length > 1" @click="removeWalkInMember(idx)" class="text-rose-500 hover:text-rose-700 p-1.5 shrink-0 cursor-pointer">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer & Real-Time Estimate -->
                    <div class="pt-4 border-t border-slate-200 mt-4 shrink-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 p-3 rounded-2xl">
                        <div>
                            <div class="text-[11px] text-slate-500">Estimasi Tagihan SIMAKSI Walk-In:</div>
                            <div class="text-sm font-extrabold text-[#D97736]">
                                <span x-text="formatRupiah(walkInEstimasiTotal)"></span>
                                <span class="text-[10px] text-slate-400 font-normal">
                                    (<span x-text="walkInForm.members.length"></span> org x <span x-text="formatRupiah(ticketPricePerPerson)"></span> + Eco-Deposit <span x-text="formatRupiah(ecoDepositFee)"></span>)
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="showWalkInModal = false" :disabled="walkInSubmitting"
                                class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-600 font-bold text-xs cursor-pointer">
                                Batal
                            </button>
                            <button type="button" @click="submitWalkIn()" :disabled="walkInSubmitting"
                                class="px-5 py-2.5 rounded-xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-heading font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer disabled:bg-slate-300">
                                <template x-if="!walkInSubmitting">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-cart-plus text-amber-300"></i>
                                        <span>Daftarkan & Masukkan ke Kasir</span>
                                    </span>
                                </template>
                                <template x-if="walkInSubmitting">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-circle-notch fa-spin text-amber-300"></i>
                                        <span>Memproses Kuota...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
