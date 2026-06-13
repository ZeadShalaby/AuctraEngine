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
        Schema::create('report_actions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')
                ->constrained('reports')
                ->cascadeOnDelete();

            $table->foreignId('admin_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('action', [
                'none',
                'delete_post',
                'delete_reel',
                'ban_user',
                'warning',
                'ignore'
            ])->default('none');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_actions');
    }
};
