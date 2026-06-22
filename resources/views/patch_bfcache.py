import glob

script = """
    <script>
        // BFCache Flicker Fix: Sembunyikan badge sebelum masuk cache agar tidak kedip angka lama
        window.addEventListener('pagehide', function() {
            let badges = document.querySelectorAll('#globalCartBadge, .globalWishlistBadge, #globalWishlistBadge');
            badges.forEach(b => b.style.setProperty('opacity', '0', 'important'));
        });
        window.addEventListener('pageshow', function(e) {
            setTimeout(() => {
                let badges = document.querySelectorAll('#globalCartBadge, .globalWishlistBadge, #globalWishlistBadge');
                badges.forEach(b => b.style.removeProperty('opacity'));
            }, 10);
        });
    </script>
"""

for f in glob.glob('resources/views/*.blade.php'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    if 'BFCache Flicker Fix' not in content:
        content = content.replace('</head>', script + '</head>')
        
        with open(f, 'w', encoding='utf-8') as file:
            file.write(content)
        print(f'Patched {f}')
