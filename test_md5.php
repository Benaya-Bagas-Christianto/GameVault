<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$trx = App\Models\Transaksi::latest()->first();
$d = App\Models\DetailTransaksi::where('transaksi_id', $trx->id)->first();
$user_id = $trx->user_id;
$game_id = $d->game_id;

$str1 = $game_id . $user_id . $trx->created_at;
$hash1 = strtoupper(substr(md5($str1), 0, 12));
$key1 = 'GV-' . substr($hash1, 0, 4) . '-' . substr($hash1, 4, 4) . '-' . substr($hash1, 8, 4);

$rawQueryTglBeli = \Illuminate\Support\Facades\DB::table('tb_transaksi')->where('id', $trx->id)->value('created_at');
$str2 = $game_id . $user_id . $rawQueryTglBeli;
$hash2 = strtoupper(substr(md5($str2), 0, 12));
$key2 = 'GV-' . substr($hash2, 0, 4) . '-' . substr($hash2, 4, 4) . '-' . substr($hash2, 8, 4);

echo "Method 1 (Controller): " . $key1 . "\n";
echo "Method 2 (Library): " . $key2 . "\n";
