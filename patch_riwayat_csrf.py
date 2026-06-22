import re

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'r', encoding='utf-8') as f:
    c = f.read()

# Fix JS inside resumePayment
new_js = """function resumePayment(snapToken, orderId) {
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    // Tampilkan modal secara instan
                    showPaymentModal('success', 'Hore! Pembayaran berhasil!', window.location.href);
                    
                    // Tembak server lokal secara asinkron
                    fetch('/checkout/success', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    });
                },
                onPending: function(result) {
                    showPaymentModal('warning', 'Menunggu pembayaran diselesaikan!', window.location.href);
                },
                onError: function(result) {
                    showPaymentModal('error', 'Maaf, pembayaran gagal!', null);
                },
                onClose: function() {
                    // do nothing
                }
            });
        }"""

# Replace the old resumePayment block
c = re.sub(r'function resumePayment\(snapToken, orderId\).*?onClose: function\(\) \{\s*// do nothing when closed from riwayat\s*\}\s*}\);\s*}', new_js, c, flags=re.DOTALL)

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'w', encoding='utf-8') as f:
    f.write(c)

print("riwayat.blade.php updated with correct csrf_token")
