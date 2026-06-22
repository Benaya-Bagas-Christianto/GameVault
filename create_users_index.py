import os

with open('resources/views/admin/games_index.blade.php', 'r', encoding='utf-8') as f:
    template = f.read()

# Extract everything up to the <main> opening tag and header.
# Actually, let's just write the blade template directly.
blade_content = """<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - GameVault Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0A0C10; color: #fff; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0A0C10; }
        ::-webkit-scrollbar-thumb { background: #2A2D3A; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #7C3AED; }
    </style>
</head>
<body class="antialiased min-h-screen flex">

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
        <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2">
            <a href="/admin/dashboard" class="inline-flex items-center gap-2 px-5 py-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all text-sm font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg> Dashboard
            </a>
            <a href="/admin/games" class="inline-flex items-center gap-2 px-5 py-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all text-sm font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" /></svg> Kelola Game Master
            </a>
            <a href="/admin/transaksi" class="inline-flex items-center gap-2 px-5 py-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all text-sm font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg> Transaksi
            </a>
            <a href="/admin/users" class="inline-flex items-center gap-2 px-5 py-3 bg-purple-500 text-white font-bold rounded-xl hover:brightness-110 hover:-translate-y-0.5 transition-all text-sm shadow-lg shadow-purple-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg> Pengguna
            </a>
        </div>
        <div class="p-6 border-t border-white/5 mt-auto">
            <a href="/" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Kembali ke Web
            </a>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="ml-64 flex-1 p-8 lg:p-12">
        <div class="max-w-7xl mx-auto">
            
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <h1 class="text-3xl font-black text-white mb-2">Data Pengguna</h1>
                    <p class="text-gray-400 text-sm">Kelola pengguna terdaftar beserta data Wishlist, Keranjang, dan Game yang dibeli.</p>
                </div>
                
                {{-- Search --}}
                <div class="flex items-center gap-3">
                    <form action="/admin/users" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari username / email..." 
                               class="bg-[#12151C] border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all w-64">
                        <svg class="w-4 h-4 text-gray-500 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-[#12151C] rounded-2xl border border-white/5 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white/[0.02] border-b border-white/5">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">User ID</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Username</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Email</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Dibeli</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Keranjang</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Wishlist</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Bergabung</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($users as $u)
                                @php
                                    $totalDibeli = 0;
                                    $gamesDibeli = [];
                                    foreach($u->transaksis as $t) {
                                        foreach($t->details as $d) {
                                            $totalDibeli++;
                                            $gamesDibeli[] = $d->game;
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4 text-gray-500 font-mono">#{{ $u->id }}</td>
                                    <td class="px-6 py-4 font-bold text-white">{{ $u->username }}</td>
                                    <td class="px-6 py-4 text-gray-400">{{ $u->email }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500/10 text-green-400 font-bold border border-green-500/20">
                                            {{ $totalDibeli }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500/10 text-blue-400 font-bold border border-blue-500/20">
                                            {{ $u->keranjangs->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-pink-500/10 text-pink-400 font-bold border border-pink-500/20">
                                            {{ $u->wishlists->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-500 text-xs">
                                        {{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="toggleDetails({{ $u->id }})" class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-500/10 text-purple-400 border border-purple-500/20 rounded-lg text-xs font-bold hover:bg-purple-500/20 transition-colors">
                                            Lihat Data
                                        </button>
                                    </td>
                                </tr>
                                
                                {{-- Accordion Row --}}
                                <tr id="details-{{ $u->id }}" class="hidden bg-[#0A0C10]/50">
                                    <td colspan="8" class="px-6 py-6 border-t border-white/5">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            
                                            {{-- Dibeli --}}
                                            <div class="bg-[#12151C] border border-white/5 rounded-xl p-4">
                                                <h4 class="text-green-400 font-bold mb-3 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Game Dibeli
                                                </h4>
                                                @if($totalDibeli > 0)
                                                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                                        @foreach($gamesDibeli as $g)
                                                            @if($g)
                                                            <div class="flex items-center gap-3 bg-white/5 p-2 rounded-lg">
                                                                <img src="{{ asset('assets/' . $g->image) }}" class="w-10 h-10 object-cover rounded">
                                                                <p class="text-xs text-white font-semibold truncate">{{ $g->name }}</p>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-500 italic">Belum ada game yang dibeli.</p>
                                                @endif
                                            </div>

                                            {{-- Keranjang --}}
                                            <div class="bg-[#12151C] border border-white/5 rounded-xl p-4">
                                                <h4 class="text-blue-400 font-bold mb-3 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg> Di Keranjang
                                                </h4>
                                                @if($u->keranjangs->count() > 0)
                                                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                                        @foreach($u->keranjangs as $k)
                                                            @if($k->game)
                                                            <div class="flex items-center gap-3 bg-white/5 p-2 rounded-lg">
                                                                <img src="{{ asset('assets/' . $k->game->image) }}" class="w-10 h-10 object-cover rounded">
                                                                <p class="text-xs text-white font-semibold truncate">{{ $k->game->name }}</p>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-500 italic">Keranjang kosong.</p>
                                                @endif
                                            </div>

                                            {{-- Wishlist --}}
                                            <div class="bg-[#12151C] border border-white/5 rounded-xl p-4">
                                                <h4 class="text-pink-400 font-bold mb-3 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Wishlist
                                                </h4>
                                                @if($u->wishlists->count() > 0)
                                                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                                        @foreach($u->wishlists as $w)
                                                            @if($w->game)
                                                            <div class="flex items-center gap-3 bg-white/5 p-2 rounded-lg">
                                                                <img src="{{ asset('assets/' . $w->game->image) }}" class="w-10 h-10 object-cover rounded">
                                                                <p class="text-xs text-white font-semibold truncate">{{ $w->game->name }}</p>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-500 italic">Wishlist kosong.</p>
                                                @endif
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center text-gray-600">
                                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        <p class="font-semibold">Tidak ada data pengguna ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>

        </div>
    </main>

    <script>
        function toggleDetails(id) {
            const el = document.getElementById('details-' + id);
            if (el.classList.contains('hidden')) {
                // close others
                document.querySelectorAll('tr[id^="details-"]').forEach(tr => {
                    tr.classList.add('hidden');
                });
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
"""

with open('resources/views/admin/users_index.blade.php', 'w', encoding='utf-8') as f:
    f.write(blade_content)

print("Created users_index.blade.php")
