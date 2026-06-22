<footer class="mt-6 pt-8 border-t border-white/5 pb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="mb-4">
                        <a href="/" class="inline-block">
                            <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault Logo" class="h-8 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)] hover:drop-shadow-[0_0_25px_rgba(124,58,237,1)] transition-all duration-300">
                        </a>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">Platform jual beli game digital terpercaya. Koleksi game premium dengan harga terjangkau.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-3">Navigasi</h4>
                    <div class="space-y-2">
                        <a href="/" class="block text-sm {{ request()->is('/') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Beranda</a>
                        <a href="/kategori" class="block text-sm {{ request()->is('kategori') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Kategori Game</a>
                        <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'}); setTimeout(function(){ if(!bantuanActive) toggleBantuan(event); }, 400); return false;" class="block text-sm text-gray-500 hover:text-white transition-all cursor-pointer">Bantuan & FAQ</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-3">Akun</h4>
                    <div class="space-y-2">
                        @if(auth()->guard()->check())
                        <a href="/profil" class="block text-sm {{ request()->is('profil') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Profil Saya</a>
                        <a href="/library" class="block text-sm {{ request()->is('library') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Library Game</a>
                        <a href="/orders" class="block text-sm {{ request()->is('orders') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Riwayat Pembelian</a>
                        @else
                        <a href="/login" class="block text-sm {{ request()->is('login') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Login</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-6 border-t border-white/5">
                <p class="text-xs text-gray-600">&copy; 2026 GameVault. Semua hak dilindungi.</p>
                <div class="flex items-center gap-6 text-xs text-gray-500 font-medium">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 14.5V16.5M7 10.0288C7.47142 10 8.05259 10 8.8 10H15.2C15.9474 10 16.5286 10 17 10.0288M7 10.0288C6.41168 10.0647 5.99429 10.1455 5.63803 10.327C5.07354 10.6146 4.6146 11.0735 4.32698 11.638C4 12.2798 4 13.1198 4 14.8V16.2C4 17.8802 4 18.7202 4.32698 19.362C4.6146 19.9265 5.07354 20.3854 5.63803 20.673C6.27976 21 7.11984 21 8.8 21H15.2C16.8802 21 17.7202 21 18.362 20.673C18.9265 20.3854 19.3854 19.9265 19.673 19.362C20 18.7202 20 17.8802 20 16.2V14.8C20 13.1198 20 12.2798 19.673 11.638C19.3854 11.0735 18.9265 10.6146 18.362 10.327C18.0057 10.1455 17.5883 10.0647 17 10.0288M7 10.0288V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8V10.0288" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Pembayaran Aman
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 70 70" xmlns="http://www.w3.org/2000/svg">
                            <g fill="#facc15">
                                <path d="M48.198,7.152L48.198,7.152 M48.051,6.334l-7.455,26.249h11.066L27.051,62.63l4.829-26.047H18.338L48.051,6.334 M48.052,2.583c-0.049,0-0.098,0.203-0.147,0.205c-0.84,0.03-1.613,0.419-2.244,0.89c-0.156,0.117-0.306,0.296-0.446,0.438l-29.713,29.92c-1.139,1.146-1.477,2.729-0.856,4.22c0.621,1.492,2.078,2.328,3.693,2.328h8.744l-3.966,21.83c-0.327,1.791,0.595,3.649,2.243,4.419c0.543,0.254,1.119,0.413,1.689,0.413c1.164,0,2.303-0.489,3.081-1.43l24.612-29.682c0.989-1.193,1.199-3.351,0.54-4.753c-0.659-1.403-2.07-2.797-3.62-2.797h-5.791l5.988-20.867c0.126-0.388,0.194-0.752,0.194-1.184c0-2.182-1.744-3.949-3.915-3.949C48.11,2.583,48.081,2.583,48.052,2.583L48.052,2.583z" />
                                <path d="M27.051,34.282c-0.244,0-0.488-0.089-0.681-0.268c-0.404-0.375-0.428-1.008-0.052-1.413l13-14c0.375-0.405,1.009-0.428,1.413-0.052c0.404,0.375,0.428,1.008,0.052,1.413l-13,14C27.587,34.175,27.319,34.282,27.051,34.282z" />
                                <path d="M40.161,42.282c-0.214,0-0.431-0.068-0.613-0.211c-0.436-0.339-0.515-0.967-0.175-1.403l3.889-5c0.339-0.435,0.967-0.515,1.403-0.175c0.436,0.339,0.515,0.967,0.175,1.403l-3.889,5C40.754,42.149,40.459,42.282,40.161,42.282z" />
                                <path d="M37.05,46.282c-0.214,0-0.431-0.068-0.612-0.211c-0.437-0.339-0.516-0.967-0.176-1.402l0.777-1c0.337-0.436,0.968-0.518,1.402-0.176c0.437,0.339,0.516,0.967,0.176,1.402l-0.777,1C37.644,46.149,37.349,46.282,37.05,46.282z" />
                            </g>
                        </svg>
                        Aktivasi Instan
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-400" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28.244 7.47h-25.572v17.060h26.656v-17.060h-1.084zM27.177 8.536l-10.298 10.298c-0.47 0.47-1.289 0.47-1.759 0l-10.3-10.298h22.356zM3.738 8.961l6.923 6.922-6.923 6.923v-13.846zM4.589 23.464l6.827-6.826 2.951 2.95c0.436 0.436 1.016 0.677 1.633 0.677s1.197-0.241 1.633-0.677l2.951-2.951 6.826 6.826h-22.822zM28.262 22.807l-6.923-6.924 6.923-6.924v13.848z" fill="currentColor"></path>
                        </svg>
                        Support 24/7
                    </span>
                </div>
            </div>
        </footer>