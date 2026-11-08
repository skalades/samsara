<div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs mb-12 scroll-mt-24" id="faq"
     x-data="{
         active: 1,
         filter: 'ALL',
         faqs: [
             {
                 id: 1,
                 category: 'SIMAKSI',
                 question: 'Apakah wajib booking SIMAKSI online sebelum datang?',
                 answer: 'Sangat disarankan melakukan booking online melalui portal ini untuk menjamin ketersediaan kuota harian (kuota dibatasi 300 orang/hari saat weekend dan 150 orang/hari saat weekday). Pendaftaran on-the-spot di loket hanya dilayani bila sisa kuota hari itu masih tersedia.'
             },
             {
                 id: 2,
                 category: 'TRANSPORT',
                 question: 'Bagaimana rute transportasi umum dari Jakarta/Bandung ke Basecamp Cigedug?',
                 answer: 'Naik bus antar-kota menuju Terminal Guntur Garut. Dari terminal, naik angkutan umum (Elf/Angkot) jurusan Cikajang / Cigedug dan turun di Pertigaan Pasar Cigedug. Dari Pasar Cigedug, gunakan ojek lokal resmi langsung menuju gerbang Basecamp SAMSARA (±15 menit).'
             },
             {
                 id: 3,
                 category: 'RUTE',
                 question: 'Apakah ada sumber air di jalur pendakian Cigedug?',
                 answer: 'Sumber air melimpah terdapat di Basecamp SAMSARA (kran & toilet bersih). Di atas jalur, mata air alami terdapat di dekat Pos 3 (Alam Rimba, turun sekitar 50m ke lembah). Namun karena sifatnya musiman saat kemarau panjang, setiap pendaki diwajibkan membawa perbekalan air minimal 2.5 hingga 3 Liter per orang dari basecamp.'
             },
             {
                 id: 4,
                 category: 'SAFETY',
                 question: 'Bagaimana jika terjadi cuaca buruk, badai, atau anggota cedera di jalur?',
                 answer: 'Basecamp SAMSARA memiliki posko siaga SAR di Pos 4 Samudra Awan dan Basecamp. Segera lapor ke petugas Pos terdekat atau hubungi Hotline WhatsApp Darurat Basecamp. Pengelola memiliki sistem SOS 1-Click SAR untuk evakuasi cepat ke bawah.'
             },
             {
                 id: 5,
                 category: 'SEWA',
                 question: 'Apakah bisa menyewa tenda dan alat gunung langsung di tempat?',
                 answer: 'Bisa! Basecamp SAMSARA menyediakan persewaan tenda dome Great Outdoor/Consina, sleeping bag polar tebal, matras, kompor, dan nesting. Anda bisa melihat katalognya di menu Sewa Alat pada web ini untuk memastikan ketersediaan unit sebelum berangkat.'
             },
             {
                 id: 6,
                 category: 'SIMAKSI',
                 question: 'Bagaimana sistem pengembalian Eco-Deposit jaminan sampah Rp 20.000?',
                 answer: 'Saat registrasi di loket, rombongan menitipkan jaminan sampah Rp 20.000 dan diberikan trash bag resmi. Ketika selesai mendaki dan turun ke basecamp, serahkan kantong sampah rombongan ke pos pemeriksaan. Petugas akan mengembalikan uang jaminan Rp 20.000 utuh 100% tanpa potongan.'
             }
         ],
         get filteredFaqs() {
             if (this.filter === 'ALL') return this.faqs;
             return this.faqs.filter(f => f.category === this.filter);
         }
     }">
    <div class="text-center max-w-xl mx-auto mb-8">
        <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#D97736]">
            <i class="fas fa-circle-question"></i> Bantuan & Jawaban Cepat
        </span>
        <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 mt-1">
            Frequently Asked Questions (FAQ)
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Pertanyaan paling sering diajukan seputar reservasi kuota, transportasi Garut, logistik, dan keamanan pendakian Cikuray via Cigedug.
        </p>

        <!-- Category Filter Pills -->
        <div class="flex items-center justify-center gap-1.5 mt-5 flex-wrap text-xs">
            <button type="button" @click="filter = 'ALL'"
                    class="px-3 py-1 rounded-full font-bold transition cursor-pointer"
                    :class="filter === 'ALL' ? 'bg-[#1E3A2B] text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                Semua
            </button>
            <button type="button" @click="filter = 'SIMAKSI'"
                    class="px-3 py-1 rounded-full font-bold transition cursor-pointer"
                    :class="filter === 'SIMAKSI' ? 'bg-[#1E3A2B] text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                SIMAKSI & Kuota
            </button>
            <button type="button" @click="filter = 'RUTE'"
                    class="px-3 py-1 rounded-full font-bold transition cursor-pointer"
                    :class="filter === 'RUTE' ? 'bg-[#1E3A2B] text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                Jalur & Air
            </button>
            <button type="button" @click="filter = 'SEWA'"
                    class="px-3 py-1 rounded-full font-bold transition cursor-pointer"
                    :class="filter === 'SEWA' ? 'bg-[#1E3A2B] text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                Sewa Alat
            </button>
            <button type="button" @click="filter = 'TRANSPORT'"
                    class="px-3 py-1 rounded-full font-bold transition cursor-pointer"
                    :class="filter === 'TRANSPORT' ? 'bg-[#1E3A2B] text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                Transportasi
            </button>
        </div>
    </div>

    <div class="space-y-3 max-w-3xl mx-auto text-xs sm:text-sm">
        <template x-for="faq in filteredFaqs" :key="faq.id">
            <div class="border border-slate-200/90 rounded-2xl overflow-hidden transition">
                <button type="button" @click="active = (active === faq.id ? null : faq.id)"
                        class="w-full p-4 sm:p-5 text-left font-bold text-slate-800 bg-slate-50/70 hover:bg-slate-100 flex items-center justify-between transition cursor-pointer">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-question-circle text-[#D97736] shrink-0"></i>
                        <span x-text="faq.question"></span>
                    </span>
                    <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                       :class="active === faq.id ? 'rotate-180 text-[#D97736]' : ''"></i>
                </button>
                <div x-show="active === faq.id" x-collapse
                     class="p-4 sm:p-5 bg-white text-slate-600 leading-relaxed border-t border-slate-100"
                     x-text="faq.answer">
                </div>
            </div>
        </template>
    </div>
</div>
