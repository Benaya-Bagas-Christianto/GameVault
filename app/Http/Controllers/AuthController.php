<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $input = $request->username_email;
        $password = $request->password;

        $user = User::where('email', $input)->orWhere('username', $input)->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            return redirect('/')->with('status', 'success')->with('msg', 'Selamat datang!');
        }
        return back()->with('status', 'error')->with('msg', 'Username/Password salah!');
    }

    public function register(Request $request) {
        $errors = [];

        if ($request->password !== $request->confirm_password) {
            $errors[] = 'Konfirmasi password tidak cocok!';
        }

        if ($request->pin !== $request->pin_confirmation) {
            $errors[] = 'Konfirmasi PIN tidak cocok!';
        }

        if (strlen($request->pin) !== 6 || !is_numeric($request->pin)) {
            $errors[] = 'PIN harus berupa 6 digit angka!';
        }

        if (!preg_match('/[A-Z]/', $request->password) || 
            !preg_match('/[a-z]/', $request->password) || 
            !preg_match('/[0-9]/', $request->password) || 
            !preg_match('/[\W_]/', $request->password)) {
            $errors[] = 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol!';
        }

        $existing = User::where('username', $request->username)->orWhere('email', $request->email)->first();
        if ($existing) {
            $errors[] = 'Username atau Email sudah terdaftar!';
        }

        if (count($errors) > 0) {
            return back()->with('status', 'error')->withErrors($errors)->withInput($request->all());
        }

        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'pin'      => Hash::make($request->pin),
        ]);

        Auth::login($user);

        return redirect('/')->with('status', 'success')->with('msg', 'Pendaftaran berhasil! Selamat datang.');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'success')->with('msg', 'Anda berhasil logout!');
    }

    // Fungsi untuk memproses Lupa Password
    public function forgotPassword(\Illuminate\Http\Request $request) {
        // 1. Cek apakah email ada di database
        $user = \App\Models\User::where('email', $request->email)->first();

        if(!$user) {
            return back()->with('msg', 'Email tidak terdaftar di GameVault!')->with('status', 'error');
        }

        // 2. Buat token acak rahasia (64 karakter)
        $token = \Illuminate\Support\Str::random(64);

        // 3. Simpan token ke tabel password_reset_tokens bawaan Laravel
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => \Carbon\Carbon::now()]
        );

        // 4. Siapkan isi Email & Link Reset
        $action_link = url('/reset-password/' . $token . '?email=' . urlencode($request->email));
        $logoPath = public_path('assets/Logo Game Vault 1.png');

        // 5. Kirim Email pakai Mailtrap
        \Illuminate\Support\Facades\Mail::send([], [], function($message) use ($request, $user, $action_link, $logoPath) {
            // Embed logo agar tampil di email client
            $logoCid = file_exists($logoPath) ? $message->embed($logoPath) : '';
            
            $body = "
                <div style='background-color: #0A0C10; color: white; padding: 30px; font-family: sans-serif; border-radius: 15px; max-width: 450px; margin: auto; border: 1px solid #1f1f1f;'>
                    <div style='text-align: center; margin-bottom: 15px;'>
                        " . ($logoCid ? "<img src='{$logoCid}' alt='Logo' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 10px;'>" : "") . "
                        <h2 style='color: #7C3AED; display: inline-block; vertical-align: middle; letter-spacing: 2px; margin: 0;'>GAMEVAULT</h2>
                    </div>
                    <hr style='border: none; border-top: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px;'>
                    <p>Halo <strong>{$user->username}</strong>,</p>
                    <p style='color: #ccc; line-height: 1.5;'>Kami menerima permintaan untuk mengubah kata sandi akun GameVault kamu. Silakan klik tombol di bawah ini untuk membuat password baru:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$action_link}' style='background-color: #7C3AED; color: white; padding: 14px 30px; text-decoration: none; border-radius: 12px; font-weight: bold; display: inline-block; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(124,58,237,0.3);'>Ubah Password</a>
                    </div>
                    <p style='font-size: 11px; color: #666; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; line-height: 1.6;'>Jika kamu tidak pernah meminta perubahan password, abaikan saja email ini. Keamanan akunmu tetap terjaga.</p>
                </div>
            ";

            $message->to($request->email)
                    ->subject('Reset Password - GameVault')
                    ->html($body);
        });

        // 6. Kembalikan ke halaman sebelumnya dengan pesan sukses
        return back()->with('msg', 'Link reset password telah dikirim ke emailmu! Silakan cek Kotak Masuk.')->with('status', 'success');
    }

    // Menampilkan halaman form ganti password
    public function showResetForm(Request $request, $token) {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // Memproses update password baru ke database
    public function updatePassword(Request $request) {
        // 1. Validasi input
        if ($request->password !== $request->confirm_password) {
            return back()->with('msg', 'Konfirmasi password tidak cocok!')->with('status', 'error');
        }

        if (!preg_match('/[A-Z]/', $request->password) || 
            !preg_match('/[a-z]/', $request->password) || 
            !preg_match('/[0-9]/', $request->password) || 
            !preg_match('/[\W_]/', $request->password)) {
            return back()->with('status', 'error')->with('msg', 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol!');
        }

        // 2. Cek apakah token & email valid di tabel password_reset_tokens
        $check = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->token)
                    ->first();

        if(!$check) {
            return redirect('/')->with('msg', 'Link kadaluarsa atau tidak valid!')->with('status', 'error');
        }

        // 3. Update password user di tabel tb_users
        \App\Models\User::where('email', $request->email)->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        // 4. Hapus token agar tidak bisa dipakai lagi
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/')->with('msg', 'Password berhasil diganti! Silakan login.')->with('status', 'success');
    }
}