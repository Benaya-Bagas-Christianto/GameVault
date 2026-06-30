<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'tb_detail_transaksi';
    protected $fillable = ['transaksi_id','game_id','harga_saat_beli', 'is_refunded'];

    // Matikan semua timestamp
    public $timestamps = false;

    public function game() {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function refund() {
        return $this->hasOne(Refund::class, 'detail_transaksi_id');
    }
}