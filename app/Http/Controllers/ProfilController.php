<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File; // Pastikan ini ada untuk fitur simpan foto
use Illuminate\Support\Facades\DB;
class ProfilController extends Controller
{
    public function index()
    {
        $total_owned = \App\Models\Transaksi::where('user_id', auth()->user()->id)
                            ->where('status', 'Success')
                            ->count();

        return view('profil', compact('total_owned'));
    }

    // Fungsi untuk memproses update Nama, Foto, & Banner Profil
    public function update(\Illuminate\Http\Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name'          => 'nullable|string|max:255',
            'cropped_photo' => 'nullable|string', // Base64 dari Cropper.js
            'banner'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Maks 5MB untuk banner
        ]);

        // Siapkan array data yang mau diupdate
        $dataUpdate = [
            'name' => $request->name,
        ];

        // 2. Cek apakah user mengirimkan foto base64 baru hasil crop
        if ($request->filled('cropped_photo')) {
            $base64_image = $request->cropped_photo;

            // Pastikan format base64 valid
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                $data = substr($base64_image, strpos($base64_image, ',') + 1);
                $data = base64_decode($data);
                
                // Buat nama file unik
                $nama_file = time() . "_" . auth()->user()->id . "_profile.jpg";
                
                // Tentukan folder tujuan (public/assets/profile)
                $tujuan_upload = public_path('assets/profile');
                
                // Buat folder jika belum ada
                if (!File::exists($tujuan_upload)) {
                    File::makeDirectory($tujuan_upload, 0755, true, true);
                }
                
                // Simpan file foto dari data base64 ke folder tujuan
                file_put_contents($tujuan_upload . '/' . $nama_file, $data);

                // Masukkan nama file baru ke dalam array dataUpdate
                $dataUpdate['foto'] = $nama_file;
            }
        }

        // 3. Cek apakah user mengirimkan banner base64 baru hasil crop
        if ($request->filled('cropped_banner')) {
            $base64_banner = $request->cropped_banner;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64_banner)) {
                $data_banner = substr($base64_banner, strpos($base64_banner, ',') + 1);
                $data_banner = base64_decode($data_banner);
                
                $banner_nama = time() . "_" . auth()->user()->id . "_banner.jpg";
                $tujuan_upload = public_path('assets/profile');
                
                if (!File::exists($tujuan_upload)) {
                    File::makeDirectory($tujuan_upload, 0755, true, true);
                }
                
                file_put_contents($tujuan_upload . '/' . $banner_nama, $data_banner);
                $dataUpdate['banner'] = $banner_nama;
            }
        }

        // 3. PROSES UPLOAD GAMBAR BANNER
        if ($request->hasFile('banner')) {
            $banner_file = $request->file('banner');
            $banner_nama = time() . "_" . auth()->user()->id . "_banner." . $banner_file->getClientOriginalExtension();
            $tujuan_upload = public_path('assets/profile');
            
            if (!File::exists($tujuan_upload)) {
                File::makeDirectory($tujuan_upload, 0755, true, true);
            }
            
            $banner_file->move($tujuan_upload, $banner_nama);
            $dataUpdate['banner'] = $banner_nama;
        }

        // 3. Update data ke tabel tb_users berdasarkan ID user yang login
        DB::table('tb_users')
            ->where('id', auth()->user()->id)
            ->update($dataUpdate);

        // 4. Kembalikan ke halaman profil dengan pesan sukses
        return redirect('/profil')->with('status', 'success')->with('msg', 'Profil berhasil diperbarui!');
    }


    public function library()
    {
        // Tarik data game + tanggal belinya dari relasi transaksi
        $games = DB::table('tb_detail_transaksi')
                    ->join('tb_transaksi', 'tb_detail_transaksi.transaksi_id', '=', 'tb_transaksi.id')
                    ->join('tb_games', 'tb_detail_transaksi.game_id', '=', 'tb_games.id')
                    ->where('tb_transaksi.user_id', auth()->user()->id)
                    ->where('tb_transaksi.status', 'Success')
                    ->select('tb_games.*', 'tb_transaksi.created_at as tgl_beli')
                    ->orderBy('tb_transaksi.created_at', 'desc') // Urutkan dari yang terbaru dibeli
                    ->get();

        $reviews = \App\Models\Review::where('user_id', auth()->user()->id)->get()->keyBy('game_id');

        return view('library', compact('games', 'reviews'));
    }

    public function orders()
    {
        // 1. Tarik semua transaksi milik user yang sedang login
        $transaksi = \App\Models\Transaksi::where('user_id', auth()->user()->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // 2. LOOPING: Cari gambar dan nama game untuk masing-masing transaksi
        foreach($transaksi as $t) {
            $gamesList = DB::table('tb_detail_transaksi')
                        ->join('tb_games', 'tb_detail_transaksi.game_id', '=', 'tb_games.id')
                        ->where('tb_detail_transaksi.transaksi_id', $t->id)
                        ->select('tb_games.name', 'tb_games.image')
                        ->get();

            if ($gamesList->count() > 0) {
                $t->game_image = $gamesList->first()->image;
                if ($gamesList->count() > 1) {
                    $t->game_name = $gamesList->first()->name . ' (+ ' . ($gamesList->count() - 1) . ' item lainnya)';
                } else {
                    $t->game_name = $gamesList->first()->name;
                }
            } else {
                $t->game_name = 'Pembelian Game';
                $t->game_image = 'no-image.jpg';
            }
        }

        // 3. Hitung statistik asli berdasarkan database
        $total_transaksi = $transaksi->count();
        $total_pengeluaran = $transaksi->where('status', 'Success')->sum('total_bayar');
        
        $total_game = DB::table('tb_detail_transaksi')
                        ->join('tb_transaksi', 'tb_detail_transaksi.transaksi_id', '=', 'tb_transaksi.id')
                        ->where('tb_transaksi.user_id', auth()->user()->id)
                        ->where('tb_transaksi.status', 'Success')
                        ->count();

        // 4. Kirim ke view
        return view('riwayat', compact('transaksi', 'total_transaksi', 'total_pengeluaran', 'total_game'));
    }

    // 1. Menampilkan Halaman Form Input Email Baru
    public function showGantiEmailForm()
    {
        return view('ganti_email');
    }

    // 2. Membuat OTP & Mengirimkan Link/Kode ke Mailtrap
    public function kirimOtpEmail(Request $request)
    {
        $request->validate([
            'email_baru' => 'required|email'
        ]);

        $emailBaru = trim($request->email_baru);

        // Cek apakah email sudah dipakai oleh orang lain di tabel tb_users
        $cekEmail = DB::table('tb_users')->where('email', $emailBaru)->first();
        if ($cekEmail) {
            return back()->with('status', 'error')->with('msg', 'Email tersebut sudah terdaftar di akun lain!');
        }

        // Generate 6 Digit Kode OTP Acak
        $otp = rand(100000, 999999);

        // Simpan email baru dan OTP ke dalam Session sementara
        session([
            'pending_email' => $emailBaru,
            'email_otp'     => $otp
        ]);

        // Siapkan Template Email untuk Mailtrap
        $body = "
            <div style='background-color: #0A0C10; color: white; padding: 30px; font-family: sans-serif; border-radius: 15px; max-width: 450px; margin: auto;'>
                <h2 style='color: #7C3AED; text-align: center; tracking-widest: 2px;'>GAMEVAULT 🎮</h2>
                <hr style='border-color: rgba(255,255,255,0.1);'>
                <p>Halo, <strong>" . auth()->user()->username . "</strong>!</p>
                <p>Kami menerima permintaan perubahan alamat email akun GameVault kamu ke: <span style='color: #a78bfa; font-weight: bold;'>" . $emailBaru . "</span></p>
                <p>Berikut adalah 6-digit kode verifikasi OTP kamu:</p>
                <div style='background-color: #12151C; border: 1px solid #7C3AED; padding: 15px; border-radius: 10px; text-align: center; margin: 20px 0;'>
                    <span style='font-size: 24px; font-family: monospace; font-weight: bold; color: #FDE047; letter-spacing: 5px;'>" . $otp . "</span>
                </div>
                <p style='font-size: 11px; color: #666;'>Jika kamu tidak merasa melakukan perubahan ini, abaikan email ini keamanan akunmu tetap terjaga.</p>
            </div>
        ";

        // Kirim email menggunakan Mailtrap (Konfigurasi otomatis mengikuti .env kamu)
        \Illuminate\Support\Facades\Mail::html($body, function($message) use ($emailBaru) {
            $message->to($emailBaru)
                    ->subject('Kode Verifikasi Ganti Email - GameVault');
        });

        // Alihkan ke halaman input kode verifikasi OTP
        return redirect('/profil/ganti-email/verifikasi')->with('status', 'success')->with('msg', 'Kode OTP telah dikirimkan ke email baru Anda! Silakan cek Mailtrap.');
    }

    // 3. Menampilkan Halaman Input OTP
    public function showVerifikasiOtpForm()
    {
        // Proteksi jika user mencoba masuk langsung tanpa input email baru
        if (!session()->has('pending_email')) {
            return redirect('/profil');
        }
        return view('verifikasi_email');
    }

    // 4. Memproses Pencocokan OTP & Update Email di Database
    public function prosesGantiEmail(Request $request)
    {
        $request->validate([
            'otp_input' => 'required|numeric'
        ]);

        $otpSession = session('email_otp');
        $emailBaru = session('pending_email');

        // Cocokkan OTP inputan user dengan OTP di session
        if ($request->otp_input == $otpSession) {
            
            // Update email baru ke dalam database tb_users
            DB::table('tb_users')
                ->where('id', auth()->user()->id)
                ->update(['email' => $emailBaru]);

            // Bersihkan data session ganti email
            session()->forget(['pending_email', 'email_otp']);

            return redirect('/profil')->with('status', 'success')->with('msg', 'Email akun berhasil diperbarui!');
        }

        return back()->with('status', 'error')->with('msg', 'Kode OTP yang Anda masukkan salah atau tidak valid!');
    }

    // ngecek status via ajax polling
    public function checkStatuses(Request $request) {
        $ids = $request->ids;
        if(!$ids) return response()->json([]);

        // Trik khusus untuk Localhost: Jika status masih Pending, kita tanya langsung ke server Midtrans!
        $pending_ids = \App\Models\Transaksi::whereIn('id', $ids)->where('status', 'Pending')->pluck('id');
        if (count($pending_ids) > 0) {
            \Midtrans\Config::$serverKey = 'Mid-server-tADVX9-15wfmU2wUv60szXql';
            \Midtrans\Config::$isProduction = false;
            
            foreach ($pending_ids as $id) {
                try {
                    // Tanya status asli ke Midtrans
                    $statusResponse = \Midtrans\Transaction::status($id);
                    
                    // Kalau di Midtrans sudah bukan pending, kita simulasikan notifikasi
                    if (isset($statusResponse->transaction_status) && $statusResponse->transaction_status != 'pending') {
                        $fakeRequest = new Request();
                        $fakeRequest->initialize([], [], [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($statusResponse));
                        $checkoutCtrl = new \App\Http\Controllers\CheckoutController();
                        $checkoutCtrl->notification($fakeRequest);
                    }
                } catch (\Exception $e) {
                    // Abaikan jika order tidak ditemukan di Midtrans
                }
            }
        }

        $statuses = \App\Models\Transaksi::whereIn('id', $ids)->pluck('status', 'id');
        return response()->json($statuses);
    }

    // 5. Menampilkan Detail Transaksi (API / AJAX)
    public function getTransactionDetail($id)
    {
        $trx = \App\Models\Transaksi::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$trx) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $details = DB::table('tb_detail_transaksi')
            ->join('tb_games', 'tb_detail_transaksi.game_id', '=', 'tb_games.id')
            ->where('tb_detail_transaksi.transaksi_id', $id)
            ->select('tb_games.name', 'tb_games.image', 'tb_detail_transaksi.harga_saat_beli')
            ->get();

        return response()->json([
            'status' => 'success',
            'transaksi' => [
                'id' => $trx->id,
                'created_at' => $trx->created_at->format('d M Y, H:i'),
                'status' => $trx->status,
                'total_bayar' => $trx->total_bayar,
            ],
            'items' => $details
        ]);
    }

    // 6. Membatalkan pesanan yang masih pending
    public function cancelOrder($id)
    {
        $trx = \App\Models\Transaksi::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->where('status', 'Pending')
            ->first();

        if ($trx) {
            $trx->status = 'Failed'; // Atau 'Cancelled' sesuai standar statusmu
            $trx->save();
            return redirect('/orders')->with('status', 'success')->with('msg', 'Pesanan berhasil dibatalkan.');
        }

        return redirect('/orders')->with('status', 'error')->with('msg', 'Pesanan tidak ditemukan atau sudah tidak bisa dibatalkan.');
    }

    // 7. Membatalkan otomatis pesanan duplikat saat user lanjut bayar
    public function cancelDuplicates($id)
    {
        $trx = \App\Models\Transaksi::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
            
        if (!$trx) return response()->json(['status' => 'error']);

        $gameIds = \App\Models\DetailTransaksi::where('transaksi_id', $trx->id)->pluck('game_id')->toArray();

        if (!empty($gameIds)) {
            $otherTrxIds = \App\Models\DetailTransaksi::whereIn('game_id', $gameIds)
                ->where('transaksi_id', '!=', $trx->id)
                ->whereHas('transaksi', function($q) {
                    $q->where('user_id', auth()->id())->where('status', 'Pending');
                })
                ->pluck('transaksi_id')
                ->toArray();

            if (!empty($otherTrxIds)) {
                \App\Models\Transaksi::whereIn('id', $otherTrxIds)->update(['status' => 'Failed']);
            }
        }

        return response()->json(['status' => 'success', 'canceled_ids' => $otherTrxIds ?? []]);
    }
}