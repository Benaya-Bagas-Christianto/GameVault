import re

with open('app/Http/Controllers/CheckoutController.php', 'r', encoding='utf-8') as f:
    content = f.read()

free_logic = '''        $total = $items->sum(fn($k) => $k->game->price * $k->quantity);

        if ($total == 0) {
            // BYPASS MIDTRANS
            $trx = Transaksi::create(['user_id'=>$user->id,'total_bayar'=>0,'status'=>'Success']);
            
            $detailsForEmail = [];
            foreach ($items as $item) {
                DetailTransaksi::create([
                    'transaksi_id'   => $trx->id,
                    'game_id'        => $item->game_id,
                    'harga_saat_beli'=> 0,
                ]);

                // Generate Kode Lisensi
                $key = 'GV-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
                $detailsForEmail[] = (object)[
                    'name' => $item->game->name,
                    'harga_saat_beli' => 0,
                    'activation_key' => $key
                ];
            }

            // Kosongkan keranjang
            Keranjang::where('user_id', $user->id)->delete();

            // Kirim Email beserta Struk PDF
            try {
                $pdf = Pdf::loadView('invoice_pdf', [
                    'trx' => $trx,
                    'details' => $detailsForEmail,
                    'user' => $user
                ])->setPaper('A4', 'portrait');

                $pdfContent = $pdf->output();

                Mail::send('emails.lisensi', ['user' => $user, 'details' => $detailsForEmail], function($message) use ($user, $trx, $pdfContent) {
                    $message->to($user->email)
                            ->subject('Lisensi Game Gratis & Invoice - GameVault #' . $trx->id)
                            ->attachData($pdfContent, 'Invoice-GameVault-#' . $trx->id . '.pdf', [
                                'mime' => 'application/pdf',
                            ]);
                });
            } catch (\\Exception $e) {
                \\Illuminate\\Support\\Facades\\Log::error('Gagal kirim email: ' . $e->getMessage());
            }

            return response()->json(['status'=>'success', 'is_free' => true, 'order_id' => $trx->id]);
        }'''

# Replace the line: $total = $items->sum(fn($k) => $k->game->price * $k->quantity);
content = content.replace('$total = $items->sum(fn($k) => $k->game->price * $k->quantity);', free_logic)

with open('app/Http/Controllers/CheckoutController.php', 'w', encoding='utf-8') as f:
    f.write(content)

# Patch cart.blade.php
with open('resources/views/cart.blade.php', 'r', encoding='utf-8') as f:
    cart_content = f.read()

cart_old_js = '''                    // Jika berhasil dapat token, panggil Pop-Up Midtrans!
                    if (data.snap_token) {'''
cart_new_js = '''                    // Jika gratis, langsung sukses!
                    if (data.is_free) {
                        localStorage.setItem('cartCount', '0');
                        localStorage.setItem('cart_cache', '[]');
                        if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                        if (typeof renderCartPage === 'function') renderCartPage([]);
                        showPaymentModal('success', 'Klaim gratis berhasil!', '/orders');
                    }
                    // Jika berhasil dapat token, panggil Pop-Up Midtrans!
                    else if (data.snap_token) {'''

cart_content = cart_content.replace(cart_old_js, cart_new_js)

with open('resources/views/cart.blade.php', 'w', encoding='utf-8') as f:
    f.write(cart_content)

# Patch script.js
with open('public/script.js', 'r', encoding='utf-8') as f:
    script_content = f.read()

script_old_js = '''        if (data.status === 'success') {
            // Panggil Pop-Up Midtrans!
            window.snap.pay(data.snap_token, {'''

script_new_js = '''        if (data.status === 'success') {
            if (data.is_free) {
                showToast("Klaim Gratis Sukses!", 'success');
                const modal = document.querySelector('.fixed.inset-0');
                if (modal) modal.remove();
                showSuccessModal(data.order_id);
                updateCartUI();
                return;
            }
            // Panggil Pop-Up Midtrans!
            window.snap.pay(data.snap_token, {'''

script_content = script_content.replace(script_old_js, script_new_js)

with open('public/script.js', 'w', encoding='utf-8') as f:
    f.write(script_content)

print("Patch applied")
