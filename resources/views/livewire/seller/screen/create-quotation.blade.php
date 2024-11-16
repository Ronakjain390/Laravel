<div x-data="invoiceComponent('{{ auth()->user()->state }}', {{ json_encode($panelUserColumnDisplayNames) }}, {{ json_encode($rows) }}, '{{ $context }}', @entangle('selectUser'), {{ json_encode($units) }})"
    x-init="init()">

    @php
        $mainUser = json_decode($this->mainUser);
        $panel = Session::get('panel');
        $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
        $permission = Auth::user()->permissions;
        if ($permission) {
            $permission = json_decode($permission);
            $additionalNumberPermission = isset($permission->sender)
                ? $permission->sender->whatsapp->additional_number
                : null;
        } else {
            $additionalNumberPermission = null;
        }
    @endphp
    {{-- <strong x-text="checked.length"></strong> items. --}}

    <div>

        <!-- First Row - Responsive 2 Columns -->
         <!-- First Row - Responsive 2 Columns -->
   <!-- First Row - Responsive 2 Columns -->
   <div class="grid grid-cols-1   sm:grid-cols-2  gap-4 mb-4">
    <!-- Column 1 -->
    <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
        <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
            {{-- <h5 class="mb-3 text-base  text-center text-gray-900 md:text-xl dark:text-white">
                Bill To
            </h5> --}}

            <div x-data="{ addUser: false, inputsDisabled: true }">
                <div x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                    <!-- Button to toggle dropdown -->
                    <div class="relative" >
                     <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch"
                         data-dropdown-placement="bottom" data-dropdown-trigger="click"
                         class="flex w-full bg-white border border-gray-400 text-xs hover:bg-orange  text-black focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50   rounded-lg   px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                         type="button">


                         @if ($buyerName == '')
                         <span x-cloak>@if($updateForm == true) Select Buyer @else Others @endif</span>
                         @elseif ($buyerName === 'Others')
                             <span>Others</span>
                         @else
                             <span>{{ strtoupper($buyerName) }}</span>
                         @endif
                             <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 10 6">
                                 <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                     stroke-width="2" d="m1 1 4 4 4-4" />
                         </svg>
                     </button>
                    <label for="small_outlined"
                    class="absolute text-sm rounded-2xl ml-3 font-bold text-black dark:text-gray-400 duration-300 transform -translate-y-3 scale-75 top-1 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-1 peer-focus:scale-75 peer-focus:-translate-y-3 start-1 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                        BILL TO
                    </label>
                    </div>
                    <!-- Dropdown menu -->
                    <div x-data="{ search: '', selectedUser: null }" id="dropdownSearch"
                        class="z-20 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                        <!-- Search input -->
                        <div class="p-3">
                            <label for="input-group-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input x-model="search" type="text" id="input-group-search"
                                    class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search user">
                            </div>
                        </div>
                        <!-- Filtered list based on search -->
                        <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                            aria-labelledby="dropdownSearchButton">
                            {{-- @dump($billTo); --}}
                            @foreach ($billTo as $user)
                                <!-- {{-- {{dd($user->details[0])}} --}} -->
                                {{-- @dd($user); --}}
                                <li x-show="search === '' || '{{ strtolower($user->buyer_name ?? null) }}'.includes(search.toLowerCase())" wire:ignore
                                 wire:click.prevent="selectUser(
                                     '{{ $user->invoice_number->series_number ?? 'Not Assigned' }}',
                                     '{{ $user->details[0]->address ?? null }}',
                                     '{{ $user->details[0]->city ?? null }}',
                                     '{{ $user->details[0]->state ?? null }}',
                                     '{{ $user->details[0]->pincode ?? null }}',
                                     '{{ $user->user->email ?? null }}',
                                     '{{ $user->details[0]->phone ?? null }}',
                                     '{{ $user->details[0]->gst_number ?? null }}',
                                     '{{ $user->buyer_name ?? 'Select Buyer' }}',
                                     '{{ json_encode($user ?? null) }}'
                                 )"                                       x-on:click="
                                        selectedUser = '{{ $user->buyer_name ?? 'Select Receiver' }}';
                                        selectedUserState = '{{ $user->details[0]->state ?? '' }}';
                                        console.log('{{ $user->seller_name ?? 'Receiver Name is Empty' }}');
                                        selectUser = true;
                                        authUserState = '{{ $authUserState }}';
                                    ">
                                    <div
                                        class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label
                                            class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded dark:text-gray-300">{{ $user->buyer_name ?? null }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <button wire:click="updateField" @click="selectUserFromOthers()" class="flex w-full items-center p-3 text-xs font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
                            <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                <path d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-2V5a1 1 0 0 0-2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 0 0 2 0V9h2a1 1 0 1 0 0-2Z" />
                            </svg>
                            Others
                        </button>
                    </div>
                </div>
                <div class="w-full text-gray-900 dark:text-white">
                    @if($updateForm == true)
                    <div class="grid grid-cols-12 gap-4"  >
                        <div class="col-span-12 md:col-span-6">
                            <div class="grid gap-2">
                                <div class="flex flex-row items-center">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24 whitespace-nowrap">Estimate No:</dt>
                                    @if ($selectedUser != null)
                                        <dd class="pl-2 text-xs text-black capitalize flex items-center whitespace-nowrap
                                        @if (isset($selectedUser['invoiceSeries']))
                                            @if ($selectedUser['invoiceSeries'] == 'Not Assigned')
                                                text-red-700
                                            @else
                                                text-black
                                            @endif
                                        @else
                                            text-black
                                        @endif">
                                            {{ $selectedUser['invoiceSeries'] ?? null }}{{ '-' }}
                                            @livewire('series-number-input', ['challanSeries' => $selectedUser['invoiceSeries'], 'seriesNumber' => $selectedUser['invoiceNumber'], 'method' => 'invoice'])
                                        </dd>
                                    @endif
                                </div>
                                <div class="flex flex-row items-center">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">Phone:</dt>
                                    <dd class="pl-2 text-xs text-black capitalize">
                                        {{ $selectedUser['phone'] ?? null }}
                                    </dd>
                                </div>
                                <div class="flex flex-row items-center">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">Email:</dt>
                                    <dd class="pl-2 text-xs text-black capitalize">
                                        {{ $selectedUser['email'] ?? null }}
                                    </dd>
                                </div>
                                <div class="flex flex-row items-center">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">Address:</dt>
                                    <dd class="pl-2 text-xs text-black capitalize">
                                        @if (!empty($selectedUser['address']))
                                            {{ $selectedUser['address'] }}
                                        @endif

                                        @if (!empty($selectedUser['city']))
                                            @if (!empty($selectedUser['address'])),
                                            @endif
                                            {{ $selectedUser['city'] }}
                                        @endif

                                        @if (!empty($selectedUser['state']))
                                            @if (!empty($selectedUser['address']) || !empty($selectedUser['city'])),
                                            @endif
                                            {{ $selectedUser['state'] }}
                                        @endif
                                       </dd>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <div class="grid gap-1">
                                {{-- <div class="flex flex-row items-center">
                                    <dt
                                        class="w-1/4 mb-1 text-black  text-xs dark:text-gray-400 whitespace-nowrap">
                                        Estimate No.
                                    </dt>
                                    @if ($selectedUser == !null)
                                    <dd class="pl-4 text-xs text-black capitalize flex items-center whitespace-nowrap
                                    @if (isset($selectedUser['invoiceSeries']))
                                        @if ($selectedUser['invoiceSeries'] == 'Not Assigned')
                                            text-red-700
                                        @else
                                            text-black
                                        @endif
                                    @else
                                        text-black
                                    @endif">
                                    {{ $selectedUser['invoiceSeries'] ?? null }}{{ '-' }}
                                 </dd>
                                    @endif
                                </div> --}}
                                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />



                                <div class="flex flex-row ">
                                    <dt class="w-24 mb-1 text-black   dark:text-gray-400">

                                    </dt>
                                    <dd class="text-xs inline-block text-black text-black capitalize ">
                                        <div class="relative">
                                            @if ($selectedUser == !null)
                                                <input wire:model.defer="createChallanRequest.estimate_date"
                                                    type="date"
                                                    class="bg-gray-100 border h-6 border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                                                    placeholder="Select date">
                                            @endif
                                        </div>
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <div class="grid">
                            {{-- <div class="flex flex-row">
                                <dt  class="w-1/4 mb-1 text-xs  text-black    whitespace-nowrap">
                                    Estimate No.
                                </dt>
                                @if ($selectedUser == !null)
                                    <dd
                                        class="pl-2 text-xs ]
                                    @if (isset($selectedUser['challanSeries'])) @if ($selectedUser['challanSeries'] == 'Not Assigned') text-red-700
                                        @else text-black @endif
                            @else
                            text-black @endif">

                            {{ $selectedUser['challanSeries'] ?? null }}{{ '-' }}{{ $selectedUser['seriesNumber'] ?? null }}
                                    </dd>
                                @endif
                            </div> --}}
                            {{-- <div class="flex flex-row ">
                                <dt class="w-1/4 mb-1 text-black text-xs  ">

                                </dt>
                                <dd class="pl-2 text-xs inline-block text-black text-black capitalize hidden sm:block">
                                    <div class="relative">
                                        @if ($selectedUser == !null)
                                            <input wire:model.defer="createChallanRequest.challan_date"
                                                type="date"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                                                placeholder="Select date">
                                        @endif
                                    </div>
                                </dd>

                            </div> --}}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @if($updateForm == false)
            <!-- Add user form -->
            <div class="gap-2 grid grid-cols-12" >
                <div class="col-span-12"  >
                <!-- Your add user form goes here -->
                    <div class="grid gap-2 sm:grid-cols-2">
                        <div class="flex flex-row mt-2">
                            <dt class="mb-1 text-black text-xs  w-24">Name</dt>
                            <input @if ($inputsDisabled == true)  @endif
                             wire:model.defer="addBuyerData.buyer_name"
                            class="hsn-box h-7 w-2/3 text-xs  rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                            type="text" maxlength="40" oninput="if(this.value.length > 40) this.value = this.value.slice(0, 40);"  />
                        </div>
                        <div class="flex flex-row mt-2">
                            <dt class="mb-1 text-black text-xs w-24">Phone</dt>
                            <div class="w-2/3">
                                <input
                                    @if ($inputsDisabled == true) disabled @endif
                                    wire:model.lazy="addBuyerData.phone"
                                    x-data="{}"
                                    x-on:input="
                                        $el.value = $el.value.slice(0, 10);
                                        if ($el.value.length === 10) {
                                            $wire.set('addBuyerData.phone', $el.value);
                                        }
                                    "
                                    id="phone"
                                    class="hsn-box h-7 w-full text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none @error('addBuyerData.phone') border-red-500 @enderror"
                                    type="number"
                                    max="9999999999"
                                />
                                <div id="phone_error" class="text-red-500 text-[0.6rem] mt-1"></div>

                                @if($existingUser)
                                    <div class="text-blue-500 text-[0.6rem] mt-1">
                                        This phone number is associated with an existing user.
                                        <button wire:click="useExistingUserDetails" class="text-blue-700 underline">
                                            Use existing details
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="flex flex-row mt-2">
                         <dt class="mb-1 text-black text-xs  w-24">Address</dt>
                         <input @if ($inputsDisabled == true)  @endif
                         wire:model.defer="addBuyerData.address"
                         class="hsn-box h-7 w-2/3 text-xs  rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                         type="text" />
                     </div>

                     <div class="sm:flex mt-2 hidden flex-row ">
                         <dt class="mb-1 text-black text-xs  w-24">Pincode</dt>
                         <input @if ($inputsDisabled == true) @endif
                         wire:model.defer="addBuyerData.pincode" oninput="if (this.value.length === 6) { @this.set('addBuyerData.pincode', this.value); @this.call('cityAndStateByPincode'); }"
                         wire:blur="cityAndStateByPincode"
                         class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                         type="text" />
                     </div>

                     <div class="sm:flex mt-2 hidden flex-row ">
                         <dt class="mb-1 text-black text-xs  w-24">City</dt>
                         <input @if ($inputsDisabled == true)  @endif
                         wire:model="addBuyerData.city"
                         class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                         type="text" />
                     </div>

                     <div class="flex flex-row mt-2">
                         <dt class="mb-1 text-black text-xs  w-24">State</dt>

                         <input @if ($inputsDisabled == true) @endif
                         wire:model="addBuyerData.state"
                         class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                         type="text" />
                     </div>

                     {{-- <div class="flex flex-row mt-2">
                         <dt class="mb-1 text-black text-xs  w-24">Gst</dt>
                         <input @if ($inputsDisabled == true)  @endif
                         wire:model.defer="addBuyerData.gst_number"
                         class="hsn-box h-7 w-2/3 text-xs  rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                         type="text" />
                     </div> --}}

                    </div>

                </div>

                <!-- Add more fields as needed -->
            </div>

            @endif
        </div>
    </div>

    @if($updateForm == false)
    <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
        <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
         <div class="grid gap-2 sm:grid-cols-2 items-center">
             <div class="flex flex-row  items-center">
                 <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24 whitespace-nowrap">Date:</dt>
                 <dd class="text-xs inline-block text-black text-black capitalize ">
                     <div class="relative">
                         @if ($selectedUser == !null)
                             <input wire:model.defer="createChallanRequest.estimate_date"
                                 type="date"
                                 class="bg-gray-100 border h-6 border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                                 placeholder="Select date">
                         @endif
                     </div>
                 </dd>
             </div>

             <div class="flex flex-row  items-center">
                 <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24 whitespace-nowrap">Estimate No:</dt>
                 @if ($selectedUser != null)
                     <dd class="pl-2 text-xs text-black capitalize flex items-center whitespace-nowrap
                     @if (isset($selectedUser['invoiceSeries']))
                         @if ($selectedUser['invoiceSeries'] == 'Not Assigned')
                             text-red-700
                         @else
                             text-black
                         @endif
                     @else
                         text-black
                     @endif">
                         {{ $selectedUser['invoiceSeries'] ?? null }}{{ '-' }}
                         @livewire('series-number-input', ['challanSeries' => $selectedUser['invoiceSeries'], 'seriesNumber' => $selectedUser['invoiceNumber'], 'method' => 'invoice'])
                     </dd>
                 @endif
             </div>
         </div>
         <div x-data="{ open: false }" class="gap-3 grid pt-2">
             <div class="flex items-center mt-2 gap-2">
                 <button @click="open = ! open" class="p-1 rounded-full bg-[#e5f811] focus:outline-none focus:ring-2 " style="background-color: #e5f811;">
                     <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                 </svg>
             </button>
             <label for="toggleInput" class="block text-[0.6rem] text-black font-italic mr-3">Additional Info</label>
             </div>
             <div x-show="open" class="grid gap-2 grid-cols-2">

                 <div class="flex flex-row mt-2">
                    <dt class="mb-1 text-black text-xs  w-24">Email</dt>
                    <div class="w-2/3">
                        <input @if ($inputsDisabled == true) disabled @endif
                        wire:model.defer="addBuyerData.email" id="email"
                        class="hsn-box h-7 w-full text-xs  rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                        type="email" pattern=".+@gmail\.com" title="Please enter a valid Gmail address" />
                        <div id="email_error" class="text-red-500 text-xs mt-1"> </div>
                    </div>
                </div>
                <div class="flex flex-row mt-2">
                    <dt class="mb-1 text-black text-xs  w-24">Company </dt>
                    <input @if ($inputsDisabled == true)  @endif
                    wire:model.defer="addBuyerData.company_name"
                    class="hsn-box h-7 w-2/3 text-xs  rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                    type="text" />
                </div>
                     <div class="flex flex-row mt-2">
                         <dt class="mb-1 text-black text-xs  w-24">Gst</dt>
                         <input @if ($inputsDisabled == true)  @endif
                         wire:model.defer="addBuyerData.gst_number"
                         class="hsn-box h-7 w-2/3 text-xs  rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                         type="text" />
                     </div>
         </div>

         <!-- Add more fields as needed -->
    </div>
         </div>

        </div>
    @endif

    <!-- Column 2 -->
    @if($updateForm == true)
    <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
        <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">


            <div x-data="{ search: '', selectedUserDetails: null }">
                <!-- Button to toggle dropdown -->
                <div class="relative" >
                <button id="dropdownDetailSearchButton" data-dropdown-toggle="dropdownDetailSearch"
                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                    class="  flex w-full bg-white border border-gray-400 text-xs hover:bg-orange  text-black focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg  px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                    type="button">

                     {{-- @if ($buyerAddress == '')
                         <span x-cloak>Select Address</span>
                     @else
                         <span> {{ strToUpper($buyerAddress) }}</span>
                     @endif --}}

                     @if (!$userSelected)
                         <span x-cloak>Select Address</span>
                     @elseif ($buyerAddress == '')
                         <span>Default Address</span>
                     @else
                         <span>{{ strToUpper($buyerAddress) }}</span>
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
                    class="z-20 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                    <!-- Search input -->
                    <div class="p-3">
                        <label for="input-address-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" id="input-address-search"
                                class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search user">
                        </div>
                    </div>
                    <!-- Filtered list based on search -->
                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownDetailSearchButton">
                        @foreach ($selectedUserDetails as $detail)
                        <li x-show="search === '' || '{{ strtolower($detail->address ?? null) }}'.includes(search.toLowerCase())"
                            wire:click="selectUserAddress('{{ json_encode($detail) }}','{{ json_encode($selectedUserDetails) }}')">
                            <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <label class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">
                                    {{ $detail->address ?? null }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                    {{-- <button
                        class="flex w-full items-center p-3 text-xs font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 18">
                            <path
                                d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-2V5a1 1 0 0 0-2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 0 0 2 0V9h2a1 1 0 1 0 0-2Z" />
                        </svg>
                        Add user
                    </button> --}}
                </div>
            </div>
            <div class="grid gap-2">
             <div class="flex flex-row">
                 <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">Address :</dt>
                 <dd class="pl-5 text-xs  text-black capitalize">
                     {{ $selectedUser['address'] ?? null }}</dd>
             </div>
             <div class="flex flex-row">
                 <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">City :</dt>
                 <dd class="pl-5 text-xs  text-black capitalize">
                     {{ $selectedUser['city'] ?? null }}</dd>
             </div>
             <div class="flex flex-row">
                 <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">State :</dt>
                 <dd class="pl-5 text-xs  text-black capitalize">
                     {{ $selectedUser['state'] ?? null }}</dd>
             </div>
             <div class="flex flex-row">
                 <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">Pincode :</dt>
                 <dd class="pl-5 text-xs  text-black capitalize">
                     {{ $selectedUser['pincode'] ?? null }}</dd>
             </div>
         </div>

        </div>
    </div>
    @endif
</div>

        <div wire:loading    class="fixed z-30 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <span   class="loading loading-spinner loading-md"></span>
        </div>
        <div x-data="{ open: false }" class=" m-2 text-xs">
            <!-- Add Row Button -->
            <div class="flex items-center justify-between sm:justify-start" x-show="selectUser" >
                <div class="sm:p-4 p-1.5">
                    <button class="rounded-full bg-yellow-500 px-3 py-1 text-[0.6rem] sm:text-xs shadow-lg text-black hover:bg-yellow-700 sm:h-5 sm:h-7"
                    style="background-color: #e5f811;" @click="addRow()">
                    <span class="hidden sm:inline">Add New Row</span>
                    <span class="inline sm:hidden">+</span>
                </button>
                </div>
                @if ($stocks)
                    <button @click="open = true" type="button"
                        class="rounded-full bg-yellow-500 px-3 py-1 text-[0.6rem] sm:text-xs shadow-lg text-black hover:bg-yellow-700 sm:h-5 sm:h-7"
                        style="background-color: #e5f811;">
                        <span class="hidden sm:inline">Add From Stock</span>
                        <span class="inline sm:hidden">Stock</span>
                    </button>
                @endif
                <div class=" flex-col hidden sm:flex ml-3">
                    @if ($showBarcode)
                        <input
                            class="text-black text-[0.6rem] sm:text-xs border border-solid rounded-lg h-5 sm:h-7 w-full sm:w-auto px-2"
                             wire:model.debounce.500ms="barcode"
                            type="text" placeholder="Scan Barcode">

                        <span x-show="showAlert" class="text-red-500 text-[0.6rem] sm:text-xs mt-1">
                            Product not found
                        </span>
                    @endif
                </div>
            </div>



            <div x-show="open" x-cloak wire:ignore.self
                x-init="$watch('open', value => { if (value) { checked = []; } })"
                class="fixed inset-0 flex items-center sm:justify-center overflow-x-auto md:ml-64 bg-opacity-50 backdrop-blur-sm z-10">
                <div class="bg-white p-3 rounded shadow-md max-w-5xl">
                    {{-- <pre x-text="JSON.stringify(checked, null, 2)"></pre> --}}
                    <!-- Modal header -->
                    <div class="">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="items-start justify-between p-2 border-b rounded-t dark:border-gray-600">
                                <button type="button" @click="open = false"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-[0.6rem] sm:text-xs w-8 h-9 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <button type="button" @click="open = false; addSelectedDataToInputs()"
                                    class="text-white bg-gray-900 hover:bg-orange hover:text-black focus:ring-2 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-[0.6rem] sm:text-xs px-5 py-2.5 text-center dark:hover:bg-orange dark:focus:ring-blue-800 mr-5 ml-auto">Add</button>
                            </div>

                                <!-- Modal body -->
                                <div class="p-2 space-y-6 h-80 overflow-y-auto" @click.stop>
                                            <div class="flex space-x-4"  @click.stop >
                                                <div class="flex bg-white dark:bg-gray-900" wire:ignore.self>
                                                    <h5 class="mr-2" style="align-self: center;">Filter: </h5>
                                                    {{-- <button wire:click="updateVariable('Article', null)" class="p-2 text-[0.6rem] sm:text-xs text-red-600 hover:text-red-800">
                                                        Remove filter
                                                    </button> --}}
                                                    <div class="mr-2" x-data="{ search: '', selectedUser: null }"    wire:ignore.self>
                                                        <!-- Button to toggle dropdown -->
                                                        <div class="flex border border-gray-900 rounded-lg text-[0.6rem] sm:text-xs">
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
                                                                <small class="flex text-[0.6rem] sm:text-xs ml-1 font-bold text-red-500">
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
                                                                <div class="relative text-[0.6rem] sm:text-xs">
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
                                                                        class="block w-full p-2 pl-10 text-[0.6rem] sm:text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                                        placeholder="Search user">
                                                                </div>
                                                            </div>
                                                            <input type="hidden" wire:model="article" style="display: none;">
                                                            <!-- Filtered list based on search -->
                                                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-[0.6rem] sm:text-xs text-gray-700 dark:text-gray-200"
                                                                aria-labelledby="dropdownArticleSearch">
                                                                <li class="cursor-pointer "
                                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                                    wire:click.prevent="filterVariable('Article','{{ null }}')">
                                                                    <div
                                                                        class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                        <label
                                                                            class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                                    </div>
                                                                </li>
                                                                @foreach ($articles as $atcl)
                                                                    <li class="cursor-pointer"
                                                                        x-show="search === '' || '{{ strtolower($atcl ?? null) }}'.includes(search.toLowerCase())"
                                                                        wire:click.prevent="filterVariable('Article','{{ $atcl }}')">
                                                                        <div
                                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                            <label
                                                                                class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $atcl ?? null }}</label>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="mr-2" x-data="{ search: '', selectedUser: null, dropdownOpen: false }" wire:ignore.self>
                                                        <!-- Button to toggle dropdown -->
                                                        <div class="flex border border-gray-900 rounded-lg text-[0.6rem] sm:text-xs">
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
                                                                <small class="flex text-[0.6rem] sm:text-xs ml-1 font-bold text-red-500">
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
                                                                <div class="relative text-[0.6rem] sm:text-xs">
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
                                                                        class="block w-full p-2 pl-10 text-[0.6rem] sm:text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                                        placeholder="Search user">
                                                                </div>
                                                            </div>
                                                            <input type="hidden" wire:model="item_code" style="display: none;">
                                                            <!-- Filtered list based on search -->
                                                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-[0.6rem] sm:text-xs text-gray-700 dark:text-gray-200"
                                                                aria-labelledby="dropdownCodeSearch">
                                                                <li class="cursor-pointer "
                                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                                    @click.prevent="dropdownOpen = false; $wire.filterVariable('item_code','{{ null }}')">
                                                                    <div
                                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                        <label
                                                                            class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                                    </div>
                                                                </li>
                                                                @foreach ($item_codes as $code)
                                                                    <li class="cursor-pointer"
                                                                        x-show="search === '' || '{{ strtolower($code ?? null) }}'.includes(search.toLowerCase())"
                                                                        @click.prevent="dropdownOpen = false; $wire.filterVariable('item_code','{{ $code }}')">
                                                                        <div
                                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                            <label
                                                                                class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $code ?? null }}</label>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="mr-2" x-data="{ search: '', selectedUser: null, dropdownOpen: false }" wire:ignore.self>
                                                        <!-- Button to toggle dropdown -->
                                                        <div class="flex border border-gray-900 rounded-lg text-[0.6rem] sm:text-xs">
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
                                                                <small class="flex text-[0.6rem] sm:text-xs ml-1 font-bold text-red-500">
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
                                                            <div class="relative text-[0.6rem] sm:text-xs">
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
                                                                    class="block w-full p-2 pl-10 text-[0.6rem] sm:text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                                    placeholder="Search user">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" wire:model="article" style="display: none;">
                                                        <!-- Filtered list based on search -->
                                                        <ul class="h-48 px-3 pb-3 overflow-y-auto text-[0.6rem] sm:text-xs text-gray-700 dark:text-gray-200"
                                                            aria-labelledby="dropdownCategorySearch">
                                                            <li class="cursor-pointer "
                                                                x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                                wire:click.prevent="updateVariable('category','{{ null }}')">
                                                                <div
                                                                    class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                    <label
                                                                        class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                                </div>
                                                            </li>
                                                            @foreach ($categories as $cat)
                                                                <li class="cursor-pointer"
                                                                    x-show="search === '' || '{{ strtolower($cat ?? null) }}'.includes(search.toLowerCase())"
                                                                    wire:click.prevent="updateVariable('category','{{ $cat }}')">
                                                                    <div
                                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                        <label
                                                                            class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $cat ?? null }}</label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    </div>

                                                    <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                                                        <div class="flex border border-gray-900 rounded-lg text-[0.6rem] sm:text-xs">
                                                            <!-- Button to toggle dropdown -->
                                                            <button id="dropdownLocationSearch" data-dropdown-toggle="dropdownLocateSearch"
                                                                data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                                                class="text-black flex w-full    bg-white    text-[0.6rem] sm:text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
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


                                                            <small class="flex text-[0.6rem] sm:text-xs ml-1 font-bold text-red-500">
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
                                                                <div class="relative text-[0.6rem] sm:text-xs">
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
                                                                        class="block w-full p-2 pl-10 text-[0.6rem] sm:text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                                        placeholder="Search user">
                                                                </div>
                                                            </div>
                                                            <input type="hidden" wire:model="location" style="display: none;">
                                                            <!-- Filtered list based on search -->
                                                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-[0.6rem] sm:text-xs text-gray-700 dark:text-gray-200"
                                                                aria-labelledby="dropdownLocationSearch">
                                                                <li class="cursor-pointer"
                                                                    x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                                    wire:click.prevent="filterVariable('location','{{ null }}')">
                                                                    <div
                                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                        <label
                                                                            class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                                    </div>
                                                                </li>
                                                                @foreach ($locations as $loc)
                                                                    <li class="cursor-pointer"
                                                                        x-show="search === '' || '{{ strtolower($loc ?? null) }}'.includes(search.toLowerCase())"
                                                                        wire:click.prevent="filterVariable('location','{{ $loc }}')">
                                                                        <div
                                                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                            <label
                                                                                class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $loc ?? null }}</label>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                                                        <div class="flex border border-gray-900 rounded-lg text-[0.6rem] sm:text-xs">
                                                            <!-- Button to toggle dropdown -->
                                                            <button id="dropdownWarehouseSearch" data-dropdown-toggle="dropdownWareSearch"
                                                                data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                                                class="text-black flex w-full    bg-white    text-[0.6rem] sm:text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
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
                                                                <small class="flex text-[0.6rem] sm:text-xs font-bold text-red-500 ml-1">
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
                                                            <div class="relative text-[0.6rem] sm:text-xs">
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
                                                                    class="block w-full p-2 pl-10 text-[0.6rem] sm:text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                                    placeholder="Search user">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" wire:model="warehouse" style="display: none;">
                                                        <!-- Filtered list based on search -->
                                                        <ul class="h-48 px-3 pb-3 overflow-y-auto text-[0.6rem] sm:text-xs text-gray-700 dark:text-gray-200"
                                                            aria-labelledby="dropdownLocationSearch" >
                                                            <li class="cursor-pointer"
                                                                x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                                                wire:click.prevent="updateVariable('warehouse','{{ null }}')">
                                                                <div
                                                                    class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                    <label
                                                                        class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                                                </div>
                                                            </li>
                                                            @foreach ($warehouses as $ware)
                                                                <li class="cursor-pointer"
                                                                    x-show="search === '' || '{{ strtolower($ware ?? null) }}'.includes(search.toLowerCase())"
                                                                    wire:click.prevent="updateVariable('warehouse','{{ $ware }}')">
                                                                    <div
                                                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                        <label
                                                                            class="w-full py-2 ml-2 text-[0.6rem] sm:text-xs text-[0.6rem] sm:text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $ware ?? null }}</label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    </div>

                                                </div>
                                            </div>
                                    <table
                                        class="w-full text-[0.6rem] sm:text-xs text-left text-black dark:text-gray-400 overflow-x-auto">
                                        <thead
                                            class="text-[0.6rem] sm:text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                            <th scope="col"
                                            class="va-b px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                class="product-checkbox w-4 h-4 border ml-1  text-purple-600 bg-gray-100 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                @click="toggleAll"
                                                :checked="allChecked"
                                            >
                                            </div>
                                        </th>
                                            <th scope="col"
                                                class="va-b px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                S. No.</th>
                                            @foreach ($ColumnDisplayNames as $index => $columnName)
                                                @if (!empty($columnName))
                                                    <th
                                                        class="va-b px-2 py-2 text-[0.6rem] sm:text-xs border border-gray-300 whitespace-nowrap">
                                                        @if ($index >= 3 && $index <= 6)
                                                            {{ $index - 2 }} ({{ ucfirst($columnName) }})
                                                        @else
                                                            {{ ucfirst($columnName) }}
                                                        @endif
                                                    </th>
                                                @endif
                                            @endforeach
                                        </thead>

                                        <tbody class="stock-table">
                                            @foreach ($stocks as $key => $product)
                                                @if ($product->qty > '0')
                                                    <tr
                                                        class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap">
                                                        <th scope="row"
                                                            class="flex items-center whitespace-nowrap px-1 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300 text-gray-900 dark:text-white">
                                                            <div>
                                                                <input type="checkbox"
                                                                value="{{ $product }}"
                                                                x-model="checked"
                                                                @change="handleCheckboxChange($event, '{{ $product }}')"
                                                                class="product-checkbox w-4 h-4 border ml-2  text-purple-600 bg-gray-100 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                            </div>
                                                        </th>
                                                        <td
                                                            class="px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                            {{ ++$key }}</td>
                                                        @foreach ($product->details as $index => $column)
                                                            @if (!empty($ColumnDisplayNames[$index]))
                                                                @php
                                                                    $column = (object) $column;
                                                                @endphp
                                                                @if ($index > 6)
                                                                @break
                                                            @endif
                                                            <td
                                                                class="px-1 py-2 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                                {{ $column->column_value }}
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                    <td
                                                        class="px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                        {{ $product->item_code }}</td>
                                                    <td
                                                        class="px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                        {{ $product->category }}</td>
                                                    <td
                                                        class="px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                        {{ $product->location }}</td>
                                                    <td
                                                        class="px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                        {{ $product->warehouse }}</td>
                                                    <td
                                                        class="px-2 py -1 text-[0.6rem] sm:text-xs border-2 border-gray-300 Unit">
                                                        {{ $product->unit }}</td>
                                                    <td
                                                        class="px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                        {{ $product->qty }}</td>
                                                    <td
                                                        class="px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                                        {{ $product->rate }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $stocks->links() }}
                            </div>

                    </div>
                </div>
            </div>
            </div>

        <div class="bg-[#ebebeb] rounded-lg shadow  overflow-x-auto text-xs">

            <div class="w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-xs font-semibold text-black">
                            <th class="p-2 text-left">#</th>
                            @foreach($panelUserColumnDisplayNames as $columnName)
                                @if($columnName !== '')
                                    <th class="p-2 text-left">{{ $columnName }}
                                        @if($columnName === 'Article')
                                        <span class="text-red-500">*</span>
                                    @endif
                                    </th>
                                @endif
                            @endforeach
                            <th class="p-2 text-left">Unit</th>
                            <th class="p-2 text-left">Rate</th>
                            {{-- @if($pdfData->challan_templete == 4) --}}
                                <th class="p-2 text-left">Tax (%)</th>
                            {{-- @endif --}}
                            <th class="p-2 text-left" >Qty  <span class="text-red-500">*</span></th>

                            <th class="p-2 text-left">Total Amount</th>
                            <th class="p-2 text-left w-10" ></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            @foreach($panelUserColumnDisplayNames as $columnName)
                                @if($columnName !== '')
                                    <td></td>
                                @endif
                            @endforeach
                            <td></td>
                            <td class="">
                                <div class="flex items-center space-x-2">
                                    <label for="withoutTax" class="text-[0.6rem] font-semibold text-black dark:text-gray-300">Without Tax</label>
                                    <input x-model="calculateTax" type="checkbox" id="withoutTax"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </td>
                            {{-- @if($pdfData->challan_templete == 4) --}}
                                <td></td>
                            {{-- @endif --}}
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <template x-for="(row, index) in rows" :key="index">
                            <tr class="items-center">
                                <td class="p-2 pr-0">
                                    <span x-text="index + 1" class="block text-xs font-semibold text-black"></span>
                                </td>
                                <template x-for="(columnName, key) in panelUserColumnDisplayNames.filter(name => name !== '')" :key="key">
                                    <td class="p-2">
                                        <div class="relative">
                                            <input
                                                x-model="row[columnName]"
                                                type="text"
                                                class="p-1 sm:w-full w-24 rounded-md text-xs text-black border"
                                                :class="{
                                                    'border-red-500': (columnName === 'Article' && row.showArticleError) || (columnName === 'rate' && row.showQtyError),
                                                    'border-gray-300': (columnName !== 'Article' && columnName !== 'rate') || (!row.showArticleError && !row.showQtyError)
                                                }"
                                                :placeholder="columnName + (columnName === 'Article' ? '' : '')"
                                                x-bind:disabled="!selectUser"
                                            />
                                        </div>
                                    </td>
                                </template>
                                <td class="p-2">
                                    <select x-model="row.unit" x-bind:disabled="!selectUser"
                                        class="p-1 w-24 rounded-md text-xs text-black border border-gray-300">
                                        <option disabled value=""></option>
                                        <template x-for="unit in units" :key="unit.short_name">
                                            <option :value="unit.short_name" x-text="unit.short_name.toUpperCase()"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input
                                        x-model="row.rate"
                                        x-bind:disabled="!selectUser"
                                        type="number"
                                        class="p-1 sm:w-full w-24 rounded-md text-xs text-black border"
                                        :class="{ 'border-red-500': rateErrors[row.id], 'border-gray-300': !rateErrors[row.id] }"
                                        placeholder="Rate"
                                        @input="validateRate(row); calculateTotal(row)"
                                    />
                                    <template x-if="rateErrors[row.id]">
                                        <p class="text-red-500 text-xs mt-1" x-text="rateErrors[row.id]"></p>
                                    </template>
                                </td>
                                {{-- @if($pdfData->challan_templete == 4) --}}
                                <td class="p-2">
                                    <input x-model="row.tax" type="number" class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                                        placeholder="Tax %" @input="calculateTotal(row)" />
                                </td>
                                {{-- @endif --}}
                                <td class="p-2">
                                    <input x-model="row.quantity" x-bind:disabled="!selectUser" type="number"
                                        class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                                        :class="{'border-red-500': row.showQtyError, 'border-gray-300': !row.showQtyError}"
                                        placeholder="Qty" @input="calculateTotal(row)" />
                                </td>
                                <td class="p-2" hidden>
                                    <input x-model="row.item_code" type="text" class="p-1 hidden w-full rounded-md text-xs text-black border border-gray-300"
                                    placeholder="Item Code" />
                                </td>
                                <td class="p-2">
                                    <input type="text" x-bind:value="calculateTotal(row)" class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300 block bg-white" readonly />
                                </td>
                                <td class="p-2 pr-0">
                                    <button x-show="rows.length >= 2" @click="deleteRow(index)"
                                        class="border bg-yellow-600 hover:bg-yellow-700 text-black rounded-md px-2 py-1 inline-block"
                                        style="background-color: #e5f811;">X</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

                    <!-- Dynamic Headers and Inputs in one row -->


            <div class="sm:flex block  justify-between">
                <!-- Left Side: Comment Input Box -->
                <div class="flex flex-grow items-center sm:mr-10  sm:p-2  sm:pr-8 ">
                    <label class="flex-grow font-semibold text-black w-5 sm:w-10"></label>
                    <input @if ($inputsDisabled == true) disabled @endif
                        wire:model.defer='createChallanRequest.comment' placeholder="Comment"
                        class="mt-1 hsn-box h-5 sm:h-7 w-full rounded-lg bg-white border border-gray-400 border-solid text-[0.6rem] sm:text-xs text-black focus:outline-none"
                        type="text" maxlength="100"
                        oninput="if(this.value.length > 100) this.value = this.value.slice(0, 100);" />


                    <!-- Total Amount in Words -->


                    <!-- Total Amount in Words -->

                </div>

                <!-- Right Side: Tax Breakdown, Discount, and Round Off -->
                <div class="flex flex-col items-start sm:p-2 p-1 text-black sm:mr-10 mr-0">
                    <!-- Tax Breakdown -->
                    {{-- <div class="mt-4 p-4 shadow rounded-lg w-full"> --}}
                        {{-- <h3 class="font-semibold text-lg">Tax Breakdown</h3> --}}
                        <div class=" w-full" x-html="taxBreakdown"></div>
                    {{-- </div> --}}

                    <!-- Discount Input Box -->
                    <div class="text-right items-center mt-4 w-full">
                        <label class="mr-6">Discount (%):</label>
                        <input x-model="discount" x-bind:disabled="!selectUser" @input="updateTotals" type="number" class="border text-black text-xs border-gray-300 w-24 p-1 rounded-md flex-grow" />
                    </div>

                    <!-- Round Off Checkbox -->
                    <div class="text-right items-center mt-2 w-full">
                        <label class="mr-2">Round Off:</label>
                        <input x-model="roundOff" class="mr-6" type="checkbox" @change="updateTotals">
                        <input x-show="roundOff" class="border text-black text-xs border-gray-300 bg-white w-24 p-1 rounded-md flex-grow" x-bind:value="roundOffAmount.toFixed(2)" disabled>
                    </div>
                </div>

            </div>


            <!-- Total Quantity and Total Amount (Sticky below Quantity and Amount fields) -->
            {{-- <div class="sm:flex block items-center justify-between border-t border-gray-400">
                <!-- Left Side: Total and Total Amount in Words -->
                <div class="flex items-center">
                    <div class="font-semibold p-2 text-black">Total</div>
                    <span x-text="totalAmountInWords" class="text-black text-xs ml-2"></span>
                </div>

                <div class="flex items-center">

                    <!-- Total Quantity -->
                     <div class="w-24 p-2 sticky bottom-0 flex-grow">
                          <input type="text" x-model="totalQty" disabled class="block text-xs text-black font-bold bg-white border border-gray-300 rounded-md p-1 w-full" />
                    </div>


                    <!-- Total Amount -->
                    <div class="w-24 p-2 sticky bottom-0 flex-grow">
                         <input type="text" x-model="totalAmount" disabled class="block text-xs text-black font-bold bg-white border border-gray-300 rounded-md p-1 w-32" />
                    </div>

                    <div class="w-10 p-2 pr-0"></div>
                </div>
            </div> --}}


            <div class="gap-4 grid grid-cols-1 grid-flow-col p-1 w-full">
                <div class="flex gap-3 items-center justify-between text-black pr-10">
                    <label for="total" class="text-black font-bold">Total</label>
                    <input type="text" x-bind:value="totalAmountInWords" disabled class="sm:block hidden text-xs mr-2 text-black bg-white border border-gray-300 rounded-md p-1 w-full" />
                </div>
                <!-- ... -->
                {{-- <div></div> --}}
                {{-- <div class="grid grid-rows-subgrid gap-4 row-span-3">
                    <div class="row-start-2"></div>
                </div> --}}
                <div>
                    <div class="flex sm:gap-6 gap-3 justify-end sm:mr-10">
                        <input type="text" x-model="totalQty" disabled class="block text-xs text-black font-bold  bg-white border border-gray-300 rounded-md p-1 w-24 ml-5 mr-3 sm:mr-0" />
                        <input type="text" x-model="totalAmount" disabled class="block text-xs text-black font-bold sm:mr-1 bg-white border border-gray-300 rounded-md p-1 w-24" />

                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- @dump($invoiceId) --}}

    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs" id="successMessage"></p>
        </div>
    </div>
    <div id="errorModal" style="display: none;">
        <div class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs">
            <p class="mt-3 " id="errorMessage">
            </p>
        </div>
    </div>

    <div x-data="{
        validationFailed: false,
        validateFields() {
            let isValid = true;
            rows.forEach(row => {
                if (!row.Article || row.Article.trim() === '') {
                    row.showArticleError = true;
                    isValid = false;
                } else {
                    row.showArticleError = false;
                }

                if (!row.quantity || isNaN(parseFloat(row.quantity)) || parseFloat(row.quantity) <= 0) {
                    row.showQtyError = true;
                    isValid = false;
                } else {
                    row.showQtyError = false;
                }
            });
            this.validationFailed = !isValid;
            return isValid;
        }
    }"
    class="flex justify-center space-x-4 lg:text-lg text-[0.6rem] sm:text-xs m-2">

        <div>
            <!-- Validation Error Message -->
            <div x-cloak  x-show="validationFailed" class="text-red-500 text-sm mb-2">
                Please fill all required fields.
            </div> <br>

            <div>
            {{-- Save Button --}}
            @if ($mainUser->team_user != null)
                @if ($mainUser->team_user->permissions->permission->seller->create_invoice == 1)
                    <button type="button" id="add"
                    @if ($action == 'save')
                        @click.prevent="if(validateFields()) { submitData(); }"
                    @elseif($action == 'edit')
                        @click.prevent="if(validateFields()) { editData(); }"
                    @endif
                    x-bind:disabled="!selectUser"
                    class="rounded-full btn-size lg:px-8 px-4 py-2 @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900  text-white @endif ">
                    Save
                    </button>
                @endif
            @else
                <button type="button" id="add"
                    @if ($action == 'save')
                        @click.prevent="if(validateFields()) { submitData(); }"
                    @elseif($action == 'edit')
                        @click.prevent="if(validateFields()) { editData(); }"
                    @endif
                    x-bind:disabled="!selectUser"
                    class="rounded-full btn-size lg:px-8 px-4 py-2 @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900  text-white @endif ">
                    Save
                </button>
            @endif

        {{-- Edit Button --}}
        @if ($mainUser->team_user != null)
            @if ($mainUser->team_user->permissions->permission->seller->modify_invoice == 1)
                <button wire:click.prevent='invoiceEdit' type="button"
                    @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gr px-4ay-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
            @endif
        @else
            <button wire:click.prevent='invoiceEdit' type="button"
                @if ($inputsResponseDisabled == true) disabled="" @endif
                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
        @endif

        @if($sendButtonDisabled == true)
        {{-- Send Button --}}
        @if ($mainUser->team_user != null)
            @if ($mainUser->team_user->permissions->permission->seller->send_invoice == 1)
                <button wire:click.prevent='sendInvoice({{ $invoiceId }})'
                    @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
            @endif
        @else
            <button wire:click.prevent='sendInvoice({{ $invoiceId }})'
                @if ($inputsResponseDisabled == true) disabled="" @endif
                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
        @endif
        @endif
        {{-- @dump($teamMembers) --}}
        @if($teamMembers != null)
        {{-- SFP Button --}}
        <button
         wire:click="$emit('openSfpModal', { challanId: {{ $invoiceId ?? 'null' }}, type: 'estimate' })"
        @if ($inputsResponseDisabled == true) disabled="" @endif
        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>

        @endif
            </div>
    </div>

    </div>
    <livewire:components.sfp-component :panelType="'quotation'"/>

<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('itemIdUpdated', (invoiceId) => {
            console.log('invoiceId updated:', invoiceId);
        });
    });
    window.addEventListener('show-error-message', event => {
            // Set the message in the modal
            document.getElementById('errorMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('errorModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('errorModal').style.display = 'none';
            }, 10000);
        });

        window.addEventListener('show-success-message', event => {
            // Set the message in the modal
            document.getElementById('successMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 10000);
        });

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
                const columns = product.details.map(detail => ({
                    column_name: detail.column_name,
                    column_value: detail.column_value
                }));
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
{{-- <script>
    function invoiceComponent(authUserStateFromServer) {
    return {
        rows: @entangle('rows').defer,
        selectUser: false,
        calculateTax: true,
        panelUserColumnDisplayNames: @entangle('panelUserColumnDisplayNames').defer,
        discount: null,
        selectedProducts: [],
        totalAmountInWords: '',
        productData: {},
        checked: [],
        roundOff: true, // Checkbox for rounding off
        totalQty: null,
        totalAmount: null,
        totalAmountInWords: '',
        taxBreakdown: '',
        roundOffAmount: null,
        showAlert: false,
        productCode: '',
        barcode: '',

        // Injected authUserState from Blade
        authUserState: authUserStateFromServer,  // Set from Blade

            selectUserFromOthers() {
            // Function to handle "Others" button click
            this.selectedUserState = this.authUserState;  // Use authUserState if "Others" is clicked
            this.selectUser = true;
        },


            init() {
                // Listen for the 'productFound' event from Livewire
                Livewire.on('productFound', (product) => {
                    this.showAlert = false; // Hide alert if product is found
                    this.barcode = '';
                    this.addProductToRow(product);
                });

                // Listen for the 'productNotFound' event from Livewire
                Livewire.on('productNotFound', () => {
                    this.showAlert = true; // Show alert if product is not found
                });
            },

            addProductToRow(product) {
                // Check if the product already exists in the rows
                let targetRow = this.rows.find(row => row.item === product.item_code);

                if (targetRow) {
                    // If the product exists, update the quantity and other details
                    targetRow.quantity += product.qty || 1; // Increment the quantity
                    targetRow.rate = product.rate || targetRow.rate; // Update the rate if provided

                    // Update detailed product data if details exist
                    if (Array.isArray(product.details)) {
                        product.details.forEach(detail => {
                            const trimmedColumnName = detail.column_name.trim(); // Trim column name
                            const trimmedPanelColumnNames = this.panelUserColumnDisplayNames.map(name => name.trim()); // Ensure panelUserColumnDisplayNames are trimmed

                            if (trimmedPanelColumnNames.includes(trimmedColumnName)) {
                                targetRow[trimmedColumnName] = detail.column_value;
                            }
                        });
                    }
                } else {
                    // If the product does not exist, find an empty row or add a new row
                    targetRow = this.rows.find(row => !row.item && !row.quantity && !row.rate) || this.addRow();

                    // Assign basic product data to the target row
                    targetRow.item = product.item_code;
                    targetRow.quantity = product.qty || 1; // Default to 1 if qty is undefined
                    targetRow.rate = product.rate || 0;    // Default to 0 if rate is undefined

                    // Assign detailed product data if details exist
                    if (Array.isArray(product.details)) {
                        product.details.forEach(detail => {
                            const trimmedColumnName = detail.column_name.trim(); // Trim column name
                            if (this.panelUserColumnDisplayNames.includes(trimmedColumnName)) {
                                targetRow[trimmedColumnName] = detail.column_value;
                            }
                        });
                    }

                    // Ensure all columns in panelUserColumnDisplayNames are populated
                    this.panelUserColumnDisplayNames
                        .filter(columnName => columnName.trim() !== '') // Filter out empty or whitespace strings
                        .forEach(columnName => {
                            const trimmedColumnName = columnName.trim();
                            if (product[trimmedColumnName] !== undefined) {
                                targetRow[trimmedColumnName] = product[trimmedColumnName];
                            }
                        });
                }

                // Calculate total for the target row
                this.calculateTotal(targetRow);
            },
            addRow() {
                const dynamicFields = this.panelUserColumnDisplayNames.reduce((acc, columnName) => {
                    if (columnName !== '') {
                        acc[columnName] = '';
                    }
                    return acc;
                }, {});

                const staticFields = {
                    item: '',
                    quantity: null,
                    rate: null,
                    tax: null,
                    calculateTax: true,
                    total: null,
                };

                const newRow = {
                    ...dynamicFields,
                    ...staticFields
                };

                this.rows.push(newRow);
                return newRow;
            },

            addSelectedProducts() {
                this.selectedProducts.forEach(product => {
                    const emptyRow = this.rows.find(row => !row.quantity && !row.rate && !row.item);

                    if (emptyRow) {
                        emptyRow.item = product.item_code;
                        emptyRow.quantity = product.qty;
                        emptyRow.rate = product.rate;
                        emptyRow.total = product.qty * product.rate;
                    } else {
                        const newRow = {
                            item: product.item_code,
                            quantity: product.qty,
                            rate: product.rate,
                            tax: 0,
                            calculateTax: true,
                            total: product.qty * product.rate
                        };
                        this.rows.push(newRow);
                    }
                });
            },

            deleteRow(index) {
                this.rows.splice(index, 1);
                this.updateTotals();
            },

            calculateTotal(row) {
                const quantity = parseFloat(row.quantity) || 0;
                const rate = parseFloat(row.rate) || 0;
                const tax = parseFloat(row.tax) || 0;

                // Calculate total without tax
                let total = quantity * rate;

                // Apply tax if enabled
                if (this.calculateTax) {
                    total += (total * tax) / 100;
                }

                // Set total for the row
                row.total = total.toFixed(2);

                // Recalculate totals
                this.updateTotals();

                return row.total;
            },

            totalQuantity() {
                return this.rows.reduce((sum, row) => sum + (parseFloat(row.quantity) || 0), 0);
            },

            totalAmountBeforeDiscount() {
                return this.rows.reduce((sum, row) => sum + (parseFloat(row.total) || 0), 0);
            },

            updateTotals() {
                // Recalculate the total quantity
                this.totalQty = this.totalQuantity();

                // Calculate total amount before any discount or round-off
                let totalAmount = this.totalAmountBeforeDiscount();

                // Apply global discount
                const discount = parseFloat(this.discount) || 0;
                const discountAmount = (totalAmount * discount) / 100;
                totalAmount -= discountAmount;

               // Apply round-off if enabled
               this.roundOffAmount = 0;
                        if (this.roundOff) {
                            this.roundOffAmount = Math.round(totalAmount) - totalAmount;
                            totalAmount = Math.round(totalAmount);
                }

                this.totalAmount = totalAmount.toFixed(2);

                // Update tax breakdown
                this.generateTaxBreakdown();

               // Convert to words
               this.totalAmountInWords = this.numberToIndianRupees(totalAmount);
            },

            generateTaxBreakdown() {
            let breakdown = '';
            const discount = parseFloat(this.discount) || 0;
            let showBreakdown = false;

            // Ensure selectedUserState uses authUserState if undefined or empty
            let selectedState = (typeof selectedUserState !== 'undefined' && selectedUserState.trim() !== '')
                                ? selectedUserState.trim().toLowerCase()
                                : this.authUserState.trim().toLowerCase();

            let authState = this.authUserState.trim().toLowerCase();

            // Determine if IGST should be applied
            const applyIGST = selectedState !== authState;

            // Aggregate data by tax rate
            const taxData = {};

            this.rows.forEach((row) => {
                const taxRate = parseFloat(row.tax) || 0;
                if (taxRate === 0) return; // Skip rows with zero tax rate

                const totalWithoutTax = parseFloat(row.total) / (1 + (taxRate / 100));
                const discountedTotal = totalWithoutTax - (totalWithoutTax * discount) / 100;
                const taxAmount = discountedTotal * (taxRate / 100);

                if (!taxData[taxRate]) {
                    taxData[taxRate] = {
                        totalWithoutTax: 0,
                        discountedTotal: 0,
                        taxAmount: 0
                    };
                }

                taxData[taxRate].totalWithoutTax += totalWithoutTax;
                taxData[taxRate].discountedTotal += discountedTotal;
                taxData[taxRate].taxAmount += taxAmount;
            });
                        // Generate breakdown HTML
                        for (const [taxRate, data] of Object.entries(taxData)) {
                            if (discount > 0 || data.totalWithoutTax > 0) {
                                showBreakdown = true;
                                breakdown += `
                                    <tr class="text-xs text-black">
                                        <td class="text-right">Amount at ${taxRate}%:</td>
                                        <td class="text-right">${data.totalWithoutTax.toFixed(2)}</td>
                                    </tr>
                                    ${discount > 0 ? `
                                    <tr class="text-xs text-black">
                                        <td class="text-right">Discount at ${discount}%:</td>
                                        <td class="text-right">${(data.totalWithoutTax * discount / 100).toFixed(2)}</td>
                                    </tr>
                                    <tr class="text-xs text-black">
                                        <td class="text-right font-bold">Amount at ${discount}%:</td>
                                        <td class="text-right font-bold">${data.discountedTotal.toFixed(2)}</td>
                                    </tr>
                                    ` : ''}
                                    ${data.totalWithoutTax > 0 ?
                                        applyIGST ? `
                                        <tr class="text-xs text-black">
                                            <td class="text-right">IGST ${taxRate}%:</td>
                                            <td class="text-right">${data.taxAmount.toFixed(2)}</td>
                                        </tr>
                                        ` : `
                                        <tr class="text-xs text-black">
                                            <td class="text-right">SGST ${(taxRate / 2).toFixed(2)}%:</td>
                                            <td class="text-right">${(data.taxAmount / 2).toFixed(2)}</td>
                                        </tr>
                                        <tr class="text-xs text-black">
                                            <td class="text-right">CGST ${(taxRate / 2).toFixed(2)}%:</td>
                                            <td class="text-right">${(data.taxAmount / 2).toFixed(2)}</td>
                                        </tr>
                                        `
                                    : ''}
                                    <tr class="text-xs text-black">
                                        <td colspan="2" style="height: 10px;"></td>
                                    </tr>
                                `;
                            }
                        }

                        // Handle case where only discount is applied
                        if (discount > 0 && Object.keys(taxData).length === 0) {
                            showBreakdown = true;
                            const totalAmountBeforeDiscount = this.totalAmountBeforeDiscount();
                            breakdown += `
                                <tr class="text-xs text-black">
                                    <td class="text-right">Total Amount:</td>
                                    <td class="text-right">${totalAmountBeforeDiscount.toFixed(2)}</td>
                                </tr>
                                <tr class="text-xs text-black">
                                    <td class="text-right">Discount at ${discount}%:</td>
                                    <td class="text-right">${(totalAmountBeforeDiscount * discount / 100).toFixed(2)}</td>
                                </tr>
                                <tr class="text-xs text-black">
                                    <td class="text-right font-bold">Amount at ${discount}%:</td>
                                    <td class="text-right font-bold">${(totalAmountBeforeDiscount - (totalAmountBeforeDiscount * discount / 100)).toFixed(2)}</td>
                                </tr>
                            `;
                        }

                        if (showBreakdown) {
                            this.taxBreakdown = `
                                <table class="w-full">
                                    ${breakdown}
                                </table>
                            `;
                        } else {
                            this.taxBreakdown = '';
                        }
                    },

                    convertNumberToWords(number) {
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
                            return hundreds + (remainder ? ' and ' + this.convertNumberToWords(remainder) : '');
                        } else if (number < 100000) {
                            const thousands = this.convertNumberToWords(Math.floor(number / 1000)) + ' Thousand';
                            const remainder = number % 1000;
                            return thousands + (remainder ? ' ' + this.convertNumberToWords(remainder) : '');
                        } else if (number < 10000000) {
                            const lakhs = this.convertNumberToWords(Math.floor(number / 100000)) + ' Lakh';
                            const remainder = number % 100000;
                            return lakhs + (remainder ? ' ' + this.convertNumberToWords(remainder) : '');
                        } else {
                            const crores = this.convertNumberToWords(Math.floor(number / 10000000)) + ' Crore';
                            const remainder = number % 10000000;
                            return crores + (remainder ? ' ' + this.convertNumberToWords(remainder) : '');
                        }
                    },

                    numberToIndianRupees(number) {
                        const amountInWords = this.convertNumberToWords(Math.floor(number));
                        const decimalPart = Math.round((number - Math.floor(number)) * 100);

                        if (decimalPart > 0) {
                            const decimalInWords = this.convertNumberToWords(decimalPart);
                            return amountInWords + ' Rupees and ' + decimalInWords + ' Paisa';
                        } else {
                            return amountInWords + ' Rupees';
                        }
                    },

            submitData() {
                const formattedData = this.rows.map(row => {
                    const columns = Object.entries(row)
                        .filter(([key, _]) => this.panelUserColumnDisplayNames.includes(key))
                        .map(([key, value]) => ({
                            column_name: key,
                            column_value: value
                        }));

                    return {
                        p_id: row.item || '',
                        unit: row.unit || null,
                        rate: parseFloat(row.rate) || null,
                        qty: parseFloat(row.quantity) || null,
                        round_off: null,
                        discount: null,
                        total_amount: parseFloat(row.total) || null,
                        tax_percentage: parseFloat(row.tax) || null,
                        discount_total_amount: null,
                        tax_amount: null,
                        tax: parseFloat(row.tax) || null,
                        item_code: null,
                        columns: columns
                    };
                });

                const requestData = {
                    order_details: formattedData,
                    total_qty: this.totalQty,
                    total: this.totalAmount,
                    discount_total_amount: this.discount
                };

                this.$wire.saveRows(requestData);
            },

            editData() {
                const formattedData = this.rows.map(row => {
                    const columns = Object.entries(row)
                        .filter(([key, _]) => this.panelUserColumnDisplayNames.includes(key))
                        .map(([key, value]) => ({
                            column_name: key,
                            column_value: value
                        }));

                    return {
                        p_id: row.item || '',
                        unit: null,
                        rate: parseFloat(row.rate) || null,
                        qty: parseFloat(row.quantity) || null,
                        round_off: null,
                        discount: null,
                        total_amount: parseFloat(row.total) || null,
                        tax_percentage: parseFloat(row.tax) || null,
                        discount_total_amount: null,
                        tax_amount: null,
                        tax: parseFloat(row.tax) || null,
                        item_code: null,
                        columns: columns
                    };
                });

                const requestData = {
                    order_details: formattedData,
                    total_qty: this.totalQty,
                    total: this.totalAmount,
                    discount_total_amount: this.discount
                };

                this.$wire.editRows(requestData);
            },


                    addSelectedDataToInputs() {
                        this.checked.forEach(checkedItem => {
                            const productData = JSON.parse(checkedItem);
                            console.log(productData);

                            // Find an empty row or add a new row if none are empty
                            let targetRow = this.rows.find(row => !row.item && !row.quantity && !row.rate) || this.addRow();

                            // Assign basic product data to the target row
                            targetRow.item = productData.item_code;
                            targetRow.quantity = productData.qty || 1;
                            targetRow.rate = productData.rate || 0;

                            // Assign detailed product data to the target row
                            if (Array.isArray(productData.details)) {
                                productData.details.forEach(detail => {
                                    const trimmedColumnName = detail.column_name.trim(); // Ensure column name is trimmed
                                    console.log(trimmedColumnName);
                                    if (this.panelUserColumnDisplayNames.includes(trimmedColumnName)) {
                                        targetRow[trimmedColumnName] = detail.column_value;
                                    }
                                });
                            }

                            // Ensure all columns in panelUserColumnDisplayNames are populated
                            this.panelUserColumnDisplayNames
                                .filter(columnName => columnName.trim() !== '') // Filter out empty or whitespace-only strings
                                .forEach(columnName => {
                                    const trimmedColumnName = columnName.trim(); // Trim the column name
                                    console.log(trimmedColumnName);
                                    if (productData[trimmedColumnName] !== undefined) {
                                        targetRow[trimmedColumnName] = productData[trimmedColumnName];
                                    }
                                });

                            // Calculate the total for the target row
                            this.calculateTotal(targetRow);
                        });

                        // Clear the checked items
                        this.checked = [];
                    },


            logProductData() {
                console.log(this.productData);
            },

            sendData() {
                this.logProductData();
                const inputField = document.getElementById('productDataInput');
                inputField.value = JSON.stringify(this.productData);
            }
        }
    }
</script> --}}
