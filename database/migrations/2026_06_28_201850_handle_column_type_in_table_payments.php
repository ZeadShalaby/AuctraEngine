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
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('type', [
                'withdraw',
                'deposit', // رايح يشحن محفظته بفلوس من بره
                'auction_terms',  // رايح يشتري كراسة شروط بالفيزا علطول
                'auction_win',    // رايح يدفع ثمن المزاد الكلي بالفيزا علطول
                'ad_fee',      // رايح يدفع ثمن الإعلان بالفيزا علطول
                'other',
                'auction_promotion',
                'auction_terms_refund'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('type', [
                'auction_terms_refund',
            ])->change();
        });
    }
};
