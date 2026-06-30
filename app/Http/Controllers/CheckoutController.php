<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str; // Ditambahkan untuk fungsi string random
use Barryvdh\DomPDF\Facade\Pdf;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        if (!Auth::check()) return response()->json(['status' => 'error', 'message' => 'Silakan login']);

        $user = Auth::user();

        $query = Keranjang::with('game')->where('user_id', $user->id);
        if ($request->has('selected_ids') && is_array($request->selected_ids)) {
            $query->whereIn('game_id', $request->selected_ids);
        }
        $items = $query->get();

        if ($items->isEmpty()) return response()->json(['status' => 'error', 'message' => 'Keranjang kosong atau item tidak ditemukan']);

        $total = $items->sum(fn($k) => $k->game->price * $k->quantity);

        if ($total == 0) {
            // BYPASS MIDTRANS
            $trx = Transaksi::create(['user_id' => $user->id, 'total_bayar' => 0, 'status' => 'Success']);

            $detailsForEmail = [];
            foreach ($items as $item) {
                $detail = DetailTransaksi::create([
                    'transaksi_id'   => $trx->id,
                    'game_id'        => $item->game_id,
                    'harga_saat_beli' => 0,
                ]);

                // Generate Kode Lisensi (Sama dengan format di UI)
                $tgl_beli = \Carbon\Carbon::parse($trx->created_at)->format('Y-m-d H:i:s');
                $kodeRaw = strtoupper(substr(md5($item->game_id . $user->id . $tgl_beli), 0, 12));
                $key = 'GV-' . substr($kodeRaw, 0, 4) . '-' . substr($kodeRaw, 4, 4) . '-' . substr($kodeRaw, 8, 4);
                $detailsForEmail[] = (object)[
                    'name' => $item->game->name,
                    'harga_saat_beli' => 0,
                    'activation_key' => $key
                ];
            }

            // Kosongkan keranjang HANYA untuk item yang dibeli
            Keranjang::where('user_id', $user->id)->whereIn('game_id', $items->pluck('game_id'))->delete();

            // Kirim Email beserta Struk PDF
            try {
                $pdf = Pdf::loadView('invoice_pdf', [
                    'trx' => $trx,
                    'details' => $detailsForEmail,
                    'user' => $user
                ])->setPaper('A4', 'portrait');

                $pdfContent = $pdf->output();

                Mail::send('emails.lisensi', ['user' => $user, 'details' => $detailsForEmail], function ($message) use ($user, $trx, $pdfContent) {
                    $message->to($user->email)
                        ->subject('Lisensi Game Gratis & Invoice - GameVault #' . $trx->id)
                        ->attachData($pdfContent, 'Invoice-GameVault-#' . $trx->id . '.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal kirim email: ' . $e->getMessage());
            }

            return response()->json(['status' => 'success', 'is_free' => true, 'order_id' => $trx->id]);
        }

        // Buat Transaksi dengan status PENDING
        $trx = Transaksi::create(['user_id' => $user->id, 'total_bayar' => $total, 'status' => 'Pending']);

        $itemDetails = [];
        foreach ($items as $item) {
            DetailTransaksi::create([
                'transaksi_id'   => $trx->id,
                'game_id'        => $item->game_id,
                'harga_saat_beli' => $item->game->price, // Ini tetap harga satuan
            ]);

            $itemDetails[] = [
                'id' => $item->game_id,
                'price' => $item->game->price,
                'quantity' => $item->quantity, // FIX: Gunakan kuantitas sebenarnya
                'name' => substr($item->game->name, 0, 50)
            ];
        }

        // SETUP MIDTRANS 
        Config::$serverKey = env('MIDTRANS_SERVER_KEY', 'Mid-server-tADVX9-15wfmU2wUv60szXql');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
        Config::$overrideNotifUrl = 'https://9c17178dead44a.lhr.life/checkout/notification';

        $payload = [
            'transaction_details' => [
                'order_id' => $trx->id,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $user->username,
                'email' => $user->email,
            ],
            'item_details' => $itemDetails
        ];

        try {
            $snapToken = Snap::getSnapToken($payload);
            $trx->snap_token = $snapToken;
            $trx->save();


            return response()->json(['status' => 'success', 'snap_token' => $snapToken, 'order_id' => $trx->id]);
        } catch (\Exception $e) {


            
            DetailTransaksi::where('transaksi_id', $trx->id)->delete();
            $trx->delete();

            return response()->json(['status' => 'error', 'message' => 'Error dari Midtrans: ' . $e->getMessage()]);
        }
    }

    // 2. FUNGSI NOTIFICATION: Ditembak Otomatis Oleh Server Midtrans
    public function notification(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('MIDTRANS HIT!', ['payload' => $request->getContent()]);

        $payload = $request->getContent();
        $notification = json_decode($payload);

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;

        // Cari transaksi beserta data User pembelinya
        $trx = Transaksi::with('user')->find($orderId);
        if (!$trx) return response()->json(['message' => 'Order not found'], 404);

        // Jika Midtrans bilang sukses dibayar (capture/settlement)
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($trx->status == 'Pending') {

                // 1. Ubah status jadi Success
                $trx->status = 'Success';
                $trx->save();

                // 2. Generate Kode Lisensi (Activation Key)
                $details = DetailTransaksi::with('game')->where('transaksi_id', $trx->id)->get();
                $detailsForEmail = [];

                foreach ($details as $d) {
                    // Membuat kode acak (Sama dengan format di UI)
                    $tgl_beli = \Carbon\Carbon::parse($trx->created_at)->format('Y-m-d H:i:s');
                    $kodeRaw = strtoupper(substr(md5($d->game_id . $trx->user_id . $tgl_beli), 0, 12));
                    $key = 'GV-' . substr($kodeRaw, 0, 4) . '-' . substr($kodeRaw, 4, 4) . '-' . substr($kodeRaw, 8, 4);

                    // Kalau kamu punya kolom activation_key di tabel tb_detail_transaksi, aktifkan kode di bawah ini:
                    // $d->activation_key = $key;
                    // $d->save();

                    $detailsForEmail[] = (object)[
                        'name' => $d->game->name,
                        'harga_saat_beli' => $d->harga_saat_beli,
                        'activation_key' => $key
                    ];
                }

                // 3. Tarik user dari relasi database (Menggantikan Auth::user)
                $user = $trx->user;

                // 4. Bersihkan keranjang belanja HANYA untuk game yang berhasil dibeli
                $purchasedGameIds = $details->pluck('game_id')->toArray();
                Keranjang::where('user_id', $user->id)->whereIn('game_id', $purchasedGameIds)->delete();

                // 5. Kirim Email beserta Struk PDF
                try {
                    $pdf = Pdf::loadView('invoice_pdf', [
                        'trx' => $trx,
                        'details' => $detailsForEmail,
                        'user' => $user
                    ])->setPaper('A4', 'portrait');

                    $pdfContent = $pdf->output();

                    Mail::send('emails.lisensi', ['user' => $user, 'details' => $detailsForEmail], function ($message) use ($user, $trx, $pdfContent) {
                        $message->to($user->email)
                            ->subject('Lisensi Game & Invoice Pembelian - GameVault #' . $trx->id)
                            ->attachData($pdfContent, 'Invoice-GameVault-#' . $trx->id . '.pdf', [
                                'mime' => 'application/pdf',
                            ]);
                    });
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Gagal kirim email: ' . $e->getMessage());
                }
            }
        }
        // Jika pembayaran baru saja dibuat (Pending / VA Generated)
        elseif ($transactionStatus == 'pending') {
            if ($trx->status == 'Pending') {
                $details = DetailTransaksi::where('transaksi_id', $trx->id)->get();
                $purchasedGameIds = $details->pluck('game_id')->toArray();
                Keranjang::where('user_id', $trx->user_id)->whereIn('game_id', $purchasedGameIds)->delete();
            }
        }
        // Jika pembayaran dibatalkan / gagal
        elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $trx->status = 'Failed';
            $trx->save();
        }

        return response()->json(['message' => 'Sukses Menerima Notifikasi Midtrans']);
    }

    // 3. FUNGSI SUCCESS: Cek status Midtrans langsung dari Frontend (Bypass Webhook Delay)
    public function success(Request $request)
    {
        $orderId = $request->order_id;
        if (!$orderId) return response()->json(['status' => 'error', 'message' => 'No Order ID']);

        // Set konfigurasi
        Config::$serverKey = env('MIDTRANS_SERVER_KEY', 'Mid-server-tADVX9-15wfmU2wUv60szXql');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            // Tanya status langsung ke Midtrans API
            $statusResponse = \Midtrans\Transaction::status($orderId);

            // Jika statusnya sudah terbayar
            if (isset($statusResponse->transaction_status) && in_array($statusResponse->transaction_status, ['capture', 'settlement'])) {
                // Simulasikan Request Webhook dan tembakkan ke fungsi notification() kita sendiri
                $fakeRequest = new Request();
                $fakeRequest->initialize([], [], [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($statusResponse));
                $this->notification($fakeRequest);
            }
        } catch (\Exception $e) {
            // Abaikan jika order tidak ada di Midtrans
        }

        return response()->json(['status' => 'success']);
    }

    // 4. FUNGSI CANCEL JIKA BELUM MILIH METODE PEMBAYARAN (onClose Snap)
    public function cancelIfUnpaid(Request $request)
    {
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

            // Hapus dari keranjang karena sudah resmi pending di Midtrans
            $details = DetailTransaksi::where('transaksi_id', $trx->id)->get();
            $gameIds = $details->pluck('game_id')->toArray();
            Keranjang::where('user_id', $trx->user_id)->whereIn('game_id', $gameIds)->delete();

            return response()->json(['status' => 'kept']);
        } catch (\Exception $e) {
            // Jika 404 (Exception), berarti user belum memilih metode pembayaran apapun (hanya buka popup lalu tutup).
            // Hapus dari database agar riwayat tidak menumpuk status Pending.
            DetailTransaksi::where('transaksi_id', $trx->id)->delete();
            $trx->delete();
            return response()->json(['status' => 'deleted']);
        }
    }

    // 5. FUNGSI MARK PENDING KETIKA USER SUDAH PILIH METODE (ON-PENDING SNAP)
    public function markPending(Request $request)
    {
        $orderId = $request->order_id;
        if (!$orderId) return response()->json(['status' => 'error', 'message' => 'No Order ID']);

        $trx = Transaksi::find($orderId);
        if (!$trx || $trx->status != 'Pending') return response()->json(['status' => 'ok']);

        // Hapus dari keranjang karena sudah resmi pending di Midtrans (misal VA dicetak)
        $details = DetailTransaksi::where('transaksi_id', $trx->id)->get();
        $gameIds = $details->pluck('game_id')->toArray();
        Keranjang::where('user_id', $trx->user_id)->whereIn('game_id', $gameIds)->delete();

        return response()->json(['status' => 'ok']);
    }
}
