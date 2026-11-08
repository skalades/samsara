<x-layouts.app title="Kalender Kuota & SIMAKSI Cikuray">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                <i class="fas fa-calendar-check"></i> Reservasi Kuota Resmi
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">SIMAKSI Online Gunung Cikuray</h1>
            <p class="text-xs sm:text-sm text-slate-500">Pilih tanggal pendakian untuk memeriksa ketersediaan kuota harian via Cigedug.</p>
        </div>

        <a href="{{ route('simaksi.create') }}" class="touch-target px-4 py-2.5 rounded-xl bg-[#D97736] hover:bg-[#B25A20] text-white font-semibold text-xs sm:text-sm tracking-wide shadow-xs transition flex items-center justify-center gap-2">
            <i class="fas fa-ticket-alt"></i> Langsung Booking Kuota
        </a>
    </div>

    <!-- CALENDAR CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs mb-8">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#1E3A2B] flex items-center justify-center text-lg">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h2 class="font-heading font-bold text-base text-slate-900">
                        {{ \Carbon\Carbon::create($year, $month, 1)->locale('id')->isoFormat('MMMM Y') }}
                    </h2>
                    <p class="text-xs text-slate-500">Kuota normal: Weekend 300 orang &bull; Weekday 150 orang</p>
                </div>
            </div>

            <!-- Month Switcher -->
            <div class="flex items-center gap-1">
                @php
                    $prevMonth = \Carbon\Carbon::create($year, $month, 1)->subMonth();
                    $nextMonth = \Carbon\Carbon::create($year, $month, 1)->addMonth();
                @endphp
                <a href="{{ route('simaksi.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="touch-target w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center text-xs">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('simaksi.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="touch-target w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center text-xs">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <!-- CALENDAR GRID -->
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2.5">
            @foreach ($calendar as $day)
                @php
                    $isFull = $day['remaining'] <= 0 && ! $day['is_blackout'];
                    $isBlackout = $day['is_blackout'];
                    $isPast = $day['is_past'];
                @endphp
                <div class="p-3 rounded-2xl border transition duration-150 flex flex-col justify-between min-h-[105px]
                    {{ $isPast ? 'bg-slate-50/60 border-slate-100 opacity-60' : '' }}
                    {{ $isBlackout ? 'bg-rose-50/80 border-rose-200' : '' }}
                    {{ $isFull ? 'bg-amber-50/80 border-amber-200' : '' }}
                    {{ ! $isPast && ! $isBlackout && ! $isFull ? 'bg-white border-slate-200 hover:border-emerald-400 hover:shadow-xs' : '' }}
                    {{ $day['is_today'] ? 'ring-2 ring-[#D97736]' : '' }}
                ">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold font-heading {{ $day['is_weekend'] ? 'text-[#D97736]' : 'text-slate-800' }}">
                            {{ $day['day_number'] }} {{ $day['day_name'] }}
                        </span>
                        @if ($day['is_today'])
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#D97736] text-white">Hari Ini</span>
                        @endif
                    </div>

                    <div class="my-1.5">
                        @if ($isBlackout)
                            <span class="text-[10px] font-bold text-rose-700 bg-rose-100/70 px-1.5 py-0.5 rounded block text-center">
                                <i class="fas fa-ban mr-0.5"></i> JALUR TUTUP
                            </span>
                            <span class="text-[9px] text-rose-600 line-clamp-1 mt-0.5">{{ $day['notes'] ?? 'Blackout' }}</span>
                        @elseif ($isFull)
                            <span class="text-[10px] font-bold text-amber-800 bg-amber-100/80 px-1.5 py-0.5 rounded block text-center">
                                KUOTA HABIS
                            </span>
                        @elseif ($isPast)
                            <span class="text-[10px] text-slate-400">Telah Lewat</span>
                        @else
                            <div class="text-xs font-bold text-emerald-800">
                                Sisa <span class="text-emerald-600 font-extrabold">{{ $day['remaining'] }}</span>
                            </div>
                            <!-- Progress bar -->
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $day['percentage'] }}%"></div>
                            </div>
                        @endif
                    </div>

                    <div>
                        @if (! $isPast && ! $isBlackout && ! $isFull)
                            <a href="{{ route('simaksi.create', ['date' => $day['date']]) }}" class="touch-target w-full text-[10px] font-bold py-1 px-2 rounded-lg bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-800 text-center transition flex items-center justify-center">
                                Pilih Tanggal
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
