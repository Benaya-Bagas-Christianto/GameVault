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
    <title>Buat Password Baru - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505 !important; color: #FFFFFF; }
    </style>
</head>
<body class="flex flex-col min-h-screen antialiased justify-center items-center p-6">

    <div class="w-full max-w-md bg-[#12151C] rounded-2xl border border-white/10 p-8 shadow-[0_0_50px_rgba(124,58,237,0.15)]">
        
        {{-- LOGO & JUDUL --}}
        <div class="text-center mb-8">
            <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault" class="w-16 h-16 mx-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)]">
            <h1 class="text-xl font-black text-white uppercase tracking-widest mt-3">PASWORD BARU</h1>
            <p class="text-xs text-gray-500 mt-1">Silakan masukkan password baru yang kuat untuk mengamankan akun GameVault kamu.</p>
        </div>

        {{-- ALERT NOTIFIKASI --}}
        @if(session('msg'))
            <div class="p-4 mb-5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-medium">
                ❌ {{ session('msg') }}
            </div>
        @endif

        {{-- FORM UPDATE PASSWORD --}}
        <form action="/reset-password" method="POST" class="space-y-5">
            @csrf
            
            {{-- Token & Email Hidden (Wajib ada sesuai controller kamu) --}}
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            {{-- TAMPILAN EMAIL INFO (READONLY) --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Mereset Akun Untuk Email</label>
                <p class="text-sm text-gray-400 font-mono bg-[#0A0C10] px-4 py-3 rounded-xl border border-white/5 truncate">
                    {{ $email }}
                </p>
            </div>

            {{-- INPUT PASSWORD BARU --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Password Baru</label>
                <div class="relative">
                    <input type="password" id="passwordInput" name="password" required minlength="8" placeholder="••••••••" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}" title="Password harus mengandung huruf besar, kecil, angka, dan simbol" 
                           class="w-full px-4 py-3.5 pr-10 bg-[#0A0C10] border border-white/10 rounded-xl text-white placeholder-gray-700 focus:outline-none focus:border-[#7C3AED] transition-colors text-sm">
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

            {{-- KONFIRMASI PASSWORD BARU --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" id="confirmPasswordInput" name="confirm_password" required minlength="8" placeholder="••••••••" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}" title="Password harus mengandung huruf besar, kecil, angka, dan simbol" 
                           class="w-full px-4 py-3.5 pr-10 bg-[#0A0C10] border border-white/10 rounded-xl text-white placeholder-gray-700 focus:outline-none focus:border-[#7C3AED] transition-colors text-sm">
                    <button type="button" onclick="togglePassword('confirmPasswordInput', 'eyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white transition-colors">
                        <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- BUTTON SUBMIT --}}
            <div class="pt-4">
                <button type="submit" class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-bold uppercase tracking-widest py-4 rounded-xl transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2">
                    <svg fill="currentColor" class="w-4 h-4" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" xml:space="preserve">
                        <g id="save">
                            <path d="M22.083,24H1.917C0.86,24,0,23.14,0,22.083V1.917C0,0.86,0.86,0,1.917,0h16.914L24,5.169v16.914
                                C24,23.14,23.14,24,22.083,24z M20,22h2V5.998l-3-3V9c0,1.103-0.897,2-2,2H7c-1.103,0-2-0.897-2-2V2H2v20h2v-7c0-1.103,0.897-2,2-2
                                h12c1.103,0,2,0.897,2,2V22z M6,22h12v-7.001L6,15V22z M7,2v7h10V2H7z"/>
                            <path d="M15,8h-4V3h4V8z"/>
                        </g>
                    </svg>
                    Simpan & Perbarui Password
                </button>
            </div>
        </form>

    </div>

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
                strengthText.textContent = '🛡️ Kuat';
                strengthText.className = 'text-[10px] font-bold tracking-wider uppercase text-green-400';
            } else {
                // Very Strong
                bar1.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                bar2.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                bar3.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                bar4.classList.add('bg-purple-500', 'shadow-[0_0_10px_rgba(168,85,247,0.6)]');
                strengthText.textContent = '💎 Sangat Kuat';
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