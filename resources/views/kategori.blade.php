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
    <title>Kategori Game - GameVault</title>
    
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #FFFFFF;
            background-color: #0A0C10;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Custom Checkbox */
        .custom-checkbox {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            background-color: transparent;
            outline: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .custom-checkbox:checked {
            background-color: #7C3AED;
            border-color: #7C3AED;
        }

        .custom-checkbox:checked::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 5px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .genre-box {
            background-color: #151821;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s;
        }

        .genre-box:hover {
            border-color: rgba(124, 58, 237, 0.5);
            transform: translateY(-2px);
        }

        .genre-box.active {
            border-color: #7C3AED;
            background-color: rgba(124, 58, 237, 0.2);
            box-shadow: 0 0 15px rgba(124, 58, 237, 0.5);
            transform: translateY(-2px);
        }

        .card-bg {
            background-color: #12151C;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            border-color: rgba(124, 58, 237, 0.5);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.15);
            transform: translateY(-2px);
        }
    </style>


    
</head>

<body class="antialiased h-screen flex flex-col overflow-hidden hide-scrollbar">

@php
    $ownedGameIds = [];
    $cartGameIds = [];
    $wishlistGameIds = [];

    if(Auth::check()) {
        $userId = Auth::id();
        $ownedGameIds = \Illuminate\Support\Facades\DB::table('tb_detail_transaksi')
                        ->join('tb_transaksi', 'tb_detail_transaksi.transaksi_id', '=', 'tb_transaksi.id')
                        ->where('tb_transaksi.user_id', $userId)
                        ->where('tb_transaksi.status', 'Success')
                        ->pluck('tb_detail_transaksi.game_id')->toArray();
                        
        $cartGameIds = \App\Models\Keranjang::where('user_id', $userId)->pluck('game_id')->toArray();
        $wishlistGameIds = \App\Models\Wishlist::where('user_id', $userId)->pluck('game_id')->toArray();
    }
@endphp

    {{-- NAVBAR (Berdasarkan Screenshot) --}}
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
            <a href="/" id="nav-beranda" class="{{ request()->is('/') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Beranda</a>
            <a href="/kategori" id="nav-kategori" class="{{ request()->is('kategori') ? 'text-purple-400 border-purple-500' : 'text-gray-400 border-transparent hover:text-white' }} border-b-2 pb-7 pt-7 transition-all">Kategori</a>
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

    <div id="berandaContent" class="flex-1 flex flex-col transition-all duration-500 w-full overflow-hidden">
    {{-- KONTEN UTAMA --}}
    <div class="flex-1 w-full flex gap-8 px-6 py-6 lg:px-6 lg:py-10 h-full overflow-hidden">

        {{-- SIDEBAR FILTER --}}
        <aside class="w-[240px] flex-shrink-0 hidden md:flex flex-col gap-8 h-full overflow-y-auto hide-scrollbar pb-6 pr-2">

            {{-- Filter Header & Pencarian Sidebar --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-xl font-bold text-white leading-none">Filter</h2>
                    <button onclick="resetFilters()" class="text-sm font-bold text-purple-400 hover:text-white transition-colors leading-none">Reset</button>
                </div>

                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="kategoriSearchInput" onkeyup="filterByTitle()" placeholder="Cari game di kategori ini..."
                        class="w-full bg-[#12151C] border border-white/10 rounded-lg py-2.5 pl-10 pr-3 text-sm text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-all placeholder-gray-500 cursor-text">
                </div>
            </div>

            {{-- Genre --}}
            <div>
                <h3 class="font-bold text-white text-sm mb-4">Genre</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="semuaGenre" class="custom-checkbox" {{ empty($genreDipilih) ? 'checked' : '' }} onchange="toggleSemuaGenre(this)">
                        <span class="text-xs font-bold {{ empty($genreDipilih) ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors">Semua Genre</span>
                    </label>

                    @php
                    // Gabungan genre dari referensi dan jelajah genre
                    $allGenres = ['Action', 'Adventure', 'RPG', 'Strategy', 'Simulation', 'Racing', 'Sports', 'Horror', 'Indie', 'Open World', 'FPS', 'Lainnya'];
                    @endphp
                    @foreach($allGenres as $g)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="genre" value="{{ $g }}" class="custom-checkbox" {{ in_array($g, $genreDipilih) ? 'checked' : '' }} onchange="applyFilters()">
                        <span class="text-xs text-gray-400 group-hover:text-white transition-colors">{{ $g }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Platform --}}
            <div>
                <h3 class="font-bold text-white text-sm mb-4 mt-8">Platform</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="semuaPlatform" class="custom-checkbox" {{ empty($platformDipilih) ? 'checked' : '' }} onchange="toggleSemuaPlatform(this)">
                        <span class="text-xs font-bold {{ empty($platformDipilih) ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors">Semua Platform</span>
                    </label>

                    @php
                    $allPlatforms = ['PC', 'PlayStation', 'Xbox'];
                    @endphp
                    @foreach($allPlatforms as $p)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="platform" value="{{ $p }}" class="custom-checkbox" {{ in_array($p, $platformDipilih) ? 'checked' : '' }} onchange="applyFilters()">
                        <span class="text-xs text-gray-400 group-hover:text-white transition-colors">{{ $p }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Harga --}}
            <div>
                <h3 class="font-bold text-white text-sm mb-4 mt-8">Harga</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="semuaHarga" class="custom-checkbox" {{ empty($hargaDipilih) ? 'checked' : '' }} onchange="toggleSemuaHarga(this)">
                        <span class="text-xs font-bold {{ empty($hargaDipilih) ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors">Semua Harga</span>
                    </label>

                    @php
                    $hargaOptions = [
                        'gratis' => 'Gratis',
                        '<100'=> 'Di bawah Rp 100.000',
                        '100-250' => 'Rp 100.000 - Rp 250.000',
                        '250-500' => 'Rp 250.000 - Rp 500.000',
                        '500-750' => 'Rp 500.000 - Rp 750.000',
                        '>750' => 'Di atas Rp 750.000'
                        ];
                        @endphp
                        @foreach($hargaOptions as $val => $label)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="harga" value="{{ $val }}" class="custom-checkbox" {{ in_array($val, $hargaDipilih) ? 'checked' : '' }} onchange="applyFilters()">
                            <span class="text-xs text-gray-400 group-hover:text-white transition-colors">{{ $label }}</span>
                        </label>
                        @endforeach
                </div>
            </div>

            {{-- Rating --}}
            <div>
                <h3 class="font-bold text-white text-sm mb-4 mt-8">Rating Minimal</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="semuaRating" class="custom-checkbox" {{ empty($ratingDipilih) ? 'checked' : '' }} onchange="toggleSemuaRating(this)">
                        <span class="text-xs font-bold {{ empty($ratingDipilih) ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors">Semua Rating</span>
                    </label>

                    @foreach([4, 3, 2, 1] as $r)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="rating" value="{{ $r }}" class="custom-checkbox" {{ in_array($r, $ratingDipilih) ? 'checked' : '' }} onchange="applyFilters()">
                        <span class="text-xs text-gray-400 group-hover:text-white transition-colors flex items-center gap-1">
                            {{ $r }} Bintang ke atas
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- AREA KANAN (Konten) --}}
        <main class="flex-1 w-full h-full overflow-y-auto hide-scrollbar pr-2 pb-20">

            {{-- Header Kategori --}}
            <div class="mb-8">
                <h1 class="text-3xl font-black text-white mb-2">Kategori</h1>
                <p class="text-sm text-gray-400">Temukan game berdasarkan kategori yang kamu suka.</p>
            </div>

            {{-- Box Kategori (Ikon Besar) --}}
            @php
            // Kombinasi genre screenshot & beranda
            $genreBoxes = [
            ['name' => 'Action', 'icon' => '<svg class="w-10 h-10 fill-current text-red-400" viewBox="0 0 511.999 511.999"><path d="M501.331,85.339h-14.862v-4.329c0-5.892-4.776-10.669-10.669-10.669c-5.891,0-10.669,4.776-10.669,10.669v4.329H262.177 c-5.892,0-10.669,4.776-10.669,10.669v7.02h-88.035v-7.02c0-5.892-4.776-10.669-10.669-10.669h-43.005v-4.329 c0-5.892-4.777-10.669-10.669-10.669c-5.891,0-10.669,4.776-10.669,10.669v4.329H77.639c-5.892,0-10.669,4.776-10.669,10.669 v16.622l-11.082,54.858L30.72,213.742c-2.766,5.084-0.975,11.446,4.037,14.339c12.64,7.296,18.41,13.666,17.754,27.18 c-0.048,0.408-0.079,0.82-0.079,1.241c0,0.004,0.001,0.008,0.001,0.012c-0.803,10.698-5.375,25.739-13.527,48.768L1.624,410.608 c-2.797,7.906-1.952,15.906,2.32,21.949c4.089,5.785,10.869,9.102,18.606,9.102h104.906c9.318,0,18.766-6.841,21.976-15.911 l28.15-79.521c0.24-0.531,0.435-1.086,0.588-1.659l6.928-19.574h70.75c43.866,0,65.499-15.04,79.819-55.492l6.161-17.407h70.35 c5.892,0,10.669-4.776,10.669-10.669v-11.916h67.815c5.892,0,10.669-4.776,10.669-10.669v-36.998 c5.89-0.001,10.667-4.777,10.667-10.669V96.008C512,90.115,507.223,85.339,501.331,85.339z M218.161,124.365h33.344v36.141 h-33.344V124.365z M163.477,124.365h33.346v36.141h-33.346V124.365z M88.098,115.809c0.14-0.696,0.21-1.403,0.21-2.112v-7.02 h53.827v7.02c0,0.028,0.004,0.054,0.004,0.082v46.727H79.069L88.098,115.809z M315.554,262.383 c-11.198,31.63-25.146,41.273-59.705,41.273h-63.197l18.251-51.561h11.331c-0.107,12.641,4.678,27.36,16.101,38.907 c2.086,2.109,4.834,3.165,7.584,3.165c2.711,0,5.423-1.026,7.502-3.084c4.19-4.144,4.225-10.898,0.082-15.088 c-7.403-7.484-9.991-16.901-9.917-23.9h75.605L315.554,262.383z M401.51,218.842v11.915H234.694 c-0.046,0.001-0.091-0.001-0.135,0h-31.196c-4.519,0-8.55,2.849-10.059,7.109l-33.025,93.299h-33.462 c-5.891,0-10.669,4.776-10.669,10.669s4.778,10.669,10.669,10.669h25.908l-7.549,21.328h-72.53 c-5.892,0-10.669,4.776-10.669,10.669s4.776,10.669,10.669,10.669h64.975l-8.304,23.461c-0.216,0.59-1.324,1.527-1.989,1.693 H22.551c-0.639,0-1.01-0.095-1.185-0.158c-0.064-0.337-0.077-1.16,0.374-2.436l37.281-105.326 c6.141-17.35,11.134-32.235,13.421-45.231h23.73c5.892,0,10.669-4.776,10.669-10.669c0-5.892-4.776-10.669-10.669-10.669H73.311 c-1.592-11.712-7.336-21.652-19.156-30.516l18.214-33.474H401.51V218.842z M422.848,208.172v-26.33h57.146v26.33H422.848z M490.662,160.506H272.843v-46.768c0-0.014,0.002-0.027,0.002-0.042v-7.02h217.817V160.506z"/><path d="M95.918,224.51h0.254c5.892,0,10.669-4.776,10.669-10.669s-4.776-10.669-10.669-10.669h-0.254 c-5.891,0-10.669,4.776-10.669,10.669S90.027,224.51,95.918,224.51z"/><path d="M126.816,224.506h41.051c5.891,0,10.669-4.776,10.669-10.669s-4.776-10.669-10.669-10.669h-41.051 c-5.891,0-10.669,4.776-10.669,10.669S120.925,224.506,126.816,224.506z"/><path d="M96.172,331.17h-0.254c-5.891,0-10.669,4.776-10.669,10.669s4.778,10.669,10.669,10.669h0.254 c5.892,0,10.669-4.776,10.669-10.669S102.064,331.17,96.172,331.17z"/></svg>'],
            ['name' => 'Adventure', 'icon' => '<svg class="w-10 h-10 fill-current text-yellow-400" viewBox="0 0 64 64"><path d="M62.364,0.773c-0.694-0.509-1.526-0.772-2.366-0.772c-0.403,0-0.809,0.061-1.202,0.185L41.033,5.795 l-16.769-5.59C23.854,0.068,23.427,0,22.999,0c-0.468,0-0.937,0.082-1.382,0.247l-19,7C1.045,7.826,0,9.324,0,11v49 c0,1.274,0.607,2.473,1.636,3.227C2.33,63.735,3.16,64,4,64c0.404,0,0.811-0.062,1.204-0.186L23,58.194l17.796,5.62 C41.188,63.938,41.593,64,41.999,64c0.428,0,0.855-0.068,1.266-0.205l18-6C62.898,57.25,64,55.722,64,54V4 C64,2.726,63.393,1.527,62.364,0.773z M59.396,2.094c0.195-0.062,0.398-0.093,0.603-0.093c0.425,0,0.845,0.137,1.184,0.386 C61.694,2.762,62,3.365,62,4v25.086c-0.234-0.036-0.477-0.083-0.732-0.132c-1.167-0.233-1.899-0.521-2.78-1.546 c-1.04-1.188-0.435-3.11-1.581-4.114c-0.442-0.378-0.866-0.504-1.294-0.504c-0.824-0.001-1.662,0.468-2.675,0.504 c-1.666,0.074-2.812,0.756-4.194,0.756c-0.427,0-0.877-0.064-1.372-0.233c-1.342-0.46-1.856-1.511-3.178-2.061 c-0.426-0.18-0.815-0.291-1.193-0.374V15.19c1.126,0.401,2.13,0.773,3.575,0.912c0.791,0.079,1.43,0.38,2.07,0.38 c0.36,0,0.721-0.096,1.108-0.38c1.199-0.894,2.229-2.522,1.592-4.108c-0.414-1.045-1.411-0.652-1.995-1.545 c-0.626-0.984-0.329-1.883-0.785-3.078c0.284-0.876,0.768-1.492,1.036-2.185L59.396,2.094z M46.697,8.084 c0.111,0.293,0.146,0.587,0.189,0.959c0.076,0.65,0.181,1.542,0.776,2.479c0.603,0.921,1.396,1.265,1.872,1.448 c0.015,0.529-0.5,1.163-0.948,1.507c-0.121-0.017-0.339-0.071-0.505-0.113c-0.354-0.09-0.794-0.2-1.309-0.252 c-1.154-0.111-1.96-0.398-2.979-0.763c-0.246-0.088-0.52-0.182-0.794-0.275V7.271l3.962-1.251 c-0.104,0.229-0.211,0.462-0.299,0.734C46.522,7.188,46.534,7.658,46.697,8.084z M41,21.214c-0.543,0.011-1.101,0.04-1.729,0.04 c-0.204,0-0.414-0.004-0.634-0.013c-0.118-0.005-0.233-0.007-0.347-0.007c-1.107,0-1.954,0.213-2.901,0.213 c-0.467,0-0.957-0.052-1.517-0.206c-1.29-0.358-2.361-0.188-3.179-1.532c-0.949-1.595-0.901-3.518,0-5.152 c0.944-1.69,2.367-1.306,3.975-1.539c0.296-0.04,0.575-0.059,0.843-0.059c1.997,0,3.314,1.027,5.488,1.592V21.214z M31.443,44.217 c-0.53,1.29-1.509,1.559-2.606,1.559c-0.188,0-0.379-0.008-0.572-0.02c-1.178-0.069-1.708-0.907-2.78-1.539 c-1.021-0.602-1.726-1.426-2.484-2.075v-12.98c0.747,1.009,1.04,2.037,2.081,2.711c0.404,0.262,0.789,0.354,1.165,0.354 c0.901,0,1.754-0.529,2.706-0.529c0.289,0,0.588,0.049,0.899,0.176c1.597,0.666,2.955,1.078,3.57,3.098 c0.403,1.3,0.414,2.296,0,3.587c-0.408,1.305-1.603,1.236-1.979,2.569C31.108,42.266,31.873,43.145,31.443,44.217z M2,19.156 c0.875-0.007,1.722-0.066,2.839-0.123c1.395-0.077,2.395-0.816,3.539-0.816c0.384,0,0.784,0.083,1.221,0.302 c1.337,0.652,1.586,2.073,2.785,3.077c1.81,1.518,3.247,1.292,5.163,2.577c1.428,0.959,2.418,1.758,3.453,2.786v14.21 c-0.095-0.013-0.18-0.039-0.28-0.043c-0.372-0.011-0.71-0.055-1.025-0.055c-0.485,0-0.921,0.102-1.357,0.576 c-0.753,0.811-0.105,2.186-0.79,3.078c-0.498,0.662-1.057,0.868-1.666,0.868c-0.592,0-1.231-0.193-1.91-0.353 c-1.364-0.311-1.815-1.725-3.173-2.055c-0.509-0.122-0.916-0.22-1.329-0.22c-0.329,0-0.661,0.062-1.054,0.22 c-1.688,0.687-1.969,2.589-2.786,4.63c-0.668,1.69-0.005,3.489-1.188,4.616c-0.575,0.549-1.126,0.718-1.722,0.718 c-0.231,0-0.471-0.026-0.719-0.064V19.156z M3.309,9.124L21,2.606V24.25c-0.702-0.588-1.444-1.137-2.338-1.737 c-1.082-0.726-2.019-1.056-2.845-1.348c-0.836-0.294-1.439-0.507-2.148-1.102c-0.3-0.251-0.536-0.607-0.811-1.021 c-0.507-0.764-1.138-1.715-2.383-2.322c-0.661-0.331-1.373-0.504-2.098-0.504c-0.902,0-1.653,0.257-2.314,0.484 c-0.488,0.167-0.91,0.312-1.335,0.335L3.922,17.08C3.188,17.12,2.598,17.152,2,17.156V11C2,10.166,2.525,9.412,3.309,9.124z M4.603,61.907C4.407,61.969,4.204,62,4,62c-0.428,0-0.837-0.134-1.182-0.387C2.306,61.238,2,60.635,2,60v-4.904 c0.235,0.028,0.472,0.055,0.719,0.055c1.191,0,2.207-0.416,3.103-1.271c1.256-1.195,1.357-2.717,1.433-3.827 c0.037-0.561,0.073-1.09,0.235-1.5c0.18-0.449,0.338-0.899,0.489-1.333c0.482-1.375,0.74-1.996,1.19-2.18 c0.178-0.072,0.257-0.072,0.3-0.072c0.18,0,0.496,0.076,0.861,0.164c0.147,0.036,0.396,0.274,0.659,0.526 c0.554,0.529,1.312,1.255,2.537,1.534l0.363,0.088c0.597,0.147,1.273,0.314,1.991,0.314c1.315,0,2.444-0.576,3.265-1.666 c0.714-0.931,0.746-1.975,0.769-2.666c0.002-0.054,0.004-0.117,0.006-0.181c0.056,0.003,0.112,0.008,0.17,0.012 c0.183,0.014,0.372,0.026,0.57,0.032c0.118,0.005,0.229,0.027,0.34,0.068v13.535L4.603,61.907z M23.603,56.287L23,56.097V44.85 c0.422,0.373,0.894,0.751,1.47,1.091c0.24,0.142,0.469,0.32,0.71,0.51c0.656,0.515,1.555,1.219,2.968,1.302 c0.229,0.015,0.461,0.023,0.689,0.023c2.193,0,3.692-0.941,4.456-2.798c0.448-1.117,0.263-2.077,0.14-2.712 c-0.045-0.23-0.095-0.492-0.07-0.576l0.017-0.052c0.03-0.04,0.184-0.16,0.295-0.249c0.476-0.374,1.271-1.001,1.657-2.234 c0.526-1.645,0.528-3.077,0.001-4.777c-0.843-2.765-2.795-3.565-4.364-4.208l-0.347-0.143c-0.541-0.221-1.098-0.33-1.669-0.33 c-0.773,0-1.433,0.196-1.961,0.354c-0.291,0.087-0.592,0.176-0.745,0.176c-0.02-0.001-0.038-0.007-0.078-0.032 c-0.288-0.187-0.476-0.492-0.81-1.06c-0.279-0.476-0.627-1.066-1.146-1.657c-0.434-0.493-0.83-0.927-1.213-1.329V2 c0.216,0,0.428,0.034,0.632,0.103L40.4,7.692L41,7.892v4.565c-0.504-0.16-0.979-0.337-1.462-0.53 c-1.183-0.476-2.407-0.968-4.026-0.968c-0.364,0-0.739,0.025-1.114,0.077c-0.293,0.042-0.587,0.061-0.898,0.079 c-1.308,0.079-3.284,0.199-4.551,2.466c-1.275,2.313-1.263,4.982,0.027,7.151c1.096,1.803,2.63,2.09,3.646,2.281 c0.253,0.047,0.492,0.092,0.717,0.154c0.681,0.188,1.352,0.279,2.052,0.279c0.569,0,1.074-0.058,1.562-0.113 c0.451-0.051,0.877-0.1,1.339-0.1c0.086,0,0.175,0.002,0.265,0.005c0.249,0.011,0.486,0.015,0.716,0.015 c0.416,0,0.804-0.014,1.177-0.026c0.189-0.007,0.369-0.01,0.552-0.014v38.567L23.603,56.287z M60.632,55.897L43,61.774V23.449 c0.136,0.044,0.271,0.089,0.418,0.15c0.354,0.147,0.628,0.378,1.007,0.698c0.554,0.467,1.243,1.049,2.298,1.41 c0.677,0.23,1.336,0.342,2.021,0.342c0.955,0,1.78-0.222,2.508-0.418c0.597-0.161,1.16-0.312,1.775-0.34 c0.733-0.026,1.366-0.202,1.875-0.343c0.249-0.068,0.529-0.146,0.676-0.158c0.078,0.142,0.15,0.592,0.198,0.893 c0.139,0.859,0.328,2.037,1.207,3.042c1.218,1.417,2.395,1.89,3.893,2.189l0.104,0.021c0.331,0.062,0.672,0.118,1.021,0.16V54 C62,54.862,61.45,55.625,60.632,55.897z"/><path d="M55.306,39.322c-0.678-0.208-1.318-0.404-2.01-0.404c-0.652,0-1.241,0.177-1.804,0.543 c-0.632,0.417-0.814,0.932-0.856,1.289c-0.111,0.965,0.604,1.723,1.361,2.525c0.506,0.536,1.27,1.346,1.122,1.731 c-0.096,0.256-0.386,0.396-0.979,0.633c-0.737,0.296-1.853,0.741-2.094,2.071c-0.136,0.738,0.032,1.416,0.485,1.96 c0.637,0.763,1.791,1.183,3.253,1.183c1.49,0,2.955-0.43,3.928-1.156c1.653-1.268,2.287-3.12,1.884-5.503 C59.194,41.763,57.732,40.085,55.306,39.322z M56.505,48.103c-0.61,0.456-1.679,0.751-2.721,0.751 c-0.919,0-1.523-0.232-1.717-0.464c-0.034-0.041-0.093-0.111-0.055-0.32c0.037-0.199,0.191-0.301,0.87-0.572 c0.715-0.286,1.694-0.678,2.107-1.782c0.593-1.553-0.639-2.858-1.539-3.812c-0.238-0.252-0.57-0.604-0.735-0.843 c0.525-0.277,1.124-0.096,2.054,0.19c1.642,0.517,2.575,1.586,2.854,3.274C57.905,46.19,57.557,47.296,56.505,48.103z"/></svg>'],
            ['name' => 'RPG', 'icon' => '<svg class="w-10 h-10 fill-current text-purple-400" viewBox="0 0 512.32 512.32"><g transform="translate(1 1)"><path d="M69.969,70.147c-3.413,3.413-3.413,8.533,0,11.947l230.4,230.4c1.707,1.707,3.413,2.56,5.973,2.56 s4.267-0.853,5.973-2.56c3.413-3.413,3.413-8.533,0-11.947l-230.4-230.4C78.503,66.733,73.383,66.733,69.969,70.147z"/><path d="M477.863,92.333c0.853-0.853,1.707-1.707,1.707-2.56l30.72-78.507c1.707-3.413,0.853-6.827-1.707-9.387 c-2.56-2.56-5.973-3.413-9.387-2.56l-78.507,30.72c-0.853,0-1.707,0.853-2.56,1.707l-162.56,162.56L93.009,31.747 c-0.853-0.853-2.56-0.853-3.413-0.853L11.089,0.173C7.676-1.533,4.263-0.68,1.703,1.88s-2.56,5.973-1.707,9.387l30.72,78.507 c0,0.853,0.853,1.707,1.707,2.56L195.409,255.32l-36.88,36.88c-4.337-6.622-11.885-11.28-20.293-11.28 c-7.68-0.853-15.36,2.56-20.48,8.533s-7.68,14.507-5.973,22.187c2.62,13.1,7.502,25.551,14.429,36.771l-87.08,87.08 c-7.761-6.033-19.233-5.513-26.336,1.589l-7.68,7.68c-4.267,4.267-5.973,9.387-5.973,14.507c0,5.973,2.56,11.093,5.973,14.507 l31.573,31.573c4.267,4.267,9.387,5.973,14.507,5.973s10.24-2.56,14.507-5.973l7.68-7.68c4.267-4.267,5.973-9.387,5.973-14.507 c0-4.528-1.473-8.564-3.671-11.739l86.254-87.018c11.711,7.402,24.644,12.448,38.59,15.13c1.707,0,2.56,0,4.267,0 c5.973,0,11.947-2.56,15.36-6.827c5.973-5.12,9.387-12.8,9.387-20.48c0-7.911-3.756-15.429-9.753-20.114l36.206-36.206 l35.869,35.869c-7.285,4.548-11.976,12.453-11.976,21.304c0,7.68,3.413,15.36,9.387,20.48c4.267,4.267,10.24,5.973,16.213,5.973 c1.707,0,2.56-0.853,4.267,0c13.956-2.684,26.896-7.735,38.614-15.146l86.551,86.551c-2.819,3.752-3.992,7.986-3.992,12.222 c0,5.973,2.56,11.093,5.973,14.507l7.68,7.68c4.267,4.267,9.387,5.973,14.507,5.973s10.24-2.56,13.653-5.973l31.573-31.573 c4.267-4.267,5.973-9.387,5.973-14.507c0-5.973-2.56-11.093-5.973-14.507l-7.68-7.68c-6.954-6.954-18.095-7.589-25.842-1.949 l-87.133-87.133c6.603-11.113,11.401-23.418,13.989-36.358c0.853-8.533-0.853-16.213-5.973-22.187s-12.8-8.533-20.48-8.533 c-7.853,0-14.951,3.703-19.39,9.623l-35.666-35.666L477.863,92.333z M429.223,46.253l58.88-23.04l-23.04,58.88L303.358,242.096 l-11.522-11.522l148.48-148.48c3.413-3.413,3.413-8.533,0-11.947c-3.413-3.413-8.533-3.413-11.947,0l-148.48,148.48 l-12.371-12.371L429.223,46.253z M61.436,485.72l-7.68,7.68c-1.707,0.853-3.413,0.853-4.267,0l-32.427-31.573 c-0.853-0.853-0.853-1.707-0.853-2.56c0-0.853,0-1.707,1.707-2.56l7.68-7.68c0.853-0.853,3.413-0.853,4.267,0l1.543,1.543 c0.426,0.975,1.047,1.9,1.871,2.724l23.893,23.893c0.903,0.903,1.809,1.559,2.838,1.985l1.429,1.429 c0.853,0.853,0.853,1.707,0.853,2.56S62.289,484.867,61.436,485.72z M63.439,458.971l-12.106-11.787l84.978-84.978 c1.705,1.976,3.483,3.898,5.339,5.754c0.853,0.853,0.853,0.853,0.853,0.853c1.867,1.867,3.784,3.645,5.74,5.353L63.439,458.971z M209.916,379.907c-1.707,0.853-3.413,2.56-6.827,1.707c-17.92-3.413-35.84-12.8-48.64-25.6 c-12.8-12.8-21.333-29.867-24.747-48.64c-0.853-2.56,0-5.973,1.707-7.68c0.853-0.853,3.413-2.56,6.827-2.56 s6.827,3.413,7.68,6.827c0.166,0.941,0.352,1.877,0.554,2.81c-0.03,0.799,0.059,1.589,0.3,2.31 c3.413,12.8,10.24,24.747,19.627,34.987c9.387,9.387,21.333,16.213,34.133,19.627c0.653,0,1.305,0,1.958,0 c1.337,0.322,2.676,0.617,4.015,0.853c4.267,0.853,6.827,4.267,6.827,8.533C213.329,375.64,211.623,378.2,209.916,379.907z M200.529,344.92c-8.533-2.56-15.36-7.68-22.187-13.653c-5.973-5.973-11.093-13.653-13.653-22.187l42.24-42.24l35.84,35.84 L200.529,344.92z M482.983,448.173c0.853,0,1.707,0,2.56,0.853l7.68,7.68c0.853,0.853,0.853,1.707,0.853,2.56 c0,0,0,0.853-1.707,2.56L460.796,493.4c-0.853,0.853-3.413,0.853-4.267,0l-7.68-7.68c-0.853-0.853-0.853-1.707-0.853-2.56 c0-0.853,0-1.707,0.853-2.56l1.669-1.669c0.934-0.42,1.848-0.995,2.597-1.744l23.893-23.893c0.824-0.824,1.445-1.749,1.871-2.724 l1.543-1.543C481.276,448.173,482.129,448.173,482.983,448.173z M458.663,446.893l-11.947,11.947l-84.673-84.673 c1.956-1.708,3.873-3.487,5.74-5.353c0.853-0.853,0.853-0.853,0.853-0.853c1.797-1.903,3.528-3.873,5.192-5.902L458.663,446.893z M372.903,297.987c3.413,0,5.12,1.707,5.973,2.56c1.707,2.56,2.56,5.12,1.707,7.68c-3.413,17.92-11.947,34.133-24.746,47.786 l-0.001,0.001c-12.8,12.8-29.866,22.186-48.639,25.599c-3.413,0.853-5.973-0.853-6.827-1.707 c-2.56-1.707-3.413-4.267-3.413-6.827c0-3.413,3.413-6.827,6.827-7.68c14.507-2.56,28.16-10.24,40.107-21.333 c0.211-0.211,0.411-0.43,0.619-0.643c0.07-0.071,0.157-0.139,0.235-0.21c10.24-9.387,16.213-21.333,19.627-34.987 c0-0.725-0.076-1.52-0.213-2.323c0.07-0.364,0.149-0.725,0.213-1.09C365.223,300.547,368.636,297.987,372.903,297.987z M331.503,331.691c-0.232,0.229-0.467,0.453-0.701,0.678c-6.731,5.866-13.486,10.88-21.899,13.404L45.223,82.093L23.036,22.36 l58.88,23.04l262.827,264.533C342.242,317.434,337.297,325.743,331.503,331.691z"/></g></svg>'],
            ['name' => 'Strategy', 'icon' => '<svg class="w-10 h-10 fill-current text-blue-400" viewBox="0 0 497.072 497.072"><path d="M495.48,300.568c-20.24-35.432-30.944-75.728-30.944-116.528v-23.504h16v-16h-16v-37.808 c9.928-11.28,16-26.024,16-42.192c0-35.288-28.712-64-64-64c-33.568,0-61.128,25.992-63.744,58.904 c-31.864-17.616-67.608-26.904-104.256-26.904c-98.464,0-183.168,65.512-208.424,160H8.536v68.28l24,16v19.72h-16v16h16v23.504 c0,40.808-10.704,81.104-30.944,116.528L0,455.36l10.288,41.176h140.504l10.288-41.176l-1.592-2.792 c-1.84-3.224-3.52-6.536-5.2-9.832c29.264,14.24,61.376,21.8,94.248,21.8c81.76,0,157.016-46.976,193.352-120h44.896 l10.288-41.176L495.48,300.568z M416.536,16.536c26.472,0,48,21.528,48,48s-21.528,48-48,48s-48-21.528-48-48 S390.064,16.536,416.536,16.536z M448.536,119.88v24.656h-64V119.88c9.424,5.472,20.336,8.656,32,8.656 S439.112,125.352,448.536,119.88z M24.536,252.256v-43.72h32v32h48v-32h32v43.72l-18.424,12.28H42.96L24.536,252.256z M112.536,280.536v16h-64v-16H112.536z M138.288,480.536H22.784l-4-16h85.752v-16H21.76c16.208-32.312,25.264-67.84,26.504-104 h32.272v-16h-32v-16h64v16h-16v16h16.272c1.232,36.16,10.296,71.688,26.504,104h-18.776v16h21.752L138.288,480.536z M248.536,448.536c-37.16,0-73.304-10.352-104.824-29.816c-9.912-26.36-15.176-54.376-15.176-82.68v-23.504h16v-16h-16v-19.72 l24-16v-68.28h-64v32h-16v-32H56.728c24.712-85.248,102.08-144,191.808-144c37.488,0,73.952,10.512,105.672,30.304 c2.416,10.52,7.424,20.04,14.328,27.88v37.816h-16v16h16v23.504c0,40.808-10.704,81.104-30.944,116.528L336,303.36l10.288,41.176 h77.632C389.032,408.096,321.56,448.536,248.536,448.536z M474.288,328.536H358.784l-4-16h85.752v-16H357.76 c16.208-32.312,25.264-67.84,26.504-104h32.272v-16h-32v-16h64v16h-16v16h16.272c1.232,36.16,10.296,71.688,26.504,104h-18.776 v16h21.752L474.288,328.536z"/><path d="M248.536,392.536c-10.88,0-21.736-1.328-32.424-3.824c0.184-1.376,0.424-2.744,0.424-4.176c0-17.648-14.352-32-32-32 s-32,14.352-32,32s14.352,32,32,32c10.416,0,19.592-5.072,25.44-12.8c12.688,3.176,25.608,4.8,38.56,4.8 c39.944,0,78.272-14.952,107.912-42.104l-10.816-11.8C318.96,379.072,284.472,392.536,248.536,392.536z M184.536,400.536 c-8.824,0-16-7.176-16-16c0-8.824,7.176-16,16-16c8.824,0,16,7.176,16,16C200.536,393.36,193.36,400.536,184.536,400.536z"/><path d="M280.952,108.448c-0.176,1.352-0.416,2.688-0.416,4.088c0,17.648,14.352,32,32,32s32-14.352,32-32s-14.352-32-32-32 c-10.464,0-19.688,5.12-25.528,12.912c-53.392-13.272-107.384,1.424-146.344,37.2l10.832,11.784 C185.992,110.744,233.472,97.384,280.952,108.448z M312.536,96.536c8.824,0,16,7.176,16,16s-7.176,16-16,16 c-8.824,0-16-7.176-16-16C296.536,103.712,303.712,96.536,312.536,96.536z"/><path d="M112.68,164.312l13.576,8.456c3.256-5.224,6.904-10.328,10.856-15.168l-12.392-10.128 C120.344,152.84,116.288,158.504,112.68,164.312z"/></svg>'],
            ['name' => 'Simulation', 'icon' => '<svg class="w-10 h-10 fill-current text-lime-400" viewBox="0 0 75.326 75.326"><path d="M20.106,24.004c0.02-0.675,0.08-2.73,1.541-3.282c1.449-0.547,2.929,0.935,3.211,1.236 c0.377,0.404,0.356,1.037-0.047,1.414c-0.403,0.377-1.036,0.356-1.414-0.047c-0.339-0.358-0.858-0.708-1.068-0.722 c0.015,0.034-0.192,0.359-0.225,1.459c-0.109,3.698,2.764,4.698,2.793,4.708c0.317,0.105,0.56,0.362,0.648,0.684l0.045,0.165 c0.016,0.059,0.027,0.12,0.032,0.182c0.81,9.646,8.438,12.767,12.005,12.767s11.196-3.121,12.005-12.767 c0.005-0.062,0.016-0.123,0.032-0.182l0.045-0.165c0.089-0.322,0.332-0.578,0.648-0.684c0.112-0.039,2.901-1.058,2.793-4.708 c-0.036-1.233-0.292-1.493-0.295-1.496c-0.109,0.031-0.666,0.403-0.998,0.758c-0.377,0.404-1.01,0.424-1.414,0.047 c-0.403-0.377-0.424-1.01-0.047-1.414c0.283-0.302,1.766-1.783,3.212-1.236c1.461,0.552,1.521,2.606,1.541,3.282 c0.107,3.633-2.035,5.644-3.568,6.415c-1.037,9.756-8.731,14.149-13.955,14.149s-12.917-4.394-13.955-14.149 C22.141,29.648,19.999,27.638,20.106,24.004z M65.724,53.549c0-0.051-0.004-0.102-0.012-0.152 c-1.057-6.872-6.599-13.306-14.12-16.394c-0.511-0.209-1.096,0.034-1.305,0.545s0.035,1.095,0.545,1.305 c6.858,2.815,11.91,8.608,12.891,14.773l0.004,10.984c0.003,0.036,0.28,3.557-1.77,5.791c-1.16,1.264-2.885,1.904-5.129,1.904 c-0.552,0-1,0.448-1,1s0.448,1,1,1c2.834,0,5.06-0.863,6.614-2.565c2.644-2.895,2.293-7.13,2.281-7.218V53.549z M18.244,72.307 c-2.244,0-3.969-0.641-5.129-1.905c-2.05-2.233-1.773-5.755-1.766-5.878V53.627c0.982-6.165,6.033-11.958,12.891-14.773 c0.511-0.21,0.755-0.794,0.545-1.305c-0.21-0.511-0.793-0.754-1.305-0.545c-7.52,3.088-13.062,9.522-14.119,16.394 c-0.008,0.05-0.012,0.101-0.012,0.152l0.004,10.884c-0.016,0.179-0.367,4.414,2.277,7.308c1.554,1.702,3.78,2.565,6.614,2.565 c0.552,0,1-0.448,1-1S18.796,72.307,18.244,72.307z M52.456,71.055c-1.23,0-2.352-0.432-3.258-1.131 c-2.883,3.431-7.084,5.402-11.569,5.402c-4.487,0-8.65-1.963-11.531-5.377c-0.901,0.684-2.011,1.106-3.227,1.106 c-2.961,0-5.37-2.409-5.37-5.37l-0.001-6.814c-0.008-1.463-0.017-3.122,1.161-4.307c0.88-0.885,2.258-1.315,4.21-1.315 c0.472,0,0.907,0.028,1.313,0.078c2.573-5.013,7.785-8.228,13.446-8.228c5.631,0,10.759,3.098,13.383,8.085 c0.026,0.05,0.028,0.103,0.045,0.155c0.43-0.058,0.893-0.09,1.399-0.09c1.953,0,3.33,0.43,4.21,1.315 c1.178,1.185,1.169,2.843,1.161,4.307l-0.001,6.814C57.826,68.646,55.417,71.055,52.456,71.055z M40.213,47.357v6.079 c1.925,0.737,3.455,2.267,4.191,4.191h2.701c0.059-1.1,0.287-2.206,1.14-3.063c0.251-0.253,0.555-0.458,0.889-0.637 C47.249,50.481,43.976,48.109,40.213,47.357z M28.24,62.714h2.582c-0.288-0.781-0.454-1.622-0.454-2.502 c0-0.197,0.014-0.391,0.03-0.584h-2.158L28.24,62.714z M37.629,65.472c2.9,0,5.26-2.36,5.26-5.26s-2.359-5.26-5.26-5.26 s-5.26,2.359-5.26,5.26S34.729,65.472,37.629,65.472z M44.889,60.212c0,0.88-0.165,1.721-0.454,2.502h2.65l-0.001-3.086h-2.226 C44.875,59.821,44.889,60.015,44.889,60.212z M37.629,47.099c-0.196,0-0.39,0.013-0.584,0.021v5.862 c0.193-0.016,0.387-0.03,0.584-0.03s0.391,0.014,0.584,0.03v-5.863C38.019,47.11,37.825,47.099,37.629,47.099z M26.134,53.902 c0.356,0.185,0.682,0.396,0.946,0.662c0.853,0.857,1.081,1.963,1.14,3.063h2.633c0.737-1.925,2.267-3.455,4.191-4.191v-6.072 C31.294,48.125,28.003,50.506,26.134,53.902z M25.928,67.08h-1.97c-0.552,0-1-0.448-1-1s0.448-1,1-1h2.281l0-1.622h-2.282 c-0.552,0-1-0.448-1-1s0.448-1,1-1h2.282l0-1.752h-2.283c-0.552,0-1-0.448-1-1s0.448-1,1-1h2.256 c-0.042-0.713-0.163-1.34-0.552-1.732c-0.479-0.481-1.418-0.726-2.792-0.726c-1.374,0-2.313,0.244-2.792,0.726 c-0.591,0.595-0.585,1.708-0.579,2.885l0.001,6.825c0,1.858,1.512,3.37,3.37,3.37C24.229,69.055,25.396,68.242,25.928,67.08z M47.78,68.515c0.026-0.032,0.063-0.047,0.091-0.075c-0.488-0.808-0.785-1.744-0.785-2.755l0-0.971h-3.772 c-1.331,1.677-3.382,2.758-5.685,2.758c-2.303,0-4.355-1.081-5.686-2.76c-0.002,0-0.004,0.001-0.006,0.001H28.24l0,0.971 c0,1.018-0.301,1.961-0.795,2.773c2.505,3.092,6.2,4.868,10.184,4.868C41.576,73.326,45.276,71.572,47.78,68.515z M52.456,69.055 c1.858,0,3.37-1.512,3.37-3.37l0.001-6.825c0.006-1.178,0.012-2.291-0.579-2.886c-0.479-0.481-1.418-0.726-2.792-0.726 c-1.374,0-2.313,0.244-2.792,0.726c-0.39,0.392-0.511,1.019-0.552,1.731h2.256c0.552,0,1,0.448,1,1s-0.448,1-1,1h-2.283l0,1.752 h2.283c0.552,0,1,0.448,1,1s-0.448,1-1,1h-2.282l0,1.622h2.282c0.552,0,1,0.448,1,1s-0.448,1-1,1h-1.97 C49.93,68.242,51.097,69.055,52.456,69.055z M38.213,25.029v4.845h-2.169c-0.552,0-1,0.448-1,1s0.448,1,1,1h3.169 c0.552,0,1-0.448,1-1v-5.845c0-0.552-0.448-1-1-1S38.213,24.477,38.213,25.029z M21.57,16.916c0-7.055,0.01-14.326,12.452-15.466 C34.187,0.641,35.736,0,37.629,0c1.893,0,3.442,0.641,3.608,1.449c12.441,1.14,12.451,8.411,12.451,15.466c0,0.552-0.448,1-1,1 H22.57C22.018,17.916,21.57,17.468,21.57,16.916z M23.571,15.916h28.116C51.661,8.783,51.067,3.29,37.629,3.29 C24.191,3.29,23.597,8.783,23.571,15.916z M39.441,11.369h-3.624c-1.88,0-3.418,1.18-3.418,2.623h10.461 C42.859,12.549,41.321,11.369,39.441,11.369z M45.15,44.253c-0.235,0.5-0.02,1.095,0.48,1.33l4.779,2.243 c0.135,0.063,0.28,0.095,0.425,0.095c0.176,0,0.352-0.046,0.508-0.139c0.285-0.168,0.468-0.466,0.49-0.796l0.378-5.757 c0.036-0.551-0.381-1.027-0.933-1.063c-0.551-0.032-1.027,0.382-1.063,0.933l-0.282,4.295l-3.453-1.621 C45.98,43.538,45.385,43.752,45.15,44.253z M28.778,43.773l-3.452,1.621l-0.282-4.295c-0.037-0.551-0.519-0.973-1.063-0.933 c-0.551,0.037-0.969,0.513-0.933,1.063l0.378,5.757c0.022,0.33,0.205,0.628,0.49,0.796c0.156,0.092,0.332,0.139,0.508,0.139 c0.145,0,0.29-0.031,0.425-0.095l4.779-2.243c0.5-0.235,0.715-0.83,0.48-1.33C29.873,43.753,29.277,43.539,28.778,43.773z M19.497,20.254c0-0.552-0.447-0.999-0.999-0.999c-0.149,0-14.906,0.174-14.906,15.246v38.158c0,0.552,0.448,1,1,1s1-0.448,1-1 V34.501c0-13.054,12.381-13.245,12.908-13.246C19.051,21.253,19.498,20.805,19.497,20.254z M56.829,19.255c-0.552,0-1,0.448-1,1 s0.448,1,1,1c0.527,0,12.906,0.15,12.906,13.246v38.158c0,0.552,0.448,1,1,1s1-0.448,1-1V34.501 C71.734,19.43,56.978,19.255,56.829,19.255z"/></svg>'],
            ['name' => 'Racing', 'icon' => '<svg class="w-10 h-10 fill-current text-orange-400" viewBox="0 0 473.982 473.982"><path d="m330.563,187.578h-187.145c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5 7.5,7.5h187.146c4.142,0 7.5-3.358 7.5-7.5s-3.359-7.5-7.501-7.5z"/><path d="m65.234,221.578h-27.718c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5 7.5,7.5h27.718c13.697,0 25.247,9.372 28.575,22.04h-54.372c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5 7.5,7.5h62.835c4.142,0 7.5-3.358 7.5-7.5 0.001-24.56-19.979-44.54-44.538-44.54z"/><path d="m408.747,236.578h27.718c4.142,0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-27.718c-24.559,0-44.539,19.98-44.539,44.54 0,4.142 3.358,7.5 7.5,7.5h62.835c4.142,0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-54.371c3.328-12.668 14.878-22.04 28.575-22.04z"/><path d="m458.676,188.615c-10.513-10.987-24.789-17.038-40.2-17.038-0.675,0-1.347,0.091-1.997,0.271l-24.103,6.659-35.507-68.214c-6.357-12.212-22.121-21.778-35.887-21.778h-167.984c-13.767,0-29.53,9.566-35.887,21.778l-35.507,68.214-24.103-6.659c-0.65-0.18-1.322-0.271-1.997-0.271-15.411,0-29.688,6.051-40.2,17.038s-15.928,25.517-15.247,40.958l5.443,108.519c0.2,3.991 3.495,7.125 7.491,7.125h2.5v32.75c0,4.142 3.358,7.5 7.5,7.5h78.911c4.142,0 7.5-3.358 7.5-7.5v-10.25c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v2.75h-63.912v-25.25h413v25.25h-63.911v-2.75c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v10.25c0,4.142 3.358,7.5 7.5,7.5h78.911c4.142,0 7.5-3.358 7.5-7.5v-32.75h2.5c3.996,0 7.291-3.133 7.491-7.125l5.444-108.563c0.68-15.397-4.736-29.927-15.249-40.914zm.266,40.207l-5.085,101.394h-433.733l-5.083-101.349c-0.497-11.268 3.445-21.88 11.102-29.881 7.434-7.77 17.482-12.15 28.385-12.396l28.854,7.972c0.018,0.005 0.036,0.01 0.055,0.015l27.984,7.731c3.991,1.103 8.123-1.239 9.226-5.232 1.103-3.992-1.239-8.123-5.232-9.226l-19.026-5.256 34.028-65.373c3.8-7.3 14.352-13.704 22.582-13.704h167.984c8.229,0 18.782,6.404 22.582,13.704l34.028,65.373-19.026,5.256c-3.993,1.103-6.335,5.234-5.232,9.226 1.103,3.993 5.234,6.334 9.226,5.232l56.893-15.718c10.903,0.247 20.951,4.627 28.385,12.396 7.656,8.001 11.598,18.613 11.103,29.836z"/><path d="m418.476,156.578h26.128c4.142,0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-26.128c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5 7.5,7.5z"/><path d="m29.376,156.578h26.128c4.142,0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-26.128c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5 7.5,7.5z"/><path d="m320.176,280.171h-166.371c-4.142,0-7.5,3.358-7.5,7.5v20.045c0,4.142 3.358,7.5 7.5,7.5s7.5-3.358 7.5-7.5v-12.545h151.371v12.545c0,4.142 3.358,7.5 7.5,7.5s7.5-3.358 7.5-7.5v-20.045c-5.68434e-14-4.142-3.358-7.5-7.5-7.5z"/><path d="m278.583,242.578c4.142,0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-83.186c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5 7.5,7.5h83.186z"/></svg>'],
            ['name' => 'Sports', 'icon' => '<svg class="w-10 h-10 fill-current text-green-400" viewBox="0 0 122.88 122.88"><path d="M61.44,0c16.97,0,32.33,6.88,43.44,18c11.12,11.12,18,26.48,18,43.44c0,16.97-6.88,32.33-18,43.44 c-11.12,11.12-26.48,18-43.44,18S29.11,116,18,104.88C6.88,93.77,0,78.41,0,61.44C0,44.47,6.88,29.11,18,18 C29.11,6.88,44.47,0,61.44,0L61.44,0z M76.85,117.08L76.73,117l6.89-23.09L69.41,78.15L52.66,78L39.38,94.62l6.66,22.32l-0.15,0.1 c4.95,1.38,10.16,2.12,15.55,2.12C66.78,119.16,71.95,118.44,76.85,117.08L76.85,117.08z M12.22,91.61l24.34,0.12L49.28,75.8 l-5.26-16.12l-21.42-9.3L3.78,64.08C4.23,74.14,7.26,83.53,12.22,91.61L12.22,91.61z M16.77,24.88l7.4,22.14l19.98,8.68 l15.44-11.97V20.94L40.51,7.63c-7.52,2.93-14.28,7.39-19.89,13C19.27,21.98,17.98,23.4,16.77,24.88L16.77,24.88z M81.7,7.37 L63.3,20.77V43.7L77.8,54.91l20.81-8.92l7.18-21.49c-1.12-1.35-2.3-2.64-3.54-3.88C96.48,14.85,89.49,10.29,81.7,7.37L81.7,7.37z M119.09,64.36l-0.02,0.01L99.09,49.82l-19.81,8.49l-6.08,18.03l13.73,15.23c0.06,0.06,0.09,0.13,0.11,0.21l23.6-0.11 C115.56,83.65,118.59,74.34,119.09,64.36L119.09,64.36z"/></svg>'],
            ['name' => 'Horror', 'icon' => '<svg class="w-10 h-10 fill-current text-gray-400" viewBox="0 0 512 512"><path d="M507.88,226.131c-2.409-4.385-5.453-7.588-7.942-9.942c-5.035-4.722-11.588-7.219-18.261-7.219 c-3.132,0-6.288,0.554-9.323,1.678l-0.225,0.088l-0.208,0.097c0,0-0.113,0.056-0.37,0.16c-1.822,0.836-10.279,4.658-19.12,8.239 c-4.064,1.646-8.199,3.228-11.756,4.433c1.084-2.674,2.248-5.975,3.332-10.062c1.502-5.653,2.875-12.801,3.831-21.907 c0.417-4.047,0.626-8.046,0.626-12.005c0.008-16.744-3.686-32.604-10.206-46.905c-9.797-21.457-25.906-39.422-45.548-52.046 c-19.651-12.623-42.874-19.939-66.942-19.939c-13.049,0-25.496,2.409-37.044,6.512c-17.337,6.152-32.651,16.069-45.219,27.48 c-12.559,11.42-22.388,24.308-28.708,36.739c-0.2,0.41-0.899,1.461-1.951,2.562c-1.574,1.686-3.935,3.59-6.665,4.971 c-2.731,1.397-5.798,2.288-9.035,2.288c-3.252-0.016-6.754-0.843-10.761-3.364c-8.102-5.108-15.105-12.488-21.682-21.265 c-6.577-8.753-12.696-18.855-19.161-29.094c-4.103-6.504-9.138-11.45-14.727-14.743c-5.59-3.284-11.708-4.891-17.747-4.883 c-8.215,0-16.214,2.9-22.991,7.982c-6.77,5.083-12.35,12.351-15.916,21.256c-4.819,12.054-10.4,24.364-16.912,34.94 c-6.496,10.584-13.956,19.345-22.139,24.581c-11.355,7.3-19.884,14.551-25.778,21.706c-2.947,3.574-5.236,7.14-6.834,10.729 C0.908,192.797,0,196.459,0,200.105c-0.008,3.453,0.86,6.882,2.538,9.876c1.253,2.249,2.939,4.241,4.931,5.935 c2.987,2.554,6.634,4.473,10.801,5.886c4.184,1.405,8.906,2.322,14.19,2.771c1.903,0.16,3.774,0.224,5.63,0.224 c8.776,0,16.944-1.718,24.339-4.36c11.114-3.959,20.518-9.966,28.042-15.538c3.758-2.795,7.042-5.477,9.805-7.758 c0.546-0.457,1.06-0.883,1.574-1.301c0.248,2.41,0.489,5.292,0.835,8.352c0.313,2.746,0.723,5.638,1.349,8.504 c0.49,2.16,1.1,4.305,1.935,6.4c0.618,1.574,1.381,3.116,2.345,4.593c1.446,2.192,3.381,4.273,5.926,5.758 c2.521,1.502,5.581,2.305,8.721,2.289c4.666-0.008,8.802-0.611,12.456-1.157c3.653-0.546,6.826-1.003,9.58-0.996 c2,0,3.774,0.217,5.566,0.763c0.289,0.08,0.578,0.185,0.868,0.297c-0.402,0.377-0.78,0.787-1.085,1.285v-0.008 c-14.358,23.24-32.234,37.703-49.62,48.447c-8.689,5.365-17.249,9.773-25.15,13.844c-7.918,4.08-15.178,7.782-21.361,11.949 c-9.829,6.649-17.907,13.298-23.778,19.73c-2.932,3.228-5.316,6.401-7.075,9.628c-0.883,1.622-1.598,3.269-2.112,4.963 c-0.506,1.702-0.827,3.469-0.827,5.284c0,1.382,0.185,2.795,0.595,4.168c0.691,2.408,2.112,4.634,3.943,6.351 c1.365,1.285,2.956,2.329,4.666,3.164c2.577,1.26,5.485,2.096,8.784,2.658c3.284,0.547,6.979,0.812,11.155,0.812 c7.829,0,17.37-0.94,28.973-2.932c4.866-0.843,8.906-1.324,12.15-1.542c-2.658,1.373-5.934,2.947-9.596,4.61 c-14.647,6.673-35.486,14.856-49.546,19.426c-4.313,1.405-7.967,3.903-10.52,7.179c-2.562,3.26-3.983,7.316-3.975,11.452 c0,3.429,0.964,6.89,2.794,10.014c2.746,4.705,7.364,8.616,13.516,11.307c6.159,2.682,13.884,4.208,23.303,4.208 c2.851,0,5.847-0.136,9.01-0.418c17.458-1.614,34.434-2.61,50.15-4.168c8.994-0.9,17.57-1.992,25.608-3.51 c-1.55,1.004-2.971,2.024-4.248,3.084c-2.056,1.735-3.766,3.566-5.067,5.662c-1.284,2.088-2.168,4.545-2.168,7.195 c-0.008,1.622,0.344,3.269,0.988,4.73c0.562,1.284,1.325,2.425,2.2,3.421c1.549,1.742,3.396,3.028,5.444,4.088 c3.091,1.574,6.657,2.642,10.808,3.372c4.144,0.731,8.866,1.1,14.174,1.1c14.038-0.007,32.226-2.61,53.86-8.865 c21.633-6.264,46.68-16.197,74.24-30.941c22.782-12.182,42.079-25.094,58.662-39.06c14.752-12.431,27.335-25.689,38.329-39.96 c0.056,0.032,0.096,0.057,0.136,0.081c2.498,1.341,5.485,2.152,8.649,2.144c3.758,0,7.644-1.084,11.587-3.14 c2.754-1.437,5.204-2.409,7.268-3.011c2.08-0.602,3.758-0.827,4.962-0.827c0.78,0,1.333,0.096,1.671,0.192 c-0.016,0.097-0.032,0.169-0.065,0.298c-0.201,0.602-0.65,1.558-1.493,2.698c-0.836,1.149-2.048,2.489-3.67,3.919 c-3.943,3.461-7.018,7.652-9.17,12.086c-2.145,4.457-3.397,9.155-3.414,13.852c0,2.288,0.314,4.577,1.044,6.801 c0.554,1.663,1.35,3.284,2.433,4.77c1.614,2.232,3.887,4.103,6.553,5.292c2.658,1.205,5.653,1.767,8.841,1.767 c4.609-0.008,9.661-1.117,15.442-3.348c5.773-2.241,12.279-5.622,19.698-10.392c12.904-8.312,22.581-19.313,29.808-31.054 c10.817-17.651,16.238-36.979,19.04-52.527c2.738-15.201,2.947-26.781,2.947-29.68c0.723-3.381,1.076-6.585,1.076-9.557 C512.016,236.113,510.305,230.516,507.88,226.131z M498.236,250.255l-0.168,0.747l0.007,0.771c0,0,0,0.136,0,0.321 c0.008,2.666-0.256,21.506-6.255,43.034c-2.996,10.777-7.412,22.204-13.844,32.644c-6.416,10.448-14.8,19.883-25.802,26.966 c-6.858,4.424-12.656,7.396-17.386,9.219c-4.714,1.839-8.352,2.498-10.8,2.489c-1.518,0-2.578-0.241-3.292-0.522 c-0.546-0.209-0.9-0.442-1.196-0.691c-0.426-0.378-0.755-0.827-1.044-1.558c-0.281-0.731-0.482-1.759-0.482-3.011 c-0.008-2.289,0.691-5.308,2.12-8.255c1.422-2.947,3.542-5.814,6.063-8.007c2.988-2.617,5.308-5.292,6.979-8.086 c0.827-1.406,1.501-2.834,1.975-4.32c0.465-1.485,0.746-3.027,0.746-4.617c0-1.766-0.369-3.613-1.164-5.292 c-0.594-1.262-1.429-2.41-2.417-3.373c-1.486-1.446-3.284-2.442-5.148-3.06c-1.879-0.618-3.838-0.875-5.87-0.883 c-2.69,0.008-5.524,0.466-8.552,1.341c-3.011,0.875-6.223,2.176-9.612,3.943c-2.554,1.333-4.385,1.686-5.653,1.686 c-0.442,0-0.82-0.048-1.181-0.121c1.55-2.417,3.51-5.557,5.709-9.266c5.196-8.769,11.677-20.615,16.848-33.19 c1.365-3.284-0.209-7.034-3.493-8.384c-3.285-1.357-7.027,0.209-8.393,3.493c-4.81,11.708-11.025,23.112-16.02,31.519 c-2.489,4.216-4.69,7.677-6.248,10.078c-0.779,1.205-1.397,2.144-1.822,2.762c-0.209,0.322-0.362,0.554-0.475,0.716l-0.096,0.136 c-11.331,15.506-24.436,29.704-40.224,43.018c-15.812,13.314-34.322,25.729-56.445,37.566 c-26.822,14.334-51.065,23.93-71.751,29.913c-20.678,5.99-37.839,8.367-50.286,8.36c-4.095,0-7.669-0.249-10.656-0.699 c-2.248-0.322-4.168-0.763-5.701-1.245c-2.313-0.723-3.742-1.606-4.248-2.08l-0.032-0.04c0.096-0.184,0.256-0.45,0.514-0.779 c0.787-1.028,2.449-2.57,5.067-4.232c2.618-1.67,6.159-3.485,10.632-5.308c6.207-2.538,10.721-4.818,14.02-7.002 c1.655-1.108,3.02-2.177,4.224-3.501c0.603-0.667,1.172-1.414,1.67-2.393c0.49-0.955,0.94-2.217,0.94-3.774 c0.008-0.827-0.145-1.727-0.442-2.561c-0.53-1.494-1.534-2.691-2.49-3.437c-0.722-0.57-1.437-0.948-2.095-1.22 c-0.996-0.41-1.896-0.611-2.778-0.748c-0.883-0.128-1.735-0.176-2.618-0.184c-2.08,0.008-4.296,0.273-6.794,0.755 c-2.481,0.497-5.227,1.22-8.206,2.184c-10.994,3.598-24.678,5.67-40.015,7.195c-15.346,1.518-32.331,2.506-50.062,4.144 c-2.794,0.257-5.404,0.378-7.83,0.378c-5.356,0-9.804-0.586-13.394-1.518c-5.396-1.397-8.825-3.566-10.793-5.613 c-0.988-1.044-1.614-2.039-2.007-2.971c-0.402-0.923-0.562-1.783-0.562-2.578c0.016-1.277,0.394-2.442,1.245-3.549 c0.876-1.1,2.241-2.16,4.369-2.859c9.836-3.196,22.405-7.926,34.226-12.768c5.894-2.425,11.604-4.875,16.67-7.187 c5.075-2.305,9.5-4.457,12.938-6.36c2.834-1.582,5.171-3.156,7.114-4.986c0.964-0.94,1.83-1.944,2.594-3.204 c0.369-0.634,0.707-1.341,0.964-2.152c0.258-0.811,0.434-1.727,0.434-2.722c0.008-1.028-0.201-2.144-0.61-3.149 c-0.352-0.883-0.851-1.67-1.405-2.336c-0.996-1.164-2.112-1.919-3.196-2.465c-1.63-0.82-3.252-1.237-5.01-1.526 c-1.767-0.273-3.678-0.393-5.83-0.393c-4.722,0.007-10.616,0.594-18.052,1.871c-11.09,1.911-19.996,2.746-26.789,2.746 c-3.678,0-6.738-0.249-9.146-0.667c-1.8-0.305-3.245-0.715-4.313-1.132c-0.802-0.321-1.389-0.642-1.798-0.915 c-0.61-0.426-0.779-0.699-0.883-0.851c-0.08-0.168-0.136-0.297-0.145-0.739c0-0.337,0.064-0.867,0.273-1.566 c0.369-1.229,1.205-2.963,2.61-4.979c2.088-3.019,5.372-6.624,9.652-10.439c4.272-3.83,9.548-7.878,15.595-11.973 c5.204-3.518,12.134-7.107,20.044-11.17c11.869-6.111,25.954-13.338,40.015-23.786c14.053-10.456,28.106-24.187,39.822-43.171 c0.096-0.153,0.152-0.33,0.225-0.482c0.265,0.289,0.53,0.586,0.795,0.9c2.819,3.308,5.95,7.774,9.364,13.7 c1.774,3.067,5.701,4.12,8.784,2.352c3.068-1.774,4.12-5.701,2.346-8.785c-3.293-5.71-6.449-10.376-9.556-14.214 c-4.657-5.75-9.283-9.684-14.109-12.15c-2.401-1.22-4.842-2.064-7.212-2.578c-2.384-0.514-4.706-0.698-6.93-0.698 c-4.103,0.008-7.869,0.618-11.467,1.14c-3.598,0.546-7.035,1.012-10.568,1.012c-0.626,0-1.076-0.08-1.445-0.185 c-0.667-0.209-1.117-0.482-1.719-1.108c-0.506-0.547-1.076-1.398-1.606-2.562c-0.923-2.031-1.671-4.946-2.176-8.143 c-0.386-2.401-0.666-4.954-0.907-7.46c-0.185-1.887-0.354-3.726-0.538-5.509c-0.152-1.325-0.305-2.602-0.514-3.839 c-0.169-0.931-0.353-1.847-0.602-2.762c-0.185-0.682-0.418-1.382-0.723-2.104c-0.474-1.068-1.108-2.281-2.393-3.486 c-0.65-0.586-1.47-1.164-2.433-1.558c-0.964-0.41-2.048-0.61-3.052-0.603c-1.252,0-2.345,0.281-3.26,0.643 c-0.916,0.378-1.67,0.826-2.353,1.316c-1.422,1.012-2.9,2.209-4.553,3.558c-2.883,2.377-6.272,5.236-10.174,8.215 c-5.846,4.465-12.833,9.179-20.662,12.72c-7.838,3.55-16.462,5.935-25.761,5.935c-1.494,0-3.012-0.056-4.554-0.192 c-3.95-0.33-7.348-0.964-10.126-1.808c-4.2-1.26-6.93-2.979-8.448-4.609c-0.763-0.827-1.262-1.614-1.598-2.45 c-0.329-0.826-0.498-1.71-0.506-2.786c0-1.502,0.378-3.405,1.397-5.686c1.502-3.437,4.449-7.653,9.067-12.223 c4.594-4.594,10.817-9.548,18.735-14.615c10.584-6.834,19.016-17.113,26.138-28.661c7.115-11.563,12.929-24.484,17.892-36.907 c2.738-6.842,6.938-12.166,11.708-15.748c4.786-3.581,10.102-5.404,15.274-5.404c3.806,0.008,7.572,0.964,11.227,3.108 c3.654,2.152,7.219,5.517,10.384,10.52c6.375,10.102,12.631,20.47,19.746,29.954c7.107,9.476,15.105,18.101,25.095,24.42 c5.886,3.727,11.997,5.357,17.626,5.34c3.742,0,7.244-0.682,10.392-1.774c4.746-1.646,8.697-4.159,11.829-6.818 c1.558-1.333,2.915-2.706,4.063-4.071c1.14-1.382,2.08-2.73,2.827-4.184c3.662-7.211,8.857-14.88,15.306-22.26 c9.677-11.082,22.187-21.513,36.538-29.094c14.342-7.589,30.498-12.351,47.668-12.351c28.596-0.007,56.164,11.645,76.498,30.781 c10.158,9.572,18.494,20.984,24.292,33.688c5.797,12.712,9.05,26.708,9.05,41.573c0,3.509-0.177,7.066-0.554,10.672 c-1.518,14.543-4.112,23.072-6.216,28.283c-1.052,2.618-1.975,4.392-2.762,5.902c-0.402,0.772-0.771,1.47-1.149,2.346 c-0.184,0.433-0.369,0.923-0.538,1.542c-0.16,0.602-0.305,1.357-0.305,2.256c0,0.731,0.104,1.574,0.369,2.393 c0.386,1.237,1.14,2.329,1.863,3.052c0.73,0.747,1.429,1.205,2.04,1.558l0.626,0.361l0.682,0.209 c1.181,0.361,2.096,0.41,2.931,0.418c1.012,0,1.912-0.113,2.843-0.265c1.727-0.281,3.534-0.748,5.501-1.334 c3.437-1.02,7.364-2.433,11.371-3.982c11.018-4.248,22.734-9.573,24.669-10.456c1.534-0.554,3.116-0.827,4.698-0.827 c3.461,0,6.858,1.301,9.46,3.751c2.016,1.886,3.999,4.055,5.476,6.753c1.47,2.706,2.521,5.919,2.53,10.415 C499.143,244.906,498.886,247.404,498.236,250.255z"/><path d="M294,181.491c-21.875,15.908-7.95,51.691,25.834,31.808C305.924,205.349,294,181.491,294,181.491z"/><path d="M346.334,223.674c20.244,34.964,47.861,9.202,40.489-11.042C386.822,212.632,362.033,220.454,346.334,223.674z"/></svg>'],
            ['name' => 'Indie', 'icon' => '<svg class="w-10 h-10 fill-current text-cyan-400" viewBox="0 0 32 32"><path d="M26.709 5.915h-21.485l-4.543 6.12 15.344 16.815 15.344-16.815-4.659-6.12zM16.024 28.671l-6.021-16.523 6.021-6.146 6.021 6.146-6.021 16.523zM9.541 11.822l-4.633-6.114zM22.4 11.822l4.56-6.114zM9.541 12.137h12.86v0.574h-12.86v-0.574z"/></svg>'],
            ['name' => 'Open World', 'icon' => '<svg class="w-10 h-10 fill-current text-cyan-400" viewBox="0 0 100 100"><path d="M1.965 12.5C.88 12.5 0 13.443 0 14.605v70.79c0 1.162.88 2.104 1.965 2.105h96.07c1.085 0 1.965-.943 1.965-2.105v-70.79c0-1.162-.88-2.104-1.965-2.105zm22.408 4.213h12.385c3.19 1.117 6.48 2.17 9.773 3.135c7.26 2.126 14.43 3.856 20.004 5.056c1.774.382 3.307.687 4.711.957c-.961 1.034-1.904 2.065-2.867 3.094l-1.057 1.129c-6.493-2.033-13.921-4.056-23.474-6.059c-6.74-1.412-13.237-3.964-19.475-7.312zm17.08 0h14.453a295.548 295.548 0 0 0 11.785 2.814c2.73.588 5.06 1.043 6.784 1.346a59.4 59.4 0 0 0 2.091.336c.52.071.951.085.795.09l.006.158a419.433 419.433 0 0 0 4.26-4.744h5.332c-4.469 5.16-9.513 10.604-14.564 16.002C62.597 43.184 52.989 53.4 48.97 59.592a10.385 10.385 0 0 1 1.79 5.836c0 1.793-.457 3.484-1.261 4.963c4.097 3.203 7.157 7.856 9.928 12.896h-4.635c-2.384-4.05-4.909-7.543-7.832-9.808a10.41 10.41 0 0 1-9.732 1.949a89.094 89.094 0 0 1-3.231 7.86h-4.43c1.527-3.143 2.92-6.369 4.096-9.782c-2.336-1.923-3.83-4.834-3.83-8.078c0-2.752 1.083-5.258 2.836-7.131c-5.91-13.21-16.585-20-28.738-27.215v-4.674c12.892 7.605 25.195 14.839 32.052 29.502a10.347 10.347 0 0 1 10.092.809c4.67-6.914 13.935-16.629 23.397-26.739c1.588-1.697 3.155-3.398 4.73-5.1c-.158-.026-.253-.038-.422-.068a191.94 191.94 0 0 1-6.931-1.375c-5.543-1.193-12.683-2.916-19.897-5.029a212.348 212.348 0 0 1-5.5-1.695zm20.65 0H79.6c-.895 1.004-1.77 1.997-2.702 3.021c-.05-.005-.077-.004-.128-.011a58.622 58.622 0 0 1-2.036-.328c-1.697-.299-4.013-.75-6.726-1.334a290.859 290.859 0 0 1-5.904-1.348zm26.846 0h7.12v4.385c-.042-.003-.08-.012-.124-.012h-.013a1.75 1.75 0 0 0 .013 3.5c.042 0 .082-.009.123-.012v3.524c-.04-.003-.08-.012-.123-.012h-.013a1.75 1.75 0 0 0 .013 3.5c.042 0 .082-.009.123-.012v3.524c-.04-.003-.08-.012-.123-.012a1.75 1.75 0 1 0 0 3.5c.042 0 .082-.009.123-.012v1.295c-.791-.251-1.598-.502-2.369-.754a1.75 1.75 0 0 0-1.54-.504c-.641-.213-1.257-.425-1.884-.638a1.75 1.75 0 1 0-3.078-1.055c-1.426-.499-2.822-.998-4.222-1.496a1.75 1.75 0 0 0-.942-.334c-.71-.253-1.433-.507-2.146-.76a1.75 1.75 0 0 0-.43-2.422l-1.596 1.71c-1.047-.37-2.159-.738-3.23-1.106c5-5.346 9.953-10.706 14.318-15.797zm5.06 1.84c-1.156.286-2.137.788-3.138 1.545a1.75 1.75 0 1 0 3.139-1.545zm-4.421 2.656a45.985 45.985 0 0 0-2.268 2.277a1.75 1.75 0 1 0 2.268-2.277zm-85.656 1.99c18.115 12.035 33.234 17 51.261 19.965c-3.805 4.23-7.139 8.16-9.572 11.578a11.803 11.803 0 0 0-5.324-1.28c-1.241 0-2.441.194-3.57.552c-7.314-14.554-19.964-21.79-32.795-29.356zm88.5 1.387a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm-6.28.148l-2.256 2.416a1.75 1.75 0 1 0 2.256-2.416zm2.78 3.352a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm-6.121.228l-2.323 2.489a1.75 1.75 0 1 0 2.323-2.489zm2.62 3.272a1.75 1.75 0 1 0 .028 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .028 3.5a1.75 1.75 0 0 0-.027-3.5zm-88.5 1.248c11.478 6.84 21.321 13.273 26.981 25.223c-1.607 2.038-2.578 4.597-2.578 7.37c0 3.318 1.382 6.333 3.592 8.509c-1.164 3.244-2.53 6.33-4.024 9.351h-4.328l2.75-4.762l-2.922-1.687l1.041-1.805l-4.047-2.336l-6.115 10.59H3.931V48.32l7.684 4.582l4.778-8.012l-12.461-7.432zm59.645 11.603c2.32.385 4.57.922 6.73 1.57a1.75 1.75 0 1 0 2.84.927c1.369.483 2.69 1 3.97 1.54a1.75 1.75 0 0 0 1.415.61c.649.29 1.276.584 1.897.88a1.75 1.75 0 1 0 3.127 1.56c.693.363 1.29.708 1.935 1.064a1.875 1.875 0 0 0-.058-.002a1.75 1.75 0 1 0 1.464.773c3.197 1.81 5.717 3.38 7.327 4.172a1.75 1.75 0 0 0 1.723 2.055c.042 0 .08-.015.122-.018v3.53c-.04-.003-.08-.012-.123-.012h-.013a1.75 1.75 0 0 0 .013 3.5c.042 0 .082-.009.123-.012v3.524c-.04-.003-.08-.012-.123-.012a1.75 1.75 0 1 0 0 3.5c.042 0 .082-.009.123-.012v9.713H61.15c-2.7-5.02-5.69-9.798-9.795-13.324a11.804 11.804 0 0 0-.613-10.328c2.623-3.807 7.38-9.234 12.834-15.198zm4.37 4.649c-.218 0-.434.04-.637.12a11.436 11.436 0 0 0-.092 3.22a1.75 1.75 0 1 0 .729-3.34zm7 0a1.75 1.75 0 1 0 0 3.5a1.75 1.75 0 0 0 0-3.5zm-56.749 3.307l-3.623 6.275l-2.431 4.21l6.275 3.624l2.432-4.211l2.492 1.44l3.623-6.276l-2.492-1.44zm53.235.193a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm-3.5 3.5a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm-20.713.021c.013.031.021.061.035.092a70.204 70.204 0 0 1-.139 3.377a1.75 1.75 0 0 0 .104-3.469zm-27.922 2.856a6.434 6.434 0 0 0-6.463 6.465a6.432 6.432 0 0 0 6.463 6.463a6.434 6.434 0 0 0 6.465-6.463a6.435 6.435 0 0 0-6.465-6.465zm31.135.623a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm-52.135.877c2.783 0 4.965 2.182 4.965 4.965s-2.182 4.963-4.965 4.963s-4.963-2.18-4.963-4.963s2.18-4.965 4.963-4.965zm34.635 2.623a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm-20.694.023c.224.556.658.995 1.444 1.514a1.75 1.75 0 0 0-1.444-1.514zm10.194 3.477a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zm7 0a1.75 1.75 0 1 0 .027 3.5a1.75 1.75 0 0 0-.027-3.5zM65.81 68.359c-3.228 0-5.877 2.65-5.877 5.877c0 3.228 2.65 5.875 5.877 5.875s5.875-2.647 5.875-5.875c0-3.227-2.648-5.877-5.875-5.877zm16.134 1.727a1.75 1.75 0 0 0-1.271 2.953l2.562-.021a1.75 1.75 0 0 0-1.29-2.932zm7 0a1.75 1.75 0 0 0-1.324 2.893l2.668-.024a1.75 1.75 0 0 0-1.344-2.87zM65.81 71.359a2.854 2.854 0 0 1 2.875 2.877a2.852 2.852 0 0 1-2.875 2.875a2.854 2.854 0 0 1-2.877-2.875a2.855 2.855 0 0 1 2.877-2.877zm-18.913 4.02c2.168 1.932 4.174 4.707 6.118 7.908H35.65c.899-1.979 1.762-3.992 2.541-6.096c.686.124 1.387.2 2.106.2c2.433 0 4.704-.745 6.601-2.012z"/></svg>'],
            ['name' => 'FPS', 'icon' => '<svg class="w-10 h-10 fill-current text-green-400" viewBox="0 0 32 32"><path d="M30 15.25h-3.326c-0.385-5.319-4.605-9.539-9.889-9.922l-0.035-0.002v-3.326c0-0.414-0.336-0.75-0.75-0.75s-0.75 0.336-0.75 0.75v0 3.326c-5.319 0.385-9.539 4.605-9.922 9.889l-0.002 0.035h-3.326c-0.414 0-0.75 0.336-0.75 0.75s0.336 0.75 0.75 0.75v0h3.326c0.385 5.319 4.605 9.539 9.889 9.922l0.035 0.002v3.326c0 0.414 0.336 0.75 0.75 0.75s0.75-0.336 0.75-0.75v0-3.326c5.319-0.385 9.539-4.605 9.922-9.889l0.002-0.035h3.326c0.414 0 0.75-0.336 0.75-0.75s-0.336-0.75-0.75-0.75v0zM16.75 25.174v-3.174c0-0.414-0.336-0.75-0.75-0.75s-0.75 0.336-0.75 0.75v0 3.174c-4.492-0.378-8.046-3.932-8.422-8.39l-0.002-0.034h3.174c0.414 0 0.75-0.336 0.75-0.75s-0.336-0.75-0.75-0.75v0h-3.174c0.378-4.492 3.932-8.046 8.39-8.422l0.034-0.002v3.174c0 0.414 0.336 0.75 0.75 0.75s0.75-0.336 0.75-0.75v0-3.174c4.492 0.378 8.046 3.932 8.422 8.39l0.002 0.034h-3.174c-0.414 0-0.75 0.336-0.75 0.75s0.336 0.75 0.75 0.75v0h3.174c-0.379 4.492-3.932 8.045-8.39 8.422l-0.034 0.002z"></path></svg>']
            ];
            @endphp

            <div class="mb-12 relative">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-4" id="genreBoxContainer">
                    @foreach($genreBoxes as $index => $gb)
                    <div onclick="toggleGenreBox('{{ $gb['name'] }}')"
                        class="genre-box cursor-pointer rounded-xl p-4 flex flex-col items-center justify-center gap-3 h-28 transition-all duration-500 ease-out {{ in_array($gb['name'], $genreDipilih) ? 'active' : '' }} {{ $index >= 6 ? 'hidden extra-genre opacity-0 scale-95 translate-y-[-10px]' : '' }}">
                        <span class="flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity">{!! $gb['icon'] !!}</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider text-center">{{ $gb['name'] }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Tombol Lihat Semua --}}
                @if(count($genreBoxes) > 6)
                <div class="mt-8 flex justify-center">
                    <button onclick="toggleGenreBoxes(this)" class="text-[11px] font-bold text-[#a78bfa] hover:text-white transition-colors uppercase tracking-widest bg-purple-500/10 px-6 py-3 rounded-lg border border-purple-500/20 hover:bg-purple-500/20">
                        Lihat Semua Kategori
                    </button>
                </div>
                @endif
            </div>

            {{-- Judul Hasil --}}
            <div class="mb-6" id="kategoriTitleContainer">
                <h2 class="text-xl font-bold text-white mb-1">Eksplorasi Katalog</h2>
                <p class="text-xs text-gray-500"><span id="gameCountText">{{ $games->total() }}</span> Game ditemukan</p>
            </div>

            {{-- Grid Game (Poster Saja ala Netflix/Screenshot) --}}
            <div id="mainGameArea" class="transition-opacity duration-300">
                @if(count($games) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 lg:gap-5" id="gameGridContainer">
                @foreach($games as $game)
                @php
                    $isOwned = in_array($game->id, $ownedGameIds);
                    $isInCart = in_array($game->id, $cartGameIds);
                    $isInWishlist = in_array($game->id, $wishlistGameIds);
                @endphp
                                            <?php
                            $avg_rating = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0;
                            $total_reviews = $game->reviews->count();
                            ?>
                            <div class="card-bg rounded-xl overflow-hidden hover-card cursor-pointer flex flex-col relative group" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}" data-title="{{ strtolower($game->name) }}">

                                @if($avg_rating >= 4.5)
                                <div class="absolute top-2 right-2 z-10 bg-yellow-500 text-black text-[9px] font-black px-1.5 py-0.5 rounded">TOP</div>
                                @elseif($game->price == 0)
                                <div class="absolute top-2 right-2 z-10 bg-green-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded">GRATIS</div>
                                @elseif($game->created_at && $game->created_at->diffInDays(now()) < 30)
                                    <div class="absolute top-2 right-2 z-10 bg-blue-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded">Baru</div>
                                @endif
                                
                            <div class="relative aspect-[3/4] overflow-hidden bg-black">
                                <?php if ($isOwned): ?>
                                    <div class="absolute top-2 right-2 bg-gray-800 text-white text-[9px] font-bold px-2 py-1 rounded shadow-lg border border-gray-600/50 backdrop-blur-sm z-20">SUDAH DIMILIKI</div>
                                <?php elseif ($isInCart): ?>
                                    <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-20">DI KERANJANG</div>
                                <?php elseif ($isInWishlist): ?>
                                    <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-20">WISHLIST</div>
                                <?php endif; ?>
                                <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="{{ $isOwned ? 'opacity-40 grayscale-[60%]' : '' }} w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">

                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                    <button onclick="event.stopPropagation(); window.tambahKeranjangCerdas('{{ $game->id }}', false, this)" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-colors">
                                        + Keranjang
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-bold text-white text-sm leading-tight mb-1 line-clamp-2 group-hover:text-purple-400 transition-colors">{{ $game->name }}</h3>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded">{{ explode(',', $game->genre)[0] }}</span>
                                    <span class="text-[10px] text-gray-500 bg-white/5 px-1.5 py-0.5 rounded">{{ explode(',', $game->platform)[0] }}</span>
                                </div>
                                @if($avg_rating > 0)
                                <div class="flex items-center gap-1 mb-2">
                                    <span class="text-yellow-500 text-xs">★</span>
                                    <span class="text-xs font-bold text-white">{{ $avg_rating }}</span>
                                    <span class="text-[10px] text-gray-500">({{ $total_reviews }})</span>
                                </div>
                                @endif
                                <p class="mt-auto font-bold text-sm {{ $game->price == 0 ? 'text-green-400' : 'text-white' }}">{{ $game->price == 0 ? "Gratis" : "Rp " . number_format($game->price, 0, ',', '.') }}</p>
                            </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginasi --}}
            @if($games->hasPages())
            <div class="mt-12 flex justify-center pb-10">
                <div class="bg-[#151821] p-2 rounded-xl border border-white/5 shadow-lg w-full max-w-2xl overflow-x-auto hide-scrollbar">
                    {{ $games->links() }}
                </div>
            </div>
            @endif

            @else
            <div class="flex flex-col items-center justify-center py-20 text-center border border-dashed border-white/10 rounded-2xl bg-[#151821]">
                <span class="text-5xl mb-4 opacity-30">🎮</span>
                <h3 class="text-lg font-bold text-gray-300 mb-1">Game Tidak Ditemukan</h3>
                <p class="text-gray-500 text-sm max-w-sm">Coba ubah kombinasi filter atau gunakan kata kunci lain.</p>
            </div>
            @endif
            </div>

                <div class="px-2 lg:px-6">
            @include('footer')
        </div>
</main>


    </div>

    </div><!-- end berandaContent -->

    {{-- ===== PANEL PUSAT BANTUAN ===== --}}
    <div id="bantuanPanel" class="fixed top-20 bottom-0 left-0 right-0 z-40 bg-[#0A0C10] overflow-y-auto hide-scrollbar transition-all duration-500 ease-in-out" style="transform: translateX(100%); opacity: 0; pointer-events: none;">
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
                <div class="card-bg p-6 rounded-2xl hover-card cursor-pointer transition-all">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center mb-5 text-xl"><svg class="w-6 h-6 fill-current" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <g>
                                    <path d="M80,71.2V74c0,3.3-2.7,6-6,6H26c-3.3,0-6-2.7-6-6v-2.8c0-7.3,8.5-11.7,16.5-15.2c0.3-0.1,0.5-0.2,0.8-0.4 c0.6-0.3,1.3-0.3,1.9,0.1C42.4,57.8,46.1,59,50,59c3.9,0,7.6-1.2,10.8-3.2c0.6-0.4,1.3-0.4,1.9-0.1c0.3,0.1,0.5,0.2,0.8,0.4 C71.5,59.5,80,63.9,80,71.2z" />
                                </g>
                                <g>
                                    <ellipse cx="50" cy="36.5" rx="14.9" ry="16.5" />
                                </g>
                            </g>
                        </svg></div>
                    <h3 class="font-bold mb-2">Akun &amp; Profil</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">Masalah terkait login, verifikasi email, ganti password, dan keamanan akun.</p>
                </div>
                <div class="card-bg p-6 rounded-2xl hover-card cursor-pointer transition-all">
                    <div class="w-12 h-12 rounded-xl bg-green-500/10 text-green-400 flex items-center justify-center mb-5 text-xl"><svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M2 10H22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6 15H8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></div>
                    <h3 class="font-bold mb-2">Pembayaran</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">Bantuan untuk transaksi Midtrans, metode pembayaran gagal, dan invoice.</p>
                </div>
                <div class="card-bg p-6 rounded-2xl hover-card cursor-pointer transition-all">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-400 flex items-center justify-center mb-5"><svg class="w-6 h-6 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg">
                            <g transform="translate(-1)">
                                <g>
                                    <g>
                                        <path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333 h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333 c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333 C235.943,223.156,226.391,213.605,214.609,213.605z" />
                                        <path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96 c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598 c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168 c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056 c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411 c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831 c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43 l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228 c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z" />
                                        <path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333 c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z" />
                                        <path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333 S354.385,234.938,342.609,234.938z" />
                                        <path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S311.719,192.271,299.943,192.271z" />
                                        <path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S397.052,192.271,385.276,192.271z" />
                                    </g>
                                </g>
                            </g>
                        </svg></div>
                    <h3 class="font-bold mb-2">Game &amp; Instalasi</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">Masalah cara unduh, aktivasi game, performa, atau minimum spesifikasi PC.</p>
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
    </div>{{-- end #bantuanPanel --}}

    {{-- Script Filter --}}
    <script>
        
        let bantuanActive = false;

        function toggleBantuan(e) {
            e.preventDefault();
            bantuanActive ? closeBantuan() : openBantuan();
        }

        function openBantuan() {
            bantuanActive = true;
            const panel = document.getElementById('bantuanPanel');
            const beranda = document.getElementById('berandaContent');
            const navKategori = document.getElementById('nav-kategori');
            const navBantuan = document.getElementById('nav-bantuan');

            if (beranda) {
                beranda.style.transform = 'translateX(-60px)';
                beranda.style.opacity = '0';
                beranda.style.pointerEvents = 'none';
                setTimeout(() => {
                    if (bantuanActive) beranda.style.display = 'none';
                }, 500);
            }

            if (panel) {
                panel.style.visibility = 'visible';
                panel.style.pointerEvents = 'auto';
                panel.style.transform = 'translateX(0)';
                panel.style.opacity = '1';
            }

            if(navKategori) {
                navKategori.classList.remove('text-purple-400', 'border-purple-500');
                navKategori.classList.add('text-gray-400', 'border-transparent');
            }
            if(navBantuan) {
                navBantuan.classList.remove('text-gray-400', 'border-transparent');
                navBantuan.classList.add('text-purple-400', 'border-purple-500');
            }
        }

        function closeBantuan() {
            bantuanActive = false;
            const panel = document.getElementById('bantuanPanel');
            const beranda = document.getElementById('berandaContent');
            const navKategori = document.getElementById('nav-kategori');
            const navBantuan = document.getElementById('nav-bantuan');

            if (panel) {
                panel.style.transform = 'translateX(100%)';
                panel.style.opacity = '0';
                panel.style.pointerEvents = 'none';
                setTimeout(() => {
                    if (!bantuanActive) panel.style.visibility = 'hidden';
                }, 500);
            }

            if (beranda) {
                beranda.style.display = 'flex';
                // Trigger reflow
                void beranda.offsetWidth;
                beranda.style.transform = 'translateX(0)';
                beranda.style.opacity = '1';
                beranda.style.pointerEvents = 'auto';
            }

            if(navBantuan) {
                navBantuan.classList.remove('text-purple-400', 'border-purple-500');
                navBantuan.classList.add('text-gray-400', 'border-transparent');
            }
            if(navKategori) {
                navKategori.classList.remove('text-gray-400', 'border-transparent');
                navKategori.classList.add('text-purple-400', 'border-purple-500');
            }
        }

        if(document.getElementById('nav-kategori')) {
            document.getElementById('nav-kategori').addEventListener('click', function(e) {
                if (bantuanActive) {
                    e.preventDefault();
                    closeBantuan();
                }
            });
        }

        function toggleFaqPanel(btn) {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('svg');
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function toggleGenreBoxes(btn) {
            const container = document.getElementById('genreBoxContainer');
            const extraGenres = document.querySelectorAll('.extra-genre');
            let isHidden = extraGenres[0].classList.contains('hidden');

            if (isHidden) {
                const startHeight = container.offsetHeight;
                
                extraGenres.forEach(el => {
                    el.classList.remove('hidden');
                });
                
                const targetHeight = container.offsetHeight;
                
                container.style.height = startHeight + 'px';
                container.style.overflow = 'hidden';
                container.style.transition = 'height 0.4s ease-out';
                
                container.offsetHeight; // Force reflow
                
                container.style.height = targetHeight + 'px';
                
                setTimeout(() => {
                    extraGenres.forEach(el => {
                        el.classList.remove('opacity-0', 'scale-95', 'translate-y-[-10px]');
                        el.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                    });
                }, 10);

                btn.innerText = 'Sembunyikan Sebagian';

                setTimeout(() => {
                    container.style.height = '';
                    container.style.overflow = '';
                    container.style.transition = '';
                }, 400);

            } else {
                const startHeight = container.offsetHeight;
                
                extraGenres.forEach(el => {
                    el.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                    el.classList.add('opacity-0', 'scale-95', 'translate-y-[-10px]');
                });

                extraGenres.forEach(el => el.classList.add('hidden'));
                const targetHeight = container.offsetHeight;
                extraGenres.forEach(el => el.classList.remove('hidden'));

                container.style.height = startHeight + 'px';
                container.style.overflow = 'hidden';
                container.style.transition = 'height 0.4s ease-out';

                container.offsetHeight; // Force reflow

                container.style.height = targetHeight + 'px';
                
                btn.innerText = 'Lihat Semua Kategori';

                setTimeout(() => {
                    extraGenres.forEach(el => el.classList.add('hidden'));
                    container.style.height = '';
                    container.style.overflow = '';
                    container.style.transition = '';
                }, 400);
            }
        }

        function toggleGenreBox(genreName) {
            const cb = document.querySelector(`input[name="genre"][value="${genreName}"]`);
            if (cb) {
                cb.checked = !cb.checked;
                if (cb.checked) {
                    const semuaGenre = document.getElementById('semuaGenre');
                    if (semuaGenre) semuaGenre.checked = false;
                }
                applyFilters();
            } else {
                window.location.href = `/kategori?genre=${genreName}`;
            }
        }

        function syncGenreBoxActiveStates() {
            const checkedGenres = Array.from(document.querySelectorAll('input[name="genre"]:checked')).map(cb => cb.value);
            document.querySelectorAll('.genre-box').forEach(box => {
                const onclickStr = box.getAttribute('onclick');
                if (onclickStr) {
                    const match = onclickStr.match(/toggleGenreBox\('([^']+)'\)/);
                    if (match && match[1]) {
                        if (checkedGenres.includes(match[1])) {
                            box.classList.add('active');
                        } else {
                            box.classList.remove('active');
                        }
                    }
                }
            });
        }

        function toggleSemuaGenre(checkbox) {
            if (checkbox.checked) {
                document.querySelectorAll('input[name="genre"]').forEach(cb => cb.checked = false);
                applyFilters();
            }
        }

        function toggleSemuaHarga(checkbox) {
            if (checkbox.checked) {
                document.querySelectorAll('input[name="harga"]').forEach(cb => cb.checked = false);
                applyFilters();
            }
        }

        function toggleSemuaPlatform(checkbox) {
            if (checkbox.checked) {
                document.querySelectorAll('input[name="platform"]').forEach(cb => cb.checked = false);
                applyFilters();
            }
        }

        function toggleSemuaRating(checkbox) {
            if (checkbox.checked) {
                document.querySelectorAll('input[name="rating"]').forEach(cb => cb.checked = false);
                applyFilters();
            }
        }

        // Jika salah satu checkbox spesifik diklik, uncheck "Semua"
        document.querySelectorAll('input[name="genre"]').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) document.getElementById('semuaGenre').checked = false;
            });
        });

        document.querySelectorAll('input[name="platform"]').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) document.getElementById('semuaPlatform').checked = false;
            });
        });

        document.querySelectorAll('input[name="harga"]').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) document.getElementById('semuaHarga').checked = false;
            });
        });

        document.querySelectorAll('input[name="rating"]').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) document.getElementById('semuaRating').checked = false;
            });
        });

        function applyFilters() {
            const getCheckedValues = (name) => {
                return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`)).map(cb => cb.value);
            };

            const genres = getCheckedValues('genre');
            const hargas = getCheckedValues('harga');
            const ratings = getCheckedValues('rating');
            const platforms = getCheckedValues('platform');

            const params = new URLSearchParams();

            if (genres.length > 0) params.set('genre', genres.join(','));
            if (hargas.length > 0) params.set('harga', hargas.join(','));
            if (ratings.length > 0) params.set('rating', ratings.join(','));
            if (platforms.length > 0) params.set('platform', platforms.join(','));
            params.set('filter', '1');

            syncGenreBoxActiveStates();
            fetchAndUpdate('/kategori?' + params.toString());
        }

        // Intercept pagination clicks for instant smooth transitions
        document.addEventListener('click', function(e) {
            const paginationContainer = document.getElementById('mainGameArea');
            if (paginationContainer && paginationContainer.contains(e.target)) {
                const link = e.target.closest('a');
                if (link && link.href) {
                    e.preventDefault();
                    fetchAndUpdate(link.href);
                }
            }
        });

        async function fetchAndUpdate(url) {
            const gameArea = document.getElementById('mainGameArea');
            if (gameArea) gameArea.style.opacity = '0.5';

            try {
                const response = await fetch(url);
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newGameArea = doc.getElementById('mainGameArea');
                if (newGameArea) {
                    gameArea.innerHTML = newGameArea.innerHTML;
                    gameArea.style.opacity = '1';
                }

                const newTitle = doc.getElementById('kategoriTitleContainer');
                if (newTitle) {
                    document.getElementById('kategoriTitleContainer').innerHTML = newTitle.innerHTML;
                }

                // Update URL in browser silently
                window.history.pushState({path: url}, '', url);
            } catch (err) {
                // Fallback
                window.location.href = url;
            }
        }

        function resetFilters() {
            window.location.href = '/kategori?reset=1';
        }

        // Live Search di Sidebar untuk filter daftar game yang tampil
        function filterByTitle() {
            const query = document.getElementById('kategoriSearchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.game-poster-card');
            let count = 0;

            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                if (title.includes(query)) {
                    card.style.display = 'block';
                    count++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('gameCountText').innerText = count;
        }
    </script>

    








<script>
    // Initialize server data if available
    window.SERVER_CART = window.SERVER_CART || {!! isset($cartGameIds) ? json_encode($cartGameIds) : '[]' !!};
    window.SERVER_WISHLIST = window.SERVER_WISHLIST || {!! isset($wishlistGameIds) ? json_encode($wishlistGameIds) : '[]' !!};

    window.isSyncingLabels = false;

    // FUNGSI SUPER ROBUST UNTUK SINKRONISASI LABEL
    window.syncGameCardLabels = function() {
        if (window.isSyncingLabels) return;
        window.isSyncingLabels = true;
        try {
            let localWishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            let localCart = JSON.parse(localStorage.getItem('cart_cache')) || [];
            
            // Gabungkan data server dan local storage (optimistic UI)
            let wishlist = [...new Set([...localWishlist, ...window.SERVER_WISHLIST])];
            let cart = [...new Set([...localCart, ...window.SERVER_CART])];
            
            // Konversi semua ke string agar komparasi aman
            wishlist = wishlist.map(id => String(id));
            cart = cart.map(id => String(id));
            
            document.querySelectorAll('[data-game-id]').forEach(card => {
                let gameId = String(card.getAttribute('data-game-id'));
                if (!gameId) return;
                
                                                        // Kumpulkan label bawaan apapun yang ada di pojok kanan atas
                    let oldLabels = [];
                    card.querySelectorAll('.absolute').forEach(el => {
                        let cls = el.className || '';
                        if (cls.includes('top-') && cls.includes('right-')) {
                            // Abaikan label cart/wishlist/dimiliki
                            if (!el.classList.contains('label-cart') && 
                                !el.classList.contains('label-wishlist') && 
                                !el.innerText.includes('DIMILIKI') &&
                                !el.innerText.includes('KERANJANG') &&
                                !el.innerText.includes('WISHLIST')) {
                                oldLabels.push(el);
                            }
                        }
                    });

                // Jika sudah ada label SUDAH DIMILIKI, jangan ditimpa, dan sembunyikan label Baru
                let ownedLabel = card.querySelector('.bg-gray-800');
                if (ownedLabel && ownedLabel.innerText.includes('DIMILIKI')) {
                    oldLabels.forEach(el => el.classList.add('hidden'));
                    return;
                }
                
                // Cari apakah ada container spesifik untuk menempelkan label
                let appendTarget = card;
                let aspectDiv = card.querySelector('img') ? card.querySelector('img').parentElement : card;
                if (aspectDiv) appendTarget = aspectDiv;
                
                // Bersihkan label lama jika ada
                card.querySelectorAll('.label-cart, .label-wishlist').forEach(el => el.remove());
                card.querySelectorAll('.bg-green-500, .bg-pink-500').forEach(el => {
                    if (el.innerText.includes('KERANJANG') || el.innerText.includes('WISHLIST')) {
                        el.remove();
                    }
                });
                
                let shouldHideOld = false;

                if (cart.includes(gameId)) {
                    let label = document.createElement('div');
                    label.className = "absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-30 label-cart pointer-events-none";
                    label.innerText = "DI KERANJANG";
                    appendTarget.appendChild(label);
                    shouldHideOld = true;
                } else if (wishlist.includes(gameId)) {
                    let label = document.createElement('div');
                    label.className = "absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-30 label-wishlist pointer-events-none";
                    label.innerText = "WISHLIST";
                    appendTarget.appendChild(label);
                    shouldHideOld = true;
                }

                // Sembunyikan label 'Baru'/'Free' jika ada label Wishlist/Cart
                oldLabels.forEach(el => {
                    if (shouldHideOld) {
                        el.classList.add('hidden');
                    } else {
                        el.classList.remove('hidden');
                    }
                });
            });
        } catch (e) {
            console.error('Error syncing labels:', e);
        } finally {
            window.isSyncingLabels = false;
        }
    };

    // Jalankan saat dokumen siap, halaman terbuka, atau setelah fetch
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.syncGameCardLabels);
    } else {
        window.syncGameCardLabels();
    }
    window.addEventListener('pageshow', window.syncGameCardLabels);
    
    // Observer untuk mendeteksi perubahan DOM (seperti fetch AJAX kategori)
    const observer = new MutationObserver((mutations) => {
        let shouldSync = false;
        for (let m of mutations) {
            if (m.addedNodes && m.addedNodes.length > 0) {
                for (let i = 0; i < m.addedNodes.length; i++) {
                    let node = m.addedNodes[i];
                    if (node.nodeType === 1) { // Element
                        if (!node.classList.contains('label-cart') && !node.classList.contains('label-wishlist')) {
                            shouldSync = true;
                            break;
                        }
                    } else if (node.nodeType === 3) { // Text node
                        // ignore text nodes to be safe
                    } else {
                        shouldSync = true;
                        break;
                    }
                }
            }
            if (shouldSync) break;
        }
        if (shouldSync) window.syncGameCardLabels();
    });
    
    let mainGameArea = document.getElementById('mainGameArea') || document.body;
    if (mainGameArea) {
        observer.observe(mainGameArea, { childList: true, subtree: true });
    }

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