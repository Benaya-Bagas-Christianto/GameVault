import re

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'r', encoding='utf-8') as f:
    c = f.read()

# Add snap.js to head
if "snap.js" not in c:
    c = c.replace('</head>', '<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env(\'MIDTRANS_CLIENT_KEY\') }}"></script>\n</head>')

# Add the "Selesaikan Pembayaran" button
btn_cancel = """<button type="button" onclick="openCancelModal('{{ $t->id }}')" class="w-full px-4 py-2 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-lg transition-colors">
                                                    Batalkan
                                                </button>"""

btn_complete = """<button type="button" onclick="openCancelModal('{{ $t->id }}')" class="w-full px-4 py-2 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-lg transition-colors">
                                                    Batalkan
                                                </button>
                                                @if($t->snap_token)
                                                <button type="button" onclick="window.snap.pay('{{ $t->snap_token }}')" class="w-full mt-2 px-4 py-2 bg-[#7C3AED]/20 border border-[#7C3AED]/50 hover:bg-[#7C3AED] hover:text-white text-[#7C3AED] text-xs font-bold rounded-lg transition-colors">
                                                    Selesaikan Pembayaran
                                                </button>
                                                @endif"""

c = c.replace(btn_cancel, btn_complete)

with open(r'd:\Laragon\laragon\www\gamevault\resources\views\riwayat.blade.php', 'w', encoding='utf-8') as f:
    f.write(c)

print("riwayat.blade.php updated")
