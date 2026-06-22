import glob
import re

files = glob.glob('resources/views/admin/*.blade.php')

# We need to find the CURRENT SVG being used for Pengguna.
# Since I previously changed it to the solid one:
# <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
#     <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
# </svg>

old_solid_svg_regex = r'<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">\s*<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c\.046-\.327\.07-\.66\.07-1a6\.97 6\.97 0 00-1\.5-4\.33A5 5 0 0119 16v1h-6\.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />\s*</svg>'

# The new SVG Repo style User Group
new_svg = '''<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 7C15 8.65685 13.6569 10 12 10C10.3431 10 9 8.65685 9 7C9 5.34315 10.3431 4 12 4C13.6569 4 15 5.34315 15 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 10C18.1046 10 19 9.10457 19 8C19 6.89543 18.1046 6 17 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 19C21 16.5147 19.389 14.4061 17.1355 13.6279" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>'''

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()

    # Replace the solid one if it's there
    content = re.sub(old_solid_svg_regex, new_svg, content, flags=re.DOTALL)
    
    with open(file, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f'Fixed SVG in {file}')
