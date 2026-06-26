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
        Schema::create('restaurant_views', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('restaurant_id');
            $table->foreignId('user_id')->nullable();
            $table->string('ip_address')->nullable()->comment('The IP address of the user who viewed the restaurant');
            $table->string('visitor_id')->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->comment('The latitude of the user who viewed the restaurant');
            $table->decimal('longitude', 11, 8)->nullable()->comment('The longitude of the user who viewed the restaurant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_views');
    }
};
