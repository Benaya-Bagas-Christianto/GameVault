<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('game_galleries', function (Blueprint $table) {
            $table->id();
            
            // Cukup gunakan bigInteger biasa, hapus baris foreign key yang bikin MySQL rewel
            $table->bigInteger('game_id'); 
            
            $table->string('type'); // Isi: 'image' atau 'video'
            $table->string('path'); // Isi: nama file gambar atau link youtube
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_galleries');
    }
};
