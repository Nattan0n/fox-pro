<div>
     <div class="py-12"> 
        <div class="flex justify-center mb-3">
               <div class="flex">
                <div class="relative w-48 mb-2 align-middle flex">
                    <input id="due" wire:model="dateCheck" type="date" class="w-full p-2 border border-gray-300 text-xs rounded" required />
                       @error('dateCheck')
                        <span class="absolute text-red-500 text-xs mt-1" style="bottom: -20px;">{{ $message }}</span>
                     @enderror
                </div>
            </div>
            <div class="flex ml-4 content-between w-ful">
                <button type="button"
                        wire:click="getData"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Find
                </button>
                <button type="button"
                        wire:click="synD"
                        class="text-white bg-green-400 hover:bg-green-500 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2  focus:outline-none">
                  Load Data 
                </button>
            </div>
            </div>
           
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (!$linkedData->isEmpty())
                    <button type="button"
                        wire:click="txtFile"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                   Export TXT 
                </button>
                @endif
                    <div class="relative overflow-x-auto">  
                        <div class="text-center"wire:loading>
                            <div role="status">
                                <svg aria-hidden="true" class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                </svg>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div> 
                         @if (!$linkedData->isEmpty())
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                       Reference No.  
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Vendor Name 
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Address 1 
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Address 2 
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Address 3 
                                    </th>
                                </tr>
                            </thead>
                         
                            <tbody>
                            @foreach ($linkedData as $data )
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"> 
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $data->chqnum}}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{  \carbon\Carbon::parse($dateCheck)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <textarea
                                        id="message" 
                                        wire:model.live="ebill_to.name.{{ $data->id }}"
                                        rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here...">{{ $ebill_to['name'][$data->id] }}</textarea>
                                    </td>
                                    <td class="px-6 py-4">
                                         <textarea
                                        id="message" 
                                        wire:model.live="ebill_to.addr1.{{ $data->id }}"
                                        rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here...">{{ $ebill_to['addr1'][$data->id] }}</textarea>
                                    </td>
                                    <td class="px-6 py-4">
                                         <textarea
                                        id="message" 
                                        wire:model.live="ebill_to.addr2.{{ $data->id }}"
                                        rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here...">{{ $ebill_to['addr2'][$data->id] }}</textarea>
                                    </td>
                                    <td class="px-6 py-4">
                                         <textarea
                                        id="message" 
                                        wire:model.live="ebill_to.addr3.{{ $data->id }}"
                                        {{-- wire:change="updateName({{ $data->id }})" --}}
                                        rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here...">{{ $ebill_to['addr3'][$data->id] }}</textarea>
                                    </td>
                                </tr>
                            </tbody> 
                            @endforeach
                        </table> 
                         @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
