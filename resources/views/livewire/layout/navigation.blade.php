<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false, showChangelog: false }" class="bg-gradient-to-r from-white via-slate-50 to-white dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700/50 shadow-lg dark:shadow-xl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo - UOB Only -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3 group">
                        <!-- UOB Logo with Enhanced Styling -->
                        <div class="w-20 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg border border-slate-200 dark:border-slate-600 p-1.5 transition-all duration-300 group-hover:shadow-2xl group-hover:scale-105">
                            <img src="{{ asset('images/uob-logo-color.png') }}"
                                alt="UOB"
                                class="w-full h-full object-contain">
                        </div>
                        <!-- App Title -->
                        <div class="hidden lg:block">
                            <h1 class="text-slate-800 dark:text-white font-bold text-sm leading-tight">Check Payment</h1>
                            <p class="text-slate-500 dark:text-slate-400 text-xs">Export System</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links with Modern Style -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                       wire:navigate
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/50' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Express
                    </a>
                    <a href="{{ route('oracle') }}"
                       wire:navigate
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('oracle') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/50' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                        </svg>
                        Oracle
                    </a>
                    <a href="{{ route('scb') }}"
                       wire:navigate
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('scb') ? 'bg-purple-700 text-white shadow-lg shadow-purple-500/50' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        SCB
                    </a>
                </div>
            </div>

            <!-- Right Side: Version Badge + User Menu -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- Version Badge -->
                <button @click="showChangelog = true"
                    class="flex items-center space-x-2 bg-slate-100 hover:bg-blue-50 dark:bg-slate-700/50
                           dark:hover:bg-blue-900/30 text-slate-600 dark:text-slate-300 hover:text-blue-600
                           dark:hover:text-blue-400 text-xs font-mono px-3 py-1.5 rounded-full border
                           border-slate-200 dark:border-slate-600 hover:border-blue-300 dark:hover:border-blue-600
                           transition-all cursor-pointer group">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse flex-shrink-0"></span>
                    <span class="font-semibold">{{ config('app_version.version', 'UOB-1.0.0') }}</span>
                    <span class="text-slate-400 dark:text-slate-500">·</span>
                    <span class="text-slate-400 dark:text-slate-500">{{ config('app_version.release_date') }}</span>
                    <span class="opacity-50 group-hover:opacity-100 transition-opacity">▾</span>
                </button>
                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-slate-300 dark:border-slate-600 text-sm leading-4 font-medium rounded-lg text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900 transition-all duration-200 shadow-sm dark:shadow-none">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name" class="font-semibold"></div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Signed in as</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <x-dropdown-link :href="route('profile')" wire:navigate class="text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50">
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <div class="border-t border-slate-200 dark:border-slate-700"></div>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-500/10">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Changelog Modal (Alpine.js) -->
    <div x-show="showChangelog"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="showChangelog = false"
         @keydown.escape.window="showChangelog = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         style="display: none;">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl
                    max-h-[80vh] overflow-hidden flex flex-col m-4"
             @click.stop>

            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-5 flex justify-between items-center flex-shrink-0">
                <div>
                    <h3 class="text-white font-bold text-lg">Release Notes</h3>
                    <p class="text-blue-200 text-sm">
                        UOB Check Payment Export System · {{ config('app_version.version') }}
                    </p>
                </div>
                <button @click="showChangelog = false"
                    class="text-white/70 hover:text-white text-2xl leading-none transition-colors">&times;</button>
            </div>

            {{-- Changelog List --}}
            <div class="overflow-y-auto p-6 space-y-6">
                @foreach(config('app_version.changelog', []) as $index => $log)
                <div class="border-l-4 pl-4
                    @if($log['type'] === 'Bug Fix') border-red-400
                    @elseif($log['type'] === 'Feature') border-green-400
                    @elseif($log['type'] === 'Improvement') border-yellow-400
                    @else border-blue-400 @endif">

                    {{-- Version + Date --}}
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-mono font-bold text-sm
                                {{ $index === 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $log['version'] }}
                            </span>
                            @if($index === 0)
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">
                                Latest
                            </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 text-right space-y-0.5">
                            <div>{{ $log['date'] }}</div>
                            @if(!empty($log['reporter']))
                            <div>
                                <span class="text-orange-400">แจ้ง:</span> {{ $log['reporter'] }}
                                &nbsp;
                                <span class="text-blue-400">แก้ไข:</span> {{ $log['fixed_by'] }}
                            </div>
                            @else
                            <div><span class="text-blue-400">โดย:</span> {{ $log['fixed_by'] }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Type Badge + Title --}}
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($log['type'] === 'Bug Fix') bg-red-100 text-red-700
                            @elseif($log['type'] === 'Feature') bg-green-100 text-green-700
                            @elseif($log['type'] === 'Improvement') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ $log['type'] }}
                        </span>
                        <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                            {{ $log['title'] }}
                        </span>
                    </div>

                    {{-- Description --}}
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $log['description'] }}</p>

                    {{-- Bugs Fixed --}}
                    @if(!empty($log['bugs_fixed']))
                    <ul class="space-y-1 mb-2">
                        @foreach($log['bugs_fixed'] as $bug)
                        <li class="flex items-start space-x-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="mt-0.5 flex-shrink-0">🐛</span>
                            <span>{{ $bug }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    {{-- Files Changed --}}
                    @if(!empty($log['files_changed']))
                    <div class="flex flex-wrap gap-1">
                        @foreach($log['files_changed'] as $file)
                        <span class="text-xs font-mono bg-gray-100 dark:bg-slate-700
                                     text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded">
                            {{ $file }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Modal Footer --}}
            <div class="border-t border-gray-100 dark:border-slate-700 px-6 py-3 flex-shrink-0
                        flex justify-between items-center bg-gray-50 dark:bg-slate-900/50">
                <span class="text-xs text-gray-400">
                    {{ count(config('app_version.changelog', [])) }} releases
                </span>
                <button @click="showChangelog = false"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 dark:border-slate-700/50">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <a href="{{ route('dashboard') }}"
               wire:navigate
               class="flex items-center px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                {{ __('Express') }}
            </a>
            <a href="{{ route('oracle') }}"
               wire:navigate
               class="flex items-center px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('oracle') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
                {{ __('Oracle') }}
            </a>
            <a href="{{ route('scb') }}"
               wire:navigate
               class="flex items-center px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('scb') ? 'bg-purple-700 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                {{ __('SCB') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-200 dark:border-slate-700/50">
            <div class="px-6 mb-3">
                <div class="font-medium text-base text-slate-900 dark:text-white" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</div>
            </div>

            <div class="space-y-1 px-2">
                <a href="{{ route('profile') }}"
                   wire:navigate
                   class="flex items-center px-4 py-3 text-base font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start flex items-center px-4 py-3 text-base font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    {{ __('Log Out') }}
                </button>
            </div>
        </div>
    </div>
</nav>
