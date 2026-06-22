import glob
import re

files = glob.glob('resources/views/admin/*.blade.php')

new_link = '''
            <a href="/admin/users" class="inline-flex items-center gap-2 px-5 py-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all text-sm font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg> Pengguna
            </a>'''

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if 'href="/admin/users"' not in content:
        # Let's search for "href=\"/admin/transaksi\"" to locate the anchor tag, and then find its closing </a>
        match = re.search(r'<a href="/admin/transaksi"[^>]*>.*?</a>', content, re.DOTALL)
        if match:
            end_pos = match.end()
            content = content[:end_pos] + new_link + content[end_pos:]
            with open(file, 'w', encoding='utf-8') as f:
                f.write(content)
            print('Updated', file)
