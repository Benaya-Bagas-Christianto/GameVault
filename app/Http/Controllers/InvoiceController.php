<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($id)
    {
        // 1. Cari data transaksi milik user yang sedang login
        $trx = DB::table('tb_transaksi')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        // Jika transaksi tidak ada atau bukan milik user ini, tolak aksesnya
        if (!$trx) {
            return abort(404, 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // 2. Ambil detail game apa saja yang dibeli di transaksi ini
        $details = DB::table('tb_detail_transaksi')
            ->join('tb_games', 'tb_detail_transaksi.game_id', '=', 'tb_games.id')
            ->where('tb_detail_transaksi.transaksi_id', $id)
            ->select('tb_games.name', 'tb_detail_transaksi.harga_saat_beli')
            ->get();

        // 3. Load tampilan struk (blade) dan kirim datanya
        $pdf = Pdf::loadView('invoice_pdf', [
            'trx' => $trx,
            'details' => $details,
            'user' => Auth::user()
        ]);

        // 4. Atur ukuran kertas ke A4
        $pdf->setPaper('A4', 'portrait');

        // 5. Download otomatis dengan nama file yang keren
        return $pdf->download('Invoice-GameVault-#' . $trx->id . '.pdf');
    }
}