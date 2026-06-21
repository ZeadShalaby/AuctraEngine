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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->enum('type', ['normal', 'subscription', 'other'])->default('normal')->index();
            $table->decimal('selling_price', 10, 2)->default(0); 
            $table->decimal('amount', 10, 2)->default(0); 
            $table->decimal('recharge_amount', 10, 2); 
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
