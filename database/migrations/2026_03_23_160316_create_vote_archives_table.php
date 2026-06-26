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
        Schema::create('vote_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('restaurant_id')->constrained();
            $table->foreignUuid('city_id');
            $table->integer('votes')->comment('The total number of votes for the restaurant')->default(0);
            $table->integer('rank')->nullable();
            $table->integer('rank_change')->nullable();
            $table->integer('unique_voters')->default(0);
            $table->integer('vote_change')->default(0);
            $table->date('archived_at');
            $table->date('first_vote_at');
            $table->date('last_vote_at');
            $table->timestamps();

            $table->index(['city_id', 'archived_at', 'rank']);
            $table->index(['restaurant_id', 'archived_at']);
            $table->index(['city_id', 'archived_at']);
            $table->index(['archived_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_archives');
    }
};
