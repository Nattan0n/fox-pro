<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-indigo-50">
    <div class="py-8">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white/95 backdrop-blur-xl shadow-2xl border-0 rounded-3xl overflow-hidden">

                {{-- Header --}}
                <div class="bg-gradient-to-r from-purple-700 via-purple-800 to-indigo-900 p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-2">SCB Supplier Payment</h2>
                            <p class="text-purple-200 text-lg">Export Pay.txt สำหรับ SCB Business Net</p>
                        </div>
                        <div class="hidden lg:flex items-center space-x-6">
                            <div class="text-right">
                                <div class="text-purple-300 text-xs font-medium uppercase tracking-widest mb-1">SCB Direct Credit</div>
                                @if($hashModuleReady)
                                    <div class="text-green-300 font-bold text-sm flex items-center justify-end space-x-1">
                                        <span class="w-2 h-2 bg-green-400 rounded-full inline-block"></span>
                                        <span>Hash Module Ready</span>
                                    </div>
                                @else
                                    <div class="text-yellow-300 font-bold text-sm flex items-center justify-end space-x-1">
                                        <span class="w-2 h-2 bg-yellow-400 rounded-full inline-block"></span>
                                        <span>Hash Module Not Set</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-center p-2 bg-white rounded-3xl shadow-lg">
                                <img src="{{ asset('images/SCB-logo.jpg') }}" alt="SCB Logo" class="h-16 w-auto rounded-2xl">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gradient-to-b from-white to-gray-50/50">

                    {{-- Search Form --}}
                    <div class="mb-8">
                        <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100">

                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Search</h3>
                                        <p class="text-gray-500">เลือกบริษัทและวันที่จ่ายเงิน</p>
                                    </div>
                                </div>
                                <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-green-50 rounded-full">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-green-700 text-sm font-medium">Oracle Connected</span>
                                </div>
                            </div>

                            {{-- Form --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                                {{-- Company --}}
                                <div class="space-y-3">
                                    <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700">
                                        <div class="w-5 h-5 bg-gradient-to-br from-purple-500 to-purple-600 rounded flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4z"/>
                                            </svg>
                                        </div>
                                        <span>บริษัท / Factory</span>
                                    </label>
                                    <select wire:model="company"
                                        class="w-full px-5 py-4 border-2 border-gray-200 bg-white text-gray-900 rounded-xl shadow-sm focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 appearance-none cursor-pointer">
                                        @foreach($companyLabels as $code => $label)
                                            <option value="{{ $code }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Date --}}
                                <div class="space-y-3">
                                    <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700">
                                        <div class="w-5 h-5 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z"/>
                                            </svg>
                                        </div>
                                        <span>วันที่จ่ายเงิน (Check Date)</span>
                                    </label>
                                    <input type="date"
                                        lang="en-GB"
                                        wire:model="checkDate"
                                        class="w-full px-5 py-4 border-2 border-gray-200 bg-white text-gray-900 rounded-xl shadow-sm focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300">
                                </div>

                                {{-- Search Button --}}
                                <div class="flex items-end">
                                    <button wire:click="search"
                                        wire:loading.attr="disabled"
                                        class="w-full px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg hover:shadow-purple-500/30 transition-all duration-300 flex items-center justify-center space-x-3 disabled:opacity-60 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="search">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </span>
                                        <span wire:loading wire:target="search">
                                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                        </span>
                                        <span wire:loading.remove wire:target="search">Search Oracle</span>
                                        <span wire:loading wire:target="search">กำลัง Query...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Results --}}
                    @if(!empty($results))
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                        {{-- Table Header + Export Button --}}
                        <div class="flex items-center justify-between p-6 border-b border-gray-100">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">ผลลัพธ์การค้นหา</h3>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    พบ {{ count($results) }} รายการ •
                                    {{ $company }} •
                                    วันที่ {{ date('d/m/Y', strtotime($checkDate)) }} •
                                    รวม {{ number_format(array_sum(array_column($results,'check_amount')),2) }} บาท
                                </p>
                            </div>
                            <button wire:click="exportToFile"
                                wire:loading.attr="disabled"
                                wire:target="exportToFile"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg hover:shadow-purple-500/30 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed space-x-2">
                                <span wire:loading.remove wire:target="exportToFile">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </span>
                                <span wire:loading wire:target="exportToFile">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </span>
                                <span wire:loading.remove wire:target="exportToFile">Export to Text File</span>
                                <span wire:loading wire:target="exportToFile">กำลัง Hash...</span>
                            </button>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-purple-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-purple-800 whitespace-nowrap">#</th>
                                        <th class="px-4 py-3 text-left font-semibold text-purple-800 whitespace-nowrap">CHQ No.</th>
                                        <th class="px-4 py-3 text-left font-semibold text-purple-800 whitespace-nowrap">Vendor Code</th>
                                        <th class="px-4 py-3 text-left font-semibold text-purple-800">Vendor Name</th>
                                        <th class="px-4 py-3 text-left font-semibold text-purple-800 whitespace-nowrap">Bank Account</th>
                                        <th class="px-4 py-3 text-right font-semibold text-purple-800 whitespace-nowrap">Amount (THB)</th>
                                        <th class="px-4 py-3 text-left font-semibold text-purple-800">Description</th>
                                        <th class="px-4 py-3 text-center font-semibold text-purple-800 whitespace-nowrap">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($results as $i => $row)
                                    @php
                                        $hasAccount = trim($row['payee_bank_account']) !== '0' && trim($row['payee_bank_account']) !== '';
                                    @endphp
                                    <tr class="hover:bg-purple-50/50 transition-colors {{ !$hasAccount ? 'bg-red-50/50' : '' }}">
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-mono text-purple-700 whitespace-nowrap font-semibold">
                                            {{ $row['check_number'] }}
                                        </td>
                                        <td class="px-4 py-3 font-mono text-gray-700 whitespace-nowrap">
                                            {{ $row['vendor_code'] }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800 max-w-xs truncate" title="{{ $row['vendor_name'] }}">
                                            {{ $row['vendor_name'] }}
                                        </td>
                                        <td class="px-4 py-3 font-mono text-sm whitespace-nowrap {{ $hasAccount ? 'text-gray-700' : 'text-red-600 font-bold' }}">
                                            {{ $hasAccount ? $row['payee_bank_account'] : 'ไม่มีบัญชี!' }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">
                                            {{ number_format((float)$row['check_amount'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-xs max-w-xs truncate" title="{{ $row['chq_desc'] }}">
                                            {{ $row['chq_desc'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($hasAccount)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Ready
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    No Bank
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-purple-50 border-t-2 border-purple-200">
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">
                                            รวมทั้งหมด {{ count($results) }} รายการ
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-purple-800 text-base">
                                            {{ number_format(array_sum(array_column($results,'check_amount')),2) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Warning for records without bank accounts --}}
                        @php
                            $noAccountCount = count(array_filter($results, fn($r) => trim($r['payee_bank_account']) === '0' || trim($r['payee_bank_account']) === ''));
                        @endphp
                        @if($noAccountCount > 0)
                        <div class="p-4 bg-amber-50 border-t border-amber-200 flex items-center space-x-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-amber-700 text-sm font-medium">
                                มี {{ $noAccountCount }} รายการที่ไม่มีบัญชีธนาคาร — Hash Module จะ Reject รายการเหล่านี้
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>
