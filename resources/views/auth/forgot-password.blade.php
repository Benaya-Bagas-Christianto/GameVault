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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-[#0A0C10] text-white flex items-center justify-center min-h-screen p-4" style="font-family: 'Inter', sans-serif;">
    <div class="bg-[#12151C] p-8 rounded-2xl border border-white/10 w-full max-w-md shadow-2xl relative">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault Logo" class="h-12 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)]">
        </div>
        <h2 class="text-2xl font-black text-center mb-1 tracking-wider uppercase text-white">Lupa Password?</h2>
        <p class="text-gray-400 text-center text-xs mb-8">Masukkan email akunmu untuk menerima link pemulihan password.</p>

        @if(session('msg'))
            <div class="mb-5 p-3 bg-green-500/10 border border-green-500/20 rounded-xl text-green-500 text-xs font-bold flex items-center justify-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('msg') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold text-center">
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ url('/forgot-password') }}" method="POST" class="space-y-5">
            @csrf
            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 block">Email Terdaftar</label>
                <input type="email" name="email" required class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-[#7C3AED] transition-all text-sm">
            </div>

            <button type="submit" class="w-full bg-[#7C3AED] text-white font-bold py-4 rounded-xl hover:bg-[#6D28D9] transition-all shadow-lg tracking-widest uppercase text-sm mt-4">
                Kirim Link Reset
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="{{ url('/login') }}" class="text-xs font-bold text-gray-500 hover:text-white uppercase tracking-widest transition-colors">Batal</a>
        </div>
    </div>
</body>
</html>