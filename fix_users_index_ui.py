import re

with open('resources/views/admin/users_index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Favicon to head
if '<link rel="icon"' not in content:
    content = content.replace('</head>', '    <link rel="icon" type="image/png" href="{{ asset(\'assets/Logo Game Vault 1.png\') }}">\n</head>')

# 2. Slow down animation
# Replace duration-300 with duration-700 for a slower, elegant animation.
content = content.replace('duration-300', 'duration-[700ms]')

# Update the setTimeout timeouts in JS from 300 to 700
content = content.replace('300); // Wait for transition', '700); // Wait for transition')
content = content.replace('setTimeout(() => otherTr.classList.add(\'hidden\'), 300);', 'setTimeout(() => otherTr.classList.add(\'hidden\'), 700);')
content = content.replace('setTimeout(() => {\n                    tr.classList.add(\'hidden\');\n                }, 300);', 'setTimeout(() => {\n                    tr.classList.add(\'hidden\');\n                }, 700);')

with open('resources/views/admin/users_index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Updated speed and favicon')
