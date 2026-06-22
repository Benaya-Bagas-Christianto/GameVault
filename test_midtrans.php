<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
\Midtrans\Config::$isProduction = false;

$trx = \App\Models\Transaksi::latest()->first();
echo "TRX ID: " . $trx->id . "\n";
try {
    $res = \Midtrans\Transaction::status($trx->id);
    echo "STATUS: " . json_encode($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
