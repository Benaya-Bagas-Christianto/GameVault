<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'tb_games';
    protected $fillable = ['name','image','price','genre','platform','stok','synopsis','description'];
    
    // Matikan updated_at
    const UPDATED_AT = null;
    
    // Relasi ke tabel ulasan/review
    public function reviews() {
        return $this->hasMany(Review::class, 'game_id');
    }

    // Relasi ke Galeri
    public function galleries()
    {
        return $this->hasMany(GameGallery::class, 'game_id', 'id');
    }
}