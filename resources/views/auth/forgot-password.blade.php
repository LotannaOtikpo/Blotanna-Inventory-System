
@php
    $businessName = \App\Models\Setting::getValue('business_name', 'Blotanna Nig Ltd');
    $businessLogo = \App\Models\Setting::getValue('business_logo');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ $businessName }}</title>
    
    @if($businessLogo)
        <link rel="icon" href="{{ route('files.display', ['path' => $businessLogo]) }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📦</text></svg>">
    @endif
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f6f6f8] text-[#111318] h-screen flex items-center justify-center p-4 font-display">

    <!-- Skeletal Loader -->
    <div id="skeleton-loader" class="fixed inset-0 z-[9999] bg-[#f6f6f8] flex items-center justify-center transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full max-w-md animate-pulse">
            <div class="flex flex-col items-center mb-8">
                <div class="w-12 h-12 bg-gray-200 rounded-xl mb-4"></div>
                <div class="h-6 w-48 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-64 bg-gray-100 rounded"></div>
            </div>
            <div class="space-y-6">
                <div>
                    <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-100 rounded-lg"></div>
                </div>
                 <div class="h-11 w-full bg-gray-200 rounded-lg"></div>
            </div>
            <div class="mt-6 text-center">
                 <div class="h-3 w-32 bg-gray-100 rounded mx-auto"></div>
            </div>
        </div>
    </div>
    
    <script>
        window.addEventListener('load', function() {
            const skeleton = document.getElementById('skeleton-loader');
            if(skeleton) {
                skeleton.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => {
                    skeleton.remove();
                }, 300);
            }
        });
    </script>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-primary/10 text-primary w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 11 11.536 11l-3 3m0 0l-3 3m3-3V14a1 1 0 01-1-1v-3a1 1 0 011-1h1m5-6h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Forgot password?</h1>
            <p class="text-sm text-gray-500 mt-2">Enter your email and we'll send you a reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                    placeholder="admin@example.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                Send Reset Link
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                &larr; Back to Login
            </a>
        </div>
    </div>
</body>
</html> 