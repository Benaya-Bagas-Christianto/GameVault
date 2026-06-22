<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'tb_wishlist';
    protected $fillable = ['user_id','game_id'];

    // Matikan updated_at
    const UPDATED_AT = null;

    public function game() {
        return $this->belongsTo(Game::class, 'game_id');
    }
}