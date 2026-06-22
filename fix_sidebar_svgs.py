import re

with open('resources/views/admin/dashboard.blade.php', 'r', encoding='utf-8') as f:
    dash = f.read()

# Extract SVGs from Dashboard
dash_svg_match = re.search(r'<a href="/admin/dashboard".*?(<svg.*?</svg>).*?Dashboard', dash, re.DOTALL)
game_svg_match = re.search(r'<a href="/admin/games".*?(<svg.*?</svg>).*?Kelola Game', dash, re.DOTALL)
trans_svg_match = re.search(r'<a href="/admin/transaksi".*?(<svg.*?</svg>).*?Transaksi', dash, re.DOTALL)

dash_svg = dash_svg_match.group(1)
game_svg = game_svg_match.group(1)
trans_svg = trans_svg_match.group(1)

# Now read users_index.blade.php
with open('resources/views/admin/users_index.blade.php', 'r', encoding='utf-8') as f:
    users = f.read()

# Replace in users_index
old_dash_svg_match = re.search(r'<a href="/admin/dashboard".*?(<svg.*?</svg>).*?Dashboard', users, re.DOTALL)
old_game_svg_match = re.search(r'<a href="/admin/games".*?(<svg.*?</svg>).*?Kelola Game', users, re.DOTALL)
old_trans_svg_match = re.search(r'<a href="/admin/transaksi".*?(<svg.*?</svg>).*?Transaksi', users, re.DOTALL)

users = users.replace(old_dash_svg_match.group(1), dash_svg)
users = users.replace(old_game_svg_match.group(1), game_svg)
users = users.replace(old_trans_svg_match.group(1), trans_svg)

with open('resources/views/admin/users_index.blade.php', 'w', encoding='utf-8') as f:
    f.write(users)

print("Fixed the sidebars in users_index.blade.php!")
