with open('resources/views/kategori.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Update genre-box active style
old_style = '''        .genre-box.active {
            border-color: #7C3AED;
            background-color: rgba(124, 58, 237, 0.1);
        }'''
new_style = '''        .genre-box.active {
            border-color: #7C3AED;
            background-color: rgba(124, 58, 237, 0.2);
            box-shadow: 0 0 15px rgba(124, 58, 237, 0.5);
            transform: translateY(-2px);
        }'''
if old_style in content:
    content = content.replace(old_style, new_style)

# Update onclick on genre boxes
old_onclick = '''<div onclick="window.location.href='/kategori?genre={{ $gb['name'] }}'"'''
new_onclick = '''<div onclick="toggleGenreBox('{{ $gb['name'] }}')"'''
content = content.replace(old_onclick, new_onclick)

# Update the JS fetchAndUpdate to also update active classes on genre boxes
js_inject = '''        function toggleSemuaGenre(checkbox) {'''

new_js = '''        function toggleGenreBox(genreName) {
            const cb = document.querySelector(`input[name="genre"][value="${genreName}"]`);
            if (cb) {
                cb.checked = !cb.checked;
                if (cb.checked) {
                    const semuaGenre = document.getElementById('semuaGenre');
                    if (semuaGenre) semuaGenre.checked = false;
                }
                applyFilters();
            } else {
                window.location.href = `/kategori?genre=${genreName}`;
            }
        }

        function syncGenreBoxActiveStates() {
            const checkedGenres = Array.from(document.querySelectorAll('input[name="genre"]:checked')).map(cb => cb.value);
            document.querySelectorAll('.genre-box').forEach(box => {
                const onclickStr = box.getAttribute('onclick');
                if (onclickStr) {
                    const match = onclickStr.match(/toggleGenreBox\\('([^']+)'\\)/);
                    if (match && match[1]) {
                        if (checkedGenres.includes(match[1])) {
                            box.classList.add('active');
                        } else {
                            box.classList.remove('active');
                        }
                    }
                }
            });
        }

        function toggleSemuaGenre(checkbox) {'''

if js_inject in content:
    content = content.replace(js_inject, new_js)

# Add syncGenreBoxActiveStates() inside applyFilters()
old_apply = '''            if (platforms.length > 0) params.set('platform', platforms.join(','));
            params.set('filter', '1');

            fetchAndUpdate('/kategori?' + params.toString());'''

new_apply = '''            if (platforms.length > 0) params.set('platform', platforms.join(','));
            params.set('filter', '1');

            syncGenreBoxActiveStates();
            fetchAndUpdate('/kategori?' + params.toString());'''

if old_apply in content:
    content = content.replace(old_apply, new_apply)

with open('resources/views/kategori.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Script execution done')
