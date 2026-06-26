<?php

use App\Enums\ContributorAction;
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
        Schema::create('contributor_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contributor_id');
            $table->integer('points'); // can be negative for penalties
            $table->string('action');
            $table->nullableUuidMorphs('loggable');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['contributor_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributor_logs');
    }
};
