import re

with open('resources/views/index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_navigasi = '''                    <div class="space-y-2">
                        <a href="/" class="block text-sm text-gray-500 hover:text-white transition-colors">Beranda</a>
                        <a href="/kategori" class="block text-sm text-gray-500 hover:text-white transition-colors">Kategori Game</a>
                        <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'}); setTimeout(function(){ if(!bantuanActive) toggleBantuan(event); }, 400); return false;" class="block text-sm text-gray-500 hover:text-white transition-colors cursor-pointer">Bantuan & FAQ</a>
                    </div>'''

new_navigasi = '''                    <div class="space-y-2">
                        <a href="/" class="block text-sm {{ request()->is('/') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Beranda</a>
                        <a href="/kategori" class="block text-sm {{ request()->is('kategori') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Kategori Game</a>
                        <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'}); setTimeout(function(){ if(!bantuanActive) toggleBantuan(event); }, 400); return false;" class="block text-sm text-gray-500 hover:text-white transition-all cursor-pointer">Bantuan & FAQ</a>
                    </div>'''

old_akun = '''                    <div class="space-y-2">
                        @if(auth()->guard()->check())
                        <a href="/profil" class="block text-sm text-gray-500 hover:text-white transition-colors">Profil Saya</a>
                        <a href="/library" class="block text-sm text-gray-500 hover:text-white transition-colors">Library Game</a>
                        <a href="/orders" class="block text-sm text-gray-500 hover:text-white transition-colors">Riwayat Pembelian</a>
                        @else
                        <a href="/login" class="block text-sm text-gray-500 hover:text-white transition-colors">Login</a>
                        @endif
                    </div>'''

new_akun = '''                    <div class="space-y-2">
                        @if(auth()->guard()->check())
                        <a href="/profil" class="block text-sm {{ request()->is('profil') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Profil Saya</a>
                        <a href="/library" class="block text-sm {{ request()->is('library') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Library Game</a>
                        <a href="/orders" class="block text-sm {{ request()->is('orders') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Riwayat Pembelian</a>
                        @else
                        <a href="/login" class="block text-sm {{ request()->is('login') ? 'text-[#7C3AED] font-bold pointer-events-none drop-shadow-[0_0_10px_rgba(124,58,237,0.5)]' : 'text-gray-500 hover:text-white' }} transition-all">Login</a>
                        @endif
                    </div>'''

content = content.replace(old_navigasi, new_navigasi)
content = content.replace(old_akun, new_akun)

with open('resources/views/index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Footer patched")
