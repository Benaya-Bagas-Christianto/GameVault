import glob, os, re

for f in glob.glob('resources/views/*.blade.php'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Fix syncCartBadge
    content = re.sub(
        r'window\.syncCartBadge = function\(e\) \{\s*if \(e\.persisted\) \{',
        r'window.syncCartBadge = function(e) {\n                            if (e && e.type === \'pageshow\' && !e.persisted) return;\n                            {',
        content
    )
    
    # Fix syncWishlistBadge
    content = re.sub(
        r'window\.syncWishlistBadge = function\(e\) \{\s*if \(e\.persisted\) \{',
        r'window.syncWishlistBadge = function(e) {\n                        if (e && e.type === \'pageshow\' && !e.persisted) return;\n                        {',
        content
    )

    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f'Fixed {f}')
