files = ['resources/views/kategori.blade.php', 'resources/views/detail.blade.php']

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # We replace @include('footer') with a wrapped version
    wrapped_footer = '''        <div class="px-2 lg:px-6">
            @include('footer')
        </div>'''
    
    if "        @include('footer')" in content:
        content = content.replace("        @include('footer')", wrapped_footer)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print('Wrapped in', file)
    else:
        print('Not found in', file)
