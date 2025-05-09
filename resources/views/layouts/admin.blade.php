<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Quản lý bán máy ảnh">
    
    <!-- Preconnect và preload resources quan trọng -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    
    <title>{{ config('app.name', 'Camera Shop') }}</title>

    <!-- Styles quan trọng inline -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Critical CSS inline để tăng tốc render -->*/
        body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; margin: 0; }
        .bg-gray-100 { background-color: rgb(243 244 246); }
        .min-h-screen { min-height: 100vh; }
        .font-sans { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    </style>
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS từ CDN với defer -->
    <script src="https://cdn.tailwindcss.com" defer></script>

    <!-- Scripts -->
    @if(app()->environment('local'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
        <script src="{{ asset('build/assets/app.js') }}" defer></script>
    @endif
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Content -->
        <div class="">
            <!-- Top bar -->
            <div class="sticky top-0 z-10 flex items-center justify-between h-16 px-6 bg-white shadow">
        
                <!-- User dropdown -->
                <div class="flex justify-between w-full">
                    <h1 class="text-xl font-semibold">Camera Shop</h1>
                    
                    <div class="relative ml-3 flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Xin chào, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="py-1">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Script chỉ tải sau khi page đã loaded -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Thêm hỗ trợ memory cache custom  
            if (!window.memoryCache) {
                window.memoryCache = {
                    data: {},
                    get: function(key) {
                        const item = this.data[key];
                        if (item && item.expiry > Date.now()) {
                            return item.value;
                        }
                        delete this.data[key];
                        return null;
                    },
                    set: function(key, value, ttl = 300000) {
                        this.data[key] = {
                            value: value,
                            expiry: Date.now() + ttl
                        };
                    },
                    clear: function() {
                        this.data = {};
                    }
                };
            }
        });
    </script>
</body>
</html> 