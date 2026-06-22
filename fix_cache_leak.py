import os, glob

script_to_inject = """
    <script>
        (function() {
            let currentUserId = "{{ Auth::check() ? Auth::id() : 'null' }}";
            if (localStorage.getItem('lastUserId') !== currentUserId) {
                localStorage.removeItem('cartCount');
                localStorage.removeItem('wishlist');
                localStorage.setItem('lastUserId', currentUserId);
            }
        })();
    </script>
"""

for root, dirs, files in os.walk('resources/views'):
    for f in files:
        if f.endswith('.blade.php'):
            filepath = os.path.join(root, f)
            with open(filepath, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Check if already injected
            if 'localStorage.getItem(\'lastUserId\')' not in content:
                # Replace the first <head> with <head> + script_to_inject
                if '<head>' in content:
                    content = content.replace('<head>', '<head>' + script_to_inject, 1)
                    with open(filepath, 'w', encoding='utf-8') as file:
                        file.write(content)
                    print(f'Patched {filepath}')
