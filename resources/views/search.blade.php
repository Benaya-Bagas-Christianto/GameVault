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
    <title>Hasil Pencarian - GameVault</title>
    
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0A0C10 !important; color: #FFFFFF; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .card-bg { background-color: #12151C; border: 1px solid rgba(255, 255, 255, 0.05); }
        .hover-card:hover { border-color: rgba(124, 58, 237, 0.5); box-shadow: 0 0 20px rgba(124, 58, 237, 0.15); transform: translateY(-4px); }
    </style>

    
</head>
<body class="flex flex-col min-h-screen antialiased selection:bg-purple-500 selection:text-white">

    {{-- NAVBAR PREMIUM (Sama seperti Beranda) --}}
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

    {{-- KONTEN UTAMA --}}
    <main class="flex-1 p-6 lg:p-10 max-w-7xl mx-auto w-full">
        
        {{-- Header Hasil Pencarian --}}
        <div class="mb-10 border-b border-white/5 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                @if(request('price') == 'free' || request('sort') == 'popular' || request('sort') == 'trending')
                <a href="/" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-purple-400 transition-colors mb-4 group font-medium">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Beranda
                </a>
                @endif
                <h1 class="text-3xl font-black text-white tracking-widest uppercase mb-2">
                    @if(request('price') == 'free')
                        Game Gratis
                    @elseif(request('sort') == 'popular')
                        Game Populer
                    @elseif(request('sort') == 'trending')
                        Game Trending
                    @elseif(request('genre'))
                        Kategori: {{ ucfirst(request('genre')) }}
                    @elseif(request('platform'))
                        Platform: {{ strtoupper(request('platform')) }}
                    @else
                        Hasil Pencarian
                    @endif
                </h1>
                @if(request('q'))
                <p class="text-gray-400 text-sm">Menampilkan hasil untuk kata kunci: <span class="text-white font-bold text-base">"{{ request('q') }}"</span></p>
                @endif
            </div>
            <div class="bg-[#12151C] border border-white/10 px-4 py-2 rounded-xl flex items-center gap-2 shadow-lg">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-sm font-bold text-gray-300">Ditemukan <span class="text-[#a78bfa]">{{ count($games) }}</span> Game</span>
            </div>
        </div>

        {{-- Grid Game --}}
        @if(count($games) > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($games as $game)
                <div class="card-bg rounded-2xl overflow-hidden hover-card cursor-pointer flex flex-col transition-all duration-300 group" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}">
                    
                    {{-- Cover Gambar & Harga --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-black border-b border-white/5">
                        {{-- Harga di Pojok Kanan Atas --}}
                        <div class="absolute top-3 right-3 z-10 bg-black/80 backdrop-blur-sm border border-white/10 text-white text-[10px] font-black tracking-widest px-2.5 py-1.5 rounded-lg shadow-lg">
                            {{ $game->price == 0 ? 'Gratis' : 'Rp ' . number_format($game->price, 0, ',', '.') }}
                        </div>
                        <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='{{ asset('assets/no-image.jpg') }}'" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                    </div>
                    
                    {{-- Detail Info Game --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-white text-base leading-tight mb-4 line-clamp-2 group-hover:text-[#a78bfa] transition-colors">{{ $game->name }}</h3>
                        
                        <div class="flex flex-wrap items-center gap-2 mt-auto">
                            <?php 
                            $avg_rating = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0;
                            $total_reviews = $game->reviews->count();
                            ?>
                            @if($avg_rating > 0)
                            <div class="flex items-center gap-1 mr-2">
                                <span class="text-yellow-500 text-[10px]">★</span>
                                <span class="text-[11px] font-bold text-white">{{ $avg_rating }}</span>
                                <span class="text-[9px] text-gray-500">({{ $total_reviews }})</span>
                            </div>
                            @endif

                            {{-- Badge Platform (Hitam) --}}
                            <span class="text-[9px] font-bold text-gray-400 border border-white/10 px-2 py-1 rounded bg-[#0A0C10] uppercase tracking-wider">
                                {{ $game->platform ?? 'PC' }}
                            </span>
                            
                            {{-- Badge Genre (Ungu) --}}
                            @if($game->genre)
                            <span class="text-[9px] font-bold text-[#a78bfa] border border-[#7C3AED]/30 px-2 py-1 rounded bg-[#7C3AED]/10 uppercase tracking-wider line-clamp-1">
                                {{ explode(',', $game->genre)[0] }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            {{-- Paginasi (Jika ada) --}}
            @if(method_exists($games, 'links'))
            <div class="mt-12 flex justify-center">
                <div class="bg-[#12151C] p-2 rounded-xl border border-white/5 shadow-lg w-full max-w-2xl overflow-x-auto hide-scrollbar">
                    {{ $games->links() }}
                </div>
            </div>
            @endif

        @else
            {{-- Tampilan Jika Game Tidak Ditemukan --}}
            <div class="flex flex-col items-center justify-center py-24 text-center border border-dashed border-white/10 rounded-2xl bg-[#12151C]">
                <span class="text-6xl mb-4 opacity-30">🔍</span>
                <h3 class="text-xl font-bold text-gray-300 mb-1">Game Tidak Ditemukan</h3>
                <p class="text-gray-500 text-sm max-w-sm mb-6">Kami tidak menemukan game yang cocok dengan kata kunci <span class="text-white font-bold">"{{ request('q') }}"</span>. Coba gunakan kata kunci lain.</p>
                <a href="/" class="px-6 py-3 bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-[0_0_20px_rgba(124,58,237,0.2)]">
                    Kembali ke Beranda
                </a>
            </div>
        @endif

    </main>

    {{-- FOOTER --}}
    <footer class="h-16 border-t border-white/5 flex items-center justify-center text-xs text-gray-600 bg-[#0A0C10]">
        &copy; 2026 GameVault. Semua hak dilindungi.
    </footer>


    








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
</script>

</body>
</html>
