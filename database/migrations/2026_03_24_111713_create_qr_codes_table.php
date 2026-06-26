<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['restaurant', 'table', 'promo', 'campaign'])->default('restaurant');
            $table->text('url');
            $table->foreignUuid('restaurant_id')->onDelete('cascade');
            $table->integer('total_scans')->default(0);
            $table->timestamp('last_scan_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['restaurant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
