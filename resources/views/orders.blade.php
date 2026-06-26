<!DOCTYPE html>
<html lang="id">

<head>
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

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Belanja - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

    </style>

    
</head>

<body class="bg-black text-gray-200 font-sans min-h-screen p-0 m-0">

    <nav class="p-6 border-b border-white/10 flex justify-between items-center bg-[#0a0a0a]">
        <a href="/" class="text-2xl font-black text-white font-orbitron tracking-widest transition drop-shadow-[0_0_5px_rgba(255,255,255,0.5)] hover:text-white">
            GAME<span class="text-white font-black">VAULT</span>
        </a>
        <a href="/" class="px-4 py-2 border border-white/20 rounded-full transition font-bold text-sm text-white hover:!bg-white hover:!text-black hover:border-white">
            &larr; Kembali ke Home
        </a>
    </nav>

    <div class="max-w-4xl mx-auto p-6 md:p-10">
        <h1 class="text-3xl md:text-4xl font-black text-white mb-8 border-l-4 border-cyan-500 pl-4 font-orbitron">
            BARANG MILIKKU
        </h1>



        @if($transaksi->count() > 0)
        <div class="space-y-8">
            @foreach($transaksi as $trx)
            <div class="bg-[#111] border border-white/10 rounded-2xl overflow-hidden hover:border-cyan-500/50 transition duration-300">
                
                {{-- HEADER TRANSAKSI --}}
                <div class="bg-[#1a1a1a] p-4 flex flex-wrap justify-between items-center border-b border-white/5 gap-4">
                    <div>
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Order ID</span>
                        <p class="text-white font-bold font-mono"># {{ $trx->id }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Tanggal</span>
                        <p class="text-gray-300 text-sm">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total</span>
                        <p class="text-cyan-400 font-bold">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</p>
                    </div>
                    
                    {{-- TOMBOL DOWNLOAD PDF DAN STATUS --}}
                    <div class="flex items-center gap-3">
                        <a href="/invoice/download/{{ $trx->id }}" target="_blank" class="flex items-center gap-2 px-3 py-1 bg-[#111] text-cyan-400 border border-[#333] hover:border-cyan-400 hover:bg-cyan-900/20 rounded text-xs font-bold uppercase transition-all shadow-sm">
                            📥 Struk PDF
                        </a>
                        <div class="px-3 py-1 bg-green-900/30 text-green-400 border border-green-500/30 rounded text-xs font-bold uppercase">
                            {{ $trx->status }}
                        </div>
                    </div>
                </div>

                {{-- DETAIL ITEM GAME --}}
                <div class="p-4 space-y-4">
                    @foreach($trx->details as $item)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 border-b border-[#222] pb-4 last:border-0 last:pb-0">
                        <div class="w-20 h-20 rounded-lg overflow-hidden border border-white/10 shrink-0">
                            <img src="{{ asset($item->game->image) }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-bold text-lg flex items-center gap-2">
                                {{ $item->game->name }}
                                @if($item->is_refunded)
                                    <span class="px-2 py-0.5 bg-red-500/20 text-red-500 border border-red-500/30 rounded text-[10px] font-black uppercase tracking-wider">Direfund</span>
                                @endif
                            </h4>
                            <p class="text-gray-500 text-sm mt-1 mb-3">
                                Activation Key:
                                <span class="font-mono text-cyan-300 bg-cyan-900/20 px-2 py-0.5 rounded ml-1">
                                    GV-{{ rand(1000, 9999) }}-XXXX
                                </span>
                            </p>
                            


                        </div>
                        <div class="text-right hidden sm:block">
                            <span class="text-gray-400 text-sm">Harga Beli</span><br>
                            @if($item->is_refunded)
                                <span class="text-gray-600 font-medium line-through">Rp {{ number_format($item->harga_saat_beli, 0, ',', '.') }}</span><br>
                                <span class="text-red-500 font-bold text-xs uppercase">Dikembalikan</span>
                            @else
                                <span class="text-white font-medium">Rp {{ number_format($item->harga_saat_beli, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 bg-[#111] rounded-3xl border border-dashed border-white/10">
            <h3 class="text-2xl font-bold text-white mb-2 font-orbitron">Belum Ada Koleksi</h3>
            <p class="text-gray-500 mb-6">Kamu belum membeli game apapun.</p>
            <a href="/" class="px-8 py-3 bg-white text-black font-bold rounded-full transition-all duration-300 transform hover:scale-110">
                Mulai Belanja
            </a>
        </div>
        @endif
    </div>



    <script>

    </script>
    @if(session('msg'))
    {{-- Modal Konfirmasi Sukses/Error --}}
    <div id="sessionModal" class="fixed inset-0 z-[250] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300">
        <div id="sessionModalContent" class="bg-[#111] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform transition-transform duration-300 scale-100">
            @if(session('status') == 'success')
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-green-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2 font-orbitron">Berhasil!</h3>
            @else
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-2 font-orbitron">Perhatian</h3>
            @endif
            <p class="text-gray-400 text-center text-sm mb-6 leading-relaxed">{{ session('msg') }}</p>
            <button type="button" onclick="closeSessionModal()" class="w-full px-4 py-3 {{ session('status') == 'success' ? 'bg-green-500/10 text-green-500 border border-green-500/20 hover:bg-green-500 hover:text-white' : 'bg-red-500/10 text-red-500 border border-red-500/20 hover:bg-red-500 hover:text-white' }} font-bold rounded-xl transition-colors text-sm text-center">OK</button>
        </div>
    </div>
    <script>
        function closeSessionModal() {
            const modal = document.getElementById('sessionModal');
            const content = document.getElementById('sessionModalContent');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
    @endif

    <script>
</script>
@include('components.toast-notification')
</body>
</html>