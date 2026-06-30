<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Review; // Tambahkan ini agar lebih rapi
use Illuminate\Http\Request; // Tambahkan ini
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Menampilkan riwayat pesanan
    public function index() {
        // Auth::check() sudah ditangani oleh routes/web.php
        $transaksi = Transaksi::with('details.game')
                        ->where('user_id', Auth::id())
                        ->latest()
                        ->get();
                        
        return view('orders', compact('transaksi'));
    }

    // Menyimpan ulasan (rating & komentar tar tar tar wkwkwk)
    public function simpanReview(Request $request) {
        $mediaPaths = [];
        $videoCuts = json_decode($request->input('video_cuts', '{}'), true);
        
        if ($request->hasFile('media')) {
            $files = $request->file('media');
            // Pastikan $files selalu array (karena dari input name="media[]")
            if (!is_array($files)) {
                $files = [$files];
            }
            
            $targetDir = public_path('assets/reviews');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            foreach ($files as $file) {
                // Pastikan format aman dan upload ke folder public/assets/reviews
                $originalName = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
                $file->move($targetDir, $filename);
                
                $finalPath = 'assets/reviews/' . $filename;
                
                // Tambahkan fragment media jika ada potongan video
                if (isset($videoCuts[$originalName])) {
                    $start = $videoCuts[$originalName]['start'] ?? 0;
                    $end = $videoCuts[$originalName]['end'] ?? 0;
                    $finalPath .= "#t={$start},{$end}";
                }
                
                $mediaPaths[] = $finalPath;
            }
        }
        
        $mediaPathStr = count($mediaPaths) > 0 ? implode('|', $mediaPaths) : null;

        // Cek apakah user sudah pernah review game ini
        $cek_review = Review::where('user_id', Auth::id())
                          ->where('game_id', $request->game_id)
                          ->first();

        if($cek_review) {
            // Kalau sudah pernah, update yang lama
            $cek_review->rating = $request->rating;
            $cek_review->komentar = $request->komentar;
            $existingMedia = $request->input('existing_media', '');
            $keptMediaArray = $existingMedia ? explode('|', $existingMedia) : [];
            
            // Hapus file fisik untuk media yang tidak dipertahankan
            $oldMediaArray = $cek_review->media ? explode('|', $cek_review->media) : [];
            $deletedMedia = array_diff($oldMediaArray, $keptMediaArray);
            foreach ($deletedMedia as $delPath) {
                $realPath = preg_replace('/#t=.*$/', '', trim($delPath));
                $filePath = public_path($realPath);
                if (file_exists($filePath) && !is_dir($filePath)) {
                    unlink($filePath);
                }
            }

            // Gabungkan media yang lama (yang dipertahankan) dengan media baru
            $finalMedia = array_merge($keptMediaArray, $mediaPaths);
            $cek_review->media = count($finalMedia) > 0 ? implode('|', $finalMedia) : null;
            $cek_review->save();
        } else {
            // Kalau belum, buat baru
            $review = new Review();
            $review->user_id = Auth::id();
            $review->game_id = $request->game_id;
            $review->rating = $request->rating;
            $review->komentar = $request->komentar;
            
            $finalMedia = $mediaPaths; // Hanya media baru jika review baru
            $review->media = count($finalMedia) > 0 ? implode('|', $finalMedia) : null;
            $review->save();
        }

        return redirect()->back()->with('msg', 'Terima kasih atas ulasanmu!')->with('status', 'success');
    }

    // Menghapus ulasan
    public function hapusReview(Request $request) {
        $review = Review::where('user_id', Auth::id())
                        ->where('game_id', $request->game_id)
                        ->first();

        if ($review) {
            // Hapus file fisik jika ada media
            if ($review->media) {
                $medias = explode('|', $review->media);
                foreach ($medias as $media) {
                    $realPath = preg_replace('/#t=.*$/', '', trim($media));
                    $filePath = public_path($realPath);
                    if (file_exists($filePath) && !is_dir($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            // Hapus dari database
            $review->delete();
            return redirect()->back()->with('msg', 'Ulasan berhasil dihapus.')->with('status', 'success');
        }

        return redirect()->back()->with('msg', 'Ulasan tidak ditemukan.')->with('status', 'error');
    }

    
} // <--- PERHATIKAN: Kurung penutup class harus ada di PALING BAWAH