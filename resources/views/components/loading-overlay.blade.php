{{-- Loading Overlay --}}
<div id="loading-overlay" class="fixed inset-0 z-[100] bg-black/80 backdrop-blur-sm hidden flex-col items-center justify-center">
    <div class="relative w-24 h-24 mb-4">
        <div class="absolute inset-0 border-4 border-white/20 rounded-full"></div>
        <div class="absolute inset-0 border-4 border-[#7C3AED] rounded-full border-t-transparent animate-spin"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-8 h-8 text-[#7C3AED]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
        </div>
    </div>
    <h3 class="text-white font-bold text-xl mb-2 tracking-wider">MENGUNGGAH...</h3>
    <p class="text-gray-400 text-sm text-center max-w-xs">Mohon tunggu sebentar, file sedang diproses...</p>
</div>

<script>
    if (typeof window.showLoadingOverlay === 'undefined') {
        window.showLoadingOverlay = function() {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }
        }
    }
</script>
