import glob
import re

files = glob.glob('resources/views/admin/*.blade.php')

new_link = '''<a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all text-sm font-medium">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg> Pengguna
            </a>
            '''

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()

    # We need to target the <a href="/admin/users"... tag and replace it with new_link
    # Except in users_index where it should be highlighted
    
    if 'users_index' in file:
        highlighted_link = '''<a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 text-sm font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg> Pengguna
            </a>
            '''
        
        # In users_index, the link might be the old format, let's find it.
        # Just replace <a href="/admin/users"...</a>
        content = re.sub(r'<a href="/admin/users"[^>]*>.*?</a>', highlighted_link, content, flags=re.DOTALL)
    else:
        # For other files, replace the old Pengguna link with the new_link
        content = re.sub(r'<a href="/admin/users"[^>]*>.*?</a>', new_link, content, flags=re.DOTALL)

    with open(file, 'w', encoding='utf-8') as f:
        f.write(content)

print('Fixed all sidebars Pengguna link format')
