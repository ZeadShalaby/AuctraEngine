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
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 14, 2);

            $table->enum('type', [
                'deposit',
                'withdraw',
                'bid_hold',
                'bid_release',
                'auction_win'
            ]);

            $table->decimal('balance_before', 14, 2);
            $table->decimal('balance_after', 14, 2);

            $table->decimal('reserved_before', 14, 2)->default(0);
            $table->decimal('reserved_after', 14, 2)->default(0);

            $table->string('reference')->nullable(); // auction_id / bid_id

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};
