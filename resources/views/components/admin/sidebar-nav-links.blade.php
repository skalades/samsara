@php
    $role = auth()->user()?->role?->value;
    $isSuperadmin = $role === 'SUPERADMIN';
    $isAdminSar = $role === 'ADMIN_SAR';
    $isKasir = $role === 'KASIR';
    $isPetugasPos = $role === 'PETUGAS_POS';

    // Helper closure for link styles
    $linkClass = function(bool $active) {
        if ($active) {
            return 'flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-[#2D5841] to-[#1E3A2B] text-white font-bold text-xs shadow-inner border-l-4 border-[#D97736] transition duration-150';
        }
        return 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-emerald-100/80 hover:bg-[#2D5841]/50 hover:text-white font-medium text-xs transition duration-150 group';
    };

    $iconClass = function(bool $active, string $defaultColor = 'text-emerald-400') {
        if ($active) {
            return 'text-[#D97736] text-sm';
        }
        return $defaultColor . ' text-sm group-hover:text-amber-300 transition duration-150';
    };
@endphp

<!-- SECTION 1: PUSAT KENDALI (Superadmin & Admin SAR) -->
@if ($isSuperadmin || $isAdminSar)
    <div class="space-y-1">
        <div class="px-3.5 pb-1.5 flex items-center justify-between">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400/70">Pusat Kendali & SAR</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
        </div>

        <nav class="space-y-1">
            @php $active = request()->routeIs('admin.index'); @endphp
            <a href="{{ route('admin.index') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-gauge-high {{ $iconClass($active, 'text-[#D97736]') }}"></i></span>
                    <span>Command Center</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('admin.analytics'); @endphp
            <a href="{{ route('admin.analytics') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-chart-line {{ $iconClass($active, 'text-amber-400') }}"></i></span>
                    <span>Analitik & Tren</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('admin.tracker') || request()->routeIs('admin.sar-export'); @endphp
            <a href="{{ route('admin.tracker') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-map-location-dot {{ $iconClass($active, 'text-emerald-400') }}"></i></span>
                    <span>Live Pos Tracker</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('admin.checkpoints.*'); @endphp
            <a href="{{ route('admin.checkpoints.index') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-camera {{ $iconClass($active, 'text-teal-400') }}"></i></span>
                    <span>Foto Checkpoint</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>
        </nav>
    </div>
@endif

<!-- SECTION 2: OPERASIONAL BASECAMP (Superadmin, Admin SAR, Kasir, Petugas Pos) -->
@if ($isSuperadmin || $isAdminSar || $isKasir || $isPetugasPos)
    <div class="space-y-1">
        <div class="px-3.5 pb-1.5 flex items-center justify-between">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400/70">Operasional Loket</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
        </div>

        <nav class="space-y-1">
            @if ($isSuperadmin || $isKasir)
                @php $active = request()->routeIs('pos.index') || request()->routeIs('pos.invoice'); @endphp
                <a href="{{ route('pos.index') }}" class="{{ $linkClass($active) }}">
                    <span class="flex items-center gap-3">
                        <span class="w-5 text-center"><i class="fas fa-cash-register {{ $iconClass($active, 'text-sky-400') }}"></i></span>
                        <span>Kasir Terpadu POS</span>
                    </span>
                    @if ($active)
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                    @endif
                </a>

                @php $active = request()->routeIs('pos.shift'); @endphp
                <a href="{{ route('pos.shift') }}" class="{{ $linkClass($active) }}">
                    <span class="flex items-center gap-3">
                        <span class="w-5 text-center"><i class="fas fa-vault {{ $iconClass($active, 'text-amber-400') }}"></i></span>
                        <span>Shift Laci Kasir</span>
                    </span>
                    @if ($active)
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                    @endif
                </a>

                @php $active = request()->routeIs('rental.*'); @endphp
                <a href="{{ route('rental.index') }}" class="{{ $linkClass($active) }}">
                    <span class="flex items-center gap-3">
                        <span class="w-5 text-center"><i class="fas fa-screwdriver-wrench {{ $iconClass($active, 'text-emerald-400') }}"></i></span>
                        <span>Rental & QC Alat</span>
                    </span>
                    @if ($active)
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                    @endif
                </a>
            @endif

            @if ($isSuperadmin || $isAdminSar || $isKasir)
                @php $active = request()->routeIs('ojek.*'); @endphp
                <a href="{{ route('ojek.index') }}" class="{{ $linkClass($active) }}">
                    <span class="flex items-center gap-3">
                        <span class="w-5 text-center"><i class="fas fa-motorcycle {{ $iconClass($active, 'text-amber-400') }}"></i></span>
                        <span>Antrean Ojek Paguyuban</span>
                    </span>
                    @if ($active)
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                    @endif
                </a>
            @endif

            @if ($isSuperadmin || $isAdminSar || $isPetugasPos)
                @php $active = request()->routeIs('fieldops.*'); @endphp
                <a href="{{ route('fieldops.index') }}" class="{{ $linkClass($active) }}">
                    <span class="flex items-center gap-3">
                        <span class="w-5 text-center"><i class="fas fa-qrcode {{ $iconClass($active, 'text-orange-400') }}"></i></span>
                        <span>Scanner Pos 1-4 PWA</span>
                    </span>
                    @if ($active)
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                    @endif
                </a>
            @endif
        </nav>
    </div>
@endif

<!-- SECTION 3: ADMINISTRASI & FINANSIAL (Superadmin & Admin SAR) -->
@if ($isSuperadmin || $isAdminSar)
    <div class="space-y-1">
        <div class="px-3.5 pb-1.5 flex items-center justify-between">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400/70">Manajemen & Finansial</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
        </div>

        <nav class="space-y-1">
            @php $active = request()->routeIs('admin.bookings.*'); @endphp
            <a href="{{ route('admin.bookings.pending') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-receipt {{ $iconClass($active, 'text-emerald-400') }}"></i></span>
                    <span>Verifikasi SIMAKSI</span>
                </span>
                @if ($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-[#D97736] text-white text-[10px] font-extrabold shadow-xs">
                        {{ $pendingCount }}
                    </span>
                @elseif ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('admin.quota*'); @endphp
            <a href="{{ route('admin.quota') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-calendar-check {{ $iconClass($active, 'text-indigo-400') }}"></i></span>
                    <span>Kelola Kuota Jalur</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('consignment.*'); @endphp
            <a href="{{ route('consignment.index') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-handshake {{ $iconClass($active, 'text-yellow-400') }}"></i></span>
                    <span>Mitra UMKM Konsinyasi</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('admin.inventory.*'); @endphp
            <a href="{{ route('admin.inventory.index') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-boxes-stacked {{ $iconClass($active, 'text-amber-400') }}"></i></span>
                    <span>Katalog Produk & Sewa</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('admin.finance*'); @endphp
            <a href="{{ route('admin.finance') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-file-invoice-dollar {{ $iconClass($active, 'text-emerald-400') }}"></i></span>
                    <span>Bagi Hasil Multi-Pihak</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @php $active = request()->routeIs('admin.settings*'); @endphp
            <a href="{{ route('admin.settings.index') }}" class="{{ $linkClass($active) }}">
                <span class="flex items-center gap-3">
                    <span class="w-5 text-center"><i class="fas fa-sliders {{ $iconClass($active, 'text-sky-400') }}"></i></span>
                    <span>Tarif & SIMAKSI Jalur</span>
                </span>
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                @endif
            </a>

            @if ($isSuperadmin)
                @php $active = request()->routeIs('admin.users.*'); @endphp
                <a href="{{ route('admin.users.index') }}" class="{{ $linkClass($active) }}">
                    <span class="flex items-center gap-3">
                        <span class="w-5 text-center"><i class="fas fa-users-gear {{ $iconClass($active, 'text-rose-400') }}"></i></span>
                        <span>Kelola Staf & Akun</span>
                    </span>
                    @if ($active)
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D97736]"></span>
                    @endif
                </a>
            @endif
        </nav>
    </div>
@endif
