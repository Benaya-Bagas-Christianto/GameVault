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
                    <p class="flex items-center justify-center">
                        <svg class="w-4 h-4 inline-block mr-1 mb-0.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="17" r="1" fill="currentColor"/><path d="M12 10L12 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.44722 18.1056L10.2111 4.57771C10.9482 3.10361 13.0518 3.10362 13.7889 4.57771L20.5528 18.1056C21.2177 19.4354 20.2507 21 18.7639 21H5.23607C3.7493 21 2.78231 19.4354 3.44722 18.1056Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ session('msg') }}
                    </p>
                @endif
                @foreach ($errors->all() as $error)
                    <p class="flex items-center justify-center">
                        <svg class="w-4 h-4 inline-block mr-1 mb-0.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="17" r="1" fill="currentColor"/><path d="M12 10L12 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.44722 18.1056L10.2111 4.57771C10.9482 3.10361 13.0518 3.10362 13.7889 4.57771L20.5528 18.1056C21.2177 19.4354 20.2507 21 18.7639 21H5.23607C3.7493 21 2.78231 19.4354 3.44722 18.1056Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $error }}
                    </p>
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
                <div class="relative">
                    <input type="password" id="passwordInput" name="password" required placeholder="••••••••" class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 pr-10 text-sm text-white focus:outline-none focus:border-purple-500 transition-colors">
                    <button type="button" onclick="togglePassword('passwordInput', 'eyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white transition-colors">
                        <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
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
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            `;
        }
    }
</script>
</html>