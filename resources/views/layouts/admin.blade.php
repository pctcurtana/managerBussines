<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NgocNhi Camera Shop') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS từ CDN (fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fallback for CSS if Vite doesn't work -->
    @if (!app()->environment('local'))
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
                    <h1 class="text-xl font-semibold">Camera Shop Admin</h1>
                    
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
            <main class="py-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html> 