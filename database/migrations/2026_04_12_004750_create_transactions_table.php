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
                'deposit',     // شحن محفظة
                'withdraw',    // سحب من المحفظة
                'bid_hold',    // حجز جدية جدية المزايدة
                'bid_release', // فك الحجز
                'auction_win', // دفع تمن المزاد الفائز به
                'auction_terms', // دفع قيمة المزاد من المحفظة
                'ad_fee',       // دفع قيمة إعلان من المحفظة (أضفناها هنا)
                'auction_promotion' // دفع قيمة اشتراك ف الميزة المزادية من المحفظة
            ]);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');

            $table->nullableMorphs('source'); // بتعمل source_type و source_id (زي Auction أو Bid أو Ad)

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
