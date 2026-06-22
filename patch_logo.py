import os, glob

target_dir = 'resources/views'
blade_files = glob.glob(target_dir + '/**/*.blade.php', recursive=True)

old_str_1 = 'alt="GameVault Logo" class="h-6 sm:h-7 lg:h-8 w-auto group-hover:opacity-80 transition-opacity"'
new_str_1 = 'alt="GameVault Logo" class="h-6 sm:h-7 lg:h-8 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)] group-hover:drop-shadow-[0_0_25px_rgba(124,58,237,1)] transition-all duration-300"'

old_str_2 = 'alt="GameVault Logo" class="h-8 w-auto hover:opacity-80 transition-opacity"'
new_str_2 = 'alt="GameVault Logo" class="h-8 w-auto drop-shadow-[0_0_15px_rgba(124,58,237,0.8)] hover:drop-shadow-[0_0_25px_rgba(124,58,237,1)] transition-all duration-300"'

count = 0
for file in blade_files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if old_str_1 in content or old_str_2 in content:
        content = content.replace(old_str_1, new_str_1)
        content = content.replace(old_str_2, new_str_2)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        count += 1

print(f'Patched {count} files.')
