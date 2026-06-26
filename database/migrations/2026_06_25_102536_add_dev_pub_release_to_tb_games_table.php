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
        Schema::table('tb_games', function (Blueprint $table) {
            $table->string('developer')->nullable()->after('console_edition');
            $table->string('publisher')->nullable()->after('developer');
            $table->date('release_date')->nullable()->after('publisher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_games', function (Blueprint $table) {
            $table->dropColumn(['developer', 'publisher', 'release_date']);
        });
    }
};
