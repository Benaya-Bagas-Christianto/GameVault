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
    
    <title>Riwayat Pembelian - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505 !important; color: #FFFFFF; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .timeline-line::before {
            content: ''; position: absolute; left: 11px; top: 0; bottom: 0; width: 2px;
            background: rgba(255, 255, 255, 0.05); z-index: 0;
        }
        .trx-card { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    </style>

    
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body class="flex flex-col h-screen overflow-hidden antialiased selection:bg-[#7C3AED] selection:text-white">

    {{-- NAVBAR ATAS --}}
        <header class="h-20 border-b border-white/5 flex items-center justify-between px-6 z-30 sticky top-0 bg-[#0A0B0E]/95 backdrop-blur-md flex-shrink-0">
        {{-- Kiri: Logo & Search --}}
        <div class="flex-1 flex justify-start items-center gap-6">
            <a href="/" class="flex-shrink-0 flex items-center group">
                <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault Logo" class="h-6 sm:h-7 lg:h-8 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)] group-hover:drop-shadow-[0_0_25px_rgba(124,58,237,1)] transition-all duration-300">
            </a>
            <form action="/search" method="GET" class="w-full max-w-sm relative hidden md:block">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari game favorit kamu..."
                    class="w-full bg-[#12151C] border border-white/10 text-sm text-white px-4 py-2.5 rounded-lg focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-all placeholder-gray-500 pl-10 cursor-text">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 transition-colors pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </form>
        </div>

        {{-- Tengah: Navigasi --}}
        <nav class="flex-1 hidden lg:flex items-center justify-center gap-8 xl:gap-10 text-sm font-medium h-full">
            <a href="/" class="{{ request()->is('/') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Beranda</a>
            <a href="/kategori" class="{{ request()->is('kategori') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Kategori</a>
            @if(request()->is('/') || request()->is('kategori'))
                <a href="#" id="nav-bantuan" onclick="toggleBantuan(event)" class="text-gray-400 hover:text-white border-b-2 border-transparent pb-7 pt-7 transition-all relative">Bantuan</a>
            @else
                <a href="/bantuan" class="{{ request()->is('bantuan') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Bantuan</a>
            @endif
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
                            
                            {
                                let isLoggedIn = @json(Auth::check());
                                if (!isLoggedIn) return;
                                let cachedCount = parseInt(localStorage.getItem('cartCount')) || 0;
                                let badgeInit = document.getElementById('globalCartBadge');
                                if (badgeInit) {
                                    badgeInit.innerText = cachedCount;
                                    badgeInit.style.setProperty('display', cachedCount > 0 ? 'flex' : 'none', 'important');
                                }
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
                        
                        {
                            let isLoggedIn = @json(Auth::check());
                            if (!isLoggedIn) return;
                            let cachedWishlist = localStorage.getItem('wishlist');
                            let wishlist = JSON.parse(cachedWishlist) || [];
                            let cachedCount = wishlist.length;
                            let badges = document.querySelectorAll('.globalWishlistBadge, #globalWishlistBadge');
                            badges.forEach(badgeInit => {
                                badgeInit.innerText = cachedCount;
                                badgeInit.style.setProperty('display', cachedCount > 0 ? 'flex' : 'none', 'important');
                            });
                        }
                    };
                    window.addEventListener('pageshow', window.syncWishlistBadge);
                </script>

            <div class="h-6 w-px bg-white/10 mx-1 hidden sm:block"></div>
            @auth
            <div class="relative cursor-pointer" onclick="toggleSettings()" id="settingsBtn">
                <div class="flex items-center gap-3 border border-white/5 py-1.5 pl-1.5 pr-4 rounded-full" style="background-color: #12151C !important;">
                    @if(Auth::user()->foto)
                    <img src="{{ asset('assets/profile/' . Auth::user()->foto) }}" class="w-8 h-8 rounded-full object-cover border border-purple-500/50">
                    @else
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold !text-white" style="background: linear-gradient(135deg, #7C3AED 0%, #4C1D95 100%);">{{ strtoupper(substr(Auth::user()->username ?? 'U', 0, 1)) }}</div>
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

    <div class="flex-1 flex overflow-hidden">
        {{-- SIDEBAR KIRI --}}
        <aside class="w-[240px] flex-shrink-0 border-r border-[#1f1f1f] hidden lg:flex flex-col bg-[#0A0C10] p-6 space-y-2">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 pl-4">Pengaturan Akun</p>
            <a href="/profil" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g><g><path d="M80,71.2V74c0,3.3-2.7,6-6,6H26c-3.3,0-6-2.7-6-6v-2.8c0-7.3,8.5-11.7,16.5-15.2c0.3-0.1,0.5-0.2,0.8-0.4 c0.6-0.3,1.3-0.3,1.9,0.1C42.4,57.8,46.1,59,50,59c3.9,0,7.6-1.2,10.8-3.2c0.6-0.4,1.3-0.4,1.9-0.1c0.3,0.1,0.5,0.2,0.8,0.4 C71.5,59.5,80,63.9,80,71.2z"/></g><g><ellipse cx="50" cy="36.5" rx="14.9" ry="16.5"/></g></g></svg> Profil Saya
            </a>
            <a href="/library" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-1)"><g><g><path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333 h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333 c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333 C235.943,223.156,226.391,213.605,214.609,213.605z"/><path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96 c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598 c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168 c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056 c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411 c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831 c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43 l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228 c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z M455.757,403.285l-69.566-62.373 c-14.316-12.852-32.697-19.956-51.767-19.956H181.592c-19.07,0-37.45,7.104-51.777,19.965l-69.758,62.544 c-19.658-18.052-21.296-61.46-5.213-122.832c12.346-47.111,33.478-98.672,51.546-134.307c1.399-2.752,2.112-5.086,3.217-9.411 c0.319-1.249,1.62-6.572,1.952-7.876c0.16-0.629,0.314-1.22,0.461-1.772l50.992-19.374c1.186,1.381,2.463,2.836,3.793,4.303 c1.113,1.222,1.113,1.222,2.273,2.457c9.927,10.492,13.691,13.62,24.204,13.62h128c10.147,0,13.785-2.861,24.417-13.27 c1.094-1.079,1.094-1.079,2.155-2.148c1.719-1.742,3.365-3.468,4.858-5.073l51.321,19.499c0.151,0.56,0.308,1.159,0.471,1.797 c0.382,1.493,1.69,6.836,1.931,7.787c1.066,4.206,1.73,6.435,2.992,9.058c18.908,39.428,38.104,87.404,50.193,134.082 C475.42,341.016,474.252,384.613,455.757,403.285z"/><path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333 c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z"/><path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333 S354.385,234.938,342.609,234.938z"/><path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S311.719,192.271,299.943,192.271z"/><path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S397.052,192.271,385.276,192.271z"/></g></g></g></svg> Library Game
            </a>
            <a href="/orders" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#7C3AED]/10 border border-[#7C3AED]/30 text-[#a78bfa] transition-all text-sm font-bold shadow-[0_0_15px_rgba(124,58,237,0.1)]">
                <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="6" width="18" height="13" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 10H20.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 15H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Riwayat Transaksi
            </a>
            <a href="/wishlist" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z"/></svg> Wishlist
            </a>
            <div class="mt-auto pt-6 border-t border-[#1f1f1f]">
                <a href="/logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-500/10 transition-all text-sm font-medium">
                    <svg class="w-5 h-5 stroke-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 16.9998L21 11.9998M21 11.9998L16 6.99982M21 11.9998H9M12 16.9998C12 17.2954 12 17.4432 11.989 17.5712C11.8748 18.9018 10.8949 19.9967 9.58503 20.2571C9.45903 20.2821 9.31202 20.2985 9.01835 20.3311L7.99694 20.4446C6.46248 20.6151 5.69521 20.7003 5.08566 20.5053C4.27293 20.2452 3.60942 19.6513 3.26118 18.8723C3 18.288 3 17.5161 3 15.9721V8.02751C3 6.48358 3 5.71162 3.26118 5.12734C3.60942 4.3483 4.27293 3.75442 5.08566 3.49435C5.69521 3.29929 6.46246 3.38454 7.99694 3.55503L9.01835 3.66852C9.31212 3.70117 9.45901 3.71749 9.58503 3.74254C10.8949 4.00297 11.8748 5.09786 11.989 6.42843C12 6.55645 12 6.70424 12 6.99982" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Keluar Akun
                </a>
            </div>
        </aside>

        {{-- KONTEN UTAMA RIWAYAT --}}
        <main class="flex-1 overflow-y-auto hide-scrollbar p-6 lg:p-10 bg-[#050505]">
            <div class="max-w-5xl mx-auto pb-10">
                
                <div class="mb-8 border-b border-[#1f1f1f] pb-6">
                    <h1 class="text-3xl font-black text-white tracking-widest uppercase mb-1">Riwayat Pembelian</h1>
                    <p class="text-gray-500 text-sm">Semua transaksi dan pembelian game yang pernah kamu lakukan.</p>
                </div>

                {{-- STATISTIK --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                    <div class="bg-[#0A0C10] border border-[#1f1f1f] p-5 rounded-2xl">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-purple-500"><svg class="w-6 h-6 stroke-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="6" width="18" height="13" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 10H20.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 15H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Transaksi</p>
                        </div>
                        <p class="text-2xl font-black text-white">{{ $total_transaksi }}</p>
                    </div>
                    
                    <div class="bg-[#0A0C10] border border-[#1f1f1f] p-5 rounded-2xl">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-green-500">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.7255 17.1019C11.6265 16.8844 11.4215 16.7257 11.1734 16.6975C10.9633 16.6735 10.7576 16.6285 10.562 16.5636C10.4743 16.5341 10.392 16.5019 10.3158 16.4674L10.4424 16.1223C10.5318 16.1622 10.6239 16.1987 10.7182 16.2317L10.7221 16.2331L10.7261 16.2344C11.0287 16.3344 11.3265 16.3851 11.611 16.3851C11.8967 16.3851 12.1038 16.3468 12.2629 16.2647L12.2724 16.2598L12.2817 16.2544C12.5227 16.1161 12.661 15.8784 12.661 15.6021C12.661 15.2955 12.4956 15.041 12.2071 14.9035C12.062 14.8329 11.8559 14.7655 11.559 14.6917C11.2545 14.6147 10.9987 14.533 10.8003 14.4493C10.6553 14.3837 10.5295 14.279 10.4161 14.1293C10.3185 13.9957 10.2691 13.7948 10.2691 13.5319C10.2691 13.2147 10.3584 12.9529 10.5422 12.7315C10.7058 12.5375 10.9381 12.4057 11.2499 12.3318C11.4812 12.277 11.6616 12.1119 11.7427 11.8987C11.8344 12.1148 12.0295 12.2755 12.2723 12.3142C12.4751 12.3465 12.6613 12.398 12.8287 12.4677L12.7122 12.8059C12.3961 12.679 12.085 12.6149 11.7841 12.6149C10.7848 12.6149 10.7342 13.3043 10.7342 13.4425C10.7342 13.7421 10.896 13.9933 11.1781 14.1318L11.186 14.1357L11.194 14.1393C11.3365 14.2029 11.5387 14.2642 11.8305 14.3322C12.1322 14.4004 12.3838 14.4785 12.5815 14.5651L12.5856 14.5669L12.5897 14.5686C12.7365 14.6297 12.8624 14.7317 12.9746 14.8805L12.9764 14.8828L12.9782 14.8852C13.0763 15.012 13.1261 15.2081 13.1261 15.4681C13.1261 15.7682 13.0392 16.0222 12.8604 16.2447C12.7053 16.4377 12.4888 16.5713 12.1983 16.6531C11.974 16.7163 11.8 16.8878 11.7255 17.1019Z" fill="currentColor"/>
                                    <path d="M11.9785 18H11.497C11.3893 18 11.302 17.9105 11.302 17.8V17.3985C11.302 17.2929 11.2219 17.2061 11.1195 17.1944C10.8757 17.1667 10.6399 17.115 10.412 17.0394C10.1906 16.9648 9.99879 16.8764 9.83657 16.7739C9.76202 16.7268 9.7349 16.6312 9.76572 16.5472L10.096 15.6466C10.1405 15.5254 10.284 15.479 10.3945 15.5417C10.5437 15.6262 10.7041 15.6985 10.8755 15.7585C11.131 15.8429 11.3762 15.8851 11.611 15.8851C11.8129 15.8851 11.9572 15.8628 12.0437 15.8181C12.1302 15.7684 12.1735 15.6964 12.1735 15.6021C12.1735 15.4929 12.1158 15.411 12.0004 15.3564C11.8892 15.3018 11.7037 15.2422 11.4442 15.1777C11.1104 15.0933 10.8323 15.0039 10.6098 14.9096C10.3873 14.8103 10.1936 14.6514 10.0288 14.433C9.86396 14.2096 9.78156 13.9092 9.78156 13.5319C9.78156 13.095 9.91136 12.7202 10.1709 12.4074C10.4049 12.13 10.7279 11.9424 11.1401 11.8447C11.2329 11.8227 11.302 11.7401 11.302 11.6425V11.2C11.302 11.0895 11.3893 11 11.497 11H11.9785C12.0862 11 12.1735 11.0895 12.1735 11.2V11.6172C12.1735 11.7194 12.2487 11.8045 12.3471 11.8202C12.7082 11.8777 13.0255 11.9866 13.2989 12.1469C13.3765 12.1924 13.4073 12.2892 13.3775 12.3756L13.0684 13.2725C13.0275 13.3914 12.891 13.4417 12.7812 13.3849C12.433 13.2049 12.1007 13.1149 11.7841 13.1149C11.4091 13.1149 11.2216 13.2241 11.2216 13.4425C11.2216 13.5468 11.2773 13.6262 11.3885 13.6809C11.4998 13.7305 11.6831 13.7851 11.9386 13.8447C12.2682 13.9192 12.5464 14.006 12.773 14.1053C12.9996 14.1996 13.1953 14.356 13.3602 14.5745C13.5291 14.7929 13.6136 15.0908 13.6136 15.4681C13.6136 15.8851 13.4879 16.25 13.2365 16.5628C13.0176 16.8354 12.7145 17.0262 12.3274 17.1353C12.2384 17.1604 12.1735 17.2412 12.1735 17.3358V17.8C12.1735 17.9105 12.0862 18 11.9785 18Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.59235 5H13.8141C14.8954 5 14.3016 6.664 13.8638 7.679L13.3656 8.843L13.2983 9C13.7702 8.97651 14.2369 9.11054 14.6282 9.382C16.0921 10.7558 17.2802 12.4098 18.1256 14.251C18.455 14.9318 18.5857 15.6958 18.5019 16.451C18.4013 18.3759 16.8956 19.9098 15.0182 20H8.38823C6.51033 19.9125 5.0024 18.3802 4.89968 16.455C4.81587 15.6998 4.94656 14.9358 5.27603 14.255C6.12242 12.412 7.31216 10.7565 8.77823 9.382C9.1696 9.11054 9.63622 8.97651 10.1081 9L10.0301 8.819L9.54263 7.679C9.1068 6.664 8.5101 5 9.59235 5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.2983 9.75C13.7125 9.75 14.0483 9.41421 14.0483 9C14.0483 8.58579 13.7125 8.25 13.2983 8.25V9.75ZM10.1081 8.25C9.69391 8.25 9.35812 8.58579 9.35812 9C9.35812 9.41421 9.69391 9.75 10.1081 9.75V8.25ZM15.9776 8.64988C16.3365 8.44312 16.4599 7.98455 16.2531 7.62563C16.0463 7.26671 15.5878 7.14336 15.2289 7.35012L15.9776 8.64988ZM13.3656 8.843L13.5103 9.57891L13.5125 9.57848L13.3656 8.843ZM10.0301 8.819L10.1854 8.08521L10.1786 8.08383L10.0301 8.819ZM8.166 7.34357C7.80346 7.14322 7.34715 7.27469 7.1468 7.63722C6.94644 7.99976 7.07791 8.45607 7.44045 8.65643L8.166 7.34357ZM13.2983 8.25H10.1081V9.75H13.2983V8.25ZM15.2289 7.35012C14.6019 7.71128 13.9233 7.96683 13.2187 8.10752L13.5125 9.57848C14.3778 9.40568 15.2101 9.09203 15.9776 8.64988L15.2289 7.35012ZM13.2209 8.10709C12.2175 8.30441 11.1861 8.29699 10.1854 8.08525L9.87486 9.55275C11.0732 9.80631 12.3086 9.81521 13.5103 9.57891L13.2209 8.10709ZM10.1786 8.08383C9.47587 7.94196 8.79745 7.69255 8.166 7.34357L7.44045 8.65643C8.20526 9.0791 9.02818 9.38184 9.88169 9.55417L10.1786 8.08383Z" fill="currentColor"/>
                                </svg>
                            </span>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Pengeluaran</p>
                        </div>
                        <p class="text-lg font-black text-green-400">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</p>
                    </div>
                    
                    {{-- GAME DIMILIKI (SEKARANG BISA DIKLIK) --}}
                    <a href="/library" class="bg-[#0A0C10] border border-[#1f1f1f] p-5 rounded-2xl block hover:border-[#7C3AED]/50 transition-colors group relative overflow-hidden">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-blue-500 group-hover:scale-110 transition-transform"><svg class="w-8 h-8 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-1)"><g><g><path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333 h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333 c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333 C235.943,223.156,226.391,213.605,214.609,213.605z"/><path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96 c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598 c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168 c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056 c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411 c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831 c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43 l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228 c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z M455.757,403.285l-69.566-62.373 c-14.316-12.852-32.697-19.956-51.767-19.956H181.592c-19.07,0-37.45,7.104-51.777,19.965l-69.758,62.544 c-19.658-18.052-21.296-61.46-5.213-122.832c12.346-47.111,33.478-98.672,51.546-134.307c1.399-2.752,2.112-5.086,3.217-9.411 c0.319-1.249,1.62-6.572,1.952-7.876c0.16-0.629,0.314-1.22,0.461-1.772l50.992-19.374c1.186,1.381,2.463,2.836,3.793,4.303 c1.113,1.222,1.113,1.222,2.273,2.457c9.927,10.492,13.691,13.62,24.204,13.62h128c10.147,0,13.785-2.861,24.417-13.27 c1.094-1.079,1.094-1.079,2.155-2.148c1.719-1.742,3.365-3.468,4.858-5.073l51.321,19.499c0.151,0.56,0.308,1.159,0.471,1.797 c0.382,1.493,1.69,6.836,1.931,7.787c1.066,4.206,1.73,6.435,2.992,9.058c18.908,39.428,38.104,87.404,50.193,134.082 C475.42,341.016,474.252,384.613,455.757,403.285z"/><path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333 c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z"/><path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333 S354.385,234.938,342.609,234.938z"/><path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S311.719,192.271,299.943,192.271z"/><path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S397.052,192.271,385.276,192.271z"/></g></g></g></svg></span>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest group-hover:text-gray-300 transition-colors">Game Dimiliki</p>
                        </div>
                        <p class="text-2xl font-black text-white group-hover:text-[#a78bfa] transition-colors">{{ $total_game }}</p>
                        <span class="absolute right-4 bottom-4 text-xs font-bold text-[#7C3AED] opacity-0 group-hover:opacity-100 transition-opacity">Lihat →</span>
                    </a>

                    <div class="bg-[#0A0C10] border border-[#1f1f1f] p-5 rounded-2xl">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-pink-500"><svg class="w-5 h-5 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5.67326018,0 C6.0598595,0 6.37326018,0.31324366 6.37326018,0.699649298 L6.373,2.009 L13.89,2.009 L13.8901337,0.708141199 C13.8901337,0.321735562 14.2035343,0.00849190182 14.5901337,0.00849190182 C14.976733,0.00849190182 15.2901337,0.321735562 15.2901337,0.708141199 L15.29,2.009 L18,2.00901806 C19.1045695,2.00901806 20,2.90399995 20,4.00801605 L20,18.001002 C20,19.1050181 19.1045695,20 18,20 L2,20 C0.8954305,20 0,19.1050181 0,18.001002 L0,4.00801605 C0,2.90399995 0.8954305,2.00901806 2,2.00901806 L4.973,2.009 L4.97326018,0.699649298 C4.97326018,0.31324366 5.28666085,0 5.67326018,0 Z M1.4,7.742 L1.4,18.001002 C1.4,18.3322068 1.66862915,18.6007014 2,18.6007014 L18,18.6007014 C18.3313708,18.6007014 18.6,18.3322068 18.6,18.001002 L18.6,7.756 L1.4,7.742 Z M6.66666667,14.6186466 L6.66666667,16.284778 L5,16.284778 L5,14.6186466 L6.66666667,14.6186466 Z M10.8333333,14.6186466 L10.8333333,16.284778 L9.16666667,16.284778 L9.16666667,14.6186466 L10.8333333,14.6186466 Z M15,14.6186466 L15,16.284778 L13.3333333,16.284778 L13.3333333,14.6186466 L15,14.6186466 Z M6.66666667,10.6417617 L6.66666667,12.3078931 L5,12.3078931 L5,10.6417617 L6.66666667,10.6417617 Z M10.8333333,10.6417617 L10.8333333,12.3078931 L9.16666667,12.3078931 L9.16666667,10.6417617 L10.8333333,10.6417617 Z M15,10.6417617 L15,12.3078931 L13.3333333,12.3078931 L13.3333333,10.6417617 L15,10.6417617 Z M4.973,3.408 L2,3.40831666 C1.66862915,3.40831666 1.4,3.67681122 1.4,4.00801605 L1.4,6.343 L18.6,6.357 L18.6,4.00801605 C18.6,3.67681122 18.3313708,3.40831666 18,3.40831666 L15.29,3.408 L15.2901337,4.33697436 C15.2901337,4.72338 14.976733,5.03662366 14.5901337,5.03662366 C14.2035343,5.03662366 13.8901337,4.72338 13.8901337,4.33697436 L13.89,3.408 L6.373,3.408 L6.37326018,4.32848246 C6.37326018,4.7148881 6.0598595,5.02813176 5.67326018,5.02813176 C5.28666085,5.02813176 4.97326018,4.7148881 4.97326018,4.32848246 L4.973,3.408 Z"/></svg></span>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Trx Terakhir</p>
                        </div>
                        <p class="text-sm font-bold text-white">{{ $transaksi->first() ? $transaksi->first()->created_at->format('d M Y') : '-' }}</p>
                    </div>
                </div>

                {{-- FILTER TABS SIMPEL --}}
                <div class="flex flex-wrap items-center gap-4 mb-8 bg-[#0A0C10] p-2 rounded-xl border border-[#1f1f1f] w-fit" id="filterTabs">
                    <button data-filter="All" class="filter-btn px-4 py-2 bg-[#7C3AED]/20 text-[#a78bfa] rounded-lg text-xs font-bold transition-colors">Semua Transaksi</button>
                    <button data-filter="Success" class="filter-btn px-4 py-2 text-gray-500 hover:text-white rounded-lg text-xs font-bold transition-colors"><span class="text-green-500 mr-1">●</span> Berhasil</button>
                    <button data-filter="Pending" class="filter-btn px-4 py-2 text-gray-500 hover:text-white rounded-lg text-xs font-bold transition-colors"><span class="text-yellow-500 mr-1">●</span> Pending</button>
                    <button data-filter="Failed" class="filter-btn px-4 py-2 text-gray-500 hover:text-white rounded-lg text-xs font-bold transition-colors"><span class="text-red-500 mr-1">●</span> Dibatalkan</button>
                </div>

                {{-- LIST RIWAYAT (TIMELINE) --}}
                <div class="relative timeline-line pl-8 space-y-6">
                    @forelse($transaksi as $t)
                        <div class="relative group trx-card" data-status="{{ in_array($t->status, ['Failed', 'Cancelled']) ? 'Failed' : $t->status }}">
                            {{-- Titik Timeline --}}
                            <div class="absolute -left-[37px] top-6 w-4 h-4 rounded-full border-4 border-[#050505] z-10 {{ $t->status == 'Success' ? 'bg-green-500' : ($t->status == 'Pending' ? 'bg-yellow-500' : 'bg-red-500') }}"></div>

                            {{-- Kartu Transaksi DENGAN GAMBAR --}}
                            <div class="bg-[#12151C] border border-[#1f1f1f] rounded-2xl p-4 flex flex-col md:flex-row items-start md:items-center gap-5 hover:border-[#7C3AED]/50 transition-colors">
                                
                                {{-- GAMBAR GAME --}}
                                <img src="{{ asset('assets/' . $t->game_image) }}" onerror="this.src='/assets/no-image.jpg'" class="w-24 h-24 object-cover rounded-xl border border-[#1f1f1f] shadow-lg flex-shrink-0 hidden sm:block">

                                {{-- Info Kiri --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-xs font-mono font-bold text-gray-400 bg-[#0A0C10] px-2 py-1 rounded border border-[#1f1f1f]">#ORD-{{ $t->id }}</span>
                                        <span class="text-xs text-gray-500">{{ $t->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">{{ $t->game_name }}</h3>
                                    
                                    @if($t->status == 'Success')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 bg-green-500/10 text-green-400 border border-green-500/20 rounded font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Sukses
                                        </span>
                                    @elseif($t->status == 'Pending')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full"></span> Menunggu Pembayaran
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 bg-red-500/10 text-red-400 border border-red-500/20 rounded font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span> Dibatalkan / Gagal
                                        </span>
                                    @endif
                                </div>

                                {{-- Info Kanan (Harga & Tombol) --}}
                                <div class="flex flex-col md:items-end w-full md:w-auto gap-3 border-t md:border-t-0 border-[#1f1f1f] pt-4 md:pt-0 mt-2 md:mt-0">
                                    <div class="md:text-right">
                                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Total Tagihan</p>
                                        <p class="text-xl font-black text-white">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex gap-2 w-full md:w-auto mt-3 md:mt-0 justify-end md:justify-start">
                                        @if($t->status == 'Pending')
                                            <form action="/orders/{{ $t->id }}/cancel" method="POST" class="flex-1 md:flex-none" id="form-cancel-{{ $t->id }}">
                                                @csrf
                                                <button type="button" onclick="openCancelModal('{{ $t->id }}')" class="w-full px-4 py-2 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-lg transition-colors">
                                                    Batalkan
                                                </button>
                                                @if($t->snap_token)
                                                <button type="button" onclick="resumePayment('{{ $t->snap_token }}', '{{ $t->id }}')" class="w-full mt-2 px-4 py-2 bg-[#7C3AED]/20 border border-[#7C3AED]/50 hover:bg-[#7C3AED] hover:text-white text-[#7C3AED] text-xs font-bold rounded-lg transition-colors">
                                                    Selesaikan Pembayaran
                                                </button>
                                                @endif
                                            </form>
                                        @endif
                                        <button onclick="showTransactionDetail({{ $t->id }})" class="flex-1 md:flex-none px-4 py-2 bg-[#0A0C10] border border-[#1f1f1f] hover:border-gray-500 text-gray-300 text-xs font-bold rounded-lg transition-colors">
                                            Lihat Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-[#12151C] border border-[#1f1f1f] rounded-2xl p-10 text-center relative z-10">
                            <span class="mb-3 flex justify-center opacity-30"><svg class="w-12 h-12 stroke-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="6" width="18" height="13" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 10H20.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 15H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <h3 class="text-white font-bold mb-1">Belum Ada Transaksi</h3>
                            <p class="text-sm text-gray-500">Kamu belum pernah melakukan pembelian game apapun.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    {{-- MODAL DETAIL TRANSAKSI --}}
    <div id="trxDetailModal" class="fixed inset-0 z-[150] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 hidden">
        <div id="trxDetailContent" class="bg-[#12151C] border border-[#1f1f1f] rounded-3xl w-[90%] max-w-lg max-h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300">
            {{-- Header --}}
            <div class="p-6 border-b border-[#1f1f1f] flex justify-between items-center flex-shrink-0">
                <div>
                    <h3 class="text-xl font-black text-white tracking-widest">DETAIL TRANSAKSI</h3>
                    <p id="modalTrxId" class="text-sm text-gray-500 font-mono mt-1">#ORD-...</p>
                </div>
                <button onclick="closeTrxDetail()" class="text-gray-500 hover:text-red-500 hover:border-red-500/50 active:text-red-600 active:border-red-600 active:bg-red-500/10 transition-all bg-[#0A0C10] p-2 rounded-lg border border-[#1f1f1f] group">
                    <svg class="w-5 h-5 group-active:scale-95 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            {{-- Content Scrollable --}}
            <div class="p-6 overflow-y-auto hide-scrollbar flex-1">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Tanggal Pembelian</p>
                        <p id="modalTrxDate" class="text-sm font-bold text-white">...</p>
                    </div>
                    <div id="modalTrxStatus">
                        {{-- Badge Status --}}
                    </div>
                </div>

                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-3 border-b border-[#1f1f1f] pb-2">Item yang dibeli</p>
                <div id="modalTrxItems" class="space-y-4 mb-6">
                    {{-- Loader atau Item --}}
                </div>

                <div class="bg-[#0A0C10] border border-[#1f1f1f] p-4 rounded-xl flex justify-between items-center">
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Total Tagihan</p>
                    <p id="modalTrxTotal" class="text-lg font-black text-[#7C3AED]">Rp 0</p>
                </div>
            </div>

            {{-- Footer / Actions --}}
            <div class="p-6 border-t border-[#1f1f1f] flex gap-3 flex-shrink-0" id="modalTrxActions">
                <button type="button" onclick="closeTrxDetail()" class="flex-1 px-4 py-3 bg-[#0A0C10] border border-[#1f1f1f] text-gray-300 font-bold rounded-xl hover:bg-white/5 transition-colors text-sm">Tutup</button>
                {{-- Tombol Download Invoice (dinamis) --}}
            </div>
        </div>
    </div>

    <div id="cancelModal" class="fixed inset-0 z-[200] items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" style="display:none;">
        <div class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2">Batalkan Pesanan?</h3>
            <p class="text-gray-400 text-center text-sm mb-6 leading-relaxed">Yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dikembalikan.</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()" class="flex-1 px-4 py-3 bg-transparent border border-white/10 text-gray-300 font-bold rounded-xl hover:bg-white/5 transition-colors text-sm">Kembali</button>
                <button type="button" id="confirmCancelBtn" class="flex-1 px-4 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors text-sm text-center flex items-center justify-center">Ya, Batalkan</button>
            </div>
        </div>
    </div>



    <script>
        let cancelFormId = null;

        function openCancelModal(orderId) {
            cancelFormId = `form-cancel-${orderId}`;
            const modal = document.getElementById('cancelModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
                modal.querySelector('div').classList.add('scale-100');
            }, 10);
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            modal.querySelector('div').classList.remove('scale-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                cancelFormId = null;
            }, 300);
        }

        document.getElementById('confirmCancelBtn').addEventListener('click', function() {
            if (cancelFormId) {
                const btn = this;
                btn.innerHTML = '<svg class="animate-spin w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
                btn.disabled = true;
                document.getElementById(cancelFormId).submit();
            }
        });

        // Close details modal logic...
        function showTransactionDetail(id) {

            const modal = document.getElementById('trxDetailModal');
            const content = document.getElementById('trxDetailContent');
            const itemsContainer = document.getElementById('modalTrxItems');
            
            // Reset isi modal
            document.getElementById('modalTrxId').innerText = '#ORD-' + id;
            document.getElementById('modalTrxDate').innerText = 'Memuat...';
            document.getElementById('modalTrxTotal').innerText = 'Rp 0';
            document.getElementById('modalTrxStatus').innerHTML = '';
            itemsContainer.innerHTML = '<div class="text-center py-4 text-gray-500 text-sm">Memuat data...</div>';
            document.getElementById('modalTrxActions').innerHTML = `<button type="button" onclick="closeTrxDetail()" class="flex-1 px-4 py-3 bg-[#0A0C10] border border-[#1f1f1f] text-gray-300 font-bold rounded-xl hover:bg-white/5 transition-colors text-sm">Tutup</button>`;

            // Tampilkan Modal (Loading state)
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);

            // Fetch AJAX
            fetch(`/orders/${id}/detail`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const trx = data.transaksi;
                    document.getElementById('modalTrxDate').innerText = trx.created_at;
                    document.getElementById('modalTrxTotal').innerText = 'Rp ' + parseInt(trx.total_bayar).toLocaleString('id-ID');
                    
                    // Status Badge
                    if(trx.status === 'Success') {
                        document.getElementById('modalTrxStatus').innerHTML = `<span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 bg-green-500/10 text-green-400 border border-green-500/20 rounded font-bold uppercase tracking-wider"><span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Sukses</span>`;
                        // Tambah tombol download
                        document.getElementById('modalTrxActions').innerHTML += `<a href="/invoice/download/${trx.id}" class="flex-1 px-4 py-3 bg-purple-500/10 border border-purple-500/20 text-purple-400 font-bold rounded-xl hover:bg-purple-500 hover:text-white transition-colors text-sm text-center">Unduh Invoice</a>`;
                    } else if(trx.status === 'Pending') {
                        document.getElementById('modalTrxStatus').innerHTML = `<span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded font-bold uppercase tracking-wider"><span class="w-1.5 h-1.5 bg-yellow-400 rounded-full"></span> Pending</span>`;
                    } else {
                        document.getElementById('modalTrxStatus').innerHTML = `<span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 bg-red-500/10 text-red-400 border border-red-500/20 rounded font-bold uppercase tracking-wider"><span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span> Dibatalkan / Gagal</span>`;
                    }

                    // Items List
                    let itemsHtml = '';
                    data.items.forEach(item => {
                        let imgUrl = item.image ? `/assets/${item.image}` : '/assets/no-image.jpg';
                        let priceFormatted = parseInt(item.harga_saat_beli).toLocaleString('id-ID');
                        
                        itemsHtml += `
                            <div class="flex items-center gap-4 bg-[#0A0C10] p-3 rounded-xl border border-[#1f1f1f]">
                                <img src="${imgUrl}" class="w-16 h-16 object-cover rounded-lg border border-[#1f1f1f]" onerror="this.src='/assets/no-image.jpg'">
                                <div class="flex-1">
                                    <p class="text-white font-bold text-sm mb-1">${item.name}</p>
                                    <p class="text-xs text-gray-500">Rp ${priceFormatted}</p>
                                </div>
                            </div>
                        `;
                    });
                    itemsContainer.innerHTML = itemsHtml;
                } else {
                    itemsContainer.innerHTML = `<div class="text-center py-4 text-red-500 text-sm">${data.message}</div>`;
                }
            })
            .catch(err => {
                itemsContainer.innerHTML = `<div class="text-center py-4 text-red-500 text-sm">Gagal memuat detail.</div>`;
            });
        }

        function closeTrxDetail() {
            const modal = document.getElementById('trxDetailModal');
            const content = document.getElementById('trxDetailContent');
            
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }



        // JS Logic untuk Filter Tabs
        const filterBtns = document.querySelectorAll('.filter-btn');
        const trxCards = document.querySelectorAll('.trx-card');
        const emptyStateContainer = document.querySelector('.timeline-line');
        const origEmptyState = emptyStateContainer.innerHTML.includes('Belum Ada Transaksi');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.getAttribute('data-filter');
                
                // Ubah style tab aktif
                filterBtns.forEach(b => {
                    b.classList.remove('bg-[#7C3AED]/20', 'text-[#a78bfa]');
                    b.classList.add('text-gray-500');
                });
                btn.classList.add('bg-[#7C3AED]/20', 'text-[#a78bfa]');
                btn.classList.remove('text-gray-500');

                // Tampilkan/Sembunyikan card
                let visibleCount = 0;
                trxCards.forEach(card => {
                    if (filter === 'All' || card.getAttribute('data-status') === filter) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Cek pesan kosong (hanya kalau sebelumnya ada data)
                if(!origEmptyState) {
                    let emptyMsg = document.getElementById('filterEmptyMsg');
                    if(visibleCount === 0) {
                        if(!emptyMsg) {
                            emptyMsg = document.createElement('div');
                            emptyMsg.id = 'filterEmptyMsg';
                            emptyMsg.className = 'bg-[#12151C] border border-[#1f1f1f] rounded-2xl p-10 text-center mt-4';
                            emptyMsg.innerHTML = '<p class="text-sm text-gray-500">Tidak ada transaksi yang sesuai dengan filter ini.</p>';
                            emptyStateContainer.appendChild(emptyMsg);
                        }
                        emptyMsg.style.display = 'block';
                    } else {
                        if(emptyMsg) emptyMsg.style.display = 'none';
                    }
                }
            });
        });

        // AUTO-REFRESH UNTUK TRANSAKSI PENDING
        const pendingIds = @json($transaksi->where('status', 'Pending')->pluck('id'));
        if (pendingIds.length > 0) {
            setInterval(() => {
                fetch('/orders/check-statuses', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: pendingIds })
                })
                .then(res => res.json())
                .then(data => {
                    let changed = false;
                    for (let id in data) {
                        if (data[id] === 'Success' || data[id] === 'Failed') {
                            changed = true;
                        }
                    }
                    if (changed) {
                        window.location.reload(); // Reload halaman otomatis!
                    }
                })
                .catch(err => console.error(err));
            }, 3000); // Cek tiap 3 detik
        }
    </script>
    @if(session('msg'))
    {{-- Modal Konfirmasi Sukses/Error --}}
    <div id="sessionModal" class="fixed inset-0 z-[250] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300">
        <div id="sessionModalContent" class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform transition-transform duration-300 scale-100">
            @if(session('status') == 'success')
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-green-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2">Berhasil!</h3>
            @else
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2">Perhatian</h3>
            @endif
            <p class="text-gray-400 text-center text-sm mb-6 leading-relaxed">{{ session('msg') }}</p>
            <button type="button" onclick="closeSessionModal()" class="w-full px-4 py-3 {{ session('status') == 'success' ? 'bg-green-500/10 text-green-500 border border-green-500/20 hover:bg-green-500 hover:text-white' : 'bg-red-500/10 text-red-500 border border-red-500/20 hover:bg-red-500 hover:text-white' }} font-bold rounded-xl transition-colors text-sm text-center">OK</button>
        </div>
    </div>
    <script>
        function closeSessionModal() {
            const modal = document.getElementById('sessionModal');
            const content = document.getElementById('sessionModalContent');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
    @endif


    <script>
        function resumePayment(snapToken, orderId) {
            fetch('/orders/' + orderId + '/cancel-duplicates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                // Background cancel check completed
            });

            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    // Tampilkan modal secara instan
                    showPaymentModal('success', 'Hore! Pembayaran berhasil!', window.location.href);
                    
                    // Tembak server lokal secara asinkron
                    fetch('/checkout/success', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    });
                },
                onPending: function(result) {
                    showPaymentModal('warning', 'Menunggu pembayaran diselesaikan!', window.location.href);
                },
                onError: function(result) {
                    showPaymentModal('error', 'Maaf, pembayaran gagal!', null);
                },
                onClose: function() {
                    // do nothing
                }
            });
        }
    </script>

<div id="paymentModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300">
            <div id="paymentModalIcon" class="flex items-center justify-center w-16 h-16 mx-auto rounded-full mb-4">
            </div>
            <h3 id="paymentModalTitle" class="text-xl font-black text-center text-white mb-2"></h3>
            <p id="paymentModalMessage" class="text-gray-400 text-center text-sm mb-6 leading-relaxed"></p>
            <button id="paymentModalBtn" type="button" class=""></button>
        </div>
    </div>

    <script>
        // FITUR: Tahan pengacakan game saat masuk ke detail
        document.addEventListener('click', function(e) {
            let link = e.target.closest('a[href*="/game/"], div[onclick*="/game/"], button[onclick*="/game/"], .hover-card');
            if (link) {
                document.cookie = "keep_seed=1; path=/; max-age=3600"; // Tahan seed 1 jam untuk navigasi kembali
            }
        });
    
// Fungsi untuk menampilkan Custom Payment Modal
        function showPaymentModal(type, message, redirectUrl = null) {
            const modal = document.getElementById('paymentModal');
            const iconDiv = document.getElementById('paymentModalIcon');
            const titleText = document.getElementById('paymentModalTitle');
            const messageText = document.getElementById('paymentModalMessage');
            const btn = document.getElementById('paymentModalBtn');

            // Reset styling
            iconDiv.className = 'flex items-center justify-center w-16 h-16 mx-auto rounded-full mb-4';

            if (type === 'success') {
                iconDiv.classList.add('bg-green-500/20');
                iconDiv.innerHTML = '<svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                titleText.innerText = 'Berhasil!';
                btn.className = 'w-full px-4 py-3 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-colors mt-6 block text-center';
                btn.innerText = 'Lihat Pesanan';
            } else if (type === 'error') {
                iconDiv.classList.add('bg-red-500/20');
                iconDiv.innerHTML = '<svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                titleText.innerText = 'Gagal!';
                btn.className = 'w-full px-4 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors mt-6 block text-center';
                btn.innerText = 'Tutup';
            } else if (type === 'warning') {
                iconDiv.classList.add('bg-yellow-500/20');
                iconDiv.innerHTML = '<svg class="w-8 h-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                titleText.innerText = 'Menunggu!';
                btn.className = 'w-full px-4 py-3 bg-yellow-500 text-white font-bold rounded-xl hover:bg-yellow-600 transition-colors mt-6 block text-center';
                btn.innerText = 'Lihat Status';
            } else {
                iconDiv.classList.add('bg-blue-500/20');
                iconDiv.innerHTML = '<svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                titleText.innerText = 'Info';
                btn.className = 'w-full px-4 py-3 bg-blue-500 text-white font-bold rounded-xl hover:bg-blue-600 transition-colors mt-6 block text-center';
                btn.innerText = 'OK';
            }

            messageText.innerText = message;

            btn.onclick = function() {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    closePaymentModal();
                }
            };

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
                modal.querySelector('div').classList.add('scale-100');
            }, 10);
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            modal.querySelector('div').classList.remove('scale-100');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
</script>
</body>

</html>
