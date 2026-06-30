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
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="asset-url" content="{{ asset('assets') }}">

    <title>GameVault - Premium Storefront</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-VaITMgxuHPGC2OLy" defer></script>
    <style>
        /* KUNCI ANTI-KEDIP PUTIH: Set background hitam sejak awal tanpa menunggu Tailwind CDN */
        html,
        body {
            background-color: #0A0C10 !important;
            color: #FFFFFF !important;
        }

        body,
        html,
        main,
        section,
        div {
            background-image: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0A0C10 !important;
            color: #FFFFFF;
            position: relative;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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

        .text-purple {
            color: #8B5CF6;
        }

        .bg-purple-primary {
            background-color: #7C3AED;
        }

        .bg-purple-primary:hover {
            background-color: #6D28D9;
        }

        /* Gaming Particle Background */
        #particles-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.4;
        }

        /* Glow Animation */
        @keyframes glow-pulse {

            0%,
            100% {
                box-shadow: 0 0 15px rgba(124, 58, 237, 0.3);
            }

            50% {
                box-shadow: 0 0 25px rgba(124, 58, 237, 0.5);
            }
        }

        .glow-effect {
            animation: glow-pulse 3s ease-in-out infinite;
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

        /* Shimmer Effect untuk badge */
        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        .shimmer {
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.1) 50%,
                    transparent 100%);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }

        /* Floating animation untuk icon */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .float-anim {
            animation: float 3s ease-in-out infinite;
        }

        /* Smooth fade in untuk elemen */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Scale pada hover untuk card game */
        .game-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .game-card:hover {
            transform: translateY(-8px) scale(1.02);
        }

        /* Gradient text animation */
        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .gradient-text-animated {
            background: linear-gradient(90deg, #FDE047, #F59E0B, #FDE047);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 4s ease infinite;
        }
    </style>


</head>

<body class="flex h-screen overflow-hidden antialiased selection:bg-purple-500 selection:text-white">

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
    ->where('tb_detail_transaksi.is_refunded', false)
    ->pluck('tb_detail_transaksi.game_id')->toArray();

    $cartGameIds = \App\Models\Keranjang::where('user_id', $userId)->pluck('game_id')->toArray();
    $wishlistGameIds = \App\Models\Wishlist::where('user_id', $userId)->pluck('game_id')->toArray();
    }
    @endphp

    <canvas id="particles-canvas"></canvas>

    <div class="flex-1 flex flex-col h-full overflow-hidden relative" style="z-index: 1;">

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
                        <a href="#section-game-populer" onclick="document.getElementById('section-game-populer').scrollIntoView({ behavior: 'smooth' }); return false;" class="text-xs text-purple-400 hover:text-purple-300">Lihat semua</a>
                    </div>
                    <div class="space-y-4">
                        <?php $rank = 1; ?>
                        <?php $__currentLoopData = $populer_games->take(5);
                        $__env->addLoop($__currentLoopData);
                        foreach ($__currentLoopData as $rg): $__env->incrementLoopIndices();
                            $loop = $__env->getLastLoop(); ?>
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
                        <?php endforeach;
                        $__env->popLoop();
                        $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </aside>


            <main class="flex-1 relative overflow-hidden">

                {{-- ===== KONTEN BERANDA ===== --}}
                <div id="berandaContent" class="absolute inset-0 pt-6 pb-6 pl-6 pr-14 lg:pt-8 lg:pb-8 lg:pl-8 lg:pr-20 flex flex-col gap-10 overflow-y-auto hide-scrollbar">










                    @if($carousel_games && $carousel_games->count() > 0)
                    <section class="relative w-full h-[450px] md:h-[550px] rounded-2xl overflow-hidden shadow-2xl">

                        <div id="heroCarousel" class="relative w-full h-full">
                            <?php $__currentLoopData = $carousel_games;
                            $__env->addLoop($__currentLoopData);
                            foreach ($__currentLoopData as $index => $game): $__env->incrementLoopIndices();
                                $loop = $__env->getLastLoop(); ?>
                                <?php $rating = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0; ?>
                                <div class="carousel-slide absolute inset-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-slide="{{ $index }}">

                                    <div class="absolute inset-0 overflow-hidden">

                                        <?php
                                        $isOwned = in_array($game->id, $ownedGameIds ?? []);
                                        $isInCart = in_array($game->id, $cartGameIds ?? []);
                                        $isInWishlist = in_array($game->id, $wishlistGameIds ?? []);
                                        ?>
                                        <?php if ($isOwned): ?>
                                            <div class="absolute top-2 right-2 bg-gray-800 text-white text-[9px] font-bold px-2 py-1 rounded shadow-lg border border-gray-600/50 backdrop-blur-sm z-20">SUDAH DIMILIKI</div>
                                        <?php elseif ($isInCart): ?>
                                            <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-20">DI KERANJANG</div>
                                        <?php elseif ($isInWishlist): ?>
                                            <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-20">WISHLIST</div>
                                        <?php endif; ?>
                                        <img src="{{ asset('assets/' . $game->image) }}"
                                            onerror="this.src='/assets/no-image.jpg'"
                                            class="{{ $isOwned ? 'opacity-40 grayscale-[60%]' : '' }} w-full h-full object-cover object-center transform scale-110 transition-transform duration-[3000ms]"
                                            data-parallax>
                                    </div>


                                    <div class="absolute inset-0 bg-gradient-to-r from-[#0A0C10]/95 via-[#0A0C10]/70 to-transparent"></div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0A0C10] via-transparent to-transparent"></div>


                                    <div class="absolute inset-0 p-8 md:p-12 flex flex-col justify-center w-full md:w-2/3 z-20">

                                        <div class="flex gap-2 mb-4">
                                            @if($index === 0)
                                            <span class="bg-purple-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-widest shadow-lg shadow-purple-500/40 backdrop-blur-sm">
                                                Featured
                                            </span>
                                            @endif
                                            @if($rating >= 4.5)
                                            <span class="bg-yellow-500 text-black text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-widest shadow-lg">
                                                Highly Rated
                                            </span>
                                            @endif
                                            @if($game->price == 0)
                                            <span class="bg-green-500 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-widest shadow-lg">
                                                Gratis
                                            </span>
                                            @endif
                                        </div>


                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="text-xs text-gray-300 bg-black/50 px-3 py-1 rounded-full border border-white/10 backdrop-blur-sm">
                                                {{ $game->platform }}

                                            </span>
                                            <span class="text-xs text-purple-300 bg-purple-500/20 px-3 py-1 rounded-full border border-purple-500/30 backdrop-blur-sm">
                                                {{ $game->genre }}

                                            </span>
                                        </div>


                                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white uppercase leading-tight mb-4 drop-shadow-2xl animate-fade-in-up"
                                            style="text-shadow: 0 0 30px rgba(0,0,0,0.8);">
                                            {{ $game->name }}

                                        </h1>


                                        @if($rating > 0)
                                        <div class="flex items-center gap-2 mb-4">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="{{ $i <= $rating ? 'text-yellow-500' : 'text-gray-600' }} text-lg drop-shadow-lg">★</span>
                                            <?php endfor; ?>
                                            <span class="text-white font-bold text-sm ml-1">{{ $rating }}</span>
                                            <span class="text-gray-400 text-xs">({{ $game->reviews->count() }} reviews)</span>
                                        </div>
                                        @endif


                                        <p class="text-gray-200 mb-6 max-w-xl text-sm md:text-base leading-relaxed line-clamp-2 drop-shadow-lg">
                                            {{ $game->synopsis ?: 'Discover endless adventures and claim your victory in this epic gaming experience.' }}

                                        </p>


                                        <div class="mb-6">
                                            @if($game->price == 0)
                                            <span class="text-3xl font-black text-green-400 drop-shadow-lg">GRATIS</span>
                                            @else
                                            <span class="text-3xl font-black text-white drop-shadow-lg">Rp {{ number_format($game->price, 0, ',', '.') }}</span>
                                            @endif
                                        </div>


                                        <div class="flex items-center gap-3">
                                            <button onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}"
                                                class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg hover:shadow-purple-500/50 hover:scale-105 active:scale-95">
                                                View Details
                                            </button>
                                            <button onclick="event.stopPropagation(); window.tambahKeranjangCerdas('{{ $game->id }}', false, this)"
                                                class="w-12 h-12 flex items-center justify-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl hover:bg-white/20 transition-all hover:scale-110 active:scale-95">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="event.stopPropagation();"
                                                class="w-12 h-12 flex items-center justify-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl hover:bg-white/20 transition-all hover:scale-110 active:scale-95">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                            $__env->popLoop();
                            $loop = $__env->getLastLoop(); ?>
                        </div>


                        <button onclick="prevSlide()"
                            class="absolute left-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 bg-black/50 backdrop-blur-md border border-white/20 rounded-full flex items-center justify-center hover:bg-black/70 hover:scale-110 transition-all group">
                            <svg class="w-6 h-6 text-white group-hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button onclick="nextSlide()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 bg-black/50 backdrop-blur-md border border-white/20 rounded-full flex items-center justify-center hover:bg-black/70 hover:scale-110 transition-all group">
                            <svg class="w-6 h-6 text-white group-hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>


                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex gap-2">
                            <?php $__currentLoopData = $carousel_games;
                            $__env->addLoop($__currentLoopData);
                            foreach ($__currentLoopData as $index => $game): $__env->incrementLoopIndices();
                                $loop = $__env->getLastLoop(); ?>
                                <button onclick="goToSlide({{ $index }})"
                                    class="carousel-dot relative w-16 h-1 rounded-full bg-white/30 overflow-hidden transition-all {{ $index === 0 ? 'active' : '' }}">
                                    <div class="absolute inset-0 bg-white rounded-full transition-transform origin-left {{ $index === 0 ? 'progress-bar' : '' }}"></div>
                                </button>
                            <?php endforeach;
                            $__env->popLoop();
                            $loop = $__env->getLastLoop(); ?>
                        </div>


                        <button id="playPauseBtn" onclick="toggleAutoPlay()"
                            class="absolute bottom-6 right-6 z-30 w-10 h-10 bg-black/50 backdrop-blur-md border border-white/20 rounded-full flex items-center justify-center hover:bg-black/70 transition-all">
                            <svg id="pauseIcon" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg id="playIcon" class="w-5 h-5 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                    </section>
                    @endif





                    <section>
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-6 bg-pink-500 rounded-full shadow-[0_0_10px_rgba(236,72,153,0.8)]"></div>
                                <h2 class="text-xl font-bold text-white"> Jelajahi Genre</h2>
                            </div>
                            <a href="/kategori" class="text-sm text-gray-400 hover:text-white hover:underline transition-all">Lihat semua</a>
                        </div>
                        <?php
                        $genre_showcase = [
                            ['name' => 'Action', 'count' => \App\Models\Game::where('genre', 'like', '%Action%')->count() . ' game', 'icon' => '⚔️', 'color' => 'bg-gradient-to-br from-red-900/40 to-red-950/60', 'border' => 'border-red-800/30', 'hover' => 'hover:border-red-600/50 hover:shadow-[0_0_20px_rgba(220,38,38,0.3)]'],
                            ['name' => 'RPG', 'count' => \App\Models\Game::where('genre', 'like', '%RPG%')->count() . ' game', 'icon' => '🎭', 'color' => 'bg-gradient-to-br from-purple-900/40 to-purple-950/60', 'border' => 'border-purple-800/30', 'hover' => 'hover:border-purple-600/50 hover:shadow-[0_0_20px_rgba(147,51,234,0.3)]'],
                            ['name' => 'FPS', 'count' => \App\Models\Game::where('genre', 'like', '%FPS%')->count() . ' game', 'icon' => '🎯', 'color' => 'bg-gradient-to-br from-green-900/40 to-green-950/60', 'border' => 'border-green-800/30', 'hover' => 'hover:border-green-600/50 hover:shadow-[0_0_20px_rgba(22,163,74,0.3)]'],
                            ['name' => 'Racing', 'count' => \App\Models\Game::where('genre', 'like', '%Racing%')->count() . ' game', 'icon' => '🏎️', 'color' => 'bg-gradient-to-br from-orange-900/40 to-orange-950/60', 'border' => 'border-orange-800/30', 'hover' => 'hover:border-orange-600/50 hover:shadow-[0_0_20px_rgba(249,115,22,0.3)]'],
                            ['name' => 'Sports', 'count' => \App\Models\Game::where('genre', 'like', '%Sports%')->count() . ' game', 'icon' => '⚽', 'color' => 'bg-gradient-to-br from-green-900/40 to-green-950/60', 'border' => 'border-green-800/30', 'hover' => 'hover:border-green-600/50 hover:shadow-[0_0_20px_rgba(34,197,94,0.3)]'],
                            ['name' => 'Horror', 'count' => \App\Models\Game::where('genre', 'like', '%Horror%')->count() . ' game', 'icon' => '💀', 'color' => 'bg-gradient-to-br from-gray-900/40 to-gray-950/60', 'border' => 'border-gray-800/30', 'hover' => 'hover:border-gray-600/50 hover:shadow-[0_0_20px_rgba(107,114,128,0.3)]'],
                            ['name' => 'Strategy', 'count' => \App\Models\Game::where('genre', 'like', '%Strategy%')->count() . ' game', 'icon' => '♟️', 'color' => 'bg-gradient-to-br from-blue-900/40 to-blue-950/60', 'border' => 'border-blue-800/30', 'hover' => 'hover:border-blue-600/50 hover:shadow-[0_0_20px_rgba(37,99,235,0.3)]'],
                            ['name' => 'Open World', 'count' => \App\Models\Game::where('genre', 'like', '%Open World%')->count() . ' game', 'icon' => '🗺️', 'color' => 'bg-gradient-to-br from-cyan-900/40 to-cyan-950/60', 'border' => 'border-cyan-800/30', 'hover' => 'hover:border-cyan-600/50 hover:shadow-[0_0_20px_rgba(6,182,212,0.3)]'],
                        ];
                        ?>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <?php $__currentLoopData = $genre_showcase;
                            $__env->addLoop($__currentLoopData);
                            foreach ($__currentLoopData as $genre): $__env->incrementLoopIndices();
                                $loop = $__env->getLastLoop(); ?>
                                <a href="/kategori?genre={{ $genre['name'] }}&filter=1" class="{{ $genre['color'] }} border {{ $genre['border'] }} {{ $genre['hover'] }} rounded-xl p-5 flex items-center gap-3 transition-all duration-300 group cursor-pointer relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 to-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl relative z-10 group-hover:scale-110 transition-transform">
                                        @include('components.genre-svg', ['genre' => $genre['name'], 'fallbackIcon' => $genre['icon']])
                                    </div>
                                    <div class="flex-1 relative z-10">
                                        <p class="font-bold text-white text-sm mb-0.5">{{ $genre['name'] }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $genre['count'] }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-white/40 relative z-10 group-hover:translate-x-1 group-hover:text-white/60 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            <?php endforeach;
                            $__env->popLoop();
                            $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>




                    <section>
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-6 bg-red-500 rounded-full shadow-[0_0_10px_rgba(239,68,68,0.8)]"></div>
                                <h2 class="text-xl font-bold text-white"> Trending Sekarang</h2>
                            </div>
                            <div class="flex items-center gap-3">
                                <button onclick="scrollSlider('trendingSlider', -1)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button onclick="scrollSlider('trendingSlider', 1)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div id="trendingSlider" class="flex gap-4 overflow-x-auto hide-scrollbar scroll-smooth snap-x snap-mandatory pb-4">
                            <?php $__currentLoopData = $trending_games;
                            $__env->addLoop($__currentLoopData);
                            foreach ($__currentLoopData as $idx => $game): $__env->incrementLoopIndices();
                                $loop = $__env->getLastLoop(); ?>
                                <?php $avg = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0; ?>
                                <div class="group relative card-bg rounded-xl overflow-hidden hover-card cursor-pointer flex flex-col flex-none w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.66rem)] lg:w-[calc(16.666%-0.83rem)] snap-start" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}">

                                    <div class="absolute top-1.5 left-1.5 z-10 w-6 h-6 rounded-md flex items-center justify-center text-[10px] font-black {{ $idx < 3 ? 'bg-yellow-500 text-black shadow-lg' : 'bg-black/70 text-white border border-white/20' }}">
                                        {{ $idx + 1 }}

                                    </div>

                                    <?php
                                    $isOwned = in_array($game->id, $ownedGameIds ?? []);
                                    $isInCart = in_array($game->id, $cartGameIds ?? []);
                                    $isInWishlist = in_array($game->id, $wishlistGameIds ?? []);
                                    ?>
                                    @if($idx < 3 && !$isOwned && !$isInCart && !$isInWishlist)
                                        <div class="absolute top-1.5 right-1.5 z-10 bg-red-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-lg animate-pulse">HOT
                                </div>
                                @endif
                                <div class="aspect-[3/4] overflow-hidden bg-black">
                                    <?php if ($isOwned): ?>
                                        <div class="absolute top-2 right-2 bg-gray-800 text-white text-[9px] font-bold px-2 py-1 rounded shadow-lg border border-gray-600/50 backdrop-blur-sm z-20">SUDAH DIMILIKI</div>
                                    <?php elseif ($isInCart): ?>
                                        <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-20">DI KERANJANG</div>
                                    <?php elseif ($isInWishlist): ?>
                                        <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-20">WISHLIST</div>
                                    <?php endif; ?>
                                    <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="{{ $isOwned ? 'opacity-40 grayscale-[60%]' : '' }} w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">
                                </div>
                                <div class="p-2.5 flex-1 flex flex-col">
                                    <h3 class="font-bold text-white text-[11px] leading-tight mb-1 line-clamp-2 group-hover:text-purple-400 transition-colors">{{ $game->name }}</h3>
                                    <div class="flex items-center gap-1 mt-0.5 mb-2">
                                        <span class="text-[8px] text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded-full border border-purple-500/20 line-clamp-1">{{ explode(',', $game->genre)[0] }}</span>
                                        <span class="text-[8px] text-gray-400 bg-white/5 px-1.5 py-0.5 rounded-full border border-white/10 line-clamp-1">{{ explode(',', $game->platform)[0] }}</span>
                                        @if($game->console_edition)
                                            @foreach(explode(',', $game->console_edition) as $ce)
                                                <span class="text-[8px] text-pink-400 bg-pink-500/10 px-1.5 py-0.5 rounded-full border border-pink-500/20">{{ trim($ce) }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 mt-auto">
                                        <span class="text-yellow-500 text-[10px]">★</span>
                                        <span class="text-[10px] font-bold text-white">{{ $avg ?: 'Baru' }}</span>
                                    </div>
                                    <p class="text-[10px] font-bold mt-1 {{ $game->price == 0 ? 'text-green-400' : 'text-white' }}">{{ $game->price == 0 ? 'Gratis' : "Rp " . number_format($game->price, 0, ',', '.') }}</p>
                                </div>
                        </div>
                    <?php endforeach;
                            $__env->popLoop();
                            $loop = $__env->getLastLoop(); ?>
                </div>
                </section>




                <section>
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-6 bg-green-400 rounded-full shadow-[0_0_10px_rgba(74,222,128,0.8)]"></div>
                            <h2 class="text-xl font-bold text-white"> Baru Ditambahkan</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="scrollSlider('newReleasesSlider', -1)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button onclick="scrollSlider('newReleasesSlider', 1)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="newReleasesSlider" class="flex gap-4 overflow-x-auto hide-scrollbar scroll-smooth snap-x snap-mandatory pb-4">
                        <?php $__currentLoopData = $new_releases;
                        $__env->addLoop($__currentLoopData);
                        foreach ($__currentLoopData as $game): $__env->incrementLoopIndices();
                            $loop = $__env->getLastLoop(); ?>
                            <?php
                            $avg = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0;
                            $isOwned = in_array($game->id, $ownedGameIds ?? []);
                            $isInCart = in_array($game->id, $cartGameIds ?? []);
                            $isInWishlist = in_array($game->id, $wishlistGameIds ?? []);
                            ?>
                            <div class="group card-bg rounded-xl overflow-hidden hover-card cursor-pointer flex flex-col relative flex-none w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.66rem)] lg:w-[calc(16.666%-0.83rem)] snap-start" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}">
                                @if(!$isOwned && !$isInCart && !$isInWishlist)
                                <span class="absolute top-1.5 right-1.5 z-10 bg-green-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-lg">Baru</span>
                                @endif
                                <div class="aspect-[3/4] overflow-hidden bg-black">

                                    <?php if ($isOwned): ?>
                                        <div class="absolute top-2 right-2 bg-gray-800 text-white text-[9px] font-bold px-2 py-1 rounded shadow-lg border border-gray-600/50 backdrop-blur-sm z-20">SUDAH DIMILIKI</div>
                                    <?php elseif ($isInCart): ?>
                                        <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-20">DI KERANJANG</div>
                                    <?php elseif ($isInWishlist): ?>
                                        <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-20">WISHLIST</div>
                                    <?php endif; ?>
                                    <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="{{ $isOwned ? 'opacity-40 grayscale-[60%]' : '' }} w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">
                                </div>
                                <div class="p-2.5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="font-bold text-white text-[11px] leading-tight mb-1.5 line-clamp-2 group-hover:text-purple-400 transition-colors">{{ $game->name }}</h3>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="text-[8px] text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded-full border border-purple-500/20 line-clamp-1">{{ explode(',', $game->genre)[0] }}</span>
                                            <span class="text-[8px] text-gray-400 bg-white/5 px-1.5 py-0.5 rounded-full border border-white/10 line-clamp-1">{{ explode(',', $game->platform)[0] }}</span>
                                        @if($game->console_edition)
                                            @foreach(explode(',', $game->console_edition) as $ce)
                                                <span class="text-[8px] text-pink-400 bg-pink-500/10 px-1.5 py-0.5 rounded-full border border-pink-500/20">{{ trim($ce) }}</span>
                                            @endforeach
                                        @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-white/5">
                                        <div class="flex items-center gap-0.5">
                                            <span class="text-yellow-500 text-[10px]">★</span>
                                            <span class="text-[10px] font-bold text-white">{{ $avg ?: 'Baru' }}</span>
                                        </div>
                                        <p class="text-[10px] font-bold {{ $game->price == 0 ? 'text-green-400' : 'text-white' }}">{{ $game->price == 0 ? 'Gratis' : "Rp " . number_format($game->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;
                        $__env->popLoop();
                        $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>




                <section class="relative rounded-2xl p-8 md:p-10 card-bg shrink-0">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="flex-1">
                            <span class="text-[10px] font-bold text-purple-400 uppercase tracking-widest bg-purple-500/10 border border-purple-500/20 px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 mb-4"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.3">
                                        <path d="M5 10H19V20H5V10Z" fill="currentColor" />
                                        <path d="M4 7H20V10H4V7Z" fill="currentColor" />
                                    </g>
                                    <path d="M19 10.0802V20.0802H5V10.0802M19 10.0802H5M19 10.0802H20V7.0802H4V10.0802H5M12 7.0802C12.8333 5.24687 14.9999 1.5802 16.9999 3.5802C18.9999 5.5802 14.5 6.91353 12 7.0802ZM12 7.0802C11.1667 5.24687 8.99999 1.5802 6.99999 3.5802C4.99999 5.5802 9.5 6.91353 12 7.0802Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg> Penawaran Spesial</span>
                            <h2 class="text-3xl md:text-5xl font-black text-white mb-3 leading-tight mt-2">Koleksi Game <br><span style="background: linear-gradient(135deg, #c084fc, #a855f7, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 0 12px rgba(168,85,247,0.6));">Premium</span> Kamu Disini</h2>
                            <p class="text-gray-400 text-sm md:text-base max-w-md">Temukan ratusan game dari berbagai genre. Beli sekali, mainkan selamanya.</p>
                        </div>
                        <div class="flex flex-col items-center gap-6 flex-shrink-0">
                            <div class="flex items-center gap-8 text-center">
                                <div>
                                    <p class="text-4xl font-black text-white">{{ $stats['total_games'] }}+</p>
                                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mt-1">Game</p>
                                </div>
                                <div class="w-px h-12 bg-white/10"></div>
                                <div>
                                    <p class="text-4xl font-black text-white">{{ $stats['total_reviews'] }}+</p>
                                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mt-1">Ulasan</p>
                                </div>
                            </div>
                            <a href="#katalog" onclick="document.getElementById('katalog').scrollIntoView({ behavior: 'smooth' }); return false;" class="px-6 py-3.5 bg-white text-black font-black rounded-xl hover:bg-gray-200 transition-colors text-[11px] uppercase tracking-widest w-full text-center">
                                Jelajahi Semua Game →
                            </a>
                        </div>
                    </div>
                </section>




                @if($free_games->count() > 0)
                <section>
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-6 bg-green-400 rounded-full shadow-[0_0_10px_rgba(74,222,128,0.8)]"></div>
                            <h2 class="text-xl font-bold text-white"> Game Gratis</h2>
                        </div>
                        <a href="/search?price=free" class="text-sm text-purple-400 hover:text-purple-300 font-medium">Lihat semua →</a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <?php $__currentLoopData = $free_games;
                        $__env->addLoop($__currentLoopData);
                        foreach ($__currentLoopData as $game): $__env->incrementLoopIndices();
                            $loop = $__env->getLastLoop(); ?>
                            <div class="group card-bg rounded-xl overflow-hidden hover-card cursor-pointer flex flex-col relative" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}">
                                <span class="absolute top-2 left-2 z-10 bg-green-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase">GRATIS</span>
                                <div class="aspect-[3/4] overflow-hidden bg-black">

                                    <?php
                                    $isOwned = in_array($game->id, $ownedGameIds ?? []);
                                    $isInCart = in_array($game->id, $cartGameIds ?? []);
                                    $isInWishlist = in_array($game->id, $wishlistGameIds ?? []);
                                    ?>
                                    <?php if ($isOwned): ?>
                                        <div class="absolute top-2 right-2 bg-gray-800 text-white text-[9px] font-bold px-2 py-1 rounded shadow-lg border border-gray-600/50 backdrop-blur-sm z-20">SUDAH DIMILIKI</div>
                                    <?php elseif ($isInCart): ?>
                                        <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-20">DI KERANJANG</div>
                                    <?php elseif ($isInWishlist): ?>
                                        <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-20">WISHLIST</div>
                                    <?php endif; ?>
                                    <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="{{ $isOwned ? 'opacity-40 grayscale-[60%]' : '' }} w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">
                                </div>
                                <div class="p-3 flex-1 flex flex-col">
                                    <h3 class="font-bold text-white text-xs line-clamp-2 mb-1 group-hover:text-green-400 transition-colors">{{ $game->name }}</h3>
                                    <div class="flex items-center gap-1.5 mb-2 mt-0.5">
                                        <span class="text-[9px] text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded">{{ explode(',', $game->genre)[0] }}</span>
                                        <span class="text-[9px] text-gray-400 bg-white/5 px-1.5 py-0.5 rounded">{{ explode(',', $game->platform)[0] }}</span>
                                        @if($game->console_edition)
                                            @foreach(explode(',', $game->console_edition) as $ce)
                                                <span class="text-[9px] text-pink-400 bg-pink-500/10 px-1.5 py-0.5 rounded border border-pink-500/20">{{ trim($ce) }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                    <p class="text-green-400 text-xs font-black mt-auto">Gratis</p>
                                </div>
                            </div>
                        <?php endforeach;
                        $__env->popLoop();
                        $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
                @endif




                <section id="section-game-populer">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-6 bg-yellow-400 rounded-full shadow-[0_0_10px_rgba(250,204,21,0.8)]"></div>
                            <h2 class="text-xl font-bold text-white"> Game Populer</h2>
                        </div>
                        <a href="/search?sort=popular" class="text-sm text-purple-400 hover:text-purple-300 font-medium">Lihat semua →</a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        <?php $__currentLoopData = $populer_games;
                        $__env->addLoop($__currentLoopData);
                        foreach ($__currentLoopData as $game): $__env->incrementLoopIndices();
                            $loop = $__env->getLastLoop(); ?>
                            <?php
                            $avg_rating = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0;
                            $total_reviews = $game->reviews->count();
                            ?>
                            <div class="card-bg rounded-xl overflow-hidden hover-card cursor-pointer flex flex-col relative group" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}">

                                @if($avg_rating >= 4.5)
                                <div class="absolute top-2 right-2 z-10 bg-yellow-500 text-black text-[9px] font-black px-1.5 py-0.5 rounded">TOP</div>
                                @elseif($game->price == 0)
                                <div class="absolute top-2 right-2 z-10 bg-green-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded">GRATIS</div>
                                @elseif($game->created_at && $game->created_at->diffInDays(now()) < 30)
                                    <div class="absolute top-2 right-2 z-10 bg-blue-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded">Baru
                            </div>
                            @endif
                            <div class="relative aspect-[3/4] overflow-hidden bg-black">

                                <?php
                                $isOwned = in_array($game->id, $ownedGameIds ?? []);
                                $isInCart = in_array($game->id, $cartGameIds ?? []);
                                $isInWishlist = in_array($game->id, $wishlistGameIds ?? []);
                                ?>
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
                                    @if($game->console_edition)
                                        @foreach(explode(',', $game->console_edition) as $ce)
                                            <span class="text-[10px] text-pink-500 bg-pink-500/10 px-1.5 py-0.5 rounded border border-pink-500/20">{{ trim($ce) }}</span>
                                        @endforeach
                                    @endif
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
                <?php endforeach;
                        $__env->popLoop();
                        $loop = $__env->getLastLoop(); ?>
        </div>
        </section>




        <section id="katalog">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-6 bg-white rounded-full shadow-[0_0_10px_rgba(255,255,255,0.5)]"></div>
                    <h2 class="text-xl font-bold text-white"> Semua Game</h2>
                </div>
                <span class="text-xs text-gray-500 bg-[#12151C] px-3 py-1.5 rounded-lg border border-white/5">{{ $stats['total_games'] }} game tersedia</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <?php $__currentLoopData = $games->take(12);
                $__env->addLoop($__currentLoopData);
                foreach ($__currentLoopData as $game): $__env->incrementLoopIndices();
                    $loop = $__env->getLastLoop(); ?>
                    <?php
                    $avg_rating = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0;
                    $total_reviews = $game->reviews->count();
                    ?>
                    <div class="card-bg rounded-xl overflow-hidden hover-card cursor-pointer flex flex-col relative group" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}">
                        <?php
                        $isOwned = in_array($game->id, $ownedGameIds ?? []);
                        $isInCart = in_array($game->id, $cartGameIds ?? []);
                        $isInWishlist = in_array($game->id, $wishlistGameIds ?? []);
                        ?>
                        @if(!$isOwned && !$isInCart && !$isInWishlist)
                        @if($game->price == 0)
                        <div class="absolute top-2 right-2 z-10 bg-green-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded">GRATIS</div>
                        @elseif($avg_rating >= 4.5)
                        <div class="absolute top-2 right-2 z-10 bg-yellow-500 text-black text-[9px] font-black px-1.5 py-0.5 rounded">TOP</div>
                        @endif
                        @endif
                        <div class="relative aspect-[4/3] overflow-hidden bg-black">
                            <?php if ($isOwned): ?>
                                <div class="absolute top-2 right-2 bg-gray-800 text-white text-[9px] font-bold px-2 py-1 rounded shadow-lg border border-gray-600/50 backdrop-blur-sm z-20">SUDAH DIMILIKI</div>
                            <?php elseif ($isInCart): ?>
                                <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-20">DI KERANJANG</div>
                            <?php elseif ($isInWishlist): ?>
                                <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-20">WISHLIST</div>
                            <?php endif; ?>
                            <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="{{ $isOwned ? 'opacity-40 grayscale-[60%]' : '' }} w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                <button onclick="event.stopPropagation(); window.tambahKeranjangCerdas('{{ $game->id }}', false, this)" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-colors">+ Keranjang</button>
                            </div>
                        </div>
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-white text-sm leading-tight mb-1 line-clamp-1 group-hover:text-purple-400 transition-colors">{{ $game->name }}</h3>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded">{{ $game->genre }}</span>
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
                <?php endforeach;
                $__env->popLoop();
                $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-8 flex justify-center">
                <a href="/kategori" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg shadow-purple-500/30 flex items-center gap-2">
                    Jelajahi Lebih Banyak
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </section>


        @include('footer')

    </div>{{-- end #berandaContent --}}

    {{-- ===== PANEL PUSAT BANTUAN ===== --}}
    <div id="bantuanPanel" class="absolute inset-0 overflow-y-auto hide-scrollbar transition-all duration-500 ease-in-out" style="transform: translateX(100%); opacity: 0; pointer-events: none;">
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
                                            C235.943,223.156,226.391,213.605,214.609,213.605z" />
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
                                            C475.42,341.016,474.252,384.613,455.757,403.285z" />
                                        <path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333
                                            c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z" />
                                        <path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333
                                            S354.385,234.938,342.609,234.938z" />
                                        <path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333
                                            S311.719,192.271,299.943,192.271z" />
                                        <path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333
                                            S397.052,192.271,385.276,192.271z" />
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
    </div>{{-- end #bantuanPanel --}}

    </main>
    </div>
    </div>


    <div id="premiumCartSidebar" class="fixed inset-y-0 right-0 w-full sm:w-[400px] border-l border-white/10 z-[200] transform translate-x-full transition-transform duration-300 flex flex-col" style="background-color: #0F111A !important; box-shadow: -10px 0 50px rgba(0,0,0,0.8);">
        <div class="h-20 flex items-center justify-between px-6 border-b border-white/10" style="background-color: #0A0C10 !important;">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" style="color: #8B5CF6;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path>
                </svg>
                <h2 class="text-lg font-black text-white uppercase tracking-widest">Keranjang Saya</h2>
            </div>
            <button onclick="closeCart()" class="text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="premiumCartContent" class="flex-1 overflow-y-auto p-6 space-y-4 hide-scrollbar" style="background-color: #0A0C10 !important;">
            <div class="flex flex-col items-center justify-center h-full text-gray-500"><span class="animate-pulse">Memuat data keranjang...</span></div>
        </div>

        <div class="p-6 border-t border-white/10" style="background-color: #0F111A !important;">
            <div class="flex justify-between items-center mb-6">
                <span class="text-gray-400 font-medium">Total Pembayaran</span>
                <span id="premiumCartTotal" class="text-2xl font-black" style="color: #FDE047 !important;">Rp 0</span>
            </div>


            <form action="{{ url('/checkout') }}" method="POST" class="w-full">
                <?php echo csrf_field(); ?>
                <button type="submit" class="block w-full text-center text-white font-bold py-4 rounded-xl transition-all uppercase tracking-widest shadow-lg hover:brightness-110" style="background-color: #7C3AED !important; box-shadow: 0 0 20px rgba(124,58,237,0.3);">
                    CHECKOUT SEKARANG
                </button>
            </form>
        </div>
    </div>
    <div id="cartOverlay" onclick="closeCart()" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[190] hidden opacity-0 transition-opacity duration-300"></div>


    <div id="loginModal" class="hidden fixed inset-0 z-[250] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity duration-300">
        <div class="bg-[#12151C] p-8 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.1)] w-full max-w-md relative">
            <button onclick="document.getElementById('loginModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-2xl font-black text-white text-center mb-2 tracking-wide uppercase">AKSES DITOLAK</h2>
            <p class="text-gray-400 text-center text-sm mb-6">Kamu harus login terlebih dahulu.</p>
            <a href="{{ url('/login') }}" class="block w-full text-center text-white font-bold py-3 rounded-xl transition-all" style="background-color: #7C3AED !important;">LOGIN SEKARANG</a>
        </div>
    </div>


    {{-- Modal Konfirmasi Sukses (Menggantikan Toast) --}}
    @include('components.success-modal')

    <script>
        function hideToast() {
            closeSuccessModal();
        }

        // --- FUNGSI TAMBAH KERANJANG (DENGAN SPINNER) ---
        window.tambahKeranjangCerdas = function(gameId, isBuyNow, btnElement) {
            let isLoggedIn = @json(Auth::check());
            if (isLoggedIn === 'false' || isLoggedIn === false) {
                document.getElementById('loginModal').classList.remove('hidden');
                return;
            }

            const originalContent = btnElement.innerHTML;

            // Ubah tombol jadi Spinner Loading Bawaan Tailwind
            btnElement.innerHTML = `<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            btnElement.disabled = true;

            // OPTIMISTIC UPDATE
            let originalCount = 0;
            if (!isBuyNow) {
                let badge = document.getElementById('globalCartBadge');
                originalCount = badge && badge.innerText && badge.style.display !== 'none' ? parseInt(badge.innerText) : 0;
                if (isNaN(originalCount)) originalCount = 0;
                let newCount = originalCount + 1;

                if (badge) {
                    badge.innerText = newCount;
                    badge.style.setProperty('display', 'flex', 'important');
                }
                localStorage.setItem('cartCount', newCount);
            }

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
                    if (isBuyNow) {
                        window.location.href = '/cart';
                    } else {
                        btnElement.innerHTML = originalContent;
                        btnElement.disabled = false;
                        if (data.status === 'success') {
                            showToast('Game berhasil masuk ke keranjangmu!');
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
                        } else {
                            showToast(data.message || 'Gagal menambahkan game ke keranjang');
                            // REVERT OPTIMISTIC UPDATE
                            let badge = document.getElementById('globalCartBadge');
                            if (badge) {
                                badge.innerText = originalCount;
                                badge.style.setProperty('display', originalCount > 0 ? 'flex' : 'none', 'important');
                            }
                            localStorage.setItem('cartCount', originalCount);
                        }
                    }
                })
                .catch(() => {
                    if (isBuyNow) window.location.href = '/cart';
                    else {
                        btnElement.innerHTML = originalContent;
                        btnElement.disabled = false;
                        showToast('Terjadi kesalahan jaringan!');
                        // REVERT OPTIMISTIC UPDATE
                        let badge = document.getElementById('globalCartBadge');
                        if (badge) {
                            badge.innerText = originalCount;
                            badge.style.setProperty('display', originalCount > 0 ? 'flex' : 'none', 'important');
                        }
                        localStorage.setItem('cartCount', originalCount);
                    }
                });
        };
    </script>
    <script>
        // FUNGSI MEMUNCULKAN ANGKA COUNT KERANJANG OTOMATIS
        function updateGlobalCartBadge() {
            let isLoggedIn = <?php echo json_encode(Auth::check(), 15, 512) ?>;
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
                    let badge = document.getElementById('globalCartBadge');
                    if (badge) {
                        badge.innerText = cartCount;
                        badge.style.setProperty('display', cartCount > 0 ? 'flex' : 'none', 'important');
                    }
                }).catch(e => console.log(e));
        }
    </script>
    <script>
        // MESIN LIVE SEARCH / AUTOCOMPLETE ALA STEAM
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            let timeout = null;

            // Debug: Cek apakah elemen ditemukan
            console.log('Search Input:', searchInput);
            console.log('Search Results:', searchResults);

            if (searchInput && searchResults) {
                // Test klik
                searchInput.addEventListener('click', function() {
                    console.log('Search input clicked!');
                });

                searchInput.addEventListener('input', function() {
                    // Bersiin timer biar server ora ngelag pas ngetik cepet
                    clearTimeout(timeout);
                    const query = this.value.trim();

                    // Sembunyiin kalo ketikan kurang dari 2 huruf
                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        searchResults.classList.remove('flex');
                        return;
                    }

                    // Tunda pencarian 300ms setelah berhenti mengetik
                    timeout = setTimeout(() => {
                        // Menampilkan tulisan loading sementara
                        searchResults.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 animate-pulse">Mencari game...</div>';
                        searchResults.classList.remove('hidden');
                        searchResults.classList.add('flex');

                        // Panggil rute Laravel
                        fetch(`/search/autocomplete?query=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                searchResults.innerHTML = ''; 

                                if (data.length > 0) {
                                    // Looping data game dari database
                                    data.forEach(game => {
                                        // Cek Harga
                                        const priceText = game.price == 0 ? 'Gratis' : 'Rp ' + new Intl.NumberFormat('id-ID').format(game.price);

                                        // Desain HTML Item biar kayak Steam hehe awok awok
                                        const item = document.createElement('a');
                                        item.href = `/game/${game.id}`;
                                        item.className = 'flex items-center gap-3 p-3 hover:bg-[#2A2E37] border-b border-white/5 transition-colors cursor-pointer';

                                        item.innerHTML = `
                                            <img src="/assets/${game.image}" onerror="this.src='/assets/no-image.jpg'" class="w-[80px] h-[45px] object-cover rounded border border-white/10 shadow-sm flex-shrink-0" style="opacity: 1 !important; filter: brightness(1) !important;">
                                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                                <h4 class="text-white text-sm font-bold truncate mb-0.5">${game.name}</h4>
                                                <p class="text-emerald-400 text-xs font-bold">${priceText}</p>
                                            </div>
                                        `;
                                        searchResults.appendChild(item);
                                    });
                                } else {
                                    // Jika game tidak ditemukan
                                    searchResults.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">Game tidak ditemukan</div>';
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                searchResults.innerHTML = '<div class="p-4 text-center text-sm text-red-500">Gagal mengambil data.</div>';
                            });
                    }, 300);
                });

                // Trik: Sembunyikan hasil kalau user klik di luar kotak pencarian
                document.addEventListener('click', function(event) {
                    if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                        searchResults.classList.add('hidden');
                        searchResults.classList.remove('flex');
                    }
                });

                // Tambahkan event listener untuk Enter
                searchInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const query = this.value.trim();
                        if (query.length > 0) {
                            window.location.href = `/search?q=${encodeURIComponent(query)}`;
                        }
                    }
                });

                // Munculkan lagi kalau diklik inputannya dan isinya ada
                searchInput.addEventListener('click', function() {
                    if (this.value.trim().length >= 2 && searchResults.innerHTML !== '') {
                        searchResults.classList.remove('hidden');
                        searchResults.classList.add('flex');
                    }
                });
            }
        });
    </script>


    <script>
        // Scroll horizontal helper
        function scrollSection(id, amount) {
            const el = document.getElementById(id);
            if (el) el.scrollBy({
                left: amount,
                behavior: 'smooth'
            });
        }

        // Counter animasi angka naik
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.counter[data-target]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const target = parseInt(el.getAttribute('data-target'));
                        if (!target || target === 0) return;
                        let current = 0;
                        const increment = Math.ceil(target / 60);
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                el.textContent = target.toLocaleString('id-ID');
                                clearInterval(timer);
                            } else {
                                el.textContent = current.toLocaleString('id-ID');
                            }
                        }, 20);
                        observer.unobserve(el);
                    }
                });
            }, {
                threshold: 0.3
            });
            counters.forEach(c => observer.observe(c));

            // Gaming Particle Animation
            const canvas = document.getElementById('particles-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;

                let particles = [];
                const particleCount = 50;
                const colors = ['#7C3AED', '#8B5CF6', '#A78BFA', '#C4B5FD'];

                class Particle {
                    constructor() {
                        this.x = Math.random() * canvas.width;
                        this.y = Math.random() * canvas.height;
                        this.size = Math.random() * 2 + 1;
                        this.speedX = (Math.random() - 0.5) * 0.5;
                        this.speedY = (Math.random() - 0.5) * 0.5;
                        this.color = colors[Math.floor(Math.random() * colors.length)];
                        this.opacity = Math.random() * 0.5 + 0.2;
                    }

                    update() {
                        this.x += this.speedX;
                        this.y += this.speedY;

                        if (this.x > canvas.width) this.x = 0;
                        if (this.x < 0) this.x = canvas.width;
                        if (this.y > canvas.height) this.y = 0;
                        if (this.y < 0) this.y = canvas.height;
                    }

                    draw() {
                        ctx.fillStyle = this.color;
                        ctx.globalAlpha = this.opacity;
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.globalAlpha = 1;
                    }
                }

                function init() {
                    particles = [];
                    for (let i = 0; i < particleCount; i++) {
                        particles.push(new Particle());
                    }
                }

                function animate() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    particles.forEach(particle => {
                        particle.update();
                        particle.draw();
                    });

                    // Draw connections
                    particles.forEach((a, i) => {
                        particles.slice(i + 1).forEach(b => {
                            const dx = a.x - b.x;
                            const dy = a.y - b.y;
                            const distance = Math.sqrt(dx * dx + dy * dy);

                            if (distance < 150) {
                                ctx.strokeStyle = '#7C3AED';
                                ctx.globalAlpha = (1 - distance / 150) * 0.1;
                                ctx.lineWidth = 0.5;
                                ctx.beginPath();
                                ctx.moveTo(a.x, a.y);
                                ctx.lineTo(b.x, b.y);
                                ctx.stroke();
                                ctx.globalAlpha = 1;
                            }
                        });
                    });

                    requestAnimationFrame(animate);
                }

                init();
                animate();

                window.addEventListener('resize', () => {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                    init();
                });
            }
        });
    </script>


    <script>
        // ========================================
        // [0] HERO CAROUSEL AUTO-PLAY (STEAM/EPIC STYLE)
        // ========================================
        (function() {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.carousel-dot');
            const playPauseBtn = document.getElementById('playPauseBtn');
            const pauseIcon = document.getElementById('pauseIcon');
            const playIcon = document.getElementById('playIcon');

            let currentSlide = 0;
            let autoPlayInterval;
            let isPlaying = true;
            const slideDelay = 5000; // 5 detik per slide

            // Function untuk ganti slide
            window.goToSlide = function(index) {
                // Reset semua slides
                slides.forEach(slide => {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0', 'z-0');
                });

                // Reset progress bars
                dots.forEach(dot => {
                    dot.classList.remove('active');
                    const progressBar = dot.querySelector('div');
                    if (progressBar) {
                        progressBar.style.transform = 'scaleX(0)';
                    }
                });

                // Aktifkan slide baru
                slides[index].classList.remove('opacity-0', 'z-0');
                slides[index].classList.add('opacity-100', 'z-10');

                // Aktifkan dot baru
                dots[index].classList.add('active');
                const progressBar = dots[index].querySelector('div');
                if (progressBar) {
                    progressBar.style.transition = `transform ${slideDelay}ms linear`;
                    progressBar.style.transform = 'scaleX(1)';
                }

                currentSlide = index;
            };

            // Function next slide
            window.nextSlide = function() {
                const next = (currentSlide + 1) % slides.length;
                goToSlide(next);
                resetAutoPlay();
            };

            // Function prev slide
            window.prevSlide = function() {
                const prev = (currentSlide - 1 + slides.length) % slides.length;
                goToSlide(prev);
                resetAutoPlay();
            };

            // Function toggle auto-play
            window.toggleAutoPlay = function() {
                if (isPlaying) {
                    clearInterval(autoPlayInterval);
                    pauseIcon.classList.add('hidden');
                    playIcon.classList.remove('hidden');
                    isPlaying = false;
                } else {
                    startAutoPlay();
                    pauseIcon.classList.remove('hidden');
                    playIcon.classList.add('hidden');
                    isPlaying = true;
                }
            };

            // Function start auto-play
            function startAutoPlay() {
                autoPlayInterval = setInterval(() => {
                    nextSlide();
                }, slideDelay);
            }

            // Function reset auto-play
            function resetAutoPlay() {
                if (isPlaying) {
                    clearInterval(autoPlayInterval);
                    startAutoPlay();
                }
            }

            // Initialize
            if (slides.length > 0) {
                goToSlide(0);
                startAutoPlay();

                // Pause on hover
                const carousel = document.getElementById('heroCarousel');
                if (carousel) {
                    carousel.addEventListener('mouseenter', () => {
                        if (isPlaying) {
                            clearInterval(autoPlayInterval);
                        }
                    });

                    carousel.addEventListener('mouseleave', () => {
                        if (isPlaying) {
                            startAutoPlay();
                        }
                    });
                }
            }
        })();

        // ========================================
        // [1] PARTICLE CANVAS ANIMATION (Subtle Gaming Effect)
        // ========================================
        (function() {
            const canvas = document.getElementById('particles-canvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const particles = [];
            const particleCount = 50; // Reduced for better performance

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2 + 1;
                    this.speedX = Math.random() * 0.5 - 0.25;
                    this.speedY = Math.random() * 0.5 - 0.25;
                    this.opacity = Math.random() * 0.5 + 0.2;
                }

                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    if (this.x > canvas.width) this.x = 0;
                    if (this.x < 0) this.x = canvas.width;
                    if (this.y > canvas.height) this.y = 0;
                    if (this.y < 0) this.y = canvas.height;
                }

                draw() {
                    ctx.fillStyle = `rgba(124, 58, 237, ${this.opacity})`;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            // Initialize particles
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }

            // Animation loop
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(particle => {
                    particle.update();
                    particle.draw();
                });
                requestAnimationFrame(animate);
            }

            animate();

            // Resize handler
            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        })();

        // ========================================
        // [2] SMOOTH SCROLL REVEAL ANIMATIONS
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.05,
                rootMargin: '50px 0px 50px 0px'
            };

            const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        fadeObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Apply fade-in animation to sections
            const sections = document.querySelectorAll('main > section');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = `all 0.3s ease-out ${index * 0.05}s`;
                fadeObserver.observe(section);
            });

            // Animate cards on scroll
            const cards = document.querySelectorAll('.gaming-card, .card-bg, .hover-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(15px)';
                card.style.transition = `all 0.25s ease-out ${(index % 6) * 0.03}s`;
                fadeObserver.observe(card);
            });
        });

        // ========================================
        // [3] BADGE PULSE ANIMATION
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const badges = document.querySelectorAll('[class*="badge"], [class*="HOT"], [class*="NEW"]');
            badges.forEach(badge => {
                badge.classList.add('badge-animate');
            });
        });

        // ========================================
        // [4] SMOOTH PARALLAX FOR HERO BANNER
        // ========================================
        document.addEventListener('mousemove', function(e) {
            const heroes = document.querySelectorAll('img[data-parallax]');
            if (heroes.length === 0) return;

            const xAxis = (window.innerWidth / 2 - e.pageX) / 50;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 50;

            heroes.forEach(hero => {
                hero.style.transform = `translateX(${xAxis}px) translateY(${yAxis}px) scale(1.1)`;
            });
        });

        // ========================================
        // [5] DYNAMIC GLOW EFFECT ON CARDS
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const gameCards = document.querySelectorAll('.gaming-card, .hover-card');

            gameCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s ease';
                    this.style.boxShadow = '0 0 30px rgba(124, 58, 237, 0.4), 0 10px 40px rgba(0, 0, 0, 0.3)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '';
                });
            });
        });

        // ========================================
        // [6] STAT COUNTER ANIMATION
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.counter[data-target]');

            const animateCounter = (counter) => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (!target) return;

                const duration = 2000; // 2 seconds
                const step = target / (duration / 16); // 60fps
                let current = 0;

                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 16);
            };

            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            counters.forEach(counter => {
                counterObserver.observe(counter);
            });
        });

        // ========================================
        // [7] SMOOTH BUTTON RIPPLE EFFECT
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button[class*="bg-purple"], button[class*="rounded"]');

            buttons.forEach(button => {
                button.classList.add('btn-ripple');

                button.addEventListener('click', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const ripple = document.createElement('span');
                    ripple.style.cssText = `
                    position: absolute;
                    left: ${x}px;
                    top: ${y}px;
                    width: 0;
                    height: 0;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    transform: translate(-50%, -50%);
                    animation: ripple-effect 0.6s ease-out;
                    pointer-events: none;
                `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Add ripple animation
            const style = document.createElement('style');
            style.textContent = `
            @keyframes ripple-effect {
                to {
                    width: 200px;
                    height: 200px;
                    opacity: 0;
                }
            }
        `;
            document.head.appendChild(style);
        });

        // ========================================
        // [8] SMOOTH SCROLL BEHAVIOR
        // ========================================
        document.documentElement.style.scrollBehavior = 'smooth';

        // ========================================
        // [9] ADD HOVER GLOW TO STAT CARDS
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('section.grid > div');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('div[class*="w-12"]');
                    if (icon) {
                        icon.style.transform = 'scale(1.15) rotate(5deg)';
                        icon.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('div[class*="w-12"]');
                    if (icon) {
                        icon.style.transform = 'scale(1) rotate(0deg)';
                    }
                });
            });
        });

        // ========================================
        // [10] PERFORMANCE OPTIMIZATION
        // ========================================
        // Reduce animations on low-end devices
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('*').forEach(el => {
                el.style.animation = 'none';
                el.style.transition = 'none';
            });
        }

        // ========================================
        // [11] KEYBOARD SHORTCUT: CTRL+K for Search
        // ========================================
        document.addEventListener('keydown', function(e) {
            // Ctrl+K or Cmd+K untuk focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }

            // ESC untuk close search results
            if (e.key === 'Escape') {
                const searchResults = document.getElementById('searchResults');
                if (searchResults) {
                    searchResults.classList.add('hidden');
                }
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.blur();
                }
            }
        });

        function scrollSlider(id, direction) {
            const slider = document.getElementById(id);
            if (!slider) return;
            // Scroll one visible width minus a bit so user sees context
            const scrollAmount = slider.clientWidth * 0.8;
            slider.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }

        // ========================================
        // [12] AJAX PAGINATION UNTUK SEMUA GAME
        // ========================================
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('#katalog a');
            if (paginationLink && paginationLink.href && paginationLink.href.includes('page=')) {
                e.preventDefault();
                const url = paginationLink.href;
                const katalogSection = document.getElementById('katalog');

                // Tambahkan efek loading (transparan)
                katalogSection.style.opacity = '0.5';
                katalogSection.style.pointerEvents = 'none';
                katalogSection.style.transition = 'opacity 0.3s ease';

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newKatalog = doc.getElementById('katalog');

                        if (newKatalog) {
                            katalogSection.innerHTML = newKatalog.innerHTML;
                        }

                        // Kembalikan ke tampilan normal
                        katalogSection.style.opacity = '1';
                        katalogSection.style.pointerEvents = 'auto';
                    })
                    .catch(error => {
                        console.error('Error fetching page:', error);
                        window.location.href = url; // Fallback jika gagal
                    });
            }
        });
        // ========================================
        // [BANTUAN PANEL] Toggle Panel Pusat Bantuan
        // ========================================
        let bantuanActive = false;

        function toggleBantuan(e) {
            e.preventDefault();
            bantuanActive ? closeBantuan() : openBantuan();
        }

        function openBantuan() {
            bantuanActive = true;
            const panel = document.getElementById('bantuanPanel');
            const beranda = document.getElementById('berandaContent');
            const navBantuan = document.getElementById('nav-bantuan');
            const sidebar = document.getElementById('mainSidebar');

            // Sembunyikan sidebar utama agar Bantuan jadi full halaman
            if (sidebar) {
                sidebar.style.display = 'none';
                sidebar.classList.remove('xl:flex');
            }

            // Slide beranda keluar ke kiri
            if (beranda) {
                beranda.classList.remove('page-enter'); // Hapus class animasi agar tidak memblokir opacity
                beranda.style.transform = 'translateX(-60px)';
                beranda.style.opacity = '0';
                beranda.style.pointerEvents = 'none';
                setTimeout(() => {
                    if (bantuanActive) beranda.style.visibility = 'hidden';
                }, 500);
            }

            // Slide bantuan masuk dari kanan
            if (panel) {
                panel.style.visibility = 'visible';
                panel.style.pointerEvents = 'auto';
                panel.style.transform = 'translateX(0)';
                panel.style.opacity = '1';
                panel.style.background = '#0A0C10'; // Tambahkan background agar tidak tembus pandang
            }

            // Update nav active state — hapus garis bawah dari SEMUA link nav lain
            ['nav-beranda', 'nav-kategori'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.classList.remove('text-purple-400', 'border-purple-500');
                    el.classList.add('text-gray-400', 'border-transparent');
                }
            });
            if (navBantuan) {
                navBantuan.classList.remove('text-gray-400', 'border-transparent');
                navBantuan.classList.add('text-purple-400', 'border-purple-500');
            }
        }

        function closeBantuan() {
            bantuanActive = false;
            const panel = document.getElementById('bantuanPanel');
            const beranda = document.getElementById('berandaContent');
            const navBantuan = document.getElementById('nav-bantuan');
            const sidebar = document.getElementById('mainSidebar');

            // Tampilkan kembali sidebar utama
            if (sidebar) {
                sidebar.style.display = '';
                sidebar.classList.add('xl:flex');
            }

            // Slide bantuan keluar ke kanan
            if (panel) {
                panel.style.transform = 'translateX(100%)';
                panel.style.opacity = '0';
                panel.style.pointerEvents = 'none';
                setTimeout(() => {
                    if (!bantuanActive) panel.style.visibility = 'hidden';
                }, 500);
            }

            // Slide beranda masuk
            if (beranda) {
                beranda.style.visibility = 'visible';
                beranda.style.transform = 'translateX(0)';
                beranda.style.opacity = '1';
                beranda.style.pointerEvents = 'auto';
            }

            // Update nav active state — hapus garis bawah dari Bantuan
            if (navBantuan) {
                navBantuan.classList.remove('text-purple-400', 'border-purple-500');
                navBantuan.classList.add('text-gray-400', 'border-transparent');
            }
            // Kembalikan garis bawah ke halaman yang sedang aktif
            var activeNavId = '{{ request()->is("kategori") ? "nav-kategori" : "nav-beranda" }}';
            var activeNav = document.getElementById(activeNavId);
            if (activeNav) {
                activeNav.classList.remove('text-gray-400', 'border-transparent');
                activeNav.classList.add('text-purple-400', 'border-purple-500');
            }
        }

        // Nav beranda juga pakai JS untuk balik dari bantuan
        document.getElementById('nav-beranda').addEventListener('click', function(e) {
            if (bantuanActive) {
                e.preventDefault();
                closeBantuan();
            }
        });

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
    </script>










    <script>
        // Initialize server data if available
        window.SERVER_CART = window.SERVER_CART || {
            !!isset($cartGameIds) ? json_encode($cartGameIds) : '[]'!!
        };
        window.SERVER_WISHLIST = window.SERVER_WISHLIST || {
            !!isset($wishlistGameIds) ? json_encode($wishlistGameIds) : '[]'!!
        };

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
            observer.observe(mainGameArea, {
                childList: true,
                subtree: true
            });
        }
    </script>

    <script>
        // FITUR: Tahan pengacakan game saat masuk ke detail
        document.addEventListener('click', function(e) {
            let link = e.target.closest('a[href*="/game/"], div[onclick*="/game/"], button[onclick*="/game/"], .hover-card');
            if (link) {
                document.cookie = "keep_seed=1; path=/; max-age=3600";
            }
        });

        // FITUR: Stay Position — Simpan & Kembalikan posisi scroll beranda
        (function() {
            var bc = document.getElementById('berandaContent');
            if (!bc) return;

            var navType = (performance.getEntriesByType('navigation')[0] || {}).type;

            if (navType === 'back_forward') {
                var saved = sessionStorage.getItem('berandaScrollPos');
                if (saved) {
                    var pos = parseInt(saved);
                    // Set langsung (HTML sudah selesai di-parse pada titik ini)
                    bc.scrollTop = pos;
                    // Set ulang setelah semua gambar selesai dimuat (div sudah tinggi penuh)
                    window.addEventListener('load', function() {
                        bc.scrollTop = pos;
                    });
                }
            }

            // Simpan posisi scroll setiap kali user scroll
            var t;
            bc.addEventListener('scroll', function() {
                clearTimeout(t);
                t = setTimeout(function() {
                    sessionStorage.setItem('berandaScrollPos', bc.scrollTop);
                }, 50);
            });

            // Backup untuk bfcache
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    var s = sessionStorage.getItem('berandaScrollPos');
                    if (s) bc.scrollTop = parseInt(s);
                }
            });
        })();
    </script>


@include('components.toast-notification')
@auth
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/refund/check-notif')
            .then(res => res.json())
            .then(data => {
                if(data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach((notif) => {
                        if (typeof showToast === 'function') {
                            showToast(notif.message, notif.type === 'error', function() {
                                fetch(`/refund/mark-notified/${notif.id}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                }).catch(err => console.error('Error marking notified:', err));
                            });
                        }
                    });
                }
            })
            .catch(err => console.error('Error fetching refund notifs:', err));
    });
</script>
@endauth
</body>

</html>