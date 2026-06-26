<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'detail_transaksi_id', 'alasan', 'status'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailTransaksi() {
        return $this->belongsTo(DetailTransaksi::class, 'detail_transaksi_id');
    }
}
