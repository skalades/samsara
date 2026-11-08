<x-layouts.app title="Formulir SIMAKSI Cikuray">
    <div class="max-w-3xl mx-auto" x-data="{
        ticketPrice: {{ (int) ($trailSetting->ticket_fee_per_person ?? 25000) }},
        ecoDeposit: {{ (int) ($trailSetting->eco_deposit_fee ?? 20000) }},
        members: [
            { full_name: '', nik: '', gender: 'L', age: 24, phone: '', medical_history: '' }
        ],
        addMember() {
            this.members.push({ full_name: '', nik: '', gender: 'L', age: 22, phone: '', medical_history: '' });
        },
        removeMember(index) {
            if (this.members.length > 1) {
                this.members.splice(index, 1);
            }
        },
        get totalTicket() {
            return this.members.length * this.ticketPrice;
        },
        get grandTotal() {
            return this.totalTicket + this.ecoDeposit;
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
        }
    }">
        <!-- Heading -->
        <div class="mb-6">
            <a href="{{ route('simaksi.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Kalender Kuota
            </a>
            <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">Registrasi SIMAKSI & Manifest</h1>
            <p class="text-xs sm:text-sm text-slate-500">Lengkapi data rombongan untuk penerbitan E-Ticket resmi Gunung Cikuray via Cigedug.</p>
        </div>

        <form action="{{ route('simaksi.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- STEP 1: JADWAL & DURASI -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#1E3A2B] flex items-center justify-center font-bold text-sm">1</div>
                    <h2 class="font-heading font-bold text-base text-slate-900">Jadwal & Durasi Pendakian</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Naik (Check-in Basecamp)</label>
                        <input type="date" name="hiking_date" value="{{ old('hiking_date', $selectedDate) }}" required min="{{ today()->toDateString() }}"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Durasi Pendakian</label>
                        <select name="duration_days" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                            <option value="2">2 Hari 1 Malam (Standar Camp Pos 4)</option>
                            <option value="1">1 Hari (Tek-Tok / Day Hike)</option>
                            <option value="3">3 Hari 2 Malam (Santai)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 2: DATA KETUA ROMBONGAN -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#1E3A2B] flex items-center justify-center font-bold text-sm">2</div>
                    <h2 class="font-heading font-bold text-base text-slate-900">Data Ketua Rombongan</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap Sesuai KTP</label>
                        <input type="text" name="leader_name" value="{{ old('leader_name') }}" required placeholder="Contoh: Muhammad Fikri"
                            @input="members[0].full_name = $event.target.value"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor NIK KTP (16 Digit)</label>
                        <input type="text" name="leader_nik" value="{{ old('leader_nik') }}" required maxlength="16" placeholder="3205xxxxxxxxxxxx"
                            @input="members[0].nik = $event.target.value"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. WhatsApp / HP Aktif</label>
                        <input type="tel" name="leader_phone" value="{{ old('leader_phone') }}" required placeholder="08xxxxxxxxxx"
                            @input="members[0].phone = $event.target.value"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email (Untuk Pengiriman E-Ticket)</label>
                        <input type="email" name="leader_email" value="{{ old('leader_email') }}" required placeholder="nama@email.com"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Domisili Asal</label>
                        <input type="text" name="leader_address" value="{{ old('leader_address') }}" required placeholder="Kota Bandung, Jawa Barat"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kontak Darurat Keluarga (Nama & Hubungan)</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required placeholder="Contoh: Ibu Siti (Ibu Kandung)"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Telepon Kontak Darurat</label>
                        <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required placeholder="08xxxxxxxxxx"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-[#1E3A2B] focus:ring-1 focus:ring-[#1E3A2B]">
                    </div>
                </div>
            </div>

            <!-- STEP 3: MANIFEST ANGGOTA ROMBONGAN (Dynamic Multi-Row) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#1E3A2B] flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <h2 class="font-heading font-bold text-base text-slate-900">Manifest Anggota Rombongan</h2>
                            <p class="text-xs text-slate-500">Seluruh personil yang naik ke jalur wajib terdata untuk keselamatan SAR.</p>
                        </div>
                    </div>

                    <button type="button" @click="addMember()" class="touch-target px-3 py-1.5 rounded-xl bg-emerald-50 text-[#1E3A2B] hover:bg-emerald-100 text-xs font-bold border border-emerald-200 flex items-center gap-1.5 transition">
                        <i class="fas fa-user-plus text-[#D97736]"></i> Tambah Anggota
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(member, idx) in members" :key="idx">
                        <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200 relative">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold font-heading text-slate-700 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-[#1E3A2B] text-white flex items-center justify-center text-[10px]" x-text="idx + 1"></span>
                                    <span x-text="idx === 0 ? 'Pendaki 1 (Ketua Rombongan)' : 'Pendaki ' + (idx + 1)"></span>
                                </span>

                                <button type="button" x-show="idx > 0" @click="removeMember(idx)" class="text-rose-500 hover:text-rose-700 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                                    <input type="text" :name="'members['+idx+'][full_name]'" x-model="member.full_name" required placeholder="Nama personil"
                                        class="touch-target w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">NIK (16 Digit)</label>
                                    <input type="text" :name="'members['+idx+'][nik]'" x-model="member.nik" required maxlength="16" placeholder="NIK KTP"
                                        class="touch-target w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white">
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Gender</label>
                                        <select :name="'members['+idx+'][gender]'" x-model="member.gender" class="touch-target w-full rounded-xl border border-slate-300 px-2 py-1.5 text-xs text-slate-900 bg-white">
                                            <option value="L">L</option>
                                            <option value="P">P</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Usia</label>
                                        <input type="number" :name="'members['+idx+'][age]'" x-model="member.age" min="10" max="75" required
                                            class="touch-target w-full rounded-xl border border-slate-300 px-2 py-1.5 text-xs text-slate-900 bg-white">
                                    </div>
                                </div>

                                <div class="sm:col-span-4 bg-amber-50/70 p-3 rounded-2xl border border-amber-200/80">
                                    <label class="block text-xs font-bold text-amber-950 mb-1 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-heartbeat text-rose-500"></i>
                                            <span>Riwayat Penyakit Bawaan / Medis / Alergi</span>
                                        </span>
                                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">Wajib SAR</span>
                                    </label>
                                    <input type="text" :name="'members['+idx+'][medical_history]'" x-model="member.medical_history" placeholder="Contoh: Asma ringan, alergi dingin, riwayat cedera lutut, atau isi 'Sehat'"
                                        class="touch-target w-full rounded-xl border border-amber-300 px-3 py-2 text-xs text-slate-900 bg-white focus:ring-1 focus:ring-amber-500">
                                    <p class="text-[10px] text-amber-900/70 mt-1 leading-tight">
                                        *Tuliskan riwayat penyakit bawaan atau obat pribadi yang dibawa demi keselamatan pendakian. Jika sehat tanpa riwayat, cukup isi <strong>"Sehat"</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- STEP 4: PERLENGKAPAN TENDA & PEMBAYARAN -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#1E3A2B] flex items-center justify-center font-bold text-sm">4</div>
                    <h2 class="font-heading font-bold text-base text-slate-900">Perlengkapan Camp & Pembayaran</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi & Warna Tenda Rombongan</label>
                        <input type="text" name="tent_color_details" value="{{ old('tent_color_details') }}" placeholder="Contoh: Tenda Orange 4P, Carrier Biru & Hitam"
                            class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Metode Pembayaran Tiket</label>
                        <select name="payment_method" class="touch-target w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900">
                            <option value="QRIS">QRIS Dinamis (Instan Semua E-Wallet & Bank)</option>
                            <option value="CASH">Bayar Tunai di Loket Basecamp</option>
                            <option value="TRANSFER">Transfer Rekening Bank SAMSARA</option>
                        </select>
                    </div>
                </div>

                <!-- LIVE PRICE SUMMARY BOX -->
                <div class="p-4 rounded-2xl bg-emerald-950 text-white mt-4 border border-emerald-800">
                    <div class="flex items-center justify-between text-xs text-emerald-200 pb-2 border-b border-emerald-800/80">
                        <span>Rincian Tiket SIMAKSI (<span x-text="members.length"></span> Pendaki x Rp 25.000)</span>
                        <span class="font-bold text-white" x-text="formatRupiah(totalTicket)"></span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-emerald-200 py-2 border-b border-emerald-800/80">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-leaf text-emerald-400"></i> Jaminan Sampah (Eco-Deposit Refundable)
                        </span>
                        <span class="font-bold text-emerald-300" x-text="formatRupiah(ecoDeposit)"></span>
                    </div>

                    <div class="flex items-center justify-between pt-3">
                        <div>
                            <span class="text-xs text-emerald-200 font-medium">Total Pembayaran</span>
                            <div class="text-xl sm:text-2xl font-heading font-extrabold text-[#D97736]" x-text="formatRupiah(grandTotal)"></div>
                        </div>

                        <button type="submit" class="touch-target px-6 py-3 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white font-bold text-sm shadow-lg transition flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Selesaikan Registrasi
                        </button>
                    </div>

                    <div class="mt-2 text-[10px] text-emerald-300/80 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        <span>Eco-Deposit Rp 20.000 akan dikembalikan tunai 100% di loket saat membawa kembali sampah ke Basecamp.</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
