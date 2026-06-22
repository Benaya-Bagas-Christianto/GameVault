<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'tb_transaksi';
    protected $fillable = ['user_id','total_bayar','status', 'snap_token'];

    // Matikan updated_at
    const UPDATED_AT = null;

    public function details() {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}