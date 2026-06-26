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
        Schema::create('contributor_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedTinyInteger('level')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 10);
            $table->unsignedInteger('points_required')->default(0);
            $table->unsignedTinyInteger('quality_score_required')->default(0);
            $table->unsignedInteger('monthly_submission_limit')->default(10);
            $table->boolean('can_peer_review')->default(false);
            $table->boolean('submission_auto_approve')->default(false);
            $table->json('perks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributor_levels');
    }
};
