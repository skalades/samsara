<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trail_settings', function (Blueprint $table) {
            $table->unsignedInteger('ticket_fee_per_person')->default(25000)->after('weather_status');
            $table->unsignedInteger('eco_deposit_fee')->default(20000)->after('ticket_fee_per_person');
            $table->unsignedInteger('insurance_fee')->default(5000)->after('eco_deposit_fee');
            $table->unsignedInteger('pad_desa_fee')->default(5000)->after('insurance_fee');
        });
    }

    public function down(): void
    {
        Schema::table('trail_settings', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_fee_per_person',
                'eco_deposit_fee',
                'insurance_fee',
                'pad_desa_fee',
            ]);
        });
    }
};
