import re

with open('resources/views/admin/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

bad_pengguna_regex = r'<a href="/admin/users" class="inline-flex items-center gap-2 px-5 py-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all text-sm font-bold">.*?Pengguna\s*</a>'
content = re.sub(bad_pengguna_regex, '', content, flags=re.DOTALL)

sidebar_transaksi_regex = r'(<a href="/admin/transaksi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all text-sm font-medium">.*?</a>\s*)(</nav>)'

new_link = '''<a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg> Pengguna
            </a>
            '''

def replacer(match):
    return match.group(1) + new_link + match.group(2)

content = re.sub(sidebar_transaksi_regex, replacer, content, flags=re.DOTALL)

with open('resources/views/admin/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Fixed dashboard sidebar')
