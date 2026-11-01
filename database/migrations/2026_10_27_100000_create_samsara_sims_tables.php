<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update users table with role and mobile field attributes
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('PENDAKI')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('assigned_pos')->nullable()->after('phone');
        });

        // 2. Trail Operational Settings & Weather Status
        Schema::create('trail_settings', function (Blueprint $table) {
            $table->id();
            $table->string('trail_status')->default('OPEN');
            $table->string('weather_status')->default('CERAH');
            $table->text('announcement')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Dynamic Daily Quota Management & Blackout Dates
        Schema::create('daily_quotas', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedInteger('max_quota')->default(300);
            $table->unsignedInteger('booked_count')->default(0);
            $table->boolean('is_blackout')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Mitra Konsinyasi UMKM Warga Cigedug
        Schema::create('consignment_mitras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nik')->nullable();
            $table->string('village_address');
            $table->string('phone_wa');
            $table->string('bank_info')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 5. Products (Dual Inventory: Beli Putus & Titip Jual Konsinyasi)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('inventory_type'); // BELI_PUTUS or KONSINYASI
            $table->foreignId('consignment_mitra_id')->nullable()->constrained('consignment_mitras')->nullOnDelete();
            $table->decimal('cost_price', 12, 2)->default(0); // HPP atau Nilai Setoran Mitra
            $table->decimal('selling_price', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock_alert')->default(5);
            $table->boolean('is_preorder_available')->default(true);
            $table->string('image_url')->nullable();
            $table->boolean('active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 6. Product Stock Logs (Audit Trail Stok)
        Schema::create('product_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('type'); // IN_RESTOCK, OUT_SALE, ADJUSTMENT, RETURN
            $table->integer('qty');
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Consignment Settlements (Bagi Hasil Otomatis Mitra UMKM)
        Schema::create('consignment_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_code')->unique();
            $table->foreignId('consignment_mitra_id')->constrained('consignment_mitras')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('total_items_sold')->default(0);
            $table->decimal('total_sales_amount', 12, 2)->default(0);
            $table->decimal('mitra_earnings_amount', 12, 2)->default(0);
            $table->decimal('samsara_margin_amount', 12, 2)->default(0);
            $table->string('status')->default('DRAFT'); // DRAFT, PAID
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Rental Items (Aset Sewa Outdoor & Siklus Perawatan)
        Schema::create('rental_items', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category');
            $table->decimal('rental_rate_per_day', 12, 2);
            $table->decimal('deposit_rate', 12, 2)->default(0);
            $table->string('status')->default('TERSEDIA'); // TERSEDIA, DISEWA, PERLU_QC_CUCI, DALAM_PERBAIKAN
            $table->unsignedInteger('times_rented_count')->default(0);
            $table->timestamp('last_cleaned_at')->nullable();
            $table->text('condition_notes')->nullable();
            $table->timestamps();
        });

        // 9. SIMAKSI Bookings (Reservasi Pendakian Utama)
        Schema::create('simaksi_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('hiking_date');
            $table->unsignedSmallInteger('duration_days')->default(2);
            $table->string('leader_name');
            $table->string('leader_nik');
            $table->string('leader_phone');
            $table->string('leader_email');
            $table->text('leader_address');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->unsignedInteger('total_hikers')->default(1);
            $table->decimal('ticket_fee_per_person', 12, 2)->default(25000);
            $table->decimal('total_ticket_amount', 12, 2);
            $table->decimal('eco_deposit_amount', 12, 2)->default(20000);
            $table->string('status')->default('PENDING_PAYMENT');
            $table->string('payment_method')->default('QRIS');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('checkout_at')->nullable();
            $table->string('current_pos')->default('Basecamp');
            $table->string('qr_token')->unique();
            $table->string('tent_color_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 10. SIMAKSI Manifest Members
        Schema::create('simaksi_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('simaksi_bookings')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('nik');
            $table->string('phone')->nullable();
            $table->string('gender', 1)->default('L');
            $table->unsignedSmallInteger('age');
            $table->text('medical_history')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamps();
        });

        // 11. Trail Checkpoints (Log Pos 1-4 Scanner & SAR Live Tracker)
        Schema::create('trail_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('simaksi_bookings')->cascadeOnDelete();
            $table->string('pos_name'); // 'Pos 1 (Tanjakan EOD)', 'Pos 2 (Batu Gendrung)', etc.
            $table->foreignId('scanned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('direction')->default('ASCENDING'); // ASCENDING, DESCENDING
            $table->unsignedInteger('hikers_passed_count')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 12. SOS Alerts & Evacuation Console
        Schema::create('sos_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('simaksi_bookings')->nullOnDelete();
            $table->string('reporter_name');
            $table->string('reporter_phone');
            $table->string('pos_location');
            $table->string('alert_type'); // MEDICAL, LOST, HYPOTHERMIA, WEATHER_TRAPPED, ACCIDENT
            $table->text('description');
            $table->string('status')->default('ACTIVE'); // ACTIVE, IN_RESCUE, RESOLVED
            $table->text('evacuation_team_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // 13. Rental Orders (Sewa Perlengkapan Lapangan)
        Schema::create('rental_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('booking_id')->nullable()->constrained('simaksi_bookings')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('id_card_deposit_type')->default('KTP');
            $table->decimal('total_rental_fee', 12, 2)->default(0);
            $table->decimal('total_deposit_amount', 12, 2)->default(0);
            $table->decimal('damage_fee', 12, 2)->default(0);
            $table->string('status')->default('ACTIVE'); // ACTIVE, RETURNED_GOOD, RETURNED_WITH_DAMAGE, FORFEITED
            $table->timestamp('rented_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 14. Rental Order Items & Condition Inspection
        Schema::create('rental_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_order_id')->constrained('rental_orders')->cascadeOnDelete();
            $table->foreignId('rental_item_id')->constrained('rental_items')->cascadeOnDelete();
            $table->unsignedSmallInteger('rental_days')->default(1);
            $table->decimal('rate_per_day', 12, 2);
            $table->decimal('item_deposit', 12, 2)->default(0);
            $table->string('return_condition')->default('BAIK');
            $table->decimal('damage_penalty', 12, 2)->default(0);
            $table->timestamps();
        });

        // 15. Unified POS Invoices (Multi-Item Single Invoice)
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('booking_id')->nullable()->constrained('simaksi_bookings')->nullOnDelete();
            $table->foreignId('cashier_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal_simaksi', 12, 2)->default(0);
            $table->decimal('subtotal_store', 12, 2)->default(0);
            $table->decimal('subtotal_rental', 12, 2)->default(0);
            $table->decimal('eco_deposit', 12, 2)->default(0);
            $table->decimal('rental_deposit', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->string('payment_method')->default('CASH');
            $table->string('payment_status')->default('PAID');
            $table->boolean('is_preorder_pickup')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 16. POS Order Items (Detail Belanja Gabungan)
        Schema::create('pos_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_order_id')->constrained('pos_orders')->cascadeOnDelete();
            $table->string('item_type'); // SIMAKSI, STORE_BELI_PUTUS, STORE_KONSINYASI, RENTAL, ECO_DEPOSIT
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('item_name');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->foreignId('mitra_id')->nullable()->constrained('consignment_mitras')->nullOnDelete();
            $table->timestamps();
        });

        // 17. Cashier Shift Reconciliation (Tutup Shift Kasir)
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('shift_number')->default(1);
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('closing_cash_physical', 12, 2)->default(0);
            $table->decimal('closing_cash_system', 12, 2)->default(0);
            $table->decimal('closing_qris_system', 12, 2)->default(0);
            $table->decimal('cash_difference', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('OPEN'); // OPEN, CLOSED
            $table->timestamps();
        });

        // 18. Eco-Deposit & Waste Management Checklist
        Schema::create('eco_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('simaksi_bookings')->cascadeOnDelete();
            $table->decimal('deposit_amount', 12, 2)->default(20000);
            $table->unsignedSmallInteger('brought_plastics_count')->default(0);
            $table->unsignedSmallInteger('brought_cans_count')->default(0);
            $table->unsignedSmallInteger('brought_bottles_count')->default(0);
            $table->unsignedSmallInteger('returned_plastics_count')->default(0);
            $table->unsignedSmallInteger('returned_cans_count')->default(0);
            $table->unsignedSmallInteger('returned_bottles_count')->default(0);
            $table->string('deposit_status')->default('HELD'); // HELD, REFUNDED, FORFEITED_TRASH_LEFT
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eco_deposits');
        Schema::dropIfExists('cashier_shifts');
        Schema::dropIfExists('pos_order_items');
        Schema::dropIfExists('pos_orders');
        Schema::dropIfExists('rental_order_items');
        Schema::dropIfExists('rental_orders');
        Schema::dropIfExists('sos_alerts');
        Schema::dropIfExists('trail_checkpoints');
        Schema::dropIfExists('simaksi_members');
        Schema::dropIfExists('simaksi_bookings');
        Schema::dropIfExists('rental_items');
        Schema::dropIfExists('consignment_settlements');
        Schema::dropIfExists('product_stock_logs');
        Schema::dropIfExists('products');
        Schema::dropIfExists('consignment_mitras');
        Schema::dropIfExists('daily_quotas');
        Schema::dropIfExists('trail_settings');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'assigned_pos']);
        });
    }
};
