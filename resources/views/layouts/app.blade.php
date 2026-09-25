<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sistem Pengawasan - Parlemen Remaja 2026</title>
    
    <!-- PWA Setup -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#000000">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon.svg') }}">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Signature Pad JS -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex flex-col">
    
    <!-- Navbar B&W Modern -->
    <nav class="bg-black text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold tracking-widest flex items-center space-x-2">
                    <span>PENGAWASAN '26</span>
                </a>
                
                @if(!request()->routeIs('home'))
                <a href="{{ route('home') }}" class="flex items-center text-xs font-bold bg-white text-black px-3 py-1.5 rounded-md hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
                @endif
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        @yield('content')
    </main>

    <footer class="text-center text-gray-500 text-xs py-6 border-t border-gray-200 bg-white mt-auto">
        &copy; 2026 Sistem Form Pengawasan Parlemen Remaja.
    </footer>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    @stack('scripts')
</body>
</html>