<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'tb_keranjang';
    protected $fillable = ['user_id','game_id','quantity'];

    // Matikan semua timestamp
    public $timestamps = false;

    public function game() {
        return $this->belongsTo(Game::class, 'game_id');
    }
}