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
        Schema::create('ad_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('placement', ['feed', 'reels', 'both']);            //? feed | reels | both
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('max_impressions')->default(0);
            $table->integer('max_days')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_prices');
    }
};
