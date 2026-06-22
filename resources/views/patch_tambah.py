import os
import re

directory = r'd:\Laragon\laragon\www\gamevault\resources\views'

new_func = """        window.tambahKeranjangCerdas = function(gameId, isBuyNow, btnElement) {
            let isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
            if (isLoggedIn === 'false' || isLoggedIn === false) {
                document.getElementById('loginModal').classList.remove('hidden');
                return;
            }

            const originalContent = btnElement.innerHTML;
            
            // Ubah tombol jadi Spinner Loading Bawaan Tailwind
            btnElement.innerHTML = `<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            btnElement.disabled = true;

            // OPTIMISTIC UPDATE
            let originalCount = 0;
            if (!isBuyNow) {
                let badge = document.getElementById('globalCartBadge');
                originalCount = badge && badge.innerText && !badge.classList.contains('hidden') ? parseInt(badge.innerText) : 0;
                if (isNaN(originalCount)) originalCount = 0;
                let newCount = originalCount + 1;
                
                if (badge) {
                    badge.innerText = newCount;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                }
                localStorage.setItem('cartCount', newCount);
            }

            fetch('/cart_process', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
                body: JSON.stringify({ game_id: gameId })
            })
            .then(res => res.json())
            .then(data => {
                if (isBuyNow) {
                    window.location.href = '/cart'; 
                } else {
                    btnElement.innerHTML = originalContent;
                    btnElement.disabled = false;
                    if (data.status === 'success') {
                        showToast('Game berhasil masuk ke keranjangmu!');
                        if (data.cart_count !== undefined) {
                            localStorage.setItem('cartCount', data.cart_count);
                            let badge = document.getElementById('globalCartBadge');
                            if (badge) {
                                badge.innerText = data.cart_count;
                                badge.classList.remove('hidden');
                                badge.classList.add('flex');
                            }
                        }
                    } else {
                        showToast(data.message || 'Gagal menambahkan game ke keranjang');
                        // REVERT OPTIMISTIC UPDATE
                        let badge = document.getElementById('globalCartBadge');
                        if (badge) {
                            badge.innerText = originalCount;
                            if (originalCount > 0) {
                                badge.classList.remove('hidden');
                                badge.classList.add('flex');
                            } else {
                                badge.classList.add('hidden');
                                badge.classList.remove('flex');
                            }
                        }
                        localStorage.setItem('cartCount', originalCount);
                    }
                }
            })
            .catch(() => {
                if (isBuyNow) window.location.href = '/cart'; 
                else { 
                    btnElement.innerHTML = originalContent; 
                    btnElement.disabled = false;
                    showToast('Terjadi kesalahan jaringan!'); 
                    // REVERT OPTIMISTIC UPDATE
                    let badge = document.getElementById('globalCartBadge');
                    if (badge) {
                        badge.innerText = originalCount;
                        if (originalCount > 0) {
                            badge.classList.remove('hidden');
                            badge.classList.add('flex');
                        } else {
                            badge.classList.add('hidden');
                            badge.classList.remove('flex');
                        }
                    }
                    localStorage.setItem('cartCount', originalCount);
                }
            });
        };"""

# We need to find the entire block of window.tambahKeranjangCerdas
# The block starts with "window.tambahKeranjangCerdas = function(gameId, isBuyNow, btnElement) {"
# and ends with "};" after the catch block.

updated = 0
for root, dirs, files in os.walk(directory):
    for f in files:
        if f.endswith('.blade.php'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Using regex to find the block
            # Match from "window.tambahKeranjangCerdas" up to the next "\n        };" or similar
            pattern = re.compile(r'window\.tambahKeranjangCerdas\s*=\s*function\(gameId,\s*isBuyNow,\s*btnElement\)\s*\{.*?\n\s*\};\s*$', re.MULTILINE | re.DOTALL)
            
            # A more generic pattern that matches the function up to its catch block end
            pattern = re.compile(r'window\.tambahKeranjangCerdas\s*=\s*function\(gameId,\s*isBuyNow,\s*btnElement\)\s*\{.*?\.catch\(\(\)\s*=>\s*\{.*?\n\s*\}\s*\);\s*\}[\;]?', re.DOTALL)
            
            new_content, count = pattern.subn(new_func, content)
            if count > 0:
                with open(path, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                updated += 1
                print(f"Updated {path}")

print(f"Total updated: {updated}")
