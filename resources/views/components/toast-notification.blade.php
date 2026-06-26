{{-- Admin Only Pending Refunds Toast Notification --}}
@php
    $showAdminToast = false;
    if (isset($pendingRefundsCount) && $pendingRefundsCount > 0 && \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
        $showAdminToast = true;
    }
@endphp

@if($showAdminToast)
<div id="slideToast" class="fixed top-24 right-0 transform translate-x-full z-[1000] transition-transform duration-500 ease-out flex items-center pr-6">
    <div class="bg-[#12151C] border border-[#7C3AED]/30 rounded-2xl shadow-[0_10px_40px_rgba(124,58,237,0.2)] p-4 flex items-start gap-4 min-w-[300px] max-w-sm relative overflow-hidden">
        {{-- Progress Bar --}}
        <div id="toastProgressBar" class="absolute bottom-0 left-0 h-1 w-full bg-[#7C3AED]"></div>
        
        {{-- Icon --}}
        <div class="flex-shrink-0 mt-0.5">
            <div class="w-10 h-10 rounded-full bg-[#7C3AED]/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-[#a78bfa]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
        </div>
        
        {{-- Content --}}
        <div class="flex-1">
            <h4 class="text-white font-bold text-sm mb-1">Pemberitahuan Admin</h4>
            <p class="text-gray-400 text-xs leading-relaxed">Ada <strong class="text-[#a78bfa]">{{ $pendingRefundsCount }} permintaan refund</strong> baru yang menunggu untuk diproses.</p>
        </div>
        
        {{-- Close Button --}}
        <button onclick="hideSlideToast()" class="text-gray-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('slideToast');
        const progressBar = document.getElementById('toastProgressBar');
        if (toast) {
            const currentCount = {{ $pendingRefundsCount }};
            let lastSeenCount = parseInt(localStorage.getItem('lastSeenPendingRefundsCount') || '0');
            const isLogin = {{ session('msg') == 'Selamat datang!' ? 'true' : 'false' }};
            const isRefundRequested = {{ session('msg') == 'Pengajuan refund berhasil dikirim dan sedang diproses oleh admin.' ? 'true' : 'false' }};

            // Jika baru saja login atau admin baru saja mengajukan refund, paksa notifikasi muncul (jika ada pending) dengan mereset lastSeen
            if (isLogin || isRefundRequested) {
                lastSeenCount = -1;
            }

            if (currentCount > lastSeenCount) {
                // Ada refund baru, atau baru login dan ada pending, tampilkan toast!
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 500);

                // Animate progress bar
                progressBar.style.transition = 'width 6s linear';
                setTimeout(() => {
                    progressBar.style.width = '0%';
                }, 600);

                // Auto hide after 6 seconds
                setTimeout(() => {
                    hideSlideToast();
                }, 6000);
            } else {
                // Tidak ada refund baru, sembunyikan secara instan
                toast.style.display = 'none';
            }

            // Selalu update localStorage dengan count terbaru agar sinkron
            localStorage.setItem('lastSeenPendingRefundsCount', currentCount);
        }
    });

    window.hideSlideToast = function() {
        const toast = document.getElementById('slideToast');
        if (toast) {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.style.display = 'none';
            }, 500);
        }
    }
</script>
@endif
