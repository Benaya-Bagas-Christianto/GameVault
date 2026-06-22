<!DOCTYPE html>
<html lang="id">
<head>
    <script>
        (function() {
            let currentUserId = "{{ Auth::check() ? Auth::id() : 'null' }}";
            if (localStorage.getItem('lastUserId') !== currentUserId) {
                localStorage.removeItem('cartCount');
                localStorage.removeItem('wishlist');
                localStorage.setItem('lastUserId', currentUserId);
            }
        })();
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#0A0C10] text-white flex items-center justify-center h-screen p-4" style="font-family: 'Inter', sans-serif;">
    <div class="bg-[#12151C] p-8 rounded-2xl border border-white/10 w-full max-w-md shadow-2xl relative">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault Logo" class="h-12 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)]">
        </div>
        <h2 class="text-2xl font-black text-center mb-1 tracking-wider uppercase text-white">GAMEVAULT</h2>
        <p class="text-gray-400 text-center text-xs mb-6">Silakan masuk untuk melanjutkan</p>

        {{-- Penampil Pesan Sukses (Misal habis register) --}}
        @if(session('msg') && session('status') == 'success')
            <div class="mb-5 p-3 bg-green-500/20 border border-green-500/50 rounded-xl text-green-400 text-xs text-center font-bold">
                ✅ {{ session('msg') }}
            </div>
        @endif

        {{-- Penampil Pesan Error (Password salah / dll) --}}
        @if ($errors->any() || (session('msg') && session('status') == 'error'))
            <div class="mb-5 p-3 bg-red-500/20 border border-red-500/50 rounded-xl text-red-400 text-xs text-center font-bold">
                @if(session('msg'))
                    <p>⚠️ {{ session('msg') }}</p>
                @endif
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Email / Username</label>
                {{-- FIX: Nama sudah diubah jadi username_email sesuai permintaan AuthController --}}
                <input type="text" name="username_email" required placeholder="Masukkan email atau username" value="{{ old('username_email') }}" class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 transition-colors">
            </div>
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400">Password</label>
                    <a href="{{ url('/forgot-password') }}" class="text-xs text-purple-400 hover:text-purple-300 transition-colors hover:underline">Forgot Password?</a>
                </div>
                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 transition-colors">
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition-colors tracking-widest uppercase text-xs shadow-lg shadow-purple-600/30">MASUK SEKARANG</button>
            </div>
        </form>
        <div class="mt-6 text-center border-t border-white/5 pt-5 space-y-3">
            <p class="text-xs text-gray-500">
                Belum punya lisensi akun? 
                <a href="{{ url('/register') }}" class="text-purple-400 hover:text-white font-bold transition-colors">Buat Akun di sini</a>
            </p>
            <a href="{{ url('/') }}" class="text-xs text-gray-500 hover:text-purple-400 transition-colors block">← Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>