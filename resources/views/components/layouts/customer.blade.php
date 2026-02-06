<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Partyhelp' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'ph-purple': {
                            400: '#a78bfa', 500: '#8b5cf6',
                            600: '#7c3aed', 700: '#6d28d9',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <header class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <a href="https://partyhelp.com.au" class="text-2xl font-bold text-ph-purple-400">
                Partyhelp
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
