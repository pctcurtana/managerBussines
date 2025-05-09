<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        // Auto refresh CSRF token to avoid 419 errors
        function refreshCSRFToken() {
            fetch('/csrf-token')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                    if (window.axios) {
                        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                    }
                })
                .catch(error => console.error('Error refreshing CSRF token:', error));
        }
        
        // Refresh token every 30 minutes
        setInterval(refreshCSRFToken, 30 * 60 * 1000);
        
        // Also refresh on page interaction after inactivity
        let activityTimer;
        function resetActivityTimer() {
            clearTimeout(activityTimer);
            activityTimer = setTimeout(refreshCSRFToken, 10 * 60 * 1000); // 10 minutes
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            ['click', 'keypress', 'scroll', 'mousemove'].forEach(function(event) {
                document.addEventListener(event, resetActivityTimer);
            });
            resetActivityTimer();
        });
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html> 