<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Data Driver Paguyuban Ojek Warga Cigedug
        Schema::create('ojek_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('driver_number')->unique(); // e.g. DRV-001
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('license_plate'); // e.g. Z 4821 SG
            $table->string('motorcycle_model')->nullable(); // e.g. Honda CRF / Beat / Megapro
            $table->string('community_name')->default('Paguyuban Ojek Cigedug Mandiri');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('total_trips')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Rute dan Tarif Resmi Ojek (Transparansi Bagi Hasil Driver vs Kas Paguyuban)
        Schema::create('ojek_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name'); // e.g. Pasar Cigedug - Basecamp SAMSARA
            $table->string('origin');
            $table->string('destination');
            $table->decimal('fare_amount', 12, 2);
            $table->decimal('driver_share', 12, 2);
            $table->decimal('kas_share', 12, 2)->default(0);
            $table->unsignedSmallInteger('estimated_duration_minutes')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Log Kehadiran & Antrean Driver Harian (Ring Antrean FIFO Fair Play)
        Schema::create('ojek_driver_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ojek_driver_id')->constrained('ojek_drivers')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('queue_order')->default(1);
            $table->string('status')->default('STANDBY'); // STANDBY, CALLED, ON_TRIP, BREAK, OFFLINE
            $table->timestamp('checked_in_at');
            $table->timestamp('last_called_at')->nullable();
            $table->timestamp('last_trip_completed_at')->nullable();
            $table->unsignedInteger('daily_trips_count')->default(0);
            $table->timestamps();

            $table->index(['date', 'status', 'queue_order']);
        });

        // 4. Tiket Antrean Penumpang (Omnichannel: Dari Kasir POS, Web, atau Walk-in Pangkalan)
        Schema::create('ojek_queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g. OJ-261101-001
            $table->foreignId('ojek_route_id')->constrained('ojek_routes')->cascadeOnDelete();
            $table->string('passenger_name');
            $table->string('passenger_phone')->nullable();
            $table->foreignId('simaksi_booking_id')->nullable()->constrained('simaksi_bookings')->nullOnDelete();
            $table->foreignId('pos_order_id')->nullable()->constrained('pos_orders')->nullOnDelete();
            $table->string('source')->default('LOKET_POS'); // LOKET_POS, PANGKALAN_WALKIN, ONLINE_WEB
            $table->foreignId('assigned_driver_id')->nullable()->constrained('ojek_drivers')->nullOnDelete();
            $table->foreignId('assigned_attendance_id')->nullable()->constrained('ojek_driver_attendances')->nullOnDelete();
            $table->decimal('fare_amount', 12, 2);
            $table->string('payment_status')->default('PAID'); // PAID, UNPAID
            $table->string('payment_method')->default('CASH'); // CASH, QRIS, TRANSFER
            $table->string('status')->default('WAITING'); // WAITING, CALLED, ON_TRIP, COMPLETED, CANCELLED
            $table->timestamp('called_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ojek_queue_tickets');
        Schema::dropIfExists('ojek_driver_attendances');
        Schema::dropIfExists('ojek_routes');
        Schema::dropIfExists('ojek_drivers');
    }
};
