import re

with open('resources/views/admin/users_index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_svg_pattern = r'<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4\.318 6\.318a4\.5 4\.5 0 000 6\.364L12 20\.364l7\.682-7\.682a4\.5 4\.5 0 00-6\.364-6\.364L12 7\.636l-1\.318-1\.318a4\.5 4\.5 0 00-6\.364 0z" /></svg>'

new_svg = '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z" /></svg>'

if re.search(old_svg_pattern, content):
    content = re.sub(old_svg_pattern, new_svg, content)
    with open('resources/views/admin/users_index.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Fixed Wishlist SVG!")
else:
    print("Could not find the SVG pattern to replace.")
