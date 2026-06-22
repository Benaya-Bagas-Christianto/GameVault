import glob

files = glob.glob('resources/views/admin/*.blade.php')

old_path = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'

new_paths = '''<path d="M15 7C15 8.65685 13.6569 10 12 10C10.3431 10 9 8.65685 9 7C9 5.34315 10.3431 4 12 4C13.6569 4 15 5.34315 15 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17 10C18.1046 10 19 9.10457 19 8C19 6.89543 18.1046 6 17 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M21 19C21 16.5147 19.389 14.4061 17.1355 13.6279" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'''

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()

    if old_path in content:
        content = content.replace(old_path, new_paths)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f'Replaced old path in {file}')
