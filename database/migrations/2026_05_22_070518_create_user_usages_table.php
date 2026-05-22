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
        Schema::create('user_usages', function (Blueprint $table) {
              $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->date('usage_date');
    $table->unsignedBigInteger('used_bytes')->default(0);

    $table->timestamps();

    $table->index(['user_id', 'usage_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_usages');
    }
};
