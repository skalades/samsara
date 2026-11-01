<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\CheckpointService;
use App\Modules\Simaksi\Models\Checkpoint;
use App\Modules\Simaksi\Models\CheckpointPhoto;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckpointPhotoController extends Controller
{
    public function __construct(
        protected CheckpointService $checkpointService,
    ) {}

    /**
     * Tampilan daftar seluruh pos & galeri dokumentasi
     */
    public function index(): View
    {
        $checkpoints = $this->checkpointService->getAllCheckpoints();

        return view('admin.checkpoints.index', compact('checkpoints'));
    }

    /**
     * Tampilan detail manajemen foto untuk satu pos spesifik
     */
    public function show(int $id): View
    {
        $checkpoint = $this->checkpointService->getCheckpoint($id);

        return view('admin.checkpoints.show', compact('checkpoint'));
    }

    /**
     * Upload foto baru untuk pos
     */
    public function store(Request $request, int $id): RedirectResponse
    {
        $checkpoint = Checkpoint::findOrFail($id);

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'is_primary' => 'nullable|boolean',
        ], [
            'image.required' => 'Wajib memilih file foto untuk diupload.',
            'image.image' => 'File harus berupa gambar valid.',
            'image.max' => 'Ukuran foto maksimal 5 MB.',
            'title.required' => 'Judul / caption foto wajib diisi.',
        ]);

        try {
            $this->checkpointService->addPhoto(
                $checkpoint,
                $request->file('image'),
                $validated,
                auth()->id()
            );

            return back()->with('success', "Foto referensi '{$validated['title']}' berhasil diupload untuk {$checkpoint->number}!");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengupload foto: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Perbarui judul & deskripsi foto
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'is_primary' => 'nullable|boolean',
        ]);

        try {
            $this->checkpointService->updatePhoto($id, $validated);

            return back()->with('success', 'Detail foto berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memperbarui foto: ' . $e->getMessage());
        }
    }

    /**
     * Setel foto sebagai foto utama pos
     */
    public function setPrimary(int $id): RedirectResponse
    {
        try {
            $photo = $this->checkpointService->setAsPrimary($id);

            return back()->with('success', "Foto '{$photo->title}' telah disetel sebagai foto utama {$photo->checkpoint->number}!");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menyetel foto utama: ' . $e->getMessage());
        }
    }

    /**
     * Hapus foto pos
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $photo = CheckpointPhoto::findOrFail($id);
            $title = $photo->title;
            $posName = $photo->checkpoint->number;

            $this->checkpointService->deletePhoto($id);

            return back()->with('success', "Foto '{$title}' dari {$posName} berhasil dihapus.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus foto: ' . $e->getMessage());
        }
    }
}
