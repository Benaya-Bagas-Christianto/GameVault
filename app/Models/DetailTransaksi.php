<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'tb_detail_transaksi';
    protected $fillable = ['transaksi_id','game_id','harga_saat_beli'];

    // Matikan semua timestamp
    public $timestamps = false;

    public function game() {
        return $this->belongsTo(Game::class, 'game_id');
    }
}