
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'BizTrack Pro') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f6f6f8] text-[#111318] h-screen flex items-center justify-center p-4 font-display overflow-hidden relative">
    
    <!-- Skeletal Loader -->
    <div id="skeleton-loader" class="fixed inset-0 z-[9999] bg-[#f6f6f8] flex items-center justify-center transition-opacity duration-500">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full max-w-md animate-pulse">
            <div class="flex flex-col items-center mb-8">
                <div class="w-16 h-16 bg-gray-200 rounded-2xl mb-4"></div>
                <div class="h-6 w-48 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-32 bg-gray-100 rounded"></div>
            </div>
            <div class="space-y-6">
                <div class="h-12 w-full bg-gray-100 rounded-lg"></div>
                <div class="h-12 w-full bg-gray-100 rounded-lg"></div>
                <div class="h-12 w-full bg-gray-200 rounded-lg"></div>
            </div>
        </div>
    </div>
    
    <script>
        window.addEventListener('load', function() {
            sessionStorage.removeItem('app_active_session');
            const skeleton = document.getElementById('skeleton-loader');
            if(skeleton) {
                skeleton.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => { skeleton.remove(); }, 500);
            }
        });
    </script>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-200 p-8 md:p-10">
            <div class="text-center mb-8">
                <div class="bg-gradient-to-br from-primary to-blue-600 text-white w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Welcome back</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Sign in to your inventory dashboard</p>
            </div>

            @if (session('status'))
                <div class="mb-6 text-sm font-medium text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl text-center shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5" onsubmit="sessionStorage.setItem('app_active_session', 'true');">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">Email Address</label>
                    <input type="email" name="email" id="email" required autofocus
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                        placeholder="admin@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1 font-semibold ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                        placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1 font-semibold ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary/20 transition-colors">
                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Remember me</span>
                    </label>
                    
                    <a href="{{ route('password.request') }}" class="text-sm text-primary hover:text-blue-700 font-semibold transition-colors">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-3.5 rounded-xl font-bold hover:bg-primary/90 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-primary/30 flex items-center justify-center gap-2 group">
                    <span>Sign In</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-400 font-medium">Protected by Blotanna Security Systems</p>
                <p class="text-[10px] text-gray-300 mt-1">&copy; {{ date('Y') }} BizTrack Pro</p>
            </div>
        </div>
    </div>
</body>
</html>
