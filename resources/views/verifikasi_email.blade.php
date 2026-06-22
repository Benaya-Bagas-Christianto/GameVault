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
    <title>Verifikasi OTP Email - GameVault</title>
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
                <span class="text-4xl">🔐</span>
                <h1 class="text-xl font-black text-white uppercase tracking-widest mt-3">VERIFIKASI KODE OTP</h1>
                <p class="text-xs text-gray-500 mt-1">Kami telah mengirimkan 6-digit kode OTP ke email baru kamu (<span class="text-purple-400 font-bold">{{ session('pending_email') }}</span>).</p>
            </div>

            @if(session('msg'))
                <div class="mb-5 p-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 {{ session('status') == 'error' ? 'bg-red-500/20 border border-red-500/50 text-red-400' : 'bg-green-500/20 border border-green-500/50 text-green-400' }}">
                    @if(session('status') == 'error')
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                    <span>{{ session('msg') }}</span>
                </div>
            @endif

            <form action="/profil/ganti-email/proses" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center">Masukkan 6 Digit Kode OTP</label>
                    <input type="text" name="otp_input" maxlength="6" required placeholder="000000" class="w-full text-center px-4 py-4 bg-[#0A0C10] border border-white/10 rounded-xl text-white placeholder-gray-700 focus:outline-none focus:border-yellow-500 text-2xl font-mono tracking-[10px] font-black">
                </div>

                <div class="flex flex-col gap-2 pt-4">
                    <button type="submit" class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold py-3 rounded-xl transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Verifikasi & Update Email</span>
                    </button>
                    <a href="/profil" class="w-full text-center text-xs font-bold text-gray-500 hover:text-white py-3 transition-colors uppercase tracking-widest">
                        Batalkan Pengajuan
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
