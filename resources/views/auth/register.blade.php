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
    <title>Daftar Akun - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800;900&display=swap" rel="stylesheet">
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#0A0C10] text-white flex items-center justify-center min-h-screen p-4 py-10" style="font-family: 'Inter', sans-serif;">
    
    <div class="bg-[#12151C] p-8 rounded-2xl border border-white/10 w-full max-w-md shadow-2xl relative my-auto">
        
        {{-- Logo Glow --}}
        <div class="flex justify-center mb-5">
            <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault Logo" class="h-12 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)]">
        </div>
        
        <h2 class="text-2xl font-black text-center mb-1 tracking-wider uppercase text-white">BUAT AKUN BARU</h2>
        <p class="text-gray-400 text-center text-xs mb-6">Bergabunglah dan amankan lisensi game kamu</p>

        {{-- Penampil Pesan Error (Misal username/email sudah dipakai, atau password tidak cocok) --}}
        @if ($errors->any() || (session('msg') && session('status') == 'error'))
            <div class="mb-5 p-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-xs text-center font-bold">
                @if(session('msg'))
                    <p>⚠️ {{ session('msg') }}</p>
                @endif
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Form Pendaftaran --}}
        <form action="{{ url('/register') }}" method="POST" class="space-y-4">
            @csrf
            
            {{-- Username --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Username</label>
                <input type="text" name="username" required placeholder="Pilih username unik" value="{{ old('username') }}" 
                       class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/50 transition-all">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Alamat Email</label>
                <input type="email" name="email" required placeholder="contoh@email.com" value="{{ old('email') }}" 
                       class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/50 transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Password --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="passwordInput" name="password" required minlength="8" placeholder="••••••••" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}" title="Password harus mengandung huruf besar, kecil, angka, dan simbol" 
                               class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 pr-10 text-sm text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/50 transition-all">
                        <button type="button" onclick="togglePassword('passwordInput', 'eyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white transition-colors">
                            <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    
                    {{-- Password Strength Indicator --}}
                    <div class="mt-2 space-y-1.5">
                        <div class="flex gap-1">
                            <div id="bar1" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                            <div id="bar2" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                            <div id="bar3" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                            <div id="bar4" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                        </div>
                        <p id="strengthText" class="text-[10px] font-bold tracking-wider uppercase text-gray-500"></p>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Ulangi Password</label>
                    <div class="relative">
                        <input type="password" id="confirmPasswordInput" name="confirm_password" required minlength="8" placeholder="••••••••" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}" title="Password harus mengandung huruf besar, kecil, angka, dan simbol" 
                               class="w-full bg-[#0A0C10] border border-white/10 rounded-xl px-4 py-3 pr-10 text-sm text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/50 transition-all">
                        <button type="button" onclick="togglePassword('confirmPasswordInput', 'eyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white transition-colors">
                            <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition-all tracking-widest uppercase text-xs shadow-[0_0_15px_rgba(124,58,237,0.3)] hover:shadow-[0_0_25px_rgba(124,58,237,0.5)]">
                    DAFTAR SEKARANG
                </button>
            </div>
        </form>

        {{-- Footer Link --}}
        <div class="mt-6 text-center border-t border-white/5 pt-5">
            <p class="text-xs text-gray-500">
                Sudah punya lisensi akun? 
                <a href="{{ url('/login') }}" class="text-purple-400 hover:text-white font-bold transition-colors">Masuk di sini</a>
            </p>
        </div>
        
    </div>

    {{-- Password Strength Checker Script --}}
    <script>
        const passwordInput = document.getElementById('passwordInput');
        const bar1 = document.getElementById('bar1');
        const bar2 = document.getElementById('bar2');
        const bar3 = document.getElementById('bar3');
        const bar4 = document.getElementById('bar4');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            // Reset bars
            [bar1, bar2, bar3, bar4].forEach(bar => {
                bar.className = 'h-1 flex-1 rounded-full bg-white/10 transition-all duration-300';
            });

            if (password.length === 0) {
                strengthText.textContent = '';
                return;
            }

            // Check criteria
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            // Update UI based on strength
            if (strength <= 2) {
                // Weak
                bar1.classList.add('bg-red-500', 'shadow-[0_0_8px_rgba(239,68,68,0.5)]');
                strengthText.textContent = '⚠️ Lemah';
                strengthText.className = 'text-[10px] font-bold tracking-wider uppercase text-red-400';
            } else if (strength <= 3) {
                // Medium
                bar1.classList.add('bg-yellow-500', 'shadow-[0_0_8px_rgba(234,179,8,0.5)]');
                bar2.classList.add('bg-yellow-500', 'shadow-[0_0_8px_rgba(234,179,8,0.5)]');
                strengthText.textContent = '⚡ Sedang';
                strengthText.className = 'text-[10px] font-bold tracking-wider uppercase text-yellow-400';
            } else if (strength === 4) {
                // Strong
                bar1.classList.add('bg-green-500', 'shadow-[0_0_8px_rgba(34,197,94,0.5)]');
                bar2.classList.add('bg-green-500', 'shadow-[0_0_8px_rgba(34,197,94,0.5)]');
                bar3.classList.add('bg-green-500', 'shadow-[0_0_8px_rgba(34,197,94,0.5)]');
                strengthText.textContent = '✅ Kuat';
                strengthText.className = 'text-[10px] font-bold tracking-wider uppercase text-green-400';
            } else {
                // Very Strong
                bar1.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                bar2.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                bar3.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                bar4.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                strengthText.textContent = '🔒 Sangat Kuat';
                strengthText.className = 'text-[10px] font-bold tracking-wider uppercase text-purple-400';
            }
        });

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
</body>
</html>
