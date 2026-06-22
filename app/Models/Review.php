<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Kasih tau Laravel nama tabel kita yang sebenarnya
    protected $table = 'tb_reviews';
    protected $guarded = ['id'];

    // Relasi: Satu review dimiliki oleh satu User
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Satu review membahas satu Game
    public function game() {
        return $this->belongsTo(Game::class, 'game_id');
    }
}