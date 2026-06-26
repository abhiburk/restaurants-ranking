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
        Schema::create('votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable();
            $table->foreignUuid('city_id')->nullable();
            $table->integer('rating')->default(0);
            $table->text('comment')->nullable();
            $table->json('amenities')->nullable();
            $table->timestamp('voted_at');
            $table->string('visitor_id');
            $table->string('ip_address');
            $table->string('ip_hash');
            $table->string('user_agent_hash');
            $table->string('vote_source')->default('url'); // qr, url
            $table->string('country_code', 2)->nullable();
            $table->boolean('is_vpn')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->string('flag_reason')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Hard dedup stops
            $table->unique(['visitor_id', 'restaurant_id', 'voted_at']);
            $table->unique(['user_id', 'restaurant_id', 'voted_at']);

            // Query performance
            $table->index(['restaurant_id', 'voted_at', 'deleted_at']);
            $table->index(['ip_hash', 'restaurant_id', 'voted_at']);
            $table->index(['created_at', 'restaurant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
