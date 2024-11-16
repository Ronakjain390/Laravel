<div>
    <div  x-data="{ open: false }">
        <div class="border-b border-gray-300 pb-4">
            <button wire:click.prevent="addRow" @if ($inputsDisabled == true) disabled="" @endif
            class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem] text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">Add New Row</button>
            <!-- Trigger button -->
            <button @click="open = true" @if ($inputsDisabled == true) disabled="" @endif class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs text-[0.6rem] shadow-lg text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">
            Add From Stock
            </button>
            <input class="text-black text-xs rounded-lg h-6 w-1/3 sm:w-auto" @if ($inputsDisabled == true) disabled="" @endif wire:model="barcode" type="text" placeholder="Scan Barcode">
        </div>
                 
        <div wire:loading    class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <span   class="loading loading-spinner loading-md"></span>
        </div>
        <!-- Modal content -->
        <div  x-show="open" x-cloak class="fixed inset-0 flex items-center sm:justify-center  overflow-x-auto md:ml-64  bg-black bg-opacity-50 backdrop-blur-sm" >
            <div class="bg-white p-3 rounded shadow-md w-auto">
                <!-- Modal header -->
                <div class="">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700" >
                        <!-- Modal header -->
                        <div class="  items-start justify-between p-2 border-b rounded-t dark:border-gray-600">
                            {{-- <h3 class="text-sm text-[#686464] text-gray-900 dark:text-white p-2">
                                Add From Stock
                            </h3> --}}
                            
                            <button type="button" @click="open = false"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-xs w-8 h-9  inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                            <button  wire:click.prevent='addFromStock' type="button" @click="open = false"
                            class="text-white bg-gray-900 hover:bg-orange hover:text-black focus:ring-2 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs px-5 py-2.5 text-center dark:hover:bg-orange dark:focus:ring-blue-800 mr-5 ml-auto">Add</button>
                            
                        </div>
                        @if($inputsDisabled == false)
                        <!-- Modal body -->
                        <div class="p-3 space-y-3 h-80 overflow-y-auto" @click.stop>
                            <div class="flex space-x-4"  @click.stop >
 
                                <div class="flex bg-white dark:bg-gray-900" >
                                    <h5 class="mr-2" style="align-self: center;">Filter: </h5>
                                    <div class="mr-2" x-data="{ search: '', selectedUser: null }" >
                                        <!-- Button to toggle dropdown -->
                                        <button id="dropdownArticleSearch" data-dropdown-toggle="dropdownArticleButton"
                                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                            class="text-black flex w-full border border-gray-900 bg-white hover:bg-orange   rounded-lg text-[0.6rem] px-2 py-1 text-center items-center justify-center  mr-2 mb-2"
                                            type="button">
                                            <span x-cloak>Article<small>
                                                    @if (!empty($Article))
                                                        ({{ $Article }}) @endif
                                                </small></span>
                                            <!-- Button content -->
                                            <svg class="w-2.5 h-2 ml-2.5" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m1 1 4 4 4-4" />
                                            </svg>
              
                                        </button>
              
                                        <!-- Dropdown menu -->
                                        <div x-cloak x-data="{ search: '', filterVariable: null }" id="dropdownArticleButton"
                                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700" wire:ignore.self>
                                            <!-- Search input -->
                                            <div class="p-3">
                                                <label for="input-group-search" class="sr-only">Search</label>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-black font-semibold" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 20 20">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                        </svg>
                                                    </div>
                                                    <input x-model="search" type="text" id="input-group-search"
                                                        class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        placeholder="Search user">
                                                </div>
                                            </div>
                                            <input type="hidden" wire:model="article" style="display: none;">
                                            <!-- Filtered list based on search -->
                                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                                aria-labelledby="dropdownArticleSearch">
                                                <li class="cursor-pointer"
                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                    wire:click.prevent="filterVariable('Article','{{ null }}')">
                                                    <div
                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                        <label
                                                            class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                    </div>
                                                </li>
                                                @foreach ($articles as $atcl)
                                                    <li class="cursor-pointer"
                                                        x-show="search === '' || '{{ strtolower($atcl ?? null) }}'.includes(search.toLowerCase())"
                                                        wire:click.prevent="filterVariable('Article','{{ $atcl }}')">
                                                        <div
                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                            <label
                                                                class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $atcl ?? null }}</label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
              
                                    <div class="mr-2" x-data="{ search: '', selectedUser: null }" >
                                        <!-- Button to toggle dropdown -->
                                        <button id="dropdownCodeSearch" data-dropdown-toggle="dropdownItemCodeSearch"
                                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                            class="text-black flex w-full border border-gray-900 bg-white hover:bg-orange   rounded-lg text-[0.6rem] px-2 py-1 text-center items-center justify-center  mr-2 mb-2"
                                            type="button">
                                            <span x-cloak>Item Code <small>
                                                    @if (!empty($item_code))
                                                        ({{ $item_code }}) @endif
                                                </small></span>
                                            <!-- Button content -->
                                            <svg class="w-2.5 h-2 ml-2.5" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m1 1 4 4 4-4" />
                                            </svg>
              
                                        </button>
              
                                        <!-- Dropdown menu -->
                                        <div  x-cloak x-data="{ search: '', filterVariable: null }" id="dropdownItemCodeSearch"
                                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                            <!-- Search input -->
                                            <div class="p-3">
                                                <label for="input-group-search" class="sr-only">Search</label>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-black font-semibold" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 20 20">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                        </svg>
                                                    </div>
                                                    <input x-model="search" type="text" id="input-group-search"
                                                        class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        placeholder="Search user">
                                                </div>
                                            </div>
                                            <input type="hidden" wire:model="item_code" style="display: none;">
                                            <!-- Filtered list based on search -->
                                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                                aria-labelledby="dropdownCodeSearch">
                                                <li class="cursor-pointer"
                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                    wire:click.prevent="filterVariable('item_code','{{ null }}')">
                                                    <div
                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                        <label
                                                            class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                    </div>
                                                </li>
                                                @foreach ($item_codes as $code)
                                                    <li class="cursor-pointer"
                                                        x-show="search === '' || '{{ strtolower($code ?? null) }}'.includes(search.toLowerCase())"
                                                        wire:click.prevent="filterVariable('item_code','{{ $code }}')">
                                                        <div
                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                            <label
                                                                class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $code ?? null }}</label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
              
                                    <div class="mr-2" x-data="{ search: '', selectedUser: null }" >
                                        <!-- Button to toggle dropdown -->
                                        <button id="dropdownLocationSearch" data-dropdown-toggle="dropdownLocateSearch"
                                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                            class="text-black flex w-full border border-gray-900 bg-white hover:bg-orange   rounded-lg text-[0.6rem] px-2 py-1 text-center items-center justify-center  mr-2 mb-2"
                                            type="button">
                                            <span x-cloak>Location<small>
                                                    @if (!empty($location))
                                                        ({{ $location }}) @endif
                                                </small></span>
                                            <!-- Button content -->
                                            <svg class="w-2.5 h-2 ml-2.5" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m1 1 4 4 4-4" />
                                            </svg>
              
                                        </button>
              
                                        <!-- Dropdown menu -->
                                        <div x-data="{ search: '', filterVariable: null }" id="dropdownLocateSearch" wire:ignore.self
                                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                            <!-- Search input -->
                                            <div class="p-3">
                                                <label for="input-group-search" class="sr-only">Search</label>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-black font-semibold" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 20 20">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                        </svg>
                                                    </div>
                                                    <input x-model="search" type="text" id="input-group-search"
                                                        class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        placeholder="Search user">
                                                </div>
                                            </div>
                                            <input type="hidden" wire:model="location" style="display: none;">
                                            <!-- Filtered list based on search -->
                                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                                aria-labelledby="dropdownLocationSearch">
                                                <li class="cursor-pointer"
                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                    wire:click.prevent="filterVariable('location','{{ null }}')">
                                                    <div
                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                        <label
                                                            class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                    </div>
                                                </li>
                                                @foreach ($locations as $loc)
                                                    <li class="cursor-pointer"
                                                        x-show="search === '' || '{{ strtolower($loc ?? null) }}'.includes(search.toLowerCase())"
                                                        wire:click.prevent="filterVariable('location','{{ $loc }}')">
                                                        <div
                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                            <label
                                                                class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $loc ?? null }}</label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            

                            <table class="w-full text-xs text-left text-black font-semibold ">
                                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 font-semibold whitespace-nowrap">
                                    <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300 ">Action</th>

                                    <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">S. No.</th>
                                    @foreach ($ColumnDisplayNames as $index => $columnName)
                                    <th class="va-b px-2 py-1 text-xs border-2 border-gray-300">
                                            @if ($index >= 3 && $index <= 6)
                                            col_{{ $index - 2 }} @if(!empty($columnName)) ( {{ ucfirst($columnName)}})@endif
                                            @else
                                            {{ !empty($columnName) ? ucfirst($columnName) : "col_" . ($index + 1) }}
                                            @endif
                                        </th>
                                    @endforeach
                                    {{-- <th scope="col" class="va-b px-2 py-2 text-xs border-2 border-gray-300 ">Location</th> --}}
                                   
              
                                </thead>
                                <tbody class="stock-table" wire:ignore.self>
              
                                    @foreach ($products as $key => $product)
                                        @php
                                            $product = (object) $product;
                                        @endphp
                                            @if ($product->qty >'0')
                                        
                                        <tr
                                            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif">
              
                                            <th scope="row"
                                                class="flex items-center whitespace-nowrap px-1  py-2 text-xs border-2 border-gray-300 text-gray-900 dark:text-white">
                                                <div class="pl-0">
                                                    <input 
                                                        wire:model.defer="selectedProductP_ids.{{ $product->id }}"
                                                        wire:click.defer="selectFromStock({{ json_encode($product) }},{{ $product->id }})"
                                                        wire:loading.attr="disabled"
                                                        @click.stop 
                                                        type="checkbox"
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                </div>
                                            </th>
                                            <td class="px-1  py-2 text-xs border-2 border-gray-300">{{ ++$key }}</td>
                                           
                                            @foreach ($product->details as $index => $column)
                                            @php
                                                $column = (object) $column;
                                            @endphp
                                        
                                            @if ($index > 6)
                                                @break
                                            @endif
                                        
                                            <td class="px-1  py-2 text-xs border-2 border-gray-300">
                                                {{ $column->column_value }}
                                            </td>
                                        @endforeach
                                        
                                            <td class="px-1  py-2 text-xs border-2 border-gray-300">{{ $product->item_code }}</td>
                                            <td class="px-1  py-2 text-xs border-2 border-gray-300">{{ ucfirst($product->location) }}</td>
                                            <td class="px-1  py-2 text-xs border-2 border-gray-300 Unit">{{ ucfirst($product->unit) }}</td>
                                            <td class="px-1  py-2 text-xs border-2 border-gray-300">{{ $product->qty }}</td>
                                            <td class="px-1  py-2 text-xs border-2 border-gray-300">{{ $product->rate }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                     
                    </div>
                </div>

            
            </div>
        </div>
    </div>
</div>
