import os
import re

directories = [r'd:\Laragon\laragon\www\gamevault\resources\views']

new_logic = """                    // Kumpulkan label bawaan apapun yang ada di pojok kanan atas
                    let oldLabels = [];
                    card.querySelectorAll('.absolute').forEach(el => {
                        let cls = el.className || '';
                        if (cls.includes('top-') && cls.includes('right-')) {
                            // Abaikan label cart/wishlist/dimiliki
                            if (!el.classList.contains('label-cart') && 
                                !el.classList.contains('label-wishlist') && 
                                !el.innerText.includes('DIMILIKI') &&
                                !el.innerText.includes('KERANJANG') &&
                                !el.innerText.includes('WISHLIST')) {
                                oldLabels.push(el);
                            }
                        }
                    });"""

old_logic_pattern = re.compile(r'// Kumpulkan label bawaan apapun yang ada di pojok kanan atas\s*let oldLabels = \[\];\s*card\.querySelectorAll\(\'\.absolute\.top-2\.right-2\'\)\.forEach\(el => \{\s*// Abaikan label cart/wishlist/dimiliki\s*if \(!el\.classList\.contains\(\'label-cart\'\) && \s*!el\.classList\.contains\(\'label-wishlist\'\) && \s*!el\.innerText\.includes\(\'DIMILIKI\'\) &&\s*!el\.innerText\.includes\(\'KERANJANG\'\) &&\s*!el\.innerText\.includes\(\'WISHLIST\'\)\) \{\s*oldLabels\.push\(el\);\s*\}\s*\}\);', re.DOTALL)

for d in directories:
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.blade.php'):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                new_content, count = old_logic_pattern.subn(new_logic, content)
                if count > 0:
                    with open(path, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f'Patched {f}')
