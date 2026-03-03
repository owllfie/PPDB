<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#0a0e1a] font-[Inter] flex flex-col items-center justify-center px-4 py-12 relative overflow-y-auto">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-purple-600/8 rounded-full blur-[150px] animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-[40%] right-[20%] w-[300px] h-[300px] bg-violet-500/5 rounded-full blur-[100px] animate-pulse" style="animation-delay: 4s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                @foreach ($errors->all() as $error)
                    <p class="text-red-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        @yield('content')

    </div>
</body>
</html>
