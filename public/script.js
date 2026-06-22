// ============================================================
// GAMEVAULT - script.js (VERSI LARAVEL)
// Perubahan utama:
//   1. Semua URL fetch diubah dari .php ke route Laravel
//   2. CSRF token ditambahkan di setiap POST request
//   3. Tidak ada perubahan logika/tampilan lainnya
// ============================================================

// --- AMBIL CSRF TOKEN DARI META TAG (WAJIB ADA DI SETIAP HALAMAN BLADE) ---
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// --- VARIABLES ---
let selectedDevice = '';
let selectedGenres = [];
let currentGames = [];
let cart = [];
let myWishlistIDs = [];

// --- LOGIKA DEBUGGING ---
console.log("Script.js Laravel Version Loaded");

// --- CUSTOM TOAST NOTIFICATION ---
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    let icon = '';
    if (type === 'success') icon = '✅';
    else if (type === 'error') icon = '⚠️';
    else icon = 'ℹ️';

    const toast = document.createElement('div');
    toast.className = `toast-box ${type} group`;
    toast.innerHTML = `
        <span class="text-xl">${icon}</span>
        <span class="text-sm font-medium flex-1">${message}</span>
        <button class="absolute right-3 top-1/2 -translate-y-1/2 text-white/50 group-hover:text-white transition-colors text-lg font-bold p-1">✕</button>
    `;

    const autoDismiss = () => {
        if (!toast.parentElement) return;
        toast.classList.add('toast-exit-smooth');
        toast.addEventListener('animationend', () => {
            if (toast.parentElement) toast.remove();
        });
    };

    const timer = setTimeout(autoDismiss, 3000);
    toast.onclick = () => { clearTimeout(timer); toast.remove(); };
    container.appendChild(toast);
}

// ============================================================
// CART LOGIC - URL DIUBAH KE ROUTE LARAVEL
// ============================================================

const desktopCartBtn = document.getElementById('desktopCartBtn');
const mobileCartBtn  = document.getElementById('mobileCartBtn');

function formatRupiah(angka) {
    if (angka === "Gratis") return "Gratis";
    if (typeof angka === 'string') {
        let cleanNum = angka.replace(/[^0-9]/g, '');
        if (cleanNum === '') return 'Rp 0';
        angka = parseInt(cleanNum);
    }
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}

// UPDATE CART UI - URL: /cart/get
function updateCartUI() {
    fetch('/cart/get', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,   // ← WAJIB untuk POST di Laravel
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            cart = data.data;
            const count = cart.length;

            ['desktopCartCount', 'mobileCartCount'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = count;
                    el.classList.toggle('hidden', count === 0);
                }
            });

            const cartListDiv = document.getElementById('cartItemsList');
            if (cartListDiv) renderCartItems(cartListDiv);

            // NEW: Sync cart_cache
            let cc = cart.map(item => String(item.game_id || item.id));
            localStorage.setItem('cartCount', count);
            localStorage.setItem('cart_cache', JSON.stringify(cc));
            if (typeof window.syncGameCardLabels === 'function') window.syncGameCardLabels();

            let totalHarga = 0;
            cart.forEach(item => {
                let priceNum = parseFloat(item.price);
                if (!isNaN(priceNum)) totalHarga += priceNum;
            });

            const totalPriceEl = document.getElementById('cartTotalPrice');
            const totalItemEl  = document.getElementById('cartTotalItem');
            if (totalPriceEl) totalPriceEl.textContent = formatRupiah(totalHarga);
            if (totalItemEl)  totalItemEl.textContent  = count;

        } else if (data.message === 'Belum login') {
            cart = [];
            const dc = document.getElementById('desktopCartCount');
            const mc = document.getElementById('mobileCartCount');
            if (dc) dc.classList.add('hidden');
            if (mc) mc.classList.add('hidden');
        }
    })
    .catch(err => console.error('updateCartUI error:', err));
}

// UPDATE WISHLIST UI - URL: /get_wishlist
function updateWishlistUI() {
    fetch('/get_wishlist?t=' + new Date().getTime())
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const list = data.data || [];
            const count = list.length;
            const badge = document.getElementById('wishlistCount');
            if (badge) {
                badge.innerText = count;
                badge.classList.toggle('hidden', count === 0);
            }
            
            // NEW: SYNC WISHLIST CACHE
            let w = list.map(item => String(item.real_game_id || item.id));
            localStorage.setItem('wishlist', JSON.stringify(w));
            if (typeof window.syncGameCardLabels === 'function') window.syncGameCardLabels();
        }
    })
    .catch(err => console.error("Gagal update angka wishlist:", err));
}

document.addEventListener('DOMContentLoaded', () => {
    updateWishlistUI();
});

// TAMBAH KE KERANJANG - URL: /cart_process
function addToCart(gameId) {
    if (!gameId) { alert("ID Game tidak valid!"); return; }

    console.log("Mengirim request untuk ID:", gameId);

    fetch('/cart_process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken          // ← CSRF Token Laravel
        },
        body: JSON.stringify({ product_id: gameId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('Berhasil masuk keranjang', 'success');

            const desktopBadge = document.getElementById('desktopCartCount');
            const mobileBadge  = document.getElementById('mobileCartCount');
            const totalItemEl  = document.getElementById('cartTotalItem');

            if (data.cart_count !== undefined) {
                if (desktopBadge) { desktopBadge.innerText = data.cart_count; desktopBadge.classList.remove('hidden'); }
                if (mobileBadge)  { mobileBadge.innerText  = data.cart_count; mobileBadge.classList.remove('hidden'); }
                if (totalItemEl)    totalItemEl.innerText  = data.cart_count;
                localStorage.setItem('cartCount', data.cart_count);
            }

            let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
            if (!cc.includes(String(gameId))) {
                cc.push(String(gameId));
                localStorage.setItem('cart_cache', JSON.stringify(cc));
            }
            if (typeof window.syncGameCardLabels === 'function') {
                window.syncGameCardLabels();
            }

            if (typeof updateCartUI === 'function') updateCartUI();

        } else if (data.message && data.message.toLowerCase().includes('login')) {
            window.location.href = '/';
        } else {
            showToast(data.message || 'Gagal menambahkan', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Gagal menghubungi server', 'error');
    });
}

// TOGGLE WISHLIST - URL: /wishlist_process
function toggleWishlist(gameId, btnElement) {
    fetch('/wishlist_process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken          // ← CSRF Token Laravel
        },
        body: JSON.stringify({ product_id: gameId })
    })
    .then(res => res.json())
    .then(data => {
        let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
        
        if (data.status === 'added') {
            if (btnElement) setHeartStyle(btnElement, true);
            showToast(data.message, "success");
            if (typeof myWishlistIDs !== 'undefined') {
                if (!myWishlistIDs.includes(parseInt(gameId))) {
                    myWishlistIDs.push(parseInt(gameId));
                }
            }
            if (!wishlist.includes(String(gameId)) && !wishlist.includes(Number(gameId))) {
                wishlist.push(String(gameId));
            }
        } else if (data.status === 'removed') {
            if (btnElement) setHeartStyle(btnElement, false);
            showToast(data.message, "info");
            if (typeof myWishlistIDs !== 'undefined') {
                myWishlistIDs = myWishlistIDs.filter(id => id !== parseInt(gameId));
            }
            wishlist = wishlist.filter(id => id != gameId && id !== String(gameId));
        } else {
            if (data.message && data.message.toLowerCase().includes('login')) {
                document.getElementById('loginModal')?.classList.remove('hidden');
            } else {
                showToast(data.message, "error");
            }
        }

        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        
        if (typeof window.syncGameCardLabels === 'function') {
            window.syncGameCardLabels();
        }

        if (typeof updateAllVisuals === 'function') updateAllVisuals();

        const wishlistBadge = document.getElementById('wishlistCount');
        if (wishlistBadge && typeof myWishlistIDs !== 'undefined') {
            wishlistBadge.innerText = myWishlistIDs.length;
            wishlistBadge.classList.toggle('hidden', myWishlistIDs.length === 0);
        }
        
        if (typeof syncWishlistBadge === 'function') syncWishlistBadge();
    })
    .catch(err => console.error("Error wishlist:", err));
}

// HAPUS DARI KERANJANG - URL: /cart/remove
function removeFromCart(index) {
    if (!cart[index]) return;
    const gameId = cart[index].game_id;

    fetch('/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken          // ← CSRF Token Laravel
        },
        body: JSON.stringify({ game_id: gameId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('Item dihapus dari keranjang', 'info');
            
            let cc = JSON.parse(localStorage.getItem('cart_cache')) || [];
            cc = cc.filter(id => id != gameId && id !== String(gameId));
            localStorage.setItem('cart_cache', JSON.stringify(cc));
            
            if (typeof window.syncGameCardLabels === 'function') {
                window.syncGameCardLabels();
            }
            
            updateCartUI();
        } else {
            showToast('Gagal menghapus item', 'error');
        }
    });
}

// CHECKOUT - URL: /checkout
function processCheckout() {
    if (cart.length === 0) return showToast('Keranjang kosong!', 'error');

    const btn = document.getElementById('btnCheckout');
    const originalText = btn.innerText;
    btn.innerText = "Membuka Payment Gateway...";
    btn.disabled = true;

    fetch('/checkout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.is_free) {
                showToast("Klaim Gratis Sukses!", 'success');
                const modal = document.querySelector('.fixed.inset-0');
                if (modal) modal.remove();
                showSuccessModal(data.order_id);
                updateCartUI();
                return;
            }
            // Panggil Pop-Up Midtrans!
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    showToast("Pembayaran Sukses!", 'success');
                    // Kirim info ke server bahwa ini sukses biar email PDF dikirim
                    fetch('/checkout/success', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: data.order_id })
                    }).then(() => {
                        const modal = document.querySelector('.fixed.inset-0');
                        if (modal) modal.remove();
                        showSuccessModal(data.order_id);
                        updateCartUI();
                    });
                },
                onPending: function(result) {
                    showToast("Menunggu Pembayaran...", 'info');
                    const modal = document.querySelector('.fixed.inset-0');
                    if (modal) modal.remove();
                    updateCartUI();
                },
                onError: function(result) {
                    showToast("Pembayaran Gagal!", 'error');
                },
                onClose: function() {
                    showToast("Kamu menutup halaman sebelum membayar.", 'error');
                }
            });
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => showToast("Terjadi kesalahan koneksi", 'error'))
    .finally(() => { if (btn) { btn.innerText = originalText; btn.disabled = false; } });
}

// LOAD WISHLIST STATUS - URL: /get_wishlist
function checkWishlistStatus() {
    fetch('/get_wishlist?t=' + new Date().getTime())
    .then(res => res.json())
    .then(data => {
        const list = Array.isArray(data) ? data : (data.data || []);
        myWishlistIDs = list.map(item => parseInt(item.real_game_id || item.id));
        updateAllVisuals();

        const badge = document.getElementById('wishlistCount');
        if (badge) {
            badge.innerText = list.length;
            badge.classList.toggle('hidden', list.length === 0);
        }
    })
    .catch(err => console.error("Gagal load wishlist:", err));
}

// SHOW WISHLIST MODAL - URL: /get_wishlist
function showWishlistModal() {
    disableScroll();

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4';
    modal.id = 'wishlist-modal';

    modal.innerHTML = `
        <div class="absolute inset-0 bg-black/20 backdrop-blur-sm transition-opacity"
             onclick="closeWishlistModal()"></div>
        <div class="relative w-full max-w-5xl bg-[#111] border border-white/30 rounded-2xl shadow-[0_0_50px_rgba(255,255,255,0.1)] overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-200">
            <button class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors z-50 p-2 hover:bg-white/5 rounded-full"
                    onclick="closeWishlistModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="p-6 border-b border-white/30 bg-[#111] text-center relative overflow-hidden">
                <div class="flex justify-center items-center gap-3 mb-2">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="w-9 h-9 text-white drop-shadow-[0_0_10px_rgba(255,255,255,0.8)]">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z" fill="currentColor"/>
                    </svg>
                    <h2 class="text-4xl font-black text-white tracking-widest gaming-title">MY WISHLIST</h2>
                </div>
                <p class="text-gray-400 text-sm">Koleksi game impianmu</p>
            </div>
            <div id="wishlistContainer" class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-black grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="col-span-full flex justify-center py-20">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // ← URL DIUBAH KE ROUTE LARAVEL
    fetch('/get_wishlist?t=' + new Date().getTime())
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('wishlistContainer');
        container.innerHTML = '';

        let listGames = [];
        if (Array.isArray(data)) { listGames = data; }
        else if (data.status === 'success' && data.data) { listGames = data.data; }

        if (!listGames || listGames.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-16 flex flex-col items-center justify-center opacity-60">
                    <div class="text-6xl mb-4 grayscale">💔</div>
                    <h3 class="text-xl font-bold text-gray-300">Wishlist Kosong</h3>
                    <p class="text-sm text-gray-500 mt-2">Yuk cari game favoritmu!</p>
                </div>`;
            return;
        }

        listGames.forEach(game => {
            const idPasti = game.real_game_id || game.id;
            const imgUrl  = game.image;
            const card = document.createElement('div');
            card.className = 'relative flex flex-col bg-[#111] border border-[#333] rounded-2xl overflow-hidden group hover:border-white/50 hover:shadow-[0_0_20px_rgba(255,255,255,0.2)] transition-all duration-300';
            card.innerHTML = `
                <div class="relative h-44 overflow-hidden bg-black">
                    <img src="${imgUrl}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.src='assets/no-image.jpg'">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#111] via-transparent to-transparent"></div>
                    <div class="absolute top-3 right-3 bg-black/80 backdrop-blur-sm px-3 py-1.5 rounded-xl border border-white/10 shadow-lg">
                        <span class="text-white font-bold text-xs">${game.price_formatted}</span>
                    </div>
                </div>
                <div class="p-5 flex flex-col flex-1 border-t border-[#222]">
                    <h3 class="text-white font-bold text-base leading-tight mb-4 line-clamp-2 h-10 group-hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.8)] transition-all">${game.name}</h3>
                    <div class="flex items-center gap-3 mt-auto">
                        <button onclick="hapusDariWishlist(${idPasti})"
                                class="flex items-center justify-center h-12 w-12 shrink-0 bg-red-500/10 hover:bg-red-600 text-red-500 hover:text-white rounded-xl transition-all duration-300 border border-red-500/20 active:scale-90">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button onclick="addToCart(${idPasti}); closeWishlistModal();"
                                class="flex-1 flex items-center justify-center gap-2 h-12 bg-white text-black font-extrabold rounded-xl hover:bg-gray-200 transition-all duration-300 active:scale-95 px-2">
                            <span class="text-sm tracking-wide font-bold">BELI</span>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    })
    .catch(err => {
        console.error(err);
        const c = document.getElementById('wishlistContainer');
        if (c) c.innerHTML = `<div class="text-center text-red-400 col-span-full mt-10">Gagal memuat data :(</div>`;
    });
}

function closeWishlistModal() {
    const modal = document.getElementById('wishlist-modal');
    if (modal) { modal.remove(); enableScroll(); }
}

function hapusDariWishlist(gameId) {
    toggleWishlist(gameId, null);
    setTimeout(() => { closeWishlistModal(); showWishlistModal(); }, 100);
}

// ============================================================
// SEMUA FUNGSI DI BAWAH TIDAK BERUBAH (Tidak ada pemanggilan URL)
// ============================================================

function showCartModal() {
    disableScroll();
    let totalHarga = 0;
    cart.forEach(item => { let p = parseFloat(item.price); if (!isNaN(p)) totalHarga += p; });

    const cartModal = document.createElement('div');
    cartModal.className = 'fixed inset-0 bg-black/10 backdrop-blur-md z-[100] flex items-center justify-center p-4 animate-fadeIn';
    cartModal.innerHTML = `
        <div class="bg-[#111]/95 border-2 border-white/30 rounded-2xl p-6 max-w-2xl w-full mx-4 relative max-h-[90vh] flex flex-col shadow-[0_0_50px_rgba(255,255,255,0.2)]">
            <button class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors text-2xl z-50 p-2"
                    onclick="this.closest('.fixed').remove(); enableScroll()">✕</button>
            <div class="text-center mb-6 border-b border-white/30 pb-4 flex justify-center items-center gap-3">
                <h2 class="text-2xl font-bold text-white gaming-title">KERANJANG BELANJA</h2>
            </div>
            <div id="cartItemsList" class="flex-1 overflow-y-auto space-y-4 mb-6 pr-2 custom-scrollbar"></div>
            <div class="border-t border-white/30 pt-4 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Item:</span>
                    <span id="cartTotalItem" class="text-white font-bold">${cart.length}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-white text-lg font-bold">Total Harga:</span>
                    <span id="cartTotalPrice" class="text-yellow-400 font-bold text-2xl">${formatRupiah(totalHarga)}</span>
                </div>
                <button id="btnCheckout" onclick="processCheckout()" class="w-fit mx-auto block px-12 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 text-white font-bold py-3 rounded-full transition-all shadow-lg shadow-green-900/50">CHECKOUT SEKARANG</button>
            </div>
        </div>`;

    const cartListDiv = cartModal.querySelector('#cartItemsList');
    renderCartItems(cartListDiv);
    document.body.appendChild(cartModal);
    cartModal.addEventListener('click', function(e) {
        if (e.target === cartModal) { cartModal.remove(); enableScroll(); }
    });
}

function renderCartItems(container) {
    if (cart.length === 0) {
        container.innerHTML = `<div class="text-center py-12 text-gray-500"><div class="text-6xl mb-4 opacity-50">🕸️</div><p class="text-lg">Keranjang Anda masih kosong.</p></div>`;
        return;
    }
    container.innerHTML = cart.map((item, index) => `
        <div class="flex items-center bg-[#111] p-4 rounded-xl border border-[#333] hover:border-white/50 transition-all group/item">
            <img src="${item.image}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg mr-4 shadow-md">
            <div class="flex-1">
                <h4 class="font-bold text-white text-lg leading-tight mb-1">${item.name}</h4>
                <span>${item.price_formatted || formatRupiah(item.price)}</span>
            </div>
            <button onclick="removeFromCart(${index})"
                class="bg-red-500/10 hover:bg-red-600 text-red-500 hover:text-white p-3 rounded-xl transition-all duration-300">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                    <path d="M4 6H20M16 6L15.7294 5.18807C15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>`).join('');
}

function showSuccessModal(orderId) {
    const modal = document.createElement('div');
    modal.id = 'checkoutSuccessModal';
    modal.className = 'fixed inset-0 bg-black/20 backdrop-blur-md z-[200] flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="modal-content relative rounded-3xl p-8 max-w-md w-full text-center overflow-hidden bg-[#111] border border-green-500/30">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-emerald-600"></div>
            <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2 gaming-title tracking-wide">PEMBELIAN BERHASIL!</h2>
            <p class="text-gray-400 mb-6 text-sm">Terima kasih telah berbelanja di GameVault.<br>Game Anda siap dimainkan.</p>
            <div class="bg-black/30 border border-green-500/30 rounded-xl p-4 mb-8">
                <span class="text-xs text-green-400 uppercase tracking-widest mb-1 block">Order ID</span>
                <span class="text-2xl font-mono font-bold text-white">#${orderId}</span>
            </div>
            <button onclick="document.getElementById('checkoutSuccessModal').remove()"
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 text-white font-bold py-4 rounded-xl transition-all shadow-lg">
                KEMBALI KE HOME
            </button>
        </div>`;
    document.body.appendChild(modal);
}

if (desktopCartBtn) desktopCartBtn.addEventListener('click', showCartModal);
if (mobileCartBtn)  mobileCartBtn.addEventListener('click', () => { showCartModal(); });

// --- NAVIGATION LOGIC ---
const hamburgerMenu    = document.getElementById('hamburgerMenu');
const hamburgerOverlay = document.getElementById('hamburgerOverlay');
const closeHamburgerMenu = document.getElementById('closeHamburgerMenu');

if (hamburgerMenu) {
    hamburgerMenu.addEventListener('click', function() {
        hamburgerMenu.classList.toggle('active');
        if (hamburgerOverlay) hamburgerOverlay.classList.toggle('hidden');
    });
}
if (closeHamburgerMenu) {
    closeHamburgerMenu.addEventListener('click', function() {
        hamburgerMenu.classList.remove('active');
        if (hamburgerOverlay) hamburgerOverlay.classList.add('hidden');
    });
}

// --- SMOOTH SCROLL ---
function smoothScrollTo(elementId) {
    const target = document.getElementById(elementId);
    if (!target) return;
    const headerOffset = 150;
    const startPosition = window.pageYOffset;
    const targetPosition = target.getBoundingClientRect().top + startPosition - headerOffset;
    const distance = targetPosition - startPosition;
    const duration = Math.abs(distance) < 300 ? 800 : 1200;
    let startTime = null;
    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const ease = (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        const run = ease(timeElapsed / duration) * distance + startPosition;
        window.scrollTo(0, run);
        if (timeElapsed < duration) requestAnimationFrame(animation);
    }
    requestAnimationFrame(animation);
}

function toggleElement(elementId, show) {
    const el = document.getElementById(elementId);
    if (!el) return;
    if (show) {
        el.classList.remove('hidden', 'opacity-0', 'max-h-0', 'mb-0', 'invisible', 'overflow-hidden');
        el.classList.add('block', 'mb-16', 'opacity-100');
        setTimeout(() => { if (el.classList.contains('opacity-100')) el.classList.add('overflow-visible'); }, 300);
    } else {
        el.classList.remove('opacity-100', 'overflow-visible');
        el.classList.add('opacity-0');
        setTimeout(() => {
            if (el.classList.contains('opacity-0')) {
                el.classList.add('hidden', 'max-h-0', 'mb-0');
                el.classList.remove('block', 'mb-16');
            }
        }, 300);
    }
}

function updateGenreCounter() {
    const counterElement   = document.getElementById('homeSelectedCount');
    const counterContainer = document.getElementById('homeGenreCounter');
    const clearButton      = document.getElementById('homeClearGenres');
    if (counterElement) counterElement.textContent = selectedGenres.length;
    if (counterContainer && clearButton) {
        if (selectedGenres.length > 0) { counterContainer.classList.remove('hidden'); clearButton.classList.remove('hidden'); }
        else { counterContainer.classList.add('hidden'); clearButton.classList.add('hidden'); }
    }
}

document.querySelectorAll('[data-device]').forEach(card => {
    card.addEventListener('click', function() {
        const device = this.dataset.device;
        const genreSection = document.getElementById('homeGenreSection');
        const gamesSection = document.getElementById('homeGamesSection');
        const isGamesOpen  = gamesSection && gamesSection.classList.contains('opacity-100') && !gamesSection.classList.contains('hidden');
        const isGenreActive = selectedGenres.length > 0;

        if (selectedDevice === device) {
            this.classList.remove('selected');
            selectedDevice = '';
            selectedGenres = [];
            document.querySelectorAll('.genre-tag').forEach(tag => tag.classList.remove('selected'));
            updateGenreCounter();
            smoothScrollTo('home');
            setTimeout(() => { toggleElement('homeGenreSection', false); toggleElement('homeGamesSection', false); }, 300);
        } else {
            document.querySelectorAll('[data-device]').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedDevice = device;
            if (genreSection) {
                genreSection.classList.remove('hidden', 'opacity-0', 'max-h-0', 'mb-0', 'invisible', 'overflow-hidden');
                genreSection.classList.add('block', 'mb-16', 'opacity-100', 'overflow-visible');
            }
            if (isGamesOpen) {
                if (isGenreActive) showGames();
                else showAllGamesForDevice();
                toggleElement('homeGamesSection', true);
            } else {
                selectedGenres = [];
                document.querySelectorAll('.genre-tag').forEach(tag => tag.classList.remove('selected'));
                updateGenreCounter();
                toggleElement('homeGamesSection', false);
            }
            setTimeout(() => { smoothScrollTo('homeGenreSection'); }, 100);
        }
    });
});

document.querySelectorAll('.genre-tag').forEach(tag => {
    tag.addEventListener('click', function() {
        const genre = this.dataset.genre;
        if (selectedGenres.includes(genre)) {
            selectedGenres = selectedGenres.filter(g => g !== genre);
            this.classList.remove('selected');
        } else {
            selectedGenres.push(genre);
            this.classList.add('selected');
        }
        updateGenreCounter();
        if (selectedGenres.length > 0) showGames();
        else { smoothScrollTo('homeGenreSection'); setTimeout(() => toggleElement('homeGamesSection', false), 500); }
    });
});

if (document.getElementById('homeShowAllGames')) {
    document.getElementById('homeShowAllGames').addEventListener('click', function() {
        const gamesSection = document.getElementById('homeGamesSection');
        const isVisible = gamesSection && !gamesSection.classList.contains('hidden') && gamesSection.classList.contains('opacity-100');
        if (isVisible && selectedGenres.length === 0) {
            smoothScrollTo('homeGenreSection');
            setTimeout(() => toggleElement('homeGamesSection', false), 500);
        } else {
            showAllGamesForDevice();
        }
    });
}

function showAllGamesForDevice() {
    let allGames = [];
    if (selectedDevice && gameDatabase[selectedDevice]) {
        Object.keys(gameDatabase[selectedDevice]).forEach(genre => {
            if (gameDatabase[selectedDevice][genre]) {
                gameDatabase[selectedDevice][genre].forEach(game => {
                    if (!allGames.find(g => g.name === game.name)) allGames.push(game);
                });
            }
        });
    }
    currentGames = allGames;
    displayGames(currentGames);
    toggleElement('homeGamesSection', true);
    setTimeout(() => smoothScrollTo('homeGamesGrid'), 200);
    selectedGenres = [];
    document.querySelectorAll('.genre-tag').forEach(tag => tag.classList.remove('selected'));
}

function showGames() {
    let gamesToShow = [];
    if (selectedDevice && gameDatabase[selectedDevice]) {
        selectedGenres.forEach(genre => {
            if (gameDatabase[selectedDevice][genre]) {
                gameDatabase[selectedDevice][genre].forEach(game => {
                    if (!gamesToShow.find(g => g.name === game.name)) gamesToShow.push(game);
                });
            }
        });
    } else { currentGames = []; displayGames([]); toggleElement('homeGamesSection', false); return; }
    currentGames = gamesToShow;
    displayGames(currentGames);
    toggleElement('homeGamesSection', true);
    setTimeout(() => smoothScrollTo('homeGamesGrid'), 200);
}

function performSearch(searchTerm) {
    let searchResults = [];
    const devicesToSearch = selectedDevice ? [selectedDevice] : Object.keys(gameDatabase);
    devicesToSearch.forEach(device => {
        if (gameDatabase[device]) {
            Object.keys(gameDatabase[device]).forEach(genre => {
                gameDatabase[device][genre].forEach(game => {
                    if (game.name.toLowerCase().includes(searchTerm)) {
                        if (!searchResults.find(g => g.name === game.name)) searchResults.push(game);
                    }
                });
            });
        }
    });
    currentGames = searchResults;
    displayGames(searchResults);
    toggleElement('homeGamesSection', true);
    if (searchResults.length > 0) setTimeout(() => smoothScrollTo('homeGamesGrid'), 200);
    else {
        const grid = document.getElementById('homeGamesGrid');
        if (grid) grid.innerHTML = `<div class="col-span-full text-center py-12"><div class="text-6xl mb-4">🔍</div><h3 class="text-2xl font-bold text-white mb-2">Game Tidak Ditemukan</h3><p class="text-gray-400">Coba kata kunci lain</p></div>`;
    }
}

function displayGames(games) {
    const gamesGrid = document.getElementById('homeGamesGrid');
    if (!gamesGrid) return;
    gamesGrid.innerHTML = '';
    if (games.length === 0) {
        gamesGrid.innerHTML = `<div class="col-span-full text-center py-20"><div class="text-6xl mb-4 grayscale opacity-50">🎮</div><h3 class="text-xl font-bold text-white">Game Tidak Ditemukan</h3><p class="text-gray-500 mt-2">Coba kata kunci atau kategori lain.</p></div>`;
        return;
    }
    games.forEach((game) => {
        const finalID = game.id ? game.id : (game.real_game_id ? game.real_game_id : null);
        if (!finalID) return;
        const gameCard = document.createElement('div');
        gameCard.className = 'gaming-card group relative bg-[#0a0a0a] border border-[#333] rounded-2xl p-4 flex flex-col h-full hover:-translate-y-2 transition-all duration-300 hover:border-white hover:shadow-[0_0_25px_rgba(255,255,255,0.3)]';
        gameCard.setAttribute('data-game-id', finalID);
        let shortDesc = game.synopsis ? game.synopsis : (game.description || "Deskripsi belum tersedia.");
        const isLiked = (typeof myWishlistIDs !== 'undefined') && myWishlistIDs.includes(parseInt(finalID));
        const wishlistText  = isLiked ? "Hapus dari Wishlist" : "Simpan ke Wishlist";
        const wishlistColor = isLiked ? "text-red-500" : "text-gray-300";
        const wishlistFill  = isLiked ? "currentColor" : "none";
        const safeName = game.name.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const safeDesc = shortDesc.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        gameCard.innerHTML = `
            <div class="relative w-full h-40 mb-3 overflow-hidden rounded-xl bg-black">
                <img src="${game.image}" alt="${game.name}" class="w-full h-full object-contain hover:scale-110 transition-transform duration-500" onerror="this.src='assets/no-image.jpg'">
            </div>
            <div class="absolute top-6 right-6 z-50">
                <button onclick="toggleGameMenu('${finalID}', event)" class="tombol-rahasia bg-black/80 text-white p-1.5 rounded-full backdrop-blur-md hover:bg-white hover:text-black shadow-lg border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>
                <div id="menu-${finalID}" class="game-context-menu hidden absolute right-0 top-8 mt-1 w-max min-w-[180px] bg-[#1a1a1a] border border-[#333] rounded-xl shadow-2xl overflow-hidden z-50">
                    <button onclick="event.stopPropagation(); toggleWishlist(${finalID}, this); closeAllMenus()"
                            class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold ${wishlistColor} hover:bg-[#252525] transition-colors text-left border-b border-[#333]">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="${wishlistFill}" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span class="whitespace-nowrap">${wishlistText}</span>
                    </button>
                    <button onclick="event.stopPropagation(); addToCart(${finalID}); closeAllMenus()"
                            class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold text-gray-300 hover:bg-[#252525] hover:text-cyan-400 transition-colors text-left">
                        <span class="whitespace-nowrap">Masuk Keranjang</span>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <h3 class="text-lg font-bold text-white leading-tight truncate font-orbitron">${game.name}</h3>
                <div class="mt-2 flex items-center gap-2">
                    <div class="bg-[#111] border border-[#444] rounded-full px-3 py-1">
                        <span class="text-white font-bold text-xs">${game.price}</span>
                    </div>
                    <span class="text-[10px] uppercase tracking-wider text-gray-500 border border-gray-700 px-2 py-1 rounded">${game.genre_type || 'GAME'}</span>
                </div>
            </div>
            <div class="flex-grow mb-4 border-t border-gray-800 pt-2">
                <p class="text-gray-400 text-xs leading-relaxed">${shortDesc}</p>
            </div>
            <div class="mt-auto">
                <button onclick="openGameModal('${encodeURIComponent(JSON.stringify(game)).replace(/'/g, '%27')}')"
                        class="w-full py-3 rounded-lg border border-[#555] text-gray-300 text-xs font-bold hover:bg-[#222] hover:text-white transition-colors tracking-widest uppercase">
                    Lihat Detail
                </button>
            </div>
        `;
        gamesGrid.appendChild(gameCard);
    });
}

// --- HELPER FUNCTIONS (Tidak Berubah) ---
function normalizeGenreName(rawName) {
    if (!rawName) return 'action';
    let name = rawName.toLowerCase().trim();
    if (name.includes('shooter') || name === 'fps') return 'fps';
    if (name.includes('rpg') || name.includes('role')) return 'rpg';
    if (name.includes('racing') || name.includes('balap')) return 'racing';
    if (name.includes('strategy')) return 'strategy';
    if (name.includes('horror')) return 'horror';
    if (name.includes('open') && name.includes('world')) return 'open world';
    return name;
}

function getGenreColorTheme(genreName) {
    const genre = normalizeGenreName(genreName);
    const colors = { 'action': 'red', 'rpg': 'purple', 'fps': 'yellow', 'strategy': 'blue', 'racing': 'green', 'puzzle': 'indigo', 'horror': 'gray', 'sports': 'teal', 'open world': 'lime' };
    return colors[genre] || 'cyan';
}

function getGameSpecs(gameName, platformType) {
    const name = gameName.toLowerCase();
    const row = (label, value) => `<div class="flex justify-between items-start py-2 border-b border-gray-800 last:border-0"><span class="text-gray-500 font-bold text-xs uppercase w-24 shrink-0">${label}</span><span class="text-gray-300 text-sm text-right font-mono flex-1 pl-4">${value}</span></div>`;
    return {
        min: row("OS","Windows 10 64-bit") + row("CPU","Intel Core i3 / Ryzen 3") + row("RAM","8 GB") + row("GPU","GTX 1050 / RX 560") + row("Storage","50 GB"),
        rec: row("OS","Windows 11 64-bit") + row("CPU","Intel Core i5 / Ryzen 5") + row("RAM","16 GB") + row("GPU","RTX 3060 / RX 6600") + row("Storage","50 GB SSD")
    };
}

function getPlatformSVG(platform) {
    const p = platform.toLowerCase();
    if (p === 'pc') return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor" class="w-full h-full"><path d="M29.861 22.931v-19.192h-27.723v19.192h13.328v4.265h-3.732v1.066h8.53v-1.066h-3.732v-4.265h13.328zM3.205 4.804h25.59v17.060h-25.59v-17.060z" /></svg>`;
    return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>`;
}

function openGameModal(gameDataEncoded) {
    let game;
    try { game = JSON.parse(decodeURIComponent(gameDataEncoded)); }
    catch (e) { console.error("Gagal membuka detail:", e); return; }

    const finalID = game.id ? game.id : (game.real_game_id ? game.real_game_id : null);
    const specs = getGameSpecs(game.name, game.platform_type || 'pc');
    const longDescription = game.description || game.synopsis || "Deskripsi detail belum tersedia.";
    const isLiked = (typeof myWishlistIDs !== 'undefined') && myWishlistIDs.includes(parseInt(finalID));
    const heartColorClass = isLiked ? "text-red-500" : "text-gray-400";
    const heartFill = isLiked ? "currentColor" : "none";
    const safeName = game.name.replace(/"/g, '&quot;').replace(/'/g, "\\'");
    const safeDesc = (game.description || game.synopsis || "").replace(/"/g, '&quot;').replace(/'/g, "\\'");

    // ==========================================
    // KODE TAMBAHAN UNTUK REVIEW & RATING
    // ==========================================
    let reviewHtml = `
        <div class="mt-8 border-t border-[#222] pt-8">
            <h4 class="text-white text-sm font-bold uppercase tracking-widest mb-4">Ulasan Pemain</h4>
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl drop-shadow-[0_0_10px_rgba(250,204,21,0.5)]">⭐</span>
                <h4 class="text-white font-bold text-2xl">${game.avg_rating || 0} <span class="text-gray-500 text-sm">/ 5</span></h4>
                <span class="text-cyan-500 font-bold text-sm bg-cyan-900/20 px-3 py-1 rounded-lg border border-cyan-500/30 ml-2">${game.total_reviews || 0} Ulasan</span>
            </div>
            <div class="space-y-3 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
    `;
                        
    if(game.reviews && game.reviews.length > 0) {
        game.reviews.forEach(rev => {
            let stars = '★'.repeat(rev.rating) + '☆'.repeat(5 - rev.rating);
            reviewHtml += `
                <div class="bg-[#111] p-4 rounded-xl border border-[#222] hover:border-[#333] transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-cyan-400 font-bold text-xs uppercase tracking-widest">${rev.nama}</p>
                        <p class="text-yellow-400 text-sm">${stars}</p>
                    </div>
                    <p class="text-gray-300 text-sm italic">"${rev.teks}"</p>
                </div>
            `;
        });
    } else {
        reviewHtml += `
            <div class="text-center py-8 bg-[#111] rounded-xl border border-dashed border-[#333]">
                <span class="text-4xl mb-2 block grayscale opacity-50">🌟</span>
                <p class="text-gray-500 text-sm">Belum ada ulasan untuk game ini.<br>Beli dan jadilah yang pertama memberi ulasan!</p>
            </div>
        `;
    }
    reviewHtml += `</div></div>`;
    // ==========================================

    document.body.style.overflow = 'hidden';
    const modalOverlay = document.createElement('div');
    modalOverlay.id = 'gameDetailModalOverlay';
    modalOverlay.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/95 backdrop-blur-xl animate-fadeIn';

    modalOverlay.innerHTML = `
        <div class="relative bg-[#0F0F0F] border border-[#333] rounded-2xl w-full max-w-5xl max-h-[95vh] flex flex-col shadow-2xl overflow-hidden">
            <button class="absolute top-4 right-4 z-50 p-2 bg-black/50 text-gray-400 rounded-full hover:bg-red-600 hover:text-white transition-all border border-white/10"
                    onclick="document.body.style.overflow = 'auto'; this.closest('#gameDetailModalOverlay').remove()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div class="w-full bg-[#050505] flex items-center justify-center p-6 border-b border-[#222] shrink-0">
                <img src="${game.image}" alt="${safeName}" class="max-w-full max-h-[40vh] object-contain shadow-[0_0_30px_rgba(0,0,0,0.5)] rounded-lg">
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-[#0F0F0F]">
                <div class="px-6 py-6 md:px-8 border-b border-[#222] bg-[#141414]">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h2 class="text-3xl md:text-4xl font-black text-white font-orbitron">${game.name}</h2>
                        <div class="px-5 py-2 bg-yellow-500 text-black font-extrabold text-2xl rounded shadow-[0_0_15px_rgba(234,179,8,0.4)]">
                            ${game.price}
                        </div>
                    </div>
                </div>
                <div class="p-6 md:p-8 space-y-8">
                    <div class="flex gap-5">
                        <div class="w-1 bg-gradient-to-b from-cyan-500 to-transparent rounded-full shrink-0"></div>
                        <div class="flex-1">
                            <h4 class="text-gray-400 text-xs font-bold uppercase tracking-[0.2em] mb-3">TENTANG GAME INI</h4>
                            <p class="text-gray-300 text-base leading-relaxed">${longDescription}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-bold uppercase tracking-widest mb-4">Spesifikasi Sistem</h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-[#111] rounded-lg border border-[#262626] p-4">
                                <div class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-3">MINIMUM</div>
                                ${specs.min}
                            </div>
                            <div class="bg-[#111] rounded-lg border border-cyan-900/40 p-4">
                                <div class="text-cyan-500 text-[10px] font-bold uppercase tracking-widest mb-3">RECOMMENDED</div>
                                ${specs.rec}
                            </div>
                        </div>
                    </div>
                    
                    ${reviewHtml}
                    
                </div>
            </div>
            <div class="p-5 border-t border-[#222] bg-[#141414] flex gap-3 shrink-0 z-20">
                <button id="wishlist-btn-modal-${finalID}"
                        onclick="toggleWishlist(${finalID}, this)"
                        class="h-12 w-12 flex items-center justify-center rounded-lg border border-[#333] bg-[#1a1a1a] ${heartColorClass} hover:bg-[#222] hover:text-red-500 transition-all group">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="${heartFill}" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6L7.5 5.25H16.5L17.25 6V19.3162L12 16.2051L6.75 19.3162V6ZM8.25 6.75V16.6838L12 14.4615L15.75 16.6838V6.75H8.25Z" stroke="currentColor"/>
                    </svg>
                </button>
                <button onclick="addToCart(${finalID}); document.body.style.overflow = 'auto'; this.closest('#gameDetailModalOverlay').remove()"
                        class="flex-1 h-12 bg-white text-black font-extrabold rounded-lg hover:bg-cyan-400 hover:scale-[1.01] transition-all shadow-lg flex items-center justify-center gap-3">
                    <span class="text-sm md:text-base tracking-wide">MASUKKAN KERANJANG</span>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modalOverlay);
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) { document.body.style.overflow = 'auto'; modalOverlay.remove(); }
    });
}

function showUniversalShareModal(title, desc, image) {
    const currentUrl = window.location.href;
    const encodedUrl = encodeURIComponent(currentUrl);
    const text = `Cek ini di GameVault: ${title}`;
    const encodedText = encodeURIComponent(text);
    const shareModal = document.createElement('div');
    shareModal.className = 'fixed inset-0 bg-black/20 backdrop-blur-md z-[110] flex items-center justify-center p-4 animate-fadeIn';
    shareModal.innerHTML = `
        <div class="bg-[#111] border border-cyan-500/50 rounded-2xl p-6 w-full max-w-sm text-center relative shadow-2xl">
            <button class="absolute top-3 right-3 text-gray-400 hover:text-white" onclick="this.closest('.fixed').remove()">✕</button>
            <h3 class="text-xl font-bold text-white mb-6">Bagikan ke Media Sosial</h3>
            <div class="space-y-3">
                <a href="https://wa.me/?text=${encodedText}%20${encodedUrl}" target="_blank" class="flex items-center justify-center gap-3 p-3 bg-green-600 hover:bg-green-500 rounded-xl transition-all text-white font-bold">WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}" target="_blank" class="flex items-center justify-center gap-3 p-3 bg-blue-600 hover:bg-blue-500 rounded-xl transition-all text-white font-bold">Facebook</a>
                <a href="https://twitter.com/intent/tweet?text=${encodedText}&url=${encodedUrl}" target="_blank" class="flex items-center justify-center gap-3 p-3 bg-black hover:bg-gray-800 border border-gray-600 rounded-xl transition-all text-white font-bold">X (Twitter)</a>
                <button onclick="navigator.clipboard.writeText('${currentUrl}'); showToast('Tautan berhasil disalin!', 'success');" class="w-full flex items-center justify-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded-xl transition-all text-white font-bold border border-gray-500">🔗 Copy Link</button>
            </div>
        </div>`;
    shareModal.addEventListener('click', function(e) { if (e.target === shareModal) shareModal.remove(); });
    document.body.appendChild(shareModal);
}

// --- WISHLIST SYSTEM ---
function setHeartStyle(btn, isLiked) {
    if (!btn) return;
    const svg = btn.querySelector('svg');
    if (isLiked) { btn.classList.remove('text-gray-400'); btn.classList.add('text-red-500'); if (svg) svg.setAttribute('fill', 'currentColor'); }
    else { btn.classList.remove('text-red-500'); btn.classList.add('text-gray-400'); if (svg) svg.setAttribute('fill', 'none'); }
}

function updateAllVisuals() {
    document.querySelectorAll('[id^="wishlist-btn-"]').forEach(btn => {
        const idStr  = btn.id.replace('wishlist-btn-modal-', '').replace('wishlist-btn-', '');
        const gameId = parseInt(idStr);
        setHeartStyle(btn, myWishlistIDs.includes(gameId));
    });
}

function toggleGameMenu(id, event) {
    if (event) event.stopPropagation();
    const menu = document.getElementById(`menu-${id}`);
    closeAllMenus();
    if (menu) menu.classList.toggle('hidden');
}

function closeAllMenus() {
    document.querySelectorAll('.game-context-menu').forEach(el => el.classList.add('hidden'));
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.game-context-menu') && !e.target.closest('button[onclick^="toggleGameMenu"]')) closeAllMenus();
});

// --- CAROUSEL ---
document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots   = document.querySelectorAll('.carousel-dot');
    let currentSlide = 0;
    let slideInterval;

    if (slides.length > 0) {
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('opacity-100', 'z-20');
                slide.classList.add('opacity-0', 'z-10');
                if (dots[i]) { dots[i].classList.remove('bg-white', 'w-8'); dots[i].classList.add('bg-white/30', 'w-3'); }
            });
            slides[index].classList.remove('opacity-0', 'z-10');
            slides[index].classList.add('opacity-100', 'z-20');
            if (dots[index]) { dots[index].classList.remove('bg-white/30', 'w-3'); dots[index].classList.add('bg-white', 'w-8'); }
            currentSlide = index;
        }
        window.nextSlide = function() { showSlide((currentSlide + 1) % slides.length); resetInterval(); };
        window.prevSlide = function() { showSlide((currentSlide - 1 + slides.length) % slides.length); resetInterval(); };
        window.goToSlide = function(index) { showSlide(index); resetInterval(); };
        function resetInterval() { clearInterval(slideInterval); slideInterval = setInterval(window.nextSlide, 5000); }
        slideInterval = setInterval(window.nextSlide, 5000);
    }
});

// --- SCROLL HELPERS ---
function geserHorizontal(elementId, jarak) {
    const container = document.getElementById(elementId);
    if (container) container.scrollBy({ left: jarak, behavior: 'smooth' });
}

function disableScroll() { document.body.style.overflow = 'hidden'; }
function enableScroll()  { document.body.style.overflow = ''; }

// --- DOMContentLoaded ---
document.addEventListener("DOMContentLoaded", () => {
    updateCartUI();
    checkWishlistStatus();

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const msg    = urlParams.get('msg');
    if (status && msg) {
        showToast(msg, status);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

window.addEventListener('load', function() {
    if (typeof gameDatabase !== 'undefined') {
        for (const device in gameDatabase) {
            for (const genre in gameDatabase[device]) {
                gameDatabase[device][genre].forEach(game => { const img = new Image(); img.src = game.image; });
            }
        }
    }
});
