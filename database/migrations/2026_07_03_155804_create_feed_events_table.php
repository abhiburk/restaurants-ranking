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
        Schema::create('feed_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('city_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('restaurant_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('event_type', [
                'rank_change',
                'trending',
                'vote_milestone',
                'streak_milestone',
                'daily_winner',
                'new_entry',
                'city_pulse',
            ]);
            $table->json('event_data');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            // Feed query index — most important
            $table->index(['city_id', 'occurred_at']);
            $table->index(['city_id', 'event_type']);
            $table->index('expires_at'); // for cleanup job
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_events');
    }
};
