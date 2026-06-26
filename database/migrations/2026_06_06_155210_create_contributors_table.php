<?php

use App\Enums\ContributorStatus;
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
        Schema::create('contributors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id');
            $table->foreignUuid('city_id');
            $table->text('motivation')->nullable();
            $table->foreignUuid('contributor_level_id')->default(1);
            $table->unsignedInteger('points')->default(0);
            $table->decimal('quality_score', 3, 2)->default(0.00);
            $table->boolean('is_active')->default(false);
            $table->string('status')->default(ContributorStatus::PENDING);
            $table->text('reason')->nullable()->comment('Reason for rejection');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignUuid('reviewed_by')->nullable();
            $table->enum('review_tier', ['standard', 'fast_track', 'peer_reviewer'])->default('standard');
            $table->timestamp('joined_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('contributor_level_id');
            $table->index('points');
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributors');
    }
};
