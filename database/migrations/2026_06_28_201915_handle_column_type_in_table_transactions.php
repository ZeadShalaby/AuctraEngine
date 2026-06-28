<?php

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
        Schema::table('transactions', function (Blueprint $table) {
             $table->enum('type', [
                'deposit',     // شحن محفظة
                'withdraw',    // سحب من المحفظة
                'bid_hold',    // حجز جدية جدية المزايدة
                'bid_release', // فك الحجز
                'auction_win', // دفع تمن المزاد الفائز به
                'auction_terms', // دفع قيمة المزاد من المحفظة
                'ad_fee',       // دفع قيمة إعلان من المحفظة (أضفناها هنا)
                'auction_promotion', // دفع قيمة اشتراك ف الميزة المزادية من المحفظة
                'auction_terms_refund'
                ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', [
                'auction_terms_refund',
            ])->change();
        });
    }
};
