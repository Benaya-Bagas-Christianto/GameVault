import re

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'r', encoding='utf-8') as f:
    c = f.read()

# Replace onclick
old_btn = "onclick=\"window.snap.pay('{{ $t->snap_token }}')\""
new_btn = "onclick=\"resumePayment('{{ $t->snap_token }}', '{{ $t->id }}')\""
c = c.replace(old_btn, new_btn)

# Add the JS function at the end before </body>
js_script = """
    <script>
        function resumePayment(snapToken, orderId) {
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
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
                    });
                },
                onPending: function(result) {
                    window.location.reload();
                },
                onError: function(result) {
                    alert('Maaf, pembayaran gagal!');
                },
                onClose: function() {
                    // do nothing when closed from riwayat
                }
            });
        }
    </script>
</body>
"""
c = c.replace("</body>", js_script)

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'w', encoding='utf-8') as f:
    f.write(c)

print("riwayat.blade.php updated with callbacks")
