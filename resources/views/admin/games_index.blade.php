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
    <title>Kelola Game - Admin GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { background: #0A0C10; color: white; font-family: 'Inter', sans-serif; }
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
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.7255 17.1019C11.6265 16.8844 11.4215 16.7257 11.1734 16.6975C10.9633 16.6735 10.7576 16.6285 10.562 16.5636C10.4743 16.5341 10.392 16.5019 10.3158 16.4674L10.4424 16.1223C10.5318 16.1622 10.6239 16.1987 10.7182 16.2317L10.7221 16.2331L10.7261 16.2344C11.0287 16.3344 11.3265 16.3851 11.611 16.3851C11.8967 16.3851 12.1038 16.3468 12.2629 16.2647L12.2724 16.2598L12.2817 16.2544C12.5227 16.1161 12.661 15.8784 12.661 15.6021C12.661 15.2955 12.4956 15.041 12.2071 14.9035C12.062 14.8329 11.8559 14.7655 11.559 14.6917C11.2545 14.6147 10.9987 14.533 10.8003 14.4493C10.6553 14.3837 10.5295 14.279 10.4161 14.1293C10.3185 13.9957 10.2691 13.7948 10.2691 13.5319C10.2691 13.2147 10.3584 12.9529 10.5422 12.7315C10.7058 12.5375 10.9381 12.4057 11.2499 12.3318C11.4812 12.277 11.6616 12.1119 11.7427 11.8987C11.8344 12.1148 12.0295 12.2755 12.2723 12.3142C12.4751 12.3465 12.6613 12.398 12.8287 12.4677L12.7122 12.8059C12.3961 12.679 12.085 12.6149 11.7841 12.6149C10.7848 12.6149 10.7342 13.3043 10.7342 13.4425C10.7342 13.7421 10.896 13.9933 11.1781 14.1318L11.186 14.1357L11.194 14.1393C11.3365 14.2029 11.5387 14.2642 11.8305 14.3322C12.1322 14.4004 12.3838 14.4785 12.5815 14.5651L12.5856 14.5669L12.5897 14.5686C12.7365 14.6297 12.8624 14.7317 12.9746 14.8805L12.9764 14.8828L12.9782 14.8852C13.0763 15.012 13.1261 15.2081 13.1261 15.4681C13.1261 15.7682 13.0392 16.0222 12.8604 16.2447C12.7053 16.4377 12.4888 16.5713 12.1983 16.6531C11.974 16.7163 11.8 16.8878 11.7255 17.1019Z" fill="currentColor"/><path d="M11.9785 18H11.497C11.3893 18 11.302 17.9105 11.302 17.8V17.3985C11.302 17.2929 11.2219 17.2061 11.1195 17.1944C10.8757 17.1667 10.6399 17.115 10.412 17.0394C10.1906 16.9648 9.99879 16.8764 9.83657 16.7739C9.76202 16.7268 9.7349 16.6312 9.76572 16.5472L10.096 15.6466C10.1405 15.5254 10.284 15.479 10.3945 15.5417C10.5437 15.6262 10.7041 15.6985 10.8755 15.7585C11.131 15.8429 11.3762 15.8851 11.611 15.8851C11.8129 15.8851 11.9572 15.8628 12.0437 15.8181C12.1302 15.7684 12.1735 15.6964 12.1735 15.6021C12.1735 15.4929 12.1158 15.411 12.0004 15.3564C11.8892 15.3018 11.7037 15.2422 11.4442 15.1777C11.1104 15.0933 10.8323 15.0039 10.6098 14.9096C10.3873 14.8103 10.1936 14.6514 10.0288 14.433C9.86396 14.2096 9.78156 13.9092 9.78156 13.5319C9.78156 13.095 9.91136 12.7202 10.1709 12.4074C10.4049 12.13 10.7279 11.9424 11.1401 11.8447C11.2329 11.8227 11.302 11.7401 11.302 11.6425V11.2C11.302 11.0895 11.3893 11 11.497 11H11.9785C12.0862 11 12.1735 11.0895 12.1735 11.2V11.6172C12.1735 11.7194 12.2487 11.8045 12.3471 11.8202C12.7082 11.8777 13.0255 11.9866 13.2989 12.1469C13.3765 12.1924 13.4073 12.2892 13.3775 12.3756L13.0684 13.2725C13.0275 13.3914 12.891 13.4417 12.7812 13.3849C12.433 13.2049 12.1007 13.1149 11.7841 13.1149C11.4091 13.1149 11.2216 13.2241 11.2216 13.4425C11.2216 13.5468 11.2773 13.6262 11.3885 13.6809C11.4998 13.7305 11.6831 13.7851 11.9386 13.8447C12.2682 13.9192 12.5464 14.006 12.773 14.1053C12.9996 14.1996 13.1953 14.356 13.3602 14.5745C13.5291 14.7929 13.6136 15.0908 13.6136 15.4681C13.6136 15.8851 13.4879 16.25 13.2365 16.5628C13.0176 16.8354 12.7145 17.0262 12.3274 17.1353C12.2384 17.1604 12.1735 17.2412 12.1735 17.3358V17.8C12.1735 17.9105 12.0862 18 11.9785 18Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M9.59235 5H13.8141C14.8954 5 14.3016 6.664 13.8638 7.679L13.3656 8.843L13.2983 9C13.7702 8.97651 14.2369 9.11054 14.6282 9.382C16.0921 10.7558 17.2802 12.4098 18.1256 14.251C18.455 14.9318 18.5857 15.6958 18.5019 16.451C18.4013 18.3759 16.8956 19.9098 15.0182 20H8.38823C6.51033 19.9125 5.0024 18.3802 4.89968 16.455C4.81587 15.6998 4.94656 14.9358 5.27603 14.255C6.12242 12.412 7.31216 10.7565 8.77823 9.382C9.1696 9.11054 9.63622 8.97651 10.1081 9L10.0301 8.819L9.54263 7.679C9.1068 6.664 8.5101 5 9.59235 5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.2983 9.75C13.7125 9.75 14.0483 9.41421 14.0483 9C14.0483 8.58579 13.7125 8.25 13.2983 8.25V9.75ZM10.1081 8.25C9.69391 8.25 9.35812 8.58579 9.35812 9C9.35812 9.41421 9.69391 9.75 10.1081 9.75V8.25ZM15.9776 8.64988C16.3365 8.44312 16.4599 7.98455 16.2531 7.62563C16.0463 7.26671 15.5878 7.14336 15.2289 7.35012L15.9776 8.64988ZM13.3656 8.843L13.5103 9.57891L13.5125 9.57848L13.3656 8.843ZM10.0301 8.819L10.1854 8.08521L10.1786 8.08383L10.0301 8.819ZM8.166 7.34357C7.80346 7.14322 7.34715 7.27469 7.1468 7.63722C6.94644 7.99976 7.07791 8.45607 7.44045 8.65643L8.166 7.34357ZM13.2983 8.25H10.1081V9.75H13.2983V8.25ZM15.2289 7.35012C14.6019 7.71128 13.9233 7.96683 13.2187 8.10752L13.5125 9.57848C14.3778 9.40568 15.2101 9.09203 15.9776 8.64988L15.2289 7.35012ZM13.2209 8.10709C12.2175 8.30441 11.1861 8.29699 10.1854 8.08525L9.87486 9.55275C11.0732 9.80631 12.3086 9.81521 13.5103 9.57891L13.2209 8.10709ZM10.1786 8.08383C9.47587 7.94196 8.79745 7.69255 8.166 7.34357L7.44045 8.65643C8.20526 9.0791 9.02818 9.38184 9.88169 9.55417L10.1786 8.08383Z" fill="currentColor"/></svg> Transaksi
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
        <div class="mb-8 border-b border-white/5 pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-widest uppercase mb-1">Kelola Game</h1>
                <p class="text-gray-500 text-sm">Tambah, edit, atau hapus data game di katalog GameVault.</p>
            </div>
            <div class="flex items-center gap-3">
                <form action="/admin/games" method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ session('admin_games_search', '') }}" placeholder="Cari game..."
                               class="w-64 pl-10 pr-4 py-2.5 bg-[#12151C] border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors text-sm shadow-inner">
                        <svg class="w-4 h-4 text-gray-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
                <a href="/admin/games/tambah"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#7C3AED] text-white font-bold rounded-xl hover:brightness-110 hover:-translate-y-0.5 transition-all text-sm shadow-lg shadow-purple-500/20">
                    + Tambah Game Baru
                </a>
            </div>
        </div>

        {{-- Alert --}}

        {{-- Table --}}
        <div class="bg-[#12151C] border border-white/5 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-purple-500 rounded-full"></div>
                <h2 class="text-white font-bold uppercase tracking-widest text-sm">Daftar Katalog Game</h2>
                <span class="ml-auto text-xs text-gray-500 bg-white/5 px-3 py-1 rounded-full border border-white/10">
                    {{ $games->count() }} game
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        @php
                            function sortUrl($column) {
                                return request()->fullUrlWithQuery(['sort_by' => $column]);
                            }

                            function sortIcon($column) {
                                $current_col = session('admin_sort_by', 'id');
                                
                                if ($current_col != $column) {
                                    return '<svg class="w-3 h-3 text-gray-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';
                                }

                                $dir = session('admin_sort_dir', 'desc');
                                if ($dir == 'asc') {
                                    return '<svg class="w-3 h-3 text-purple-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>';
                                }
                                return '<svg class="w-3 h-3 text-purple-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                            }
                        @endphp
                        <tr class="border-b border-white/5">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Cover</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                <a href="{{ sortUrl('name') }}" class="flex items-center gap-1.5 hover:text-white transition-colors w-max">
                                    Nama Game {!! sortIcon('name') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                <a href="{{ sortUrl('platform') }}" class="flex items-center gap-1.5 hover:text-white transition-colors w-max">
                                    Platform {!! sortIcon('platform') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                <a href="{{ sortUrl('genre') }}" class="flex items-center gap-1.5 hover:text-white transition-colors w-max">
                                    Genre {!! sortIcon('genre') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                <a href="{{ sortUrl('price') }}" class="flex items-center gap-1.5 hover:text-white transition-colors w-max">
                                    Harga {!! sortIcon('price') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1a1a1a]">
                        @forelse($games as $g)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">#{{ $g->id }}</td>
                            <td class="px-6 py-4">
                                <img src="{{ asset('assets/' . $g->image) }}"
                                     class="w-14 h-10 object-cover rounded-lg border border-white/10 group-hover:border-purple-500/40 transition-colors"
                                     alt="{{ $g->name }}">
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-semibold">{{ $g->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach(explode(',', $g->platform) as $p)
                                        <span class="text-xs px-2 py-0.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full">{{ trim($p) }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ $g->genre }}</td>
                            <td class="px-6 py-4 text-purple-400 font-bold">Rp {{ number_format($g->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="/admin/games/edit/{{ $g->id }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded-lg text-xs font-bold hover:bg-yellow-500/20 transition-colors">
                                       <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="m3.99 16.854-1.314 3.504a.75.75 0 0 0 .966.965l3.503-1.314a3 3 0 0 0 1.068-.687L18.36 9.175s-.354-1.061-1.414-2.122c-1.06-1.06-2.122-1.414-2.122-1.414L4.677 15.786a3 3 0 0 0-.687 1.068zm12.249-12.63 1.383-1.383c.248-.248.579-.406.925-.348.487.08 1.232.322 1.934 1.025.703.703.945 1.447 1.025 1.934.058.346-.1.677-.348.925L19.774 7.76s-.353-1.06-1.414-2.12c-1.06-1.062-2.121-1.415-2.121-1.415z" fill="currentColor"/></svg> Edit
                                    </a>
                                    <a href="/admin/games/hapus/{{ $g->id }}"
                                       onclick="event.preventDefault(); openDeleteModal(this.href, '{{ addslashes($g->name) }}')"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-lg text-xs font-bold hover:bg-red-500/20 transition-colors">
                                       <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L17.1991 18.0129C17.129 19.065 17.0939 19.5911 16.8667 19.99C16.6666 20.3412 16.3648 20.6235 16.0011 20.7998C15.588 21 15.0607 21 14.0062 21H9.99377C8.93927 21 8.41202 21 7.99889 20.7998C7.63517 20.6235 7.33339 20.3412 7.13332 19.99C6.90607 19.5911 6.871 19.065 6.80086 18.0129L6 6M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M14 10V17M10 10V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-600">
                                <img src="{{ asset('assets/Logo Game Vault 1.png') }}" alt="GameVault" class="w-16 h-16 mx-auto mb-3 opacity-50 grayscale">
                                <p class="font-semibold">Belum ada game di katalog.</p>
                                <a href="/admin/games/tambah" class="text-purple-500 text-sm hover:underline mt-1 inline-block">Tambah game pertama →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2">Hapus Game?</h3>
            <p class="text-gray-400 text-center text-sm mb-6 leading-relaxed" id="deleteModalText">Yakin ingin menghapus game ini?</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-3 bg-transparent border border-white/10 text-gray-300 font-bold rounded-xl hover:bg-white/5 transition-colors text-sm">Batal</button>
                <a id="confirmDeleteBtn" href="#" onclick="sessionStorage.setItem('gamesIndexScroll', window.scrollY);" class="flex-1 px-4 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors text-sm text-center flex items-center justify-center">Ya, Hapus</a>
            </div>
        </div>
    </div>

    @if(session('pesan'))
    {{-- Modal Konfirmasi Sukses --}}
    <div id="successModal" class="fixed inset-0 z-[150] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div id="successModalContent" class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-green-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2">Berhasil!</h3>
            <p id="successModalText" class="text-gray-400 text-center text-sm mb-6 leading-relaxed">{{ session('pesan') }}</p>
            <button type="button" onclick="closeSuccessModal()" class="w-full px-4 py-3 bg-green-500/10 text-green-500 border border-green-500/20 font-bold rounded-xl hover:bg-green-500 hover:text-white transition-colors text-sm text-center">OK</button>
        </div>
    </div>
    @endif

    <script>
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
            if (!modal) return;
            
            // Animasi keluar
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }


        // Logika Modal Hapus
        function openDeleteModal(url, gameName) {
            document.getElementById('confirmDeleteBtn').href = url;
            document.getElementById('deleteModalText').innerHTML = `Game <strong class="text-white">${gameName}</strong> beserta semua gambarnya akan dihapus permanen.`;
            
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            
            // Animasi masuk
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            // Animasi keluar
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            modal.firstElementChild.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Simpan posisi scroll sebelum meninggalkan halaman index ini (saat klik Edit/Hapus)
        document.querySelectorAll('a[href*="/admin/games/edit/"], a[href*="/admin/games/hapus/"]').forEach(link => {
            link.addEventListener('click', () => {
                sessionStorage.setItem('gamesIndexScroll', window.scrollY);
            });
        });

        // Coba kembalikan posisi scroll secepat mungkin tanpa menunggu 'load' untuk mencegah flicker
        const scrollPos = sessionStorage.getItem('gamesIndexScroll');
        if (scrollPos) {
            // Matikan scroll restoration bawaan secepatnya
            if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
            
            const targetPos = parseInt(scrollPos);
            
            // Coba eksekusi langsung
            window.scrollTo(0, targetPos);
            
            // Coba eksekusi saat DOM selesai dibaca
            document.addEventListener('DOMContentLoaded', () => {
                window.scrollTo(0, targetPos);
            });
            
            // Coba lagi saat semua gambar sudah dimuat, lalu hapus penyimpanan
            window.addEventListener('load', () => {
                window.scrollTo(0, targetPos);
                sessionStorage.removeItem('gamesIndexScroll');
                if ('scrollRestoration' in history) history.scrollRestoration = 'auto';
            });
        }
    </script>
</body>
</html>
