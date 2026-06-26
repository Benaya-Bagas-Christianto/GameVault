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
    <title>{{ $game ? 'Edit Game' : 'Tambah Game Baru' }} - Admin GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <style>
        body { background: #0A0C10; color: white; font-family: 'Inter', sans-serif; }
        input[type="file"]::-webkit-file-upload-button {
            background: #1a1a1a; color: #9ca3af; border: 1px solid #333;
            padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 12px;
        }
        input[type="file"]::-webkit-file-upload-button:hover { background: #222; color: white; }
        
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
            border: 2px solid #EC4899;
        }
        .trimmer-range::-moz-range-thumb {
            pointer-events: auto;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            cursor: grab;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
            border: 2px solid #EC4899;
        }
    </style>
</head>
<body class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#0A0C10] border-r border-white/5 flex flex-col fixed top-0 left-0 h-full z-20">
        <div class="p-6 border-b border-white/5">
            <a href="/admin/dashboard" class="flex items-center gap-3">
                <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault" class="w-8 h-8 drop-shadow-[0_0_15px_rgba(124,58,237,0.8)]">
                <div>
                    <p class="text-white font-black tracking-widest text-sm uppercase">GameVault</p>
                    <p class="text-purple-500 text-xs font-bold uppercase tracking-widest">Admin Panel</p>
                </div>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none"><path stroke="currentColor" stroke-width="2" d="M4 5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5ZM14 5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V5ZM4 16a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3ZM14 13a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-6Z"/></svg> Dashboard
            </a>
            <a href="/admin/games" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 text-sm font-bold">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 512.549 512.549" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-1)"><g><g><path d="M214.609,213.605h-21.333v-21.333c0-11.782-9.551-21.333-21.333-21.333c-11.782,0-21.333,9.551-21.333,21.333v21.333 h-21.333c-11.782,0-21.333,9.551-21.333,21.333c0,11.782,9.551,21.333,21.333,21.333h21.333v21.333 c0,11.782,9.551,21.333,21.333,21.333c11.782,0,21.333-9.551,21.333-21.333v-21.333h21.333c11.782,0,21.333-9.551,21.333-21.333 C235.943,223.156,226.391,213.605,214.609,213.605z"/><path d="M500.924,269.309c-12.915-49.866-33.027-100.133-53.035-141.855c-0.273-1.775-1.598-7.188-2.052-8.96 c-1.151-4.496-2.174-7.968-3.373-11.107c-3.242-8.489-6.404-13.576-15.335-16.973l-62.11-23.598 c-12.051-4.584-25.525-2.901-36.12,4.506l-2.105,1.472l-1.696,1.93c-1.745,1.985-4.607,5.115-7.619,8.168 c-0.822,0.829-0.822,0.829-1.629,1.624c-0.394,0.386-0.773,0.751-1.132,1.091H200.334c-1.075-1.142-1.075-1.142-1.908-2.056 c-2.723-3.006-5.302-6.099-6.856-8.047l-1.925-2.413l-2.53-1.768c-10.596-7.408-24.07-9.09-36.128-4.503L88.895,90.411 c-8.969,3.412-12.121,8.51-15.348,17.028c-1.184,3.126-2.188,6.564-3.337,11.078c-0.4,1.572-1.694,6.865-1.941,7.831 c-19.259,38.796-41.47,92.992-54.698,143.469C-7.884,351.686-3.78,412.893,40.752,442.363c13.705,9.031,31.564,7.364,43.661-3.43 l73.894-66.253c6.541-5.872,14.782-9.057,23.284-9.057h152.832c8.502,0,16.743,3.185,23.274,9.048l73.866,66.228 c12.24,10.921,30.427,12.721,44.123,3.176C518.544,411.991,522.09,351.038,500.924,269.309z M455.757,403.285l-69.566-62.373 c-14.316-12.852-32.697-19.956-51.767-19.956H181.592c-19.07,0-37.45,7.104-51.777,19.965l-69.758,62.544 c-19.658-18.052-21.296-61.46-5.213-122.832c12.346-47.111,33.478-98.672,51.546-134.307c1.399-2.752,2.112-5.086,3.217-9.411 c0.319-1.249,1.62-6.572,1.952-7.876c0.16-0.629,0.314-1.22,0.461-1.772l50.992-19.374c1.186,1.381,2.463,2.836,3.793,4.303 c1.113,1.222,1.113,1.222,2.273,2.457c9.927,10.492,13.691,13.62,24.204,13.62h128c10.147,0,13.785-2.861,24.417-13.27 c1.094-1.079,1.094-1.079,2.155-2.148c1.719-1.742,3.365-3.468,4.858-5.073l51.321,19.499c0.151,0.56,0.308,1.159,0.471,1.797 c0.382,1.493,1.69,6.836,1.931,7.787c1.066,4.206,1.73,6.435,2.992,9.058c18.908,39.428,38.104,87.404,50.193,134.082 C475.42,341.016,474.252,384.613,455.757,403.285z"/><path d="M342.609,192.271c11.776,0,21.333-9.557,21.333-21.333s-9.557-21.333-21.333-21.333 c-11.776,0-21.333,9.557-21.333,21.333S330.833,192.271,342.609,192.271z"/><path d="M342.609,234.938c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333c11.776,0,21.333-9.557,21.333-21.333 S354.385,234.938,342.609,234.938z"/><path d="M299.943,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S311.719,192.271,299.943,192.271z"/><path d="M385.276,192.271c-11.776,0-21.333,9.557-21.333,21.333s9.557,21.333,21.333,21.333s21.333-9.557,21.333-21.333 S397.052,192.271,385.276,192.271z"/></g></g></g></svg> Kelola Game
            </a>
            <a href="/admin/transaksi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg> Transaksi
            </a>
                        <a href="/admin/refunds" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> Permintaan Refund
                @if(isset($pendingRefundsCount) && $pendingRefundsCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full">{{ $pendingRefundsCount }}</span>
                @endif
            </a>
<a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 7C15 8.65685 13.6569 10 12 10C10.3431 10 9 8.65685 9 7C9 5.34315 10.3431 4 12 4C13.6569 4 15 5.34315 15 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 10C18.1046 10 19 9.10457 19 8C19 6.89543 18.1046 6 17 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 19C21 16.5147 19.389 14.4061 17.1355 13.6279" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg> Pengguna
            </a>
            
        </nav>
        <div class="p-4 border-t border-white/5">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:text-white hover:bg-white/5 transition-all text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Kembali ke Store
            </a>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="ml-64 flex-1 p-8 lg:p-10">

        {{-- Header --}}
        <div class="mb-8 border-b border-white/5 pb-6">
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-3">
                <a href="/admin/games" class="hover:text-purple-400 transition-colors">Kelola Game</a>
                <span>/</span>
                <span class="text-gray-300">{{ $game ? 'Edit Game' : 'Tambah Game Baru' }}</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-widest uppercase mb-1">
                {{ $game ? 'Edit Data Game' : 'Tambah Game Baru' }}
            </h1>
            <p class="text-gray-500 text-sm">
                {{ $game ? 'Perbarui informasi game yang sudah ada di katalog.' : 'Isi detail game baru untuk ditambahkan ke katalog GameVault.' }}
            </p>
        </div>



        {{-- Form --}}
        <form action="{{ $game ? '/admin/games/update/'.$game->id : '/admin/games/simpan' }}" method="POST" enctype="multipart/form-data" id="game-form" onsubmit="showLoadingOverlay()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kolom Kiri: Info Utama --}}
                <div class="lg:col-span-2 space-y-5">
                    <div class="bg-[#12151C] border border-white/5 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-1.5 h-6 bg-purple-500 rounded-full"></div>
                            <h2 class="text-white font-bold uppercase tracking-widest text-sm">Informasi Utama</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri: Nama, Platform, Label, Genre, Harga --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Game <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ $game->name ?? '' }}" required
                                           placeholder="Contoh: Cyberpunk 2077"
                                           class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-colors text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Platform <span class="text-red-500">*</span></label>
                                    <input type="text" name="platform" value="{{ $game->platform ?? '' }}" required 
                                           class="w-full bg-white/5 border border-white/10 text-white text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 transition-colors placeholder-gray-500"
                                           placeholder="Contoh: PC, PlayStation, Xbox">
                                    <p class="text-gray-600 text-xs mt-1">Pisahkan dengan koma jika lebih dari satu</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Label Spesial / Edisi (Opsional)</label>
                                    <input type="text" name="console_edition" value="{{ $game->console_edition ?? '' }}" 
                                           class="w-full bg-white/5 border border-white/10 text-white text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 transition-colors placeholder-gray-500"
                                           placeholder="Contoh: PS4, PS5, Remastered">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Genre <span class="text-red-500">*</span></label>
                                    <input type="text" name="genre" value="{{ $game->genre ?? '' }}" required
                                           placeholder="Action, RPG, Strategy..."
                                           class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-colors text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-bold">Rp</span>
                                        <input type="text" name="price" id="price-input" value="{{ $game ? number_format($game->price, 0, ',', '.') : '0' }}" required
                                               class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-colors text-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan: Developer, Publisher, Rilis --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Developer</label>
                                    <input type="text" name="developer" value="{{ $game->developer ?? '' }}"
                                           class="w-full bg-white/5 border border-white/10 text-white text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 transition-colors placeholder-gray-500"
                                           placeholder="Contoh: Naughty Dog">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Publisher</label>
                                    <input type="text" name="publisher" value="{{ $game->publisher ?? '' }}"
                                           class="w-full bg-white/5 border border-white/10 text-white text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 transition-colors placeholder-gray-500"
                                           placeholder="Contoh: Sony Interactive">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Rilis</label>
                                    <input type="date" name="release_date" value="{{ $game->release_date ? \Carbon\Carbon::parse($game->release_date)->format('Y-m-d') : '' }}"
                                           class="w-full bg-white/5 border border-white/10 text-white text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 transition-colors placeholder-gray-500 [color-scheme:dark]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#12151C] border border-white/5 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-1.5 h-6 bg-purple-500 rounded-full"></div>
                            <h2 class="text-white font-bold uppercase tracking-widest text-sm">Deskripsi & Spesifikasi</h2>
                        </div>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Sinopsis Singkat</label>
                                <textarea name="synopsis" rows="2"
                                          placeholder="Ringkasan singkat tentang game ini..."
                                          class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-colors text-sm resize-none">{{ $game->synopsis ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Deskripsi Lengkap</label>
                                <textarea name="description" id="editor-description" rows="5"
                                          placeholder="Deskripsi lengkap tentang gameplay, fitur, dan cerita game..."
                                          class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-colors text-sm resize-none">{{ $game->description ?? '' }}</textarea>
                            </div>
                            
                            <div class="pt-4 border-t border-white/5">
                                <p class="text-xs font-bold text-purple-400 uppercase tracking-widest mb-3">System Requirements (Khusus Game PC)</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Spesifikasi Minimum</label>
                                        <textarea name="sys_req_min" id="editor-sys-min" rows="5"
                                                  placeholder="OS: Windows 10 64-bit&#10;CPU: Intel Core i3 / Ryzen 3&#10;RAM: 8 GB&#10;GPU: GTX 1050 / RX 560&#10;Storage: 50 GB"
                                                  class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-gray-300 placeholder-gray-600 focus:outline-none focus:border-purple-500 transition-colors text-sm resize-none leading-relaxed">{{ $game->sys_req_min ?? '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Spesifikasi Recommended</label>
                                        <textarea name="sys_req_rec" id="editor-sys-rec" rows="5"
                                                  placeholder="OS: Windows 11 64-bit&#10;CPU: Intel Core i5 / Ryzen 5&#10;RAM: 16 GB&#10;GPU: RTX 3060 / RX 6600&#10;Storage: 50 GB SSD"
                                                  class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-gray-300 placeholder-gray-600 focus:outline-none focus:border-purple-500 transition-colors text-sm resize-none leading-relaxed">{{ $game->sys_req_rec ?? '' }}</textarea>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-xs mt-2">*Kosongkan jika game eksklusif konsol.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Kotak Galeri Media (Sekarang ada DI DALAM kolom kiri) --}}
                    <div class="bg-[#12151C] border border-white/5 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-1.5 h-6 bg-pink-500 rounded-full"></div>
                            <h2 class="text-white font-bold uppercase tracking-widest text-sm">Media Galeri Tambahan</h2>
                        </div>
                        <div class="space-y-5">
                            @if($game && $game->galleries && $game->galleries->where('type', 'image')->count() > 0)
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Gambar Galeri Saat Ini</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($game->galleries->where('type', 'image') as $img)
                                    <div id="gallery-item-{{ $img->id }}" class="relative group aspect-[16/9] rounded-xl overflow-hidden border border-white/10 bg-black transition-all duration-300">
                                        <img id="preview_gal_{{ $img->id }}" src="{{ asset('assets/galleries/' . $img->path) }}" class="w-full h-full object-cover transition-all group-hover:opacity-40">
                                        
                                        {{-- Overlay Tombol Aksi --}}
                                        <div class="absolute top-2 right-2 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            {{-- Tombol Ubah --}}
                                            <button type="button" onclick="document.getElementById('replace_gal_{{ $img->id }}').click()" title="Ganti Gambar" class="w-7 h-7 bg-blue-500/90 hover:bg-blue-600 text-white rounded-lg flex items-center justify-center shadow-md backdrop-blur-sm transition-colors">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="m3.99 16.854-1.314 3.504a.75.75 0 0 0 .966.965l3.503-1.314a3 3 0 0 0 1.068-.687L18.36 9.175s-.354-1.061-1.414-2.122c-1.06-1.06-2.122-1.414-2.122-1.414L4.677 15.786a3 3 0 0 0-.687 1.068zm12.249-12.63 1.383-1.383c.248-.248.579-.406.925-.348.487.08 1.232.322 1.934 1.025.703.703.945 1.447 1.025 1.934.058.346-.1.677-.348.925L19.774 7.76s-.353-1.06-1.414-2.12c-1.06-1.062-2.121-1.415-2.121-1.415z"/></svg>
                                            </button>
                                            
                                            {{-- Tombol Hapus --}}
                                            <button type="button" onclick="confirmDeleteGallery('{{ $img->id }}')" title="Hapus Gambar" class="w-7 h-7 bg-red-500/90 hover:bg-red-600 text-white rounded-lg flex items-center justify-center shadow-md backdrop-blur-sm transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        
                                        {{-- Hidden Input untuk Edit --}}
                                        <input type="file" name="replace_gallery[{{ $img->id }}]" id="replace_gal_{{ $img->id }}" accept="image/*" class="hidden" onchange="previewGalleryImage(this, 'preview_gal_{{ $img->id }}')">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tambahkan Gambar Galeri Baru (Opsional)</label>
                                <input type="file" id="new-gallery-input" name="gallery_images[]" multiple accept="image/*"
                                       class="w-full text-sm text-gray-400 bg-white/5 border border-white/10 rounded-xl px-3 py-2 cursor-pointer focus:outline-none focus:border-pink-500 transition-colors">
                                <p class="text-gray-600 text-xs mt-2">Bisa pilih/blok maksimal 10 gambar sekaligus. Format: JPG, PNG, WEBP.</p>
                                
                                {{-- Preview Container untuk Upload Baru --}}
                                <div id="new-gallery-preview" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 hidden">
                                    {{-- JS akan memasukkan preview gambar di sini --}}
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Video Trailer (Opsional)</label>
                                @php
                                    $videoUrl = '';
                                    $videoType = '';
                                    $ytStart = '';
                                    $ytEnd = '';
                                    $fileStart = '';
                                    $fileEnd = '';
                                    if ($game && $game->galleries) {
                                        $video = $game->galleries->where('type', 'video')->first();
                                        if ($video) {
                                            $videoUrl = $video->path;
                                            $videoType = (str_contains($video->path, 'youtube.com') || str_contains($video->path, 'youtu.be')) ? 'url' : 'file';
                                            if ($videoType == 'url') {
                                                parse_str(parse_url($videoUrl, PHP_URL_QUERY) ?? '', $query);
                                                $ytStart = $query['start'] ?? '';
                                                $ytEnd = $query['end'] ?? '';
                                                $videoUrl = preg_replace('/([?&])(start|end)=[^&]+(&|$)/', '$1', $videoUrl);
                                                $videoUrl = rtrim($videoUrl, '?&');
                                            } else if ($videoType == 'file' && str_contains($videoUrl, '#t=')) {
                                                preg_match('/#t=([\d.]+),?([\d.]*)/', $videoUrl, $matches);
                                                $fileStart = $matches[1] ?? '';
                                                $fileEnd = $matches[2] ?? '';
                                                $videoUrl = preg_replace('/#.*$/', '', $videoUrl);
                                            }
                                        }
                                    }
                                @endphp

                                @if($videoUrl && $videoType == 'file')
                                    <div class="mb-3 relative w-full aspect-video rounded-xl overflow-hidden border border-white/10 bg-black group">
                                        <video src="{{ asset('assets/galleries/' . $videoUrl) . ($fileStart || $fileEnd ? '#t=' . ($fileStart ?: 0) . ',' . ($fileEnd ?: '') : '') }}" controls class="w-full h-full object-contain"></video>
                                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <label class="flex items-center gap-2 px-3 py-1.5 bg-red-500/90 hover:bg-red-600 text-white text-xs font-bold rounded-lg cursor-pointer shadow-md backdrop-blur-sm transition-colors">
                                                <input type="checkbox" name="remove_video" value="1" class="rounded bg-red-900 border-red-500 text-white focus:ring-red-500">
                                                Hapus Video
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mb-3">Video Trailer saat ini. Upload file baru atau isi link YouTube untuk mengganti. Centang "Hapus Video" jika ingin mengosongkan.</p>
                                @endif

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Pilih File dari Komputer</label>
                                        
                                        {{-- PREVIEW VIDEO BARU --}}
                                        <div id="new-video-preview-container" class="mb-3 relative w-full aspect-video rounded-xl overflow-hidden border border-pink-500/50 bg-black hidden">
                                            <div class="absolute top-2 left-2 z-10 bg-pink-500 text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-lg tracking-widest uppercase">Video Baru</div>
                                            <video id="new-video-preview" src="" controls class="w-full h-full object-contain"></video>
                                        </div>

                                        <div class="relative">
                                            <input type="hidden" name="video_cuts" id="videoCutsInput" value="{}">
                                            <input type="file" name="gallery_video_file" id="video-file-input" accept="video/mp4,video/webm,video/ogg"
                                                   class="w-full text-sm text-gray-400 bg-white/5 border border-white/10 rounded-xl px-3 py-2 cursor-pointer focus:outline-none focus:border-pink-500 transition-colors"
                                                   onchange="handleAdminVideoSelect(this)">
                                            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-500 hover:text-white" onclick="document.getElementById('video-file-input').value=''; document.getElementById('gallery-video-url').disabled = false; document.getElementById('videoCutsInput').value = '{}'; document.getElementById('file_video_start').value = ''; document.getElementById('file_video_end').value = ''; document.getElementById('new-video-preview-container').classList.add('hidden'); document.getElementById('new-video-preview').src = '';" title="Batal pilih file">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                        <div class="flex gap-4 mt-3">
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Mulai (Detik)</label>
                                                <input type="number" name="file_video_start" id="file_video_start" value="{{ $fileStart }}" placeholder="0" min="0" step="0.1" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-pink-500">
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Selesai (Detik)</label>
                                                <input type="number" name="file_video_end" id="file_video_end" value="{{ $fileEnd }}" placeholder="Kosongkan jika sampai habis" min="0" step="0.1" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-pink-500">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-px bg-white/5"></div>
                                        <span class="text-[10px] text-gray-600 font-bold tracking-widest uppercase">ATAU</span>
                                        <div class="flex-1 h-px bg-white/5"></div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Gunakan Link YouTube</label>
                                        <input type="url" name="gallery_video" id="gallery-video-url" value="{{ $videoType == 'url' ? $videoUrl : '' }}" placeholder="Contoh: https://www.youtube.com/watch?v=..."
                                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500/30 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                        
                                        <div class="flex gap-4 mt-3">
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Mulai (Detik)</label>
                                                <input type="number" name="youtube_start" value="{{ $ytStart }}" placeholder="0" min="0" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-pink-500">
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Selesai (Detik)</label>
                                                <input type="number" name="youtube_end" value="{{ $ytEnd }}" placeholder="Kosongkan jika sampai habis" min="0" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-pink-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Kolom Kanan: Cover & Aksi --}}
                <div class="space-y-5">
                    <div class="bg-[#12151C] border border-white/5 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-1.5 h-6 bg-yellow-500 rounded-full"></div>
                            <h2 class="text-white font-bold uppercase tracking-widest text-sm">Cover Game</h2>
                        </div>

                        @if($game && $game->image)
                            <div class="mb-4" id="current-cover-container">
                                <p class="text-xs text-gray-500 mb-2 uppercase tracking-widest font-bold">Cover Saat Ini <span class="text-purple-400 lowercase font-normal ml-1">(akan berubah saat file baru dipilih)</span></p>
                                <img id="image-preview" src="{{ asset('assets/' . $game->image) }}"
                                     class="w-full aspect-[3/4] object-cover rounded-xl border border-white/10"
                                     alt="{{ $game->name }}">
                            </div>
                        @else
                            <div id="preview-container" class="mb-4 hidden">
                                <p class="text-xs text-gray-500 mb-2 uppercase tracking-widest font-bold">Preview</p>
                                <img id="image-preview" src="" class="w-full aspect-[3/4] object-cover rounded-xl border border-white/10" alt="Preview">
                            </div>
                            <div id="placeholder" class="mb-4 w-full aspect-[3/4] bg-white/5 border-2 border-dashed border-white/10 rounded-xl flex flex-col items-center justify-center text-gray-600">
                                <svg class="w-10 h-10 mb-2 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="m3.99 16.854-1.314 3.504a.75.75 0 0 0 .966.965l3.503-1.314a3 3 0 0 0 1.068-.687L18.36 9.175s-.354-1.061-1.414-2.122c-1.06-1.06-2.122-1.414-2.122-1.414L4.677 15.786a3 3 0 0 0-.687 1.068zm12.249-12.63 1.383-1.383c.248-.248.579-.406.925-.348.487.08 1.232.322 1.934 1.025.703.703.945 1.447 1.025 1.934.058.346-.1.677-.348.925L19.774 7.76s-.353-1.06-1.414-2.12c-1.06-1.062-2.121-1.415-2.121-1.415z"/></svg>
                                <p class="text-xs">Preview cover</p>
                            </div>
                        @endif

                        <input type="hidden" name="remove_cover" id="remove_cover" value="0">
                        
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            {{ $game ? 'Ganti/Hapus Cover' : 'Upload Cover' }} {{ !$game ? '*' : '' }}
                        </label>
                        
                        <div class="flex flex-col gap-3">
                            <label for="image-input" class="group relative flex items-center justify-center w-full px-4 py-4 bg-white/5 border-2 border-dashed border-white/10 hover:border-purple-500 hover:bg-purple-500/10 rounded-xl cursor-pointer transition-all">
                                <div class="flex items-center gap-3 text-gray-400 group-hover:text-purple-400 transition-colors">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span class="font-bold text-sm" id="file-name-display">Pilih File Gambar Baru</span>
                                </div>
                                <input type="file" name="image" accept="image/*" id="image-input" class="hidden" {{ $game ? '' : 'required' }}>
                            </label>

                            @if($game && $game->image)
                            <button type="button" id="btn-remove-cover" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl hover:bg-red-500/20 transition-colors text-sm font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Kosongkan Cover
                            </button>
                            @endif
                        </div>
                        <p class="text-gray-600 text-xs mt-3">Format: JPG, PNG, WEBP. Rasio 3:4 direkomendasikan.</p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="bg-[#12151C] border border-white/5 rounded-2xl p-6 space-y-3">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3.5 bg-[#7C3AED] text-white font-black rounded-xl hover:brightness-110 hover:-translate-y-0.5 transition-all text-sm shadow-lg shadow-purple-500/20">
                            @if($game)
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="m3.99 16.854-1.314 3.504a.75.75 0 0 0 .966.965l3.503-1.314a3 3 0 0 0 1.068-.687L18.36 9.175s-.354-1.061-1.414-2.122c-1.06-1.06-2.122-1.414-2.122-1.414L4.677 15.786a3 3 0 0 0-.687 1.068zm12.249-12.63 1.383-1.383c.248-.248.579-.406.925-.348.487.08 1.232.322 1.934 1.025.703.703.945 1.447 1.025 1.934.058.346-.1.677-.348.925L19.774 7.76s-.353-1.06-1.414-2.12c-1.06-1.062-2.121-1.415-2.121-1.415z"/></svg> Simpan Perubahan
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambahkan Game
                            @endif
                        </button>
                        <a href="/admin/games"
                           class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-transparent border border-white/10 text-gray-400 font-bold rounded-xl hover:border-red-500 hover:text-red-500 active:border-red-600 active:text-red-600 transition-all text-sm">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </form>

    </main>

    {{-- Cropper Modal --}}
    <div id="cropperModal" class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center hidden p-4">
        <div class="bg-[#12151C] border border-white/10 rounded-2xl p-6 max-w-2xl w-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-white font-bold text-lg">Sesuaikan Cover</h3>
                <button type="button" id="btnCancelCrop" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="w-full bg-black rounded-xl overflow-hidden mb-4" style="max-height: 60vh;">
                <img id="imageToCrop" src="" class="max-w-full">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('btnCancelCrop').click()" class="px-5 py-2.5 bg-transparent border border-white/10 text-gray-300 font-bold rounded-xl hover:bg-white/5 transition-colors text-sm">Batal</button>
                <button type="button" id="btnCrop" class="px-5 py-2.5 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-colors text-sm">Potong & Simpan</button>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Galeri --}}
    <div id="deleteGalleryModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2">Hapus Gambar?</h3>
            <p class="text-gray-400 text-center text-sm mb-6 leading-relaxed">Yakin ingin menghapus gambar galeri ini secara permanen?</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteGalleryModal()" class="flex-1 px-4 py-3 bg-transparent border border-white/10 text-gray-300 font-bold rounded-xl hover:bg-white/5 transition-colors text-sm">Batal</button>
                <button type="button" id="confirmDeleteGalleryBtn" class="flex-1 px-4 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors text-sm text-center flex items-center justify-center">Ya, Hapus</button>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Sukses --}}
    <div id="successModal" class="fixed inset-0 z-[150] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 {{ session('pesan') ? '' : 'hidden' }}">
        <div id="successModalContent" class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-green-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2">Berhasil!</h3>
            <p id="successModalText" class="text-gray-400 text-center text-sm mb-6 leading-relaxed">{{ session('pesan') ?? 'Tindakan berhasil.' }}</p>
            <button type="button" onclick="closeSuccessModal()" class="w-full px-4 py-3 bg-green-500/10 text-green-500 border border-green-500/20 font-bold rounded-xl hover:bg-green-500 hover:text-white transition-colors text-sm text-center">OK</button>
        </div>
    </div>

    <script>
        function showToast() {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.classList.remove('hidden');
                
                // Trigger reflow
                void toast.offsetWidth;
                
                toast.classList.remove('translate-y-20', 'opacity-0');
                
                setTimeout(() => {
                    toast.classList.add('translate-y-20', 'opacity-0');
                    setTimeout(() => toast.classList.add('hidden'), 300);
                }, 3000);
            }
        }

        // Preview gambar galeri yang di-edit secara spesifik
        function previewGalleryImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    if (preview) {
                        preview.src = e.target.result;
                    }
                }
                reader.readAsDataURL(file);
            }
        }

        // Preview upload gambar galeri baru (Multiple)
        const newGalleryInput = document.getElementById('new-gallery-input');
        if (newGalleryInput) {
            newGalleryInput.addEventListener('change', function(e) {
                const previewContainer = document.getElementById('new-gallery-preview');
                previewContainer.innerHTML = ''; // Hapus preview lama
                
                if (this.files.length > 0) {
                    previewContainer.classList.remove('hidden');
                    
                    Array.from(this.files).forEach((file) => {
                        if (!file.type.match('image.*')) return;
                        
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const div = document.createElement('div');
                            div.className = 'relative aspect-[16/9] rounded-xl overflow-hidden border border-pink-500/50 bg-black animate-[pulse_0.5s_ease-out_1]';
                            div.innerHTML = `
                                <img src="${ev.target.result}" class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2 bg-pink-500 text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-lg tracking-widest uppercase">Baru</div>
                            `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    previewContainer.classList.add('hidden');
                }
            });
        }

        // Preview cover utama sebelum upload (Dengan Cropper.js)
        let cropper;
        const input = document.getElementById('image-input');
        const cropperModal = document.getElementById('cropperModal');
        const imageToCrop = document.getElementById('imageToCrop');
        const btnCrop = document.getElementById('btnCrop');
        const btnCancelCrop = document.getElementById('btnCancelCrop');

        if (input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                const reader = new FileReader();
                reader.onload = function(ev) {
                    imageToCrop.src = ev.target.result;
                    cropperModal.classList.remove('hidden');
                    
                    if (cropper) { cropper.destroy(); }
                    
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 3 / 4,
                        viewMode: 1,
                        background: false,
                        modal: true,
                        autoCropArea: 1,
                    });
                };
                reader.readAsDataURL(file);
            });
            
            btnCancelCrop.addEventListener('click', () => {
                cropperModal.classList.add('hidden');
                if (cropper) cropper.destroy();
                input.value = ''; // Reset input
            });

            btnCrop.addEventListener('click', () => {
                if (!cropper) return;
                
                const canvas = cropper.getCroppedCanvas({
                    width: 600,
                    height: 800
                });
                
                canvas.toBlob((blob) => {
                    const file = new File([blob], 'cover_cropped.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files; // Replace file in input
                    
                    // Update preview
                    const preview = document.getElementById('image-preview');
                    const container = document.getElementById('preview-container');
                    const placeholder = document.getElementById('placeholder');
                    if (preview) {
                        preview.src = canvas.toDataURL('image/jpeg');
                        if (container) container.classList.remove('hidden');
                        if (placeholder) placeholder.classList.add('hidden');
                    }
                    
                    cropperModal.classList.add('hidden');
                    cropper.destroy();
                }, 'image/jpeg', 0.9);
            });
        }

        // Tampilkan nama file yang dipilih (karena input aslinya di-hide)
        if (input) {
            input.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    document.getElementById('file-name-display').innerText = e.target.files[0].name;
                }
            });
        }

        // Logika Hapus Cover
        const btnRemoveCover = document.getElementById('btn-remove-cover');
        const removeCoverInput = document.getElementById('remove_cover');
        if (btnRemoveCover) {
            btnRemoveCover.addEventListener('click', function() {
                // Set input hidden untuk dikirim ke server
                removeCoverInput.value = '1';
                
                // Reset file input
                if (input) input.value = '';
                document.getElementById('file-name-display').innerText = 'Pilih File Gambar Baru';
                
                // Sembunyikan preview cover
                const currentContainer = document.getElementById('current-cover-container');
                if (currentContainer) {
                    currentContainer.classList.add('hidden');
                }
                
                // Ubah tampilan tombol hapus sebagai indikator
                btnRemoveCover.innerHTML = 'Cover Dikosongkan (Klik Simpan Perubahan)';
                btnRemoveCover.classList.replace('bg-red-500/10', 'bg-gray-500/10');
                btnRemoveCover.classList.replace('text-red-400', 'text-gray-400');
                btnRemoveCover.classList.replace('border-red-500/20', 'border-gray-500/20');
                btnRemoveCover.classList.replace('hover:bg-red-500/20', 'hover:bg-gray-500/20');
            });
        }

        // Logika Modal Hapus Galeri (Dengan AJAX)
        let deleteGalleryId = null;

        function confirmDeleteGallery(id) {
            deleteGalleryId = id;
            
            const btn = document.getElementById('confirmDeleteGalleryBtn');
            btn.onclick = function(e) {
                e.preventDefault();
                processDeleteGallery();
            };
            
            const modal = document.getElementById('deleteGalleryModal');
            modal.classList.remove('hidden');
            
            // Animasi masuk
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
        }

        function processDeleteGallery() {
            if (!deleteGalleryId) return;
            
            // Tutup modal hapus
            closeDeleteGalleryModal();
            
            // Lakukan AJAX request tanpa memuat ulang halaman
            fetch(`/admin/games/hapus-galeri/${deleteGalleryId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    // Hapus gambar dari DOM dengan animasi
                    const item = document.getElementById('gallery-item-' + deleteGalleryId);
                    if (item) {
                        item.style.transition = 'all 0.3s';
                        item.style.transform = 'scale(0.8)';
                        item.style.opacity = '0';
                        setTimeout(() => item.remove(), 300);
                    }
                    
                    // Tampilkan modal sukses
                    const modal = document.getElementById('successModal');
                    const content = document.getElementById('successModalContent');
                    const text = document.getElementById('successModalText');
                    
                    if (text) text.innerText = data.message;
                    
                    if (modal) {
                        modal.classList.remove('hidden');
                        setTimeout(() => {
                            modal.classList.remove('opacity-0');
                            content.classList.remove('scale-95');
                            content.classList.add('scale-100');
                        }, 50);
                    }
                }
            });
        }

        function closeDeleteGalleryModal() {
            const modal = document.getElementById('deleteGalleryModal');
            
            // Animasi keluar
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            modal.firstElementChild.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Logika Modal Sukses Global
        @if(session('pesan'))
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('successModal');
            const content = document.getElementById('successModalContent');
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }, 50);
            }
        });
        @endif

        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            const content = document.getElementById('successModalContent');
            
            // Animasi keluar
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Simpan posisi scroll ke sessionStorage saat ada aksi submit atau hapus
        function saveScrollPosition() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        }

        document.querySelectorAll('button[type="submit"], button[title="Hapus Gambar"]').forEach(btn => {
            btn.addEventListener('click', saveScrollPosition);
        });

        // Kembalikan posisi scroll setelah halaman dimuat ulang
        window.addEventListener('load', function() {
            const scrollPos = sessionStorage.getItem('scrollPosition');
            if (scrollPos) {
                // Matikan scroll otomatis browser
                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual';
                }
                
                // Coba kembalikan posisi scroll beberapa kali untuk mengantisipasi load gambar
                let attempts = 0;
                const scrollInterval = setInterval(() => {
                    window.scrollTo(0, parseInt(scrollPos));
                    attempts++;
                    if (attempts >= 5) {
                        clearInterval(scrollInterval);
                        sessionStorage.removeItem('scrollPosition');
                        if ('scrollRestoration' in history) {
                            history.scrollRestoration = 'auto';
                        }
                    }
                }, 200);
            }
        });
    </script>

    {{-- MODAL TRIMMER VIDEO --}}
    <div id="videoTrimmerModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity">
        <div class="bg-[#0A0C10] rounded-2xl border border-white/10 shadow-[0_0_50px_rgba(124,58,237,0.3)] w-full max-w-2xl overflow-hidden relative flex flex-col mx-4" onclick="event.stopPropagation()">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-bold">Potong Video (Opsional)</h3>
                <button type="button" onclick="closeTrimmer()" class="text-gray-400 hover:text-white transition-colors">?</button>
            </div>
            <div class="p-6 bg-[#12151C] flex flex-col justify-center items-center gap-6">
                <video id="trimmerVideo" src="" class="max-h-[40vh] max-w-full rounded-xl bg-black" controls></video>
                
                <div class="w-full flex flex-col gap-2 relative mt-4">
                    <div class="flex justify-between text-xs text-gray-400 font-medium px-1">
                        <span id="trimStartText">0s</span>
                        <span id="trimEndText">0s</span>
                    </div>
                    <div class="relative w-full h-3 bg-gray-800 rounded-full flex items-center">
                        <div id="trimmerTrack" class="absolute h-full bg-[#EC4899] rounded-full pointer-events-none" style="left: 0%; right: 0%;"></div>
                        <input type="range" id="trimStart" min="0" max="100" value="0" step="0.1" class="absolute w-full h-full appearance-none bg-transparent trimmer-range" oninput="updateTrimmer()">
                        <input type="range" id="trimEnd" min="0" max="100" value="100" step="0.1" class="absolute w-full h-full appearance-none bg-transparent trimmer-range" oninput="updateTrimmer()">
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">Geser tombol untuk menentukan batas waktu mulai dan selesai.</p>
                </div>
            </div>
            <div class="p-6 border-t border-white/10 flex justify-end gap-3">
                <button type="button" onclick="closeTrimmer()" class="px-5 py-2 rounded-xl text-gray-400 hover:text-white font-medium transition-colors">Lewati / Batal</button>
                <button type="button" onclick="saveTrim()" class="px-5 py-2 rounded-xl bg-[#EC4899] hover:bg-[#BE185D] text-white font-bold transition-colors">Simpan Potongan</button>
            </div>
        </div>
    </div>
    
    <script>
        let trimmerVideo = null;
        let videoCutsData = {}; 

        let trimStartInput = document.getElementById('trimStart');
        let trimEndInput = document.getElementById('trimEnd');
        let trimStartText = document.getElementById('trimStartText');
        let trimEndText = document.getElementById('trimEndText');
        let trimmerTrack = document.getElementById('trimmerTrack');

        if (document.getElementById('trimmerVideo')) {
            document.getElementById('trimmerVideo').addEventListener('timeupdate', function() {
                let end = parseFloat(trimEndInput.value);
                if (this.currentTime >= end) {
                    this.pause();
                    this.currentTime = parseFloat(trimStartInput.value);
                }
            });
        }

        function handleAdminVideoSelect(input) {
            document.getElementById('gallery-video-url').value = '';
            document.getElementById('gallery-video-url').disabled = true;
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.type.startsWith('video/')) {
                    trimmerVideo = document.getElementById('trimmerVideo');
                    const fileURL = URL.createObjectURL(file);
                    
                    // Set trimmer source
                    trimmerVideo.src = fileURL;

                    // Tampilkan preview langsung di form Admin
                    let newPreviewContainer = document.getElementById('new-video-preview-container');
                    let newPreviewVideo = document.getElementById('new-video-preview');
                    if (newPreviewContainer && newPreviewVideo) {
                        newPreviewContainer.classList.remove('hidden');
                        newPreviewVideo.src = fileURL;
                    }

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
            }
        }

        function closeTrimmer() {
            document.getElementById('videoTrimmerModal').classList.add('hidden');
            if (trimmerVideo) {
                trimmerVideo.pause();
                trimmerVideo.src = "";
            }
        }

        function saveTrim() {
            let start = parseFloat(trimStartInput.value);
            let end = parseFloat(trimEndInput.value);
            let duration = trimmerVideo.duration;
            
            start = Math.round(start * 10) / 10;
            end = Math.round(end * 10) / 10;
            
            if (start > 0 || end < duration) {
                videoCutsData['admin_video'] = { start: start, end: end };
                document.getElementById('videoCutsInput').value = JSON.stringify(videoCutsData);
                if (document.getElementById('file_video_start')) document.getElementById('file_video_start').value = start.toFixed(1);
                if (document.getElementById('file_video_end')) document.getElementById('file_video_end').value = end.toFixed(1);
            } else {
                document.getElementById('videoCutsInput').value = "{}";
            }
            
            document.getElementById('videoTrimmerModal').classList.add('hidden');
            if (trimmerVideo) {
                trimmerVideo.pause();
                trimmerVideo.src = "";
            }
        }

        function cancelTrim() {
            document.getElementById('videoCutsInput').value = "{}";
            if (document.getElementById('file_video_start')) document.getElementById('file_video_start').value = '';
            if (document.getElementById('file_video_end')) document.getElementById('file_video_end').value = '';
            
            document.getElementById('videoTrimmerModal').classList.add('hidden');
            if (trimmerVideo) {
                trimmerVideo.pause();
                trimmerVideo.src = "";
            }
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

        document.getElementById('image-upload').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('preview-container').classList.remove('hidden');
                    document.getElementById('upload-prompt').classList.add('hidden');
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        document.getElementById('remove-image').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('image-upload').value = '';
            document.getElementById('preview-container').classList.add('hidden');
            document.getElementById('upload-prompt').classList.remove('hidden');
            // If editing, might need to handle removing existing image
        });

        const priceInput = document.getElementById('price-input');
        if (priceInput) {
            priceInput.addEventListener('input', function(e) {
                let val = this.value.replace(/[^0-9]/g, '');
                if (val !== '') {
                    this.value = parseInt(val, 10).toLocaleString('id-ID');
                } else {
                    this.value = '';
                }
            });
        }
    </script>

    @include('components.loading-overlay')
    @include('components.success-modal')
    @include('components.toast-notification')

    <style>
    /* CKEditor 5 Dark Theme Overrides */
    .ck.ck-editor__main>.ck-editor__editable {
        background: #12151C !important;
        color: white !important;
        border-color: rgba(255,255,255,0.1) !important;
        border-radius: 0 0 0.75rem 0.75rem !important;
        min-height: 150px;
    }
    .ck.ck-toolbar {
        background: #0A0C10 !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }
    .ck.ck-button, .ck.ck-dropdown__button {
        color: #ccc !important;
    }
    .ck.ck-button:hover {
        background: rgba(124, 58, 237, 0.2) !important;
        color: white !important;
    }
    .ck.ck-button.ck-on {
        background: rgba(124, 58, 237, 0.5) !important;
        color: white !important;
    }
    .ck-reset_all :not(.ck-reset_all-excluded *), .ck.ck-reset_all {
        color: #ccc !important;
    }
    .ck.ck-list {
        background: #0A0C10 !important;
    }
    .ck.ck-list__item .ck-button:hover {
        background: rgba(124, 58, 237, 0.2) !important;
    }
    </style>

    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editorConfig = {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', '|',
                    'bulletedList', 'numberedList', '|',
                    'undo', 'redo'
                ]
            };

            ClassicEditor
                .create( document.querySelector( '#editor-description' ), editorConfig )
                .catch( error => { console.error( error ); } );

            ClassicEditor
                .create( document.querySelector( '#editor-sys-min' ), editorConfig )
                .catch( error => { console.error( error ); } );

            ClassicEditor
                .create( document.querySelector( '#editor-sys-rec' ), editorConfig )
                .catch( error => { console.error( error ); } );
        });
    </script>
</body>
</html>

