import os
import re

# 1. Update routes/web.php
with open(r'd:\Laragon\laragon\www\gamevault\routes\web.php', 'r', encoding='utf-8') as f:
    web_php = f.read()

if "cancel-if-unpaid" not in web_php:
    web_php = web_php.replace("Route::post('/checkout/success', [CheckoutController::class, 'success']);", "Route::post('/checkout/success', [CheckoutController::class, 'success']);\n    Route::post('/checkout/cancel-if-unpaid', [CheckoutController::class, 'cancelIfUnpaid']);")
    with open(r'd:\Laragon\laragon\www\gamevault\routes\web.php', 'w', encoding='utf-8') as f:
        f.write(web_php)


# 2. Update CheckoutController.php
with open(r'd:\Laragon\laragon\www\gamevault\app\Http\Controllers\CheckoutController.php', 'r', encoding='utf-8') as f:
    checkout_php = f.read()

# Return order_id in process()
checkout_php = checkout_php.replace(
    "return response()->json(['status'=>'success', 'snap_token' => $snapToken]);",
    "return response()->json(['status'=>'success', 'snap_token' => $snapToken, 'order_id' => $trx->id]);"
)

# Add cancelIfUnpaid method at the end of class before the last brace
cancel_logic = r"""
    // 4. FUNGSI CANCEL JIKA BELUM MILIH METODE PEMBAYARAN (onClose Snap)
    public function cancelIfUnpaid(Request $request) {
        $orderId = $request->order_id;
        if (!$orderId) return response()->json(['status' => 'error', 'message' => 'No Order ID']);
        
        $trx = Transaksi::find($orderId);
        if (!$trx || $trx->status != 'Pending') return response()->json(['status' => 'ok']);

        // Set konfigurasi
        Config::$serverKey = env('MIDTRANS_SERVER_KEY', 'Mid-server-tADVX9-15wfmU2wUv60szXql');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            // Tanya status langsung ke Midtrans API
            $statusResponse = \Midtrans\Transaction::status($orderId);
            // Jika tidak ada Exception (404), berarti transaksi sudah masuk ke sistem Midtrans 
            // (user sudah memilih metode pembayaran seperti VA). Biarkan tetap Pending.
            return response()->json(['status' => 'kept']);
        } catch (\Exception $e) {
            // Jika 404 (Exception), berarti user belum memilih metode pembayaran apapun (hanya buka popup lalu tutup).
            // Hapus dari database agar riwayat tidak menumpuk status Pending.
            DetailTransaksi::where('transaksi_id', $trx->id)->delete();
            $trx->delete();
            return response()->json(['status' => 'deleted']);
        }
    }
}"""
if "cancelIfUnpaid" not in checkout_php:
    # use normal string replace for the last brace to avoid regex backslash issues
    idx = checkout_php.rfind('}')
    checkout_php = checkout_php[:idx] + cancel_logic.replace(r'\Midtrans', '\\Midtrans') + checkout_php[idx+1:]
    
    with open(r'd:\Laragon\laragon\www\gamevault\app\Http\Controllers\CheckoutController.php', 'w', encoding='utf-8') as f:
        f.write(checkout_php)

print("Done")
