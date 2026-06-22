import re

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\cart.blade.php', 'r', encoding='utf-8') as f:
    cart = f.read()

# Extract modal HTML + JS from cart.blade.php
match = re.search(r'(<div id="paymentModal".*?</script>)', cart, re.DOTALL)
if match:
    modal_code = match.group(1)
    
    with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'r', encoding='utf-8') as f:
        riwayat = f.read()
    
    if 'id="paymentModal"' not in riwayat:
        # We need to change window.location.reload() inside resumePayment to showPaymentModal
        # Wait, the modal code has the showPaymentModal function!
        riwayat = riwayat.replace('</body>', '\n' + modal_code + '\n</body>')
        
        # Now update resumePayment to use showPaymentModal
        riwayat = riwayat.replace("""onSuccess: function(result) {
                    // Tembak server lokal secara asinkron di belakang layar
                    fetch('/checkout/success', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    }).then(() => {
                        window.location.reload();
                    });""", """onSuccess: function(result) {
                    // Tembak server lokal secara asinkron di belakang layar
                    fetch('/checkout/success', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    }).then(() => {
                        showPaymentModal('success', 'Hore! Pembayaran berhasil!', window.location.href);
                    });""")
        
        riwayat = riwayat.replace("""onPending: function(result) {
                    window.location.reload();
                },
                onError: function(result) {
                    alert('Maaf, pembayaran gagal!');
                },""", """onPending: function(result) {
                    showPaymentModal('warning', 'Menunggu pembayaran diselesaikan!', window.location.href);
                },
                onError: function(result) {
                    showPaymentModal('error', 'Maaf, pembayaran gagal!', null);
                },""")
        
        with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'w', encoding='utf-8') as f:
            f.write(riwayat)
        print("Successfully copied modal to riwayat")
    else:
        print("paymentModal already exists")
else:
    print("Could not find paymentModal in cart.blade.php")
