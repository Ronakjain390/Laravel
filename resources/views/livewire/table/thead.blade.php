<div>
    @if (in_array($this->persistedTemplate, ['sent_challan']))
 
    {{-- <th>
        <div class="flex items-center mb-4">
            <input id="selectAll" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" x-model="allSelected" @click="selectAll()">
            <label for="selectAll" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Select All</label>
        </div>
    </th> --}}
    <!-- Other table headers here -->
 
@endif
{{-- <th>
    <div class="flex items-center mb-4">
        <input id="selectAll" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" x-model="allSelected" @click="selectAll()">
        <label for="selectAll" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Select All</label>
    </div>
</th> --}}
<th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm">#</th>
@if (isset($ColumnDisplayNames) && !is_null($ColumnDisplayNames))
{{-- @dump($ColumnDisplayNames) --}}

    @foreach ($ColumnDisplayNames as $columnName)
    {{-- @dd($columnName); --}}
        
        @if ($columnName === 'Invoice No') 
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm  whitespace-nowrap">
                <button id="dropdownSeriesSearchButton" data-dropdown-toggle="dropdownSeriesSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Invoice No
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedSeries: null }" id="dropdownSeriesSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownSeriesSearchButton">
                        <li x-show="search === '' || '{{ strtolower('all series' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('merged_invoice_series', null)" class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                    Series</label>
                            </div>
                        </li> 
                        @foreach ($merged_invoice_series ?? [] as $key => $series)
                        
                        <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('invoice_series','{{ strval($series) }}')"
                            class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                            </div>
                        </li>
                        @endforeach
                        {{-- @foreach (json_decode($invoiceFiltersData)->merged_invoice_series ?? [] as $key => $series)
                       
                            <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                                wire:click="updateVariable('invoice_series','{{ strval($series) }}')"
                                class="cursor-pointer">
                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <label
                                        class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                                </div>
                            </li>
                        @endforeach --}}
                    </ul>
                </div>
                <br>
                @if(isset($invoice_series))
                    <div class="flex w-0 ">
                        <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                            <small>{{ $invoice_series }}</small>
                            <button wire:click="updateVariable('invoice_series', null)"
                            class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                        </div>
                    </div>
                @endif

            </th>
            @elseif ($columnName === 'PO No')
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm  whitespace-nowrap">
                <button id="dropdownSeriesSearchButton" data-dropdown-toggle="dropdownSeriesSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Po No
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedSeries: null }" id="dropdownSeriesSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownSeriesSearchButton">
                        <li x-show="search === '' || '{{ strtolower('all series' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('merged_purchase_order_series', null)" class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                    Series</label>
                            </div>
                        </li>
                        {{-- @dump(json_decode($challanFiltersData)) --}}
                        @if(isset($challanFiltersData) && null !== ($decodedFilters = json_decode($challanFiltersData)))
                        @foreach (json_decode($challanFiltersData)->merged_purchase_order_series ?? [] as $key => $series)
                            {{-- {{dd($series->details[0])}} --}}
                            <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                                wire:click="updateVariable('purchase_order_series','{{ strval($series) }}')"
                                class="cursor-pointer">
                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <label
                                        class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                                </div>
                            </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                <br>
                @if($purchase_order_series)
                <div class="flex w-0 ">

                    <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem] whitespace-nowrap">
                

                    <small >{{ $purchase_order_series }}</small>
                    <button wire:click="updateVariable('purchase_order_series', null)"
                    class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                </div>
                </div>
                @endif

            </th>
          

            @elseif ($columnName === 'Challan No')
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm  whitespace-nowrap">
                <button id="dropdownSeriesSearchButton" data-dropdown-toggle="dropdownSeriesSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Challan No
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedSeries: null }" id="dropdownSeriesSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownSeriesSearchButton">
                        {{-- @dump($merged_challan_series) --}}
                        
                        <li x-show="search === '' || '{{ strtolower('all series' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('merged_challan_series', null)" class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                    Series</label>
                            </div>
                        </li>
                        @foreach ($merged_challan_series ?? [] as $key => $series)
                        
                        <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('challan_series','{{ strval($series) }}')"
                            class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                            </div>
                        </li>
                        @endforeach
                        {{-- @foreach (json_decode($challanFiltersData)->merged_challan_series ?? [] as $key => $series)
                       
                            <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                                wire:click="updateVariable('challan_series','{{ strval($series) }}')"
                                class="cursor-pointer">
                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <label
                                        class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                                </div>
                            </li>
                        @endforeach --}}
                    </ul>
                </div>
                <br>
                @if(isset($challan_series))
                    <div class="flex w-0 ">
                        <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                            <small>{{ $challan_series }}</small>
                            <button wire:click="updateVariable('challan_series', null)"
                            class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                        </div>
                    </div>
                @endif

            </th>
            @elseif ($columnName === 'Goods Receipt No')
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm  whitespace-nowrap">
                <button id="dropdownSeriesSearchButton" data-dropdown-toggle="dropdownSeriesSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Goods Receipt No
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedSeries: null }" id="dropdownSeriesSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownSeriesSearchButton">
                        {{-- @dump($merged_challan_series) --}}
                        
                        <li x-show="search === '' || '{{ strtolower('all series' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('merged_challan_series', null)" class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                    Series</label>
                            </div>
                        </li>
                        @foreach ($merged_goods_series ?? [] as $key => $series)
                        
                        <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('goods_series','{{ strval($series) }}')"
                            class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                            </div>
                        </li>
                        @endforeach
                        {{-- @foreach (json_decode($challanFiltersData)->goodschallan_series ?? [] as $key => $series)
                       
                            <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                                wire:click="updateVariable('goods_series','{{ strval($series) }}')"
                                class="cursor-pointer">
                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <label
                                        class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                                </div>
                            </li>
                        @endforeach --}}
                    </ul>
                </div>
                <br>
                @if(isset($goods_series))
                    <div class="flex w-0 ">
                        <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                            <small>{{ $goods_series }}</small>
                            <button wire:click="updateVariable('goods_series', null)"
                            class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                        </div>
                    </div>
                @endif

            </th>

        @elseif ($columnName === 'Date' || $columnName === 'Sent Date' )
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm">
                <div class="relative">
                    <button id="dropdownDateSearchButton" data-dropdown-toggle="dropdownDateSearch"
                        data-dropdown-placement="bottom"
                        class="inline-flex whitespace-nowrap items-center rounded-lg bg-transparen text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        {{ $columnName === 'Sent Date' ? 'Sent Date' : 'Date' }}    
                        <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    {{-- @if (  (!Route::is(['seller']) )) --}}
                    <!-- Dropdown menu -->
                    <div x-data="{ from: null, to: null, selectedDate: null }" id="dropdownDateSearch"
                        class="z-10 hidden w-40 bg-white rounded-lg shadow w-100 dark:bg-gray-700" wire:ignore.self>
                        <!-- Date input -->
                        <div class="p-3">
                            <label for="date-from"
                                class="text-xs  text-black dark:text-gray-300">From</label>
                            {{-- <input x-model="from" type="date"id="date-from" wire:model="from" wire:change="updateVariable('from', $event.target.value)"
                                class="block w-full p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> --}}
                                
                                <input x-model="from" type="date"id="date-from" wire:model="from" wire:change="updateVariable('from', $event.target.value)"
                                type="date"
                                class="bg-gray-50 p-1 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                                placeholder="Select date">
                            </div>
                        {{-- <input x-model="from" type="date" id="date-from" wire:model="from" wire:change="updateVariable('from', $event.target.value)" class="block p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> --}}
                        <div class="p-3">
                            <label for="date-to"
                                class="text-xs  text-black dark:text-gray-300">To</label>
                            <input x-model="to" type="date" id="date-to" wire:model="to" wire:change="updateVariable('to', $event.target.value)"
                                class="block w-full p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                       
                    </div>
                    {{-- @endif --}}
                    @if(!is_null($from))
                    <div class="flex w-0 whitespace-nowrap">

                        <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                            <small>{{ date('d-m-Y', strtotime($from)) }}</small> <br>
                            <small>{{ date('d-m-Y', strtotime($to)) }}</small>
                
                        <button wire:click="updateVariable('from', null)"
                            class=" text-red-500 hover:text-red-700 cursor-pointer pl-3">X</button>
                    </div>
                    </div>
                    @endif
            </th>
            @elseif ($columnName ===  'Received Date' )
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm">
                <div class="relative">
                    <button id="dropdownDateSearchButton" data-dropdown-toggle="dropdownReceivedDateSearch"
                        data-dropdown-placement="bottom"
                        class="inline-flex whitespace-nowrap items-center rounded-lg bg-transparen text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        Received Date 
                        <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    {{-- @if (  (!Route::is(['seller']) )) --}}
                    <!-- Dropdown menu -->
                    <div x-data="{ recvdfrom: null, recvdto: null, selectedDate: null }" id="dropdownReceivedDateSearch"
                        class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700" wire:ignore.self>
                        <!-- Date input -->
                        <div class="p-3">
                            <label for="recvddate-from"
                                class="text-xs  text-black dark:text-gray-300">From</label>
                            <input x-model="recvdfrom" type="date"id="recvddate-from" wire:model="recvdfrom" wire:change="updateVariable('recvdfrom', $event.target.value)"
                                class="block  p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        {{-- <input x-model="from" type="date" id="recvddate-from" wire:model="from" wire:change="updateVariable('from', $event.target.value)" class="block p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> --}}
                        <div class="p-3">
                            <label for="recvddate-to"
                                class="text-xs  text-black dark:text-gray-300">To</label>
                            <input x-model="recvdto" type="date" id="recvddate-to" wire:model="recvdto" wire:change="updateVariable('recvdto', $event.target.value)"
                                class="block  p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                       
                    </div>
                    {{-- @endif --}}
                    @if(isset($recvdfrom) && isset($recvdto))
                        <div class="flex w-0 whitespace-nowrap">
                            <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                                
                                <small>{{ date('d-m-Y', strtotime($recvdfrom)) }}</small> <br>
                                <small>{{ date('d-m-Y', strtotime($recvdto)) }}</small>
                                <button wire:click="updateVariable('recvdfrom', null)"
                                    class="text-red-500 hover:text-red-700 cursor-pointer pl-3">X</button>
                            </div>
                        </div>
                    @endif
            </th>
            @unless (in_array($this->persistedTemplate,['sent_challan']))
        
        @elseif ($columnName === 'Creator')
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm ">
                <button id="dropdownCreatorSearchButton" data-dropdown-toggle="dropdownCreatorSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Creator
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedSeries: null }" id="dropdownSeriesSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownSeriesSearchButton">
                        <li x-show="search === '' || '{{ strtolower('all series' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('merged_invoice_series', null)" class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                    Series</label>
                            </div>
                        </li>
                        @foreach (json_decode($challanFiltersData)->merged_invoice_series ?? [] as $key => $series)
                            {{-- {{dd($series->details[0])}} --}}
                            <li x-show="search === '' || '{{ strtolower($series ?? null) }}'.includes(search.toLowerCase())"
                                wire:click="updateVariable('invoice_series','{{ strval($series) }}')"
                                class="cursor-pointer">
                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <label
                                        class=" py-1 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $series ?? null }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <br>
               
                
            </th>
            @endunless
        @elseif ($columnName === 'Receiver')
        <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm " x-data="{ search: '', selectedReceiver: null, isOpen: false }">
            <button id="dropdownReceiverSearchButton" data-dropdown-toggle="dropdownReceiverSearch"
                data-dropdown-placement="bottom"
                class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button" @click="isOpen = !isOpen">
                Receiver
                <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>
                <!-- Dropdown menu -->
                 <!-- Dropdown menu -->
                    <div id="dropdownReceiverSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700" @click.away="isOpen = false; search = ''">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                    aria-labelledby="dropdownReceiverSearchButton">
                        <li x-show="search === '' || '{{ strtolower('all receiver' ?? null) }}'.includes(search.toLowerCase())"
                        wire:click="updateVariable('receiver_id', null); isOpen = false" class="cursor-pointer">
                        <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                            <label class="py-2 ml-2 text-xs text-black cursor-pointer rounded dark:text-gray-300">All Receiver</label>
                        </div>
                    </li>
                    {{-- @dump($receiver_ids) --}}
                    @foreach ($receiver_ids ?? [] as $key => $receiver)
                    <li x-show="search === '' || '{{ strtolower($receiver ?? '') }}'.includes(search.toLowerCase())"
                        wire:click="updateVariable('receiver_id',{{ $key }}); isOpen = false" class="cursor-pointer">
                        <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                            <label
                                class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $receiver ?? '' }}</label>
                        </div>
                    </li>
                @endforeach
                    </ul>
                </div>
                <br> 
                @if(!empty($receiver_id) && isset($receiver_id))
                <div class="flex  whitespace-nowrap">
                    @php
                    $users = App\Models\Challan::where('receiver_id', $receiver_id)->first();
                    // dd($users);
                @endphp
                 
                 <div class="flex w-0 ">

                    <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">

                    <small class="font-normal text-[0.6rem]">
                        {{ $users->receiver ?? ''}}</small>
                    <button wire:click="updateVariable('receiver_id', null)"
                        class="text-red-500 hover:text-red-700 cursor-pointer pl-3">X</button>
                </div>
                </div>
                @endif
                

            </th>
            @elseif ($columnName === 'Buyer')
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm ">
                <button id="dropdownBuyerSearchButton" data-dropdown-toggle="dropdownBuyerSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Buyer
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedBuyer: null }" id="dropdownBuyerSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownBuyerSearchButton">
                        <li x-show="search === '' || '{{ strtolower('all buyer' ?? null) }}'.includes(search.toLowerCase())"
                        wire:click="updateVariable('buyer_id', null); search = ''" class="cursor-pointer">
                        <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                            <label class="py-2 ml-2 text-xs text-black cursor-pointer rounded dark:text-gray-300">All Buyer</label>
                        </div>
                    </li>
                    
                    @foreach ($buyer_ids ?? [] as $key => $buyer)
                    <li x-show="search === '' || '{{ strtolower($buyer ?? '') }}'.includes(search.toLowerCase())"
                        wire:click="updateVariable('buyer_id',{{ $key }}); isOpen = false" class="cursor-pointer">
                        <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                            <label
                                class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $buyer ?? '' }}</label>
                        </div>
                    </li>
                @endforeach
                    </ul>
                </div>
                <br> 
                @if(!is_null($buyer_id))
                <div class="flex  whitespace-nowrap">
                    @php
                    $users = App\Models\Invoice::where('buyer_id', $buyer_id)->first();
                    // dd($users);
                @endphp
                 <div class="flex w-0 ">

                    <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                    <small class="font-normal text-[0.6rem]">
                        {{ $users->buyer ?? ''}}</small>
                    <button wire:click="updateVariable('buyer_id', null)"
                        class=" text-red-500 hover:text-red-700 cursor-pointer pl-3">X</button>
                </div>
                </div>
                @endif
                

            </th>
            
            @elseif ($columnName === 'Tags')
            {{-- @dd($allTagss) --}}
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm">
                <button id="dropdownTagsSearchButton" data-dropdown-toggle="dropdownTagsSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Tags
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedTags: null }" id="dropdownTagsSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring
                                -blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownTagsSearchButton">
                        <li x-show="search === '' || '{{ strtolower('all tags' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('tags', null)" class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label class=" py-2 ml-2 text-xs text-black cursor-pointer rounded dark:text-gray-300">All Tags</label>
                            </div>
                        </li>
                        @foreach ($allTagss ?? [] as $key => $tag)
                        
                            <li x-show="search === '' || '{{ strtolower($tag ?? '') }}'.includes(search.toLowerCase())"
                                wire:click="updateVariable('tags','{{ strval($tag->id) }}')"
                                class="cursor-pointer">
                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <label class=" py-1 ml-2 text-xs text-black cursor-pointer rounded dark:text-gray-300">{{ $tag->name ?? '' }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @php
                $tagss = App\Models\TagsTable::where('id', $tags)->first();
                @endphp
                <br>
                @if($tagss)
                <div class="flex w-0 whitespace-nowrap">
                    <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                        <small class="font-normal text-[0.6rem]">{{ $tagss->name ?? null }}</small>
                        <button wire:click="updateVariable('tags', null)"
                            class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                    </div>
                </div>
                @endif
            </th>
            
        @elseif ($columnName === 'State')
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm">
                <button id="dropdownStateSearchButton" data-dropdown-toggle="dropdownStateSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    State
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedState: null }" id="dropdownStateSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownStateSearchButton">
                        {{-- {{dd(json_decode($challanFiltersData)->receiver_id)}} --}}
                        <li x-show="search === '' || '{{ strtolower('all state' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('state', null)" class="cursor-pointer">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                    State</label>
                            </div>
                        </li>
                        {{-- {{dd(!is_null($challanFiltersData) , isset(json_decode($challanFiltersData)->state) ,count(json_decode($challanFiltersData)->state) > 0)}} --}}
                        @if(isset($challanFiltersData) && !is_null($challanFiltersData) && isset(json_decode($challanFiltersData)->state) && count(json_decode($challanFiltersData)->state) > 0)

                            @foreach (json_decode($challanFiltersData)->state as $key => $stat)
                                {{-- {{dd($stat)}} --}}
                                <li x-show="search === '' || '{{ strtolower($stat ?? null) }}'.includes(search.toLowerCase())"
                                    wire:click="updateVariable('state', '{{ strval($stat) }}')"
                                    class="cursor-pointer">
                                    <div
                                        class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label
                                            class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $stat ?? null }}</label>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <br>
                {{-- <small class="font-normal text-[0.6rem]">{{ $state }}</small> --}}
                @if($state)
                <div class="flex w-0 ">
                    <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">

                    <small class="font-normal text-[0.6rem]">{{ $state }}</small>
                    <button wire:click="updateVariable('state', null)"
                    class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                </div>
                </div>
                @endif
            </th>
        @elseif ($columnName === 'Status' || $columnName === 'Sent Status')
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm whitespace-nowrap">
                <button id="dropdownStatusSearchButton" data-dropdown-toggle="dropdownStatusSearch"
                    data-dropdown-placement="bottom"
                    class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    {{ $columnName === 'Sent Status' ? 'Sent Status' : 'Status' }}
                    <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                @if(!in_array($this->persistedTemplate, ['sent_invoice']))
                <!-- Dropdown menu -->
                <div x-data="{ search: '', selectedStatus: null }" id="dropdownStatusSearch"
                    class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-group-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-group-search"
                                class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownStatusSearchButton">
                        <li x-show="search === '' || '{{ strtolower('all status' ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="updateVariable('status', null)" class="cursor-pointer">
                            <div class="flex items
                            -center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label
                                    class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                    Status</label>
                            </div>
                        </li>
                        {{-- @foreach (json_decode($challanFiltersData)->status ?? [] as $key => $status) --}}
                            @php
                                $statuses = ['Sent' => 'sent', 'Accepted' => 'accept', 'Not Sent' => 'draft', 'Rejected' => 'reject'];
                            @endphp

                            @foreach ($statuses as $displayStatus => $backendStatus)
                                <li x-show="search === '' || '{{ strtolower($displayStatus) }}'.includes(search.toLowerCase())"
                                    wire:click="updateVariable('status', '{{ strval($backendStatus) }}')"
                                    class="cursor-pointer">
                                    <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label class="py-2 ml-2 text-xs text-black cursor-pointer rounded dark:text-gray-300">{{ $displayStatus }}</label>
                                    </div>
                                </li>
                            @endforeach
                        {{-- @endforeach --}}
                    </ul>
                </div>

                <br>
                @if($status)
                <div class="flex w-0 ">
                    <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem] whitespace-nowrap">

                    <small class="font-normal text-[0.6rem]">
                        @if($status == 'draft')
                        Not Sent
                        @else
                            {{ $status }}
                        @endif</small>
                    <button wire:click="updateVariable('status', null)"
                    class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                </div>
                </div>
                @endif
                @endif
                {{-- @if($status)
                <div class="flex w-0 ">
                    <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">

                    <small class="font-normal text-[0.6rem]">{{ $status }}</small>
                    <button wire:click="updateVariable('status', null)"
                    class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                </div>
                </div>
                @endif --}}
            </th>
            @if ($this->persistedTemplate == 'sent_challan')
            @elseif ($columnName === 'Creator')
                <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm ">
                    <button id="dropdownCreatorSearchButton" data-dropdown-toggle="dropdownCreatorSearch"
                        data-dropdown-placement="bottom"
                        class="inline-flex items-center rounded-lg bg-transparent text-center text-sm focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        Creator
                        <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <!-- Dropdown menu -->
                    <div x-data="{ search: '', selectedCreator: null }" id="dropdownCreatorSearch"
                        class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                        <!-- Search input -->
                        <div class="p-3">
                            <label for="input-group-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input x-model="search" type="text" id="input-group-search"
                                    class="block  p-2 pl-10 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search user">
                            </div>
                        </div>
                        <!-- Filtered list based on search -->
                        <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                            aria-labelledby="dropdownCreatorSearchButton">
                            <li x-show="search === '' || '{{ strtolower('all sender' ?? null) }}'.includes(search.toLowerCase())"
                                wire:click="updateVariable('sender_id', null)" class="cursor-pointer">
                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <label
                                        class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">All
                                        Creator</label>
                                </div>
                            </li>
                            {{-- @foreach (json_decode($challanFiltersData)->receiver_id  ?? [] as $key => $receiver) --}}

                            @foreach (json_decode($challanFiltersData)->sender_id ?? [] as $key => $sender)
                                <li x-show="search === '' || '{{ strtolower($sender ?? null) }}'.includes(search.toLowerCase())"
                                    wire:click="updateVariable('sender_id',{{ $key }})"
                                    class="cursor-pointer">
                                    <div
                                        class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label
                                            class=" py-2 ml-2 text-xs  text-black cursor-pointer rounded dark:text-gray-300">{{ $sender ?? null }}</label>

                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <br>
                    <div class="flex w-0 ">

                        <div class="border border-gray-600 font-normal rounded-md px-1 text-[0.6rem]">
                            <small>{{ $sender ?? '' }}</small>
                            <button wire:click="updateVariable('sender_id', null)"
                                class="text-red-500 hover:text-red-700 cursor-pointer pr-3">X</button>
                        </div>
                    </div>

                </th>
            @endif
        @else
            <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm capitalize whitespace-nowrap">{{ $columnName }}</th>
        @endif
    @endforeach
@endif
{{-- @dd($this->persistedTemplate); --}}

@unless (in_array($this->persistedTemplate, ['detailed_sent_invoice', 'detailed_all_buyers', 'detailed_purchase_order_buyer', 'detailed_received_challan','detailed_received_return_challan', 'detailed_purchase_order', 'detailed_sent_challan', 'detailed_sent_return_challan', 'sent-receipt-detailed-view']))
    <th scope="col" class="font-semibold va-b px-1  w-0 py-2 text-sm capitalize">Actions</th>
@endunless


<script>
    window.addEventListener('hide-dropdown', event => {
    document.getElementById('dropdownDateSearch').classList.add('hidden');
})
// Event listener to detect tab change and reinitialize dropdown
document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', (message, component) => {
            // initDropdown();
            console.log('processed');
            initFlowbite();
        });
    });

    document.addEventListener('wire:navigated', () => {
        initFlowbite();
        console.log('navigated');
    });
</script>
</div>
