<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameGallery extends Model
{
    use HasFactory;

    protected $table = 'game_galleries';
    protected $fillable = ['game_id', 'type', 'path'];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}