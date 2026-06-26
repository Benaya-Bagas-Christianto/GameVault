<form action="/search" method="GET" class="w-full max-w-sm relative hidden md:block group" id="live-search-form">
    <input type="text" name="q" id="live-search-input" value="{{ request('q') }}" placeholder="Cari game favorit kamu..." autocomplete="off"
        class="w-full bg-[#12151C] border border-white/10 text-sm font-medium text-white px-4 py-2.5 rounded-lg focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/30 transition-all placeholder-gray-500 pl-10 cursor-text">
    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 transition-colors pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>

    <!-- Dropdown -->
    <div id="live-search-dropdown" class="absolute top-full left-0 w-full mt-2 bg-[#1A1D24] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[100] hidden flex-col">
        <div id="live-search-results" class="max-h-80 overflow-y-auto custom-scrollbar">
            <!-- Results will be injected here -->
        </div>
        <!-- Search Action -->
        <button type="submit" class="w-full text-left px-4 py-3 bg-white/5 hover:bg-white/10 border-t border-white/10 text-sm font-medium text-gray-400 hover:text-white transition-colors flex items-center gap-2">
            Advanced Search
            <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('live-search-input');
    const searchDropdown = document.getElementById('live-search-dropdown');
    const searchResults = document.getElementById('live-search-results');
    
    // Only init if the elements exist on this page
    if(!searchInput || !searchDropdown || !searchResults) return;

    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            searchDropdown.classList.add('hidden');
            searchDropdown.classList.remove('flex');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/search/autocomplete?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(game => {
                            const priceText = game.price == 0 ? 'Gratis' : 'Rp ' + parseInt(game.price).toLocaleString('id-ID');
                            const imagePath = game.image ? `/assets/${game.image}` : '/assets/no-image.jpg';
                            
                            const item = document.createElement('a');
                            item.href = `/game/${game.id}`;
                            item.className = 'flex items-center gap-3 p-3 hover:bg-white/10 border-b border-white/5 transition-colors cursor-pointer';
                            
                            item.innerHTML = `
                                <img src="${imagePath}" class="w-16 h-9 object-cover rounded shadow-md" onerror="this.src='/assets/no-image.jpg'" alt="${game.name}">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-white text-sm font-medium truncate">${game.name}</h4>
                                    <p class="text-gray-400 font-medium text-xs mt-0.5">${priceText}</p>
                                </div>
                            `;
                            searchResults.appendChild(item);
                        });
                        searchDropdown.classList.remove('hidden');
                        searchDropdown.classList.add('flex');
                    } else {
                        searchResults.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">Tidak ada game yang cocok.</div>';
                        searchDropdown.classList.remove('hidden');
                        searchDropdown.classList.add('flex');
                    }
                })
                .catch(err => console.error(err));
        }, 300);
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.add('hidden');
            searchDropdown.classList.remove('flex');
        }
    });

    // Show when focused if there is text
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && searchResults.innerHTML !== '') {
            searchDropdown.classList.remove('hidden');
            searchDropdown.classList.add('flex');
        }
    });
});
</script>

<style>
/* Custom scrollbar for the dropdown */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.1);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.2);
}
</style>
