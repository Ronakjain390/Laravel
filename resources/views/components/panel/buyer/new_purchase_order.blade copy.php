<div class="rounded-lg dark:border-gray-700 mt-4">

        <!-- First Row - Responsive 2 Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <!-- Column 1 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        Buyer
                    </h5>

                    <div class="w-full text-gray-900 dark:text-white">

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-10">
                                <div class="grid gap-2">
                                    {{-- <div class="flex flex-row">
                                        <dt class="mb-1 text-black md:text-sm dark:text-gray-400 w-1/4 font-semibold">Date:</dt>
                                        <dd class="pl-2 text-xs  text-black capitalize">
                                            {{ date('j F , Y') }}
                                            <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                                        </dd>
                                    </div> --}}
                                    {{-- @php
                                        $selectedUser = json_decode($this->selectedUser);
                                        @dump($selectedUser['purchase_order_series'])
                                    @endphp --}}
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">PO No:</dt>
                                        <dd
                                            class="pl-2 text-xs  capitalize @if (isset($selectedUser['purchase_order_series'])) @if ($selectedUser['purchase_order_series'] == 'Not Assigned') text-red-700 @else text-black @endif
                                        @else
                                        text-black @endif">

                                            {{ $selectedUser['purchase_order_series'] ?? null }} - {{ $selectedUser['seriesNumber'] ?? null }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Name : </dt>
                                        <dd class="pl-2 text-xs  text-black capitalize">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->name ?? null }}</dd>
                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Address : </dt>
                                        <dd class="pl-2 text-xs  text-black capitalize">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->address ?? null }}</dd>
                                    </div>


                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Email : </dt>
                                        <dd class="pl-2 text-xs  text-black capitalize">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->email ?? null }}</dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Phone : </dt>
                                        <dd class="pl-2 text-xs  text-black capitalize">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->phone ?? null }}</dd>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>


                </div>
            </div>
            <!-- Column 2 -->

            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                    {{-- <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        Seller
                    </h5> --}}

                    <div x-data="{ search: '', selectedUserDetails: null }">
                        <div class="relative" >
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownDetailSearchButton" data-dropdown-toggle="dropdownDetailSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class="flex w-full bg-white border border-gray-400 text-xs text-black hover:bg-orange focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg   px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                            type="button">
                            @if ($buyerName == '')
                            <span x-cloak>Select Seller</span>
                        @else
                        <span> {{ strToUpper($buyerName) }}</span>
                        @endif
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <label for="small_outlined"
                        class="absolute text-sm rounded-2xl ml-3 font-bold text-black dark:text-gray-400 duration-300 transform -translate-y-3 scale-75 top-1 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-1 peer-focus:scale-75 peer-focus:-translate-y-3 start-1 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            SHIP TO
                        </label>
                        </div>
                        <!-- Dropdown menu -->
                        <div x-data="{ search: '', selectedUserDetails: null }" id="dropdownDetailSearch"
                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                            <!-- Search input -->
                            <div class="p-3">
                                <label for="input-address-search" class="sr-only">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                    </div>
                                    <input x-model="search" type="text" id="input-address-search"
                                        class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search user">
                                </div>
                            </div>
                            <!-- Filtered list based on search -->
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDetailSearchButton">
                                @if(isset($Sellers))
                                @foreach ($Sellers as $user)
                                {{-- @dump($user) --}}
                                <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($user->seller_name ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="selectUser('{{ $user->series_number->series_number ?? 'Not Assigned' }}', '{{ $user->details[0]->address ?? null }}', '{{ $user->user->email ?? null }}', '{{ $user->details[0]->phone ?? null }}', '{{ $user->details[0]->gst_number ?? null }}','{{ $user->seller_name ?? 'Select Seller' }}', '{{ json_encode($user ?? null) }}')"
                                            x-on:click="selectedUser = '{{ $user->seller_name ?? 'Select Receiver' }}';  selectedUserState = '{{ $user->details[0]->state ?? '' }}'; console.log('{{ $user->seller_name ?? 'Receiver Name is Empty' }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-1.5 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $user->seller_name ?? null }}</label>
                                            </div>
                                        </li>
                                @endforeach
                                @endif
                            </ul>

                        </div>
                    </div>
                    <div class="grid gap-2">
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Phone:</dt>
                            <dd class="pl-2 text-xs text-black capitalize">
                                {{ $selectedUser['phone'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Email:</dt>
                            <dd class="pl-2 text-xs text-black capitalize">
                                {{ $selectedUser['email'] ?? null }}</dd>
                        </div>
                        @if(is_array($selectedUser) && !empty($selectedUser['gst']))
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">GST:</dt>
                            <dd class="pl-2 text-xs text-black capitalize">
                                {{ $selectedUser['gst'] }}</dd>
                        </div>
                    @endif
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Address:</dt>
                            <dd class="pl-2 text-xs text-black capitalize">
                                {{ $selectedUser['address'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Date</dt>
                            @if ($selectedUser == !null)
                            <input wire:model.defer="createChallanRequest.order_date"
                            type="date"
                            class="bg-gray-50 p-1 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                            placeholder="Select date">
                          @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div wire:loading    class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <span   class="loading loading-spinner loading-md"></span>
        </div>
        @if($inputsDisabled == false)
        <div  x-data="{ open: false }">
            <div class="border-b border-gray-300 pb-4">
                <button wire:click.prevent="addRow" @if ($inputsDisabled == true) disabled="" @endif
                class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem] text-black hover:bg-yellow-700"
                style="background-color: #e5f811;">Add New Row</button>
                <!-- Trigger button -->
                @if($products)
                    <button @click="open = true" type="button" @if ($inputsDisabled == true) disabled="" @endif class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem]  text-black hover:bg-yellow-700"
                    style="background-color: #e5f811;">
                    Add From Stock
                    </button>
                @endif
                {{-- @if(auth()->user()->barcode)
                <input class="text-black text-xs rounded-lg h-6 w-1/3 sm:w-auto" @if ($inputsDisabled == true) disabled="" @endif wire:model="barcode" type="text" placeholder="Scan Barcode">
                @endif --}}
            </div>

            <!-- Modal content -->
            <div  x-show="open" x-cloak class="fixed inset-0 flex items-center sm:justify-center  overflow-x-auto md:ml-64 bg-opacity-50 backdrop-blur-sm" >
                <div class="bg-white p-3 rounded shadow-md max-w-5xl">
                    <!-- Modal header -->
                    <div class="">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="items-start justify-between p-2 border-b rounded-t dark:border-gray-600">
                                <button type="button" @click="open = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-xs w-8 h-9 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"  >
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <button type="button" @click="open = false; sendData()" class="text-white bg-gray-900 hover:bg-orange hover:text-black focus:ring-2 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs px-5 py-2.5 text-center dark:hover:bg-orange dark:focus:ring-blue-800 mr-5 ml-auto">Add</button>
                            </div>
                            @if($inputsDisabled == false)
                            <!-- Modal body -->
                            <div class="p-2 space-y-6 h-80 overflow-y-auto" @click.stop>
                                <div class="flex space-x-4"  @click.stop >

                                    <div class="flex bg-white dark:bg-gray-900" wire:ignore.self>
                                        <h5 class="mr-2" style="align-self: center;">Filter: </h5>
                                        {{-- <button wire:click="updateVariable('Article', null)" class="p-2 text-xs text-red-600 hover:text-red-800">
                                            Remove filter
                                        </button> --}}
                                        <div class="mr-2" x-data="{ search: '', selectedUser: null }"    wire:ignore.self>
                                            <!-- Button to toggle dropdown -->
                                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                                <button id="dropdownArticleSearch" data-dropdown-toggle="dropdownArticleButton"
                                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                                    class="text-black flex w-full   bg-white rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2 "
                                                    type="button">
                                                    <span x-cloak>Article

                                                    </span>
                                                    @if (empty($Article))
                                                        <!-- Button content -->
                                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 10 6">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                                        </svg>
                                                    @endif
                                                    <small class="flex text-xs ml-1 font-bold text-red-500">
                                                        @if (!empty($Article))
                                                            ({{ $Article }})
                                                            <span wire:click="updateVariable('Article', null)"  class="cursor-pointer ml-2">X</span>
                                                        @endif
                                                    </small>
                                                </button>
                                            </div>

                                            <!-- Dropdown menu -->
                                            <div x-data="{ search: '', updateVariable: null }" id="dropdownArticleButton" wire:ignore.self
                                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                                <!-- Search input -->
                                                <div class="p-3">
                                                    <label for="input-group-search" class="sr-only">Search</label>
                                                    <div class="relative text-xs">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
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
                                                    <li class="cursor-pointer "
                                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                        wire:click.prevent="filterVariable('Article','{{ null }}')">
                                                        <div
                                                            class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                            <label
                                                                class="w-full py-2 ml-2 text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                        </div>
                                                    </li>
                                                    @foreach ($articles as $atcl)
                                                        <li class="cursor-pointer"
                                                            x-show="search === '' || '{{ strtolower($atcl ?? null) }}'.includes(search.toLowerCase())"
                                                            wire:click.prevent="filterVariable('Article','{{ $atcl }}')">
                                                            <div
                                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                <label
                                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $atcl ?? null }}</label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="mr-2" x-data="{ search: '', selectedUser: null, dropdownOpen: false }" wire:ignore.self>
                                            <!-- Button to toggle dropdown -->
                                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                                <button @click="dropdownOpen = !dropdownOpen" id="dropdownCodeSearch" data-dropdown-toggle="dropdownItemCodeSearch"
                                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                                    class="text-black flex w-  whitespace-nowrap bg-white   focus:outline-none   rounded-lg  px-2 py-1 text-center items-center justify-center  mr-2"
                                                    type="button">
                                                    <span x-cloak>Item Code <small>

                                                        </small></span>
                                                    @if (empty($item_code))
                                                        <!-- Button content -->
                                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 10 6">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                                        </svg>
                                                    @endif
                                                    <small class="flex text-xs ml-1 font-bold text-red-500">
                                                        @if (!empty($item_code))
                                                            ({{ $item_code }})
                                                            <span wire:click="updateVariable('item_code', null)"  class="cursor-pointer ml-2">X</span>
                                                        @endif
                                                    </small>
                                                </button>
                                            </div>

                                            <!-- Dropdown menu -->
                                            <div x-data="{ search: '', updateVariable: null }" id="dropdownItemCodeSearch" wire:ignore.self
                                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                                <!-- Search input -->
                                                <div class="p-3">
                                                    <label for="input-group-search" class="sr-only">Search</label>
                                                    <div class="relative text-xs">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
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
                                                    <li class="cursor-pointer "
                                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                        @click.prevent="dropdownOpen = false; $wire.filterVariable('item_code','{{ null }}')">
                                                        <div
                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                            <label
                                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                        </div>
                                                    </li>
                                                    @foreach ($item_codes as $code)
                                                        <li class="cursor-pointer"
                                                            x-show="search === '' || '{{ strtolower($code ?? null) }}'.includes(search.toLowerCase())"
                                                            @click.prevent="dropdownOpen = false; $wire.filterVariable('item_code','{{ $code }}')">
                                                            <div
                                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                <label
                                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $code ?? null }}</label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="mr-2" x-data="{ search: '', selectedUser: null, dropdownOpen: false }" wire:ignore.self>
                                            <!-- Button to toggle dropdown -->
                                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                                <button  id="dropdownCategorySearch" data-dropdown-toggle="dropdownCategoryButton"
                                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                                    class="text-black flex w-  whitespace-nowrap bg-white   focus:outline-none   rounded-lg  px-2 py-1 text-center items-center justify-center  mr-2"
                                                    type="button">
                                                    <span x-cloak>Category <small>

                                                        </small></span>
                                                    @if (empty($category))
                                                        <!-- Button content -->
                                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 10 6">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                                        </svg>
                                                    @endif
                                                    <small class="flex text-xs ml-1 font-bold text-red-500">
                                                        @if (!empty($category))
                                                            ({{ $category }})
                                                            <span wire:click="updateVariable('category', null)"  class="cursor-pointer ml-2">X</span>
                                                        @endif
                                                    </small>
                                                </button>
                                            </div>

                                            <!-- Dropdown menu -->
                                            <div x-data="{ search: '', updateVariable: null }" id="dropdownCategoryButton" wire:ignore.self
                                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                            <!-- Search input -->
                                            <div class="p-3">
                                                <label for="input-group-search" class="sr-only">Search</label>
                                                <div class="relative text-xs">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
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
                                                aria-labelledby="dropdownCategorySearch">
                                                <li class="cursor-pointer "
                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                    wire:click.prevent="updateVariable('category','{{ null }}')">
                                                    <div
                                                        class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                        <label
                                                            class="w-full py-2 ml-2 text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                    </div>
                                                </li>
                                                @foreach ($categories as $cat)
                                                    <li class="cursor-pointer"
                                                        x-show="search === '' || '{{ strtolower($cat ?? null) }}'.includes(search.toLowerCase())"
                                                        wire:click.prevent="updateVariable('category','{{ $cat }}')">
                                                        <div
                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                            <label
                                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $cat ?? null }}</label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        </div>

                                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                                <!-- Button to toggle dropdown -->
                                                <button id="dropdownLocationSearch" data-dropdown-toggle="dropdownLocateSearch"
                                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                                    class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
                                                    type="button">
                                                    <span x-cloak>Location </span>
                                                        @if (empty($location))
                                                    <!-- Button content -->
                                                    <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                                    </svg>
                                                    @endif


                                                <small class="flex text-xs ml-1 font-bold text-red-500">
                                                    @if (!empty($location))
                                                        ({{ $location }})
                                                        <span wire:click="updateVariable('location', null)"  class="cursor-pointer ml-2">X</span>
                                                    @endif
                                                </small>
                                                </button>
                                            </div>
                                            <!-- Dropdown menu -->
                                            <div x-data="{ search: '', updateVariable: null }" id="dropdownLocateSearch" wire:ignore.self
                                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                                <!-- Search input -->
                                                <div class="p-3">
                                                    <label for="input-group-search" class="sr-only">Search</label>
                                                    <div class="relative text-xs">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
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
                                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                        </div>
                                                    </li>
                                                    @foreach ($locations as $loc)
                                                        <li class="cursor-pointer"
                                                            x-show="search === '' || '{{ strtolower($loc ?? null) }}'.includes(search.toLowerCase())"
                                                            wire:click.prevent="filterVariable('location','{{ $loc }}')">
                                                            <div
                                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                <label
                                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $loc ?? null }}</label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                                <!-- Button to toggle dropdown -->
                                                <button id="dropdownWarehouseSearch" data-dropdown-toggle="dropdownWareSearch"
                                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                                    class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
                                                    type="button">
                                                    <span x-cloak>Warehouse </span>
                                                    @if (empty($warehouse))
                                                    <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                                    </svg>
                                                    @endif
                                                    {{-- onclick="window.location.reload();" --}}
                                                    <small class="flex text-xs font-bold text-red-500 ml-1">
                                                        @if (!empty($warehouse))
                                                            ({{ $warehouse }})
                                                            <span wire:click="updateVariable('warehouse', null)"  class="cursor-pointer ml-2">X</span>
                                                        @endif
                                                    </small>
                                                </button>
                                            </div>
                                            <!-- Dropdown menu -->
                                            <div x-data="{ search: '', updateVariable: null }" id="dropdownWareSearch" wire:ignore.self
                                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                            <!-- Search input -->
                                            <div class="p-3">
                                                <label for="input-group-search" class="sr-only">Search</label>
                                                <div class="relative text-xs">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
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
                                            <input type="hidden" wire:model="warehouse" style="display: none;">
                                            <!-- Filtered list based on search -->
                                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                                aria-labelledby="dropdownLocationSearch" >
                                                <li class="cursor-pointer"
                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                    wire:click.prevent="updateVariable('warehouse','{{ null }}')">
                                                    <div
                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                        <label
                                                            class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                    </div>
                                                </li>
                                                @foreach ($warehouses as $ware)
                                                    <li class="cursor-pointer"
                                                        x-show="search === '' || '{{ strtolower($ware ?? null) }}'.includes(search.toLowerCase())"
                                                        wire:click.prevent="updateVariable('warehouse','{{ $ware }}')">
                                                        <div
                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                            <label
                                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $ware ?? null }}</label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        </div>

                                    </div>
                                </div>
                                <table class="w-full text-xs text-left text-black dark:text-gray-400 overflow-x-auto">
                                    <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Action</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">S. No.</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Article</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">HSN</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Details</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Item Code</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Category</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Location</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Warehouse</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Unit</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Qty</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Rate</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Tax</th>

                                        {{-- @foreach ($ColumnDisplayNames as $index => $columnName)
                                        @if (!empty($columnName))
                                        <th class="va-b px-2 py-2 text-xs border border-gray-300 whitespace-nowrap">
                                            @if ($index >= 3 && $index <= 6)
                                            {{ $index - 2 }} ({{ ucfirst($columnName)}})
                                            @else
                                            {{ ucfirst($columnName) }}
                                            @endif
                                        </th>
                                        @endif
                                        @endforeach --}}
                                    </thead>

                                    <tbody class="stock-table">
                                        @foreach ($stock as $key => $product)
                                        {{-- @php
                                        $stock = (object) $stock;
                                        @endphp --}}
                                        @if ($product->qty > '0')
                                        <tr class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap">
                                            <th scope="row" class="flex items-center whitespace-nowrap px-1 py-1 text-xs border-2 border-gray-300 text-gray-900 dark:text-white">
                                                <div class="pl-0 border border-gray-400 ">
                                                    <input type="checkbox"
                                                    class="product-checkbox w-4 h-4 border ml-2  text-purple-600 bg-gray-100 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                    onclick="window.selectProduct({{ json_encode($product) }}, '{{ $product->id }}')"
                                                    data-product-id="{{ $product->id }}">
                                                </div>
                                            </th>
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ ++$key }}</td>
                                            @foreach ($product->details as $index => $column)
                                            @if (!empty($ColumnDisplayNames[$index]))
                                            @php
                                            $column = (object) $column;
                                            @endphp
                                            @if ($index > 2)
                                            @break
                                            @endif
                                            <td class="px-1 py-2 text-xs border-2 border-gray-300">
                                                {{ $column->column_value }}
                                            </td>
                                            @endif
                                            @endforeach
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ $product->item_code }}</td>
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ $product->category }}</td>
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ $product->location }}</td>
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ $product->warehouse }}</td>
                                            <td class="px-2 py -1 text-xs border-2 border-gray-300 Unit">{{ $product->unit }}</td>
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ $product->qty }}</td>
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ $product->rate }}</td>
                                            <td class="px-2 py-1 text-xs border-2 border-gray-300">{{ $product->tax }}</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                        </table>
                                    {{ $stock->links() }}
                            </div>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="bg-[#ebebeb] py-2 ">
            <div class="flex flex-col">
                <div class="overflow-x-auto ">
                    <div class="min-w-full py-2 align-middle inline-block">
                        <div class="overflow-hidden rounded-lg bg-[#ebebeb] p-3  max-w-full" wire:ignore.self>

                            <table class="min-w-full w-full">
                    <thead>
                        <tr class="border-b border-gray-300 text-left">
                            <th class="px-2 text-xs text-black">S.No.</th>
                            @foreach ($panelColumnDisplayNames as $columnName)
                            @if (!empty($columnName))
                                <th class="px-2 text-xs text-black">{{ $columnName }}
                                    @if ($columnName == 'Article')
                                        <span class="text-red-500">*</span>
                                    @endif
                                </th>
                            @endif
                            @endforeach
                            <th class="px-2 text-xs text-black">Unit</th>


                            <th class="px-2 text-xs text-black">Rate</th>
                            <th class="px-2 text-xs text-black">Qty
                                <span class="text-red-500">*</span>
                            </th>
                            <th class="px-2 text-xs text-black">Tax(%)</th>
                            <th class="px-2 text-xs text-black">Total Amount</th>
                            <th class="px-2 text-xs text-black"></th>
                        </tr>
                        @php
                        $nonEmptyColumnCount = 0;
                        foreach ($panelColumnDisplayNames as $columnName) {
                            if (!empty($columnName)) {
                                $nonEmptyColumnCount++;
                            }
                        }
                    @endphp
                    <tr>
                        <td colspan="{{ $nonEmptyColumnCount + 2 }}"></td>
                        <td colspan="2">
                            <div class="flex items-center">
                                <label for="calculateTax" class=" ml-2 text-[0.6rem] font-semibold text-black dark:text-gray-300">Without Tax</label>
                                <input wire:model.defer="calculateTax" id="calculateTax" type="checkbox" @if ($inputsDisabled == true) disabled="" @endif
                                    class="w-4 h-4 ml-2 text-blue-600  bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($createChallanRequest['order_details'] as $index => $row)
                            <tr>
                                <td class="px-1 py-2"><input @if ($inputsDisabled == true) disabled="" @endif
                                        value="{{ $index + 1 }}"
                                        class="hsn-box h-7 w-10 rounded-lg bg-white text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        type="text" /></td>
                                        @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                                            @if (!empty($columnName))
                                                @php
                                                    $this->createChallanRequest['order_details'][$index]['columns'][$key]['column_name'] = $columnName;
                                                @endphp
                                                <td class="px-1 py-2">
                                                    <input @if ($inputsDisabled == true) disabled @endif
                                                        wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                        {{-- wire:input="clearArticleError({{ $index }})" --}}
                                                        class="hsn-box h-7 w-24 dynamic-width-input rounded-lg bg-white border {{ $columnName == 'Article' && $errors->has('article.' . $index) ? 'border-red-300' : 'border-gray-400' }} text-xs font-normal text-black focus:outline-none"
                                                        type="text" />
                                                    {{-- @if ($columnName == 'Article')
                                                        @error('article.' . $index)
                                                            <div class="text-xs text-red-500">{{ $message }}</div>
                                                        @enderror
                                                    @endif --}}
                                                </td>
                                            @endif
                                        @endforeach

                                <td class="px-1 py-2">
                                    <input @if ($inputsDisabled == true) disabled="" @endif type="text"
                                        wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit"
                                        class="hsn-box h-7 w-24 rounded-lg bg-white text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        value="" list="units" />
                                    <datalist id="units">
                                        <option value="Pcs">Pcs</option>
                                        <option value="Mtr">Mtr</option>
                                        <option value="Ltr">Ltr</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Dozen">Dozen</option>
                                        <option class="add-unit" value="Add Unit">Add Unit</option>
                                        <!-- Add Unit option -->
                                    </datalist>
                                </td>


                                <td class="px-2 py-2">
                                    <input @if ($inputsDisabled == true) disabled="" @endif
                                        class="rate hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs  text-black focus:outline-none"
                                        min="0" type="number" wire:model.defer="createChallanRequest.order_details.{{ $index }}.rate" data-index="{{ $index }}"/>
                                </td>
                                <td class="px-2 py-2">
                                    <input @if ($inputsDisabled == true) disabled="" @endif
                                        class="qty hsn-box h-7 w-24 flex rounded-lg  text-center  text-xs  text-black   bg-white {{ $errors->has('qty.' . $index) ? 'border-2 border-red-300' : 'border-gray-400' }} "
                                        min="1" type="number" wire:model.defer="createChallanRequest.order_details.{{ $index }}.qty" data-index="{{ $index }}" />
                                </td>
                                        <!-- Tax -->
                                        <td class="px-2 py-2 ">
                                            <input @if ($inputsDisabled == true) disabled="" @endif class=" tax hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none" min="0" type="number" wire:model.defer="createChallanRequest.order_details.{{ $index }}.tax" data-index="{{ $index }}" data-tax-index="{{ $index }}"/>
                                        </td>
                                        <td class="px-2 py-2" wire:ignore>
                                            <input @if ($inputsDisabled == true) disabled @endif x-ignore
                                                        wire:model.defer="createChallanRequest.order_details.{{ $index }}.total_amount"
                                                        class="hsn-box cursor-not-allowed h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none total"
                                                        type="number" data-index="{{ $index }}" readonly/>
                                        </td>
                                <td class="px-2 py-2">
                                    <button type="button" wire:click.prevent="removeRow({{ $index }})"
                                        class="bg-yellow-500 px-4 py-1 text-black hover:bg-yellow-700"
                                        style="background-color: #e5f811;">X</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @foreach ($createChallanRequest['order_details'] as $index => $row)
                <!-- Blade template section for displaying calculations -->
                <div id="totals-container" class="text-right text-xs text-black mr-5"  wire:ignore></div>
            @endforeach
            <script>
                window.authUser = @json(auth()->user());
               function debounce(func, wait) {
                               let timeout;
                               return function (...args) {
                                   clearTimeout(timeout);
                                   timeout = setTimeout(() => func.apply(this, args), wait);
                               };
                           }

                           window.onload = function () {
                               const calculateTaxCheckbox = document.getElementById('calculateTax');
                               if (calculateTaxCheckbox) {
                                   calculateTaxCheckbox.checked = true;
                               }
                               updateTotals();
                           };

                           function updateTotals() {
                           let totalQty = 0;
                           let totalAmount = 0;
                           const totalsContainer = document.getElementById('totals-container');
                           totalsContainer.innerHTML = '';

                           const rowTotals = {};
                           const rowDiscounts = {};
                           const rowTaxAmounts = {};

                           document.querySelectorAll('.qty').forEach(input => {
                               totalQty += Number(input.value);
                           });

                           document.querySelectorAll('.total').forEach(input => {
                               totalAmount += Number(input.value);
                           });

                           const discountInput = document.querySelector('.discount');
                           const discount = discountInput ? parseFloat(discountInput.value) || 0 : 0;
                           const discountAmount = (totalAmount * discount) / 100;
                           const discountedTotalAmount = totalAmount - discountAmount;

                           // Check the state of the "Round Off" checkbox
                           const roundOffCheckbox = document.getElementById('vue-checkbox-list');
                           let roundOffAmount = 0;
                           if (roundOffCheckbox && roundOffCheckbox.checked) {
                               roundOffAmount = Math.round(discountedTotalAmount) - discountedTotalAmount;
                           }

                               document.querySelectorAll('.qty').forEach((input, index) => {
                                   const rateInput = document.querySelector(`.rate[data-index="${index}"]`);
                                   const qtyInput = document.querySelector(`.qty[data-index="${index}"]`);
                                   const taxInput = document.querySelector(`.tax[data-index="${index}"]`);
                                   const calculateTaxCheckbox = document.getElementById('calculateTax');

                                   const rate = rateInput ? parseFloat(rateInput.value) || 0 : 0;
                                   const qty = qtyInput ? parseFloat(qtyInput.value) || 0 : 0;
                                   const tax = taxInput ? parseFloat(taxInput.value) || 0 : 0;
                                   let totalWithoutTax = rate * qty;

                                   if (calculateTaxCheckbox && !calculateTaxCheckbox.checked) {
                                       totalWithoutTax = (totalWithoutTax * 100) / (100 + tax);
                                   }

                                   const taxAmount = (totalWithoutTax * tax) / 100;
                                   const discountAmounts = (totalWithoutTax * discount) / 100;
                                   const discountedTotalAmounts = totalWithoutTax - discountAmounts;
                                   const taxAmounts = (discountedTotalAmounts * tax) / 100;
                                   const total = totalWithoutTax + taxAmount;

                                   if (!rowTotals[tax]) {
                                       rowTotals[tax] = 0;
                                       rowDiscounts[tax] = 0;
                                       rowTaxAmounts[tax] = 0;
                                   }

                                   rowTotals[tax] += parseFloat(total.toFixed(2));
                                   rowDiscounts[tax] += parseFloat(discountAmounts.toFixed(2));
                                   rowTaxAmounts[tax] += parseFloat(taxAmounts.toFixed(2));

                                   // Update the total_amount input field for each row
                                   const totalAmountInput = document.querySelector(`.total[data-index="${index}"]`);
                                   if (totalAmountInput) {
                                       totalAmountInput.value = total.toFixed(2);
                                       // Dispatch event to ensure Livewire updates
                                       totalAmountInput.dispatchEvent(new Event('input'));
                                   }
                               });

                               for (const tax in rowTotals) {
                                const totalWithoutTax = (rowTotals[tax] / (1 + (tax / 100))).toFixed(2);
                                const discountedTotalAmounts = (totalWithoutTax - rowDiscounts[tax]).toFixed(2);
                                const taxAmounts = rowTaxAmounts[tax].toFixed(2);

                                totalsContainer.innerHTML += `<div>`;

                                if (tax > 0) {
                                    totalsContainer.innerHTML += `<span>Sales at ${tax}%: ${totalWithoutTax}</span><br>`;

                                    if (rowDiscounts[tax] > 0) {
                                        totalsContainer.innerHTML += `<span>Discount at ${discount}%: ${rowDiscounts[tax].toFixed(2)}</span><br>`;
                                        totalsContainer.innerHTML += `<span>Net Sale: ${discountedTotalAmounts}</span><br>`;
                                    }

                                   // Normalize the state strings to ensure they match correctly
                                    const normalizedAuthUserState = authUser.state.trim().toUpperCase();
                                    const normalizedSelectedUserState = selectedUserState.trim().toUpperCase();

                                    console.log(normalizedAuthUserState, normalizedSelectedUserState);

                                    if (normalizedAuthUserState === normalizedSelectedUserState) {
                                        // Intra-state: calculate SGST and CGST
                                        totalsContainer.innerHTML += `<span>SGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span><br>`;
                                        totalsContainer.innerHTML += `<span>CGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span>`;
                                    } else {
                                        // Inter-state: calculate IGST
                                        totalsContainer.innerHTML += `<span>IGST ${tax}%: ${taxAmounts}</span>`;
                                    }
                                }
                                console.log(authUser.state, selectedUserState);
                                totalsContainer.innerHTML += `</div>`;
                            }

                               const totalQtyField = document.querySelector('.totalQtyField');
                               if (totalQtyField) {
                                   totalQtyField.value = totalQty === 0 ? null : totalQty;
                               }

                               const totalAmountField = document.querySelector('.totalAmountField');
                               if (totalAmountField) {
                                   totalAmountField.value = (discountedTotalAmount + roundOffAmount).toFixed(2);
                               }

                               // Update hidden inputs to ensure values are submitted with the form
                               const hiddenTotalQtyField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.total_qty"]');
                               if (hiddenTotalQtyField) {
                                   hiddenTotalQtyField.value = totalQty === 0 ? null : totalQty;
                                   hiddenTotalQtyField.dispatchEvent(new Event('input'));
                               }

                               const hiddenTotalField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.total"]');
                               if (hiddenTotalField) {
                                   hiddenTotalField.value = (discountedTotalAmount + roundOffAmount).toFixed(2);
                                   hiddenTotalField.dispatchEvent(new Event('input'));
                               }

                               const roundOffAmountField = document.getElementById('roundOffAmount');
                               if (roundOffAmountField) {
                                   roundOffAmountField.value = roundOffAmount.toFixed(2);
                               }

                               const hiddenRoundOffField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.round_off"]');
                               if (hiddenRoundOffField) {
                                   hiddenRoundOffField.value = roundOffAmount.toFixed(2);
                                   hiddenRoundOffField.dispatchEvent(new Event('input'));
                               }

                               const totalAmountInWords = numberToIndianRupees(discountedTotalAmount + roundOffAmount);
                               const totalAmountInWordsInput = document.getElementById('totalAmountInWords');
                               if (totalAmountInWordsInput) {
                                   totalAmountInWordsInput.value = totalAmountInWords;
                               }

                               console.log('Total Quantity:', totalQty);
                               console.log('Discounted Total Amount:', discountedTotalAmount);
                               console.log('Round-Off Amount:', roundOffAmount);
                               console.log('Total Amount:', discountedTotalAmount + roundOffAmount);
                               }

                               document.addEventListener('DOMContentLoaded', function () {
                               document.body.addEventListener('input', debounce(calculateTotal, 300));
                               const calculateTaxCheckbox = document.getElementById('calculateTax');
                               if (calculateTaxCheckbox) {
                                   calculateTaxCheckbox.addEventListener('change', calculateTotal);
                               }

                               const roundOffCheckbox = document.getElementById('vue-checkbox-list');
                               if (roundOffCheckbox) {
                                   roundOffCheckbox.addEventListener('change', updateTotals);
                               }
                               });

                           function calculateTotal(event) {
                               if (event.target.matches('.rate, .qty, .tax, .total, .discount') || event.target.id === 'calculateTax') {
                                   document.querySelectorAll('.qty').forEach((input, index) => {
                                       const rateInput = document.querySelector(`.rate[data-index="${index}"]`);
                                       const qtyInput = document.querySelector(`.qty[data-index="${index}"]`);
                                       const taxInput = document.querySelector(`.tax[data-index="${index}"]`);
                                       const discountInput = document.querySelector('.discount');

                                       const rate = rateInput ? parseFloat(rateInput.value) || 0 : 0;
                                       const qty = qtyInput ? parseFloat(qtyInput.value) || 0 : 0;
                                       const tax = taxInput ? parseFloat(taxInput.value) || 0 : 0;
                                       const discount = discountInput ? parseFloat(discountInput.value) || 0 : 0;
                                       let totalWithoutTax = rate * qty;
                                       const taxAmount = (totalWithoutTax * tax) / 100;

                                       let total;

                                       const calculateTaxCheckbox = document.getElementById('calculateTax');
                                       if (calculateTaxCheckbox && calculateTaxCheckbox.checked) {
                                           total = totalWithoutTax + taxAmount;
                                       } else {
                                           total = totalWithoutTax;
                                           totalWithoutTax = parseFloat((totalWithoutTax * 100 / (100 + tax)).toFixed(2));
                                       }

                                       const totalInput = document.querySelector(`.total[data-index="${index}"]`);
                                       if (totalInput) {
                                           totalInput.value = total.toFixed(2);
                                           // Dispatch event to ensure Livewire updates
                                           totalInput.dispatchEvent(new Event('input'));
                                       }
                                   });

                                   updateTotals();
                               }

                           }
                        function convertNumberToWords(number) {
                            const words = {
                                0: 'Zero',
                                1: 'One',
                                2: 'Two',
                                3: 'Three',
                                4: 'Four',
                                5: 'Five',
                                6: 'Six',
                                7: 'Seven',
                                8: 'Eight',
                                9: 'Nine',
                                10: 'Ten',
                                11: 'Eleven',
                                12: 'Twelve',
                                13: 'Thirteen',
                                14: 'Fourteen',
                                15: 'Fifteen',
                                16: 'Sixteen',
                                17: 'Seventeen',
                                18: 'Eighteen',
                                19: 'Nineteen',
                                20: 'Twenty',
                                30: 'Thirty',
                                40: 'Forty',
                                50: 'Fifty',
                                60: 'Sixty',
                                70: 'Seventy',
                                80: 'Eighty',
                                90: 'Ninety'
                            };

                            if (number < 21) {
                                return words[number];
                            } else if (number < 100) {
                                const tens = words[10 * Math.floor(number / 10)];
                                const units = number % 10;
                                return tens + (units ? ' ' + words[units] : '');
                            } else if (number < 1000) {
                                const hundreds = words[Math.floor(number / 100)] + ' Hundred';
                                const remainder = number % 100;
                                return hundreds + (remainder ? ' and ' + convertNumberToWords(remainder) : '');
                            } else if (number < 100000) {
                                const thousands = convertNumberToWords(Math.floor(number / 1000)) + ' Thousand';
                                const remainder = number % 1000;
                                return thousands + (remainder ? ' ' + convertNumberToWords(remainder) : '');
                            } else if (number < 10000000) {
                                const lakhs = convertNumberToWords(Math.floor(number / 100000)) + ' Lakh';
                                const remainder = number % 100000;
                                return lakhs + (remainder ? ' ' + convertNumberToWords(remainder) : '');
                            } else {
                                const crores = convertNumberToWords(Math.floor(number / 10000000)) + ' Crore';
                                const remainder = number % 10000000;
                                return crores + (remainder ? ' ' + convertNumberToWords(remainder) : '');
                            }
                        }

                        function numberToIndianRupees(number) {
                            if (number === 0) {
                                return null;
                            }

                            const amountInWords = convertNumberToWords(Math.floor(number));
                            const decimalPart = Math.round((number - Math.floor(number)) * 100);

                            if (decimalPart > 0) {
                                const decimalInWords = convertNumberToWords(decimalPart);
                                return amountInWords + ' Rupees and ' + decimalInWords + ' Paisa';
                            } else {
                                return amountInWords + ' Rupees';
                            }
                        }
                            // Check for updates every second
                            // setInterval(updateTotals, 1000);
                        </script>


            </div>


            <div class=" mb-4 grid grid-cols-12 border-t-2 border-gray-300">
                <div class="py-4 col text-xs text-black font-semibold">Comment</div>
                <div class="lg:col-span-10 py-2 col-span-10 w-full">
                        <input @if ($inputsDisabled == true) disabled="" @endif
                            wire:model.defer='createChallanRequest.comment'
                            class="hsn-box h-8 w-full rounded-lg bg-white text-center font-mono text-xs font-normal text-black focus:outline-none"
                            type="text" />
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                    <div class="lg:col-span-1 py-4 text-xs text-black font-semibold"></div>
                    <div class="lg:col-span-8 col-span-8 py-2">
                        <!-- <input @if ($inputsDisabled == true) disabled="" @endif class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" value="{{ $totalAmount }}" disabled style="background-color: #423E3E;" /> -->
                    </div>


                    <div class="col-span-2 flex items-center justify-between">
                        <div class="lg:col-span-1 col-span-2 w-24 py-2 whitespace-nowrap">
                            <input id="vue-checkbox-list" @if ($inputsDisabled == true) disabled="" @endif type="checkbox" value="" class="w-4 h-4 text-gray-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="vue-checkbox-list" class="w-full py-3 ms-2 text-xs font-medium text-gray-900 dark:text-gray-300">Round Off</label>

                        </div>
                        <div class="lg:col-span-1 col-span-2 w-24 py-2">
                            <input class="hsn-box h-8 w-20 ml-3 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none"
                            type="text" id="roundOffAmount" readonly value="0.00" />
                     <input type="hidden" wire:model.defer="createChallanRequest.round_off" />
                        </div>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                    <div class="py-4 col text-xs text-black font-semibold">Total</div>
                    <div class="lg:col-span-8 col-span-8 py-2">
                        <input @if ($inputsDisabled == true) disabled="" @endif
                            class="hsn-box h-8 w-11/12 rounded-lg  text-xs border border-gray-400 text-black focus:outline-none"
                            type="text" id="totalAmountInWords" disabled
                            />
                    </div>
                    <div class="col-span-2 flex items-center justify-between">
                        <div class="lg:col-span-1 col-span-2 w-24 py-2">
                            <input  @if ($inputsDisabled == true) readonly @endif
                                class="hsn-box h-8 w-full rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none totalQtyField"
                                type="text" value="{{ $createChallanRequest['total_qty'] }}" readonly
                            />
                            <input type="hidden" wire:model.defer="createChallanRequest.total_qty" />
                        </div>
                        <div class="lg:col-span-1 col-span-2 w-24 py-2">
                            <input  @if ($inputsDisabled == true) readonly @endif
                                    class="hsn-box h-8 w-full ml-3 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none totalAmountField"
                                    type="text" value="{{ $createChallanRequest['total'] }}" readonly
                                />
                                <input type="hidden" wire:model.defer="createChallanRequest.total" />
                        </div>
                    </div>
                </div>
            </div>
            @if ($challanSave)
                        <div id="alert-border-3" class="p-2 mb-4 text-xs text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
                            role="alert">
                            <span class="font-medium">Success:</span> {{ $challanSave }}
                        </div>

                    @endif
            <div class="flex justify-center space-x-4">
                {{-- {{dd($mainUser)}} --}}
                @php
                    $mainUser = json_decode($mainUser);
                @endphp
                    @if ($mainUser->team_user != null)
                        @if ($mainUser->team_user->permissions->permission->buyer->new_purchase_order == 1)
                            <button type="button"
                                @if ($action == 'save') wire:click.prevent='purchaseOrderCreate' @elseif($action == 'edit') wire:click.prevent='purchaseOrdermodify' @endif
                                @if ($inputsDisabled == true) disabled="" @endif @if ($inputsResponseDisabled == false) disabled   @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black  @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2 ">Save</button>
                        @endif
                    @else
                        <button type="button"
                            @if ($action == 'save') wire:click.prevent='purchaseOrderCreate' @elseif($action == 'edit') wire:click.prevent='purchaseOrdermodify' @endif
                            @if ($inputsDisabled == true) disabled="" @endif @if ($inputsResponseDisabled == false) disabled   @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black  @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2 ">Save</button>
                    @endif


                @if ($mainUser->team_user != null)
                    @if ($mainUser->team_user->permissions->permission->buyer->modify_purchase_order == 1)
                        <button wire:click.prevent='purchaseOrderEdit' type="button"
                            @if ($inputsResponseDisabled == true) disabled="" @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gr px-4ay-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
                    @endif
                    @else
                        <button wire:click.prevent='purchaseOrderEdit' type="button"
                            @if ($inputsResponseDisabled == true) disabled="" @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8  px-4 py-2 text-black ">Edit</button>
                @endif


                @if($sendButtonDisabled == true)
                @if ($mainUser->team_user != null)
                    @if ($mainUser->team_user->permissions->permission->buyer->send_purchase_order == 1)
                        <button wire:click.prevent='sendPurchaseOrder({{ $purchaseOrderId }})'
                            @if ($inputsResponseDisabled == true) disabled="" @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
                    @endif
                @else
                    <button wire:click.prevent='sendPurchaseOrder({{ $purchaseOrderId }})'
                        @if ($inputsResponseDisabled == true) disabled="" @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
                    @endif
                @endif
                {{-- <button @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full bg-white lg:px-8 px-4 py-2 text-black ">SFP</button> --}}
            </div>
        </div>

    <script>
        document.addEventListener('input', function(event) {
              if (event.target.classList.contains('dynamic-width-input')) {
                autoAdjustWidth(event.target);
                   }
                   });

                  function autoAdjustWidth(input) {
                   input.style.width = '24';
                      input.style.width = (input.scrollWidth + 1) + 'px';
                   }

                   let productData = {};

window.selectProduct = function(product, productId) {
product = JSON.parse(JSON.stringify(product));
console.log(product, 'product');
const checkboxes = document.querySelectorAll(`input[data-product-id="${productId}"]`);

checkboxes.forEach((checkbox, index) => {
    // Append a unique identifier (index) to the productId
    const uniqueProductId = `${productId}-${index}`;
    if (checkbox.checked) {
        console.log('checked', uniqueProductId);
        const columns = product.details.map(detail => ({ column_name: detail.column_name, column_value: detail.column_value }));
        const productDetails = {
            p_id: product.id, // Keep the original product ID here for reference
            unit: product.unit,
            rate: product.rate,
            qty: product.qty,
            item_code: product.item_code,
            total_amount: product.total_amount,
            columns: columns
        };
        productData[uniqueProductId] = productDetails;
        console.log(productData, 'productData');
    } else {
        delete productData[uniqueProductId];
    }
});
}

function sendDataToLivewire(data) {
    console.log('sendDataToLivewire called');
    const selectedProductIds = Object.keys(data);
    console.log('selectedProductIds', selectedProductIds);
    window.Livewire.emit('addFromStock', selectedProductIds);
     // Uncheck all checkboxes after sending data
    document.querySelectorAll('input[type="checkbox"][data-product-id]').forEach(checkbox => {
        // checkbox.checked = false;
    });

    // Optionally, clear productData if you want to reset the selection completely
    productData = {};
}

window.sendData = function() {
    sendDataToLivewire(productData);
}
function selectAllProducts(event) {
    const isChecked = event.target.checked;
    document.querySelectorAll('.product-checkbox').forEach((checkbox) => {
        checkbox.checked = isChecked;
        // Optionally, trigger any additional logic for when a product is selected
    });
}
    </script>
</div>
