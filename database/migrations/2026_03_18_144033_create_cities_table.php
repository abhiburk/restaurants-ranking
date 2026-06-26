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
        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->foreignUuid('state_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->comment('When active, the city will be available for new registration.')->default(false);
            $table->boolean('is_live')->comment('When true, the city will be show as live.')->default(true);
            $table->string('banner')->nullable()->comment('City banner image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
