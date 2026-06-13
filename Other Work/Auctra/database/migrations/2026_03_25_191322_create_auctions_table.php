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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();;
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->decimal('start_price', 10, 2); // ? starting price
            $table->decimal('min_bid_increment', 10, 2)->default(1); // ? minimum bid increment
            $table->decimal('buy_now_price', 10, 2)->nullable();
            $table->decimal('current_price', 10, 2)->nullable();  //? current highest bid price
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            // ? status new or used
            $table->enum('condition', ['new', 'used']);
            $table->enum('status', ['pending', 'processing', 'active', 'ended', 'cancelled'])->default('pending'); // ? todo AuctionStatus
            $table->integer('bids_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
