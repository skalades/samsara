<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_visits', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('ip_hash', 64)->nullable();
            $table->string('page_path');
            $table->string('page_type')->default('landing')->index(); // landing, booking, checkout, etc.
            $table->date('visited_date')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_visits');
    }
};
