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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Saya - GameVault</title>
    
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <style>
        body,
        html,
        main,
        section,
        div,
        aside,
        header {
            background-image: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0A0C10 !important;
            color: #FFFFFF !important;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .gv-checkbox {
            appearance: none;
            background-color: rgba(0, 0, 0, 0.5);
            margin: 0;
            font: inherit;
            color: currentColor;
            width: 1.25em;
            height: 1.25em;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.35em;
            display: grid;
            place-content: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .gv-checkbox::before {
            content: "";
            width: 0.7em;
            height: 0.7em;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em white;
            background-color: white;
            transform-origin: center;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        }

        .gv-checkbox:checked {
            background-color: #7C3AED;
            border-color: #7C3AED;
        }

        .gv-checkbox:checked::before {
            transform: scale(1);
        }
    </style>
    {{-- SCRIPT MIDTRANS SNAP --}}
    @php
    $isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    $snapUrl = $isProduction
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = env('MIDTRANS_CLIENT_KEY');
    @endphp
    <script type="text/javascript" src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

    
</head>

<body class="flex flex-col h-screen overflow-hidden antialiased selection:bg-purple-500 selection:text-white" style="background-color: #0A0C10 !important;">
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

        <div class="flex-1 overflow-y-auto hide-scrollbar flex flex-col pt-4 pb-6 pl-6 pr-6 lg:pt-6 lg:pb-10 lg:pl-10 lg:pr-10" style="background-color: #0A0C10 !important;">

            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/5 pb-4 mb-6">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-white">Keranjang Saya</h1>
                        <span id="headerCartCount" class="text-xs font-bold px-2.5 py-1 rounded-md border border-purple-500/30" style="background-color: rgba(124,58,237,0.2) !important; color: #8B5CF6 !important;">0 Item</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer hover:text-white transition-colors">
                            <input type="checkbox" id="selectAllCart" class="gv-checkbox" checked> Pilih Semua
                        </label>
                        <div class="w-px h-4 bg-white/10 mx-1"></div>
                        <button onclick="bukaModalHapus('banyak')" class="flex items-center gap-1.5 text-gray-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span class="hidden sm:inline">Hapus Semua</span>
                        </button>
                    </div>
                </div>

            <div class="flex flex-col xl:flex-row gap-8 xl:gap-10">
                {{-- KONTEN KIRI: LIST KERANJANG --}}
                <div class="flex-1 flex flex-col w-full">

                <p class="text-sm text-gray-500 mb-4">Periksa kembali item yang ada di keranjang sebelum melanjutkan pembayaran.</p>

                {{-- DAFTAR ITEM KERANJANG (DIRENDER OLEH JS) --}}
                <div id="fullCartList" class="space-y-4">
                    <div class="flex flex-col items-center justify-center py-20 text-gray-500">
                        <span class="animate-pulse">Memuat data keranjang...</span>
                    </div>
                </div>

                {{-- SECTION: MUNGKIN KAMU JUGA SUKA --}}
                <div class="mt-16">
                    <h3 class="text-lg font-bold text-white mb-6">Mungkin kamu juga suka</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($games->take(4) as $rec)
                        @php
                        $avg_rating = $rec->reviews->count() > 0 ? round($rec->reviews->avg('rating'), 1) : '0';
                        @endphp
                        <div class="rounded-xl overflow-hidden cursor-pointer group" style="background-color: #12151C !important; border: 1px solid rgba(255,255,255,0.05);" onclick="window.location.href='{{ url('/game/'.$rec->id) }}'">
                            <div class="relative aspect-video overflow-hidden">
                                <img src="{{ asset('assets/' . $rec->image) }}" onerror="this.src='/assets/no-image.jpg'" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all">
                            </div>
                            <div class="p-4">
                                <h4 class="text-sm font-bold text-white line-clamp-1 mb-1">{{ $rec->name }}</h4>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] border border-white/10 px-2 py-0.5 rounded text-gray-400" style="background-color: #1A1D24 !important;">{{ explode(',', $rec->genre)[0] ?? 'Action' }}</span>
                                    <span class="text-[10px] border border-white/10 px-2 py-0.5 rounded text-gray-400" style="background-color: #1A1D24 !important;">{{ explode(',', $rec->platform)[0] ?? 'PC' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 mb-3">
                                    <span class="text-yellow-500 text-xs">★</span>
                                    <span class="text-xs font-bold text-gray-300">{{ $avg_rating }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-white">{{ $rec->price == 0 ? "Gratis" : "Rp " . number_format($rec->price, 0, ',', '.') }}</p>
                                    <button onclick="tambahDariRekomendasi({{ $rec->id }}, this, event)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors" style="background-color: rgba(124,58,237,0.2) !important; color: #8B5CF6 !important;">+</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- KONTEN KANAN: RINGKASAN PESANAN --}}
    <aside class="w-full xl:w-[400px] flex-shrink-0 ml-auto mt-0 xl:mt-[36px]">
        <div class="p-6 rounded-2xl sticky top-6 xl:top-6" style="background-color: #12151C !important; border: 1px solid rgba(255,255,255,0.05);">
            <h2 class="font-bold text-white text-lg mb-6">Ringkasan Pesanan</h2>

            <div class="space-y-4 mb-6 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Item (<span id="summaryCartCount">0</span>)</span>
                    <span class="text-white font-bold" id="summaryCartPrice">Rp 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Diskon</span>
                    <span class="text-red-400 font-bold">- Rp 0</span>
                </div>
            </div>

            <div class="border-t border-white/10 pt-6 mb-6">
                <p class="text-gray-400 text-sm mb-1">Total Pembayaran</p>
                <p id="finalCartTotal" class="text-3xl font-black" style="color: #8B5CF6 !important;">Rp 0</p>
            </div>

            {{-- Form diubah jadi id="checkoutForm" dan tombolnya memicu onclick --}}
            <form id="checkoutForm" style="width: 100%; display: block; margin-top: 16px;">
                <button type="button" onclick="prosesPembayaranMidtrans(this)" id="btnCheckout"
                    style="position: relative !important; display: block !important; width: 100% !important; background-color: #512da8 !important; border-radius: 6px !important; padding: 14px 0 !important; border: none !important; cursor: pointer !important; box-sizing: border-box !important; text-decoration: none !important;">

                    {{-- Teks dipaksa di tengah pakai rata tengah biasa --}}
                    <span style="display: block !important; text-align: center !important; color: #ffffff !important; font-size: 15px !important; font-weight: 500 !important; font-family: sans-serif !important; letter-spacing: 0.5px !important; margin: 0 !important; line-height: 1 !important;">Lanjut ke Pembayaran</span>

                    {{-- Panah dipaksa mutlak di ujung kanan dan di tengah atas-bawah --}}
                    <svg style="position: absolute !important; right: 16px !important; top: 50% !important; transform: translateY(-50%) !important; width: 20px !important; height: 20px !important; color: #ffffff !important; margin: 0 !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"></path>
                    </svg>

                </button>
            </form>

            <div class="mt-8 border-t border-white/10 pt-6 space-y-4">
                <p class="text-xs font-bold text-white mb-3">Metode Pembayaran yang Diterima</p>
                <div class="flex items-center gap-3 grayscale opacity-60">
                    <span class="font-black text-white">VISA</span>
                    <span class="font-black text-white">Mastercard</span>
                    <span class="font-black text-blue-400">DANA</span>
                    <span class="font-black text-purple-400">OVO</span>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-white">Pembayaran Aman</p>
                        <p class="text-[10px] text-gray-500">100% transaksi terenkripsi</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-white">Unduh Cepat</p>
                        <p class="text-[10px] text-gray-500">Server berkecepatan tinggi</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    </div>
    </div>

    {{-- MODAL LOGIN (Mencegah Error AJAX) --}}
    <div id="loginModal" class="hidden fixed inset-0 z-[250] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity duration-300">
        <div class="bg-[#12151C] p-8 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.1)] w-full max-w-md relative">
            <button onclick="document.getElementById('loginModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-2xl font-black text-white text-center mb-2 tracking-wide uppercase">LOGIN DIBUTUHKAN</h2>
            <p class="text-gray-400 text-center text-sm mb-6">Kamu harus login terlebih dahulu.</p>
            <a href="{{ url('/login') }}" class="block w-full text-center text-white font-bold py-3 rounded-xl transition-all" style="background-color: #7C3AED !important;">LOGIN SEKARANG</a>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-[300] flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#12151C] p-6 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] w-full max-w-sm text-center transform scale-95 transition-transform duration-300" id="deleteModalContent">
            <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Hapus dari Keranjang?</h3>
            <p class="text-sm text-gray-400 mb-6">Game ini akan dihapus dari daftar belanjaanmu. Apakah kamu yakin?</p>
            <div class="flex items-center gap-3">
                <button onclick="tutupModalHapus()" class="flex-1 px-4 py-3 bg-[#0A0C10] hover:bg-white/5 border border-white/10 text-gray-300 hover:text-white text-sm font-bold rounded-xl transition-colors">Batal</button>
                <button onclick="eksekusiHapus()" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-red-500/20">Ya, Hapus</button>
            </div>
        </div>
    </div>

    {{-- SCRIPT HALAMAN KERANJANG PENUH --}}
    <script>
        function removeItemsFromCartUI(selectedIds) {
            selectedIds.forEach(id => {
                const itemElements = document.querySelectorAll(`.cart-item-checkbox[data-id="${id}"]`);
                itemElements.forEach(itemElement => {
                    const row = itemElement.closest('.group') || itemElement.closest('.cart-item') || itemElement.closest('div.flex');
                    if (row) row.remove();
                });
            });
            updateSummaryCalculation();
            
            const sisaItem = document.querySelectorAll('.cart-item-checkbox').length;
            if (sisaItem === 0) {
                document.getElementById('fullCartList').innerHTML = `
                    <div class="flex flex-col items-center justify-center py-20 text-gray-500 border border-white/5 rounded-2xl" style="background-color: #12151C !important;">
                        <svg class="w-20 h-20 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path></svg>
                        <p class="text-lg font-medium text-white mb-1">Keranjang Kosong</p>
                        <p class="text-sm text-gray-400">Jelajahi game dan tambahkan ke keranjangmu.</p>
                    </div>`;
            }
        }

        function loadCartPage() {
            let isLoggedIn = @json(Auth::check());
            if (!isLoggedIn) {
                document.getElementById('fullCartList').innerHTML = `<div class="flex flex-col items-center justify-center py-20 text-gray-500"><p class="mb-4">Silakan login untuk melihat keranjang.</p><a href="{{ url('/login') }}" class="px-6 py-2 bg-[#7C3AED] text-white rounded-lg font-bold">Login</a></div>`;
                return;
            }

            fetch('{{ url("/cart/get") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => renderCartPage(data))
                .catch(err => {
                    document.getElementById('fullCartList').innerHTML = '<p class="text-center text-red-500 py-10">Gagal memuat keranjang.</p>';
                });
        }

        function renderCartPage(data) {
            const grid = document.getElementById('fullCartList');
            let items = Array.isArray(data) ? data : (data.data || data.cart || []);

            if (items.length === 0) {
                grid.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-20 text-gray-500 border border-white/5 rounded-2xl" style="background-color: #12151C !important;">
                        <svg class="w-20 h-20 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path></svg>
                        <p class="text-lg font-medium text-white mb-1">Keranjang Kosong</p>
                        <p class="text-sm text-gray-400">Jelajahi game dan tambahkan ke keranjangmu.</p>
                    </div>`;
                updateSummaryCalculation();
                return;
            }

            let html = '';
            items.forEach(item => {
                let game = item.game || item;
                let name = game.name || 'Game Title';
                let price = parseInt(game.price) || 0;
                let priceStr = price === 0 ? 'Gratis' : 'Rp ' + price.toLocaleString('id-ID');
                let imagePath = item.image;
                let gameId = item.game_id;
                let genre = game.genre || 'Action';

                html += `
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-2xl border border-white/5 relative group" style="background-color: #12151C !important;">
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <input type="checkbox" class="cart-item-checkbox gv-checkbox" data-price="${price}" data-id="${gameId}" checked>
                        <img src="${imagePath}" class="w-32 aspect-[4/3] object-cover rounded-xl shadow-lg border border-white/10">
                    </div>
                    
                    <div class="flex-1 w-full mt-2 sm:mt-0">
                        <h3 class="font-bold text-white text-lg leading-tight mb-2 hover:text-[#8B5CF6] transition-colors cursor-pointer" onclick="window.location.href='/game/${gameId}'">${name}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] border border-white/10 px-2 py-0.5 rounded text-gray-400" style="background-color: #1A1D24 !important;">${item.genre || 'Action'}</span>
                            <span class="text-[10px] border border-white/10 px-2 py-0.5 rounded text-gray-400" style="background-color: #1A1D24 !important;">${item.platform || 'PC'}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-6 mt-4 sm:mt-0">
                        <div class="text-right">
                            <p class="font-black text-lg" style="color: #FDE047 !important;">${priceStr}</p>
                        </div>
                        
                        <div class="hidden items-center border border-white/10 rounded-lg" style="background-color: #0A0C10 !important;">
                            <span class="px-4 py-1 text-sm font-bold text-white qty-span" data-qty="1">1</span>
                        </div>
                        
                       <button onclick="bukaModalHapus(${gameId})" class="p-2 text-gray-500 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>`;
            });

            grid.innerHTML = html;
            attachCartListeners();
        }

        function attachCartListeners() {
            const selectAll = document.getElementById('selectAllCart');
            const itemCheckboxes = document.querySelectorAll('.cart-item-checkbox');

            if (selectAll) {
                selectAll.addEventListener('change', function(e) {
                    itemCheckboxes.forEach(cb => cb.checked = e.target.checked);
                    updateSummaryCalculation();
                });
            }
            itemCheckboxes.forEach(cb => cb.addEventListener('change', updateSummaryCalculation));
            updateSummaryCalculation(); // Hitung pas pertama load
        }

        function updateSummaryCalculation() {
            const itemCheckboxes = document.querySelectorAll('.cart-item-checkbox');
            let count = 0;
            let total = 0;
            let totalItemsInCart = 0;

            itemCheckboxes.forEach(cb => {
                let row = cb.closest('.group');
                let qtySpan = row ? row.querySelector('.qty-span') : null;
                let qty = 1;
                totalItemsInCart += qty;
                if (cb.checked) {
                    count += qty;
                    total += (parseInt(cb.getAttribute('data-price')) || 0) * qty;
                }
            });

            // Pastikan jumlah item di header tetap menunjukkan total barang, bukan yang dicentang saja
            document.getElementById('headerCartCount').innerText = totalItemsInCart + ' Item';
            document.getElementById('summaryCartCount').innerText = count;
            document.getElementById('summaryCartPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('finalCartTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');

            // FIX COUNT: Update icon keranjang global di header
            localStorage.setItem('cartCount', totalItemsInCart);
            let badge = document.getElementById('globalCartBadge');
            if (badge) {
                badge.innerText = totalItemsInCart;
                badge.style.setProperty('display', totalItemsInCart > 0 ? 'flex' : 'none', 'important');
            }

            const selectAll = document.getElementById('selectAllCart');
            if (selectAll && itemCheckboxes.length > 0) selectAll.checked = (count === totalItemsInCart && totalItemsInCart > 0);

            // Matikan tombol checkout kalau tidak ada yang dicentang
            const btnCheckout = document.getElementById('btnCheckout');
            if (count === 0) {
                btnCheckout.classList.add('opacity-50', 'cursor-not-allowed');
                btnCheckout.disabled = true;
            } else {
                btnCheckout.classList.remove('opacity-50', 'cursor-not-allowed');
                btnCheckout.disabled = false;
            }
        }

        // --- LOGIKA MODAL HAPUS CUSTOM ---
        let gameIdYangMauDihapus = null;

        window.bukaModalHapus = function(gameId) {
            gameIdYangMauDihapus = gameId; // Simpan ID game yang mau dihapus ke memori
            const modal = document.getElementById('deleteConfirmModal');
            const content = document.getElementById('deleteModalContent');

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 10);
        }

        window.tutupModalHapus = function() {
            const modal = document.getElementById('deleteConfirmModal');
            const content = document.getElementById('deleteModalContent');

            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                gameIdYangMauDihapus = null; // Bersihkan memori
            }, 300); // Tunggu animasi selesai
        }

        window.eksekusiHapus = function() {
            if (!gameIdYangMauDihapus) return;

            tutupModalHapus(); // Tutup modal biar mulus

            let gameIdsToDelete = [];

            if (gameIdYangMauDihapus === 'banyak') {
                const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
                if (checkedBoxes.length === 0) return; // Tidak ada yang dihapus
                checkedBoxes.forEach(cb => {
                    gameIdsToDelete.push(cb.getAttribute('data-id'));
                    const row = cb.closest('.group');
                    if (row) row.remove();
                });
            } else {
                gameIdsToDelete.push(gameIdYangMauDihapus);
                const itemElement = document.querySelector(`.cart-item-checkbox[data-id="${gameIdYangMauDihapus}"]`);
                if (itemElement) {
                    const row = itemElement.closest('.group');
                    if (row) row.remove();
                }
            }

            // 2. REKALKULASI LANGSUNG
            updateSummaryCalculation();

            // Cek apakah keranjang jadi kosong
            const sisaItem = document.querySelectorAll('.cart-item-checkbox').length;
            if (sisaItem === 0) {
                document.getElementById('fullCartList').innerHTML = `
                    <div class="flex flex-col items-center justify-center py-20 text-gray-500 border border-white/5 rounded-2xl" style="background-color: #12151C !important;">
                        <svg class="w-20 h-20 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path></svg>
                        <p class="text-lg font-medium text-white mb-1">Keranjang Kosong</p>
                        <p class="text-sm text-gray-400">Jelajahi game dan tambahkan ke keranjangmu.</p>
                    </div>`;
            }

            // UPDATE LOCALSTORAGE SUPAYA LABEL HILANG DI HALAMAN LAIN
            let localCartCache = JSON.parse(localStorage.getItem('cart_cache')) || [];
            gameIdsToDelete.forEach(id => {
                localCartCache = localCartCache.filter(cid => cid !== String(id));
            });
            localStorage.setItem('cart_cache', JSON.stringify(localCartCache));
            localStorage.setItem('cartCount', sisaItem);
            
            if (typeof window.syncGameCardLabels === 'function') {
                window.syncGameCardLabels();
            }

            // 3. Eksekusi hapus data ke server di background
            // Hapus satu per satu secara paralel agar sesuai endpoint yang ada
            Promise.all(gameIdsToDelete.map(id => {
                return fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ game_id: id })
                });
            })).catch(e => console.log('Gagal hapus di server', e));
        }

        // FUNGSI TAMBAH DARI REKOMENDASI SECARA INSTAN
        window.tambahDariRekomendasi = function(gameId, btn, event) {
            event.stopPropagation(); // Mencegah klik parent
            let isLoggedIn = @json(Auth::check());
            if (!isLoggedIn) {
                document.getElementById('loginModal').classList.remove('hidden');
                return;
            }

            const originalHTML = btn.innerHTML;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            btn.disabled = true;

            fetch('/cart_process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        game_id: gameId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`;
                    btn.classList.replace('text-[#8B5CF6]', 'text-green-400');

                    // UPDATE BADGE LANGSUNG DARI DATA SERVER
                    if (data.cart_count !== undefined) {
                            localStorage.setItem('cartCount', data.cart_count);
                            let badge = document.getElementById('globalCartBadge');
                            if (badge) {
                                badge.innerText = data.cart_count;
                                badge.style.setProperty('display', data.cart_count > 0 ? 'flex' : 'none', 'important');
                            }
                            
                            // NEW: Update cart_cache for label syncing
                            let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
                            if (!cc.includes(String(gameId))) {
                                cc.push(String(gameId));
                                localStorage.setItem('cart_cache', JSON.stringify(cc));
                            }
                            if (typeof window.syncGameCardLabels === 'function') {
                                window.syncGameCardLabels();
                            }
                        }

                    // Muat ulang isi keranjang untuk memunculkan item baru di daftar
                    loadCartPage();

                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.classList.replace('text-green-400', 'text-[#8B5CF6]');
                        btn.disabled = false;
                    }, 2000);
                })
                .catch(() => {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                });
        }

        document.addEventListener('DOMContentLoaded', loadCartPage);


        // FUNGSI DROPDOWN PROFIL
</script>

    <script>
        window.prosesPembayaranMidtrans = function(btnElement) {
            // Cek checkbox yang dipilih
            const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showPaymentModal('error', 'Pilih minimal satu game untuk checkout!', null);
                return;
            }
            const selectedIds = Array.from(checkedBoxes).map(cb => cb.getAttribute('data-id'));

            // Ubah teks tombol jadi loading
            const originalContent = btnElement.innerHTML;
            btnElement.innerHTML = '<span class="animate-pulse text-[15px] font-medium tracking-wide whitespace-nowrap">Memproses...</span>';
            btnElement.disabled = true;

            let isMethodSelected = false;

            // Kirim permintaan ke backend diam-diam (AJAX)
            fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ selected_ids: selectedIds })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error('Server error ' + res.status + ': ' + text.substring(0, 200));
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    // Kembalikan teks tombol
                    btnElement.innerHTML = originalContent;
                    btnElement.disabled = false;

                    // Jika gratis, langsung sukses!
                    if (data.is_free) {
                        // Hapus dari local storage cache
                        let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
                        selectedIds.forEach(id => {
                            cc = cc.filter(cid => cid !== String(id));
                        });
                        localStorage.setItem('cart_cache', JSON.stringify(cc));
                        localStorage.setItem('cartCount', cc.length);
                        
                        if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                        
                        removeItemsFromCartUI(selectedIds);
                        
                        showPaymentModal('success', 'Klaim gratis berhasil!', '/orders');
                    }
                    // Jika berhasil dapat token, panggil Pop-Up Midtrans!
                    else if (data.snap_token) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                isMethodSelected = true;
                                
                                // Hapus item yang dibeli dari local storage
                                let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
                                selectedIds.forEach(id => {
                                    cc = cc.filter(cid => cid !== String(id));
                                });
                                localStorage.setItem('cart_cache', JSON.stringify(cc));
                                localStorage.setItem('cartCount', cc.length);
                                
                                if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                                
                                removeItemsFromCartUI(selectedIds);
                                
                                // Langsung tampilkan modal tanpa menunggu respon (Instan!)
                                showPaymentModal('success', 'Hore! Pembayaran berhasil!', '/orders');

                                // Tembak server lokal secara asinkron di belakang layar
                                fetch('/checkout/success', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        order_id: result.order_id
                                    })
                                });
                            },
                            onPending: function(result) {
                                isMethodSelected = true;
                                
                                // Beritahu backend untuk hapus keranjang karena VA ter-generate
                                fetch('/checkout/mark-pending', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ order_id: data.order_id })
                                });

                                // Hapus item yang dibeli dari local storage karena sudah resmi pending (contoh: VA muncul)
                                let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
                                selectedIds.forEach(id => {
                                    cc = cc.filter(cid => cid !== String(id));
                                });
                                localStorage.setItem('cart_cache', JSON.stringify(cc));
                                localStorage.setItem('cartCount', cc.length);
                                if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                                
                                removeItemsFromCartUI(selectedIds);
                                
                                showPaymentModal('warning', 'Menunggu pembayaran diselesaikan!', '/orders');
                            },
                            onError: function(result) {
                                showPaymentModal('error', 'Maaf, pembayaran gagal!', null);
                            },
                            onClose: function() {
                                if (!isMethodSelected) {
                                    // Tembak server untuk mengecek apakah metode pembayaran sudah dipilih
                                    fetch('/checkout/cancel-if-unpaid', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            order_id: data.order_id
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(cancelData => {
                                        if (cancelData.status === 'kept') {
                                            // Transaksi sukses jadi pending, hapus item dari keranjang
                                            let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
                                            selectedIds.forEach(id => {
                                                cc = cc.filter(cid => cid !== String(id));
                                            });
                                            localStorage.setItem('cart_cache', JSON.stringify(cc));
                                            localStorage.setItem('cartCount', cc.length);
                                            if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                                            
                                            removeItemsFromCartUI(selectedIds);
                                        }
                                    });
                                    showPaymentModal('info', 'Jendela ditutup. Jika belum memilih metode, pesanan dibatalkan otomatis.', null);
                                } else {
                                    // User sudah memilih metode (misal dapat VA) lalu menutup jendela
                                    showPaymentModal('info', 'Jendela ditutup. Jangan lupa selesaikan pembayaranmu ya!', '/orders');
                                }
                            }
                        });
                    } else {
                        showPaymentModal('error', data.message ? data.message : 'Gagal mengambil kode pembayaran!', null);
                    }
                })
                .catch(error => {
                    btnElement.innerHTML = originalContent;
                    btnElement.disabled = false;
                    console.error('Checkout error detail:', error);
                    showPaymentModal('error', 'Error: ' + error.message, null);
                    console.error(error);
                });
        }

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

    @include('components.success-modal')

    <!-- Custom Modal Payment -->
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
    </script>
</body>

</html>