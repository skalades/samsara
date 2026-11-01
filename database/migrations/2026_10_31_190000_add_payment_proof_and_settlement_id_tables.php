<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add payment proof to simaksi_bookings
        Schema::table('simaksi_bookings', function (Blueprint $table) {
            $table->string('payment_proof_path')->nullable()->after('qr_token');
            $table->index(['status', 'hiking_date'], 'simaksi_status_date_idx');
        });

        // 2. Add consignment settlement link to pos_order_items to prevent double settlement
        Schema::table('pos_order_items', function (Blueprint $table) {
            $table->foreignId('consignment_settlement_id')
                ->nullable()
                ->after('mitra_id')
                ->constrained('consignment_settlements')
                ->nullOnDelete();

            $table->index(['mitra_id', 'consignment_settlement_id'], 'pos_items_mitra_settle_idx');
        });
    }

    public function down(): void
    {
        Schema::table('pos_order_items', function (Blueprint $table) {
            $table->dropIndex('pos_items_mitra_settle_idx');
            $table->dropConstrainedForeignId('consignment_settlement_id');
        });

        Schema::table('simaksi_bookings', function (Blueprint $table) {
            $table->dropIndex('simaksi_status_date_idx');
            $table->dropColumn('payment_proof_path');
        });
    }
};
