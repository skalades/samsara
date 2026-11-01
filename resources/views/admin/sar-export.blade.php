<x-layouts.admin title="Manifest SAR {{ $booking->booking_code }}">
    <div class="max-w-3xl mx-auto space-y-4">
        <div class="flex items-center justify-between no-print">
            <a href="{{ route('admin.tracker') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Pos Tracker
            </a>

            <button type="button" onclick="window.print()" class="touch-target px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Dokumen SAR (1-Click PDF)
            </button>
        </div>

        <!-- PRINTABLE OFFICIAL SAR MANIFEST SHEET -->
        <div class="bg-white rounded-3xl border-2 border-slate-900 shadow-md p-6 sm:p-8 space-y-5 text-slate-900">
            <!-- Header SAR -->
            <div class="flex items-center justify-between pb-4 border-b-2 border-slate-900">
                <div class="flex items-center gap-3">
                    <img src="/LOGO.jpeg" class="w-12 h-12 rounded-xl object-cover border border-slate-800">
                    <div>
                        <div class="font-heading font-black text-lg tracking-wider text-slate-900">TIM SAR BASECAMP SAMSARA</div>
                        <div class="text-xs text-slate-600 font-semibold">FORMULIR MANIFEST OPERASI PENCARIAN & EVAKUASI JALUR CIKURAY</div>
                        <div class="text-[10px] text-slate-500">Cigedug, Garut &bull; Posko Darurat: 0812-9876-5432</div>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-rose-100 text-rose-800 border border-rose-300">DOKUMEN EVAKUASI</span>
                    <div class="font-mono font-black text-base mt-1">{{ $booking->booking_code }}</div>
                    <div class="text-[10px] text-slate-400">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</div>
                </div>
            </div>

            <!-- KEY EMERGENCY DETAILS -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-300">
                <div>
                    <span class="text-slate-500 block text-[10px]">Posisi Terakhir:</span>
                    <span class="font-heading font-extrabold text-[#D97736] text-sm">{{ $booking->current_pos }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Total Personil:</span>
                    <span class="font-heading font-bold text-slate-900 text-sm">{{ $booking->total_hikers }} Orang</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Tanggal Check-in:</span>
                    <span class="font-bold text-slate-900">{{ $booking->checkin_at ? $booking->checkin_at->format('d/m H:i') : $booking->hiking_date->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Warna/Tipe Tenda:</span>
                    <span class="font-bold text-rose-700">{{ $booking->tent_color_details ?: 'Tenda Dome' }}</span>
                </div>
            </div>

            <!-- LEADER & EMERGENCY CONTACTS -->
            <div class="p-4 rounded-2xl border border-slate-200 text-xs space-y-2">
                <div class="font-bold uppercase tracking-wider text-[11px] text-slate-700 pb-1 border-b border-slate-100">
                    Kontak Utama & Keluarga Korban:
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-500">Ketua Rombongan:</span>
                        <div class="font-bold text-slate-900">{{ $booking->leader_name }} (NIK: {{ $booking->leader_nik }})</div>
                        <div class="text-slate-600">No. HP: {{ $booking->leader_phone }}</div>
                        <div class="text-slate-500 text-[11px]">Alamat: {{ $booking->leader_address }}</div>
                    </div>
                    <div>
                        <span class="text-slate-500">Kontak Darurat Keluarga:</span>
                        <div class="font-bold text-rose-800">{{ $booking->emergency_contact_name }}</div>
                        <div class="font-bold text-rose-700">No. Telepon: {{ $booking->emergency_contact_phone }}</div>
                    </div>
                </div>
            </div>

            <!-- DETAILED MEMBER MANIFEST & MEDICAL HISTORY -->
            <div>
                <div class="font-heading font-extrabold text-xs text-slate-900 uppercase tracking-wider mb-2">
                    Daftar Manifest Personil & Riwayat Medis:
                </div>

                <div class="rounded-xl border border-slate-300 overflow-hidden text-xs">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-100 text-[11px] font-bold text-slate-700 border-b border-slate-300">
                            <tr>
                                <th class="p-2.5">No</th>
                                <th class="p-2.5">Nama Lengkap</th>
                                <th class="p-2.5">NIK KTP</th>
                                <th class="p-2.5">L/P & Usia</th>
                                <th class="p-2.5">Riwayat Penyakit / Alergi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($booking->members as $idx => $m)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5 font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-2.5 font-bold text-slate-900">{{ $m->full_name }}</td>
                                    <td class="p-2.5 font-mono text-[11px] text-slate-600">{{ $m->nik }}</td>
                                    <td class="p-2.5">{{ $m->gender }}, {{ $m->age }} th</td>
                                    <td class="p-2.5">
                                        <span class="font-semibold {{ $m->medical_history && !str_contains(strtolower($m->medical_history), 'tidak') ? 'text-rose-700 bg-rose-50 px-2 py-0.5 rounded' : 'text-slate-500' }}">
                                            {{ $m->medical_history ?: 'Tidak ada catatan' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TIMELINE SCAN CHECKPOINT LAPANGAN -->
            <div>
                <div class="font-heading font-extrabold text-xs text-slate-900 uppercase tracking-wider mb-2">
                    Riwayat Pemindaian Checkpoint Terakhir:
                </div>
                <div class="space-y-1.5 text-xs">
                    @forelse ($booking->checkpoints as $cp)
                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <span class="font-bold text-slate-800">{{ $cp->pos_name }} ({{ $cp->direction === 'ASCENDING' ? 'Naik' : 'Turun' }})</span>
                            <span class="text-slate-500">{{ $cp->created_at->format('d/m/Y H:i:s') }} WIB &bull; Catatan: {{ $cp->notes ?: 'Normal' }}</span>
                        </div>
                    @empty
                        <div class="text-slate-400 italic text-xs">Belum ada pemindaian checkpoint.</div>
                    @endforelse
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="pt-6 border-t border-slate-300 grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <span class="text-slate-500">Koordinator Lapangan Basecamp</span>
                    <div class="h-16"></div>
                    <div class="font-bold underline text-slate-900">( Sdr. Dadan SAR SAMSARA )</div>
                </div>
                <div>
                    <span class="text-slate-500">Komandan Tim Evakuasi SAR</span>
                    <div class="h-16"></div>
                    <div class="font-bold underline text-slate-900">( ............................................ )</div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
