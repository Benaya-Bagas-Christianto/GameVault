{{-- Modal Konfirmasi Sukses/Error Global --}}
<div id="successModal" class="fixed inset-0 z-[500] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div id="successModalContent" class="bg-[#12151C] border border-white/10 p-6 rounded-3xl w-[90%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300">
        <div id="toastIconContainer" class="flex items-center justify-center w-16 h-16 mx-auto bg-green-500/20 rounded-full mb-4">
            <svg id="toastIconSuccess" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg id="toastIconError" class="w-8 h-8 text-red-500 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h3 id="toastTitle" class="text-xl font-black text-center text-white mb-2">Berhasil!</h3>
        <p id="successModalText" class="text-gray-400 text-center text-sm mb-6 leading-relaxed">Berhasil</p>
        <button type="button" onclick="closeSuccessModal()" id="toastBtn" class="w-full px-4 py-3 bg-green-500/10 text-green-500 border border-green-500/20 font-bold rounded-xl hover:bg-green-500 hover:text-white transition-colors text-sm text-center">OK</button>
    </div>
</div>

<script>
    window.toastQueue = [];
    window.isToastShowing = false;
    window.toastCallback = null;

    // FUNGSI MENGATUR MODAL SUKSES/ERROR GLOBAL DENGAN SISTEM ANTREAN
    window.showToast = function(message, isError = false, callback = null) {
        window.toastQueue.push({ message, isError, callback });
        if (!window.isToastShowing) {
            window.processToastQueue();
        }
    };

    window.processToastQueue = function() {
        if (window.toastQueue.length === 0) {
            window.isToastShowing = false;
            return;
        }
        
        window.isToastShowing = true;
        const currentToast = window.toastQueue.shift();
        window.toastCallback = currentToast.callback;

        const modal = document.getElementById('successModal');
        const content = document.getElementById('successModalContent');
        if (!modal) return;
        
        document.getElementById('successModalText').innerText = currentToast.message;
        
        // Handle styling based on success/error
        const iconContainer = document.getElementById('toastIconContainer');
        const iconSuccess = document.getElementById('toastIconSuccess');
        const iconError = document.getElementById('toastIconError');
        const title = document.getElementById('toastTitle');
        const btn = document.getElementById('toastBtn');
        
        if (currentToast.isError) {
            iconContainer.className = 'flex items-center justify-center w-16 h-16 mx-auto bg-red-500/20 rounded-full mb-4';
            iconSuccess.classList.add('hidden');
            iconError.classList.remove('hidden');
            title.innerText = 'Gagal!';
            title.className = 'text-xl font-black text-center text-red-500 mb-2';
            btn.className = 'w-full px-4 py-3 bg-red-500/10 text-red-500 border border-red-500/20 font-bold rounded-xl hover:bg-red-500 hover:text-white transition-colors text-sm text-center';
        } else {
            iconContainer.className = 'flex items-center justify-center w-16 h-16 mx-auto bg-green-500/20 rounded-full mb-4';
            iconError.classList.add('hidden');
            iconSuccess.classList.remove('hidden');
            title.innerText = 'Berhasil!';
            title.className = 'text-xl font-black text-center text-white mb-2';
            btn.className = 'w-full px-4 py-3 bg-green-500/10 text-green-500 border border-green-500/20 font-bold rounded-xl hover:bg-green-500 hover:text-white transition-colors text-sm text-center';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // Sedikit delay agar transisi CSS berjalan
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    };

    window.closeSuccessModal = function() {
        const modal = document.getElementById('successModal');
        const content = document.getElementById('successModalContent');
        if (!modal) return;
        
        // Animasi keluar
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            // Panggil callback setelah animasi selesai
            if (typeof window.toastCallback === 'function') {
                window.toastCallback();
                window.toastCallback = null;
            }

            // Cek apakah ada antrean lagi
            setTimeout(() => {
                window.processToastQueue();
            }, 300);
            
        }, 300);
    };
</script>

@if(session('msg'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            if(typeof window.showToast === 'function') {
                window.showToast("{{ session('msg') }}", {{ session('status') == 'error' ? 'true' : 'false' }});
            }
        }, 300);
    });
</script>
@endif
