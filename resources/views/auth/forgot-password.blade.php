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
    <title>Lupa Password - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center p-4">
    <div class="bg-[#111] border border-[#333] p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault" class="w-12 h-12 drop-shadow-[0_0_15px_rgba(124,58,237,0.8)]">
        </div>
        <h2 class="text-2xl font-black text-cyan-400 font-orbitron mb-2 text-center uppercase tracking-widest">Lupa Password?</h2>
        <p class="text-gray-500 text-sm text-center mb-8">Masukkan email akunmu untuk menerima link pemulihan password.</p>

        @if(session('msg'))
            <div class="mb-5 p-3 bg-green-500/20 border border-green-500/50 rounded-xl text-green-400 text-xs font-bold flex items-center justify-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('msg') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-500/10 text-red-500 border border-red-500/20 text-xs font-bold text-center">
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ url('/forgot-password') }}" method="POST" class="space-y-5">
            @csrf
            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Email Terdaftar</label>
                <input type="email" name="email" required class="w-full bg-[#1a1a1a] border border-[#333] rounded-lg px-4 py-3 outline-none focus:border-cyan-400 transition-all">
            </div>

            <button type="submit" class="w-full bg-cyan-600 text-white font-bold py-4 rounded-xl hover:bg-white hover:text-black transition-all shadow-lg tracking-widest uppercase text-sm mt-4">
                Kirim Link Reset
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="{{ url('/login') }}" class="text-xs text-gray-500 hover:text-cyan-400 transition-colors">← Kembali ke Login</a>
        </div>
    </div>
</body>
</html>