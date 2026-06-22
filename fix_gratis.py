with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace <100 logic
old_logic = "elseif ($h == '<100') { $q->orWhere('price', '<', 100000); }"
new_logic = "elseif ($h == '<100') { $q->orWhere(function($sub) { $sub->where('price', '>', 0)->where('price', '<', 100000); }); }"

if old_logic in content:
    content = content.replace(old_logic, new_logic)
    with open('routes/web.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print('Updated web.php')
else:
    print('Logic not found in web.php')

with open('resources/views/kategori.blade.php', 'r', encoding='utf-8') as f:
    k_content = f.read()

old_options = """$hargaOptions = [
                      '<100'=> 'Di bawah Rp 100.000',
                          '100-250' => 'Rp 100.000 - Rp 250.000',
                          '250-500' => 'Rp 250.000 - Rp 500.000',
                          '500-750' => 'Rp 500.000 - Rp 750.000',
                          '>750' => 'Di atas Rp 750.000'
                          ];"""

new_options = """$hargaOptions = [
                          'gratis' => 'Gratis',
                          '<100'=> 'Di bawah Rp 100.000',
                          '100-250' => 'Rp 100.000 - Rp 250.000',
                          '250-500' => 'Rp 250.000 - Rp 500.000',
                          '500-750' => 'Rp 500.000 - Rp 750.000',
                          '>750' => 'Di atas Rp 750.000'
                          ];"""

if old_options in k_content:
    k_content = k_content.replace(old_options, new_options)
    with open('resources/views/kategori.blade.php', 'w', encoding='utf-8') as f:
        f.write(k_content)
    print('Updated kategori.blade.php')
else:
    print('Options not found in kategori.blade.php')
