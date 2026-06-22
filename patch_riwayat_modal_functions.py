import re

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\cart.blade.php', 'r', encoding='utf-8') as f:
    c = f.read()

# Extract functions
functions = re.search(r'(// Fungsi untuk menampilkan Custom Payment Modal.*?function closePaymentModal.*?},\s*300\);\s*})', c, re.DOTALL)
if not functions:
    print("Could not find functions in cart.blade.php")
else:
    func_code = functions.group(1)
    
    with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'r', encoding='utf-8') as f:
        r = f.read()
    
    if 'function showPaymentModal' not in r:
        # We need to inject func_code inside the script tag that we added previously
        # Let's find the closing </script> right before </body>
        r = r.replace('</script>\n</body>', '\n' + func_code + '\n</script>\n</body>')
        
        with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'w', encoding='utf-8') as f:
            f.write(r)
        print("Successfully injected showPaymentModal and closePaymentModal into riwayat.blade.php")
    else:
        print("Functions already exist")
