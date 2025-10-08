<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
    <div class="py-8">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Container with UOB Banking Style -->
            <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl shadow-2xl border-0 rounded-3xl overflow-hidden">
                <!-- Modern Header Section -->
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-2">Check Payment</h2>
                            <p class="text-blue-100 text-lg">Manage your check payments and exports ( CU27 )</p>
                        </div>
                        <div class="hidden lg:flex items-center space-x-6">
                            <div class="flex items-center justify-center p-4 bg-white rounded-xl shadow-lg">
                                <img src="{{ asset('images/uob-logo-color.png') }}"
                                    alt="UOB Logo"
                                    class="h-10 w-auto">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gradient-to-b from-white to-gray-50/50 dark:from-slate-800 dark:to-slate-900/50">
                    <!-- Enhanced Search Form -->
                    <div class="mb-8">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl border border-gray-100 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Search Parameters</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Configure your payment search criteria</p>
                                    </div>
                                </div>
                                <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-full">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-green-700 dark:text-green-400 text-sm font-medium">Oracle Connected</span>
                                </div>
                            </div>

                            <!-- Enhanced Form Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                                <!-- Organization Selection -->
                                <div class="space-y-3">
                                    <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <div class="w-5 h-5 bg-gradient-to-br from-orange-400 to-orange-500 rounded flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span>Organization</span>
                                    </label>
                                    <div class="relative">
                                        <select wire:model="orgId"
                                            class="w-full px-5 py-4 border-2 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 appearance-none cursor-pointer hover:border-gray-300 dark:hover:border-slate-500">
                                            @foreach($orgOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Enhanced Date Input -->
                                <div class="space-y-3">
                                    <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <div class="w-5 h-5 bg-gradient-to-br from-blue-400 to-blue-500 rounded flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span>Check Date</span>
                                    </label>
                                    <div class="relative">
                                        <input wire:model="selectedDate"
                                            type="date"
                                            class="w-full px-5 py-4 border-2 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300"
                                            required />
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Select specific date for payment search</span>
                                    </p>
                                </div>
                                
                                <!-- User Selection -->
                                <div class="space-y-3">
                                    <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <div class="w-5 h-5 bg-gradient-to-br from-purple-400 to-purple-500 rounded flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span>User Filter</span>
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded-full">Optional</span>
                                    </label>
                                    <div class="relative">
                                        <select wire:model="userId"
                                            class="w-full px-5 py-4 border-2 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 appearance-none cursor-pointer hover:border-gray-300 dark:hover:border-slate-500">
                                            <option value="">All Users</option>
                                            @foreach($userOptions as $value => $label)
                                            @if($value !== '')
                                            <option value="{{ $value }}">{{ $label }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Check start, end -->
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <label for="check_start" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Check Start</label>
                                        <div class="mt-1">
                                            <input type="text" id="check_start" wire:model.live.debounce.300ms="checkStart"
                                                class="w-full px-5 py-4 border-2 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 appearance-none cursor-pointer hover:border-gray-300 dark:hover:border-slate-500"
                                                placeholder="Check Start">
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label for="check_end" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Check End</label>
                                        <div class="mt-1">
                                            <input type="text" id="check_end" wire:model.live.debounce.300ms="checkEnd"
                                                class="w-full px-5 py-4 border-2 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 appearance-none cursor-pointer hover:border-gray-300 dark:hover:border-slate-500"
                                                placeholder="Check End">
                                        </div>
                                    </div>
                                </div>
                            </div>
<!-- เพิ่มหลังจาก form grid และก่อน action buttons -->

<!-- Validation Message -->
@if(empty($checkStart) && empty($checkEnd))
<div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-2 border-amber-200 dark:border-amber-700 rounded-2xl p-6 shadow-lg">
    <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200">Check Number Required</h4>
            <p class="text-amber-700 dark:text-amber-300">Please enter either Check Start or Check End number to search for payment data.</p>
            <p class="text-amber-600 dark:text-amber-400 text-sm mt-1">You can enter a single check number in either field or specify a range.</p>
        </div>
    </div>
</div>
@endif

<!-- Optional: Success validation message -->
@if(!empty($checkStart) || !empty($checkEnd))
<div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-700 rounded-2xl p-6 shadow-lg">
    <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            <h4 class="text-lg font-bold text-green-800 dark:text-green-200">Ready to Search</h4>
            <p class="text-green-700 dark:text-green-300">
                Search criteria: 
                @if(!empty($checkStart) && !empty($checkEnd))
                    Check numbers from {{ $checkStart }} to {{ $checkEnd }}
                @elseif(!empty($checkStart))
                    Check numbers starting from {{ $checkStart }}
                @else
                    Check numbers up to {{ $checkEnd }}
                @endif
            </p>
        </div>
    </div>
</div>
@endif
<!-- Enhanced Action Buttons -->
<div class="flex flex-col sm:flex-row gap-4">
    <button type="button"
        wire:click="getOracleData"
        @if(empty($checkStart) && empty($checkEnd)) disabled @endif
        class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-4 font-semibold rounded-xl shadow-xl transform focus:ring-4 focus:outline-none transition-all duration-300 group
            {{ (empty($checkStart) && empty($checkEnd))
               ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed shadow-sm' 
               : 'bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 hover:from-blue-700 hover:via-blue-800 hover:to-blue-900 text-white hover:shadow-2xl hover:-translate-y-1 focus:ring-blue-500/30' }}">
        
        <span wire:loading.remove wire:target="getOracleData" class="flex items-center space-x-3">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center transition-colors
                {{ (empty($checkStart) && empty($checkEnd)) 
                   ? 'bg-gray-400 dark:bg-gray-500' 
                   : 'bg-white/20 group-hover:bg-white/30' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <span>
                @if(empty($checkStart) && empty($checkEnd))
                    Enter Check Number Range
                @else
                    Search Oracle Data
                @endif
            </span>
            @if(!empty($checkStart) || !empty($checkEnd))
            <div class="w-2 h-2 bg-white/40 rounded-full group-hover:bg-white/60 transition-colors"></div>
            @endif
        </span>
        
        <span wire:loading wire:target="getOracleData" class="flex items-center space-x-3">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Processing...</span>
        </span>
    </button>

    @if (!$oracleData->isEmpty())
    <button type="button"
        wire:click="exportOracleCU27"
        @if(empty($selectedChecks)) disabled @endif
        class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-4 font-semibold rounded-xl shadow-xl transform focus:ring-4 focus:outline-none transition-all duration-300 group
            {{ empty($selectedChecks) 
               ? 'bg-gray-300 text-gray-500 cursor-not-allowed shadow-sm' 
               : 'bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700 hover:from-emerald-600 hover:via-emerald-700 hover:to-emerald-800 text-white hover:shadow-2xl hover:-translate-y-1 focus:ring-emerald-500/30' }}">
        <span wire:loading.remove wire:target="exportOracleCU27" class="flex items-center space-x-3">
            <div class="w-6 h-6 {{ empty($selectedChecks) ? 'bg-gray-400' : 'bg-white/20 group-hover:bg-white/30' }} rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span>
                @if(!empty($selectedChecks))
                Export CU27 ({{ count($selectedChecks) }} selected)
                @else
                Select checks to export
                @endif
            </span>
        </span>
        <span wire:loading wire:target="exportOracleCU27" class="flex items-center space-x-3">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Exporting...</span>
        </span>
    </button>
    @endif
</div>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    @if (session()->has('success'))
                    <div class="mb-8 bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border-2 border-emerald-200 dark:border-emerald-700 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-emerald-800 dark:text-emerald-200">Success!</h4>
                                <p class="text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if (session()->has('error'))
                    <div class="mb-8 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-2 border-red-200 dark:border-red-700 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-red-800 dark:text-red-200">Error</h4>
                                <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if (session()->has('warning'))
                    <div class="mb-8 bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 border-2 border-amber-200 dark:border-amber-700 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200">Warning</h4>
                                <p class="text-amber-700 dark:text-amber-300">{{ session('warning') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if (session()->has('info'))
                    <div class="mb-8 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-blue-800 dark:text-blue-200">Information</h4>
                                <p class="text-blue-700 dark:text-blue-300">{{ session('info') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- DATA DISPLAY WRAPPER - รอจนกว่าจะโหลดครบ 100% -->
                    <div x-data="{ 
                        dataReady: @entangle('dataReady').live,
                        dataCount: 0
                    }" 
                         x-show="dataReady"
                         x-cloak
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100">

                        <!-- Enhanced Selection Controls -->
                        @if (!$oracleData->isEmpty())
                        <div class="mb-8 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/20 dark:via-purple-900/20 dark:to-pink-900/20 border-2 border-indigo-200 dark:border-indigo-700 rounded-2xl p-8 shadow-xl">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center space-x-4">
                                        <div x-data="{ 
                                            selectAll: @entangle('selectAllChecks').live,
                                            selectedChecks: @entangle('selectedChecks').live,
                                            uniqueCount: {{ $oracleData->unique('your_reference')->count() }},
                                            toggle() {
                                                $wire.set('selectAllChecks', !this.selectAll);
                                            }
                                        }" class="flex items-center space-x-4">
                                            <div class="relative">
                                                <input
                                                    type="checkbox"
                                                    x-model="selectAll"
                                                    @click="toggle()"
                                                    class="w-6 h-6 text-indigo-600 bg-white border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition-all duration-200"
                                                    id="selectAllChecks">
                                                <div class="absolute inset-0 w-6 h-6 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg opacity-0 hover:opacity-10 transition-opacity pointer-events-none"></div>
                                            </div>

                                            <label for="selectAllChecks" class="flex items-center space-x-3 cursor-pointer group">
                                                <div>
                                                    <p class="text-lg font-bold text-indigo-900 dark:text-indigo-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-200 transition-colors">
                                                        Select All Checks
                                                    </p>
                                                    <p class="text-sm text-indigo-600 dark:text-indigo-400">
                                                        Selected: <span x-text="selectedChecks.length" class="font-bold"></span> / <span x-text="uniqueCount" class="font-bold"></span> checks
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    @if(!empty($selectedChecks))
                                    <button
                                        type="button"
                                        wire:click="clearSelection"
                                        class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-red-100 to-rose-100 hover:from-red-200 hover:to-rose-200 text-red-700 text-sm font-semibold rounded-xl border-2 border-red-200 hover:border-red-300 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Clear Selection</span>
                                    </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 lg:flex lg:items-center lg:space-x-8 gap-4 lg:gap-0">
                                    <div class="flex items-center space-x-3 p-4 bg-white/60 dark:bg-slate-800/60 rounded-xl border border-indigo-200 dark:border-indigo-700">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Selected</p>
                                            <p class="text-xl font-bold text-indigo-900 dark:text-indigo-100">{{ count($selectedChecks) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3 p-4 bg-white/60 dark:bg-slate-800/60 rounded-xl border border-green-200 dark:border-green-700">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unique Checks</p>
                                            <p class="text-xl font-bold text-green-900 dark:text-green-100">{{ $oracleData->unique('your_reference')->count() }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3 p-4 bg-white/60 dark:bg-slate-800/60 rounded-xl border border-blue-200 dark:border-blue-700">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Records</p>
                                            <p class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ $oracleData->count() }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3 p-4 bg-white/60 dark:bg-slate-800/60 rounded-xl border border-purple-200 dark:border-purple-700">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Date</p>
                                            <p class="text-lg font-bold text-purple-900 dark:text-purple-100">{{ Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(!empty($selectedChecks))
                            <div class="mt-8 p-6 bg-white/80 dark:bg-slate-800/80 rounded-xl border-2 border-indigo-200/50 dark:border-indigo-700/50 shadow-inner">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-bold text-indigo-900 dark:text-indigo-100 flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Selected Check Numbers</span>
                                    </h4>
                                    <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-200 text-sm font-medium rounded-full">
                                        {{ count($selectedChecks) }} checks selected
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($selectedChecks as $checkNum)
                                    <div class="group inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 text-indigo-800 dark:text-indigo-200 text-sm font-medium rounded-full border-2 border-indigo-200 dark:border-indigo-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-mono">{{ $checkNum }}</span>
                                        <button
                                            type="button"
                                            wire:click="toggleCheckSelection('{{ $checkNum }}')"
                                            class="w-5 h-5 hover:bg-red-200 dark:hover:bg-red-800 rounded-full flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-900/50 transition-colors">
                                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Enhanced Data Table -->
                        @if (!$oracleData->isEmpty())
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 overflow-hidden">
                            <div class="px-8 py-6 bg-gradient-to-r from-gray-50 via-blue-50 to-indigo-50 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700 border-b border-gray-200 dark:border-slate-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Records</h3>
                                            <p class="text-gray-600 dark:text-gray-400">{{ Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</p>
                                        </div>
                                    </div>
                                    @if(!empty($selectedChecks))
                                    <div class="flex items-center space-x-2 px-4 py-2 bg-blue-100 dark:bg-blue-900/50 rounded-xl border border-blue-200 dark:border-blue-700">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-blue-800 dark:text-blue-200 font-semibold">{{ count($selectedChecks) }} selected</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="overflow-x-auto max-h-[700px] scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-slate-600 scrollbar-track-gray-100 dark:scrollbar-track-slate-800">
                                <table class="w-full">
                                    <thead class="bg-gradient-to-r from-gray-100 via-gray-50 to-gray-100 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Select</span>
                                                </div>
                                            </th>
                                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">Check Number</th>
                                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">Beneficiary Name</th>
                                            <th class="px-6 py-5 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">Check Amount</th>
                                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">Invoice Number</th>
                                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">Invoice Date</th>
                                            <th class="px-6 py-5 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">Invoice Amount</th>
                                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-slate-600">Vendor Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-100 dark:divide-slate-700">
                                        @php
                                        $processedChecks = [];
                                        $rowIndex = 0;
                                        @endphp

                                        @foreach ($oracleData as $index => $data)
                                        @php
                                        $checkNumber = $data->your_reference;
                                        $isFirstOccurrence = !in_array($checkNumber, $processedChecks);

                                        if ($isFirstOccurrence) {
                                            $processedChecks[] = $checkNumber;
                                        }

                                        $isSelected = in_array($checkNumber, $selectedChecks);
                                        $rowClasses = $index % 2 === 0 ? 'bg-white dark:bg-slate-800' : 'bg-gray-50/50 dark:bg-slate-800/50';

                                        if ($isSelected) {
                                            $rowClasses .= ' ring-2 ring-indigo-300 dark:ring-indigo-600 bg-gradient-to-r from-indigo-50/50 via-blue-50/50 to-indigo-50/50 dark:from-indigo-900/20 dark:via-blue-900/20 dark:to-indigo-900/20';
                                        }

                                        $uniqueRowId = 'row_' . $index . '_' . md5($checkNumber . $data->invoice_num);
                                        @endphp

                                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-all duration-200 {{ $rowClasses }}"
                                            id="{{ $uniqueRowId }}">

                                            <td class="px-6 py-5 whitespace-nowrap">
                                                @if($isFirstOccurrence)
                                                <div class="flex items-center">
                                                    <div class="relative">
                                                        <input
                                                            type="checkbox"
                                                            wire:model.live="selectedChecks"
                                                            value="{{ $checkNumber }}"
                                                            id="checkbox_{{ $checkNumber }}"
                                                            class="w-5 h-5 text-indigo-600 bg-white border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition-all duration-200"
                                                            {{ in_array($checkNumber, $selectedChecks) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>

                                            <td class="px-6 py-5 whitespace-nowrap">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg {{ $isSelected ? 'bg-gradient-to-br from-indigo-500 to-purple-600' : 'bg-gradient-to-br from-blue-500 to-cyan-600' }} transition-all duration-200">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-lg font-bold text-gray-900 dark:text-white font-mono">
                                                            {{ $checkNumber }}
                                                        </div>
                                                        @if($isSelected)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 dark:from-indigo-900/50 dark:to-purple-900/50 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-700">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Selected
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-6 py-5">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ Str::limit($data->benefi_name ?? 'N/A', 35) }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-5 text-right">
                                                <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                                    ฿{{ number_format($data->check_amt ?? 0, 2) }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-5">
                                                <div class="text-sm text-gray-600 dark:text-gray-400 font-mono bg-gray-50 dark:bg-slate-700 px-3 py-1 rounded-lg">
                                                    {{ $data->invoice_num ?? 'N/A' }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-5 whitespace-nowrap">
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $data->invoice_date ?? 'N/A' }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-5 text-right">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    ฿{{ number_format($data->invoice_amount ?? 0, 2) }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-5">
                                                @if(isset($data->vendor_type_desc) && !empty($data->vendor_type_desc))
                                                <span class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold shadow-sm
                                                    @if($data->vendor_type === 'EMPLOYEE') bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 dark:from-blue-900/50 dark:to-blue-800/50 dark:text-blue-300
                                                    @elseif($data->vendor_type === 'VENDOR') bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300 dark:from-green-900/50 dark:to-green-800/50 dark:text-green-300
                                                    @elseif($data->vendor_type === 'CONTRACTOR') bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300 dark:from-yellow-900/50 dark:to-yellow-800/50 dark:text-yellow-300
                                                    @elseif($data->vendor_type === 'CUSTOMER') bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300 dark:from-purple-900/50 dark:to-purple-800/50 dark:text-purple-300
                                                    @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 dark:from-gray-700 dark:to-gray-600 dark:text-gray-300
                                                    @endif">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $data->vendor_type_desc }}
                                                </span>
                                                @else
                                                <span class="inline-flex items-center px-3 py-2 rounded-xl text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    No Type
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Enhanced Summary Dashboard -->
                        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                            <div class="group bg-gradient-to-br from-blue-50 via-blue-100 to-cyan-100 dark:from-blue-900/30 dark:via-blue-800/30 dark:to-cyan-800/30 rounded-2xl p-8 border-2 border-blue-200/50 dark:border-blue-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-blue-600 dark:text-blue-400 text-sm font-bold uppercase tracking-wider">Total Records</p>
                                        <p class="text-4xl font-black text-blue-900 dark:text-blue-100 mt-2">{{ $oracleData->count() }}</p>
                                    </div>
                                </div>
                                <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>

                            <div class="group bg-gradient-to-br from-emerald-50 via-green-100 to-emerald-100 dark:from-emerald-900/30 dark:via-green-800/30 dark:to-emerald-800/30 rounded-2xl p-8 border-2 border-emerald-200/50 dark:border-emerald-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-emerald-600 dark:text-emerald-400 text-sm font-bold uppercase tracking-wider">Unique Checks</p>
                                        <p class="text-4xl font-black text-emerald-900 dark:text-emerald-100 mt-2">{{ $oracleData->unique('your_reference')->count() }}</p>
                                    </div>
                                </div>
                                <div class="w-full bg-emerald-200 dark:bg-emerald-800 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-2 rounded-full" style="width: {{ $oracleData->count() > 0 ? ($oracleData->unique('your_reference')->count() / $oracleData->count()) * 100 : 0 }}%"></div>
                                </div>
                            </div>

                            <div class="group bg-gradient-to-br from-indigo-50 via-purple-100 to-indigo-100 dark:from-indigo-900/30 dark:via-purple-800/30 dark:to-indigo-800/30 rounded-2xl p-8 border-2 border-indigo-200/50 dark:border-indigo-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-indigo-600 dark:text-indigo-400 text-sm font-bold uppercase tracking-wider">Selected Checks</p>
                                        <p class="text-4xl font-black text-indigo-900 dark:text-indigo-100 mt-2">{{ count($selectedChecks) }}</p>
                                    </div>
                                </div>
                                <div class="w-full bg-indigo-200 dark:bg-indigo-800 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full" style="width: {{ $oracleData->unique('your_reference')->count() > 0 ? (count($selectedChecks) / $oracleData->unique('your_reference')->count()) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div> <!-- ปิด DATA DISPLAY WRAPPER -->

                </div>
            </div>
        </div>

        <!-- Full Screen Loading Modal -->
        <div
            x-data="{ 
                progress: @entangle('loadingProgress').live,
                message: @entangle('loadingMessage').live,
                isVisible: @entangle('isLoading').live,
                isProcessing: @entangle('isProcessing').live,
                pollingActive: @entangle('pollingActive').live,
                dataReady: @entangle('dataReady').live,
                isComplete: false,
                pollingInterval: null,
                stuckCounter: 0,
                lastProgress: 0,
                
                startPolling() {
                    if (this.pollingInterval) return;
                    console.log('Starting polling...');
                    
                    this.pollingInterval = setInterval(() => {
                        if (!this.isProcessing) {
                            console.log('Stopping polling - not processing');
                            this.stopPolling();
                            return;
                        }
                        
                        // Check if stuck
                        if (this.progress === this.lastProgress && this.progress < 100) {
                            this.stuckCounter++;
                            console.log('Progress stuck at', this.progress, '% - Counter:', this.stuckCounter);
                            
                            if (this.stuckCounter > 20) { // 20 * 200ms = 4 seconds stuck
                                console.log('Progress stuck, forcing continue...');
                                @this.call('forceContinue');
                                this.stuckCounter = 0;
                            }
                        } else {
                            this.stuckCounter = 0;
                        }
                        
                        this.lastProgress = this.progress;
                        
                        // Continue processing if needed
                        if (this.pollingActive && this.isProcessing && this.progress < 100) {
                            @this.call('processDataStep');
                        }
                    }, 200);
                },
                
                stopPolling() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                        console.log('Polling stopped');
                    }
                }
            }"
            x-show="isVisible"
            x-init="
                // Start polling when visible
                $watch('isVisible', value => {
                    console.log('Loading modal visibility changed:', value);
                    if (value) {
                        startPolling();
                    } else {
                        stopPolling();
                    }
                });
                
                // Watch progress
                $watch('progress', value => {
                    console.log('Progress updated:', value);
                    isComplete = value >= 100;
                    
                    if (isComplete) {
                        console.log('Loading complete, finalizing...');
                        setTimeout(() => {
                            @this.call('finalizeDataDisplay');
                            stopPolling();
                        }, 1500);
                    }
                });
                
                // Listen for custom events
                window.addEventListener('startLoadingProcess', () => {
                    console.log('Event: startLoadingProcess');
                    startPolling();
                });
                
                window.addEventListener('continueProcessing', () => {
                    console.log('Event: continueProcessing');
                    setTimeout(() => {
                        if (progress < 100) {
                            @this.call('processDataStep');
                        }
                    }, 100);
                });
                
                window.addEventListener('retryProcessing', () => {
                    console.log('Event: retryProcessing');
                    setTimeout(() => {
                        @this.call('processDataStep');
                    }, 500);
                });
                
                window.addEventListener('prepareDataDisplay', () => {
                    console.log('Event: prepareDataDisplay');
                    setTimeout(() => {
                        @this.call('finalizeDataDisplay');
                    }, 1000);
                });
                
                window.addEventListener('loadingCompleted', () => {
                    console.log('Event: loadingCompleted - 100% reached');
                    stopPolling();
                });
            "
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center px-4 py-6 bg-gray-900/95 backdrop-blur-md"
            :class="{ 'hidden': !isVisible }">

            <div class="relative w-full max-w-2xl">
                <!-- Main Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden">
                    <!-- Animated Progress Bar Header -->
                    <div class="relative h-3 bg-gray-200 dark:bg-slate-700 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 opacity-30 animate-pulse"></div>
                        <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 transition-all duration-700 ease-out shadow-lg"
                            :style="`width: ${Math.max(0, Math.min(100, progress))}%`"></div>
                    </div>

                    <!-- Content -->
                    <div class="p-12">
                        <!-- Oracle + UOB Logo -->
                        <div class="flex justify-center items-center space-x-8 mb-10">
                            <!-- Oracle Logo -->
                            <div class="relative">
                                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-2xl"
                                    :class="{ 'animate-pulse': !isComplete }">
                                    <span class="text-white font-bold text-lg">ORACLE</span>
                                </div>
                            </div>

                            <!-- Connection Line -->
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"
                                    :class="{ 'animate-pulse': !isComplete }"></div>
                                <div class="w-16 h-0.5 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                                <div class="w-2 h-2 bg-indigo-500 rounded-full"
                                    :class="{ 'animate-pulse animation-delay-200': !isComplete }"></div>
                            </div>

                            <!-- UOB Logo -->
                            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-2xl p-2">
                                <img src="{{ asset('images/uob-logo-color.png') }}" 
                                    alt="UOB" 
                                    class="w-full h-full object-contain">
                            </div>
                        </div>

                        <!-- Title -->
                        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-6">
                            Loading Oracle Data
                        </h2>

                        <!-- Progress Percentage -->
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center">
                                <span class="text-7xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent"
                                    x-text="`${Math.max(0, Math.min(100, progress))}%`">0%</span>
                                <!-- Completion Check Mark -->
                                <div x-show="isComplete" x-transition class="ml-4">
                                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center animate-scale-up">
                                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Large Progress Bar -->
                        <div class="mb-8">
                            <div class="relative h-8 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                                <!-- Animated Background -->
                                <div class="absolute inset-0 opacity-30">
                                    <div class="h-full w-full bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400"
                                        :class="{ 'animate-pulse': !isComplete }"></div>
                                </div>

                                <!-- Actual Progress -->
                                <div class="relative h-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 rounded-full transition-all duration-700 ease-out flex items-center justify-end pr-4"
                                    :style="`width: ${Math.max(0, Math.min(100, progress))}%`">
                                    <!-- Shimmer Effect -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30 animate-shimmer"
                                        x-show="!isComplete"></div>
                                    <span class="text-white text-sm font-bold drop-shadow"
                                        x-show="progress > 10"
                                        x-text="`${Math.max(0, Math.min(100, progress))}%`"></span>
                                </div>
                            </div>

                            <!-- Progress Milestone Markers -->
                            <div class="flex justify-between mt-2 px-1">
                                <template x-for="milestone in [0, 25, 50, 75, 100]">
                                    <div class="flex flex-col items-center">
                                        <div class="w-0.5 h-3 bg-gray-300 dark:bg-gray-600"></div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="`${milestone}%`"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Status Message -->
                        <div class="text-center mb-8">
                            <p class="text-xl text-gray-700 dark:text-gray-300 font-medium"
                                x-text="message || 'Initializing...'">
                                Initializing...
                            </p>
                        </div>

                        <!-- Progress Steps -->
                        <div class="grid grid-cols-5 gap-4 mb-8">
                            <template x-for="(step, index) in [
                                {at: 20, label: 'Connect', icon: '🔌'},
                                {at: 40, label: 'Query', icon: '🔍'},
                                {at: 60, label: 'Fetch', icon: '📊'},
                                {at: 80, label: 'Process', icon: '⚙️'},
                                {at: 100, label: 'Complete', icon: '✅'}
                            ]" :key="index">
                                <div class="flex flex-col items-center space-y-2">
                                    <div class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-500 transform"
                                        :class="progress >= step.at 
                                         ? 'bg-gradient-to-br from-green-400 to-green-500 text-white shadow-lg scale-110' 
                                         : 'bg-gray-200 dark:bg-slate-700 text-gray-400'">
                                        <span class="text-2xl" x-text="progress >= step.at ? step.icon : '⏳'"></span>
                                    </div>
                                    <span class="text-xs font-medium transition-colors duration-300"
                                        :class="progress >= step.at ? 'text-green-600 dark:text-green-400' : 'text-gray-400'"
                                        x-text="step.label"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Info Section -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        Please wait while we fetch your payment data from Oracle database.
                                    </p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        This may take a few moments for large datasets.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Animated Loading Dots -->
                        <div class="flex justify-center mt-8 space-x-2" x-show="!isComplete">
                            <div class="w-4 h-4 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                            <div class="w-4 h-4 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                            <div class="w-4 h-4 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                        </div>

                        <!-- Success Animation (shows at 100%) -->
                        <div class="flex justify-center mt-8" x-show="isComplete">
                            <div class="w-24 h-24 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center animate-scale-up">
                                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Timeout Warning -->
                        <div x-show="stuckCounter > 15" 
                             x-transition
                             class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200 px-4 py-3 rounded-lg">
                            <p class="text-sm font-medium">Processing is taking longer than expected...</p>
                            <p class="text-xs mt-1">Please wait while we complete the operation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing loading system...');
        
        let processInterval = null;
        let timeoutHandle = null;
        let retryCount = 0;
        const maxRetries = 3;
        
        // Monitor Livewire lifecycle
        document.addEventListener('livewire:init', () => {
            console.log('Livewire initialized');
        });
        
        document.addEventListener('livewire:navigated', () => {
            console.log('Livewire navigated');
        });
        
        // Main loading process handler
        Livewire.on('startLoadingProcess', () => {
            console.log('🚀 Starting loading process...');
            retryCount = 0;
            
            // Clear any existing intervals
            if (processInterval) {
                clearInterval(processInterval);
                processInterval = null;
            }
            
            // Start monitoring and auto-advance
            processInterval = setInterval(() => {
                const component = @this;
                const isProcessing = component.get('isProcessing');
                const progress = component.get('loadingProgress');
                const currentStep = component.get('loadingStep');
                
                console.log(`⚡ Status Check - Processing: ${isProcessing}, Progress: ${progress}%, Step: ${currentStep}`);
                
                if (!isProcessing && progress >= 100) {
                    console.log('✅ Processing complete!');
                    clearInterval(processInterval);
                    processInterval = null;
                    return;
                }
                
                // Auto-advance if still processing
                if (isProcessing && progress < 100) {
                    component.call('processDataStep');
                }
                
            }, 250); // Check every 250ms
            
            // Set overall timeout
            timeoutHandle = setTimeout(() => {
                const finalProgress = @this.get('loadingProgress');
                if (finalProgress < 100) {
                    console.error('⚠️ Loading timeout at', finalProgress, '%');
                    
                    if (retryCount < maxRetries) {
                        retryCount++;
                        console.log(`🔄 Retrying... (attempt ${retryCount}/${maxRetries})`);
                        @this.call('forceContinue');
                    } else {
                        console.error('❌ Max retries reached, stopping');
                        if (processInterval) {
                            clearInterval(processInterval);
                            processInterval = null;
                        }
                        @this.call('handleProcessingError', { message: 'Loading timeout after max retries' });
                    }
                }
            }, 30000); // 30 second timeout
        });
        
        // Handle continue processing
        Livewire.on('continueProcessing', () => {
            console.log('📍 Continue processing signal received');
            setTimeout(() => {
                const progress = @this.get('loadingProgress');
                if (progress < 100) {
                    @this.call('processDataStep');
                }
            }, 100);
        });
        
        // Handle retry
        Livewire.on('retryProcessing', () => {
            console.log('🔁 Retry processing signal received');
            setTimeout(() => {
                @this.call('processDataStep');
            }, 500);
        });
        
        // Handle completion
        Livewire.on('loadingCompleted', () => {
            console.log('🎉 Loading completed successfully!');
            
            // Clear all intervals and timeouts
            if (processInterval) {
                clearInterval(processInterval);
                processInterval = null;
            }
            
            if (timeoutHandle) {
                clearTimeout(timeoutHandle);
                timeoutHandle = null;
            }
        });
        
        // Handle prepare data display
        Livewire.on('prepareDataDisplay', () => {
            console.log('📊 Preparing to display data...');
            setTimeout(() => {
                @this.call('finalizeDataDisplay');
            }, 1000);
        });
        
        // Global debug function
        window.debugLoading = function() {
            const component = @this;
            const data = {
                'Is Loading': component.get('isLoading'),
                'Is Processing': component.get('isProcessing'),
                'Polling Active': component.get('pollingActive'),
                'Progress': component.get('loadingProgress') + '%',
                'Step': component.get('loadingStep'),
                'Message': component.get('loadingMessage'),
                'Data Ready': component.get('dataReady'),
                'Data Count': component.get('oracleData')?.length || 0
            };
            console.table(data);
            return data;
        };
        
        // Auto-debug in development
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            setInterval(() => {
                const component = @this;
                if (component.get('isLoading')) {
                    console.log('🔍 Auto-debug:', window.debugLoading());
                }
            }, 5000);
        }
    });
    </script>

    <!-- Enhanced UOB Banking Styles -->
    <style>
        /* Hide elements with x-cloak until Alpine loads */
        [x-cloak] { 
            display: none !important; 
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(200%);
            }
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }

        @keyframes scale-up {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-up {
            animation: scale-up 0.5s ease-out;
        }

        .animation-delay-200 {
            animation-delay: 200ms;
        }

        /* Custom Scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 4px;
        }

        .scrollbar-track-gray-100::-webkit-scrollbar-track {
            background-color: #f3f4f6;
            border-radius: 4px;
        }

        .dark .scrollbar-thumb-slate-600::-webkit-scrollbar-thumb {
            background-color: #475569;
        }

        .dark .scrollbar-track-slate-800::-webkit-scrollbar-track {
            background-color: #1e293b;
        }

        /* Enhanced Checkbox Styling */
        input[type="checkbox"] {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            animation: none !important;
        }

        input[type="checkbox"]:checked {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e") !important;
            transform: scale(1.05);
        }

        input[type="checkbox"]:hover {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        input[type="checkbox"]:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Enhanced Date Input Styling */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%234f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>');
            background-size: 20px 20px;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            background-color: rgba(79, 70, 229, 0.1);
        }

        /* Dark mode date picker */
        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%23a5b4fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>');
        }

        /* Loading Animation */
        @keyframes pulse-slow {
            0%, 100% {
                opacity: 0.8;
            }
            50% {
                opacity: 0.4;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s infinite;
        }
    </style>
</div>