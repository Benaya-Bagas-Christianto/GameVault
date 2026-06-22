<div id="settingsDropdown" class="absolute right-0 mt-3 w-64 bg-[#12151C] border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible transform -translate-y-2 transition-all duration-200 z-50">
    <div class="px-4 py-4 border-b border-white/5">
        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-3">Pengaturan Akun</p>
        <a href="/profil" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-purple-400 bg-purple-500/10 border border-purple-500/20 rounded-xl hover:bg-purple-500/20 hover:border-purple-500/30 transition-all">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="M80,71.2V74c0,3.3-2.7,6-6,6H26c-3.3,0-6-2.7-6-6v-2.8c0-7.3,8.5-11.7,16.5-15.2c0.3-0.1,0.5-0.2,0.8-0.4 c0.6-0.3,1.3-0.3,1.9,0.1C42.4,57.8,46.1,59,50,59c3.9,0,7.6-1.2,10.8-3.2c0.6-0.4,1.3-0.4,1.9-0.1c0.3,0.1,0.5,0.2,0.8,0.4 C71.5,59.5,80,63.9,80,71.2z" /><ellipse cx="50" cy="36.5" rx="14.9" ry="16.5" /></svg>
            Profil Saya
        </a>
    </div>
    
    <div class="p-2 space-y-1">
        <a href="/library" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-colors">
            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Library Game
        </a>
        <a href="/orders" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-colors">
            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="6" width="18" height="13" rx="2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /><path d="M3 10H20.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /><path d="M7 15H9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
            Riwayat Transaksi
        </a>
        <a href="/wishlist" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-colors">
            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
            Wishlist
        </a>
        
        @if(Auth::user()->role === 'admin')
        <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-blue-400 hover:text-blue-300 hover:bg-blue-500/10 rounded-xl transition-colors">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g><path d="M1.836,23.001c-1.042,0.891-1.672,2.188-1.728,3.559c-0.056,1.37,0.467,2.714,1.435,3.687 c0.935,0.939,2.179,1.457,3.504,1.457c1.424,0,2.778-0.613,3.716-1.681l6.626-7.474l7.739,7.739 c0.916,0.916,2.134,1.421,3.429,1.421c1.296,0,2.514-0.505,3.43-1.421s1.421-2.134,1.421-3.43c0-1.295-0.505-2.513-1.421-3.429 l-8.152-8.152l2.042-2.303c0.604,0.172,1.225,0.259,1.848,0.259c1.903,0,3.659-0.817,4.818-2.242c1.601-1.967,1.8-4.746,0.507-7.08 c-0.291-0.524-1.166-0.503-1.443,0.031l-1.473,2.837c-0.354,0.683-1.248,0.967-1.937,0.612l-1.502-0.782 c-0.341-0.176-0.592-0.475-0.707-0.84c-0.116-0.365-0.082-0.754,0.094-1.094l1.66-3.188c0.133-0.256,0.124-0.556-0.025-0.804 c-0.146-0.242-0.401-0.39-0.75-0.392c-1.903,0-3.66,0.816-4.819,2.241c-1.285,1.579-1.679,3.691-1.079,5.712l-5.612,4.806 L5.739,5.334C6.204,4.658,6.346,3.819,6.08,3.012C5.768,2.065,5.002,1.38,4.033,1.177L1.97,0.744 C1.544,0.653,1.1,0.787,0.793,1.095c-0.309,0.309-0.44,0.749-0.351,1.177l0.433,2.063c0.265,1.266,1.396,2.186,2.69,2.186 c0.537,0,1.038-0.166,1.476-0.47l7.653,7.653L1.836,23.001z M3.565,5.538c-0.706,0-1.521-0.46-1.712-1.373L1.421,2.136 C1.401,2.04,1.431,2.01,1.5,1.941C1.55,1.891,1.615,2,1.73,2c0.011,0,0.022,0,0.034,0l2.063,0.295 C4.444,2.424,4.931,2.724,5.13,3.327C5.319,3.9,5.197,4.507,4.811,4.943L4.643,5.116C4.339,5.386,3.966,5.538,3.565,5.538z M29.279,24.137c0.728,0.727,1.128,1.693,1.128,2.722c0,1.029-0.4,1.996-1.128,2.723c-1.452,1.455-3.99,1.455-5.444,0l-7.782-7.782 l5.117-5.772L29.279,24.137z M20.118,8.242c-0.63-1.786-0.329-3.685,0.805-5.078C21.83,2.05,23.18,1.38,24.712,1.3l-1.518,2.914 c-0.299,0.578-0.356,1.237-0.16,1.857s0.623,1.127,1.2,1.426l1.502,0.782c0.347,0.179,0.733,0.273,1.121,0.273 c0.913,0,1.742-0.503,2.163-1.312l1.315-2.533c0.913,1.905,0.703,4.091-0.568,5.653c-1.347,1.654-3.753,2.286-5.891,1.567 c-0.19-0.063-0.399-0.008-0.534,0.142L8.013,29.36c-0.749,0.854-1.83,1.343-2.966,1.343c-1.057,0-2.05-0.413-2.795-1.162 c-0.783-0.787-1.189-1.832-1.145-2.941s0.534-2.117,1.379-2.839L19.972,8.788C20.129,8.653,20.187,8.437,20.118,8.242z" /><path d="M19.877,21.033l6.026,6.026c0.098,0.098,0.226,0.146,0.354,0.146s0.256-0.049,0.354-0.146 c0.195-0.195,0.195-0.512,0-0.707l-6.026-6.026c-0.195-0.195-0.512-0.195-0.707,0S19.682,20.838,19.877,21.033z" /><path d="M5.065,24.769c-1.103,0-2,0.897-2,2s0.897,2,2,2s2-0.897,2-2S6.168,24.769,5.065,24.769z M5.065,27.769 c-0.552,0-1-0.448-1-1s0.448-1,1-1s1,0.448,1,1S5.617,27.769,5.065,27.769z" /></g></svg>
            Buka Admin Panel
        </a>
        @endif
    </div>
    
    <div class="p-2 border-t border-white/5">
        <a href="#" onclick="event.preventDefault(); localStorage.removeItem('cartCount'); localStorage.removeItem('wishlist'); localStorage.removeItem('cart_cache'); window.location.href='/logout';" class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-colors">
            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 16.9998L21 11.9998M21 11.9998L16 6.99982M21 11.9998H9M12 16.9998C12 17.2954 12 17.4432 11.989 17.5712C11.8748 18.9018 10.8949 19.9967 9.58503 20.2571C9.45903 20.2821 9.31202 20.2985 9.01835 20.3311L7.99694 20.4446C6.46248 20.6151 5.69521 20.7003 5.08566 20.5053C4.27293 20.2452 3.60942 19.6513 3.26118 18.8723C3 18.288 3 17.5161 3 15.9721V8.02751C3 6.48358 3 5.71162 3.26118 5.12734C3.60942 4.3483 4.27293 3.75442 5.08566 3.49435C5.69521 3.29929 6.46246 3.38454 7.99694 3.55503L9.01835 3.66852C9.31212 3.70117 9.45901 3.71749 9.58503 3.74254C10.8949 4.00297 11.8748 5.09786 11.989 6.42843C12 6.55645 12 6.70424 12 6.99982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
            Keluar Akun
        </a>
    </div>
</div>

<script>
    if (typeof window.toggleSettings === 'undefined') {
        window.toggleSettings = function() {
            const dropdown = document.getElementById('settingsDropdown');
            if (dropdown && dropdown.classList.contains('opacity-0')) {
                dropdown.classList.remove('opacity-0', 'invisible', '-translate-y-2');
                dropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
            } else if (dropdown) {
                dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                dropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
            }
        };

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('settingsDropdown');
            const settingsBtn = document.getElementById('settingsBtn');
            if (dropdown && settingsBtn && !settingsBtn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                dropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
            }
        });
    }
</script>
