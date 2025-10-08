<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'UOB Check Payment') }}</title>

        <!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/uob-icon.png') }}">

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
                    <!-- Oracle + UOB Logo -->
                    <div class="flex justify-center items-center space-x-6">
                        <!-- Oracle Logo -->
                        <div class="relative">
                            <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-2xl hover:shadow-3xl transition-shadow duration-300">
                                <span class="text-white font-bold text-lg">ORACLE</span>
                            </div>
                        </div>

                        <!-- Connection Line -->
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <div class="w-12 h-0.5 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse animation-delay-200"></div>
                        </div>

                        <!-- UOB Logo -->
                        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-2xl hover:shadow-3xl transition-shadow duration-300 p-2">
                            <img src="{{ asset('images/uob-logo-color.png') }}" 
                                alt="UOB" 
                                class="w-full h-full object-contain">
                        </div>
                    </div>
                </a>
                
                <!-- Title -->
                <div class="mt-6 text-center">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Export Check Payment to UOB</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Oracle & UOB Integration Platform</p>
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