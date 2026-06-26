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
    {{-- TRIK ANTI ERROR PATH URL --}}
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="asset-url" content="{{ asset('assets') }}">
    
    <title>{{ $game->name }} - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    <style>
        body, html, main, section, div, aside, header { background-image: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #0A0C10 !important; color: #FFFFFF !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .progress-bar-bg { background-color: #1A1D24 !important; border-radius: 999px; overflow: hidden; height: 8px; }
        .progress-bar-fill { background-color: #7C3AED !important; height: 100%; border-radius: 999px; }
    </style>

    
</head>

<body class="flex h-screen overflow-hidden antialiased selection:bg-purple-500 selection:text-white" style="background-color: #0A0C10 !important;">

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

        <main class="flex-1 overflow-y-auto hide-scrollbar p-6 lg:p-10" style="background-color: #0A0C10 !important;">
            @php
                $avg_rating = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : '0';
                $total_reviews = $game->reviews->count();
                $review_k = $total_reviews > 1000 ? round($total_reviews/1000, 1) . 'K' : $total_reviews;
                        $b5 = $total_reviews > 0 ? round(($game->reviews->where('rating', 5)->count() / $total_reviews) * 100) : 0;
                $b4 = $total_reviews > 0 ? round(($game->reviews->where('rating', 4)->count() / $total_reviews) * 100) : 0;
                $b3 = $total_reviews > 0 ? round(($game->reviews->where('rating', 3)->count() / $total_reviews) * 100) : 0;
                $b2 = $total_reviews > 0 ? round(($game->reviews->where('rating', 2)->count() / $total_reviews) * 100) : 0;
                $b1 = $total_reviews > 0 ? round(($game->reviews->where('rating', 1)->count() / $total_reviews) * 100) : 0;

                $isOwned = false;
                if(Auth::check()) {
                    $isOwned = \Illuminate\Support\Facades\DB::table('tb_detail_transaksi')
                        ->join('tb_transaksi', 'tb_detail_transaksi.transaksi_id', '=', 'tb_transaksi.id')
                        ->where('tb_transaksi.user_id', Auth::id())
                        ->where('tb_transaksi.status', 'Success')
                        ->where('tb_detail_transaksi.game_id', $game->id)
                        ->exists();
                }
            @endphp

            <div class="max-w-7xl mx-auto">
                <a href="javascript:history.back()" class="text-gray-500 hover:text-white transition-colors text-sm flex items-center gap-2 mb-6 w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>

                <div class="relative w-full rounded-2xl overflow-hidden mb-8 border border-white/5" style="background-color: #000000 !important; box-shadow: 0 10px 40px rgba(0,0,0,0.8);">
                    <div class="absolute inset-0 z-0 pointer-events-none">
                        <img id="heroBackgroundImage" src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="w-full h-full object-cover opacity-30 transform scale-105" style="transition: src 0.3s ease;">
                    </div>
                    <div class="absolute inset-0 z-0 pointer-events-none" style="background: linear-gradient(to right, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 60%, transparent 100%) !important;"></div>
                    <div class="absolute inset-0 z-0 pointer-events-none" style="background: linear-gradient(to top, rgba(0,0,0,1) 0%, transparent 100%) !important;"></div>
                                <div class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row gap-8 lg:gap-12 items-center md:items-stretch">
                        <div class="flex-shrink-0 w-64 md:w-72 rounded-xl overflow-hidden border border-white/10 relative" style="box-shadow: 0 0 30px rgba(0,0,0,0.9);">
                            <img id="mainCoverImage" src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="w-full aspect-[3/4] object-cover" style="transition: src 0.3s ease;">
                        </div>
                                        <div class="flex-1 flex flex-col justify-center w-full">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black !text-white mb-4" style="text-shadow: 2px 2px 10px rgba(0,0,0,0.9);">{{ $game->name }}</h1>
                            <div class="flex flex-wrap items-center gap-3 mb-5">
                                @foreach(explode(',', $game->genre) as $tag)
                                    <span class="text-gray-300 text-xs font-medium px-3 py-1.5 rounded-lg border border-white/10" style="background-color: #1A1D24 !important;">{{ trim($tag) }}</span>
                                @endforeach
                                <span class="text-gray-300 text-xs font-medium px-3 py-1.5 rounded-lg border border-white/10" style="background-color: #1A1D24 !important;">{{ $game->platform ?? 'PC' }}</span>
                                @if($game->console_edition)
                                    @foreach(explode(',', $game->console_edition) as $ce)
                                        <span class="text-pink-400 text-xs font-medium px-3 py-1.5 rounded-lg border border-pink-500/20 bg-pink-500/10">{{ trim($ce) }}</span>
                                    @endforeach
                                @endif
                            </div>

                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-yellow-500 text-lg">★</span>
                                    <span class="text-lg font-bold !text-white">{{ $avg_rating }}</span>
                                </div>
                                <div class="w-px h-5 bg-white/20"></div>
                                <p class="text-sm text-gray-400">{{ $review_k }} ulasan</p>
                            </div>

                            <p class="text-gray-300 text-sm md:text-base leading-relaxed mb-8 max-w-3xl" style="text-shadow: 1px 1px 5px rgba(0,0,0,0.9);">
                                {{ $game->synopsis ?: 'Jelajahi dunia tanpa batas dan raih kemenanganmu. Mainkan sekarang juga dan temukan rahasia yang tersembunyi.' }}
                            </p>

                            <div class="flex flex-wrap items-center gap-4 mt-auto">
                                @if($isOwned)
                                    <div class="flex flex-col gap-3 w-full md:w-auto">
                                        <div class="flex items-center gap-2 bg-green-500/10 text-green-400 border border-green-500/30 px-5 py-3 rounded-xl font-bold text-sm shadow-[0_0_15px_rgba(34,197,94,0.1)] backdrop-blur-md">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Game ini sudah ada di Library Anda
                                        </div>
                                        <button onclick="window.location.href='/library'" class="w-full text-center text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg border border-white/20 hover:bg-white/10 hover:border-purple-500 hover:text-purple-300" style="background-color: #1A1D24 !important;">
                                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Lihat / Buka di Library
                                        </button>
                                    </div>
                                @else
                                    <p class="text-4xl font-black w-full md:w-auto mb-4 md:mb-0" style="color: #FDE047 !important; text-shadow: 0 0 15px rgba(253, 224, 71, 0.2);">
                                        {{ $game->price == 0 ? "Gratis" : "Rp " . number_format($game->price, 0, ',', '.') }}
                                    </p>
                                                                <button onclick="tambahKeranjangCerdas('{{ $game->id }}', true, this)" class="flex-1 md:flex-none text-center text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg hover:scale-105" style="background-color: #7C3AED !important; box-shadow: 0 0 20px rgba(124,58,237,0.4);">
                                        Beli Sekarang
                                    </button>

                                    @php
                                        $isInCartDetail = Auth::check() ? \App\Models\Keranjang::where('user_id', Auth::id())->where('game_id', $game->id)->exists() : false;
                                    @endphp
                                    <button id="btnCartDetail" onclick="toggleCartDetail('{{ $game->id }}', this)" class="flex items-center justify-center w-14 h-14 border rounded-xl transition-all group hover:scale-105 {{ $isInCartDetail ? 'bg-[#7C3AED] border-[#7C3AED] text-white' : 'bg-[#1A1D24] border-white/20 hover:border-[#7C3AED] hover:text-[#8B5CF6] text-white' }}" title="{{ $isInCartDetail ? 'Hapus dari Keranjang' : 'Tambah ke Keranjang' }}">
                                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 3H4.5L6.5 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM6.07142 14H18L21 5H4.78571M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"></path></svg>
                                    </button>

                                    @php
                                        $isInWishlistDetail = Auth::check() ? \App\Models\Wishlist::where('user_id', Auth::id())->where('game_id', $game->id)->exists() : false;
                                    @endphp
                                    <button id="btnWishlistDetail" onclick="addToWishlist('{{ $game->id }}')" class="flex items-center justify-center w-14 h-14 border rounded-xl transition-all group hover:scale-105 {{ $isInWishlistDetail ? 'bg-[#EF4444] border-[#EF4444] text-white' : 'bg-[#1A1D24] border-white/20 hover:border-[#EF4444] hover:text-[#EF4444] text-white' }}" title="{{ $isInWishlistDetail ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}">
                                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="none" viewBox="3 3 18 18" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" fill="currentColor" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($game->galleries && $game->galleries->where('type', 'image')->count() > 0)
                <div class="relative mb-10 group">
                    <button onclick="scrollGallery('left')" class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/70 hover:bg-[#7C3AED] text-white rounded-full flex items-center justify-center z-10 opacity-80 hover:opacity-100 transition-all duration-300 shadow-[0_0_15px_rgba(0,0,0,0.8)] border border-white/20" style="transform: translateY(-70%);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <div id="galleryContainer" class="flex gap-4 overflow-x-auto hide-scrollbar snap-x pb-4 scroll-smooth">
                        @foreach($game->galleries->where('type', 'image') as $img)
                        <div onclick="openLightbox('{{ asset("assets/galleries/" . $img->path) }}')" class="gallery-thumb snap-start flex-shrink-0 relative rounded-xl overflow-hidden cursor-pointer border border-white/10 transition-all hover:scale-105 hover:border-white/30 hover:opacity-100 shadow-md">
                            <img src="{{ asset('assets/galleries/' . $img->path) }}" class="h-36 md:h-48 w-auto object-cover opacity-80 hover:opacity-100 transition-opacity">
                        </div>
                        @endforeach
                    </div>

                    <button onclick="scrollGallery('right')" class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/70 hover:bg-[#7C3AED] text-white rounded-full flex items-center justify-center z-10 opacity-80 hover:opacity-100 transition-all duration-300 shadow-[0_0_15px_rgba(0,0,0,0.8)] border border-white/20" style="transform: translateY(-70%);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                @endif

                <div class="border-b border-white/10 mb-8 flex gap-8 overflow-x-auto hide-scrollbar">
                    <button id="tabInformasi" onclick="switchTab('informasi')" class="pb-4 font-bold whitespace-nowrap px-2 border-b-2" style="color: #8B5CF6 !important; border-color: #8B5CF6 !important;">Informasi</button>
                    <button id="tabUlasan" onclick="switchTab('ulasan')" class="text-gray-400 hover:text-white pb-4 font-medium whitespace-nowrap px-2 border-b-2 border-transparent transition-colors">Ulasan ({{ $total_reviews }})</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                    <div class="lg:col-span-2">
                        <div id="contentInformasi" class="space-y-8 block">
                            <div class="border border-white/5 p-8 rounded-2xl" style="background-color: #12151C !important;">
                                <h2 class="text-xl font-bold !text-white mb-6">Tentang Game</h2>
                                <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">
                                    {!! isset($game->description) ? (str_contains($game->description, '<') ? $game->description : nl2br(e($game->description))) : 'Tidak ada deskripsi rinci untuk game ini.' !!}
                                </div>
                            </div>

                            @php
                                $videoTrailer = $game->galleries ? $game->galleries->where('type', 'video')->first() : null;
                            @endphp
                            @if($videoTrailer)
                                @php
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $videoTrailer->path, $match);
                                    $youtubeId = $match[1] ?? '';
                                    
                                    $ytQuery = '';
                                    if ($youtubeId) {
                                        parse_str(parse_url($videoTrailer->path, PHP_URL_QUERY) ?? '', $query);
                                        $ytParams = [];
                                        if (!empty($query['start'])) $ytParams[] = 'start=' . $query['start'];
                                        if (!empty($query['end'])) $ytParams[] = 'end=' . $query['end'];
                                        if (count($ytParams) > 0) {
                                            $ytQuery = '?' . implode('&', $ytParams);
                                        }
                                    }
                                    
                                    $isLocal = !$youtubeId && !str_contains($videoTrailer->path, 'youtube.com') && !str_contains($videoTrailer->path, 'youtu.be');
                                @endphp
                                @if($youtubeId || $isLocal)
                                <div class="border border-white/5 p-8 rounded-2xl" style="background-color: #12151C !important;">
                                    <h2 class="text-xl font-bold !text-white mb-6">Video Trailer</h2>
                                    <div class="relative w-full rounded-xl overflow-hidden bg-black" style="padding-top: 56.25%;">
                                        @if($youtubeId)
                                        <iframe class="absolute top-0 left-0 w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}{{ $ytQuery }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        @else
                        @php
                            $cleanTrailerPath = preg_replace('/#.*$/', '', $videoTrailer->path);
                            $trailerHash = str_contains($videoTrailer->path, '#') ? '#' . explode('#', $videoTrailer->path)[1] : '';
                        @endphp
                        <video src="{{ url('/stream-media?path=assets/galleries/' . $cleanTrailerPath) }}{{ $trailerHash }}" controls class="absolute top-0 left-0 w-full h-full object-contain"></video>
                    @endif
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>

                        <div id="contentUlasan" class="space-y-8 hidden">
                            <div class="border border-white/5 p-8 rounded-2xl" style="background-color: #12151C !important;">
                            <h2 class="text-xl font-bold !text-white mb-6">Ulasan Pengguna</h2>
                            <div class="flex flex-col md:flex-row gap-8 mb-8 border-b border-white/5 pb-8">
                                <div class="flex flex-col justify-center items-center md:items-start text-center md:text-left min-w-[120px]">
                                    <h3 class="text-6xl font-black !text-white mb-2">{{ $avg_rating }}</h3>
                                    <div class="flex text-lg mb-2">
                                        @for($i=1; $i<=5; $i++)
                                            @if($i <= round($avg_rating)) <span class="text-yellow-500">★</span>
                                            @else <span class="text-gray-600">★</span> @endif
                                        @endfor
                                    </div>
                                    <p class="text-xs text-gray-500">Berdasarkan {{ $review_k }} ulasan</p>
                                </div>
                                <div class="flex-1 space-y-2.5">
                                    <div class="flex items-center gap-3"><span class="text-xs text-gray-400 w-4">5★</span><div class="flex-1 progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $b5 }}%"></div></div><span class="text-xs text-gray-500 w-8 text-right">{{ $b5 }}%</span></div>
                                    <div class="flex items-center gap-3"><span class="text-xs text-gray-400 w-4">4★</span><div class="flex-1 progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $b4 }}%"></div></div><span class="text-xs text-gray-500 w-8 text-right">{{ $b4 }}%</span></div>
                                    <div class="flex items-center gap-3"><span class="text-xs text-gray-400 w-4">3★</span><div class="flex-1 progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $b3 }}%"></div></div><span class="text-xs text-gray-500 w-8 text-right">{{ $b3 }}%</span></div>
                                    <div class="flex items-center gap-3"><span class="text-xs text-gray-400 w-4">2★</span><div class="flex-1 progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $b2 }}%"></div></div><span class="text-xs text-gray-500 w-8 text-right">{{ $b2 }}%</span></div>
                                    <div class="flex items-center gap-3"><span class="text-xs text-gray-400 w-4">1★</span><div class="flex-1 progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $b1 }}%"></div></div><span class="text-xs text-gray-500 w-8 text-right">{{ $b1 }}%</span></div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @if($game->reviews->count() > 0)
                                    @foreach($game->reviews as $rev)
                                    <div class="p-5 rounded-xl border border-white/5" style="background-color: #1A1D24 !important;">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                @if($rev->user && $rev->user->foto)
                                                    <img src="{{ asset('assets/profile/' . $rev->user->foto) }}" class="w-10 h-10 rounded-full object-cover border border-purple-500/30 shadow-sm" alt="Avatar">
                                                @else
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm" style="background: linear-gradient(135deg, #7C3AED 0%, #4C1D95 100%); border: 1px solid rgba(255,255,255,0.1);">{{ strtoupper(substr($rev->user->username ?? 'U', 0, 1)) }}</div>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-bold !text-white">{{ $rev->user->username ?? 'Anonim' }}</p>
                                                    <p class="text-[10px] text-gray-500">{{ $rev->created_at ? $rev->created_at->format('d M Y') : 'Baru saja' }}</p>
                                                </div>
                                            </div>
                                            <div class="text-sm flex">
                                                @for($i=0; $i<$rev->rating; $i++) <span class="text-yellow-500">★</span> @endfor
                                                @for($i=$rev->rating; $i<5; $i++) <span class="text-gray-600">★</span> @endfor
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-300 leading-relaxed mb-4">{{ $rev->komentar }}</p>
                                                                        @if($rev->media)
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @php
                                                    $mediaItems = explode('|', $rev->media);
                                                @endphp
                                                @foreach($mediaItems as $mediaItem)
                                                    <div class="w-max max-w-sm rounded-xl overflow-hidden border border-white/10">
                                                        @php
                                                            $cleanPath = preg_replace('/#.*$/', '', trim($mediaItem));
                                                            $hashPart = str_contains($mediaItem, '#') ? '#' . explode('#', $mediaItem)[1] : '';
                                                        @endphp
                                                        @if(\Illuminate\Support\Str::endsWith(strtolower($cleanPath), ['.mp4', '.webm', '.ogg', '.mov']))
                                                            <video src="{{ url('/stream-media?path=' . ltrim($cleanPath, '/')) }}{{ $hashPart }}" controls class="w-full max-h-64 object-contain bg-black"></video>
                                                        @else
                                                            <img src="{{ asset(trim($mediaItem)) }}" class="w-full max-h-64 object-cover cursor-pointer hover:opacity-90 transition-opacity" onclick="openLightbox(this.src)">
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <div class="p-6 rounded-xl border border-white/5 text-center" style="background-color: #1A1D24 !important;">
                                        <span class="text-4xl mb-3 opacity-30 block">💬</span>
                                        <p class="text-gray-400 text-sm font-medium mb-1">Belum ada ulasan untuk game ini.</p>
                                        <p class="text-gray-500 text-xs">Jadilah yang pertama memberikan rating dan ulasan setelah membelinya!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="border border-white/5 p-6 rounded-2xl text-sm" style="background-color: #12151C !important;">
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-2 border-b border-white/5 pb-3">
                                    <span class="text-gray-500 col-span-1">Developer</span>
                                    <span class="!text-white font-medium col-span-2 text-right">{{ $game->developer ?: 'N/A' }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 border-b border-white/5 pb-3">
                                    <span class="text-gray-500 col-span-1">Publisher</span>
                                    <span class="!text-white font-medium col-span-2 text-right">{{ $game->publisher ?: 'N/A' }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 border-b border-white/5 pb-3">
                                    <span class="text-gray-500 col-span-1">Rilis</span>
                                    <span class="!text-white font-medium col-span-2 text-right">{{ $game->release_date ? \Carbon\Carbon::parse($game->release_date)->format('d F Y') : 'TBA' }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 border-b border-white/5 pb-3">
                                    <span class="text-gray-500 col-span-1">Genre</span>
                                    <span class="!text-white font-medium col-span-2 text-right">{{ $game->genre }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 border-b border-white/5 pb-3">
                                    <span class="text-gray-500 col-span-1">Platform</span>
                                    <span class="!text-white font-medium col-span-2 text-right">{{ $game->platform }}
                                        @if($game->console_edition)
                                        <span class="text-pink-400">({{ $game->console_edition }})</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="border border-white/5 p-6 rounded-2xl text-sm border-t-2" style="background-color: #12151C !important; border-top-color: #7C3AED !important;">
                            <h3 class="font-bold !text-white mb-5 text-base">Persyaratan Sistem</h3>
                            <div class="mb-6">
                                <p class="font-bold mb-3 text-xs uppercase tracking-widest" style="color: #8B5CF6 !important;">Minimum</p>
                                <div class="text-gray-300 text-xs leading-relaxed space-y-2 whitespace-pre-line border-l-2 border-white/10 pl-3">
                                    {!! isset($game->sys_req_min) && !empty(trim($game->sys_req_min)) ? (str_contains($game->sys_req_min, '<') ? $game->sys_req_min : nl2br(e($game->sys_req_min))) : "OS: Windows 10 64-bit\nProcessor: Intel Core i5\nMemory: 8 GB RAM\nGraphics: GTX 1060" !!}
                                </div>
                            </div>
                            <div>
                                <p class="font-bold mb-3 text-xs uppercase tracking-widest" style="color: #8B5CF6 !important;">Recommended</p>
                                <div class="text-gray-300 text-xs leading-relaxed space-y-2 whitespace-pre-line border-l-2 border-white/10 pl-3">
                                    {!! isset($game->sys_req_rec) && !empty(trim($game->sys_req_rec)) ? (str_contains($game->sys_req_rec, '<') ? $game->sys_req_rec : nl2br(e($game->sys_req_rec))) : "OS: Windows 11 64-bit\nProcessor: Intel Core i7\nMemory: 16 GB RAM\nGraphics: RTX 3060" !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    </div>
                <div class="px-2 lg:px-6">
            @include('footer')
        </div>
</main>


    </div>
    
    {{-- FIX: GANTI closeCart JADI tutupKeranjangBaru() --}}
    <div id="cartOverlay" onclick="tutupKeranjangBaru()" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[190] hidden opacity-0 transition-opacity duration-300"></div>

    {{-- MODAL LOGIN --}}
    <div id="loginModal" class="hidden fixed inset-0 z-[250] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity duration-300">
        <div class="bg-[#12151C] p-8 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.1)] w-full max-w-md relative">
            <button onclick="document.getElementById('loginModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="text-2xl font-black text-white text-center mb-2 tracking-wide uppercase">AKSES DITOLAK</h2>
            <p class="text-gray-400 text-center text-sm mb-6">Kamu harus login terlebih dahulu.</p>
            <a href="{{ url('/login') }}" class="block w-full text-center text-white font-bold py-3 rounded-xl transition-all hover:brightness-110 shadow-[0_0_20px_rgba(124,58,237,0.3)]" style="background-color: #7C3AED !important;">LOGIN SEKARANG</a>
        </div>
    </div>

    {{-- MODAL LIGHTBOX GALLERY --}}
    <div id="lightboxModal" class="hidden fixed inset-0 z-[300] flex items-center justify-center bg-black/95 backdrop-blur-sm transition-opacity duration-300" onclick="closeLightbox()">
        <button class="absolute top-6 right-6 text-white/50 hover:text-white bg-white/10 hover:bg-red-500 rounded-full p-2 transition-colors z-50">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <img id="lightboxImage" src="" class="max-w-[95vw] max-h-[90vh] object-contain rounded-lg shadow-[0_0_50px_rgba(0,0,0,0.8)]" onclick="event.stopPropagation()">
    </div>

    {{-- Modal Konfirmasi Sukses (Menggantikan Toast) --}}
    @include('components.success-modal')

    <script>
        function switchTab(tabId) {
            const btnInfo = document.getElementById('tabInformasi');
            const btnUlasan = document.getElementById('tabUlasan');
            const contentInfo = document.getElementById('contentInformasi');
            const contentUlasan = document.getElementById('contentUlasan');

            if (tabId === 'informasi') {
                btnInfo.className = 'pb-4 font-bold whitespace-nowrap px-2 border-b-2';
                btnInfo.style.color = '#8B5CF6';
                btnInfo.style.borderColor = '#8B5CF6';
                        btnUlasan.className = 'text-gray-400 hover:text-white pb-4 font-medium whitespace-nowrap px-2 border-b-2 border-transparent transition-colors';
                btnUlasan.style.color = '';
                btnUlasan.style.borderColor = 'transparent';

                contentInfo.classList.remove('hidden');
                contentUlasan.classList.add('hidden');
            } else {
                btnUlasan.className = 'pb-4 font-bold whitespace-nowrap px-2 border-b-2';
                btnUlasan.style.color = '#8B5CF6';
                btnUlasan.style.borderColor = '#8B5CF6';
                        btnInfo.className = 'text-gray-400 hover:text-white pb-4 font-medium whitespace-nowrap px-2 border-b-2 border-transparent transition-colors';
                btnInfo.style.color = '';
                btnInfo.style.borderColor = 'transparent';

                contentUlasan.classList.remove('hidden');
                contentInfo.classList.add('hidden');
            }
        }
function openLightbox(src) {
            document.getElementById('lightboxImage').src = src;
            document.getElementById('lightboxModal').classList.remove('hidden');
        }

        function closeLightbox() {
            document.getElementById('lightboxModal').classList.add('hidden');
            document.getElementById('lightboxImage').src = '';
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeLightbox();
            }
        });

        function scrollGallery(direction) {
            const container = document.getElementById('galleryContainer');
            if (container) {
                const scrollAmount = 350; // Jarak scroll
                if (direction === 'left') {
                    container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                } else {
                    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            }
        }

        window.addToWishlist = function(gameId) {
            let isLoggedIn = @json(Auth::check());
            if (!isLoggedIn) {
                document.getElementById('loginModal').classList.remove('hidden');
                return;
            }
                fetch('/wishlist_process', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ game_id: gameId })
            })
            .then(res => res.json())
            .then(data => {
                let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                wishlist = wishlist.map(id => String(id));
                gameId = String(gameId);

                let btn = document.getElementById('btnWishlistDetail');
                if (data.status === 'added') {
                    if (!wishlist.includes(gameId)) wishlist.push(gameId);
                    showToast('Berhasil ditambahkan ke Wishlist! ❤️');
                    if (btn) {
                        btn.classList.add('bg-[#EF4444]', 'border-[#EF4444]', 'text-white');
                        btn.classList.remove('bg-[#1A1D24]', 'border-white/20', 'hover:text-[#EF4444]');
                        btn.setAttribute('title', 'Hapus dari Wishlist');
                    }
                } else if (data.status === 'removed') {
                    wishlist = wishlist.filter(id => id != gameId && id !== String(gameId));
                    showToast('Game dihapus dari Wishlist.');
                    if (btn) {
                        btn.classList.remove('bg-[#EF4444]', 'border-[#EF4444]', 'text-white');
                        btn.classList.add('bg-[#1A1D24]', 'border-white/20', 'hover:text-[#EF4444]');
                        btn.setAttribute('title', 'Tambah ke Wishlist');
                    }
                }
                        localStorage.setItem('wishlist', JSON.stringify(wishlist));
                        // NEW: Trigger syncGameCardLabels to update 'WISHLIST' label immediately if present
                if (typeof window.syncGameCardLabels === 'function') {
                    window.syncGameCardLabels();
                }
                        // Update Badge if exists
                if (typeof syncWishlistBadge === 'function') {
                    syncWishlistBadge();
                } else {
                    let badgeInit = document.getElementById('globalWishlistBadge');
                    if (badgeInit) {
                        badgeInit.innerText = wishlist.length;
                        if (wishlist.length > 0) {
                            badgeInit.classList.remove('hidden');
                            badgeInit.classList.add('flex');
                        } else {
                            badgeInit.classList.add('hidden');
                            badgeInit.classList.remove('flex');
                        }
                    }
                }
            })
            .catch(() => {
                showToast('Terjadi kesalahan jaringan!');
            });
        }

        function hideToast() {
            closeSuccessModal();
        }

        window.toggleCartDetail = function(gameId, btnElement) {
            let isLoggedIn = @json(Auth::check());
            if (!isLoggedIn) {
                document.getElementById('loginModal').classList.remove('hidden');
                return;
            }

            const isAdded = btnElement.classList.contains('bg-[#7C3AED]');
            const url = isAdded ? '/cart/remove' : '/cart_process';
                const originalContent = btnElement.innerHTML;
            btnElement.innerHTML = `<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            btnElement.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ game_id: gameId })
            })
            .then(res => res.json())
            .then(data => {
                btnElement.innerHTML = originalContent;
                btnElement.disabled = false;
                        if (data.status === 'success') {
                    let newCount = data.cart_count;
                    if (isAdded) {
                        showToast('Berhasil dihapus dari keranjang.');
                        btnElement.classList.remove('bg-[#7C3AED]', 'border-[#7C3AED]', 'text-white');
                        btnElement.classList.add('bg-[#1A1D24]', 'border-white/20', 'hover:text-[#8B5CF6]', 'text-white');
                        btnElement.setAttribute('title', 'Tambah ke Keranjang');
                    } else {
                        showToast('Game berhasil masuk ke keranjangmu!');
                        btnElement.classList.add('bg-[#7C3AED]', 'border-[#7C3AED]', 'text-white');
                        btnElement.classList.remove('bg-[#1A1D24]', 'border-white/20', 'hover:text-[#8B5CF6]');
                        btnElement.setAttribute('title', 'Hapus dari Keranjang');
                    }
                    if (newCount !== undefined) {
                        localStorage.setItem('cartCount', newCount);
                        let badge = document.getElementById('globalCartBadge');
                        if (badge) {
                            badge.innerText = newCount;
                            badge.style.setProperty('display', newCount > 0 ? 'flex' : 'none', 'important');
                        }
                    }

                    // NEW: Sync cart_cache
                    let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
                    if (isAdded) { // Berarti fungsi ini aslinya menghapus
                        cc = cc.filter(id => id !== String(gameId));
                    } else { // Berarti fungsi ini menambahkan
                        if (!cc.includes(String(gameId))) cc.push(String(gameId));
                    }
                    localStorage.setItem('cart_cache', JSON.stringify(cc));

                    if (typeof window.syncGameCardLabels === 'function') {
                        window.syncGameCardLabels();
                    }
                } else {
                    showToast(data.message || 'Gagal memproses keranjang');
                }
            })
            .catch(() => {
                btnElement.innerHTML = originalContent;
                btnElement.disabled = false;
                showToast('Terjadi kesalahan jaringan!');
            });
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
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
                body: JSON.stringify({ game_id: gameId })
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
            let isLoggedIn = @json(Auth::check());
            if(!isLoggedIn) return;
                fetch('/cart/get', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                let cartCount = data.cart_count !== undefined ? data.cart_count : (Array.isArray(data.data) ? data.data.length : 0);
                localStorage.setItem('cartCount', cartCount);
                let badge = document.getElementById('globalCartBadge');
                if(badge) {
                    badge.innerText = cartCount;
                    badge.style.setProperty('display', cartCount > 0 ? 'flex' : 'none', 'important');
                }
            }).catch(e => console.log(e));
        }
        document.addEventListener('DOMContentLoaded', function() {
            let badge = document.getElementById('globalCartBadge');
            let cachedCount = localStorage.getItem('cartCount');
            if(badge && cachedCount !== null) {
                badge.innerText = cachedCount;
                badge.style.setProperty('display', parseInt(cachedCount) > 0 ? 'flex' : 'none', 'important');
            }
            updateGlobalCartBadge();
        });
    </script>
    <script>
        // MESIN LIVE SEARCH / AUTOCOMPLETE ALA STEAM
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            let timeout = null;

            if (searchInput && searchResults) {
                searchInput.addEventListener('input', function() {
                    // Bersihkan timer (Debounce) agar server tidak ngelag saat ngetik cepat
                    clearTimeout(timeout);
                    const query = this.value.trim();

                    // Sembunyikan kalau ketikan kurang dari 2 huruf
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
                                searchResults.innerHTML = ''; // Kosongkan loading
                                                        if (data.length > 0) {
                                    // Looping data game dari database
                                    data.forEach(game => {
                                        // Cek Harga
                                        const priceText = game.price == 0 ? 'Gratis' : 'Rp ' + new Intl.NumberFormat('id-ID').format(game.price);
                                                                        // Desain HTML Item ala Steam
                                        const item = document.createElement('a');
                                        item.href = `/game/${game.id}`;
                                        item.className = 'flex items-center gap-3 p-3 hover:bg-[#2A2E37] border-b border-white/5 transition-colors cursor-pointer';
                                                                        item.innerHTML = `
                                            <img src="/assets/${game.image}" onerror="this.src='/assets/no-image.jpg'" class="w-[80px] h-[45px] object-cover rounded border border-white/10 shadow-sm flex-shrink-0">
                                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                                <h4 class="text-white text-sm font-bold truncate mb-0.5">${game.name}</h4>
                                                <p class="text-[#a78bfa] text-xs font-bold">${priceText}</p>
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

        // FITUR: Tahan pengacakan game saat kembali dari halaman detail
        document.cookie = "keep_seed=1; path=/; max-age=3600"; // Valid selama 1 jam
    </script>
@include('components.toast-notification')
</body>
</html>