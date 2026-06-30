<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\RefundStatusMail;

class RefundController extends Controller
{
    // API endpoint untuk cek notifikasi refund user
    public function checkNotif(Request $request)
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        
        $notifications = Refund::whereHas('detailTransaksi.transaksi', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->where('is_notified', false)
        ->whereIn('status', ['approved', 'rejected'])
        ->with('detailTransaksi.game')
        ->get();
        
        $notifData = [];
        foreach($notifications as $notif) {
            $gameName = $notif->detailTransaksi->game->name ?? 'Game';
            $statusStr = $notif->status == 'approved' ? 'disetujui' : 'ditolak';
            $notifData[] = [
                'id' => $notif->id,
                'message' => "Pengajuan refund untuk $gameName telah $statusStr.",
                'type' => $notif->status == 'approved' ? 'success' : 'error'
            ];
            // KITA TIDAK LAGI MENANDAI SEBAGAI NOTIFIED DI SINI
        }
        
        return response()->json(['notifications' => $notifData]);
    }

    // API endpoint untuk menandai notifikasi sudah di-klik
    public function markNotified($id)
    {
        $refund = Refund::find($id);
        if ($refund) {
            $refund->update(['is_notified' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    // User submits refund request
    public function requestRefund(Request $request)
    {
        $request->validate([
            'detail_transaksi_id' => 'required|exists:tb_detail_transaksi,id',
            'alasan' => 'required|string|max:255',
        ]);

        $detail = DetailTransaksi::with('game')->findOrFail($request->detail_transaksi_id);
        
        // Find transaction
        $transaksi = Transaksi::where('id', $detail->transaksi_id)
            ->where('user_id', auth()->id())
            ->where('status', 'Success')
            ->first();

        if (!$transaksi) {
            return redirect()->back()->with('status', 'error')->with('msg', 'Transaksi tidak ditemukan atau tidak valid.');
        }

        // Check 14 days limit
        $waktuBeli = Carbon::parse($transaksi->created_at);
        if ($waktuBeli->diffInDays(now()) > 14) {
            return redirect()->back()->with('status', 'error')->with('msg', 'Batas waktu pengajuan refund (14 hari) telah lewat.');
        }

        $existing = Refund::where('detail_transaksi_id', $detail->id)->first();
        if ($existing) {
            return redirect()->back()->with('status', 'error')->with('msg', 'Refund untuk game ini sudah diajukan sebelumnya.');
        }

        $hasReview = \App\Models\Review::where('user_id', auth()->id())->where('game_id', $detail->game_id)->exists();
        if ($hasReview) {
            return redirect()->back()->with('status', 'error')->with('msg', 'Anda tidak bisa mengajukan refund karena sudah memberikan ulasan untuk game ini.');
        }

        $alasanFinal = $request->alasan;
        if ($alasanFinal === 'Lainnya' && $request->filled('alasan_lainnya')) {
            $alasanFinal = $request->alasan_lainnya;
        }

        Refund::create([
            'user_id' => auth()->id(),
            'detail_transaksi_id' => $detail->id,
            'alasan' => $alasanFinal,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('status', 'success')->with('msg', 'Pengajuan refund berhasil dikirim dan sedang diproses oleh admin.');
    }

    // User cancels their pending refund request
    public function cancelRefund(Request $request)
    {
        $request->validate([
            'detail_transaksi_id' => 'required|exists:tb_detail_transaksi,id',
        ]);

        $refund = Refund::where('detail_transaksi_id', $request->detail_transaksi_id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$refund) {
            return redirect()->back()->with('status', 'error')->with('msg', 'Pengajuan refund tidak ditemukan atau sudah tidak bisa dibatalkan.');
        }

        $refund->delete(); // Atau update status jadi 'cancelled' jika Anda ingin menyimpan riwayatnya. Di sini kita hapus saja agar bisa diajukan ulang nanti jika batas 14 hari masih ada.

        return redirect()->back()->with('status', 'success')->with('msg', 'Pengajuan refund berhasil dibatalkan.');
    }

    // Admin views refund requests
    public function indexAdmin()
    {
        $refunds = Refund::with(['user', 'detailTransaksi.game'])->orderBy('created_at', 'desc')->get();
        return view('admin.refunds', compact('refunds'));
    }

    // Admin processes refund
    public function process(Request $request, $id)
    {
        $refund = Refund::with(['user', 'detailTransaksi.game'])->findOrFail($id);
        $action = $request->input('action');
        $gameName = $refund->detailTransaksi->game->name;

        if ($action === 'approve') {
            DB::transaction(function () use ($refund) {
                $refund->update(['status' => 'approved']);
                $detail = \App\Models\DetailTransaksi::findOrFail($refund->detail_transaksi_id);
                $detail->update(['is_refunded' => true]);
                
                // Kurangi total pendapatan transaksi
                $trx = \App\Models\Transaksi::findOrFail($detail->transaksi_id);
                $trx->update(['total_bayar' => max(0, $trx->total_bayar - $detail->harga_saat_beli)]);
            });

            // Kirim Email Disetujui
            Mail::to($refund->user->email)->send(new RefundStatusMail($refund, 'approved', $gameName));

            return redirect()->back()->with('status', 'success')->with('msg', 'Refund disetujui. Akses lisensi game telah dicabut dan email notifikasi telah dikirim.');
        } elseif ($action === 'reject') {
            $refund->update(['status' => 'rejected']);

            // Kirim Email Ditolak
            Mail::to($refund->user->email)->send(new RefundStatusMail($refund, 'rejected', $gameName));

            return redirect()->back()->with('status', 'success')->with('msg', 'Refund ditolak dan email notifikasi telah dikirim.');
        }

        return redirect()->back()->with('status', 'error')->with('msg', 'Aksi tidak valid.');
    }
}
