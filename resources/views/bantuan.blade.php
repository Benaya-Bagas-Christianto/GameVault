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
    <title>Pusat Bantuan - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <style>
        /* KUNCI ANTI-KEDIP PUTIH: Set background hitam sejak awal tanpa menunggu Tailwind CDN */
        html,
        body {
            background-color: #0A0C10 !important;
            color: #FFFFFF !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .card-bg {
            background-color: #12151C;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .hover-card:hover {
            border-color: rgba(124, 58, 237, 0.5);
            transform: translateY(-2px);
        }


        /* === ANIMASI TRANSISI HALAMAN (FADE & SCALE) === */
        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: scale(0.98);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .page-enter {
            animation: pageEnter 0.15s ease-out both;
        }

        /* === ANIMASI KONTEN MASUK === */
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeSlideIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .animate-in-delay-1 {
            animation-delay: 0.06s;
        }

        .animate-in-delay-2 {
            animation-delay: 0.13s;
        }

        .animate-in-delay-3 {
            animation-delay: 0.20s;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    
</head>

<body class="antialiased bg-[#0A0B0E] text-white overflow-x-hidden selection:bg-purple-500 selection:text-white min-h-screen flex flex-col">

    <header class="h-20 border-b border-white/5 flex items-center justify-between px-6 z-30 sticky top-0 bg-[#0A0B0E]/95 backdrop-blur-md flex-shrink-0">
        {{-- Kiri: Logo & Search --}}
        <div class="flex-1 flex justify-start items-center gap-6">
            <a href="/" class="flex-shrink-0 flex items-center group">
                <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault Logo" class="h-6 sm:h-7 lg:h-8 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)] group-hover:drop-shadow-[0_0_25px_rgba(124,58,237,1)] transition-all duration-300">
            </a>
                            @include('components.search-bar')
        </div>

        {{-- Tengah: Navigasi --}}
        <nav class="flex-1 hidden lg:flex items-center justify-center gap-8 xl:gap-10 text-sm font-medium h-full">
            <a href="/" id="nav-beranda" class="{{ request()->is('/') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Beranda</a>
            <a href="/kategori" id="nav-kategori" class="{{ request()->is('kategori') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Kategori</a>
            <a href="/bantuan" class="{{ request()->is('bantuan') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Bantuan</a>
        </nav>

        {{-- Kanan: Ikon & Login --}}
        <div class="flex-1 flex items-center justify-end gap-4 sm:gap-5">
            {{-- ICON KERANJANG --}}
            <a href="/cart" class="relative {{ request()->is('cart') ? 'text-[#8B5CF6]' : 'text-gray-400 hover:text-white' }} transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path>
                </svg>
                    @php
                        $globalCartCount = Auth::check() ? \App\Models\Keranjang::where('user_id', Auth::id())->count() : 0;
                    @endphp
                    <span id="globalCartBadge" class="absolute -top-1.5 -right-1.5 w-4 h-4 text-white text-[9px] font-bold items-center justify-center rounded-full" style="background-color: #7C3AED !important; display: {{ $globalCartCount > 0 ? 'flex' : 'none' }} !important;">{{ $globalCartCount }}</span>
                    <script>
                        window.syncCartBadge = function(e) {
    let isLoggedIn = @json(Auth::check());
    if (!isLoggedIn) return;
    if (localStorage.getItem('cartCount') === null) {
        if (typeof updateGlobalCartBadge === 'function') updateGlobalCartBadge();
        return;
    }
    let cachedCount = parseInt(localStorage.getItem('cartCount')) || 0;
    let badgeInit = document.getElementById('globalCartBadge');
    if (badgeInit) {
        badgeInit.innerText = cachedCount;
        badgeInit.style.setProperty('display', cachedCount > 0 ? 'flex' : 'none', 'important');
    }
};
                        window.addEventListener('pageshow', window.syncCartBadge);
                    </script>
                </a>

                {{-- ICON WISHLIST --}}
                <a href="/wishlist" class="relative {{ request()->is('wishlist') ? 'text-[#8B5CF6]' : 'text-gray-400 hover:text-white' }} transition-colors hidden sm:block">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    @php
                        $globalWishlistCount = Auth::check() ? \App\Models\Wishlist::where('user_id', Auth::id())->count() : 0;
                    @endphp
                    <span class="globalWishlistBadge absolute -top-1.5 -right-1.5 w-4 h-4 text-white text-[9px] font-bold items-center justify-center rounded-full" style="background-color: #EF4444 !important; display: {{ $globalWishlistCount > 0 ? 'flex' : 'none' }} !important;">{{ $globalWishlistCount }}</span>
                </a>
                <script>
                    window.syncWishlistBadge = function(e) {
    let isLoggedIn = @json(Auth::check());
    if (!isLoggedIn) return;
    let cachedWishlist = localStorage.getItem('wishlist');
    if (cachedWishlist === null) {
        if (typeof updateGlobalWishlistBadge === 'function') updateGlobalWishlistBadge();
        return;
    }
    let wishlist = JSON.parse(cachedWishlist) || [];
    let cachedCount = wishlist.length;
    let badges = document.querySelectorAll('.globalWishlistBadge, #globalWishlistBadge');
    badges.forEach(badgeInit => {
        badgeInit.innerText = cachedCount;
        badgeInit.style.setProperty('display', cachedCount > 0 ? 'flex' : 'none', 'important');
    });
};
                    window.addEventListener('pageshow', window.syncWishlistBadge);
                </script>

            <div class="h-6 w-px bg-white/10 mx-1 hidden sm:block"></div>
            @auth
            <div class="relative cursor-pointer" onclick="toggleSettings()" id="settingsBtn">
                @if(isset($pendingRefundsCount) && $pendingRefundsCount > 0)
                    <div class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full border-2 border-[#12151C] z-10" style="margin-top: -2px; margin-right: -2px;"></div>
                @endif
                <div class="flex items-center gap-3 border border-white/5 py-1.5 pl-1.5 pr-4 rounded-full" style="background-color: #12151C !important;">
                    @if(Auth::user()->foto)
                    <img src="{{ asset('assets/profile/' . Auth::user()->foto) }}" class="w-10 h-10 rounded-full object-cover border border-purple-500/50">
                    @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold !text-white" style="background: linear-gradient(135deg, #7C3AED 0%, #4C1D95 100%);">{{ strtoupper(substr(Auth::user()->username ?? 'U', 0, 1)) }}</div>
                    @endif
                    <span class="text-sm font-semibold !text-white hidden sm:block">{{ Auth::user()->username }}</span>
                    <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                @include('components.settings-dropdown')
            </div>
            @else
            <a href="/login" class="px-5 py-1.5 bg-white text-black text-xs font-bold rounded-full hover:bg-gray-200 transition-colors">LOGIN</a>
            @endauth
        </div>
    </header>

    <div id="bantuanContent" class="max-w-5xl mx-auto page-enter p-6 md:p-10 mt-4">


        {{-- Hero Section --}}
        <div class="text-center mb-16 relative animate-in animate-in-delay-1">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-purple-600/20 blur-[100px] rounded-full pointer-events-none"></div>
            <h1 class="text-4xl md:text-5xl font-black mb-4 relative z-10">Pusat <span class="text-4xl md:text-5xl font-black mb-4 relative z-10">Bantuan</span></h1>
            <p class="text-gray-400 mb-8 text-sm md:text-base max-w-xl mx-auto relative z-10">Kami siap membantu kamu dengan berbagai pertanyaan seputar GameVault. Cari masalahmu di sini.</p>
            <div class="flex flex-wrap items-center justify-center gap-2 md:gap-3 mt-5 text-[11px] md:text-xs relative z-10">
                <span class="text-gray-500 mr-1">Populer sekarang:</span>
                <span class="px-3 py-1.5 rounded-full bg-[#12151C] border border-white/10 hover:border-purple-500 hover:text-purple-400 cursor-pointer transition-colors text-gray-400">Lupa Password</span>
                <span class="px-3 py-1.5 rounded-full bg-[#12151C] border border-white/10 hover:border-purple-500 hover:text-purple-400 cursor-pointer transition-colors text-gray-400">Cara Refund</span>
                <span class="px-3 py-1.5 rounded-full bg-[#12151C] border border-white/10 hover:border-purple-500 hover:text-purple-400 cursor-pointer transition-colors text-gray-400">Gagal Bayar</span>
            </div>
        </div>

        {{-- Kategori Bantuan --}}
        <h2 class="text-lg font-bold mb-5 animate-in animate-in-delay-2">Kategori Bantuan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-14 animate-in animate-in-delay-2">
            <div class="card-bg rounded-2xl hover-card cursor-pointer transition-all overflow-hidden">
                <div class="w-full h-28 bg-blue-500/10 flex items-center justify-center mb-0 relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-transparent"></div>
                    <svg class="w-20 h-20 fill-current text-blue-400 relative z-10" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <g>
                                <path d="M80,71.2V74c0,3.3-2.7,6-6,6H26c-3.3,0-6-2.7-6-6v-2.8c0-7.3,8.5-11.7,16.5-15.2c0.3-0.1,0.5-0.2,0.8-0.4 c0.6-0.3,1.3-0.3,1.9,0.1C42.4,57.8,46.1,59,50,59c3.9,0,7.6-1.2,10.8-3.2c0.6-0.4,1.3-0.4,1.9-0.1c0.3,0.1,0.5,0.2,0.8,0.4 C71.5,59.5,80,63.9,80,71.2z" />
                            </g>
                            <g>
                                <ellipse cx="50" cy="36.5" rx="14.9" ry="16.5" />
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="font-bold mb-2">Akun & Profil</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">Masalah terkait login, verifikasi email, ganti password, dan keamanan akun.</p>
                </div>
            </div>
            <div class="card-bg rounded-2xl hover-card cursor-pointer transition-all overflow-hidden">
                <div class="w-full h-28 bg-green-500/10 flex items-center justify-center mb-0 relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-transparent"></div>
                    <svg class="w-20 h-20 stroke-current text-green-400 relative z-10" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 10H22" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6 15H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="font-bold mb-2">Pembayaran</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">Bantuan untuk transaksi Midtrans, metode pembayaran gagal, dan invoice.</p>
                </div>
            </div>
            <div class="card-bg rounded-2xl hover-card cursor-pointer transition-all overflow-hidden">
                <div class="w-full h-28 bg-pink-500/10 flex items-center justify-center mb-0 relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-pink-500/20 to-transparent"></div>
                    <svg class="w-16 h-16 fill-current text-pink-400 relative z-10" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg">
                        <g transform="translate(-1)">
                            <g>
                                <g>
                                    <path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333
                                        h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333
                                        c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333
                                        C235.943,223.156,226.391,213.605,214.609,213.605z"/>
                                    <path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96
                                        c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598
                                        c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168
                                        c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056
                                        c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411
                                        c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831
                                        c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43
                                        l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228
                                        c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z M455.757,403.285l-69.566-62.373
                                        c-14.316-12.852-32.697-19.956-51.767-19.956H181.592c-19.07,0-37.45,7.104-51.777,19.965l-69.758,62.544
                                        c-19.658-18.052-21.296-61.46-5.213-122.832c12.346-47.111,33.478-98.672,51.546-134.307c1.399-2.752,2.112-5.086,3.217-9.411
                                        c0.319-1.249,1.62-6.572,1.952-7.876c0.16-0.629,0.314-1.22,0.461-1.772l50.992-19.374c1.186,1.381,2.463,2.836,3.793,4.303
                                        c1.113,1.222,1.113,1.222,2.273,2.457c9.927,10.492,13.691,13.62,24.204,13.62h128c10.147,0,13.785-2.861,24.417-13.27
                                        c1.094-1.079,1.094-1.079,2.155-2.148c1.719-1.742,3.365-3.468,4.858-5.073l51.321,19.499c0.151,0.56,0.308,1.159,0.471,1.797
                                        c0.382,1.493,1.69,6.836,1.931,7.787c1.066,4.206,1.73,6.435,2.992,9.058c18.908,39.428,38.104,87.404,50.193,134.082
                                        C475.42,341.016,474.252,384.613,455.757,403.285z"/>
                                    <path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333
                                        c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z"/>
                                    <path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333
                                        S354.385,234.938,342.609,234.938z"/>
                                    <path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333
                                        S311.719,192.271,299.943,192.271z"/>
                                    <path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333
                                        S397.052,192.271,385.276,192.271z"/>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="font-bold mb-2">Game &amp; Instalasi</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">Masalah cara unduh, aktivasi game, performa, atau minimum spesifikasi PC.</p>
                </div>
            </div>
        </div>

        {{-- FAQ & Kotak Kontak --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-in animate-in-delay-3">
            <div class="lg:col-span-2 card-bg p-8 rounded-2xl">
                <h2 class="text-lg font-bold mb-6">Pertanyaan yang Sering Diajukan</h2>
                <div class="space-y-2">
                    <div class="border-b border-white/5">
                        <button onclick="toggleFaq(this)" class="w-full py-4 flex items-center justify-between text-left focus:outline-none group">
                            <h4 class="font-bold text-sm text-gray-300 group-hover:text-white transition-colors">Bagaimana cara mengunduh game yang sudah dibeli?</h4>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden pb-5 text-xs text-gray-400 leading-relaxed">
                            Silakan buka menu "Riwayat Pembelian" yang ada di dropdown profil Anda, klik invoice pembelian yang sudah sukses, lalu klik tombol "Download Game".
                        </div>
                    </div>
                    <div class="border-b border-white/5">
                        <button onclick="toggleFaq(this)" class="w-full py-4 flex items-center justify-between text-left focus:outline-none group">
                            <h4 class="font-bold text-sm text-gray-300 group-hover:text-white transition-colors">Metode pembayaran apa saja yang tersedia?</h4>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden pb-5 text-xs text-gray-400 leading-relaxed">
                            Kami mendukung pembayaran lengkap via Midtrans, termasuk Transfer Bank (BCA, BNI, BRI), QRIS, GoPay, dan OVO secara real-time.
                        </div>
                    </div>
                    <div class="border-b border-white/5">
                        <button onclick="toggleFaq(this)" class="w-full py-4 flex items-center justify-between text-left focus:outline-none group">
                            <h4 class="font-bold text-sm text-gray-300 group-hover:text-white transition-colors">Bagaimana cara meminta refund?</h4>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden pb-5 text-xs text-gray-400 leading-relaxed">
                            Refund bisa diajukan maksimal 14 hari setelah pembelian dengan syarat waktu bermain (playtime) di bawah 2 jam. Hubungi teknisi kami untuk memprosesnya.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-bg p-8 rounded-2xl flex flex-col items-center text-center justify-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-[#7C3AED]/5 to-transparent pointer-events-none"></div>
                <h3 class="font-bold mb-3 text-lg relative z-10">Masih butuh bantuan?</h3>
                <p class="text-xs text-gray-400 mb-8 relative z-10">Tim support kami siap membantu menyelesaikan masalahmu kapan saja.</p>
                <a href="https://wa.me/6281234567890?text=Halo%20Admin%20GameVault,%20saya%20butuh%20bantuan..." target="_blank" class="w-24 h-24 bg-gradient-to-br from-[#7C3AED] to-[#5B21B6] hover:brightness-110 text-white font-bold rounded-2xl shadow-[0_0_25px_rgba(124,58,237,0.4)] transition-all hover:scale-105 flex flex-col items-center justify-center gap-2 relative z-10">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="text-[11px] leading-tight font-black uppercase tracking-wider">Chat<br>Teknisi</span>
                </a>
            </div>
        </div>

        <footer class="mt-16 text-center text-xs text-gray-600 pb-8">
            &copy; 2026 GameVault. Semua hak dilindungi.
        </footer>
    </div>

    <script>
        // Mesin Accordion FAQ
        function toggleFaq(btn) {
            var content = btn.nextElementSibling;
            var icon = btn.querySelector('svg');
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>


    <script>
</script>
@include('components.toast-notification')
</body>

</html>