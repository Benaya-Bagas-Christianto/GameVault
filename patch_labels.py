import os
import re

directories = [r'd:\Laragon\laragon\www\gamevault\resources\views']

cart_patch = """if (data.cart_count !== undefined) {
                            localStorage.setItem('cartCount', data.cart_count);
                            let badge = document.getElementById('globalCartBadge');
                            if (badge) {
                                badge.innerText = data.cart_count;
                                badge.style.setProperty('display', data.cart_count > 0 ? 'flex' : 'none', 'important');
                            }
                            
                            // NEW: Update cart_cache for label syncing
                            let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
                            if (!cc.includes(String(gameId))) {
                                cc.push(String(gameId));
                                localStorage.setItem('cart_cache', JSON.stringify(cc));
                            }
                            if (typeof window.syncGameCardLabels === 'function') {
                                window.syncGameCardLabels();
                            }
                        }"""

wish_patch = """if (data.status === 'added') {
                    if (!wishlist.includes(gameId)) wishlist.push(gameId);
                    showToast('Berhasil ditambahkan ke Wishlist! ❤️');
                    if (btn) {
                        btn.classList.add('bg-[#EF4444]', 'border-[#EF4444]', 'text-white');
                        btn.classList.remove('bg-[#1A1D24]', 'border-white/20', 'hover:text-[#EF4444]');
                        btn.setAttribute('title', 'Hapus dari Wishlist');
                    }
                } else if (data.status === 'removed') {
                    wishlist = wishlist.filter(id => id !== gameId);
                    showToast('Game dihapus dari Wishlist.');
                    if (btn) {
                        btn.classList.remove('bg-[#EF4444]', 'border-[#EF4444]', 'text-white');
                        btn.classList.add('bg-[#1A1D24]', 'border-white/20', 'hover:text-[#EF4444]');
                        btn.setAttribute('title', 'Tambah ke Wishlist');
                    }
                }
                
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                
                // NEW: Trigger syncGameCardLabels to update 'WISHLIST' label immediately if present
                if (typeof window.syncGameCardLabels === 'function') {
                    window.syncGameCardLabels();
                }"""

for d in directories:
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.blade.php'):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                changed = False
                
                # Replace for Cart
                pattern_cart = re.compile(r'if \(data\.cart_count !== undefined\) \{\s*localStorage\.setItem\(\'cartCount\', data\.cart_count\);\s*let badge = document\.getElementById\(\'globalCartBadge\'\);\s*if \(badge\) \{\s*badge\.innerText = data\.cart_count;\s*badge\.style\.setProperty\(\'display\', data\.cart_count > 0 \? \'flex\' : \'none\', \'important\'\);\s*\}\s*\}', re.DOTALL)
                
                new_content, count_cart = pattern_cart.subn(cart_patch, content)
                if count_cart > 0:
                    content = new_content
                    changed = True
                    
                # Replace for Wishlist
                pattern_wish = re.compile(r'if \(data\.status === \'added\'\) \{\s*if \(\!wishlist\.includes\(gameId\)\) wishlist\.push\(gameId\);\s*showToast\(\'Berhasil ditambahkan ke Wishlist! ❤️\'\);\s*if \(btn\) \{\s*btn\.classList\.add\(\'bg-\[#EF4444\]\', \'border-\[#EF4444\]\', \'text-white\'\);\s*btn\.classList\.remove\(\'bg-\[#1A1D24\]\', \'border-white/20\', \'hover:text-\[#EF4444\]\'\);\s*btn\.setAttribute\(\'title\', \'Hapus dari Wishlist\'\);\s*\}\s*\} else if \(data\.status === \'removed\'\) \{\s*wishlist = wishlist\.filter\(id => id !== gameId\);\s*showToast\(\'Game dihapus dari Wishlist\.\'\);\s*if \(btn\) \{\s*btn\.classList\.remove\(\'bg-\[#EF4444\]\', \'border-\[#EF4444\]\', \'text-white\'\);\s*btn\.classList\.add\(\'bg-\[#1A1D24\]\', \'border-white/20\', \'hover:text-\[#EF4444\]\'\);\s*btn\.setAttribute\(\'title\', \'Tambah ke Wishlist\'\);\s*\}\s*\}\s*localStorage\.setItem\(\'wishlist\', JSON\.stringify\(wishlist\)\);', re.DOTALL)
                
                new_content, count_wish = pattern_wish.subn(wish_patch, content)
                if count_wish > 0:
                    content = new_content
                    changed = True

                if changed:
                    with open(path, 'w', encoding='utf-8') as file:
                        file.write(content)
                    print(f'Patched {f}')
