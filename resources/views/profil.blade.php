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
    
    <title>Profil Saya - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050505 !important;
            color: #FFFFFF;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .panel-bg {
            background-color: #0A0C10;
            border: 1px solid #1f1f1f;
        }

        .input-bg {
            background-color: #12151C;
            border: 1px solid #1f1f1f;
        }

        /* CSS Khusus agar kotak cropper menjadi bulat untuk foto profil */
        body:not(.cropping-banner) .cropper-view-box,
        body:not(.cropping-banner) .cropper-face {
            border-radius: 50%;
        }
    </style>

    
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
            <a href="/profil" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#7C3AED]/10 border border-[#7C3AED]/30 text-[#a78bfa] transition-all text-sm font-bold shadow-[0_0_15px_rgba(124,58,237,0.1)]">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <g>
                            <path d="M80,71.2V74c0,3.3-2.7,6-6,6H26c-3.3,0-6-2.7-6-6v-2.8c0-7.3,8.5-11.7,16.5-15.2c0.3-0.1,0.5-0.2,0.8-0.4 c0.6-0.3,1.3-0.3,1.9,0.1C42.4,57.8,46.1,59,50,59c3.9,0,7.6-1.2,10.8-3.2c0.6-0.4,1.3-0.4,1.9-0.1c0.3,0.1,0.5,0.2,0.8,0.4 C71.5,59.5,80,63.9,80,71.2z" />
                        </g>
                        <g>
                            <ellipse cx="50" cy="36.5" rx="14.9" ry="16.5" />
                        </g>
                    </g>
                </svg> Profil Saya
            </a>
            <a href="/library" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
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
                </svg> Library Game
            </a>
            <a href="/orders" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5 stroke-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="6" width="18" height="13" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M3 10H20.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7 15H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg> Riwayat Transaksi
            </a>
            <a href="/wishlist" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z" />
                </svg> Wishlist
            </a>
            <div class="mt-auto pt-6 border-t border-[#1f1f1f]">
                <a href="/logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-500/10 transition-all text-sm font-medium">
                    <svg class="w-5 h-5 stroke-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 16.9998L21 11.9998M21 11.9998L16 6.99982M21 11.9998H9M12 16.9998C12 17.2954 12 17.4432 11.989 17.5712C11.8748 18.9018 10.8949 19.9967 9.58503 20.2571C9.45903 20.2821 9.31202 20.2985 9.01835 20.3311L7.99694 20.4446C6.46248 20.6151 5.69521 20.7003 5.08566 20.5053C4.27293 20.2452 3.60942 19.6513 3.26118 18.8723C3 18.288 3 17.5161 3 15.9721V8.02751C3 6.48358 3 5.71162 3.26118 5.12734C3.60942 4.3483 4.27293 3.75442 5.08566 3.49435C5.69521 3.29929 6.46246 3.38454 7.99694 3.55503L9.01835 3.66852C9.31212 3.70117 9.45901 3.71749 9.58503 3.74254C10.8949 4.00297 11.8748 5.09786 11.989 6.42843C12 6.55645 12 6.70424 12 6.99982" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg> Keluar Akun
                </a>
            </div>
        </aside>

        {{-- KONTEN UTAMA PROFIL --}}
        <main class="flex-1 overflow-y-auto hide-scrollbar p-6 lg:p-10 bg-[#050505]">
            <div class="max-w-6xl mx-auto pb-10">

                {{-- PESAN NOTIFIKASI --}}


                {{-- BUNGKUS SELURUH KONTEN DENGAN FORM AGAR BANNER BISA TERSIMPAN BERSAMAAN --}}
                <form action="/profil/update" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- BANNER & HEADER PROFIL --}}
                    <div id="bannerContainer" class="relative w-full h-48 md:h-56 rounded-2xl border border-[#1f1f1f] mb-8 flex items-center px-8 md:px-10 overflow-hidden group transition-all"
                        style="background: {!! Auth::user()->banner ? 'url(\''.asset('assets/profile/'.Auth::user()->banner).'\') center/cover no-repeat' : 'linear-gradient(to right, #2c1065, #12082b, #0A0C10)' !!};">

                        {{-- Overlay Gelap --}}
                        <div class="absolute inset-0 bg-black/40"></div>

                        {{-- Tombol Edit Banner (Muncul Saat Hover) --}}
                        <div class="absolute top-4 right-4 z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                            <label for="bannerInput" class="cursor-pointer bg-black/60 hover:bg-black/80 text-white text-xs font-bold px-6 py-3 rounded-xl border border-white/20 backdrop-blur-sm flex items-center gap-2 transition-all uppercase tracking-widest shadow-lg">
                                <span class="flex items-center justify-center">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="m3.99 16.854-1.314 3.504a.75.75 0 0 0 .966.965l3.503-1.314a3 3 0 0 0 1.068-.687L18.36 9.175s-.354-1.061-1.414-2.122c-1.06-1.06-2.122-1.414-2.122-1.414L4.677 15.786a3 3 0 0 0-.687 1.068zm12.249-12.63 1.383-1.383c.248-.248.579-.406.925-.348.487.08 1.232.322 1.934 1.025.703.703.945 1.447 1.025 1.934.058.346-.1.677-.348.925L19.774 7.76s-.353-1.06-1.414-2.12c-1.06-1.062-2.121-1.415-2.121-1.415z" fill="currentColor" />
                                    </svg>
                                </span> Edit Banner
                            </label>
                            <input type="file" id="bannerInput" name="banner" accept="image/*" class="hidden">
                            <input type="hidden" name="cropped_banner" id="croppedBannerInput">
                        </div>

                        {{-- Info User (Avatar & Nama) --}}
                        <div class="flex items-center gap-5 md:gap-6 w-full relative z-10">
                            <div class="flex-shrink-0 relative group/avatar">
                                @if(Auth::user()->foto)
                                <img id="profilePreview" src="{{ asset('assets/profile/' . Auth::user()->foto) }}" class="w-24 h-24 md:w-28 md:h-28 rounded-full object-cover border-4 border-[#050505] shadow-[0_0_30px_rgba(124,58,237,0.3)] bg-[#050505]">
                                @else
                                <div id="profilePreviewPlaceholder" class="w-24 h-24 md:w-28 md:h-28 rounded-full flex items-center justify-center text-4xl font-bold text-white border-4 border-[#050505] shadow-[0_0_30px_rgba(124,58,237,0.3)] bg-gradient-to-br from-[#7C3AED] to-[#4C1D95]">
                                    {{ strtoupper(substr(Auth::user()->username ?? 'U', 0, 1)) }}
                                </div>
                                <img id="profilePreview" src="" class="w-24 h-24 md:w-28 md:h-28 rounded-full object-cover border-4 border-[#050505] shadow-[0_0_30px_rgba(124,58,237,0.3)] bg-[#050505] hidden">
                                @endif
                                
                                {{-- Tombol Edit Foto Profil --}}
                                <label for="fotoInput" class="absolute bottom-1 right-1 bg-[#7C3AED] hover:bg-[#6D28D9] text-white p-2 md:p-2.5 rounded-full cursor-pointer shadow-[0_0_15px_rgba(124,58,237,0.5)] border-2 border-[#050505] transition-all transform hover:scale-110 opacity-100 md:opacity-0 group-hover/avatar:opacity-100" title="Ubah Foto Profil">
                                    <svg class="w-4 h-4 md:w-5 md:h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </label>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-widest drop-shadow-md">{{ Auth::user()->username }}</h1>
                                    <span class="bg-green-500/20 text-green-400 border border-green-500/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider backdrop-blur-sm">Terverifikasi</span>
                                </div>
                                <p class="text-xs text-gray-300 drop-shadow-md">Pengguna Resmi GameVault • Indonesia</p>
                            </div>
                        </div>
                    </div>

                    {{-- STATISTIK RINGKAS --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                        <div class="panel-bg p-5 rounded-2xl flex items-center gap-4 hover:border-[#7C3AED]/50 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-[#7C3AED]/10 text-[#a78bfa] flex items-center justify-center text-2xl border border-[#7C3AED]/20"><svg class="w-6 h-6 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg">
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
                                </svg></div>
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Game Dimiliki</p>
                                <div class="flex items-end gap-2">
                                    <p class="text-2xl font-black text-white">{{ $total_owned ?? 0 }}</p>
                                    <a href="/library" class="text-[10px] text-[#a78bfa] hover:text-white mb-1 transition-colors">Lihat Library →</a>
                                </div>
                            </div>
                        </div>
                        <div class="panel-bg p-5 rounded-2xl flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20"><svg class="w-6 h-6 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.67326018,0 C6.0598595,0 6.37326018,0.31324366 6.37326018,0.699649298 L6.373,2.009 L13.89,2.009 L13.8901337,0.708141199 C13.8901337,0.321735562 14.2035343,0.00849190182 14.5901337,0.00849190182 C14.976733,0.00849190182 15.2901337,0.321735562 15.2901337,0.708141199 L15.29,2.009 L18,2.00901806 C19.1045695,2.00901806 20,2.90399995 20,4.00801605 L20,18.001002 C20,19.1050181 19.1045695,20 18,20 L2,20 C0.8954305,20 0,19.1050181 0,18.001002 L0,4.00801605 C0,2.90399995 0.8954305,2.00901806 2,2.00901806 L4.973,2.009 L4.97326018,0.699649298 C4.97326018,0.31324366 5.28666085,0 5.67326018,0 Z M1.4,7.742 L1.4,18.001002 C1.4,18.3322068 1.66862915,18.6007014 2,18.6007014 L18,18.6007014 C18.3313708,18.6007014 18.6,18.3322068 18.6,18.001002 L18.6,7.756 L1.4,7.742 Z M6.66666667,14.6186466 L6.66666667,16.284778 L5,16.284778 L5,14.6186466 L6.66666667,14.6186466 Z M10.8333333,14.6186466 L10.8333333,16.284778 L9.16666667,16.284778 L9.16666667,14.6186466 L10.8333333,14.6186466 Z M15,14.6186466 L15,16.284778 L13.3333333,16.284778 L13.3333333,14.6186466 L15,14.6186466 Z M6.66666667,10.6417617 L6.66666667,12.3078931 L5,12.3078931 L5,10.6417617 L6.66666667,10.6417617 Z M10.8333333,10.6417617 L10.8333333,12.3078931 L9.16666667,12.3078931 L9.16666667,10.6417617 L10.8333333,10.6417617 Z M15,10.6417617 L15,12.3078931 L13.3333333,12.3078931 L13.3333333,10.6417617 L15,10.6417617 Z M4.973,3.408 L2,3.40831666 C1.66862915,3.40831666 1.4,3.67681122 1.4,4.00801605 L1.4,6.343 L18.6,6.357 L18.6,4.00801605 C18.6,3.67681122 18.3313708,3.40831666 18,3.40831666 L15.29,3.408 L15.2901337,4.33697436 C15.2901337,4.72338 14.976733,5.03662366 14.5901337,5.03662366 C14.2035343,5.03662366 13.8901337,4.72338 13.8901337,4.33697436 L13.89,3.408 L6.373,3.408 L6.37326018,4.32848246 C6.37326018,4.7148881 6.0598595,5.02813176 5.67326018,5.02813176 C5.28666085,5.02813176 4.97326018,4.7148881 4.97326018,4.32848246 L4.973,3.408 Z" />
                                </svg></div>
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Bergabung Sejak</p>
                                <p class="text-xl font-bold text-white">{{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : '2026' }}</p>
                            </div>
                        </div>
                        <div class="panel-bg p-5 rounded-2xl flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-yellow-500/10 text-yellow-400 flex items-center justify-center border border-yellow-500/20"><svg class="w-6 h-6 fill-current" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8 16L4.35009 13.3929C2.24773 11.8912 1 9.46667 1 6.88306V3L8 0L15 3V6.88306C15 9.46667 13.7523 11.8912 11.6499 13.3929L8 16ZM12.2071 5.70711L10.7929 4.29289L7 8.08579L5.20711 6.29289L3.79289 7.70711L7 10.9142L12.2071 5.70711Z" />
                                </svg></div>
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Status Keamanan</p>
                                <p class="text-xl font-bold text-yellow-400">Terlindungi</p>
                            </div>
                        </div>
                    </div>

                    {{-- FORM GRID (Kiri: Profil, Kanan: Email & Keamanan) --}}

                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                        {{-- KOLOM KIRI: INFORMASI PROFIL --}}
                        <div class="xl:col-span-2 panel-bg p-6 lg:p-8 rounded-2xl">
                            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-[#1f1f1f] pb-4 mb-6">Informasi Profil</h2>

                            <div class="space-y-6">
                                {{-- Input tersembunyi --}}
                                <input type="file" id="fotoInput" accept="image/*" class="hidden">
                                <input type="hidden" name="cropped_photo" id="croppedPhotoInput">

                                {{-- Baris 1: Username --}}
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Username</label>
                                        <span class="text-[9px] text-green-400 font-bold tracking-widest">✓ Tersedia</span>
                                    </div>
                                    <input type="text" value="{{ Auth::user()->username }}" disabled class="w-full px-4 py-3.5 bg-[#050505] border border-[#1f1f1f] rounded-xl text-gray-500 cursor-not-allowed text-sm font-medium">
                                    <p class="text-[10px] text-gray-600 mt-2">*Username bersifat permanen dan tidak dapat diubah.</p>
                                </div>

                                {{-- Baris 2: Nama Lengkap --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" placeholder="Masukkan nama lengkap kamu" class="w-full px-4 py-3.5 input-bg rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-[#7C3AED] focus:ring-1 focus:ring-[#7C3AED]/30 transition-colors text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: EMAIL & KEAMANAN --}}
                        <div class="space-y-6">

                            {{-- Kotak Email --}}
                            <div class="panel-bg p-6 rounded-2xl">
                                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-[#1f1f1f] pb-4 mb-6">Alamat Email</h2>
                                <div class="flex justify-between items-end mb-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest">Email Terdaftar</label>
                                    <span class="text-[9px] text-green-400 font-bold tracking-widest">✓ Terverifikasi</span>
                                </div>
                                <div class="w-full px-4 py-3.5 bg-[#050505] border border-[#1f1f1f] rounded-xl text-gray-300 text-sm font-medium mb-4 truncate">
                                    {{ Auth::user()->email }}
                                </div>
                                <a href="/profil/ganti-email" class="flex justify-center w-full bg-[#12151C] hover:bg-[#1a1d24] border border-[#1f1f1f] text-gray-400 hover:text-white text-xs font-bold py-3 rounded-xl transition-colors uppercase tracking-widest">
                                    Ubah Email Akun
                                </a>
                            </div>

                            {{-- Kotak Keamanan (Ganti Password) --}}
                            <div class="panel-bg p-6 rounded-2xl">
                                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-[#1f1f1f] pb-4 mb-6">Keamanan Akun</h2>
                                <div class="flex items-center gap-3 mb-5">
                                    <span class="text-yellow-500 flex items-center justify-center"><svg class="w-5 h-5 fill-current" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8 16L4.35009 13.3929C2.24773 11.8912 1 9.46667 1 6.88306V3L8 0L15 3V6.88306C15 9.46667 13.7523 11.8912 11.6499 13.3929L8 16ZM12.2071 5.70711L10.7929 4.29289L7 8.08579L5.20711 6.29289L3.79289 7.70711L7 10.9142L12.2071 5.70711Z" />
                                        </svg></span>
                                    <p class="text-xs text-gray-400 leading-relaxed">Amankan akun kamu secara berkala dengan memperbarui kata sandi melalui tautan verifikasi email.</p>
                                </div>
                                <a href="javascript:void(0)" onclick="document.getElementById('formResetPassword').submit();" class="flex justify-center items-center gap-2 w-full bg-[#7C3AED]/10 hover:bg-[#7C3AED]/20 border border-[#7C3AED]/30 text-[#a78bfa] hover:text-white text-xs font-bold py-3 rounded-xl transition-all uppercase tracking-widest group">
                                    <span class="group-hover:scale-110 transition-transform flex items-center justify-center">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 14.5V16.5M7 10.0288C7.47142 10 8.05259 10 8.8 10H15.2C15.9474 10 16.5286 10 17 10.0288M7 10.0288C6.41168 10.0647 5.99429 10.1455 5.63803 10.327C5.07354 10.6146 4.6146 11.0735 4.32698 11.638C4 12.2798 4 13.1198 4 14.8V16.2C4 17.8802 4 18.7202 4.32698 19.362C4.6146 19.9265 5.07354 20.3854 5.63803 20.673C6.27976 21 7.11984 21 8.8 21H15.2C16.8802 21 17.7202 21 18.362 20.673C18.9265 20.3854 19.3854 19.9265 19.673 19.362C20 18.7202 20 17.8802 20 16.2V14.8C20 13.1198 20 12.2798 19.673 11.638C19.3854 11.0735 18.9265 10.6146 18.362 10.327C18.0057 10.1455 17.5883 10.0647 17 10.0288M7 10.0288V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8V10.0288" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span> Ganti Password
                                </a>
                            </div>

                        </div>
                    </div>

                    {{-- TOMBOL AKSI SIMPAN --}}
                    <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-[#1f1f1f]">
                        <a href="/" class="text-xs font-bold text-gray-500 hover:text-white transition-colors uppercase tracking-widest">Batal</a>
                        <button type="submit" class="px-8 py-3.5 bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-[0_0_20px_rgba(124,58,237,0.2)]">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

                {{-- Form Hidden untuk Mailtrap Reset Password --}}
                <form id="formResetPassword" action="/forgot-password" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                </form>

            </div>
        </main>
    </div>
    <!-- Modal untuk Cropping Gambar -->
    <div id="cropperModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div class="bg-[#0A0C10] border border-[#1f1f1f] rounded-2xl w-full max-w-lg p-6 transform scale-95 transition-transform duration-300" id="cropperModalContent">
            <h3 class="text-white font-bold text-lg mb-4">Sesuaikan Foto Profil</h3>

            <!-- Area Gambar -->
            <div class="w-full h-[300px] bg-black rounded-xl overflow-hidden mb-6 flex items-center justify-center">
                <img id="imageToCrop" src="" class="max-w-full max-h-full">
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" id="btnCancelCrop" class="px-5 py-2.5 text-xs font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest">
                    Batal
                </button>
                <button type="button" id="btnApplyCrop" class="px-6 py-2.5 bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-[0_0_15px_rgba(124,58,237,0.3)]">
                    Terapkan
                </button>
            </div>
        </div>
    </div>

    <!-- Script Cropper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fotoInput = document.getElementById('fotoInput');
            const bannerInput = document.getElementById('bannerInput');
            const cropperModal = document.getElementById('cropperModal');
            const cropperModalContent = document.getElementById('cropperModalContent');
            const imageToCrop = document.getElementById('imageToCrop');
            const btnCancelCrop = document.getElementById('btnCancelCrop');
            const btnApplyCrop = document.getElementById('btnApplyCrop');
            
            const profilePreview = document.getElementById('profilePreview');
            const profilePreviewPlaceholder = document.getElementById('profilePreviewPlaceholder');
            const croppedPhotoInput = document.getElementById('croppedPhotoInput');
            
            const bannerContainer = document.getElementById('bannerContainer');
            const croppedBannerInput = document.getElementById('croppedBannerInput');

            let cropper = null;
            let currentCropType = null; // 'foto' or 'banner'

            function openCropper(file, type) {
                const maxSize = type === 'banner' ? 5 * 1024 * 1024 : 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert(`Ukuran file maksimal ${type === 'banner' ? '5MB' : '2MB'}. Silakan pilih file yang lebih kecil.`);
                    if(type === 'foto') fotoInput.value = '';
                    if(type === 'banner') bannerInput.value = '';
                    return;
                }

                currentCropType = type;
                
                // Atur style bundar atau kotak
                if (type === 'banner') {
                    document.body.classList.add('cropping-banner');
                } else {
                    document.body.classList.remove('cropping-banner');
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    imageToCrop.src = event.target.result;

                    cropperModal.classList.remove('hidden');
                    setTimeout(() => {
                        cropperModal.classList.remove('opacity-0');
                        cropperModalContent.classList.remove('scale-95');
                    }, 10);

                    if (cropper) {
                        cropper.destroy();
                    }

                    // Rasio bebas untuk banner (NaN) atau 21/9, kita gunakan NaN agar user bisa potong panjang/pendek
                    // Atau kita bisa paksa rasio misalnya 4:1 agar pas dengan container banner
                    const aspectRatio = type === 'banner' ? 16 / 4 : 1;

                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: aspectRatio,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: type === 'banner' ? 1 : 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                };

                reader.readAsDataURL(file);
            }

            fotoInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files.length > 0) {
                    openCropper(e.target.files[0], 'foto');
                }
            });

            bannerInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files.length > 0) {
                    openCropper(e.target.files[0], 'banner');
                }
            });

            function closeModal() {
                cropperModal.classList.add('opacity-0');
                cropperModalContent.classList.add('scale-95');
                setTimeout(() => {
                    cropperModal.classList.add('hidden');
                    if(currentCropType === 'foto') fotoInput.value = '';
                    if(currentCropType === 'banner') bannerInput.value = '';
                    currentCropType = null;
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    document.body.classList.remove('cropping-banner');
                }, 300);
            }

            btnCancelCrop.addEventListener('click', closeModal);

            btnApplyCrop.addEventListener('click', function() {
                if (!cropper) return;

                const options = currentCropType === 'banner' ? {
                    width: 1200,
                    height: 300, 
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                } : {
                    width: 500,
                    height: 500,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                };

                const canvas = cropper.getCroppedCanvas(options);
                const base64Image = canvas.toDataURL('image/jpeg', 0.8);

                if (currentCropType === 'foto') {
                    croppedPhotoInput.value = base64Image;
                    profilePreview.src = base64Image;
                    profilePreview.classList.remove('hidden');
                    if (profilePreviewPlaceholder) {
                        profilePreviewPlaceholder.classList.add('hidden');
                    }
                } else if (currentCropType === 'banner') {
                    croppedBannerInput.value = base64Image;
                    bannerContainer.style.background = `url('${base64Image}') center/cover no-repeat`;
                }

                closeModal();
            });
        });
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

</body>

</html>