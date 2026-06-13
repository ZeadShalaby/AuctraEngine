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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 14, 2);
            $table->enum('type', [
                'deposit',
                'withdraw',
                'bid_hold',
                'bid_release',
                'auction_win'
            ]);
            $table->enum('status', [
                'pending',
                'completed',
                'failed'
            ])->default('completed');
            $table->string('reference')->nullable(); //! auction_id or bid_id
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
