<?php

use App\Enums\RestaurantClaimStatus;
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
        Schema::create('restaurant_claims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('user_id');
            $table->foreignUuid('restaurant_id')->comment('The restaurant that is being claimed');
            $table->foreignUuid('city_id')->comment('The city that the user is claiming');
            $table->string('name')->comment('Name of the person claiming the restaurant');
            $table->string('email')->comment('Email of the person claiming the restaurant');
            $table->string('phone')->comment('Phone number of the person claiming the restaurant');
            $table->text('notes')->nullable()->comment('Additional notes provided by the claimant');
            $table->string('document')->comment('Path to the uploaded document for verification');
            $table->string('status')->default(RestaurantClaimStatus::PENDING)->comment('Status of the claim (PENDING, APPROVED, REJECTED)');
            $table->text('reason')->nullable()->comment('Reason for rejection or approval');
            $table->timestamp('reviewed_at')->nullable()->comment('Timestamp when the claim was reviewed');
            $table->timestamp('reviewed_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_claims');
    }
};
