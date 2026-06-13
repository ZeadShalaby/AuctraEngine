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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2); // ? the amount of the bid
            $table->boolean('is_auto')->default(false); // ? if the bid is auto
            $table->decimal('max_auto_bid', 10, 2)->nullable();  // ? the max auto bid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
