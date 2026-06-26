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
    
    <title>Library Game - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505 !important; color: #FFFFFF; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .card-bg { background-color: #12151C; border: 1px solid rgba(255, 255, 255, 0.05); }
        .hover-card:hover { border-color: rgba(124, 58, 237, 0.5); box-shadow: 0 0 20px rgba(124, 58, 237, 0.15); }
        .trimmer-range { pointer-events: none; }
        .trimmer-range::-webkit-slider-thumb {
            pointer-events: auto;
            appearance: none;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            cursor: grab;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
            border: 2px solid #7C3AED;
        }
        .trimmer-range::-moz-range-thumb {
            pointer-events: auto;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            cursor: grab;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
            border: 2px solid #7C3AED;
        }
    </style>

    
</head>
<body class="flex flex-col h-screen overflow-hidden antialiased selection:bg-purple-500 selection:text-white">

    {{-- NAVBAR ATAS --}}
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

    <div class="flex-1 flex overflow-hidden">
        {{-- SIDEBAR KIRI (Navigasi Akun ala referensi gambar) --}}
        <aside class="w-[240px] flex-shrink-0 border-r border-[#1f1f1f] hidden lg:flex flex-col bg-[#0A0C10] p-6 space-y-2">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 pl-4">Akun Saya</p>
            <a href="/profil" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g><g><path d="M80,71.2V74c0,3.3-2.7,6-6,6H26c-3.3,0-6-2.7-6-6v-2.8c0-7.3,8.5-11.7,16.5-15.2c0.3-0.1,0.5-0.2,0.8-0.4 c0.6-0.3,1.3-0.3,1.9,0.1C42.4,57.8,46.1,59,50,59c3.9,0,7.6-1.2,10.8-3.2c0.6-0.4,1.3-0.4,1.9-0.1c0.3,0.1,0.5,0.2,0.8,0.4 C71.5,59.5,80,63.9,80,71.2z"/></g><g><ellipse cx="50" cy="36.5" rx="14.9" ry="16.5"/></g></g></svg> Profil
            </a>
            <a href="/library" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#7C3AED]/10 border border-[#7C3AED]/30 text-[#a78bfa] transition-all text-sm font-bold shadow-[0_0_15px_rgba(124,58,237,0.1)]">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-1)"><g><g><path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333 h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333 c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333 C235.943,223.156,226.391,213.605,214.609,213.605z"/><path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96 c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598 c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168 c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056 c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411 c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831 c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43 l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228 c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z M455.757,403.285l-69.566-62.373 c-14.316-12.852-32.697-19.956-51.767-19.956H181.592c-19.07,0-37.45,7.104-51.777,19.965l-69.758,62.544 c-19.658-18.052-21.296-61.46-5.213-122.832c12.346-47.111,33.478-98.672,51.546-134.307c1.399-2.752,2.112-5.086,3.217-9.411 c0.319-1.249,1.62-6.572,1.952-7.876c0.16-0.629,0.314-1.22,0.461-1.772l50.992-19.374c1.186,1.381,2.463,2.836,3.793,4.303 c1.113,1.222,1.113,1.222,2.273,2.457c9.927,10.492,13.691,13.62,24.204,13.62h128c10.147,0,13.785-2.861,24.417-13.27 c1.094-1.079,1.094-1.079,2.155-2.148c1.719-1.742,3.365-3.468,4.858-5.073l51.321,19.499c0.151,0.56,0.308,1.159,0.471,1.797 c0.382,1.493,1.69,6.836,1.931,7.787c1.066,4.206,1.73,6.435,2.992,9.058c18.908,39.428,38.104,87.404,50.193,134.082 C475.42,341.016,474.252,384.613,455.757,403.285z"/><path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333 c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z"/><path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333 S354.385,234.938,342.609,234.938z"/><path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S311.719,192.271,299.943,192.271z"/><path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S397.052,192.271,385.276,192.271z"/></g></g></g></svg> Library Game
            </a>
            <a href="/orders" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-[#12151C] hover:text-white transition-all text-sm font-medium">
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

        {{-- KONTEN UTAMA LIBRARY --}}
        <main class="flex-1 overflow-y-auto hide-scrollbar p-6 lg:p-10 bg-[#050505]">
            <div class="max-w-6xl mx-auto">
                
                {{-- Header Konten --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-black text-white tracking-widest uppercase mb-1">Library Saya</h1>
                    <p class="text-gray-500 text-sm">Semua lisensi game yang sudah kamu beli tersimpan aman di sini.</p>
                </div>

                {{-- Baris Statistik Cepat --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                    <div class="bg-[#0d0d0d] border border-[#1f1f1f] p-5 rounded-2xl flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-2xl border border-purple-500/20"><svg class="w-6 h-6 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-1)"><g><g><path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333 h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333 c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333 C235.943,223.156,226.391,213.605,214.609,213.605z"/><path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96 c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598 c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168 c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056 c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411 c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831 c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43 l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228 c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z M455.757,403.285l-69.566-62.373 c-14.316-12.852-32.697-19.956-51.767-19.956H181.592c-19.07,0-37.45,7.104-51.777,19.965l-69.758,62.544 c-19.658-18.052-21.296-61.46-5.213-122.832c12.346-47.111,33.478-98.672,51.546-134.307c1.399-2.752,2.112-5.086,3.217-9.411 c0.319-1.249,1.62-6.572,1.952-7.876c0.16-0.629,0.314-1.22,0.461-1.772l50.992-19.374c1.186,1.381,2.463,2.836,3.793,4.303 c1.113,1.222,1.113,1.222,2.273,2.457c9.927,10.492,13.691,13.62,24.204,13.62h128c10.147,0,13.785-2.861,24.417-13.27 c1.094-1.079,1.094-1.079,2.155-2.148c1.719-1.742,3.365-3.468,4.858-5.073l51.321,19.499c0.151,0.56,0.308,1.159,0.471,1.797 c0.382,1.493,1.69,6.836,1.931,7.787c1.066,4.206,1.73,6.435,2.992,9.058c18.908,39.428,38.104,87.404,50.193,134.082 C475.42,341.016,474.252,384.613,455.757,403.285z"/><path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333 c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z"/><path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333 S354.385,234.938,342.609,234.938z"/><path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S311.719,192.271,299.943,192.271z"/><path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S397.052,192.271,385.276,192.271z"/></g></g></g></svg></div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Total Game</p>
                            <p class="text-2xl font-black text-white">{{ $games->count() }} <span class="text-xs font-normal text-gray-500">Lisensi</span></p>
                        </div>
                    </div>
                    <div class="bg-[#0d0d0d] border border-[#1f1f1f] p-5 rounded-2xl flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20"><svg class="w-6 h-6 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5.67326018,0 C6.0598595,0 6.37326018,0.31324366 6.37326018,0.699649298 L6.373,2.009 L13.89,2.009 L13.8901337,0.708141199 C13.8901337,0.321735562 14.2035343,0.00849190182 14.5901337,0.00849190182 C14.976733,0.00849190182 15.2901337,0.321735562 15.2901337,0.708141199 L15.29,2.009 L18,2.00901806 C19.1045695,2.00901806 20,2.90399995 20,4.00801605 L20,18.001002 C20,19.1050181 19.1045695,20 18,20 L2,20 C0.8954305,20 0,19.1050181 0,18.001002 L0,4.00801605 C0,2.90399995 0.8954305,2.00901806 2,2.00901806 L4.973,2.009 L4.97326018,0.699649298 C4.97326018,0.31324366 5.28666085,0 5.67326018,0 Z M1.4,7.742 L1.4,18.001002 C1.4,18.3322068 1.66862915,18.6007014 2,18.6007014 L18,18.6007014 C18.3313708,18.6007014 18.6,18.3322068 18.6,18.001002 L18.6,7.756 L1.4,7.742 Z M6.66666667,14.6186466 L6.66666667,16.284778 L5,16.284778 L5,14.6186466 L6.66666667,14.6186466 Z M10.8333333,14.6186466 L10.8333333,16.284778 L9.16666667,16.284778 L9.16666667,14.6186466 L10.8333333,14.6186466 Z M15,14.6186466 L15,16.284778 L13.3333333,16.284778 L13.3333333,14.6186466 L15,14.6186466 Z M6.66666667,10.6417617 L6.66666667,12.3078931 L5,12.3078931 L5,10.6417617 L6.66666667,10.6417617 Z M10.8333333,10.6417617 L10.8333333,12.3078931 L9.16666667,12.3078931 L9.16666667,10.6417617 L10.8333333,10.6417617 Z M15,10.6417617 L15,12.3078931 L13.3333333,12.3078931 L13.3333333,10.6417617 L15,10.6417617 Z M4.973,3.408 L2,3.40831666 C1.66862915,3.40831666 1.4,3.67681122 1.4,4.00801605 L1.4,6.343 L18.6,6.357 L18.6,4.00801605 C18.6,3.67681122 18.3313708,3.40831666 18,3.40831666 L15.29,3.408 L15.2901337,4.33697436 C15.2901337,4.72338 14.976733,5.03662366 14.5901337,5.03662366 C14.2035343,5.03662366 13.8901337,4.72338 13.8901337,4.33697436 L13.89,3.408 L6.373,3.408 L6.37326018,4.32848246 C6.37326018,4.7148881 6.0598595,5.02813176 5.67326018,5.02813176 C5.28666085,5.02813176 4.97326018,4.7148881 4.97326018,4.32848246 L4.973,3.408 Z"/></svg></div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Terakhir Dibeli</p>
                            <p class="text-lg font-bold text-white">{{ $games->first() ? \Carbon\Carbon::parse($games->first()->tgl_beli)->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                    <div class="bg-[#0d0d0d] border border-[#1f1f1f] p-5 rounded-2xl flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-500/10 text-green-400 flex items-center justify-center border border-green-500/20"><svg class="w-6 h-6 fill-current" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 16L4.35009 13.3929C2.24773 11.8912 1 9.46667 1 6.88306V3L8 0L15 3V6.88306C15 9.46667 13.7523 11.8912 11.6499 13.3929L8 16ZM12.2071 5.70711L10.7929 4.29289L7 8.08579L5.20711 6.29289L3.79289 7.70711L7 10.9142L12.2071 5.70711Z"/></svg></div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Status Keamanan</p>
                            <p class="text-lg font-bold text-green-400">Terenkripsi 100%</p>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar ala referensi --}}
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b border-[#1f1f1f]">
                    <div class="flex gap-6 filter-tabs">
                        <button class="filter-tab active text-sm font-bold text-white border-b-2 border-[#7C3AED] pb-2 transition-colors" onclick="setFilter('semua', this)">Semua Game</button>
                        <button class="filter-tab text-sm font-medium text-gray-500 hover:text-white border-b-2 border-transparent pb-2 transition-colors" onclick="setFilter('baru', this)">Baru Dibeli</button>
                        <button class="filter-tab text-sm font-medium text-gray-500 hover:text-white border-b-2 border-transparent pb-2 transition-colors" onclick="setFilter('favorit', this)">Favorit</button>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="searchInput" onkeyup="filterGames()" placeholder="Cari di library..." class="w-full bg-[#12151C] border border-[#1f1f1f] text-xs text-white px-4 py-2.5 rounded-lg focus:outline-none focus:border-[#7C3AED] transition-colors pl-9">
                        <svg class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                {{-- Grid Koleksi Game --}}
                @if($games->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6" id="gamesGrid">
                        @foreach($games as $game)
                        <div class="game-card bg-[#0d0d0d] rounded-2xl overflow-hidden hover-card flex flex-col transition-all duration-300 group"
                             data-id="{{ $game->id }}" 
                             data-name="{{ strtolower($game->name) }}" 
                             data-date="{{ \Carbon\Carbon::parse($game->tgl_beli)->timestamp }}">
                            {{-- Cover Game --}}
                            <div class="relative aspect-[3/4] overflow-hidden bg-black border-b border-[#1f1f1f]">
                                {{-- Tombol Favorit --}}
                                <button onclick="toggleFavorite(event, {{ $game->id }})" class="absolute top-3 left-3 z-10 p-1.5 bg-black/40 hover:bg-black/80 rounded-full backdrop-blur-sm transition-all border border-white/5">
                                    <svg class="w-4 h-4 fill-current heart-icon text-gray-400 hover:text-red-500 transition-colors" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </button>
                                {{-- Badge "Dimiliki" ala UI --}}
                                <div class="absolute top-3 right-3 z-10 bg-gray-900/90 text-green-400 text-[10px] tracking-widest font-black px-3 py-1.5 rounded-md shadow-[0_0_15px_rgba(34,197,94,0.3)] border border-green-500/50 backdrop-blur-md uppercase">DIBELI</div>
                                
                                {{-- Label Platform / Console Edition --}}
                                <div class="absolute bottom-3 left-3 flex gap-1 z-10 flex-wrap max-w-[80%]">
                                    @if($game->console_edition)
                                        <span class="text-[9px] text-white bg-black/80 px-2.5 py-1 rounded-md border border-white/20 font-bold shadow-lg uppercase backdrop-blur-sm">{{ $game->console_edition }}</span>
                                    @endif
                                    @if($game->platform)
                                        <span class="text-[9px] text-gray-300 bg-black/80 px-2.5 py-1 rounded-md border border-white/10 font-bold shadow-lg uppercase backdrop-blur-sm">{{ $game->platform }}</span>
                                    @endif
                                </div>
                                
                                <a href="/game/{{ $game->id }}">
                                    <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-500">
                                </a>
                            </div>
                            
                            {{-- Detail info game --}}
                            <div class="p-5 flex-1 flex flex-col">
                                <a href="/game/{{ $game->id }}" class="hover:text-[#a78bfa] transition-colors">
                                    <h3 class="font-bold text-white text-base leading-tight mb-1 line-clamp-1" title="{{ $game->name }}">{{ $game->name }}</h3>
                                </a>
                                <p class="text-[10px] text-gray-500 mb-4 mt-1">Dibeli pada: {{ \Carbon\Carbon::parse($game->tgl_beli)->format('d M Y') }}</p>
                                
                                @if($game->refund_status === 'pending')
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="text-[10px] text-yellow-500 font-bold bg-yellow-500/10 py-1.5 px-3 rounded-lg w-max border border-yellow-500/20 uppercase tracking-wider">Refund Diproses</div>
                                        <form action="/refund/cancel" method="POST" id="cancelRefundForm{{ $game->detail_id }}" class="m-0">
                                            @csrf
                                            <button type="button" onclick="bukaModalCancelRefund('{{ $game->detail_id }}', '{{ addslashes($game->name) }}')" class="text-[10px] text-gray-400 hover:text-white bg-[#1A1D24] hover:bg-red-500/20 hover:border-red-500/50 py-1.5 px-3 rounded-lg w-max border border-white/10 transition-all font-bold uppercase tracking-wider flex items-center gap-1" title="Batalkan Refund">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Batal
                                            </button>
                                        </form>
                                    </div>
                                @elseif($game->refund_status === 'rejected')
                                    <div class="text-[10px] text-red-500 font-bold mb-3 bg-red-500/10 py-1.5 px-3 rounded-lg w-max border border-red-500/20 uppercase tracking-wider">Refund Ditolak</div>
                                @elseif(\Carbon\Carbon::parse($game->tgl_beli)->diffInDays(now()) <= 14 && empty($game->refund_status))
                                    @if(isset($reviews[$game->id]))
                                        <div class="text-[10px] text-gray-500 font-bold mb-3 bg-gray-500/10 py-1.5 px-3 rounded-lg w-max border border-gray-500/20 uppercase tracking-wider cursor-help" title="Tidak bisa refund karena game sudah diulas">Telah Diulas</div>
                                    @else
                                        <button onclick="bukaModalRefund('{{ $game->detail_id }}', '{{ addslashes($game->name) }}')" class="text-[10px] text-red-400 hover:text-white mb-3 bg-red-500/10 hover:bg-red-500 py-1.5 px-3 rounded-lg w-max border border-red-500/20 transition-all font-bold uppercase tracking-wider">Ajukan Refund</button>
                                    @endif
                                @endif
                                
                                {{-- Tombol Buka Lisensi (Action Realistis E-Commerce) --}}
                                <button onclick="bukaLibraryDetail('{{ $game->id }}', '{{ addslashes($game->name) }}', '{{ asset('assets/' . $game->image) }}', '{{ strtoupper(substr(md5($game->id . auth()->user()->id . $game->tgl_beli), 0, 15)) }}')" class="mt-auto w-full flex items-center justify-center text-center text-xs font-bold bg-[#1A1D24] text-white border border-white/10 hover:border-[#7C3AED] hover:bg-[#7C3AED]/20 py-3 rounded-xl transition-all tracking-wide">
                                    <svg class="w-3.5 h-3.5 mr-1.5 fill-current text-yellow-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.6809 5.34814C14.0521 5.34814 12.7265 6.66395 12.7265 8.29353C12.7265 9.92311 14.0521 11.2389 15.6809 11.2389C17.3097 11.2389 18.6353 9.92311 18.6353 8.29353C18.6353 6.66395 17.3097 5.34814 15.6809 5.34814ZM14.2265 8.29353C14.2265 7.49816 14.8748 6.84814 15.6809 6.84814C16.487 6.84814 17.1353 7.49816 17.1353 8.29353C17.1353 9.0889 16.487 9.73891 15.6809 9.73891C14.8748 9.73891 14.2265 9.0889 14.2265 8.29353Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.52998 20.8783C9.86298 20.414 9.97017 19.9429 9.96222 19.5233C10.3544 19.6387 10.7424 19.6533 11.1141 19.5828C11.8825 19.437 12.4511 18.9512 12.7527 18.5507L12.758 18.5437L12.7631 18.5366C13.2883 17.8043 13.2872 17.0543 13.1586 16.5164C13.0956 16.2528 13.0021 16.0361 12.9245 15.8846C12.8853 15.8081 12.849 15.746 12.8207 15.7005C12.8132 15.6885 12.8063 15.6775 12.7999 15.6677C12.7112 15.5021 12.6111 15.3719 12.5269 15.2737L12.5359 15.2647L13.0001 14.8024C13.3817 14.9849 13.7957 15.0999 14.1583 15.1749C14.744 15.2962 15.3171 15.3369 15.6807 15.3369C19.582 15.3369 22.75 12.1863 22.75 8.29344C22.75 4.40056 19.582 1.25 15.6807 1.25C11.7794 1.25 8.61144 4.40056 8.61144 8.29344C8.61144 9.2105 8.82018 9.99588 9.02588 10.549C9.07825 10.6898 9.13081 10.8166 9.18035 10.9279L1.92511 18.1535C1.66869 18.4089 1.36789 18.853 1.27697 19.4092C1.17837 20.0124 1.34031 20.6829 1.92511 21.2654L2.80687 22.1435C2.82046 22.1571 2.83457 22.1701 2.84916 22.1825C3.10385 22.3999 3.53164 22.6513 4.04572 22.7273C4.59712 22.8088 5.23527 22.6818 5.77579 22.1435L6.34232 21.5793C6.87523 21.8849 7.43853 21.9545 7.95941 21.8548C8.63497 21.7254 9.19686 21.321 9.51964 20.8924L9.5249 20.8854L9.52998 20.8783ZM10.1114 8.29344C10.1114 5.23477 12.602 2.75 15.6807 2.75C18.7594 2.75 21.25 5.23477 21.25 8.29344C21.25 11.3521 18.7594 13.8369 15.6807 13.8369C15.4075 13.8369 14.9372 13.8044 14.4623 13.7061C13.9654 13.6032 13.5752 13.4504 13.3674 13.2779C13.0699 13.031 12.6332 13.0508 12.3592 13.3237L11.4774 14.2019C11.2757 14.4028 11.0818 14.6305 10.9794 14.8933C10.8499 15.2261 10.8912 15.5463 11.0394 15.8121C11.1273 15.9697 11.2689 16.1202 11.3278 16.183L11.3476 16.2042C11.4173 16.2811 11.4555 16.3314 11.4834 16.387L11.5098 16.4397L11.54 16.4817L11.5468 16.4924C11.5558 16.507 11.5712 16.533 11.5895 16.5685C11.6267 16.6412 11.6709 16.7445 11.6997 16.8652C11.7544 17.0937 11.7538 17.3656 11.5494 17.6551C11.4087 17.8384 11.1424 18.0506 10.8345 18.1091C10.5769 18.1579 10.1571 18.1261 9.59673 17.5681C9.30409 17.2766 8.83089 17.2766 8.53825 17.5681L8.24433 17.8608C7.96748 18.1365 7.94891 18.5782 8.20054 18.8761C8.20194 18.8778 8.2058 18.8826 8.2116 18.8903C8.22363 18.9062 8.24339 18.9336 8.2668 18.9704C8.31483 19.0461 8.37128 19.1508 8.41138 19.2706C8.48694 19.4963 8.49882 19.7374 8.31639 19.9966C8.19643 20.1519 7.95303 20.3287 7.67726 20.3815C7.4429 20.4264 7.14284 20.3931 6.8045 20.0562C6.51186 19.7647 6.03866 19.7647 5.74602 20.0562L4.7173 21.0807C4.55241 21.2449 4.4068 21.2643 4.26505 21.2434C4.09729 21.2186 3.93333 21.1293 3.84077 21.0562L2.9836 20.2025C2.74543 19.9653 2.73591 19.7821 2.75733 19.6511C2.78643 19.4731 2.89711 19.3025 2.9836 19.2163L10.6279 11.6033C10.8747 11.3575 10.9185 10.9735 10.7333 10.6784L10.7311 10.6748C10.7284 10.6703 10.7232 10.6615 10.7158 10.6487C10.7012 10.6231 10.6781 10.5814 10.6494 10.5251C10.5918 10.4123 10.5122 10.2423 10.4318 10.0262C10.2701 9.59135 10.1114 8.98632 10.1114 8.29344ZM8.20054 18.8761C8.20192 18.8777 8.2033 18.8793 8.20469 18.881L8.20354 18.8796L8.20054 18.8761Z" fill="currentColor"/>
                                    </svg>
                                    LIHAT LISENSI & ULASAN
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Empty State untuk Filter --}}
                    <div id="emptyFilterState" class="hidden flex-col items-center justify-center py-20 text-center border border-dashed border-[#1f1f1f] rounded-2xl bg-[#0d0d0d]">
                        <span class="text-6xl mb-4 opacity-30">🔍</span>
                        <h3 class="text-lg font-bold text-gray-300 mb-1">Game Tidak Ditemukan</h3>
                        <p class="text-gray-500 text-sm max-w-sm mb-6">Tidak ada game yang sesuai dengan pencarian atau filter Anda.</p>
                    </div>
                @else
                    {{-- Tampilan kalau kosong --}}
                    <div class="flex flex-col items-center justify-center py-20 text-center border border-dashed border-[#1f1f1f] rounded-2xl bg-[#0d0d0d]">
                        <span class="text-6xl mb-4 opacity-30">📦</span>
                        <h3 class="text-lg font-bold text-gray-300 mb-1">Library Kosong</h3>
                        <p class="text-gray-500 text-sm max-w-sm mb-6">Kamu belum melakukan pembelian game apapun.</p>
                        <a href="/" class="px-6 py-3 bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all">Jelajahi Store</a>
                    </div>
                @endif

            </div>
        </main>
    </div>

    {{-- Modal Konfirmasi Sukses (Menggantikan Toast) --}}
    @include('components.success-modal')

    {{-- MODAL POPUP LIBRARY DETAIL --}}
    <div id="libraryDetailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity">
        <div class="bg-[#0A0C10] rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.3)] w-full max-w-4xl max-h-[90vh] overflow-hidden relative flex flex-col mx-4" onclick="event.stopPropagation()">
            <button onclick="document.getElementById('libraryDetailModal').classList.add('hidden')" class="absolute top-4 right-4 text-white/50 hover:text-white bg-black/50 hover:bg-red-500 w-8 h-8 rounded-full flex items-center justify-center z-50 transition-all">✕</button>
            
            {{-- Header Hero --}}
            <div class="relative h-48 md:h-64 w-full flex-shrink-0 bg-[#12151C]">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0A0C10] via-[#0A0C10]/60 to-transparent z-10"></div>
                <img id="detailGameImage" src="" class="w-full h-full object-cover opacity-60 mix-blend-screen">
                <div class="absolute bottom-6 left-6 md:left-10 z-20">
                    <h2 class="text-3xl md:text-5xl font-black text-white" style="text-shadow: 2px 2px 10px rgba(0,0,0,0.8);" id="detailGameName">Nama Game</h2>
                </div>
            </div>

            {{-- Content Area --}}
            <div class="flex flex-col md:flex-row p-6 md:p-10 gap-8 overflow-y-auto hide-scrollbar flex-1">
                
                {{-- Kiri: Lisensi & Unduh --}}
                <div class="flex-1 space-y-6">
                    <div class="bg-[#12151C] border border-white/5 p-6 rounded-2xl">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-3">Lisensi / CD Key Anda</p>
                        <div class="bg-black/50 border border-purple-500/30 p-4 rounded-xl mb-4 flex justify-between items-center group">
                            <p id="detailLisensiCode" class="text-lg md:text-xl font-mono font-black text-purple-400 tracking-widest">XXXXX-YYYYY-ZZZZZ</p>
                        </div>
                        <button onclick="salinKodeDetail(this)" class="w-full bg-[#1A1D24] hover:bg-white/10 border border-white/10 text-white font-bold py-3 rounded-xl transition-all text-sm uppercase tracking-widest flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2 stroke-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z" stroke-width="1.5"/><path d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5" stroke-width="1.5"/></svg>
                            Salin Lisensi
                        </button>
                    </div>
                </div>

                {{-- Kanan: Form Ulasan --}}
                <div class="flex-1 bg-[#12151C] border border-white/5 p-6 rounded-2xl flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold" id="ulasanTitle">Tulis Ulasan Anda</p>
                            <button type="button" id="editReviewBtn" class="hidden text-gray-400 hover:text-white transition-colors" onclick="unlockReviewForm()" title="Edit Ulasan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </div>
                        <form id="deleteReviewForm" action="/review/delete" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="game_id" id="deleteReviewGameId" value="">
                            <button type="button" onclick="bukaModalHapusUlasan()" class="text-xs text-red-500 hover:text-red-400 font-bold transition-colors">Hapus Ulasan</button>
                        </form>
                    </div>
                    
                    <form action="/review/simpan" method="POST" id="reviewForm" onsubmit="return validateReview(event)" class="flex flex-col flex-1" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="game_id" id="reviewGameId" value="">
                        
                        {{-- Bintang Interaktif --}}
                        <div class="flex items-center gap-2 mb-4 transition-opacity" id="starRatingContainer">
                            <input type="hidden" name="rating" id="reviewRating" value="">
                            @for($i=1; $i<=5; $i++)
                                <svg data-val="{{ $i }}" class="w-8 h-8 cursor-pointer transition-colors text-gray-600 star-icon hover:scale-110" fill="currentColor" viewBox="0 0 20 20" onclick="setRating({{ $i }})"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endfor
                        </div>

                        <textarea name="komentar" placeholder="Bagaimana pengalaman Anda memainkan game ini? Ceritakan kesan Anda..." required class="w-full bg-[#050505] border border-white/10 text-sm text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-all resize-none flex-1 mb-4 hide-scrollbar read-only:opacity-60 read-only:cursor-default" rows="3"></textarea>

                        {{-- Upload Media --}}
                        <div class="mb-4">
                            <button type="button" onclick="triggerUpload()" id="mediaUploadLabel" class="flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-purple-400 cursor-pointer transition-all w-max">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Lampirkan Foto / Video
                            </button>
                            <input type="file" id="mediaSelect" accept="image/*,video/*" class="hidden" onchange="handleMediaSelect(this)">
                            <input type="file" name="media[]" id="mediaSubmit" multiple class="hidden">
                            <input type="hidden" name="existing_media" id="existingMediaInput" value="">
                            <input type="hidden" name="video_cuts" id="videoCutsInput" value="{}">
                            
                            {{-- Preview Area --}}
                            <div id="mediaPreviewContainer" class="hidden mt-3 flex gap-3 overflow-x-auto hide-scrollbar pb-2">
                                {{-- JS will populate previews here --}}
                            </div>
                        </div>

                        <button type="submit" id="submitReviewBtn" class="w-full bg-white text-black font-bold py-3 rounded-xl transition-all hover:bg-gray-200 mt-auto shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                            KIRIM ULASAN
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL CROPPER --}}
    <div id="cropperModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity">
        <div class="bg-[#0A0C10] rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.3)] w-full max-w-2xl overflow-hidden relative flex flex-col mx-4" onclick="event.stopPropagation()">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-bold">Sesuaikan Gambar</h3>
                <button type="button" onclick="closeCropper()" class="text-gray-400 hover:text-white transition-colors">✕</button>
            </div>
            <div class="p-6 bg-[#12151C] flex justify-center items-center h-[50vh]">
                <img id="cropperImage" src="" class="max-h-full max-w-full">
            </div>
            <div class="p-6 border-t border-white/10 flex justify-end gap-3">
                <button type="button" onclick="closeCropper()" class="px-5 py-2 rounded-xl text-gray-400 hover:text-white font-medium transition-colors">Batal</button>
                <button type="button" onclick="cropAndSave()" class="px-5 py-2 rounded-xl bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold transition-colors">Crop & Simpan</button>
            </div>
        </div>
    </div>

    {{-- MODAL REFUND --}}
    <div id="refundModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity">
        <div class="bg-[#0A0C10] rounded-2xl border border-red-500/30 shadow-[0_0_50px_rgba(239,68,68,0.2)] w-full max-w-lg overflow-hidden relative flex flex-col mx-4" onclick="event.stopPropagation()">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Ajukan Refund
                </h3>
                <button type="button" onclick="tutupModalRefund()" class="text-gray-400 hover:text-white transition-colors">✕</button>
            </div>
            <form action="/refund/request" method="POST" class="flex flex-col">
                @csrf
                <input type="hidden" name="detail_transaksi_id" id="refundDetailId" value="">
                
                <div class="p-6 bg-[#12151C] flex flex-col gap-4">
                    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                        <p class="text-xs text-red-200 leading-relaxed">
                            <strong>Perhatian:</strong> Pengajuan refund hanya dapat dilakukan jika pembelian belum melewati batas 14 hari. Jika disetujui, akses lisensi game ini akan ditarik dari akun Anda.
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Game yang di-Refund</label>
                        <p id="refundGameName" class="text-white font-bold text-lg bg-[#050505] p-3 rounded-lg border border-white/5"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Alasan Refund</label>
                        <select name="alasan" id="alasanSelect" onchange="toggleAlasanLainnya(this.value)" required class="w-full bg-[#050505] border border-white/10 text-sm text-white px-4 py-3 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/30 transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Alasan --</option>
                            <option value="Game tidak dapat dijalankan (Crash/Error)">Game tidak dapat dijalankan (Crash/Error)</option>
                            <option value="Spesifikasi PC tidak memenuhi syarat">Spesifikasi PC tidak memenuhi syarat</option>
                            <option value="Salah membeli game">Salah membeli game</option>
                            <option value="Game tidak sesuai ekspektasi">Game tidak sesuai ekspektasi</option>
                            <option value="Banyak bug / glitch">Banyak bug / glitch</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        
                        <div id="alasanLainnyaContainer" class="hidden mt-3">
                            <textarea name="alasan_lainnya" id="alasanLainnyaInput" rows="3" placeholder="Jelaskan alasan Anda secara singkat..." class="w-full bg-[#050505] border border-white/10 text-sm text-white px-4 py-3 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/30 transition-all resize-none"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-white/10 flex justify-end gap-3 bg-[#0A0C10]">
                    <button type="button" onclick="tutupModalRefund()" class="px-5 py-2.5 rounded-xl text-gray-400 hover:text-white font-medium transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold transition-colors text-sm shadow-lg shadow-red-500/20">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL BATALKAN REFUND --}}
    <div id="cancelRefundModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity" onclick="tutupModalCancelRefund()">
        <div class="bg-[#0A0C10] rounded-2xl border border-red-500/30 shadow-[0_0_50px_rgba(239,68,68,0.2)] w-full max-w-md overflow-hidden relative flex flex-col mx-4" onclick="event.stopPropagation()">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Batalkan Pengajuan
                </h3>
                <button type="button" onclick="tutupModalCancelRefund()" class="text-gray-400 hover:text-white transition-colors">✕</button>
            </div>
            <form action="/refund/cancel" method="POST" class="flex flex-col">
                @csrf
                <input type="hidden" name="detail_transaksi_id" id="cancelRefundDetailId" value="">
                
                <div class="p-6 bg-[#12151C]">
                    <p class="text-sm text-gray-300 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan pengajuan refund untuk game <strong id="cancelRefundGameName" class="text-white"></strong>?
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        Jika dibatalkan, pengajuan ini akan dihapus dari antrean Admin dan Anda bisa mengajukannya kembali nanti (jika belum lebih dari 14 hari).
                    </p>
                </div>
                
                <div class="p-6 border-t border-white/10 flex justify-end gap-3 bg-[#0A0C10]">
                    <button type="button" onclick="tutupModalCancelRefund()" class="px-5 py-2.5 rounded-xl text-gray-400 hover:text-white font-medium transition-colors text-sm">Tutup</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold transition-colors text-sm shadow-lg shadow-red-500/20">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TRIMMER VIDEO --}}
    <div id="videoTrimmerModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity">
        <div class="bg-[#0A0C10] rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.3)] w-full max-w-2xl overflow-hidden relative flex flex-col mx-4" onclick="event.stopPropagation()">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-bold">Potong Video (Opsional)</h3>
                <button type="button" onclick="closeTrimmer()" class="text-gray-400 hover:text-white transition-colors">✕</button>
            </div>
            <div class="p-6 bg-[#12151C] flex flex-col justify-center items-center gap-6">
                <video id="trimmerVideo" src="" class="max-h-[40vh] max-w-full rounded-xl bg-black" controls></video>
                
                <div class="w-full flex flex-col gap-2 relative mt-4">
                    <div class="flex justify-between text-xs text-gray-400 font-medium px-1">
                        <span id="trimStartText">0s</span>
                        <span id="trimEndText">0s</span>
                    </div>
                    <div class="relative w-full h-3 bg-gray-800 rounded-full flex items-center">
                        <div id="trimmerTrack" class="absolute h-full bg-[#7C3AED] rounded-full pointer-events-none" style="left: 0%; right: 0%;"></div>
                        <input type="range" id="trimStart" min="0" max="100" value="0" step="0.1" class="absolute w-full h-full appearance-none bg-transparent trimmer-range" oninput="updateTrimmer()">
                        <input type="range" id="trimEnd" min="0" max="100" value="100" step="0.1" class="absolute w-full h-full appearance-none bg-transparent trimmer-range" oninput="updateTrimmer()">
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">Geser tombol untuk menentukan batas waktu mulai dan selesai.</p>
                </div>
            </div>
            <div class="p-6 border-t border-white/10 flex justify-end gap-3">
                <button type="button" onclick="closeTrimmer()" class="px-5 py-2 rounded-xl text-gray-400 hover:text-white font-medium transition-colors">Lewati / Batal</button>
                <button type="button" onclick="saveTrim()" class="px-5 py-2 rounded-xl bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold transition-colors">Simpan Potongan</button>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS ULASAN --}}
    <div id="deleteReviewConfirmModal" class="hidden fixed inset-0 z-[300] flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#12151C] p-6 rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] w-full max-w-sm text-center transform scale-95 transition-transform duration-300" id="deleteReviewModalContent">
            <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Hapus Ulasan?</h3>
            <p class="text-sm text-gray-400 mb-6">Ulasan beserta lampiran medianya akan dihapus. Apakah kamu yakin?</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="tutupModalHapusUlasan()" class="flex-1 px-4 py-3 bg-[#0A0C10] hover:bg-white/5 border border-white/10 text-gray-300 hover:text-white text-sm font-bold rounded-xl transition-colors">Batal</button>
                <button type="button" onclick="eksekusiHapusUlasan()" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-red-500/20">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <script>
        const userReviews = @json($reviews ?? []);
        let currentCropper = null;
        let currentFileObj = null;

        function bukaModalHapusUlasan() {
            const modal = document.getElementById('deleteReviewConfirmModal');
            const content = document.getElementById('deleteReviewModalContent');
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }

        function tutupModalHapusUlasan() {
            const modal = document.getElementById('deleteReviewConfirmModal');
            const content = document.getElementById('deleteReviewModalContent');
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function eksekusiHapusUlasan() {
            document.getElementById('deleteReviewForm').submit();
        }

        function setRating(val) {
            document.getElementById('reviewRating').value = val;
            let stars = document.querySelectorAll('.star-icon');
            stars.forEach((star, index) => {
                if(index < val) {
                    star.classList.add('text-yellow-500');
                    star.classList.remove('text-gray-600');
                } else {
                    star.classList.remove('text-yellow-500');
                    star.classList.add('text-gray-600');
                }
            });
        }

        function validateReview(event) {
            let rating = document.getElementById('reviewRating').value;
            if(!rating || rating == "0") {
                event.preventDefault();
                showErrorToast("Mohon berikan rating (klik bintang) sebelum mengirim ulasan!");
                return false;
            }

            // Masukkan selectedMediaFiles ke dalam input hidden 'media[]' menggunakan DataTransfer
            if (selectedMediaFiles.length > 0) {
                const dataTransfer = new DataTransfer();
                selectedMediaFiles.forEach(file => dataTransfer.items.add(file));
                document.getElementById('mediaSubmit').files = dataTransfer.files;
            }

            if (typeof window.showLoadingOverlay === 'function') {
                window.showLoadingOverlay();
            }
            return true;
        }

        function showErrorToast(msg) {
            let toast = document.createElement('div');
            toast.className = 'fixed bottom-10 left-1/2 transform -translate-x-1/2 bg-[#0A0C10] border border-red-500/50 text-white px-6 py-4 rounded-xl shadow-[0_0_30px_rgba(239,68,68,0.2)] z-[100] flex items-center transition-all duration-300 opacity-0 translate-y-8 font-medium text-sm w-max max-w-[90vw]';
            toast.innerHTML = `<svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> <span>${msg}</span>`;
            document.body.appendChild(toast);
            
            // Animasi masuk
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-8');
            });
            
            // Animasi keluar otomatis
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-8');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function bukaModalRefund(detailId, gameName) {
            document.getElementById('refundDetailId').value = detailId;
            document.getElementById('refundGameName').innerText = gameName;
            document.getElementById('refundModal').classList.remove('hidden');
            
            // Reset form saat dibuka
            document.getElementById('alasanSelect').value = "";
            document.getElementById('alasanLainnyaContainer').classList.add('hidden');
            document.getElementById('alasanLainnyaInput').required = false;
            document.getElementById('alasanLainnyaInput').value = "";
        }

        function tutupModalRefund() {
            document.getElementById('refundModal').classList.add('hidden');
        }

        function bukaModalCancelRefund(detailId, gameName) {
            document.getElementById('cancelRefundDetailId').value = detailId;
            document.getElementById('cancelRefundGameName').innerText = gameName;
            document.getElementById('cancelRefundModal').classList.remove('hidden');
        }

        function tutupModalCancelRefund() {
            document.getElementById('cancelRefundModal').classList.add('hidden');
        }

        function toggleAlasanLainnya(value) {
            const container = document.getElementById('alasanLainnyaContainer');
            const input = document.getElementById('alasanLainnyaInput');
            if (value === 'Lainnya') {
                container.classList.remove('hidden');
                input.required = true;
                input.focus();
            } else {
                container.classList.add('hidden');
                input.required = false;
                input.value = "";
            }
        }

        function bukaLibraryDetail(id, namaGame, image, kodeRaw) {
            let formattedCode = kodeRaw.match(/.{1,5}/g).join('-');
            
            document.getElementById('detailGameName').innerText = namaGame;
            document.getElementById('detailGameImage').src = image;
            document.getElementById('detailLisensiCode').innerText = formattedCode;
            document.getElementById('reviewGameId').value = id;
            
            // Reset form
            document.getElementById('reviewRating').value = '';
            document.querySelector('textarea[name="komentar"]').value = '';
            document.querySelectorAll('.star-icon').forEach(star => {
                star.classList.remove('text-yellow-500');
                star.classList.add('text-gray-600');
            });
            // Reset state default (terbuka & tidak ada tombol edit)
            isReviewFormUnlocked = false;
            document.getElementById('editReviewBtn').classList.add('hidden');
            document.getElementById('starRatingContainer').classList.remove('pointer-events-none', 'opacity-60');
            document.querySelector('textarea[name="komentar"]').readOnly = false;
            document.getElementById('mediaUploadLabel').classList.remove('hidden');
            document.getElementById('submitReviewBtn').classList.remove('hidden');
            document.getElementById('ulasanTitle').innerText = 'Tulis Ulasan Anda';

            // Cek apakah sudah pernah review
            if (userReviews[id]) {
                const review = userReviews[id];
                // Set rating
                setRating(review.rating);
                // Set komentar
                document.querySelector('textarea[name="komentar"]').value = review.komentar || '';
                // Tampilkan form hapus
                document.getElementById('deleteReviewGameId').value = id;
                document.getElementById('deleteReviewForm').classList.remove('hidden');
                // Ubah text tombol
                document.getElementById('submitReviewBtn').innerText = 'UPDATE ULASAN';
                document.getElementById('ulasanTitle').innerText = 'Ulasan Anda';
                
                // Set existing media
                if (review.media) {
                    existingMediaUrls = review.media.split('|');
                } else {
                    existingMediaUrls = [];
                }

                // LOCK THE FORM (Read-only mode)
                document.getElementById('editReviewBtn').classList.remove('hidden');
                document.getElementById('starRatingContainer').classList.add('pointer-events-none', 'opacity-60');
                document.querySelector('textarea[name="komentar"]').readOnly = true;
                document.getElementById('mediaUploadLabel').classList.add('hidden');
                document.getElementById('submitReviewBtn').classList.add('hidden');
            } else {
                existingMediaUrls = [];
            }
            
            clearMedia();
            
            // Tutup modal jika klik di luar
            document.getElementById('libraryDetailModal').onclick = function() {
                this.classList.add('hidden');
            };
            
            document.getElementById('libraryDetailModal').classList.remove('hidden');
        }

        function unlockReviewForm() {
            isReviewFormUnlocked = true;
            document.getElementById('editReviewBtn').classList.add('hidden');
            document.getElementById('starRatingContainer').classList.remove('pointer-events-none', 'opacity-60');
            document.querySelector('textarea[name="komentar"]').readOnly = false;
            document.getElementById('mediaUploadLabel').classList.remove('hidden');
            document.getElementById('submitReviewBtn').classList.remove('hidden');
            document.querySelector('textarea[name="komentar"]').focus();
            renderMediaPreviews();
        }

        let selectedMediaFiles = [];
        let mediaAction = 'append';
        let mediaReplaceIndex = -1;
        let mediaReplaceIsExisting = false;

        function triggerUpload() {
            mediaAction = 'append';
            document.getElementById('mediaSelect').click();
        }

        function triggerReplace(index, isExisting) {
            mediaAction = 'replace';
            mediaReplaceIndex = index;
            mediaReplaceIsExisting = isExisting;
            document.getElementById('mediaSelect').click();
        }

        function processMediaAdd(file) {
            if (mediaAction === 'replace') {
                if (mediaReplaceIsExisting) {
                    existingMediaUrls.splice(mediaReplaceIndex, 1);
                    selectedMediaFiles.push(file);
                } else {
                    selectedMediaFiles[mediaReplaceIndex] = file;
                }
            } else {
                selectedMediaFiles.push(file);
            }
            renderMediaPreviews();
            mediaAction = 'append'; // Reset
        }

        function handleMediaSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                currentFileObj = file;
                
                if (file.type.startsWith('image/')) {
                    const fileURL = URL.createObjectURL(file);
                    document.getElementById('cropperImage').src = fileURL;
                    document.getElementById('cropperModal').classList.remove('hidden');
                    
                    if (currentCropper) {
                        currentCropper.destroy();
                    }
                    
                    // Beri waktu sejenak agar modal tampil dan cropper bisa hitung dimensi dengan benar
                    setTimeout(() => {
                        currentCropper = new Cropper(document.getElementById('cropperImage'), {
                            viewMode: 1,
                            background: false,
                            autoCropArea: 1
                        });
                    }, 100);
                } else if (file.type.startsWith('video/')) {
                    openTrimmer(file);
                }
            }
            input.value = '';
        }

        function closeCropper() {
            document.getElementById('cropperModal').classList.add('hidden');
            if (currentCropper) {
                currentCropper.destroy();
                currentCropper = null;
            }
        }

        function cropAndSave() {
            if (!currentCropper) return;
            
            currentCropper.getCroppedCanvas({
                maxWidth: 1920,
                maxHeight: 1920
            }).toBlob(function(blob) {
                const fileName = currentFileObj ? currentFileObj.name : 'cropped-image.jpg';
                const fileType = currentFileObj ? currentFileObj.type : 'image/jpeg';
                const newFile = new File([blob], fileName, { type: fileType });
                
                processMediaAdd(newFile);
                closeCropper();
            }, currentFileObj ? currentFileObj.type : 'image/jpeg', 0.9);
        }

        let existingMediaUrls = [];
        let isReviewFormUnlocked = false;

        function renderMediaPreviews() {
            const container = document.getElementById('mediaPreviewContainer');
            container.innerHTML = '';
            
            // Update hidden input untuk media yang masih dipertahankan
            document.getElementById('existingMediaInput').value = existingMediaUrls.join('|');
            
            if (selectedMediaFiles.length > 0 || existingMediaUrls.length > 0) {
                container.classList.remove('hidden');
                
                // Tampilkan media yang sudah ada di database
                existingMediaUrls.forEach((m, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative w-24 h-24 rounded-xl overflow-hidden border border-purple-500/50 group flex-shrink-0';
                    let cleanUrl = m.split('#')[0];
                    let hashPart = m.includes('#') ? '#' + m.split('#')[1] : '';
                    let extension = cleanUrl.split('.').pop().toLowerCase();
                    let mediaElement = '';
                    if (['mp4', 'webm', 'ogg', 'mov'].includes(extension)) {
                        mediaElement = `<video src="/stream-media?path=${cleanUrl}${hashPart}" class="w-full h-full object-cover" muted loop autoplay></video>`;
                    } else {
                        mediaElement = `<img src="/${m}" class="w-full h-full object-cover">`;
                    }
                    
                    let deleteBtnClass = isReviewFormUnlocked ? "" : "hidden";
                    wrapper.innerHTML = `
                        ${mediaElement}
                        <div class="absolute top-0 right-0 bg-purple-500 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-bl-lg z-10">TERSIMPAN</div>
                        <div class="absolute inset-0 bg-black/60 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-20 ${deleteBtnClass}">
                            <button type="button" onclick="triggerReplace(${index}, true)" title="Ganti Foto/Video" class="p-1.5 bg-white/10 hover:bg-white/30 rounded-lg text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                            <button type="button" onclick="removeExistingMedia(${index})" title="Hapus Foto/Video" class="p-1.5 bg-white/10 hover:bg-red-500 rounded-lg text-red-500 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    `;
                    container.appendChild(wrapper);
                });

                // Tampilkan media yang baru dipilih untuk diupload
                selectedMediaFiles.forEach((file, index) => {
                    const fileURL = URL.createObjectURL(file);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative w-24 h-24 rounded-xl overflow-hidden border border-white/10 group flex-shrink-0';
                    
                    let mediaElement = '';
                    if (file.type.startsWith('image/')) {
                        mediaElement = `<img src="${fileURL}" class="w-full h-full object-cover">`;
                    } else if (file.type.startsWith('video/')) {
                        mediaElement = `<video src="${fileURL}" class="w-full h-full object-cover" muted loop autoplay></video>`;
                    }
                    
                    wrapper.innerHTML = `
                        ${mediaElement}
                        <div class="absolute inset-0 bg-black/60 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                            <button type="button" onclick="triggerReplace(${index}, false)" title="Ganti Foto/Video" class="p-1.5 bg-white/10 hover:bg-white/30 rounded-lg text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                            <button type="button" onclick="removeMedia(${index})" title="Hapus Foto/Video" class="p-1.5 bg-white/10 hover:bg-red-500 rounded-lg text-red-500 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    `;
                    container.appendChild(wrapper);
                });
            } else {
                container.classList.add('hidden');
            }
            
            updateFileInput();
        }

        function removeExistingMedia(index) {
            existingMediaUrls.splice(index, 1);
            renderMediaPreviews();
        }

        function removeMedia(index) {
            selectedMediaFiles.splice(index, 1);
            renderMediaPreviews();
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedMediaFiles.forEach(file => dataTransfer.items.add(file));
            document.getElementById('mediaSubmit').files = dataTransfer.files;
        }

        // --- VIDEO TRIMMER LOGIC ---
        let trimmerVideo = null;
        let videoCutsData = {}; 

        let trimStartInput = document.getElementById('trimStart');
        let trimEndInput = document.getElementById('trimEnd');
        let trimStartText = document.getElementById('trimStartText');
        let trimEndText = document.getElementById('trimEndText');
        let trimmerTrack = document.getElementById('trimmerTrack');

        document.getElementById('trimmerVideo').addEventListener('timeupdate', function() {
            let end = parseFloat(trimEndInput.value);
            if (this.currentTime >= end) {
                this.pause();
                this.currentTime = parseFloat(trimStartInput.value);
            }
        });

        function openTrimmer(file) {
            currentFileObj = file;
            trimmerVideo = document.getElementById('trimmerVideo');
            
            const fileURL = URL.createObjectURL(file);
            trimmerVideo.src = fileURL;
            
            trimmerVideo.onloadedmetadata = function() {
                let duration = trimmerVideo.duration;
                trimStartInput.max = duration;
                trimEndInput.max = duration;
                
                trimStartInput.value = 0;
                trimEndInput.value = duration;
                
                updateTrimmer();
                document.getElementById('videoTrimmerModal').classList.remove('hidden');
            };
        }

        function closeTrimmer() {
            document.getElementById('videoTrimmerModal').classList.add('hidden');
            if (trimmerVideo) {
                trimmerVideo.pause();
                trimmerVideo.src = "";
            }
            // Add directly without trim
            processMediaAdd(currentFileObj);
        }

        function saveTrim() {
            let start = parseFloat(trimStartInput.value);
            let end = parseFloat(trimEndInput.value);
            let duration = trimmerVideo.duration;
            
            start = Math.round(start * 10) / 10;
            end = Math.round(end * 10) / 10;
            
            // Only save if it's actually trimmed
            if (start > 0 || end < duration) {
                videoCutsData[currentFileObj.name] = { start: start, end: end };
                document.getElementById('videoCutsInput').value = JSON.stringify(videoCutsData);
            }
            
            document.getElementById('videoTrimmerModal').classList.add('hidden');
            if (trimmerVideo) {
                trimmerVideo.pause();
                trimmerVideo.src = "";
            }
            processMediaAdd(currentFileObj);
        }

        function updateTrimmer() {
            let start = parseFloat(trimStartInput.value);
            let end = parseFloat(trimEndInput.value);
            let max = parseFloat(trimStartInput.max);
            
            if (start >= end) {
                if (event && event.target.id === 'trimStart') {
                    trimStartInput.value = end - 0.1;
                    start = end - 0.1;
                } else {
                    trimEndInput.value = start + 0.1;
                    end = start + 0.1;
                }
            }
            
            trimStartText.innerText = start.toFixed(1) + 's';
            trimEndText.innerText = end.toFixed(1) + 's';
            
            let startPercent = (start / max) * 100;
            let endPercent = (end / max) * 100;
            
            trimmerTrack.style.left = startPercent + '%';
            trimmerTrack.style.right = (100 - endPercent) + '%';
            
            if (trimmerVideo) {
                if (event && event.target.id === 'trimStart') {
                    trimmerVideo.currentTime = start;
                } else if (event && event.target.id === 'trimEnd') {
                    trimmerVideo.currentTime = end;
                }
            }
        }
        // --- END VIDEO TRIMMER LOGIC ---

        function clearMedia() {
            selectedMediaFiles = [];
            renderMediaPreviews();
        }

        function salinKodeDetail(btn) {
            let kode = document.getElementById('detailLisensiCode').innerText;
            navigator.clipboard.writeText(kode);
            
            let originalText = btn.innerHTML;
            btn.innerHTML = `
                <svg class="w-5 h-5 mr-2 stroke-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Berhasil Disalin!
            `;
            btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.remove('bg-[#1A1D24]', 'border-white/10');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
                btn.classList.add('bg-[#1A1D24]', 'border-white/10');
            }, 2000);
        }

        // Logic Filter & Favorit
        let currentFilter = 'semua';
        let favorites = JSON.parse(localStorage.getItem('library_favorites')) || [];

        function toggleFavorite(event, gameId) {
            event.stopPropagation();
            const btn = event.currentTarget;
            const icon = btn.querySelector('.heart-icon');
            
            if (favorites.includes(gameId)) {
                favorites = favorites.filter(id => id !== gameId);
                icon.classList.remove('text-red-500');
                icon.classList.add('text-gray-400');
            } else {
                favorites.push(gameId);
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-red-500');
            }
            
            localStorage.setItem('library_favorites', JSON.stringify(favorites));
            
            // jika sedang difilter favorit, refilter lagi
            if (currentFilter === 'favorit') {
                filterGames();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.game-card').forEach(card => {
                const gameId = parseInt(card.getAttribute('data-id'));
                if (favorites.includes(gameId)) {
                    const icon = card.querySelector('.heart-icon');
                    if(icon) {
                        icon.classList.remove('text-gray-400');
                        icon.classList.add('text-red-500');
                    }
                }
            });
        });

        function setFilter(filterType, btnElement) {
            currentFilter = filterType;
            
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('font-bold', 'text-white', 'border-[#7C3AED]');
                tab.classList.add('font-medium', 'text-gray-500', 'border-transparent');
            });
            
            btnElement.classList.remove('font-medium', 'text-gray-500', 'border-transparent');
            btnElement.classList.add('font-bold', 'text-white', 'border-[#7C3AED]');
            
            filterGames();
        }

        function filterGames() {
            const searchQuery = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.game-card');
            const gridContainer = document.getElementById('gamesGrid');
            const emptyFilterState = document.getElementById('emptyFilterState');
            
            if (!gridContainer) return;

            let cardsArray = Array.from(cards);
            let visibleCount = 0;
            
            const thirtyDaysAgo = Math.floor(Date.now() / 1000) - (30 * 24 * 60 * 60);

            cardsArray.forEach(card => {
                const name = card.getAttribute('data-name');
                const gameId = parseInt(card.getAttribute('data-id'));
                const dateBeli = parseInt(card.getAttribute('data-date'));
                
                let matchSearch = name.includes(searchQuery);
                let matchFilter = true;
                
                if (currentFilter === 'favorit') {
                    matchFilter = favorites.includes(gameId);
                } else if (currentFilter === 'baru') {
                    matchFilter = dateBeli >= thirtyDaysAgo;
                }
                
                if (matchSearch && matchFilter) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            if (emptyFilterState) {
                if (visibleCount === 0) {
                    emptyFilterState.classList.remove('hidden');
                    emptyFilterState.classList.add('flex');
                    gridContainer.classList.add('hidden');
                    gridContainer.classList.remove('grid');
                } else {
                    emptyFilterState.classList.add('hidden');
                    emptyFilterState.classList.remove('flex');
                    gridContainer.classList.remove('hidden');
                    gridContainer.classList.add('grid');
                }
            }
        }
    </script>
@include('components.loading-overlay')
@include('components.toast-notification')
</body>
</html>