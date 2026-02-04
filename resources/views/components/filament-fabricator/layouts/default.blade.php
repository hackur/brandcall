<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} - BrandCall</title>
    <meta name="description" content="{{ $page->meta_description ?? 'Branded Caller ID Platform' }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased">
    <div class="relative min-h-screen bg-slate-950">
        <!-- Subtle gradient background -->
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950"></div>
        
        <!-- Single subtle accent glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-brand-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- Content -->
        <div class="relative z-10">
            <!-- Navigation -->
            <nav class="px-6 py-6">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <!-- Logo -->
                    <a href="/" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 shadow-lg shadow-brand-600/25">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">BrandCall</span>
                    </a>

                    <!-- Nav Links -->
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white transition-colors">
                                Log in
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Page Blocks -->
            {{ $slot }}

            <!-- Footer -->
            <footer class="py-12 border-t border-slate-800/50">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <!-- Logo -->
                        <a href="/" class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-600">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <span class="font-semibold text-white">BrandCall</span>
                        </a>

                        <!-- Links -->
                        <div class="flex items-center gap-8 text-sm">
                            <a href="#" class="text-slate-400 hover:text-white transition-colors">Privacy</a>
                            <a href="#" class="text-slate-400 hover:text-white transition-colors">Terms</a>
                            <a href="#" class="text-slate-400 hover:text-white transition-colors">Support</a>
                        </div>

                        <!-- Copyright -->
                        <p class="text-sm text-slate-500">
                            © {{ date('Y') }} BrandCall. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
