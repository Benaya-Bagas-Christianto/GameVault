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
    Schema::create('tb_reviews', function (Blueprint $table) {
        $table->id();
        // Menggunakan tipe data biasa agar tidak bentrok dengan tabel lama
        $table->integer('user_id'); 
        $table->integer('game_id'); 
        $table->integer('rating');
        $table->text('komentar')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
