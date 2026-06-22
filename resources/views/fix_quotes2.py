import glob, os

for f in glob.glob('resources/views/*.blade.php'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    content = content.replace(r"\'pageshow\'", "'pageshow'")
    
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f'Fixed {f}')
