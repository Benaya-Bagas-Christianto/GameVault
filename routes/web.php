<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController, AuthController, CartController,
    WishlistController, CheckoutController,
    OrderController, ProfilController, SearchController,
    AdminController, InvoiceController
};

// Halaman Utama dan Pencarian
Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [SearchController::class, 'index']);
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete']);
Route::get('/library', [App\Http\Controllers\ProfilController::class, 'library'])->middleware('auth');

// =========================================================================
// RUTE KATEGORI (MENDUKUNG KOMBINASI BANYAK FILTER / MULTI-SELECT)
// =========================================================================
Route::get('/kategori', function (\Illuminate\Http\Request $request) {
    if ($request->has('reset')) {
        session()->forget(['kategori_genre', 'kategori_harga', 'kategori_rating', 'kategori_rilis', 'kategori_platform', 'kategori_seed']);
        return redirect('/kategori');
    }

    if ($request->has('filter')) {
        session([
            'kategori_genre' => $request->query('genre', ''),
            'kategori_harga' => $request->query('harga', ''),
            'kategori_rating' => $request->query('rating', ''),
            'kategori_rilis' => $request->query('rilis', ''),
            'kategori_platform' => $request->query('platform', ''),
        ]);
        $gq = $request->query('genre', '');
        $hq = $request->query('harga', '');
        $rq = $request->query('rating', '');
        $rlq = $request->query('rilis', '');
        $pq = $request->query('platform', '');
    } else {
        $gq = session('kategori_genre', '');
        $hq = session('kategori_harga', '');
        $rq = session('kategori_rating', '');
        $rlq = session('kategori_rilis', '');
        $pq = session('kategori_platform', '');
        
        $hasKeepSeed = isset($_COOKIE['keep_seed']);
        if (!$hasKeepSeed) {
            // Acak game lagi setiap kali buka menu kategori (bukan dari filter/pagination)
            session(['kategori_seed' => rand(1000, 9999)]);
        }
    }

    if (!session()->has('kategori_seed')) {
        session(['kategori_seed' => rand(1000, 9999)]);
    }
    $seed = session('kategori_seed');
    
    if (isset($_COOKIE['keep_seed'])) {
        setcookie('keep_seed', '', time() - 3600, '/');
        unset($_COOKIE['keep_seed']);
    }

    // 1. Tangkap parameter URL, pisahkan dengan koma menjadi Array
    $genreDipilih = $gq ? explode(',', $gq) : [];
    $hargaDipilih = $hq ? explode(',', $hq) : [];
    $ratingDipilih = $rq ? explode(',', $rq) : [];
    $rilisDipilih = $rlq ? explode(',', $rlq) : [];
    $platformDipilih = $pq ? explode(',', $pq) : [];
    
    $query = \App\Models\Game::with('reviews');
    
    // 2. Logika Filter Genre (Kombinasi OR)
    if (!empty($genreDipilih)) {
        $query->where(function($q) use ($genreDipilih) {
            foreach ($genreDipilih as $g) {
                $q->orWhere('genre', 'like', "%$g%");
            }
        });
    }

    // 2.5 Logika Filter Platform (Kombinasi OR)
    if (!empty($platformDipilih)) {
        $query->where(function($q) use ($platformDipilih) {
            foreach ($platformDipilih as $p) {
                $q->orWhere('platform', 'like', "%$p%");
            }
        });
    }
    
    // 3. Logika Filter Harga (Kombinasi OR)
    if (!empty($hargaDipilih)) {
        $query->where(function($q) use ($hargaDipilih) {
            foreach ($hargaDipilih as $h) {
                if ($h == 'gratis') { $q->orWhere('price', 0); }
                elseif ($h == '<100') { $q->orWhere(function($sub) { $sub->where('price', '>', 0)->where('price', '<', 100000); }); }
                elseif ($h == '100-250') { $q->orWhereBetween('price', [100000, 250000]); }
                elseif ($h == '250-500') { $q->orWhereBetween('price', [250000, 500000]); }
                elseif ($h == '500-750') { $q->orWhereBetween('price', [500000, 750000]); }
                elseif ($h == '>750') { $q->orWhere('price', '>', 750000); }
            }
        });
    }

    // 4. Logika Filter Rilis (Kombinasi OR)
    if (!empty($rilisDipilih)) {
        $query->where(function($q) use ($rilisDipilih) {
            foreach ($rilisDipilih as $r) {
                if ($r == '1bulan') { $q->orWhere('created_at', '>=', now()->subMonth()); }
                elseif ($r == '3bulan') { $q->orWhere('created_at', '>=', now()->subMonths(3)); }
                elseif ($r == '6bulan') { $q->orWhere('created_at', '>=', now()->subMonths(6)); }
                elseif ($r == 'tahun_ini') { $q->orWhereYear('created_at', date('Y')); }
            }
        });
        if (in_array('terbaru', $rilisDipilih)) {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderByRaw("RAND($seed)");
        }
    } else {
        $query->orderByRaw("RAND($seed)");
    }

    // 5. Logika Filter Rating (BARU DITAMBAHKAN)
    if (!empty($ratingDipilih)) {
        // Ambil angka rating terendah dari yang dipilih
        $minRating = min(array_map('floatval', $ratingDipilih)); 
        
        $query->whereHas('reviews') 
              ->withAvg('reviews', 'rating') 
              ->having('reviews_avg_rating', '>=', $minRating); 
    }
    
    $games = $query->paginate(12)->appends([
        'genre' => $gq,
        'harga' => $hq,
        'rating' => $rq,
        'rilis' => $rlq,
        'platform' => $pq,
        'filter' => 1
    ]);
    // Kirim data Array ke tampilan Blade
    return view('kategori', compact('games', 'genreDipilih', 'hargaDipilih', 'ratingDipilih', 'rilisDipilih', 'platformDipilih'));
});

// =========================================================================
// RUTE LOGIN & REGISTER NORMAL
// =========================================================================
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// =========================================================================
// FIX: RUTE FORGOT PASSWORD (MENAMBAHKAN GET AGAR TIDAK ERROR METHOD)
// =========================================================================
Route::get('/forgot-password', function () {
    if (view()->exists('auth.forgot-password')) {
        return view('auth.forgot-password');
    } elseif (view()->exists('forgot-password')) {
        return view('forgot-password');
    }
    // Jika file blade forgot-password belum kamu buat, munculkan teks pengaman ini
    return '<body style="background-color:#0A0C10; color:white; text-align:center; padding-top:100px; font-family:sans-serif;"><h2>Halaman Lupa Password Belum Dibuat</h2><a href="/login" style="color:#8B5CF6;">Kembali ke Login</a></body>';
})->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

// Rute ini HARUS DI LUAR middleware auth, karena Midtrans tidak login ke web kita!
Route::post('/checkout/notification', [App\Http\Controllers\CheckoutController::class, 'notification']);
// Kelompok Rute Fitur yang Wajib Login (Authenticated Users)
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/profil', [ProfilController::class, 'index']);
    Route::post('/profil', [ProfilController::class, 'update']);
    Route::post('/profil/update', [ProfilController::class, 'update']);

    Route::post('/cart_process', [CartController::class, 'add']);
    Route::post('/cart/get', [CartController::class, 'get']);
    Route::post('/cart/remove', [CartController::class, 'remove']);

    Route::post('/wishlist_process', [WishlistController::class, 'toggle']);
    Route::get('/get_wishlist', [WishlistController::class, 'get']);

    // Rute Checkout (POST)
    Route::post('/checkout', [CheckoutController::class, 'process']);
    
    // Rute Penyelamat (GET) jika user tidak sengaja klik tombol Back di browser saat bayar
    Route::get('/checkout', function () {
        return redirect('/')->with('msg', 'Silakan proses checkout melalui keranjang belanja Anda.');
    });
    
    Route::post('/checkout/success', [CheckoutController::class, 'success']);
    Route::post('/checkout/cancel-if-unpaid', [CheckoutController::class, 'cancelIfUnpaid']); 
    Route::post('/checkout/mark-pending', [CheckoutController::class, 'markPending']);
    Route::get('/orders', [ProfilController::class, 'orders']);
    Route::post('/orders/check-statuses', [ProfilController::class, 'checkStatuses']);
    Route::get('/orders/{id}/detail', [ProfilController::class, 'getTransactionDetail']);
    Route::post('/orders/{id}/cancel', [ProfilController::class, 'cancelOrder']);
    Route::post('/orders/{id}/cancel-duplicates', [ProfilController::class, 'cancelDuplicates']);
    Route::post('/review/simpan', [OrderController::class, 'simpanReview']); 
    Route::post('/review/delete', [OrderController::class, 'hapusReview']); 
    
    Route::get('/invoice/download/{id}', [InvoiceController::class, 'download']);
});

// Route untuk Fitur Ganti Email Akun
Route::get('/profil/ganti-email', [App\Http\Controllers\ProfilController::class, 'showGantiEmailForm'])->middleware('auth');
Route::post('/profil/ganti-email/kirim', [App\Http\Controllers\ProfilController::class, 'kirimOtpEmail'])->middleware('auth');
Route::get('/profil/ganti-email/verifikasi', [App\Http\Controllers\ProfilController::class, 'showVerifikasiOtpForm'])->middleware('auth');
Route::post('/profil/ganti-email/proses', [App\Http\Controllers\ProfilController::class, 'prosesGantiEmail'])->middleware('auth');

// Kelompok Rute Khusus Admin Panel
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/games', [AdminController::class, 'indexGames']); 
    Route::get('/games/hapus/{id}', [AdminController::class, 'hapusGame']); 
    Route::get('/games/hapus-galeri/{id}', [AdminController::class, 'hapusGaleri']); 
    Route::get('/games/tambah', [AdminController::class, 'tambahGame']); 
    Route::post('/games/simpan', [AdminController::class, 'simpanGame']); 
    Route::get('/games/edit/{id}', [AdminController::class, 'editGame']); 
    Route::post('/games/update/{id}', [AdminController::class, 'updateGame']); 
    Route::get('/transaksi', [AdminController::class, 'indexTransaksi']); 
    Route::get('/transaksi/cetak', [AdminController::class, 'cetakLaporan']); 
    Route::get('/users', [AdminController::class, 'indexUsers']); 
});

// Halaman dedicated untuk fitur Keranjang Belanja Baru
Route::get('/cart', function () {
    $hasKeepSeed = isset($_COOKIE['keep_seed']);
    $seed = session('game_seed_cart');
    if (!$hasKeepSeed || !$seed) {
        $seed = rand(1, 999999);
        session(['game_seed_cart' => $seed]);
    }
    
    if ($hasKeepSeed) {
        setcookie('keep_seed', '', time() - 3600, '/');
        unset($_COOKIE['keep_seed']);
    }

    $games = \App\Models\Game::with('reviews')->inRandomOrder($seed)->get();
    return view('cart', compact('games'));
});

// Halaman dedicated untuk fitur Wishlist
Route::get('/wishlist', function () {
    $games = \App\Models\Game::with('reviews')->get();
    return view('wishlist', compact('games'));
});

Route::get('/game/{id}', function ($id) {
    $game = \App\Models\Game::with('reviews.user')->findOrFail($id);
    return view('detail', compact('game'));
});

// Route untuk Fitur Ganti Email Akun
Route::get('/profil/ganti-email', [App\Http\Controllers\ProfilController::class, 'showGantiEmailForm'])->middleware('auth');
Route::post('/profil/ganti-email/kirim', [App\Http\Controllers\ProfilController::class, 'kirimOtpEmail'])->middleware('auth');
Route::get('/profil/ganti-email/verifikasi', [App\Http\Controllers\ProfilController::class, 'showVerifikasiOtpForm'])->middleware('auth');
Route::post('/profil/ganti-email/proses', [App\Http\Controllers\ProfilController::class, 'prosesGantiEmail'])->middleware('auth');

// Kelompok Rute Khusus Admin Panel
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/games', [AdminController::class, 'indexGames']); 
    Route::get('/games/hapus/{id}', [AdminController::class, 'hapusGame']); 
    Route::get('/games/hapus-galeri/{id}', [AdminController::class, 'hapusGaleri']); 
    Route::get('/games/tambah', [AdminController::class, 'tambahGame']); 
    Route::post('/games/simpan', [AdminController::class, 'simpanGame']); 
    Route::get('/games/edit/{id}', [AdminController::class, 'editGame']); 
    Route::post('/games/update/{id}', [AdminController::class, 'updateGame']); 
    Route::get('/transaksi', [AdminController::class, 'indexTransaksi']); 
    Route::get('/transaksi/cetak', [AdminController::class, 'cetakLaporan']); 
    Route::get('/users', [AdminController::class, 'indexUsers']); 
});

// Halaman dedicated untuk fitur Keranjang Belanja Baru
Route::get('/cart', function () {
    $hasKeepSeed = isset($_COOKIE['keep_seed']);
    $seed = session('game_seed_cart');
    if (!$hasKeepSeed || !$seed) {
        $seed = rand(1, 999999);
        session(['game_seed_cart' => $seed]);
    }
    
    if ($hasKeepSeed) {
        setcookie('keep_seed', '', time() - 3600, '/');
        unset($_COOKIE['keep_seed']);
    }

    $games = \App\Models\Game::with('reviews')->inRandomOrder($seed)->get();
    return view('cart', compact('games'));
});

// Halaman dedicated untuk fitur Wishlist
Route::get('/wishlist', function () {
    $games = \App\Models\Game::with('reviews')->get();
    return view('wishlist', compact('games'));
});

// Detail halaman game berdasarkan ID
Route::get('/game/{id}', function ($id) {
    $game = \App\Models\Game::with('reviews.user')->findOrFail($id);
    return view('detail', compact('game'));
});

// Halaman Bantuan / FAQ (Statis)
Route::view('/bantuan', 'bantuan');
