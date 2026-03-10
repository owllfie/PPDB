<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $isGuest = !auth()->check();
    @endphp
    <style>
        #main-content {
            padding-top: {{ $isGuest ? '2rem' : '5rem' }};
            min-height: 100vh;
        }
        @media (min-width: 640px) {
            #main-content {
                margin-left: {{ $isGuest ? '0' : '16rem' }};
            }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    @if (!$isGuest)
        @include('components.header')
    @endif

    @if (!$isGuest)
        @include('components.sidebar')
    @endif

    <div id="main-content">
        <div class="px-4 py-6 md:px-8 max-w-full">
            @yield('content')
        </div>
    </div>

    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('logo-sidebar');
            sidebar?.classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>
