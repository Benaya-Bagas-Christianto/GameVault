import re

with open('resources/views/admin/users_index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the inner div of the accordion to have transition classes
old_div = r'<div class="grid grid-cols-1 md:grid-cols-3 gap-6">'
new_div = r'<div id="content-{{ $u->id }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 transition-all duration-300 ease-in-out opacity-0 max-h-0 overflow-hidden transform -translate-y-4">'
content = content.replace(old_div, new_div)

# Replace the JS
old_js = '''<script>
        function toggleDetails(id) {
            const el = document.getElementById('details-' + id);
            if (el.classList.contains('hidden')) {
                // close others
                document.querySelectorAll('tr[id^="details-"]').forEach(tr => {
                    tr.classList.add('hidden');
                });
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
    </script>'''

new_js = '''<script>
        function closeAllDetails() {
            document.querySelectorAll('div[id^="content-"]').forEach(contentDiv => {
                contentDiv.classList.remove('opacity-100', 'max-h-[1000px]', 'translate-y-0');
                contentDiv.classList.add('opacity-0', 'max-h-0', '-translate-y-4');
            });
            setTimeout(() => {
                document.querySelectorAll('tr[id^="details-"]').forEach(tr => {
                    tr.classList.add('hidden');
                });
            }, 300); // Wait for transition
        }

        function toggleDetails(id) {
            const tr = document.getElementById('details-' + id);
            const contentDiv = document.getElementById('content-' + id);
            
            if (tr.classList.contains('hidden')) {
                // Close others first
                document.querySelectorAll('div[id^="content-"]').forEach(c => {
                    if(c.id !== 'content-' + id) {
                        c.classList.remove('opacity-100', 'max-h-[1000px]', 'translate-y-0');
                        c.classList.add('opacity-0', 'max-h-0', '-translate-y-4');
                    }
                });
                
                // Unhide TR immediately
                document.querySelectorAll('tr[id^="details-"]').forEach(otherTr => {
                    if (otherTr.id !== 'details-' + id) {
                        setTimeout(() => otherTr.classList.add('hidden'), 300);
                    }
                });

                tr.classList.remove('hidden');
                
                // Allow browser to render TR first, then trigger CSS transition
                setTimeout(() => {
                    contentDiv.classList.remove('opacity-0', 'max-h-0', '-translate-y-4');
                    contentDiv.classList.add('opacity-100', 'max-h-[1000px]', 'translate-y-0');
                }, 10);
            } else {
                // Hide current
                contentDiv.classList.remove('opacity-100', 'max-h-[1000px]', 'translate-y-0');
                contentDiv.classList.add('opacity-0', 'max-h-0', '-translate-y-4');
                setTimeout(() => {
                    tr.classList.add('hidden');
                }, 300);
            }
        }
    </script>'''

content = content.replace(old_js, new_js)

# Also padding on the <td> might cause jitter if it's there when hidden.
# Let's transition the padding too, or remove the py-6 from the <td> and put it inside the div.
# Currently: <td colspan="8" class="px-6 py-6 border-t border-white/5">
# Let's change it to: <td colspan="8" class="px-6 border-t border-white/5">
# And put py-6 inside the content div: <div id="content-{{ $u->id }}" class="py-6 grid ...
old_td = '<td colspan="8" class="px-6 py-6 border-t border-white/5">'
new_td = '<td colspan="8" class="px-6 border-t border-white/5">'
content = content.replace(old_td, new_td)

old_new_div_with_py = r'<div id="content-{{ $u->id }}" class="py-6 grid grid-cols-1 md:grid-cols-3 gap-6 transition-all duration-300 ease-in-out opacity-0 max-h-0 overflow-hidden transform -translate-y-4">'
content = content.replace(new_div, old_new_div_with_py)

with open('resources/views/admin/users_index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Added animation to users_index accordion')
