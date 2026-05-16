<header class="h-20 border-b border-[#3A4566] bg-[#242D45] px-6 md:px-8 flex items-center justify-between">
    <!-- Left -->
    <div>
        <h1 class="text-white text-2xl font-bold leading-tight">{{$pageTitle ?? 'Dashboard'}}</h1>
        <p class="text-sm text-[#A0A8C0] mt-1">Welcome back, Administrator!</p>
    </div>

    <!-- Right -->
    <div class="flex items-center gap-4">
        <!-- Search -->
        <div class="hidden md:flex items-center w-72 rounded-2xl border border-[#3A4566] bg-[#2A3352] px-4 py-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#A0A8C0] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                placeholder="Global search..."
                class="w-full bg-transparent outline-none text-sm text-white placeholder:text-[#7F89A8]"
            >
        </div>

        <!-- Notification -->
        <div class="relative">
            <button 
            onclick="toggleNotificationPanel()"
            class="relative w-11 h-11 rounded-full border border-[#3A4566] bg-[#2A3352] flex items-center justify-center text-[#A0A8C0] hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                </svg>
    
                <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-[#FF4D4D] text-white text-[10px] font-semibold flex items-center justify-center">
                    3
                </span>
            </button>
            @include('components.notification-panel')
        </div>
        <!-- Profile -->
        <button class="flex items-center gap-3 pl-2">
            <div class="w-11 h-11 rounded-full bg-[#0057B8] flex items-center justify-center text-white font-bold">
                A
            </div>

            <div class="hidden md:block text-left">
                <p class="text-white text-sm font-semibold leading-tight">Administrator</p>
                <p class="text-xs text-[#A0A8C0] mt-0.5">Admin</p>
            </div>

            <svg xmlns="http://www.w3.org/2000/svg" class="hidden md:block w-4 h-4 text-[#A0A8C0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>
</header>

<script>
    function toggleNotificationPanel(force = null) {
        const panel = document.getElementById('notificationPanel');
        if (!panel) return;

        if (force === true) {
            panel.classList.remove('hidden');
            return;
        }

        if (force === false) {
            panel.classList.add('hidden');
            return;
        }

        panel.classList.toggle('hidden');
    }

    document.addEventListener('click', function (e) {
        const panel = document.getElementById('notificationPanel');
        if (!panel) return;

        const notifWrapper = e.target.closest('.relative');
        const clickedButton = e.target.closest('[onclick*="toggleNotificationPanel"]');

        if (!panel.contains(e.target) && !clickedButton) {
            panel.classList.add('hidden');
        }
    });
</script>