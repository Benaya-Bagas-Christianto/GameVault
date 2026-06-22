import os
import re

directories = [r'd:\Laragon\laragon\www\gamevault\resources\views']

def fix_isLoggedIn(content):
    # Pattern to match the broken Prettier-formatted {{ Auth::check() ? 'true' : 'false' }}
    pattern_broken = re.compile(r'let isLoggedIn\s*=\s*\{\s*\{\s*Auth::check\(\)\s*\?\s*\'true\'\s*:\s*\'false\'\s*\}\s*\};', re.MULTILINE)
    content = pattern_broken.sub(r'let isLoggedIn = @json(Auth::check());', content)
    
    # Pattern to match the original single-line version
    pattern_single = re.compile(r'let isLoggedIn\s*=\s*\{\{\s*Auth::check\(\)\s*\?\s*\'true\'\s*:\s*\'false\'\s*\}\};')
    content = pattern_single.sub(r'let isLoggedIn = @json(Auth::check());', content)
    
    return content

for d in directories:
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.blade.php'):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                new_content = fix_isLoggedIn(content)
                if new_content != content:
                    with open(path, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f'Patched {f}')
