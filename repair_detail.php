<?php
$file = __DIR__ . '/resources/views/detail.blade.php';
$lines = file($file);

$insert = <<<EOD
                <div class="border-b border-white/10 mb-8 flex gap-8 overflow-x-auto hide-scrollbar">
                    <button id="tabInformasi" onclick="switchTab('informasi')" class="pb-4 font-bold whitespace-nowrap px-2 border-b-2" style="color: #8B5CF6 !important; border-color: #8B5CF6 !important;">Informasi</button>
                    <button id="tabUlasan" onclick="switchTab('ulasan')" class="text-gray-400 hover:text-white pb-4 font-medium whitespace-nowrap px-2 border-b-2 border-transparent transition-colors">Ulasan ({{ \$total_reviews }})</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                    <div class="lg:col-span-2">
                        <div id="contentInformasi" class="space-y-8 block">
                            <div class="border border-white/5 p-8 rounded-2xl" style="background-color: #12151C !important;">
                                <h2 class="text-xl font-bold !text-white mb-6">Tentang Game</h2>
                                <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">
                                    {{ \$game->description ?? 'Tidak ada deskripsi rinci untuk game ini.' }}
                                </div>
                            </div>

                            @php
                                \$videoTrailer = \$game->galleries ? \$game->galleries->where('type', 'video')->first() : null;
                            @endphp
                            @if(\$videoTrailer)
                                @php
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', \$videoTrailer->path, \$match);
                                    \$youtubeId = \$match[1] ?? '';
                                    \$isLocal = !\$youtubeId && !str_contains(\$videoTrailer->path, 'youtube.com') && !str_contains(\$videoTrailer->path, 'youtu.be');
                                @endphp
                                @if(\$youtubeId || \$isLocal)
                                <div class="border border-white/5 p-8 rounded-2xl" style="background-color: #12151C !important;">
                                    <h2 class="text-xl font-bold !text-white mb-6">Video Trailer</h2>
                                    <div class="relative w-full rounded-xl overflow-hidden bg-black" style="padding-top: 56.25%;">
                                        @if(\$youtubeId)
                                        <iframe class="absolute top-0 left-0 w-full h-full" src="https://www.youtube.com/embed/{{ \$youtubeId }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        @else
                                        <video src="{{ asset('assets/galleries/' . \$videoTrailer->path) }}" controls class="absolute top-0 left-0 w-full h-full object-contain"></video>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>

                        <div id="contentUlasan" class="space-y-8 hidden">
                            <div class="border border-white/5 p-8 rounded-2xl" style="background-color: #12151C !important;">
                            <h2 class="text-xl font-bold !text-white mb-6">Ulasan Pengguna</h2>
                            <div class="flex flex-col md:flex-row gap-8 mb-8 border-b border-white/5 pb-8">
                                <div class="flex flex-col justify-center items-center md:items-start text-center md:text-left min-w-[120px]">
                                    <h3 class="text-6xl font-black !text-white mb-2">{{ \$avg_rating }}</h3>
                                    <div class="flex text-lg mb-2">
                                        @for(\$i=1; \$i<=5; \$i++)
                                            @if(\$i <= round(\$avg_rating)) <span class="text-yellow-500">★</span>
                                            @else <span class="text-gray-600">★</span> @endif
                                        @endfor
                                    </div>
                                    <p class="text-xs text-gray-500">Berdasarkan {{ \$review_k }} ulasan</p>
                                </div>
                                <div class="flex-1 space-y-2.5">
                                    <div class="flex items-center gap-3"><span class="text-xs text-gray-400 w-4">5★</span><div class="flex-1 progress-bar-bg"><div class="progress-bar-fill" style="width: {{ \$b5 }}%"></div></div><span class="text-xs text-gray-500 w-8 text-right">{{ \$b5 }}%</span></div>
EOD;

// We need to inject this at line index 249 (which is line 250 in 1-based indexing).
// But wait, the lines after line 248 are:
// 249: 
// 250: <div class="flex items-center gap-3"><span class="text-xs text-gray-400 w-4">4★...
array_splice($lines, 249, 0, $insert . "\n");

file_put_contents($file, implode("", $lines));
echo "Repaired detail.blade.php\n";
