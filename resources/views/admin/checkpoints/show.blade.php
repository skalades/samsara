<x-layouts.admin title="Galeri Foto {{ $checkpoint->title }} | SAMSARA Admin">
    <div class="space-y-6" x-data="{
        uploadModal: false,
        editModal: false,
        activeEdit: { id: null, title: '', description: '', is_primary: false },
        previewUrl: null,
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
            }
        }
    }">
        <!-- TOP ADAPTIVE HEADER BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('admin.checkpoints.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                        &larr; Seluruh Checkpoint
                    </a>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-xs font-mono font-bold text-[#D97736] uppercase tracking-wider">{{ $checkpoint->number }} ({{ $checkpoint->elevation }})</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                    {{ $checkpoint->title }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    {{ $checkpoint->tagline ?? $checkpoint->description }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="uploadModal = true" class="touch-target px-4 py-2.5 rounded-2xl bg-[#D97736] hover:bg-[#B25A20] text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-cloud-arrow-up"></i>
                    <span>Upload Foto Baru</span>
                </button>

                <a href="{{ route('home') }}#pos-explorer" target="_blank" class="touch-target px-3.5 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-xs border border-slate-200 shadow-xs transition flex items-center gap-1.5">
                    <i class="fas fa-eye text-emerald-600"></i>
                    <span class="hidden sm:inline">Preview Portal</span>
                </a>
            </div>
        </div>

        <!-- FLASH NOTIFICATION -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if (isset($errors) && $errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1">
                    <i class="fas fa-triangle-exclamation text-rose-600"></i>
                    <span>Terdapat kesalahan input:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- CHECKPOINT INFO SUMMARY PILL -->
        <div class="p-4 rounded-2xl bg-slate-900 text-white flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-[#D97736] text-white flex items-center justify-center font-black text-sm">
                    {{ $checkpoint->number }}
                </span>
                <div>
                    <div class="font-heading font-extrabold text-sm">{{ $checkpoint->title }}</div>
                    <div class="text-[11px] text-slate-400">
                        {{ $checkpoint->distance }} &bull; Estimasi: {{ $checkpoint->time_estimate }} &bull; Medan: {{ $checkpoint->terrain }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 font-mono text-emerald-300">
                <i class="fas fa-images"></i>
                <span>{{ $checkpoint->photos->count() }} Foto Terupload</span>
            </div>
        </div>

        <!-- PHOTOS GRID -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-extrabold text-lg text-slate-900">
                    Galeri Dokumentasi Lapangan ({{ $checkpoint->photos->count() }})
                </h3>
                <span class="text-xs text-slate-500">
                    Foto bertanda <span class="text-[#D97736] font-bold">UTAMA</span> akan tampil sebagai sampul besar di public portal.
                </span>
            </div>

            @if ($checkpoint->photos->isEmpty())
                <div class="p-12 text-center bg-white rounded-3xl border border-slate-200/90 shadow-sm space-y-3">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="font-heading font-extrabold text-base text-slate-800">Belum Ada Foto Terupload</div>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Pos ini saat ini menggunakan gambar default jalur. Upload foto resmi kondisi lapangan agar pendaki memiliki referensi visual akurat.
                    </p>
                    <button type="button" @click="uploadModal = true"
                            class="touch-target px-5 py-2.5 rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white text-xs font-bold transition inline-flex items-center gap-2 cursor-pointer shadow-sm">
                        <i class="fas fa-cloud-arrow-up text-[#D97736]"></i> Upload Foto Sekarang
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($checkpoint->photos as $photo)
                        <div class="bg-white rounded-3xl border {{ $photo->is_primary ? 'border-[#D97736] ring-2 ring-[#D97736]/20' : 'border-slate-200/90' }} shadow-sm overflow-hidden flex flex-col justify-between group">
                            <!-- Image Container -->
                            <div>
                                <div class="relative h-56 bg-slate-900 overflow-hidden">
                                    <img src="{{ $photo->resolved_url }}" alt="{{ $photo->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20"></div>

                                    <!-- Primary Badge -->
                                    <div class="absolute top-3 left-3">
                                        @if ($photo->is_primary)
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-[#D97736] text-white shadow-md flex items-center gap-1.5">
                                                <i class="fas fa-star text-amber-200"></i> FOTO UTAMA POS
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-900/70 text-slate-300 backdrop-blur-xs border border-white/10">
                                                Galeri Pendukung
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Lightbox Trigger Button -->
                                    <button type="button"
                                            @click="$dispatch('open-lightbox', { src: '{{ $photo->resolved_url }}', title: '{{ addslashes($photo->title) }}', desc: '{{ addslashes($photo->description ?? '') }}' })"
                                            class="absolute top-3 right-3 w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white text-xs flex items-center justify-center backdrop-blur-xs border border-white/20 transition shadow-md cursor-pointer"
                                            title="Perbesar di Lightbox">
                                        <i class="fas fa-expand text-[#D97736]"></i>
                                    </button>

                                    <!-- Caption Bar on Bottom of Image -->
                                    <div class="absolute bottom-3 left-3 right-3 text-white">
                                        <h4 class="font-heading font-extrabold text-base text-white leading-snug drop-shadow-sm">
                                            {{ $photo->title }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Photo Description -->
                                <div class="p-5">
                                    <p class="text-xs text-slate-600 leading-relaxed min-h-[3rem]">
                                        {{ $photo->description ?: 'Tidak ada catatan kondisi visual tambahan.' }}
                                    </p>

                                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                                        <span>Diupload: {{ $photo->created_at->format('d M Y') }}</span>
                                        @if ($photo->uploader)
                                            <span class="truncate max-w-[120px]">Oleh: {{ $photo->uploader->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Card Action Toolbar -->
                            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                                <!-- Set as Primary Button (if not yet primary) -->
                                @if (! $photo->is_primary)
                                    <form action="{{ route('admin.checkpoints.photos.primary', $photo->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="touch-target px-3 py-1.5 rounded-xl bg-white hover:bg-emerald-50 text-emerald-700 font-bold border border-emerald-300 transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                            <i class="fas fa-check"></i> Set Utama
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-bold text-[#D97736] flex items-center gap-1">
                                        <i class="fas fa-circle-check"></i> Sampul Pos
                                    </span>
                                @endif

                                <div class="flex items-center gap-1.5">
                                    <!-- Edit Trigger Button -->
                                    <button type="button"
                                            @click="activeEdit = { id: {{ $photo->id }}, title: '{{ addslashes($photo->title) }}', description: '{{ addslashes($photo->description ?? '') }}', is_primary: {{ $photo->is_primary ? 'true' : 'false' }} }; editModal = true;"
                                            class="touch-target px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold border border-slate-300 transition flex items-center gap-1 cursor-pointer shadow-xs">
                                        <i class="fas fa-pen text-slate-500"></i> Edit
                                    </button>

                                    <!-- Delete Button Form -->
                                    <form action="{{ route('admin.checkpoints.photos.destroy', $photo->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto \'{{ addslashes($photo->title) }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="touch-target w-8 h-8 rounded-xl bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 transition flex items-center justify-center cursor-pointer shadow-xs" title="Hapus Foto">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 1. MODAL UPLOAD FOTO BARU -->
        <div x-show="uploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="uploadModal" @click="uploadModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="uploadModal" class="inline-block bg-white rounded-3xl p-6 text-left shadow-2xl max-w-lg w-full border border-slate-200 relative z-10 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-[#D97736] text-white flex items-center justify-center text-sm font-black">
                                <i class="fas fa-camera"></i>
                            </span>
                            <div>
                                <h3 class="font-heading font-black text-base text-slate-900">Upload Foto Dokumentasi</h3>
                                <p class="text-[11px] text-slate-500">{{ $checkpoint->number }} - {{ $checkpoint->title }}</p>
                            </div>
                        </div>
                        <button type="button" @click="uploadModal = false; previewUrl = null;" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.checkpoints.photos.store', $checkpoint->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                        @csrf
                        
                        <!-- File Upload Input with Live Preview -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">File Foto Lapangan (JPG, PNG, WebP &bull; Maks 5 MB)</label>
                            <input type="file" name="image" required accept="image/*" @change="handleFileSelect"
                                   class="touch-target w-full rounded-2xl border border-slate-300 p-2.5 text-xs text-slate-700 bg-slate-50 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1E3A2B] file:text-white hover:file:bg-[#12241B] cursor-pointer">
                            
                            <template x-if="previewUrl">
                                <div class="mt-3 relative rounded-2xl overflow-hidden h-40 bg-slate-900 border border-slate-200">
                                    <img :src="previewUrl" alt="Preview Foto" class="w-full h-full object-cover">
                                    <div class="absolute bottom-2 left-2 px-2 py-0.5 rounded-lg bg-black/70 text-white text-[10px] font-bold">
                                        Preview File
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Judul / Label Foto *</label>
                            <input type="text" name="title" required placeholder="Contoh: Titik Shelter Istirahat & Mata Air"
                                   class="touch-target w-full rounded-xl border border-slate-300 p-3 font-semibold text-slate-900 focus:ring-2 focus:ring-[#D97736] focus:border-[#D97736]">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Deskripsi & Catatan Kondisi Jalur (Opsional)</label>
                            <textarea name="description" rows="3" placeholder="Informasikan kondisi kontur tanah, vegetasi, atau petunjuk keselamatan bagi pendaki..."
                                      class="w-full rounded-xl border border-slate-300 p-3 text-slate-900 focus:ring-2 focus:ring-[#D97736] focus:border-[#D97736]"></textarea>
                        </div>

                        <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <input type="checkbox" name="is_primary" id="is_primary" value="1" class="rounded border-slate-300 text-[#D97736] focus:ring-[#D97736] w-4 h-4 cursor-pointer">
                            <label for="is_primary" class="font-bold text-slate-800 cursor-pointer">
                                Jadikan sebagai Foto Utama (Sampul Besar Checkpoint)
                            </label>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-2">
                            <button type="button" @click="uploadModal = false; previewUrl = null;"
                                    class="touch-target px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-2xl bg-[#D97736] hover:bg-[#B25A20] text-white font-extrabold shadow-md transition flex items-center gap-2">
                                <i class="fas fa-upload"></i>
                                <span>Simpan & Upload Foto</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2. MODAL EDIT FOTO DETAIL -->
        <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="editModal" @click="editModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div x-show="editModal" class="inline-block bg-white rounded-3xl p-6 text-left shadow-2xl max-w-lg w-full border border-slate-200 relative z-10 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-slate-800 text-white flex items-center justify-center text-sm font-black">
                                <i class="fas fa-pen"></i>
                            </span>
                            <h3 class="font-heading font-black text-base text-slate-900">Edit Keterangan Foto</h3>
                        </div>
                        <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form :action="'/admin/checkpoints/photos/' + activeEdit.id" method="POST" class="space-y-4 text-xs">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Judul / Label Foto *</label>
                            <input type="text" name="title" x-model="activeEdit.title" required
                                   class="touch-target w-full rounded-xl border border-slate-300 p-3 font-semibold text-slate-900">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Deskripsi Kondisi</label>
                            <textarea name="description" x-model="activeEdit.description" rows="3"
                                      class="w-full rounded-xl border border-slate-300 p-3 text-slate-900"></textarea>
                        </div>

                        <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <input type="checkbox" name="is_primary" id="edit_is_primary" value="1" x-model="activeEdit.is_primary" class="rounded border-slate-300 text-[#D97736] focus:ring-[#D97736] w-4 h-4 cursor-pointer">
                            <label for="edit_is_primary" class="font-bold text-slate-800 cursor-pointer">
                                Jadikan sebagai Foto Utama Checkpoint
                            </label>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-2">
                            <button type="button" @click="editModal = false"
                                    class="touch-target px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="touch-target px-5 py-2.5 rounded-2xl bg-[#1E3A2B] hover:bg-[#12241B] text-white font-extrabold shadow-md transition flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- GLOBAL LIGHTBOX COMPONENT FOR PREVIEW -->
        <x-public.photo-lightbox />
    </div>
</x-layouts.admin>
