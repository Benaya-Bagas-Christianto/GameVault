<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. Beri tahu Laravel untuk pakai tabel tb_users
    protected $table = 'tb_users';

    // 2. Sesuaikan dengan kolom yang benar-benar ada di database kamu
    protected $fillable = [
        'username', 
        'email',
        'password',
    ];

    // 3. Matikan updated_at karena di tabelmu cuma ada created_at
    const UPDATED_AT = null;

    protected $hidden = [
        'password',
    ];

    public function wishlists() {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    public function keranjangs() {
        return $this->hasMany(Keranjang::class, 'user_id');
    }

    public function transaksis() {
        return $this->hasMany(Transaksi::class, 'user_id');
    }
}