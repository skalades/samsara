<x-layouts.admin title="Kelola Kuota & Blackout">
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Mountain Pulse
            </a>
            <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900">Pengaturan Kuota Dinamis & Blackout</h1>
            <p class="text-xs sm:text-sm text-slate-500">Atur kapasitas kuota harian dan tetapkan tanggal blackout penutupan berkala jalur.</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
            <h3 class="font-heading font-extrabold text-base text-slate-900 mb-4">Daftar Kuota 30 Hari Kedepan</h3>

            <div class="space-y-3">
                @foreach ($quotas as $q)
                    <form action="{{ route('admin.quota.update') }}" method="POST" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                        @csrf
                        <input type="hidden" name="date" value="{{ $q->date->toDateString() }}">

                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl {{ $q->is_blackout ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }} flex items-center justify-center font-bold text-xs">
                                {{ $q->date->format('d') }}
                            </span>
                            <div>
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $q->date->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    Terpesan: <span class="font-bold text-slate-800">{{ $q->booked_count }}</span> orang &bull;
                                    Sisa: <span class="font-bold text-emerald-700">{{ $q->remaining_quota }}</span> orang
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <div class="flex items-center gap-1">
                                <span class="text-slate-500 text-[11px]">Maks:</span>
                                <input type="number" name="max_quota" value="{{ $q->max_quota }}" class="w-20 text-center font-bold text-slate-800 rounded-lg border border-slate-300 p-1.5 text-xs">
                            </div>

                            <label class="flex items-center gap-1 cursor-pointer p-1.5 rounded-lg border {{ $q->is_blackout ? 'border-rose-300 bg-rose-50 text-rose-800' : 'border-slate-300 bg-white text-slate-700' }}">
                                <input type="checkbox" name="is_blackout" value="1" {{ $q->is_blackout ? 'checked' : '' }} class="rounded text-rose-600">
                                <span class="font-bold text-[11px]">Blackout</span>
                            </label>

                            <input type="text" name="notes" value="{{ $q->notes }}" placeholder="Keterangan blackout..."
                                class="w-40 rounded-lg border border-slate-300 p-1.5 text-[11px]">

                            <button type="submit" class="touch-target px-3 py-1.5 rounded-lg bg-[#1E3A2B] hover:bg-[#12241B] text-white font-bold text-[11px] transition">
                                Simpan
                            </button>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
