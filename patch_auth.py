import os
import re

files = [
    r'd:\Laragon\laragon\www\gamevault\resources\views\auth\register.blade.php',
    r'd:\Laragon\laragon\www\gamevault\resources\views\auth\reset-password.blade.php'
]

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()

    # We add pattern and title to the password and confirm_password inputs
    content = re.sub(
        r'(<input type="password" id="(passwordInput|confirmPasswordInput)" name="(password|confirm_password)" required minlength="8" placeholder="••••••••")',
        r'\1 pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[\\W_]).{8,}" title="Password harus mengandung huruf besar, kecil, angka, dan simbol"',
        content
    )

    with open(file, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f'Updated {file}')
