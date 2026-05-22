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
        Schema::create('user_plans', function (Blueprint $table) {
              $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->date('start_date');
    $table->date('end_date');

    $table->unsignedBigInteger('limit_mb')->default(100);
    $table->enum('limit_type', ['daily', 'monthly', 'yearly'])->default('monthly');

    $table->boolean('is_active')->default(true);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_plans');
    }
};
