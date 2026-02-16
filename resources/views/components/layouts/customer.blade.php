<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Partyhelp' }}</title>
    <link rel="icon" href="{{ asset('images/brand/ph-icon-dark.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <header class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <a href="https://partyhelp.com.au" class="inline-block">
                <img src="{{ asset('images/brand/ph-logo-white.png') }}" alt="Partyhelp" class="brand-logo-inverse">
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="bg-gray-800 border-t border-gray-700 mt-12">
        <div class="max-w-4xl mx-auto px-4 py-6 text-center text-gray-400 text-sm">
            &copy; {{ date('Y') }} Partyhelp. All rights reserved.
        </div>
    </footer>
</body>
</html>
