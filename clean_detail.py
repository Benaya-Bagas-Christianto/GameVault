import re

with open('resources/views/detail.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Try regex to remove the small footer
content = re.sub(r'<footer[^>]*>.*?</footer>', '', content, flags=re.DOTALL)

# Remove any existing include footer
content = content.replace("@include('footer')", "")
content = content.replace("        \n", "")

# Insert just before </main>
main_idx = content.rfind('</main>')
if main_idx != -1:
    new_content = content[:main_idx] + '        @include(\'footer\')\n' + content[main_idx:]
    with open('resources/views/detail.blade.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
    print('Cleaned up detail')
