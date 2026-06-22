import re

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\cart.blade.php', 'r', encoding='utf-8') as f:
    c = f.read()

# For onSuccess
success_target = """                            onSuccess: function(result) {
                                // Clear localStorage keranjang
                                localStorage.setItem('cartCount', '0');
                                localStorage.setItem('cart_cache', '[]');
                                
                                // Langsung tampilkan modal tanpa menunggu respon (Instan!)"""
success_replacement = """                            onSuccess: function(result) {
                                // Clear localStorage keranjang
                                localStorage.setItem('cartCount', '0');
                                localStorage.setItem('cart_cache', '[]');
                                if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                                if (typeof renderCartPage === 'function') renderCartPage([]);
                                
                                // Langsung tampilkan modal tanpa menunggu respon (Instan!)"""
c = c.replace(success_target, success_replacement)

# For onPending
pending_target = """                            onPending: function(result) {
                                // Clear localStorage keranjang karena transaksi sudah tercatat
                                localStorage.setItem('cartCount', '0');
                                localStorage.setItem('cart_cache', '[]');
                                if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                                
                                showPaymentModal('warning', 'Menunggu pembayaran diselesaikan!', '/orders');
                            },"""
pending_replacement = """                            onPending: function(result) {
                                // Clear localStorage keranjang karena transaksi sudah tercatat
                                localStorage.setItem('cartCount', '0');
                                localStorage.setItem('cart_cache', '[]');
                                if (typeof window.syncCartBadge === 'function') window.syncCartBadge();
                                if (typeof renderCartPage === 'function') renderCartPage([]);
                                
                                showPaymentModal('warning', 'Menunggu pembayaran diselesaikan!', '/orders');
                            },"""
c = c.replace(pending_target, pending_replacement)

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\cart.blade.php', 'w', encoding='utf-8') as f:
    f.write(c)

print("cart.blade.php updated to render empty cart instantly")
