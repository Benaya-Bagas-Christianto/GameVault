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
                <div class="flex justify-center mb-3">
                    <svg class="w-14 h-14 text-yellow-500 drop-shadow-[0_0_15px_rgba(234,179,8,0.3)]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 14.5V16.5M7 10.0288C7.47142 10 8.05259 10 8.8 10H15.2C15.9474 10 16.5286 10 17 10.0288M7 10.0288C6.41168 10.0647 5.99429 10.1455 5.63803 10.327C5.07354 10.6146 4.6146 11.0735 4.32698 11.638C4 12.2798 4 13.1198 4 14.8V16.2C4 17.8802 4 18.7202 4.32698 19.362C4.6146 19.9265 5.07354 20.3854 5.63803 20.673C6.27976 21 7.11984 21 8.8 21H15.2C16.8802 21 17.7202 21 18.362 20.673C18.9265 20.3854 19.3854 19.9265 19.673 19.362C20 18.7202 20 17.8802 20 16.2V14.8C20 13.1198 20 12.2798 19.673 11.638C19.3854 11.0735 18.9265 10.6146 18.362 10.327C18.0057 10.1455 17.5883 10.0647 17 10.0288M7 10.0288V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8V10.0288" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
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
@include('components.toast-notification')
</body>
</html>
