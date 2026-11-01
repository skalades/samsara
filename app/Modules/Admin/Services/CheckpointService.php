<?php

namespace App\Modules\Admin\Services;

use App\Modules\Simaksi\Models\Checkpoint;
use App\Modules\Simaksi\Models\CheckpointPhoto;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckpointService
{
    /**
     * Dapatkan semua checkpoint beserta foto-fotonya
     */
    public function getAllCheckpoints(): Collection
    {
        return Checkpoint::with(['photos.uploader'])
            ->orderBy('order')
            ->get();
    }

    /**
     * Dapatkan checkpoint spesifik berdasarkan ID atau Code
     */
    public function getCheckpoint(int|string $identifier): Checkpoint
    {
        if (is_numeric($identifier)) {
            return Checkpoint::with('photos.uploader')->findOrFail($identifier);
        }

        return Checkpoint::with('photos.uploader')->where('code', $identifier)->firstOrFail();
    }

    /**
     * Upload foto baru untuk checkpoint
     */
    public function addPhoto(Checkpoint $checkpoint, UploadedFile $file, array $data, ?int $userId = null): CheckpointPhoto
    {
        // Pastikan direktori storage public tersedia
        $directory = 'checkpoints/' . $checkpoint->code;
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug($data['title'] ?? 'foto') . '-' . time() . '-' . Str::random(6) . '.' . $extension;

        $path = $file->storeAs($directory, $filename, 'public');
        $photoUrl = '/storage/' . $path;

        $isPrimary = ! empty($data['is_primary']);

        // Jika foto ini dijadikan foto utama, reset is_primary foto lain di pos ini
        if ($isPrimary) {
            $checkpoint->photos()->update(['is_primary' => false]);
        } elseif ($checkpoint->photos()->count() === 0) {
            // Foto pertama otomatis menjadi primary jika belum ada
            $isPrimary = true;
        }

        $highestOrder = $checkpoint->photos()->max('order') ?? 0;

        return CheckpointPhoto::create([
            'checkpoint_id' => $checkpoint->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'photo_url' => $photoUrl,
            'is_primary' => $isPrimary,
            'order' => $highestOrder + 1,
            'uploaded_by' => $userId,
        ]);
    }

    /**
     * Update detail foto (judul, deskripsi, is_primary)
     */
    public function updatePhoto(int $photoId, array $data): CheckpointPhoto
    {
        $photo = CheckpointPhoto::findOrFail($photoId);

        if (! empty($data['is_primary']) && ! $photo->is_primary) {
            // Reset foto lain di pos ini
            CheckpointPhoto::where('checkpoint_id', $photo->checkpoint_id)
                ->update(['is_primary' => false]);
            $photo->is_primary = true;
        }

        $photo->title = $data['title'] ?? $photo->title;
        $photo->description = $data['description'] ?? $photo->description;

        if (isset($data['order'])) {
            $photo->order = (int) $data['order'];
        }

        $photo->save();

        return $photo;
    }

    /**
     * Set foto sebagai foto utama pos
     */
    public function setAsPrimary(int $photoId): CheckpointPhoto
    {
        $photo = CheckpointPhoto::findOrFail($photoId);

        CheckpointPhoto::where('checkpoint_id', $photo->checkpoint_id)
            ->update(['is_primary' => false]);

        $photo->update(['is_primary' => true]);

        return $photo;
    }

    /**
     * Hapus foto dari database dan storage
     */
    public function deletePhoto(int $photoId): bool
    {
        $photo = CheckpointPhoto::findOrFail($photoId);
        $wasPrimary = $photo->is_primary;
        $checkpointId = $photo->checkpoint_id;

        // Hapus file fisik dari storage jika berada di direktori /storage/checkpoints/...
        if (str_starts_with($photo->photo_url, '/storage/')) {
            $relativePath = str_replace('/storage/', '', $photo->photo_url);
            Storage::disk('public')->delete($relativePath);
        }

        $deleted = $photo->delete();

        // Jika foto yang dihapus adalah primary, jadikan foto lain (jika ada) sebagai primary
        if ($wasPrimary) {
            $nextPhoto = CheckpointPhoto::where('checkpoint_id', $checkpointId)->first();
            if ($nextPhoto) {
                $nextPhoto->update(['is_primary' => true]);
            }
        }

        return (bool) $deleted;
    }
}
