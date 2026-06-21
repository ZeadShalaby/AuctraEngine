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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('merchant_ref')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->enum('payment_gateway', ['moamalat', 'paypal', 'stripe', 'other']);
            $table->enum('type', [
                'deposit', // رايح يشحن محفظته بفلوس من بره
                'auction_terms',  // رايح يشتري كراسة شروط بالفيزا علطول
                'auction_win',    // رايح يدفع ثمن المزاد الكلي بالفيزا علطول
                'ad_fee',      // رايح يدفع ثمن الإعلان بالفيزا علطول
                'other',
                'auction_promotion'
            ]);

            $table->nullableMorphs('payable');

            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
