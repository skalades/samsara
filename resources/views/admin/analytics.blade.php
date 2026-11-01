<x-layouts.admin title="Analitik & Statistik Pendakian | SAMSARA SIMS">
    <div class="space-y-8" x-data="{
        activeDataset: 'hikers',
        year: {{ $year }}
    }">
        <!-- TOP ADAPTIVE HEADER BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D97736] uppercase tracking-wider mb-1">
                    <i class="fas fa-chart-line"></i> Business Intelligence & Field Insights
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                    Analitik & Statistik Jalur SAMSARA
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Monitoring tren pergerakan pendaki, kepadatan hari, efektivitas konversi booking SIMAKSI, dan penjualan toko.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Year Selector Form -->
                <form action="{{ route('admin.analytics') }}" method="GET" class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-2xl border border-slate-200 shadow-xs">
                    <label for="year-select" class="text-xs font-bold text-slate-500">Tahun:</label>
                    <select id="year-select" name="year" onchange="this.form.submit()" class="text-xs font-bold text-slate-900 bg-transparent border-0 focus:ring-0 cursor-pointer">
                        @foreach (range(date('Y') - 1, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('admin.index') }}" class="touch-target px-4 py-2.5 rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Command Center</span>
                </a>
            </div>
        </div>

        <!-- 4 KPI SUMMARY STAT CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            <x-ui.stat-card
                title="Conversion Rate SIMAKSI"
                value="{{ $conversionStats['conversion_rate'] }}%"
                subtext="Pengunjung web yang booking"
                icon="fas fa-filter"
                color="earth"
            />

            <x-ui.stat-card
                title="Total Pendaki {{ $year }}"
                value="{{ number_format($monthlyTrends['total_hikers'], 0, ',', '.') }} Orang"
                subtext="{{ number_format($monthlyTrends['total_bookings'], 0, ',', '.') }} Rombongan Terdaftar"
                icon="fas fa-person-hiking"
                color="forest"
            />

            <x-ui.stat-card
                title="Hari Paling Padat"
                value="{{ $peakHeatmap['busiest_day'] }}"
                subtext="{{ number_format($peakHeatmap['busiest_day_total'], 0, ',', '.') }} pendaki di hari tersebut"
                icon="fas fa-calendar-check"
                color="amber"
            />

            <x-ui.stat-card
                title="Top Produk Toko"
                value="{{ $topProducts[0]['name'] ?? 'N/A' }}"
                subtext="{{ $topProducts[0]['total_qty'] ?? 0 }} terjual ({{ $topProducts[0]['type'] ?? '' }})"
                icon="fas fa-fire"
                color="emerald"
            />
        </div>

        <!-- 1. GRAFIK TREN PENDAKI PER BULAN (CHART.JS) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-100 gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1E3A2B]"></span>
                        <span class="text-xs font-bold text-[#D97736] uppercase tracking-wider font-mono">Volume Trajectory</span>
                    </div>
                    <h2 class="font-heading font-extrabold text-xl sm:text-2xl text-slate-900">
                        Grafik Tren Pendaki per Bulan (Tahun {{ $year }})
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Fluktuasi kuota pendakian berdasarkan data tiket SIMAKSI resmi. Bulan puncak tahun ini: <strong class="text-emerald-700">{{ $monthlyTrends['peak_month'] }} ({{ $monthlyTrends['peak_hikers'] }} Pendaki)</strong>.
                    </p>
                </div>

                <!-- Chart Metric Toggle Buttons -->
                <div class="inline-flex rounded-2xl bg-slate-100 p-1 border border-slate-200/70 text-xs font-bold shrink-0 self-start sm:self-center">
                    <button type="button" @click="activeDataset = 'hikers'; updateChart();"
                            class="px-4 py-2 rounded-xl transition cursor-pointer"
                            :class="activeDataset === 'hikers' ? 'bg-[#1E3A2B] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <i class="fas fa-users mr-1.5 text-emerald-300"></i> Total Pendaki (Jiwa)
                    </button>
                    <button type="button" @click="activeDataset = 'bookings'; updateChart();"
                            class="px-4 py-2 rounded-xl transition cursor-pointer"
                            :class="activeDataset === 'bookings' ? 'bg-[#D97736] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <i class="fas fa-ticket-alt mr-1.5 text-amber-200"></i> Jumlah Rombongan
                    </button>
                </div>
            </div>

            <!-- Chart.js Canvas Container -->
            <div class="mt-6 relative" style="height: 340px;">
                <canvas id="monthlyTrendChart"></canvas>
            </div>

            <!-- Chart Insight Footer Pill -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-[#1E3A2B] inline-block"></span>
                        <strong class="text-slate-700">Total Pendaki:</strong> {{ number_format($monthlyTrends['total_hikers'], 0, ',', '.') }} orang
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-[#D97736] inline-block"></span>
                        <strong class="text-slate-700">Total Rombongan:</strong> {{ number_format($monthlyTrends['total_bookings'], 0, ',', '.') }} booking
                    </span>
                </div>
                <div class="text-[11px] font-mono text-emerald-800 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                    <i class="fas fa-sparkles text-amber-500"></i> Rata-rata rombongan: {{ $monthlyTrends['total_bookings'] > 0 ? round($monthlyTrends['total_hikers'] / $monthlyTrends['total_bookings'], 1) : 0 }} orang/kelompok
                </div>
            </div>
        </div>

        <!-- 2. HEATMAP HARI PALING PADAT (BULAN VS HARI) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-100 gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D97736]"></span>
                        <span class="text-xs font-bold text-[#D97736] uppercase tracking-wider font-mono">Density Matrix</span>
                    </div>
                    <h2 class="font-heading font-extrabold text-xl sm:text-2xl text-slate-900">
                        Heatmap Hari Paling Padat (Bulan vs Hari)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Pemetaan konsentrasi kepadatan jalur pendakian sepanjang tahun {{ $year }}. Arahkan kursor atau sentuh sel untuk rincian data.
                    </p>
                </div>

                <!-- Heatmap Color Scale Legend -->
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-semibold bg-slate-50 p-2.5 rounded-2xl border border-slate-200">
                    <span class="mr-1">Intensitas:</span>
                    <span class="w-4 h-4 rounded-md bg-slate-100 border border-slate-200" title="0 pendaki"></span>
                    <span class="w-4 h-4 rounded-md bg-emerald-200" title="Rendah"></span>
                    <span class="w-4 h-4 rounded-md bg-emerald-400" title="Sedang"></span>
                    <span class="w-4 h-4 rounded-md bg-amber-400" title="Ramai"></span>
                    <span class="w-4 h-4 rounded-md bg-[#D97736]" title="Padat"></span>
                    <span class="w-4 h-4 rounded-md bg-rose-600" title="Sangat Padat (Puncak)"></span>
                    <span class="ml-1 text-[10px] text-slate-400">Tinggi</span>
                </div>
            </div>

            <!-- Heatmap Interactive Grid Table (Mobile Horizontal Scrollable) -->
            <div class="mt-6 overflow-x-auto no-scrollbar pb-2">
                <table class="w-full min-w-[700px] border-collapse text-xs">
                    <thead>
                        <tr class="text-slate-500 font-bold border-b border-slate-200">
                            <th class="py-3 px-3 text-left w-28 uppercase tracking-wider text-[11px]">Bulan</th>
                            @foreach ($peakHeatmap['days'] as $dayName)
                                <th class="py-3 px-2 text-center {{ in_array($dayName, ['Sabtu', 'Minggu']) ? 'text-[#D97736] font-black' : '' }}">
                                    {{ $dayName }}
                                </th>
                            @endforeach
                            <th class="py-3 px-3 text-right text-slate-700 font-black w-24">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($peakHeatmap['matrix'] as $monthNum => $days)
                            @php
                                $monthTotal = array_sum(array_column($days, 'count'));
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-2.5 px-3 font-bold text-slate-900 whitespace-nowrap">
                                    {{ $peakHeatmap['months'][$monthNum] }}
                                </td>
                                @foreach ($days as $dayNum => $cell)
                                    @php
                                        $intensity = $cell['intensity'];
                                        $count = $cell['count'];
                                        $groups = $cell['groups'];
                                        $bgClass = match ($intensity) {
                                            0 => 'bg-slate-50 text-slate-300',
                                            1 => 'bg-emerald-100 text-emerald-900 font-semibold',
                                            2 => 'bg-emerald-200 text-emerald-950 font-bold',
                                            3 => 'bg-amber-300 text-amber-950 font-bold',
                                            4 => 'bg-[#D97736] text-white font-black shadow-xs',
                                            5 => 'bg-rose-600 text-white font-black shadow-sm ring-1 ring-rose-400',
                                            default => 'bg-slate-50 text-slate-400',
                                        };
                                    @endphp
                                    <td class="p-1.5 text-center">
                                        <div class="group relative inline-block w-full">
                                            <div class="h-10 rounded-xl flex items-center justify-center transition-all duration-200 transform group-hover:scale-105 cursor-pointer {{ $bgClass }}">
                                                <span class="font-mono text-xs">{{ $count }}</span>
                                            </div>

                                            <!-- Interactive Hover Tooltip Popover -->
                                            <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-30 w-48 p-2.5 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl border border-white/10 text-center">
                                                <div class="font-bold text-amber-300">{{ $cell['month_name'] }} - {{ $cell['day_name'] }}</div>
                                                <div class="text-xs font-black mt-0.5">{{ $count }} Pendaki</div>
                                                <div class="text-[10px] text-slate-400">({{ $groups }} rombongan SIMAKSI)</div>
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800 whitespace-nowrap">
                                    {{ number_format($monthTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Heatmap Summary Insight Banner -->
            <div class="mt-6 p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-amber-900">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-amber-400 text-amber-950 flex items-center justify-center text-sm font-black shrink-0 shadow-xs">
                        <i class="fas fa-lightbulb"></i>
                    </span>
                    <div>
                        <strong class="font-heading font-black block">Pola Kepadatan Lapangan</strong>
                        <span>Hari <strong>{{ $peakHeatmap['busiest_day'] }}</strong> merupakan hari paling dominan dengan {{ number_format($peakHeatmap['busiest_day_total'], 0, ',', '.') }} pendaki. Disarankan penyiapan personel ranger ekstra di Pos 4 (Samudra Awan).</span>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <span class="text-[10px] text-amber-700 uppercase font-bold block">Kepadatan Maksimal Sel</span>
                    <span class="font-heading font-black text-sm text-[#D97736]">{{ $peakHeatmap['max_cell_hikers'] }} Jiwa/Hari</span>
                </div>
            </div>
        </div>

        <!-- 3 & 4. DUAL SECTION: CONVERSION RATE FUNNEL + TOP PRODUK TOKO -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- 3. CONVERSION RATE & FUNNEL (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                <span class="text-xs font-bold text-sky-600 uppercase tracking-wider font-mono">Acquisition Funnel</span>
                            </div>
                            <h2 class="font-heading font-extrabold text-xl text-slate-900">
                                Conversion Rate: Visitor &rarr; SIMAKSI
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Rasio pengunjung portal website resmi terhadap reservasi pendakian berhasil.
                            </p>
                        </div>

                        <!-- Big Badge CR -->
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-widest">Rate Konversi</span>
                            <span class="font-heading font-black text-3xl text-emerald-600">{{ $conversionStats['conversion_rate'] }}%</span>
                        </div>
                    </div>

                    <!-- Funnel Visual Stack -->
                    <div class="mt-6 space-y-4">
                        @foreach ($conversionStats['funnel'] as $stage)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-slate-800 flex items-center gap-2">
                                        <i class="{{ $stage['icon'] }} text-slate-400 text-xs"></i>
                                        <span>{{ $stage['stage'] }}</span>
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-900">{{ number_format($stage['count'], 0, ',', '.') }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">
                                            {{ $stage['percentage'] }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-xl h-3 overflow-hidden p-0.5">
                                    <div class="h-full rounded-lg transition-all duration-700 {{ $stage['color'] }}"
                                         style="width: {{ max($stage['percentage'], 3) }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Conversion Ratio Explanations -->
                <div class="mt-8 pt-5 border-t border-slate-100 grid grid-cols-2 gap-3 text-center">
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Total Pengunjung Unik</span>
                        <span class="font-heading font-extrabold text-lg text-slate-900 mt-0.5 block">
                            {{ number_format($conversionStats['unique_visitors'], 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-slate-500">Sesi IP/Browser</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Rasio Bayar dari Booking</span>
                        <span class="font-heading font-extrabold text-lg text-emerald-600 mt-0.5 block">
                            {{ $conversionStats['paid_conversion_rate'] }}%
                        </span>
                        <span class="text-[10px] text-slate-500">{{ $conversionStats['paid_bookings'] }} dari {{ $conversionStats['total_bookings'] }} lunas</span>
                    </div>
                </div>
            </div>

            <!-- 4. TOP PRODUK TERJUAL DI TOKO (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider font-mono">Retail Bestsellers</span>
                            </div>
                            <h2 class="font-heading font-extrabold text-xl text-slate-900">
                                Top Produk Terjual di Toko
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Produk paling diminati pendaki di kasir basecamp & pre-order.
                            </p>
                        </div>

                        <a href="{{ route('store.index') }}" class="text-xs font-bold text-[#D97736] hover:underline flex items-center gap-1 shrink-0">
                            Katalog <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <!-- Top Products Ranked List -->
                    <div class="mt-5 space-y-3">
                        @forelse ($topProducts as $product)
                            <div class="p-3.5 rounded-2xl bg-slate-50/80 hover:bg-slate-100/80 transition border border-slate-200/80 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <!-- Rank Medal Badge -->
                                    <span class="w-7 h-7 rounded-xl flex items-center justify-center font-heading font-black text-xs shrink-0
                                        {{ $product['rank'] === 1 ? 'bg-amber-400 text-amber-950 shadow-xs' : ($product['rank'] === 2 ? 'bg-slate-300 text-slate-800' : ($product['rank'] === 3 ? 'bg-orange-300 text-orange-950' : 'bg-white border border-slate-200 text-slate-600')) }}">
                                        #{{ $product['rank'] }}
                                    </span>

                                    <div>
                                        <div class="font-heading font-extrabold text-slate-900 leading-snug">
                                            {{ $product['name'] }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500">
                                            <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 text-[10px] font-semibold">
                                                {{ $product['type'] }}
                                            </span>
                                            <span>&bull;</span>
                                            <span>Rp {{ number_format($product['avg_price'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <div class="font-heading font-black text-slate-900 text-sm">
                                        {{ number_format($product['total_qty'], 0, ',', '.') }} <span class="text-[11px] font-normal text-slate-500">terjual</span>
                                    </div>
                                    <div class="text-[10px] font-mono text-emerald-700">
                                        Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs">
                                <i class="fas fa-box-open text-3xl mb-2 block"></i>
                                Belum ada riwayat penjualan produk toko.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-handshake text-amber-600"></i> Mendukung UMKM Konsinyasi Warga Cigedug
                    </span>
                    <a href="{{ route('consignment.index') }}" class="font-bold text-[#D97736] hover:underline">
                        Bagi Hasil UMKM &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- PUSH CHART.JS CDN SCRIPT -->
    @push('styles')
        <style>
            /* Custom Scrollbar for Heatmap */
            .no-scrollbar::-webkit-scrollbar {
                height: 6px;
            }
            .no-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 9999px;
            }
            .no-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 9999px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('monthlyTrendChart');
                if (!ctx) return;

                const labels = @json($monthlyTrends['labels']);
                const hikersData = @json($monthlyTrends['hikers']);
                const bookingsData = @json($monthlyTrends['bookings']);

                // Create gradient fills
                const chartCtx = ctx.getContext('2d');
                const forestGradient = chartCtx.createLinearGradient(0, 0, 0, 320);
                forestGradient.addColorStop(0, 'rgba(30, 58, 43, 0.45)');
                forestGradient.addColorStop(1, 'rgba(30, 58, 43, 0.02)');

                const earthGradient = chartCtx.createLinearGradient(0, 0, 0, 320);
                earthGradient.addColorStop(0, 'rgba(217, 119, 54, 0.45)');
                earthGradient.addColorStop(1, 'rgba(217, 119, 54, 0.02)');

                window.trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Pendaki (Jiwa)',
                                data: hikersData,
                                borderColor: '#1E3A2B',
                                backgroundColor: forestGradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#1E3A2B',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                yAxisID: 'y',
                            },
                            {
                                label: 'Jumlah Rombongan (Booking)',
                                data: bookingsData,
                                borderColor: '#D97736',
                                backgroundColor: earthGradient,
                                borderWidth: 2.5,
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0.35,
                                pointBackgroundColor: '#D97736',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                yAxisID: 'y1',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    font: {
                                        family: 'Plus Jakarta Sans',
                                        weight: 'bold',
                                        size: 12
                                    },
                                    color: '#334155'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Outfit', size: 13, weight: 'bold' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                                padding: 12,
                                cornerRadius: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y + (context.datasetIndex === 0 ? ' orang' : ' rombongan');
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 11 },
                                    color: '#64748b'
                                }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Pendaki (Jiwa)',
                                    color: '#1E3A2B',
                                    font: { family: 'Outfit', weight: 'bold', size: 11 }
                                },
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: { family: 'Plus Jakarta Sans', size: 11 },
                                    color: '#64748b'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Rombongan',
                                    color: '#D97736',
                                    font: { family: 'Outfit', weight: 'bold', size: 11 }
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    font: { family: 'Plus Jakarta Sans', size: 11 },
                                    color: '#64748b'
                                }
                            }
                        }
                    }
                });

                // Function to update chart visibility based on active toggle
                window.updateChart = function() {
                    const active = Alpine.$data(document.querySelector('[x-data]')).activeDataset;
                    if (!window.trendChart) return;

                    if (active === 'hikers') {
                        window.trendChart.setDatasetVisibility(0, true);
                        window.trendChart.setDatasetVisibility(1, false);
                    } else if (active === 'bookings') {
                        window.trendChart.setDatasetVisibility(0, false);
                        window.trendChart.setDatasetVisibility(1, true);
                    }
                    window.trendChart.update();
                };
            });
        </script>
    @endpush
</x-layouts.admin>
