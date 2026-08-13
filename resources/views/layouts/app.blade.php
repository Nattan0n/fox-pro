<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light">

        <title>Pay Export Hub</title>

        <!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="{{ asset('images/payment-hub-icon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Toast Notifications (global, top-right, auto-dismiss 10s) -->
            <div
                x-data="{
                    toasts: [],
                    _seq: 0,
                    addToast(type, html) {
                        const id = ++this._seq;
                        this.toasts.push({ id, type, html, visible: true });
                        setTimeout(() => this.dismiss(id), 10000);
                    },
                    dismiss(id) {
                        const t = this.toasts.find(t => t.id === id);
                        if (t) t.visible = false;
                        setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id) }, 400);
                    }
                }"
                @scb-notify.window="addToast($event.detail.type, $event.detail.html)"
                class="fixed top-6 right-6 z-50 space-y-3 w-80"
                style="pointer-events:none"
            >
                <template x-for="toast in toasts" :key="toast.id">
                    <div
                        x-show="toast.visible"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-8"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-8"
                        :class="toast.type === 'error' ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200'"
                        class="rounded-2xl shadow-2xl p-4"
                        style="pointer-events:auto"
                    >
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <svg x-show="toast.type === 'success'" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                                    :class="toast.type === 'error' ? 'text-red-500' : 'text-green-600'"
                                    x-text="toast.type === 'error' ? 'Error' : 'Success'">
                                </p>
                                <div class="text-sm leading-snug"
                                    :class="toast.type === 'error' ? 'text-red-700' : 'text-green-700'"
                                    x-html="toast.html">
                                </div>
                            </div>
                            <button @click="dismiss(toast.id)" class="flex-shrink-0 ml-1 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3 h-0.5 rounded-full overflow-hidden"
                            :class="toast.type === 'error' ? 'bg-red-100' : 'bg-green-100'">
                            <div class="h-full rounded-full w-full"
                                :class="toast.type === 'error' ? 'bg-red-400' : 'bg-green-400'"
                                style="animation: toast-shrink 10s linear forwards">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <style>
            @keyframes toast-shrink { from { width:100% } to { width:0% } }
            </style>
        </div>
    </body>
</html>
