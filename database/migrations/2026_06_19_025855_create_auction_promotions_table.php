<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auction_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('promotion_package_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->useCurrent();
            $table->enum('status', [
                'pending',
                'active',
                'expired',
                'cancelled',
            ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_promotions');
    }
};
