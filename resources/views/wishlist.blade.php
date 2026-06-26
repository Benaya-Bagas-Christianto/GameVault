@php
$globalCartCount = Auth::check() ? \App\Models\Keranjang::where('user_id', Auth::id())->count() : 0;
$globalWishlistCount = Auth::check() ? \App\Models\Wishlist::where('user_id', Auth::id())->count() : 0;
$serverWishlistIds = Auth::check() ? \App\Models\Wishlist::where('user_id', Auth::id())->pluck('game_id')->map('strval')->toArray() : [];
@endphp
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
    <title>Wishlist Saya - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0A0C10 !important;
            color: #FFFFFF !important;
            background-image: none !important;
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
            width: 1.15em;
            height: 1.15em;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.25em;
            display: grid;
            place-content: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .gv-checkbox::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
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


</head>

<body class="flex h-screen overflow-hidden antialiased selection:bg-purple-500 selection:text-white" style="background-color: #0A0C10 !important;">

    <aside class="w-64 border-r border-white/5 flex flex-col h-full flex-shrink-0 z-20 hidden md:flex" style="background-color: #0F111A !important;">
        <div class="h-20 flex items-center px-6">
            <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault" class="w-8 h-8 mr-3 drop-shadow-[0_0_15px_rgba(124,58,237,0.8)]">
            <h1 class="text-xl font-black tracking-widest uppercase text-white">GAMEVAULT</h1>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-8 hide-scrollbar">
            <nav class="space-y-1">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Beranda
                </a>
                <a href="{{ url('/#katalog') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg">
                        <g transform="translate(-1)">
                            <g>
                                <g>
                                    <path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333 h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333 c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333 C235.943,223.156,226.391,213.605,214.609,213.605z" />
                                    <path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96 c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598 c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168 c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056 c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411 c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831 c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43 l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228 c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z M455.757,403.285l-69.566-62.373 c-14.316-12.852-32.697-19.956-51.767-19.956H181.592c-19.07,0-37.45,7.104-51.777,19.965l-69.758,62.544 c-19.658-18.052-21.296-61.46-5.213-122.832c12.346-47.111,33.478-98.672,51.546-134.307c1.399-2.752,2.112-5.086,3.217-9.411 c0.319-1.249,1.62-6.572,1.952-7.876c0.16-0.629,0.314-1.22,0.461-1.772l50.992-19.374c1.186,1.381,2.463,2.836,3.793,4.303 c1.113,1.222,1.113,1.222,2.273,2.457c9.927,10.492,13.691,13.62,24.204,13.62h128c10.147,0,13.785-2.861,24.417-13.27 c1.094-1.079,1.094-1.079,2.155-2.148c1.719-1.742,3.365-3.468,4.858-5.073l51.321,19.499c0.151,0.56,0.308,1.159,0.471,1.797 c0.382,1.493,1.69,6.836,1.931,7.787c1.066,4.206,1.73,6.435,2.992,9.058c18.908,39.428,38.104,87.404,50.193,134.082 C475.42,341.016,474.252,384.613,455.757,403.285z" />
                                    <path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333 c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z" />
                                    <path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333 S354.385,234.938,342.609,234.938z" />
                                    <path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S311.719,192.271,299.943,192.271z" />
                                    <path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S397.052,192.271,385.276,192.271z" />
                                </g>
                            </g>
                        </g>
                    </svg>
                    Game
                </a>
                <a href="{{ url('/wishlist') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl font-bold transition-all relative" style="background-color: rgba(124,58,237,0.1) !important; color: #8B5CF6 !important;">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z" />
                        </svg>
                        Wishlist
                    </div>
                    <span id="globalWishlistBadge" class="w-5 h-5 text-white text-[10px] font-bold items-center justify-center rounded-full" style="background-color: #EF4444 !important; display: {{ $globalWishlistCount > 0 ? 'flex' : 'none' }} !important;">{{ $globalWishlistCount }}</span>

                </a>
            </nav>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
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
                <a href="/" class="{{ request()->is('/') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Beranda</a>
                <a href="/kategori" class="{{ request()->is('kategori') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Kategori</a>
                <a href="/bantuan" class="{{ request()->is('bantuan') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Bantuan</a>
            </nav>

            {{-- Kanan: Ikon & Login --}}
            <div class="flex-1 flex items-center justify-end gap-4 sm:gap-5">
                {{-- ICON KERANJANG --}}
                <a href="/cart" class="relative {{ request()->is('cart') ? 'text-[#8B5CF6]' : 'text-gray-400 hover:text-white' }} transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path>
                    </svg>
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

    <div class="flex-1 overflow-y-auto hide-scrollbar flex">
        <aside id="mainSidebar" class="w-[320px] p-6 lg:p-8 flex-shrink-0 border-r border-white/5 hidden xl:flex flex-col gap-8 bg-[#0A0C10] transition-all duration-300">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-white">Paling Populer</h2>
                    <a href="/" class="text-xs text-purple-400 hover:text-purple-300">Lihat semua</a>
                </div>
                <div class="space-y-4">
                    <?php $rank = 1; ?>
                    @foreach($populer_games->take(5) as $rg)
                        <?php $avg_rating = $rg->reviews->count() > 0 ? round($rg->reviews->avg('rating'), 1) : '0'; ?>
                        <div class="flex items-center gap-3 group cursor-pointer" onclick="window.location.href='/game/{{ $rg->id }}'">
                            <span class="text-xs font-bold text-gray-500 w-3">{{ $rank++ }}</span>
                            <img src="{{ asset('assets/' . $rg->image) }}" class="w-14 h-14 rounded-lg object-cover border border-white/5 group-hover:border-purple-500/50 transition-colors">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-white leading-tight line-clamp-1 group-hover:text-purple-400 transition-colors">{{ $rg->name }}</h4>
                                <div class="flex items-center gap-1.5 mb-1 mt-0.5">
                                    <span class="text-[9px] text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded">{{ explode(',', $rg->genre)[0] }}</span>
                                    <span class="text-[9px] text-gray-400 bg-white/5 px-1.5 py-0.5 rounded">{{ explode(',', $rg->platform)[0] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1"><span class="text-yellow-500 text-[10px]">★</span><span class="text-[11px] font-bold text-white">{{ $avg_rating }}</span></div>
                                    <span class="text-xs font-bold text-white">{{ $rg->price == 0 ? "Gratis" : "Rp " . number_format($rg->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <main class="flex-1 relative overflow-hidden">
            <div id="wishlistMainContent" class="absolute inset-0 overflow-y-auto hide-scrollbar flex flex-col xl:flex-row transition-all duration-500" style="background-color: #0A0C10 !important;">
                <div class="flex-1 p-6 lg:p-8 flex flex-col gap-6">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/5 pb-4">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-white">Wishlist Saya</h1>
                        <span id="headerCount" class="text-xs font-bold px-2.5 py-1 rounded-md border border-purple-500/30" style="background-color: rgba(124,58,237,0.2) !important; color: #8B5CF6 !important;">0 Game</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <label class="flex items-center gap-2 text-gray-400 cursor-pointer hover:text-white transition-colors">
                            <input type="checkbox" id="selectAll" class="gv-checkbox"> Pilih Semua
                        </label>
                        <div class="w-px h-4 bg-white/10 mx-1"></div>
                        <button onclick="bukaModalHapus('banyak')" class="flex items-center gap-1.5 text-gray-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span class="hidden sm:inline">Hapus Semua</span>
                        </button>
                        <button onclick="beliTerpilih(this)" class="flex items-center gap-1.5 text-gray-400 hover:text-purple-400 transition-colors ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path>
                            </svg>
                            <span class="hidden sm:inline">Masukkan Keranjang Semua</span>
                        </button>
                    </div>
                </div>

                <div id="wishlistPageGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-5"></div>
            </main>

            <aside class="w-full xl:w-[320px] p-6 lg:p-8 flex-shrink-0 border-l border-white/5 flex flex-col gap-8 border-t xl:border-t-0" style="background-color: #0A0C10 !important;">
                <div class="p-5 rounded-2xl" style="background-color: #12151C !important; border: 1px solid rgba(255,255,255,0.05);">
                    <h2 class="font-bold text-white text-lg mb-5 border-b border-white/5 pb-3">Ringkasan Wishlist</h2>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-sm"><span class="text-gray-400">Total Game</span><span class="text-white font-bold" id="summaryCount">0</span></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-gray-400">Total Harga</span><span class="text-white font-bold" id="summaryTotal">Rp 0</span></div>
                    </div>
                    <button onclick="beliTerpilih(this)" class="w-full font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 group border" style="background-color: rgba(124,58,237,0.1) !important; color: #8B5CF6 !important; border-color: rgba(124,58,237,0.3) !important;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path>
                        </svg> Beli Terpilih
                    </button>
                </div>
            </aside>
        </div>
    </div>


    {{-- LOGIN MODAL --}}
    <div id="loginModal" class="hidden fixed inset-0 z-[250] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity duration-300">
        <div class="bg-[#12151C] p-8 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.1)] w-full max-w-md relative">
            <button onclick="document.getElementById('loginModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">✕</button>
            <h2 class="text-2xl font-black text-white text-center mb-2 tracking-wide uppercase">AKSES DITOLAK</h2>
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
            <h3 class="text-lg font-bold text-white mb-2" id="deleteModalTitle">Hapus dari Wishlist?</h3>
            <p class="text-sm text-gray-400 mb-6" id="deleteModalDesc">Game ini akan dihapus dari daftar wishlistmu. Apakah kamu yakin?</p>
            <div class="flex items-center gap-3">
                <button onclick="tutupModalHapus()" class="flex-1 px-4 py-3 bg-[#0A0C10] hover:bg-white/5 border border-white/10 text-gray-300 hover:text-white text-sm font-bold rounded-xl transition-colors">Batal</button>
                <button onclick="eksekusiHapus()" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-red-500/20">Ya, Hapus</button>
            </div>
        </div>
    </div>

    {{-- MODAL PERINGATAN (Pilih minimal 1 game) --}}
    <div id="alertModal" class="hidden fixed inset-0 z-[300] flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#12151C] p-6 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] w-full max-w-sm text-center transform scale-95 transition-transform duration-300" id="alertModalContent">
            <div class="w-16 h-16 bg-yellow-500/10 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-yellow-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Peringatan</h3>
            <p class="text-sm text-gray-400 mb-6" id="alertModalMessage">Pilih minimal satu game.</p>
            <button onclick="tutupModalAlert()" class="w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-black text-sm font-bold rounded-xl transition-colors shadow-lg shadow-yellow-500/20">Mengerti</button>
        </div>
    </div>

    {{-- MODAL SUKSES HAPUS --}}
    <div id="successDeleteModal" class="hidden fixed inset-0 z-[300] flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#12151C] p-6 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] w-full max-w-sm text-center transform scale-95 transition-transform duration-300" id="successDeleteModalContent">
            <div class="w-16 h-16 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Berhasil Dihapus!</h3>
            <p class="text-sm text-gray-400 mb-6">Game telah dihapus dari daftar wishlistmu.</p>
            <button onclick="tutupModalSuccessDelete()" class="w-full px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-emerald-500/20">Tutup</button>
        </div>
    </div>

    {{-- SCRIPT KERANJANG REAL-TIME UNTUK WISHLIST --}}
    <script>
        const gameDatabase = {!! isset($games) ? (method_exists($games, 'items') ? json_encode($games->items()) : json_encode($games)) : '[]' !!};

        window.removeFromWishlist = function(gameId) {
            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            wishlist = wishlist.filter(id => id != gameId && id !== String(gameId));
            localStorage.setItem('wishlist', JSON.stringify(wishlist));

            // UPDATE BADGE IMMEDIATELY
            if (typeof syncWishlistBadge === 'function') {
                syncWishlistBadge();
            }
            if (typeof window.syncWishlistBadge === 'function') {
                window.syncWishlistBadge();
            }

            // Sync with server if logged in
            let isLoggedIn = @json(Auth::check());
            if (isLoggedIn) {
                return fetch('/wishlist_process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        game_id: gameId
                    })
                }).catch(e => console.log('Failed to sync wishlist deletion to server', e));
            }
            return Promise.resolve();
        };

        function renderWishlistPage() {
            const grid = document.getElementById('wishlistPageGrid');
            grid.innerHTML = `<div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-500"><div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-white mb-4"></div><p class="text-lg font-medium text-white">Memuat Wishlist...</p></div>`;

            fetch('/get_wishlist?t=' + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                let listGames = data.data || [];
                let wishlistIds = listGames.map(item => String(item.real_game_id || item.id));
                localStorage.setItem('wishlist', JSON.stringify(wishlistIds));
                
                console.log("SERVER RETURNED LIST GAMES:", listGames);
                console.log("MAPPED WISHLIST IDS:", wishlistIds);
                console.log("GAMEDATABASE TYPE/LENGTH:", typeof gameDatabase, Array.isArray(gameDatabase), gameDatabase.length);
                
                if (wishlistIds.length === 0) {
                    grid.innerHTML = `<div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-500"><p class="text-lg font-medium text-white mb-1">Wishlist Kosong</p></div>`;
                    document.getElementById('headerCount').innerText = '0 Game';
                    document.getElementById('summaryCount').innerText = '0';
                    document.getElementById('summaryTotal').innerText = 'Rp 0';
                    return;
                }

                let html = '';
                gameDatabase.forEach(game => {
                    if (wishlistIds.includes(game.id) || wishlistIds.includes(game.id.toString())) {
                        let avg_rating = game.reviews && game.reviews.length > 0 ? (game.reviews.reduce((a, b) => a + parseInt(b.rating), 0) / game.reviews.length).toFixed(1) : '0';
                        let priceStr = game.price == 0 ? 'Gratis' : 'Rp ' + parseInt(game.price).toLocaleString('id-ID');
                        let imagePath = game.image ? ('/assets/' + game.image) : '/assets/no-image.jpg';
                        let priceClass = game.price == 0 ? 'text-green-400' : 'text-white';
                        html += `
                        <div class="rounded-xl overflow-hidden hover-card flex flex-col relative group" style="background-color: #12151C !important; border: 1px solid rgba(255,255,255,0.05);">
                            <input type="checkbox" class="wishlist-item-checkbox gv-checkbox absolute top-3 left-3 z-10 shadow-lg" data-price="${game.price}" data-id="${game.id}">
                        <button onclick="bukaModalHapus('satu', ${game.id})" class="absolute top-3 right-3 z-10 w-8 h-8 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center transition-all shadow-lg border border-white/10" style="color: #8B5CF6 !important;">✕</button>
                        <div class="relative aspect-[3/4] overflow-hidden bg-black cursor-pointer" onclick="window.location.href='/game/${game.id}'">
                            <img src="${imagePath}" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-transform duration-500 group-hover:scale-105">
                        </div>
                        <div class="p-4 flex-1 flex flex-col relative" style="background-color: #12151C !important;">
                            <h3 class="font-bold text-white text-base leading-tight mb-1 line-clamp-1">${game.name}</h3>
                            <p class="mt-auto font-bold ${priceClass}">${priceStr}</p>
                            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-white/5">
                                <button onclick="tambahSatuKeKeranjang(${game.id}, this)" class="flex-1 bg-white/5 hover:bg-white/10 text-white font-medium py-2 rounded-lg flex justify-center items-center gap-2 transition-colors text-sm">Masuk Keranjang</button>
                            </div>
                        </div>
                    </div>`;
                }
            });
                grid.innerHTML = html;
                document.getElementById('headerCount').innerText = wishlistIds.length + ' Game';
                attachCheckboxListeners();
            }).catch(e => {
                console.error("Gagal memuat wishlist:", e);
                grid.innerHTML = `<div class="col-span-full text-center py-10 text-red-500">Gagal memuat data wishlist</div>`;
            });
        }

        function attachCheckboxListeners() {
            const selectAll = document.getElementById('selectAll');
            const itemCheckboxes = document.querySelectorAll('.wishlist-item-checkbox');
            const summaryCount = document.getElementById('summaryCount');
            const summaryTotal = document.getElementById('summaryTotal');

            function updateSummary() {
                let count = 0;
                let total = 0;
                itemCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        count++;
                        total += parseInt(cb.getAttribute('data-price')) || 0;
                    }
                });
                summaryCount.innerText = count;
                summaryTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');
                if (selectAll && itemCheckboxes.length > 0) selectAll.checked = (count === itemCheckboxes.length);
            }
            if (selectAll) {
                selectAll.addEventListener('change', function(e) {
                    itemCheckboxes.forEach(cb => cb.checked = e.target.checked);
                    updateSummary();
                });
            }
            itemCheckboxes.forEach(cb => cb.addEventListener('change', updateSummary));
        }

        let modeHapusWishlist = 'banyak';
        let idHapusSatu = null;

        window.bukaModalHapus = function(mode, id = null) {
            modeHapusWishlist = mode;
            idHapusSatu = id;

            if (mode === 'banyak') {
                const checkboxes = document.querySelectorAll('.wishlist-item-checkbox:checked');
                if (checkboxes.length === 0) {
                    bukaModalAlert("Pilih minimal satu game untuk dihapus dari wishlist.");
                    return;
                }
                document.getElementById('deleteModalDesc').innerText = 'Game yang dipilih akan dihapus dari daftar wishlistmu. Apakah kamu yakin?';
            } else {
                document.getElementById('deleteModalDesc').innerText = 'Game ini akan dihapus dari daftar wishlistmu. Apakah kamu yakin?';
            }

            const modal = document.getElementById('deleteConfirmModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        window.tutupModalHapus = function() {
            const modal = document.getElementById('deleteConfirmModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        window.bukaModalAlert = function(msg) {
            document.getElementById('alertModalMessage').innerText = msg;
            const modal = document.getElementById('alertModal');
            const content = document.getElementById('alertModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        window.tutupModalAlert = function() {
            const modal = document.getElementById('alertModal');
            const content = document.getElementById('alertModalContent');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        window.bukaModalSuccessDelete = function() {
            const modal = document.getElementById('successDeleteModal');
            const content = document.getElementById('successDeleteModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);

            // Auto close after 2 seconds
            setTimeout(tutupModalSuccessDelete, 2000);
        }

        window.tutupModalSuccessDelete = function() {
            const modal = document.getElementById('successDeleteModal');
            const content = document.getElementById('successDeleteModalContent');
            if (modal.classList.contains('hidden')) return;
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        window.eksekusiHapus = async function() {
            tutupModalHapus();
            if (modeHapusWishlist === 'banyak') {
                const checkboxes = document.querySelectorAll('.wishlist-item-checkbox:checked');
                let promises = [];
                checkboxes.forEach(cb => {
                    let p = window.removeFromWishlist(cb.getAttribute('data-id'));
                    if (p) promises.push(p);
                });
                await Promise.all(promises);
            } else if (idHapusSatu) {
                let p = window.removeFromWishlist(idHapusSatu);
                if (p) await p;
            }
            
            // Sekarang setelah server benar-benar menghapus data, kita bisa render ulang dari server
            renderWishlistPage();
            bukaModalSuccessDelete();
        };

        // FUNGSI KERANJANG REAL-TIME
        window.openCart = function() {
            document.getElementById('cartOverlay').classList.remove('hidden');
            setTimeout(() => document.getElementById('cartOverlay').classList.remove('opacity-0'), 10);
            document.getElementById('premiumCartSidebar').classList.remove('translate-x-full');

            fetch('/cart/get', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => renderPremiumCart(data))
                .catch(err => {
                    document.getElementById('premiumCartContent').innerHTML = '<p class="text-center text-red-500 mt-10">Gagal memuat keranjang.</p>';
                });
        }

        window.closeCart = function() {
            document.getElementById('premiumCartSidebar').classList.add('translate-x-full');
            document.getElementById('cartOverlay').classList.add('opacity-0');
            setTimeout(() => document.getElementById('cartOverlay').classList.add('hidden'), 300);
        }

        function renderPremiumCart(data) {
            let content = '';
            let total = 0;
            let count = 0;
            let items = Array.isArray(data) ? data : (data.data || data.cart || []);

            let cc = items.map(item => String(item.game_id || item.id));
            localStorage.setItem('cart_cache', JSON.stringify(cc));
            if (typeof window.syncGameCardLabels === 'function') window.syncGameCardLabels();

            if (items.length === 0) {
                content = `<div class="flex flex-col items-center justify-center h-full text-gray-500 mt-20"><p>Keranjang kosong.</p></div>`;
            } else {
                items.forEach(item => {
                    let name = item.name || 'Game Title';
                    let price = parseInt(item.price) || 0;

                    // GAMBAR MENGAMBIL LANGSUNG DARI CONTROLLER YANG SUDAH DIPERBAIKI
                    let imagePath = item.image;
                    let cartId = item.cart_id; // ID ASLI DARI DATABASE

                    total += price;
                    count++;

                    content += `<div class="flex gap-4 p-4 border border-white/5 rounded-xl relative group" style="background-color: #12151C !important;">
                        <img src="${imagePath}" onerror="this.src='/assets/no-image.jpg'" class="w-16 h-20 object-cover rounded-lg shadow-md">
                        <div class="flex-1 flex flex-col justify-center pr-6">
                            <h3 class="font-bold text-white text-sm line-clamp-1">${name}</h3>
                            <p class="font-bold text-sm mt-1" style="color: #FDE047 !important;">Rp ${price.toLocaleString('id-ID')}</p>
                        </div>
                        <button onclick="hapusDariKeranjang(${cartId})" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 p-1" title="Hapus">✕</button>
                    </div>`;
                });
            }
            document.getElementById('premiumCartContent').innerHTML = content;
            document.getElementById('premiumCartTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
            let badge = document.getElementById('globalCartBadge');
            if (badge) {
                badge.innerText = count;
                badge.style.setProperty('display', count > 0 ? 'flex' : 'none', 'important');
            }
        }

        window.hapusDariKeranjang = function(cartId) {
            document.getElementById('premiumCartContent').innerHTML = '<p class="text-center text-gray-500 mt-10 animate-pulse">Menghapus item...</p>';
            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cart_id: cartId,
                    id: cartId
                }) // PASTIKAN MENGIRIM CART ID
            }).then(() => {
                if (typeof updateGlobalCartBadge === 'function') updateGlobalCartBadge();
                if (typeof openCart === 'function') openCart();
            }).catch(() => {
                if (typeof openCart === 'function') openCart();
            });
        }


        window.beliTerpilih = function(btnElement) {
            let isLoggedIn = @json(Auth::check());
            if (!isLoggedIn) {
                document.getElementById('loginModal').classList.remove('hidden');
                return;
            }

            const checkboxes = document.querySelectorAll('.wishlist-item-checkbox:checked');
            if (checkboxes.length === 0) {
                bukaModalAlert("Pilih minimal satu game.");
                return;
            }

            const originalContent = btnElement.innerHTML;
            btnElement.innerHTML = '<span class="animate-pulse">Memproses...</span>';
            const promises = Array.from(checkboxes).map(cb => {
                return fetch('/cart_process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        game_id: cb.getAttribute('data-id'),
                        action: 'add'
                    })
                });
            });
            Promise.all(promises).then(async () => {
                let removePromises = Array.from(checkboxes).map(cb => {
                    let p = window.removeFromWishlist(cb.getAttribute('data-id'));
                    return p ? p : Promise.resolve();
                });
                await Promise.all(removePromises);
                window.location.href = '/cart';
            }).catch(async () => {
                let removePromises = Array.from(checkboxes).map(cb => {
                    let p = window.removeFromWishlist(cb.getAttribute('data-id'));
                    return p ? p : Promise.resolve();
                });
                await Promise.all(removePromises);
                window.location.href = '/cart';
            });
        };

        window.tambahSatuKeKeranjang = function(gameId, btnElement) {
            let isLoggedIn = @json(Auth::check());
            if (!isLoggedIn) {
                document.getElementById('loginModal').classList.remove('hidden');
                return;
            }
            const originalContent = btnElement.innerHTML;
            btnElement.innerHTML = '<span class="animate-pulse text-xs">...</span>';
            fetch('/cart_process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    game_id: gameId,
                    action: 'add'
                })
            }).then(async () => {
                btnElement.innerHTML = originalContent;
                if(typeof updateGlobalCartBadge === 'function') updateGlobalCartBadge();
                if(typeof showToast === 'function') showToast('Game berhasil ditambahkan ke keranjang!');
                
                let p = window.removeFromWishlist(gameId);
                if (p) await p;
                renderWishlistPage();
            }).catch(() => {
                btnElement.innerHTML = originalContent;
                if(typeof showToast === 'function') showToast('Gagal menambahkan ke keranjang', true);
            });
        };

        document.addEventListener('DOMContentLoaded', renderWishlistPage);
    </script>
    <script>
        // FUNGSI MEMUNCULKAN ANGKA COUNT KERANJANG OTOMATIS
        function updateGlobalCartBadge() {
            let isLoggedIn = @json(Auth::check());
            if (!isLoggedIn) return;

            fetch('/cart/get', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    let cartCount = data.cart_count !== undefined ? data.cart_count : (Array.isArray(data.data) ? data.data.length : 0);
                    localStorage.setItem('cartCount', cartCount);
                    
                    if (data.data && Array.isArray(data.data)) {
                        let cc = data.data.map(item => String(item.game_id || item.id));
                        localStorage.setItem('cart_cache', JSON.stringify(cc));
                        if (typeof window.syncGameCardLabels === 'function') window.syncGameCardLabels();
                    }
                    
                    let badge = document.getElementById('globalCartBadge');
                    if (badge) {
                        badge.innerText = cartCount;
                        badge.style.setProperty('display', cartCount > 0 ? 'flex' : 'none', 'important');
                    }
                }).catch(e => console.log(e));
        }
    </script>

    {{-- ===== PANEL PUSAT BANTUAN ===== --}}
    <div id="bantuanPanel" class="absolute inset-0 overflow-y-auto hide-scrollbar transition-all duration-500 ease-in-out" style="transform: translateX(100%); opacity: 0; pointer-events: none; top: 80px;">
        <div class="max-w-5xl mx-auto px-6 lg:px-8 py-10">

            {{-- Hero Section --}}
            <div class="text-center mb-14 relative">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-purple-600/20 blur-[100px] rounded-full pointer-events-none"></div>
                <span class="inline-flex items-center gap-2 text-[10px] font-bold text-purple-400 uppercase tracking-widest bg-purple-500/10 border border-purple-500/20 px-3 py-1.5 rounded-full mb-6">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g opacity="0.3">
                            <path d="M5 10H19V20H5V10Z" fill="currentColor" />
                            <path d="M4 7H20V10H4V7Z" fill="currentColor" />
                        </g>
                        <path d="M19 10.0802V20.0802H5V10.0802M19 10.0802H5M19 10.0802H20V7.0802H4V10.0802H5M12 7.0802C12.8333 5.24687 14.9999 1.5802 16.9999 3.5802C18.9999 5.5802 14.5 6.91353 12 7.0802ZM12 7.0802C11.1667 5.24687 8.99999 1.5802 6.99999 3.5802C4.99999 5.5802 9.5 6.91353 12 7.0802Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Pusat Bantuan
                </span>
                <h1 class="text-4xl md:text-5xl font-black mb-4 relative z-10">Pusat <span style="background: linear-gradient(135deg, #c084fc, #a855f7, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Bantuan</span></h1>
                <p class="text-gray-400 mb-8 text-sm md:text-base max-w-xl mx-auto relative z-10">Kami siap membantu kamu dengan berbagai pertanyaan seputar GameVault. Cari masalahmu di sini.</p>
                <div class="flex flex-wrap items-center justify-center gap-2 md:gap-3 mt-5 text-[11px] md:text-xs relative z-10">
                    <span class="text-gray-500 mr-1">Populer sekarang:</span>
                    <span class="px-3 py-1.5 rounded-full bg-[#12151C] border border-white/10 hover:border-purple-500 hover:text-purple-400 cursor-pointer transition-colors text-gray-400">Lupa Password</span>
                    <span class="px-3 py-1.5 rounded-full bg-[#12151C] border border-white/10 hover:border-purple-500 hover:text-purple-400 cursor-pointer transition-colors text-gray-400">Cara Refund</span>
                    <span class="px-3 py-1.5 rounded-full bg-[#12151C] border border-white/10 hover:border-purple-500 hover:text-purple-400 cursor-pointer transition-colors text-gray-400">Gagal Bayar</span>
                </div>
            </div>

            {{-- Kategori Bantuan --}}
            <h2 class="text-lg font-bold mb-5">Kategori Bantuan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-14">
                <div class="card-bg rounded-2xl hover-card cursor-pointer transition-all overflow-hidden">
                    <div class="w-full h-28 bg-blue-500/10 flex items-center justify-center relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-transparent"></div>
                        <svg class="w-16 h-16 fill-current text-blue-400 relative z-10" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <path d="M80,71.2V74c0,3.3-2.7,6-6,6H26c-3.3,0-6-2.7-6-6v-2.8c0-7.3,8.5-11.7,16.5-15.2c0.3-0.1,0.5-0.2,0.8-0.4 c0.6-0.3,1.3-0.3,1.9,0.1C42.4,57.8,46.1,59,50,59c3.9,0,7.6-1.2,10.8-3.2c0.6-0.4,1.3-0.4,1.9-0.1c0.3,0.1,0.5,0.2,0.8,0.4 C71.5,59.5,80,63.9,80,71.2z" />
                            <ellipse cx="50" cy="36.5" rx="14.9" ry="16.5" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold mb-2">Akun &amp; Profil</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">Masalah terkait login, verifikasi email, ganti password, dan keamanan akun.</p>
                    </div>
                </div>
                <div class="card-bg rounded-2xl hover-card cursor-pointer transition-all overflow-hidden">
                    <div class="w-full h-28 bg-green-500/10 flex items-center justify-center relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-transparent"></div>
                        <svg class="w-16 h-16 stroke-current text-green-400 relative z-10" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
                    <div class="w-full h-28 bg-pink-500/10 flex items-center justify-center relative">
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

            {{-- FAQ & Kontak --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                <div class="lg:col-span-2 card-bg p-8 rounded-2xl">
                    <h2 class="text-lg font-bold mb-6">Pertanyaan yang Sering Diajukan</h2>
                    <div class="space-y-2">
                        <div class="border-b border-white/5">
                            <button onclick="toggleFaqPanel(this)" class="w-full py-4 flex items-center justify-between text-left focus:outline-none group">
                                <h4 class="font-bold text-sm text-gray-300 group-hover:text-white transition-colors">Bagaimana cara mengunduh game yang sudah dibeli?</h4>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300 group-hover:text-purple-400 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-panel-content hidden pb-5 text-xs text-gray-400 leading-relaxed">Silakan buka menu "Riwayat Pembelian" yang ada di dropdown profil Anda, klik invoice pembelian yang sudah sukses, lalu klik tombol "Download Game".</div>
                        </div>
                        <div class="border-b border-white/5">
                            <button onclick="toggleFaqPanel(this)" class="w-full py-4 flex items-center justify-between text-left focus:outline-none group">
                                <h4 class="font-bold text-sm text-gray-300 group-hover:text-white transition-colors">Metode pembayaran apa saja yang tersedia?</h4>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300 group-hover:text-purple-400 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-panel-content hidden pb-5 text-xs text-gray-400 leading-relaxed">Kami mendukung pembayaran lengkap via Midtrans, termasuk Transfer Bank (BCA, BNI, BRI), QRIS, GoPay, dan OVO secara real-time.</div>
                        </div>
                        <div class="border-b border-white/5">
                            <button onclick="toggleFaqPanel(this)" class="w-full py-4 flex items-center justify-between text-left focus:outline-none group">
                                <h4 class="font-bold text-sm text-gray-300 group-hover:text-white transition-colors">Bagaimana cara meminta refund?</h4>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300 group-hover:text-purple-400 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-panel-content hidden pb-5 text-xs text-gray-400 leading-relaxed">Refund bisa diajukan maksimal 14 hari setelah pembelian dengan syarat waktu bermain (playtime) di bawah 2 jam. Hubungi teknisi kami untuk memprosesnya.</div>
                        </div>
                        <div class="border-b border-white/5">
                            <button onclick="toggleFaqPanel(this)" class="w-full py-4 flex items-center justify-between text-left focus:outline-none group">
                                <h4 class="font-bold text-sm text-gray-300 group-hover:text-white transition-colors">Apakah game bisa dimainkan offline?</h4>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300 group-hover:text-purple-400 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-panel-content hidden pb-5 text-xs text-gray-400 leading-relaxed">Tergantung game-nya. Sebagian besar game di GameVault bisa dimainkan offline setelah diunduh. Cek deskripsi game untuk info lebih lanjut.</div>
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

        </div>
        </main>
    </div>
</div>

{{-- PANEL BANTUAN (IDENTIK) --}}

    <script>
        let bantuanOpen = false;
        function toggleBantuan(e) {
            if (e) e.preventDefault();
            bantuanOpen = !bantuanOpen;
            const panel = document.getElementById('bantuanPanel');
            const main = document.getElementById('wishlistMainContent');
            const navLink = document.getElementById('nav-bantuan');
            if (bantuanOpen) {
                panel.style.transform = 'translateX(0)';
                panel.style.opacity = '1';
                panel.style.pointerEvents = 'all';
                if (main) { main.style.transform = 'translateX(-30px)'; main.style.opacity = '0'; main.style.pointerEvents = 'none'; }
                if (navLink) { navLink.classList.add('text-purple-400', 'border-purple-500'); navLink.classList.remove('text-gray-400', 'border-transparent'); }
            } else {
                panel.style.transform = 'translateX(100%)';
                panel.style.opacity = '0';
                panel.style.pointerEvents = 'none';
                if (main) { main.style.transform = ''; main.style.opacity = ''; main.style.pointerEvents = ''; }
                if (navLink) { navLink.classList.remove('text-purple-400', 'border-purple-500'); navLink.classList.add('text-gray-400', 'border-transparent'); }
            }
        }
        function toggleFaqPanel(btn) {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('svg');
            content.classList.toggle('hidden');
            icon.style.transform = content.classList.contains('hidden') ? '' : 'rotate(180deg)';
        }
    </script>
    @include('components.success-modal')
@include('components.toast-notification')
</body>

</html>