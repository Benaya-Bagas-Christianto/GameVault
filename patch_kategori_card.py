import re

with open('resources/views/kategori.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add CSS
css_to_add = '''
        .card-bg {
            background-color: #12151C;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            border-color: rgba(124, 58, 237, 0.5);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.15);
            transform: translateY(-2px);
        }
    </style>
'''
content = content.replace('    </style>', css_to_add)

# 2. Replace game card
old_card_regex = r'<div class="game-poster-card.*?</div>\s*</div>'

new_card = '''                            <?php
                            $avg_rating = $game->reviews->count() > 0 ? round($game->reviews->avg('rating'), 1) : 0;
                            $total_reviews = $game->reviews->count();
                            ?>
                            <div class="card-bg rounded-xl overflow-hidden hover-card cursor-pointer flex flex-col relative group" onclick="window.location.href='/game/{{ $game->id }}'" data-game-id="{{ $game->id }}" data-title="{{ strtolower($game->name) }}">

                                @if($avg_rating >= 4.5)
                                <div class="absolute top-2 right-2 z-10 bg-yellow-500 text-black text-[9px] font-black px-1.5 py-0.5 rounded">TOP</div>
                                @elseif($game->price == 0)
                                <div class="absolute top-2 right-2 z-10 bg-green-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded">FREE</div>
                                @elseif($game->created_at && $game->created_at->diffInDays(now()) < 30)
                                    <div class="absolute top-2 right-2 z-10 bg-blue-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded">Baru</div>
                                @endif
                                
                            <div class="relative aspect-[3/4] overflow-hidden bg-black">
                                <?php if ($isOwned): ?>
                                    <div class="absolute top-2 right-2 bg-gray-800 text-white text-[9px] font-bold px-2 py-1 rounded shadow-lg border border-gray-600/50 backdrop-blur-sm z-20">SUDAH DIMILIKI</div>
                                <?php elseif ($isInCart): ?>
                                    <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(34,197,94,0.3)] border border-green-400 z-20">DI KERANJANG</div>
                                <?php elseif ($isInWishlist): ?>
                                    <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-2 py-1 rounded shadow-[0_0_10px_rgba(236,72,153,0.3)] border border-pink-400 z-20">WISHLIST</div>
                                <?php endif; ?>
                                <img src="{{ asset('assets/' . $game->image) }}" onerror="this.src='/assets/no-image.jpg'" class="{{ $isOwned ? 'opacity-40 grayscale-[60%]' : '' }} w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">

                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                    <button onclick="event.stopPropagation(); window.tambahKeranjangCerdas('{{ $game->id }}', false, this)" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-colors">
                                        + Keranjang
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-bold text-white text-sm leading-tight mb-1 line-clamp-2 group-hover:text-purple-400 transition-colors">{{ $game->name }}</h3>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded">{{ explode(',', $game->genre)[0] }}</span>
                                    <span class="text-[10px] text-gray-500 bg-white/5 px-1.5 py-0.5 rounded">{{ explode(',', $game->platform)[0] }}</span>
                                </div>
                                @if($avg_rating > 0)
                                <div class="flex items-center gap-1 mb-2">
                                    <span class="text-yellow-500 text-xs">★</span>
                                    <span class="text-xs font-bold text-white">{{ $avg_rating }}</span>
                                    <span class="text-[10px] text-gray-500">({{ $total_reviews }})</span>
                                </div>
                                @endif
                                <p class="mt-auto font-bold text-sm {{ $game->price == 0 ? 'text-green-400' : 'text-white' }}">{{ $game->price == 0 ? "Gratis" : "Rp " . number_format($game->price, 0, ',', '.') }}</p>
                            </div>
                    </div>'''

content = re.sub(old_card_regex, new_card, content, flags=re.DOTALL)

with open('resources/views/kategori.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Patch applied successfully')
