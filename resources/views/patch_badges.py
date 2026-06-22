import os, glob, re

for f in glob.glob('resources/views/*.blade.php'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Remove the overwriting of localStorage in head
    content = re.sub(r'<script>\s*// Overwrite localStorage\.setItem.*?</script>', '', content, flags=re.DOTALL)
    
    # Replace Cart Badge Logic (PHP)
    content = re.sub(
        r'@php\s+\$cookieCartCount = isset\(\$_COOKIE\[\'cart_count\'\]\).*?\$globalCartCount = \$cookieCartCount !== null \? \$cookieCartCount : \$serverCartCount;\s+@endphp',
        r'@php\n                        $globalCartCount = Auth::check() ? \\App\\Models\\Keranjang::where(\'user_id\', Auth::id())->count() : 0;\n                    @endphp',
        content, flags=re.DOTALL
    )
    
    # Replace Cart Badge Logic (JS)
    content = re.sub(
        r'<script>\s*\(function\(\) \{\s*let currentUserId = \{\{ Auth::check\(\) \? Auth::id\(\) : \'null\' \}\};.*?window\.addEventListener\(\'pageshow\', window\.syncCartBadge\);\s*</script>',
        r'''<script>
                        window.syncCartBadge = function(e) {
                            if (e.persisted) {
                                let isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
                                if (!isLoggedIn) return;
                                let cachedCount = parseInt(localStorage.getItem('cartCount')) || 0;
                                let badgeInit = document.getElementById('globalCartBadge');
                                if (badgeInit) {
                                    badgeInit.innerText = cachedCount;
                                    badgeInit.style.setProperty('display', cachedCount > 0 ? 'flex' : 'none', 'important');
                                }
                            }
                        };
                        window.addEventListener('pageshow', window.syncCartBadge);
                    </script>''',
        content, flags=re.DOTALL
    )

    # Replace Wishlist Badge Logic (PHP)
    content = re.sub(
        r'@php\s+\$cookieWishlistCount = isset\(\$_COOKIE\[\'wishlist_count\'\]\).*?\$serverWishlistIds = Auth::check\(\) \? \\App\\Models\\Wishlist::where\(\'user_id\', Auth::id\(\)\)->pluck\(\'game_id\'\)->map\(\'strval\'\)->toArray\(\) : \[\];\s+@endphp',
        r'@php\n                        $globalWishlistCount = Auth::check() ? \\App\\Models\\Wishlist::where(\'user_id\', Auth::id())->count() : 0;\n                    @endphp',
        content, flags=re.DOTALL
    )

    # Replace Wishlist Badge Logic (JS)
    content = re.sub(
        r'<script>\s*\(function\(\) \{\s*let currentUserId = \{\{ Auth::check\(\) \? Auth::id\(\) : \'null\' \}\};.*?window\.addEventListener\(\'pageshow\', window\.syncWishlistBadge\);\s*</script>',
        r'''<script>
                    window.syncWishlistBadge = function(e) {
                        if (e.persisted) {
                            let isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
                            if (!isLoggedIn) return;
                            let cachedWishlist = localStorage.getItem('wishlist');
                            let wishlist = JSON.parse(cachedWishlist) || [];
                            let cachedCount = wishlist.length;
                            let badges = document.querySelectorAll('.globalWishlistBadge, #globalWishlistBadge');
                            badges.forEach(badgeInit => {
                                badgeInit.innerText = cachedCount;
                                badgeInit.style.setProperty('display', cachedCount > 0 ? 'flex' : 'none', 'important');
                            });
                        }
                    };
                    window.addEventListener('pageshow', window.syncWishlistBadge);
                </script>''',
        content, flags=re.DOTALL
    )

    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f'Processed {f}')
