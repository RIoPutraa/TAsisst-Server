<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TAssist</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden bg-[#1A2035]" style="font-family: Inter, sans-serif;">

    <!-- Background decoration -->
    <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10 blur-3xl bg-[#0057B8]"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full opacity-10 blur-3xl bg-[#4DA3FF]"></div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md mx-4 rounded-2xl p-8 shadow-2xl bg-[#242D45] border border-[#3A4566]">
        
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 bg-[#0057B8]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                </svg>
            </div>

            <h1 class="text-white text-2xl font-extrabold">TAssist</h1>
            <p class="text-sm mt-1 text-[#A0A8C0]">
                Academic TA Management System
            </p>
            <div class="mt-3 px-3 py-1 rounded-full text-xs font-medium bg-[rgba(0,87,184,0.15)] text-[#4DA3FF]">
                Admin Portal
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
            @csrf

            @if ($errors->any())
                <div class="flex items-start gap-2 p-3 rounded-xl text-sm bg-[rgba(255,77,77,0.1)] border border-[rgba(255,77,77,0.3)] text-[#FF4D4D]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.66 18h16.68a1 1 0 00.87-1.5l-7.5-13a1 1 0 00-1.74 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-start gap-2 p-3 rounded-xl text-sm bg-[rgba(255,77,77,0.1)] border border-[rgba(255,77,77,0.3)] text-[#FF4D4D]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.66 18h16.68a1 1 0 00.87-1.5l-7.5-13a1 1 0 00-1.74 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm mb-2 text-[#A0A8C0] font-medium">
                    Email Address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your Email"
                    required
                    class="w-full px-4 py-3 rounded-xl outline-none transition-all duration-200 text-sm bg-[#2A3352] border border-[#3A4566] text-white focus:border-[#4DA3FF] focus:ring-4 focus:ring-[rgba(77,163,255,0.1)]"
                >
            </div>

            <!-- Password -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm mb-2 text-[#A0A8C0] font-medium">
                    Password
                </label>

                <div class="relative">
                    <input
                        :type="show ? 'text' : 'password'"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        class="w-full px-4 py-3 pr-12 rounded-xl outline-none transition-all duration-200 text-sm bg-[#2A3352] border border-[#3A4566] text-white focus:border-[#4DA3FF] focus:ring-4 focus:ring-[rgba(77,163,255,0.1)]"
                    >

                    <button
                        type="button"
                        @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-[#A0A8C0]"
                    >
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>

                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.11-2.27A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.132 5.411M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m3-3v3m0 0l6.364 6.364M12 12L5.636 18.364M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Login Button -->
            <button
                type="submit"
                class="w-full py-3 rounded-xl text-white text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-[#0057B8] hover:bg-[#0046A0] font-semibold"
            >
                Sign In
            </button>
        </form>

        <p class="text-center text-xs mt-6 text-[#A0A8C0]">
            TAssist
        </p>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>