<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameGallery;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Menampilkan Dashboard Statistik
     */
    public function dashboard() {
        $total_games = Game::count();
        $total_transaksi = Transaksi::count();
        $total_users = User::where('role', 'user')->count();

        // Data untuk Grafik Pendapatan (Hanya hitung yang 'Success')
        $salesData = Transaksi::selectRaw("MONTHNAME(created_at) as bulan, SUM(total_bayar) as total")
            ->whereYear('created_at', date('Y'))
            ->where('status', 'Success')
            ->groupByRaw('MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $labels = $salesData->pluck('bulan');
        $totals = $salesData->pluck('total');

        return view('admin.dashboard', compact('total_games', 'total_transaksi', 'total_users', 'labels', 'totals'));
    }

    /**
     * CRUD GAME: Menampilkan Daftar Game
     */
    public function indexGames(Request $request) {
        $query = Game::query();

        // Fitur Pencarian
        if ($request->has('search')) {
            session(['admin_games_search' => $request->search]);
        }

        // Reset urutan jika diminta
        if ($request->has('reset_sort')) {
            session()->forget('admin_games_sorts');
            return redirect('/admin/games');
        }

        // Logika Sorting Biasa (Single-Column)
        if ($request->has('sort_by')) {
            $column = $request->sort_by;
            $allowed_sorts = ['name', 'platform', 'genre', 'price', 'id'];
            
            if (in_array($column, $allowed_sorts)) {
                $current_col = session('admin_sort_by');
                $current_dir = session('admin_sort_dir', 'desc');
                
                if ($current_col == $column) {
                    $dir = $current_dir == 'asc' ? 'desc' : 'asc';
                } else {
                    $dir = 'asc';
                }
                session(['admin_sort_by' => $column, 'admin_sort_dir' => $dir]);
            }
        }

        $search = session('admin_games_search', '');
        if ($search != '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%");
            });
        }

        // Apply Sorting Tunggal
        $sort_by = session('admin_sort_by', 'id');
        $sort_dir = session('admin_sort_dir', 'desc');

        if (in_array($sort_by, ['name', 'platform', 'genre', 'price', 'id'])) {
            $query->orderBy($sort_by, $sort_dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $games = $query->get();
        return view('admin.games_index', compact('games'));
    }

    /**
     * CRUD GAME: Menghapus Game & Gambar Fisiknya
     */
    public function hapusGame($id) {
        $game = Game::find($id);
        
        if($game) {
            $image_path = public_path('assets/' . $game->image);
            if(File::exists($image_path) && $game->image != null) {
                File::delete($image_path);
            }
            $game->delete();
        }

        return redirect('/admin/games')->with('pesan', 'Game berhasil dihapus!');
    }

    /**
     * CRUD GAME: Form Tambah
     */
    public function tambahGame() {
        return view('admin.games_form', ['game' => null]);
    }

    /**
     * CRUD GAME: Simpan Data Baru
     */
    public function simpanGame(Request $request) {
        $game = new Game();
        $game->name = $request->name;
        $game->platform = $request->platform;
        $game->genre = strtolower($request->genre);
        $game->price = $request->price;
        $game->synopsis = $request->synopsis;
        $game->description = $request->description;
        // Menyimpan 2 Spesifikasi Sistem
        $game->sys_req_min = $request->sys_req_min;
        $game->sys_req_rec = $request->sys_req_rec;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('assets'), $nama_file);
            $game->image = $nama_file;
        }

        $game->save();

        // Simpan Galeri Gambar
        if ($request->hasFile('gallery_images')) {
            $files = $request->file('gallery_images');
            $count = 0;
            foreach ($files as $file) {
                if ($count >= 10) break;
                $nama_file = time() . "_" . uniqid() . "_" . $file->getClientOriginalName();
                $file->move(public_path('assets/galleries'), $nama_file);
                GameGallery::create([
                    'game_id' => $game->id,
                    'type' => 'image',
                    'path' => $nama_file
                ]);
                $count++;
            }
        }

        // Simpan Galeri Video
        if ($request->hasFile('gallery_video_file')) {
            $file = $request->file('gallery_video_file');
            $nama_file = time() . "_video_" . uniqid() . "." . $file->getClientOriginalExtension();
            $file->move(public_path('assets/galleries'), $nama_file);
            
            $videoCuts = json_decode($request->input('video_cuts', '{}'), true);
            if (isset($videoCuts['admin_video'])) {
                $start = $videoCuts['admin_video']['start'] ?? 0;
                $end = $videoCuts['admin_video']['end'] ?? 0;
                $nama_file .= "#t={$start},{$end}";
            }

            GameGallery::create([
                'game_id' => $game->id,
                'type' => 'video',
                'path' => $nama_file
            ]);
        } else if ($request->has('gallery_video') && !empty($request->gallery_video)) {
            $ytUrl = $request->gallery_video;
            $ytStart = $request->input('youtube_start');
            $ytEnd = $request->input('youtube_end');
            
            if ($ytStart !== null || $ytEnd !== null) {
                $separator = str_contains($ytUrl, '?') ? '&' : '?';
                $params = [];
                if ($ytStart !== null && $ytStart !== '') $params[] = "start={$ytStart}";
                if ($ytEnd !== null && $ytEnd !== '') $params[] = "end={$ytEnd}";
                if (count($params) > 0) {
                    $ytUrl .= $separator . implode('&', $params);
                }
            }

            GameGallery::create([
                'game_id' => $game->id,
                'type' => 'video',
                'path' => $ytUrl
            ]);
        }

        return redirect('/admin/games')->with('pesan', 'Game baru berhasil ditambahkan!');
    }

    /**
     * CRUD GAME: Form Edit
     */
    public function editGame($id) {
        $game = Game::find($id);
        return view('admin.games_form', compact('game'));
    }

    /**
     * CRUD GAME: Update Perubahan
     */
    public function updateGame(Request $request, $id) {
        $game = Game::find($id);
        $game->name = $request->name;
        $game->platform = $request->platform;
        $game->genre = strtolower($request->genre);
        $game->price = $request->price;
        $game->synopsis = $request->synopsis;
        $game->description = $request->description;
        // Mengupdate 2 Spesifikasi Sistem
        $game->sys_req_min = $request->sys_req_min;
        $game->sys_req_rec = $request->sys_req_rec;

        if ($request->remove_cover == '1') {
            $image_path = public_path('assets/' . $game->image);
            if(File::exists($image_path) && $game->image != null) {
                File::delete($image_path);
            }
            $game->image = null;
        }

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            $image_path = public_path('assets/' . $game->image);
            if(File::exists($image_path) && $game->image != null) {
                File::delete($image_path);
            }

            $file = $request->file('image');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('assets'), $nama_file);
            $game->image = $nama_file;
        }

        $game->save();

        // Tambah Gambar Galeri Baru (Append, tidak menimpa)
        if ($request->hasFile('gallery_images')) {
            $files = $request->file('gallery_images');
            $count = 0;
            foreach ($files as $file) {
                if ($count >= 10) break;
                $nama_file = time() . "_" . uniqid() . "_" . $file->getClientOriginalName();
                $file->move(public_path('assets/galleries'), $nama_file);
                GameGallery::create([
                    'game_id' => $game->id,
                    'type' => 'image',
                    'path' => $nama_file
                ]);
                $count++;
            }
        }

        // Ganti Gambar Galeri Spesifik
        if ($request->hasFile('replace_gallery')) {
            foreach ($request->file('replace_gallery') as $galleryId => $file) {
                $gallery = GameGallery::find($galleryId);
                if ($gallery && $gallery->game_id == $game->id) {
                    // Hapus file fisik lama
                    $oldPath = public_path('assets/galleries/' . $gallery->path);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                    
                    // Simpan file baru
                    $nama_file = time() . "_" . uniqid() . "_" . $file->getClientOriginalName();
                    $file->move(public_path('assets/galleries'), $nama_file);
                    $gallery->path = $nama_file;
                    $gallery->save();
                }
            }
        }

        // Update Galeri Video
        if ($request->hasFile('gallery_video_file')) {
            $oldVideo = GameGallery::where('game_id', $game->id)->where('type', 'video')->first();
            if ($oldVideo && !str_contains($oldVideo->path, 'youtube.com') && !str_contains($oldVideo->path, 'youtu.be')) {
                $oldPath = public_path('assets/galleries/' . preg_replace('/#.*$/', '', $oldVideo->path));
                if (File::exists($oldPath)) { File::delete($oldPath); }
            }
            GameGallery::where('game_id', $game->id)->where('type', 'video')->delete();
            
            $file = $request->file('gallery_video_file');
            $nama_file = time() . "_video_" . uniqid() . "." . $file->getClientOriginalExtension();
            $file->move(public_path('assets/galleries'), $nama_file);
            
            $videoCuts = json_decode($request->input('video_cuts', '{}'), true);
            if (isset($videoCuts['admin_video'])) {
                $start = $videoCuts['admin_video']['start'] ?? 0;
                $end = $videoCuts['admin_video']['end'] ?? 0;
                $nama_file .= "#t={$start},{$end}";
            }

            GameGallery::create([
                'game_id' => $game->id,
                'type' => 'video',
                'path' => $nama_file
            ]);
        } else if ($request->has('gallery_video') && !empty($request->gallery_video)) {
            $oldVideo = GameGallery::where('game_id', $game->id)->where('type', 'video')->first();
            if ($oldVideo && !str_contains($oldVideo->path, 'youtube.com') && !str_contains($oldVideo->path, 'youtu.be') && $oldVideo->path !== $request->gallery_video) {
                $oldPath = public_path('assets/galleries/' . preg_replace('/#.*$/', '', $oldVideo->path));
                if (File::exists($oldPath)) { File::delete($oldPath); }
            }
            GameGallery::where('game_id', $game->id)->where('type', 'video')->delete();
            
            $ytUrl = $request->gallery_video;
            $ytStart = $request->input('youtube_start');
            $ytEnd = $request->input('youtube_end');
            
            if ($ytStart !== null || $ytEnd !== null) {
                $separator = str_contains($ytUrl, '?') ? '&' : '?';
                $params = [];
                if ($ytStart !== null && $ytStart !== '') $params[] = "start={$ytStart}";
                if ($ytEnd !== null && $ytEnd !== '') $params[] = "end={$ytEnd}";
                if (count($params) > 0) {
                    $ytUrl .= $separator . implode('&', $params);
                }
            }

            GameGallery::create([
                'game_id' => $game->id,
                'type' => 'video',
                'path' => $ytUrl
            ]);
        } else if ($request->has('remove_video') && $request->remove_video == '1') {
            $oldVideo = GameGallery::where('game_id', $game->id)->where('type', 'video')->first();
            if ($oldVideo && !str_contains($oldVideo->path, 'youtube.com') && !str_contains($oldVideo->path, 'youtu.be')) {
                $oldPath = public_path('assets/galleries/' . $oldVideo->path);
                if (File::exists($oldPath)) { File::delete($oldPath); }
            }
            GameGallery::where('game_id', $game->id)->where('type', 'video')->delete();
        } else if ($request->has('gallery_video') && empty($request->gallery_video) && !$request->hasFile('gallery_video_file')) {
            $oldVideo = GameGallery::where('game_id', $game->id)->where('type', 'video')->first();
            if ($oldVideo && (str_contains($oldVideo->path, 'youtube.com') || str_contains($oldVideo->path, 'youtu.be'))) {
                $oldVideo->delete();
            }
        }

        return redirect('/admin/games')->with('pesan', 'Data game berhasil diperbarui!');
    }

    /**
     * CRUD GAME: Hapus Galeri Spesifik
     */
    public function hapusGaleri(Request $request, $id) {
        $gallery = GameGallery::find($id);
        if ($gallery) {
            $path = public_path('assets/galleries/' . $gallery->path);
            if (File::exists($path)) {
                File::delete($path);
            }
            $game_id = $gallery->game_id;
            $gallery->delete();

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['status' => 'success', 'message' => 'Gambar galeri berhasil dihapus!']);
            }
            return redirect('/admin/games/edit/' . $game_id)->with('pesan', 'Gambar galeri berhasil dihapus!');
        }
        
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['status' => 'error', 'message' => 'Galeri tidak ditemukan.']);
        }
        return back();
    }

    /**
     * TRANSAKSI: Menampilkan Riwayat Transaksi
     */
    public function indexTransaksi() {
        $transaksi = Transaksi::join('tb_users', 'tb_transaksi.user_id', '=', 'tb_users.id')
            ->select('tb_transaksi.*', 'tb_users.name as nama_pembeli', 'tb_users.username')
            ->orderBy('tb_transaksi.id', 'desc')
            ->get();
            
        return view('admin.transaksi_index', compact('transaksi'));
    }

    /**
     * LAPORAN: Mencetak Laporan PDF
     */
    public function cetakLaporan() {
        $transaksi = Transaksi::join('tb_users', 'tb_transaksi.user_id', '=', 'tb_users.id')
            ->select('tb_transaksi.*', 'tb_users.name as nama_pembeli', 'tb_users.username')
            ->orderBy('tb_transaksi.id', 'desc')
            ->get();
            
        // Hitung total hanya yang statusnya sukses
        $total_pendapatan = $transaksi->where('status', 'Success')->sum('total_bayar'); 

        $pdf = Pdf::loadView('admin.laporan_pdf', compact('transaksi', 'total_pendapatan'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Penjualan-GameVault.pdf');
    }

    /**
     * Manajemen Pengguna
     */
    public function indexUsers(Request $request) {
        $query = User::where('role', 'user')->with([
            'wishlists.game', 
            'keranjangs.game', 
            'transaksis' => function($q) {
                $q->where('status', 'Success')->with('details.game');
            }
        ])->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10);
        return view('admin.users_index', compact('users'));
    }
}
