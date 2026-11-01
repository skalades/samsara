<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Data Pos / Checkpoint Jalur
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // basecamp, pos1, pos2, pos3, pos4, puncak
            $table->string('number'); // BC, POS 1, POS 2, POS 3, POS 4, PUNCAK
            $table->string('title'); // Basecamp SAMSARA Cigedug, Tanjakan EOD, etc.
            $table->string('elevation'); // 1.245 MDPL
            $table->string('distance'); // 0 KM (Titik Mulai)
            $table->string('time_estimate'); // 60 - 90 Menit
            $table->string('water_info'); // Melimpah (Kran & Toilet)
            $table->boolean('water_available')->default(false);
            $table->string('signal_info'); // 4G Semua Operator (Kuat)
            $table->string('tent_capacity'); // 80+ Tenda (Camp Utama)
            $table->string('terrain'); // Jalur Berakar & Bebatuan
            $table->string('default_image')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Dokumentasi Foto Lapangan per Pos (Admin CRUD)
        Schema::create('checkpoint_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkpoint_id')->constrained('checkpoints')->cascadeOnDelete();
            $table->string('title'); // Label / Caption foto
            $table->text('description')->nullable(); // Penjelasan kondisi visual bagi pendaki
            $table->string('photo_url'); // Path foto di storage/public
            $table->boolean('is_primary')->default(false); // Foto utama pos
            $table->unsignedInteger('order')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoint_photos');
        Schema::dropIfExists('checkpoints');
    }
};
