<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pay Export Hub') }}</title>

        <!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="{{ asset('images/payment-hub-icon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Custom Animation Styles -->
        <style>
            @keyframes pulse-slow {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
            .animation-delay-200 {
                animation-delay: 200ms;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
            <!-- Logo Section -->
            <div class="mb-8">
                <a href="/" wire:navigate>
                    <!-- Pay Export Hub Logo -->
                    <div class="flex justify-center items-center">
                        <div class="w-28 h-28 bg-white rounded-2xl flex items-center justify-center shadow-2xl hover:shadow-3xl transition-shadow duration-300 p-3 border border-slate-100">
                            <img src="{{ asset('images/payment-hub-icon.svg') }}"
                                alt="Pay Export Hub"
                                class="w-full h-full object-contain">
                        </div>
                    </div>
                </a>
                
                <!-- Title -->
                <div class="mt-6 text-center">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Export Check Payment Platform</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Oracle, UOB &amp; SCB Integration Platform</p>
                </div>
            </div>

            <!-- Login/Register Card -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
                <p>&copy; 2025 Thai Rung Union Car Public Company Limited. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
