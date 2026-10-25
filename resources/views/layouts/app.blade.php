<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'homely - Home Services Booking Platform')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        tealPrimary: '#0d9488',
                        tealHover: '#0f766e',
                        darkSlate: '#0f172a',
                        lightBg: '#f8fafc'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-lightBg font-sans text-slate-800 flex flex-col min-h-screen">

    <!-- Navigation Bar Component -->
    <x-navbar />

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Component -->
    <x-footer />

</body>
</html>