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
    <title>Ajukan Ganti Email - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505 !important; color: #FFFFFF; }
    </style>

    
</head>
<body class="flex flex-col min-h-screen antialiased">
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-[#12151C] rounded-2xl border border-white/10 p-8 shadow-2xl">
            <div class="text-center mb-6">
                <span class="text-4xl">✏️</span>
                <h1 class="text-xl font-black text-white uppercase tracking-widest mt-3">GANTI ALAMAT EMAIL</h1>
                <p class="text-xs text-gray-500 mt-1">Masukkan alamat email baru kamu. Kode verifikasi OTP akan dikirimkan ke email tersebut.</p>
            </div>

            @if(session('msg'))
                <div class="p-4 mb-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-medium">
                    ❌ {{ session('msg') }}
                </div>
            @endif

            <form action="/profil/ganti-email/kirim" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Email Baru Kamu</label>
                    <input type="email" name="email_baru" required placeholder="contoh@emailbaru.com" class="w-full px-4 py-3.5 bg-[#0A0C10] border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-[#7C3AED] text-sm">
                </div>

                <div class="flex flex-col gap-2 pt-4">
                    <button type="submit" class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-bold uppercase tracking-widest py-4 rounded-xl transition-all shadow-lg shadow-purple-500/20">
                        🚀 Kirim Kode Verifikasi
                    </button>
                    <a href="/profil" class="w-full text-center text-xs font-bold text-gray-500 hover:text-white py-3 transition-colors uppercase tracking-widest">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
