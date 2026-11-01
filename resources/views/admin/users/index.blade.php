<x-layouts.admin title="Manajemen Pengguna & Staf">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
         x-data="{
            showAddModal: false,
            showEditModal: false,
            editData: {
                id: null,
                name: '',
                email: '',
                role: 'KASIR',
                phone: '',
                assigned_pos: 'Basecamp'
            },
            openEdit(user) {
                this.editData = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role: user.role,
                    phone: user.phone || '',
                    assigned_pos: user.assigned_pos || ''
                };
                this.showEditModal = true;
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
                    <span class="text-slate-800 font-bold">Manajemen Pengguna & Staf</span>
                </nav>
                <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fas fa-users-gear text-[#D97736]"></i>
                    Kelola Akun Staf & Hak Akses (RBAC)
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Kelola otentikasi akun Superadmin, Koordinator SAR, Kasir Basecamp, dan Petugas Pos Jalur 1–4.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.index') }}"
                   class="touch-target px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold border border-slate-300 flex items-center gap-2 shadow-xs transition">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <button type="button" @click="showAddModal = true"
                        class="touch-target px-4 py-2.5 rounded-xl bg-[#2D5841] hover:bg-[#1E3A2B] text-white text-xs sm:text-sm font-bold flex items-center gap-2 shadow-md transition active:scale-95">
                    <i class="fas fa-user-plus text-amber-300"></i> + Tambah Akun Staf
                </button>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Akun</span>
                <span class="font-heading font-extrabold text-2xl text-slate-900 mt-1 block">{{ $stats['total'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-rose-500 uppercase tracking-wider block">Superadmin</span>
                <span class="font-heading font-extrabold text-2xl text-rose-600 mt-1 block">{{ $stats['superadmin'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-amber-500 uppercase tracking-wider block">Admin SAR</span>
                <span class="font-heading font-extrabold text-2xl text-amber-600 mt-1 block">{{ $stats['sar'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-sky-500 uppercase tracking-wider block">Kasir Basecamp</span>
                <span class="font-heading font-extrabold text-2xl text-sky-600 mt-1 block">{{ $stats['kasir'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-500 uppercase tracking-wider block">Petugas Pos</span>
                <span class="font-heading font-extrabold text-2xl text-emerald-600 mt-1 block">{{ $stats['pos'] }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">User Pendaki</span>
                <span class="font-heading font-extrabold text-2xl text-slate-600 mt-1 block">{{ $stats['pendaki'] }}</span>
            </div>
        </div>

        <!-- FILTER & SEARCH -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama staf, email, nomor HP, atau lokasi pos..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841] focus:border-[#2D5841]">
                </div>

                <div class="w-full sm:w-56">
                    <select name="role" onchange="this.form.submit()"
                            class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-xs sm:text-sm font-medium focus:ring-2 focus:ring-[#2D5841]">
                        <option value="">Semua Peran (All Roles)</option>
                        <option value="SUPERADMIN" {{ $roleFilter === 'SUPERADMIN' ? 'selected' : '' }}>Superadmin</option>
                        <option value="ADMIN_SAR" {{ $roleFilter === 'ADMIN_SAR' ? 'selected' : '' }}>Koordinator SAR</option>
                        <option value="KASIR" {{ $roleFilter === 'KASIR' ? 'selected' : '' }}>Petugas Kasir</option>
                        <option value="PETUGAS_POS" {{ $roleFilter === 'PETUGAS_POS' ? 'selected' : '' }}>Petugas Pos Jalur</option>
                        <option value="PENDAKI" {{ $roleFilter === 'PENDAKI' ? 'selected' : '' }}>Pendaki / Pengunjung</option>
                    </select>
                </div>

                @if(!empty($search) || !empty($roleFilter))
                    <a href="{{ route('admin.users.index') }}"
                       class="px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- USERS TABLE -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-base text-slate-900">Daftar Akun Pengguna</h3>
                    <p class="text-xs text-slate-500">Menampilkan {{ $users->total() }} akun terdaftar</p>
                </div>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <i class="fas fa-shield-halved mr-1"></i> Multi-Role RBAC Active
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Nama Staf & Email</th>
                            <th class="py-3.5 px-4">Peran (Role)</th>
                            <th class="py-3.5 px-4">Penugasan Pos</th>
                            <th class="py-3.5 px-4">No. HP / WA</th>
                            <th class="py-3.5 px-4">Terdaftar</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $u)
                            @php
                                $roleVal = $u->role instanceof \App\Shared\Enums\UserRole ? $u->role->value : (string)$u->role;
                                $roleLabel = $u->role instanceof \App\Shared\Enums\UserRole ? $u->role->label() : $roleVal;
                                $isSelf = auth()->id() === $u->id;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#1E3A2B] to-[#2D5841] text-amber-300 font-bold text-xs flex items-center justify-center shadow-xs shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                                <span>{{ $u->name }}</span>
                                                @if($isSelf)
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500 font-mono">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @switch($roleVal)
                                        @case('SUPERADMIN')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fas fa-crown text-[10px]"></i> Superadmin
                                            </span>
                                            @break
                                        @case('ADMIN_SAR')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="fas fa-person-hiking text-[10px]"></i> Koordinator SAR
                                            </span>
                                            @break
                                        @case('KASIR')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                <i class="fas fa-cash-register text-[10px]"></i> Kasir Basecamp
                                            </span>
                                            @break
                                        @case('PETUGAS_POS')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fas fa-mountain text-[10px]"></i> Petugas Pos
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                                <i class="fas fa-user text-[10px]"></i> Pendaki
                                            </span>
                                    @endswitch
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($u->assigned_pos)
                                        <span class="inline-flex items-center gap-1 text-slate-700 font-medium">
                                            <i class="fas fa-location-dot text-[#D97736] text-[10px]"></i>
                                            {{ $u->assigned_pos }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ $u->phone ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-xs">
                                    {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                @click="openEdit({
                                                    id: {{ $u->id }},
                                                    name: '{{ addslashes($u->name) }}',
                                                    email: '{{ addslashes($u->email) }}',
                                                    role: '{{ $roleVal }}',
                                                    phone: '{{ addslashes($u->phone ?? '') }}',
                                                    assigned_pos: '{{ addslashes($u->assigned_pos ?? '') }}'
                                                })"
                                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition"
                                                title="Edit Akun">
                                            <i class="fas fa-edit text-slate-600"></i> Edit
                                        </button>

                                        @if(!$isSelf)
                                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg border border-rose-200 hover:border-rose-300 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition"
                                                        title="Hapus Akun">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400 text-xs">
                                    <i class="fas fa-user-slash text-2xl text-slate-300 block mb-2"></i>
                                    Tidak ada data pengguna yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if ($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: TAMBAH AKUN BARU -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showAddModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200" @click.stop>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                                <i class="fas fa-user-plus text-[#D97736]"></i> + Tambah Akun Staf / Pengguna
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan petugas pos, kasir, SAR, atau superadmin baru.</p>
                        </div>
                        <button type="button" @click="showAddModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Petugas:</label>
                                <input type="text" name="name" required placeholder="Misal: Dadan Komandan SAR"
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Alamat Email (Login):</label>
                                    <input type="email" name="email" required placeholder="petugas@samsara.id"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP:</label>
                                    <input type="text" name="phone" placeholder="081234567890"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Kata Sandi (Password):</label>
                                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Peran / Hak Akses (Role):</label>
                                    <select name="role" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-[#2D5841]">
                                        <option value="KASIR">KASIR - Petugas Kasir Basecamp</option>
                                        <option value="PETUGAS_POS">PETUGAS_POS - Scanner PWA Pos 1-4</option>
                                        <option value="ADMIN_SAR">ADMIN_SAR - Koordinator Lapangan & SAR</option>
                                        <option value="SUPERADMIN">SUPERADMIN - Pengelola Penuh</option>
                                        <option value="PENDAKI">PENDAKI - Pengunjung / Pengguna</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Penugasan Pos / Lokasi:</label>
                                <select name="assigned_pos" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                    <option value="">-- Tanpa Pos Khusus --</option>
                                    @foreach($posList as $pos)
                                        <option value="{{ $pos }}">{{ $pos }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-400 mt-1">Khusus petugas pos jalur, tentukan pos jaga default.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" @click="showAddModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-[#2D5841] hover:bg-[#1E3A2B] text-white text-xs font-bold shadow-md">
                                Simpan Akun Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDIT AKUN -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showEditModal = false"></div>
            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200" @click.stop>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                                <i class="fas fa-user-pen text-amber-500"></i> Edit Data Akun
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi, peran, atau ubah kata sandi.</p>
                        </div>
                        <button type="button" @click="showEditModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/users') }}/' + editData.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap:</label>
                                <input type="text" name="name" x-model="editData.name" required
                                       class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Alamat Email:</label>
                                    <input type="email" name="email" x-model="editData.email" required
                                           class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP:</label>
                                    <input type="text" name="phone" x-model="editData.phone"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Ubah Kata Sandi (Opsional):</label>
                                    <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tidak diubah"
                                           class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Peran / Hak Akses (Role):</label>
                                    <select name="role" x-model="editData.role" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-[#2D5841]">
                                        <option value="SUPERADMIN">SUPERADMIN - Pengelola Penuh</option>
                                        <option value="ADMIN_SAR">ADMIN_SAR - Koordinator SAR</option>
                                        <option value="KASIR">KASIR - Petugas Kasir Basecamp</option>
                                        <option value="PETUGAS_POS">PETUGAS_POS - Scanner PWA Pos</option>
                                        <option value="PENDAKI">PENDAKI - Pengunjung / Pengguna</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Penugasan Pos / Lokasi:</label>
                                <select name="assigned_pos" x-model="editData.assigned_pos" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#2D5841]">
                                    <option value="">-- Tanpa Pos Khusus --</option>
                                    @foreach($posList as $pos)
                                        <option value="{{ $pos }}">{{ $pos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" @click="showEditModal = false"
                                    class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md">
                                Perbarui Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.admin>
