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
                <div class="flex justify-center">
                <svg class="w-12 h-12 text-[#7C3AED]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="m3.99 16.854-1.314 3.504a.75.75 0 0 0 .966.965l3.503-1.314a3 3 0 0 0 1.068-.687L18.36 9.175s-.354-1.061-1.414-2.122c-1.06-1.06-2.122-1.414-2.122-1.414L4.677 15.786a3 3 0 0 0-.687 1.068zm12.249-12.63 1.383-1.383c.248-.248.579-.406.925-.348.487.08 1.232.322 1.934 1.025.703.703.945 1.447 1.025 1.934.058.346-.1.677-.348.925L19.774 7.76s-.353-1.06-1.414-2.12c-1.06-1.062-2.121-1.415-2.121-1.415z" fill="currentColor"/>
                </svg>
            </div>
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
                    <button type="submit" class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-bold uppercase tracking-widest py-4 rounded-xl transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.3009 13.6949L20.102 3.89742M10.5795 14.1355L12.8019 18.5804C13.339 19.6545 13.6075 20.1916 13.9458 20.3356C14.2394 20.4606 14.575 20.4379 14.8492 20.2747C15.1651 20.0866 15.3591 19.5183 15.7472 18.3818L19.9463 6.08434C20.2845 5.09409 20.4535 4.59896 20.3378 4.27142C20.2371 3.98648 20.013 3.76234 19.7281 3.66167C19.4005 3.54595 18.9054 3.71502 17.9151 4.05315L5.61763 8.2523C4.48114 8.64037 3.91289 8.83441 3.72478 9.15032C3.56153 9.42447 3.53891 9.76007 3.66389 10.0536C3.80791 10.3919 4.34498 10.6605 5.41912 11.1975L9.86397 13.42C10.041 13.5085 10.1295 13.5527 10.2061 13.6118C10.2742 13.6643 10.3352 13.7253 10.3876 13.7933C10.4468 13.87 10.491 13.9585 10.5795 14.1355Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Kirim Kode Verifikasi
                </button>
                    <a href="/profil" class="w-full text-center text-xs font-bold text-gray-500 hover:text-white py-3 transition-colors uppercase tracking-widest">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </main>
@include('components.toast-notification')
</body>
</html>
