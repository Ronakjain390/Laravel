<div class="rounded-lg dark:border-gray-700 mt-4">
    <div class="@if($pdfData->challan_templete == 1 || $pdfData->challan_templete == 5 || $pdfData->challan_templete == 6 )   grid-cols-1 sm:grid-cols-2 @elseif($updateForm == true) grid grid-cols-1 sm:grid-cols-2  @endif gap-2 mb-4">
        <!-- Column 1 -->
        <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
            <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb] @if ($pdfData->challan_templete == 1 || $pdfData->challan_templete == 5 || $pdfData->challan_templete == 6) @if($updateForm == true) grid @endif md:grid-cols-2 @endif">

                {{-- <div >Loading...</div> --}}
                <div x-data="{ addUser: false, inputsDisabled: true }">
                    <div x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                        <!-- Button to toggle dropdown -->
                        <div class="relative" >
                        <button id="dropdownSearchButton small_outlined" data-dropdown-toggle="dropdownSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class="text-black border border-gray-400 flex w-full bg-white hover:bg-orange focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg text-xs px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover-bg-[#050708]/30 mr-2 mb-2"
                            type="button">

                            @if ($receiverName == '')
                                <span x-cloak>@if($updateForm == true) Select Receiver @else Others @endif</span>
                            @elseif ($receiverName === 'Others')
                                <span>Others</span>
                            @else
                                <span>{{ strtoupper($receiverName) }}</span>
                            @endif

                            <!-- Button content -->
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>

                        </button>
                        <label for="small_outlined"
                        class="absolute text-sm rounded-2xl font-bold text-black dark:text-gray-400 duration-300 transform -translate-y-3 scale-75 top-1 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-1 peer-focus:scale-75 peer-focus:-translate-y-3 start-1 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            SEND TO
                        </label>
                        </div>
                        <!-- Dropdown menu -->
                        <div x-data="{ search: '', selectedUser: null }" id="dropdownSearch"
                            class="z-10 hidden bg-white rounded-lg shadow w-72 dark:bg-gray-700">
                            <!-- Search input -->
                            <div class="p-3">
                                <label for="input-group-search" class="sr-only">Search</label>
                                <div class="relative">
                                    {{-- <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-black font-semibold" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                    </div> --}}
                                    <input x-model="search" type="text" id="input-group-search"
                                        class="block w-full p-2 pl-5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search user">
                                </div>
                            </div>
                            <input type="hidden" wire:model="receiverName" style="display: none;">
                            <!-- Filtered list based on search -->
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownSearchButton">
                                @foreach ($billToData as $user)
                                    <li class="cursor-pointer" wire:ignore.self
                                        x-show="search === '' || '{{ strtolower($user->receiver_name ?? null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="selectUser('{{ $user->series_number->series_number ?? 'Not Assigned' }}', '{{ $user->details[0]->address ?? null }}', '{{ $user->details[0]->city ?? null }}', '{{ $user->details[0]->state ?? null }}', '{{ $user->details[0]->pincode ?? null }}', '{{ $user->user->email ?? null }}', '{{ $user->details[0]->phone ?? null }}', '{{ $user->details[0]->gst_number ?? null }}','{{ $user->receiver_name ?? 'Select Receiver' }}', '{{ json_encode($user ?? null) }}')"
                                        x-on:click="selectedUser = '{{ $user->receiver_name ?? 'Select Receiver' }}'; console.log('{{ $user->receiver_name ?? 'Receiver Name is Empty' }}')">
                                        <div class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label wire:ignore class="w-full py-1.5 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $user->receiver_name ?? null }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <button wire:click="updateField"   class="flex w-full items-center p-3 text-xs font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
                                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                    <path d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-2V5a1 1 0 0 0-2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 0 0 2 0V9h2a1 1 0 1 0 0-2Z" />
                                </svg>
                                Others
                            </button>
                        </div>
                        <div id="message-container" class="text-center text-red-500 text-sm "></div>

                    </div>

                    <div class="w-full text-gray-900 dark:text-white">
                        @if($updateForm == true)
                        <div class="grid grid-cols-12 gap-3" x-show="!addUser"  >
                            <div class="col-span-12 md:col-span-6">
                                <div class="grid gap-3">
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs font-semibold w-1/4">Phone</dt>
                                        @if ($selectedUser && $selectedUser['phone'])
                                            <dd class="pl-2 text-xs text-[#686464] text-black capitalize">
                                                {{ $selectedUser['phone'] }}
                                            </dd>
                                        @endif
                                        <dd class="ml-auto text-[0.6rem] inline-block text-[#686464] text-black capitalize md:hidden">
                                            <div class="relative">
                                                @if ($selectedUser == !null)
                                                    <input wire:model.defer="createChallanRequest.challan_date"
                                                        type="date"
                                                        class="bg-gray-50  placeholder: text-gray-900 text-[0.6rem]    focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 h-8 w-auto "
                                                        placeholder="Select date">
                                                @endif
                                            </div>
                                        </dd>
                                    </div>

                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs font-semibold w-1/4">Email</dt>
                                        @if ($selectedUser && $selectedUser['email'])
                                            <dd class="pl-2 text-xs text-black capitalize">
                                                {{ $selectedUser['email'] }}
                                            </dd>
                                            @endif
                                        </div>

                                </div>
                            </div>
                            @unless ($pdfData->challan_templete == 1 || $pdfData->challan_templete == 5 || $pdfData->challan_templete == 6)
                            <div class="col-span-12 md:col-span-6">
                                <div class="grid">
                                    <div class="flex flex-row items-end items-center">
                                        <dt class="w-1/4 mb-1 text-black text-xs font-semibold whitespace-nowrap">
                                            Series No.
                                        </dt>
                                        @if ($selectedUser != null)
                                            <dd class="pl-2 text-xs capitalize flex whitespace-nowrap items-center
                                                @if (isset($selectedUser['challanSeries']))
                                                    @if ($selectedUser['challanSeries'] == 'Not Assigned')
                                                        text-red-700
                                                    @else
                                                        text-black
                                                    @endif
                                                @else
                                                    text-black
                                                @endif">
                                                {{ $selectedUser['challanSeries'] ?? null }}{{ '-' }}
                                                <span class="inline-flex items-center">
                                                    @livewire('series-number-input', ['challanSeries' => $selectedUser['challanSeries'], 'seriesNumber' => $selectedUser['seriesNumber'], 'method' => 'challan'])
                                                </span>
                                            </dd>
                                        @endif
                                    </div>
                                    <div class="flex flex-row mt-1 ">
                                        <dt class="w-1/4 mb-1 text-black text-xs font-semibold ">

                                        </dt>
                                        <dd class="pl-2 text-xs inline-block text-[#686464] text-black capitalize hidden sm:block">
                                            <div class="relative">
                                                @if ($selectedUser == !null)
                                                    <input wire:model.defer="createChallanRequest.challan_date"
                                                        type="date"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                                                        placeholder="Select date">
                                                @endif
                                            </div>
                                        </dd>

                                    </div>
                                </div>

                            </div>
                            @endunless

                            <div class="col-span-12">
                                <div class="col-span-6  grid md:grid-cols-2 gap-4 md:col-span-8">
                                  <div class="flex flex-row items-center">
                                    <dt class="mb-1  w-1/4 text-xs font-semibold text-black">Address</dt>
                                    <dd class="text-xs capitalize ml-3 text-black">
                                        @if(!empty($selectedUser['address']))
                                            {{ $selectedUser['address'] }}
                                        @endif

                                        @if(!empty($selectedUser['city']))
                                            @if(!empty($selectedUser['address'])), @endif
                                            {{ $selectedUser['city'] }}
                                        @endif

                                        @if(!empty($selectedUser['state']))
                                            @if(!empty($selectedUser['address']) || !empty($selectedUser['city'])), @endif
                                            {{ $selectedUser['state'] }}
                                        @endif
                                    </dd>
                                  </div>
                                </div>

                              <div class="flex flex-row mt-3 block sm:hidden">
                                <dt
                                    class="w-1/4 mb-1 text-black text-xs font-semibold  whitespace-nowrap">
                                    Series No.
                                </dt>
                                @if ($selectedUser == !null)
                                <dd class="pl-2 text-xs capitalize
                                @if (isset($selectedUser['challanSeries']))
                                    @if ($selectedUser['challanSeries'] == 'Not Assigned')
                                        text-red-700
                                    @else
                                        text-black
                                    @endif
                                @else
                                    text-black
                                @endif">
                                {{ $selectedUser['challanSeries'] ?? null }}{{ '-' }}
                                <span class="inline-flex items-center">
                                    @livewire('series-number-input', ['challanSeries' => $selectedUser['challanSeries'], 'seriesNumber' => $selectedUser['seriesNumber'], 'method' => 'challan'])
                                </span>
                            </dd>
                                @endif
                            </div>
                            {{-- @dump($selectedUser) --}}
                            @if ($selectedUser != null && !empty($selectedUser['gst']))
                                <div class="flex flex-row mt-3 block sm:hidden">
                                    <dt class="w-1/4 mb-1 text-black text-xs font-semibold whitespace-nowrap">
                                        GST
                                    </dt>
                                    <dd class="text-xs ml-2 capitalize text-black"> {{ $selectedUser['gst'] }} </dd>
                                </div>
                            @endif

                            @if($additionalNumberPermission)
                                        <div class="flex flex-row mt-3 block sm:hidden">
                                            {{-- <dt class="mb-1  w-1/4 text-xs font-semibold text-black">Add Number</dt> --}}
                                                  {{-- <input type="text" placeholder="Additional Number" wire:model.defer="createChallanRequest.additional_phone_number" class="input input-bordered input-sm w-full max-w-xs bg-white border border-gray-400"  @if ($inputsDisabled == true) disabled="" @endif     maxlength="10" />  --}}
                                                  <input @if ($inputsDisabled == true) disabled="" @endif placeholder="Add number"
                                                  wire:model.defer="createChallanRequest.additional_phone_number"
                                                  class="hsn-box h-7 w-32 pl-2 rounded-lg bg-white border border-gray-400 text-xs  text-black focus:outline-none"
                                                  min="0" type="number" maxlength="10"/>
                                        </div>
                                        @endif
                        </div>

                              @unless ($pdfData->challan_templete == 1 || $pdfData->challan_templete == 5 || $pdfData->challan_templete == 6)
                              <div class="col-span-12">
                                <div class="col-span-6 grid md:grid-cols-2 gap-4 md:col-span-8">
                                  <div class="flex flex-row items-center">
                                    <dt class="mb-1  w-1/4 text-xs font-semibold text-black">GST</dt>
                                    <dd class="text-xs ml-2 capitalize text-black">{{ $selectedUser['gst'] ?? null }}</dd>
                                  </div>
                                </div>
                              </div>
                                @endunless
                                @unless ($pdfData->challan_templete == 1 || $pdfData->challan_templete == 5 || $pdfData->challan_templete == 6)

                                @if($additionalNumberPermission)
                                <div class="col-span-12">
                                    <div class="col-span-6 grid md:grid-cols-2 gap-4 md:col-span-8">
                                    <div class="flex flex-row items-center">
                                        {{-- <dt class="mb-1  w-1/4 text-xs font-semibold text-black">Add Number</dt> --}}
                                          {{-- <input type="text" placeholder="Additional Number" wire:model.defer="createChallanRequest.additional_phone_number" class="input input-bordered input-sm w-full max-w-xs bg-white border border-gray-400"  @if ($inputsDisabled == true) disabled="" @endif     maxlength="10" />  --}}
                                          <input @if ($inputsDisabled == true) disabled="" @endif placeholder="Add number"
                                          wire:model.defer="createChallanRequest.additional_phone_number"
                                          class="hsn-box h-7 w-32 ml-2 rounded-lg bg-white border border-gray-400 text-xs  text-black focus:outline-none"
                                          min="0" type="number" maxlength="10"/>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endunless
                              {{-- <div class="col-span-12">
                                <div class="col-span-6 grid md:grid-cols-2 gap-4 md:col-span-8">
                                    <div x-data="{ open: false }" class="flex">

                                        <dt class="mb-1  w-1/4 text-xs font-semibold text-black"  @click="open = ! open">Add Number</dt>
                                        <dd class="text-xs ml-2 capitalize text-black" x-show="open" @click.outside="open = false">Contents</dd>
                                    </div>
                                </div>
                              </div> --}}
                        </div>
                    @endif

                        @if($updateForm == false)
                        <!-- Add user form -->
                        <div class="gap-2 grid grid-cols-12  " >
                            <div class="col-span-12 md:col-span-6"  >
                            <!-- Your add user form goes here -->
                                <div class="grid gap-2" wire:ignore>
                                    <div class="flex flex-row mt-2">
                                        <dt class="mb-1 text-black text-xs  w-24">Name</dt>
                                        <input @if ($inputsDisabled == true)  @endif
                                            wire:model.defer="addReceiverData.receiver_name"
                                            class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                            type="text" maxlength="40" oninput="if(this.value.length > 40) this.value = this.value.slice(0, 40);"   />
                                    </div>
                                    <div class="flex flex-row mt-2">
                                        <dt class="mb-1 text-black text-xs w-24">Phone</dt>
                                        <input @if ($inputsDisabled == true) disabled @endif
                                            wire:model.defer="addReceiverData.phone"
                                            class="hsn-box h-7 w-2/3 text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                            type="number" max="9999999999" maxlength="10"
                                            oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);" />
                                    </div>
                                    <div class="flex flex-row mt-2">
                                        <dt class="mb-1 text-black text-xs  w-24">Email</dt>
                                        <input @if ($inputsDisabled == true)  @endif
                                            wire:model.defer="addReceiverData.email"
                                            class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                            type="email" pattern=".+@gmail\.com" title="Please enter a valid Gmail address" />
                                    </div>
                                    <div class="flex flex-row mt-2">
                                        <dt class="mb-1 text-black text-xs  w-24">Company </dt>
                                        <input @if ($inputsDisabled == true)  @endif
                                            wire:model.defer="addReceiverData.company_name"
                                            class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                            type="text" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-6">

                                <div class="grid gap-2"  wire:ignore>

                                    <div class="flex flex-row mt-2">
                                        <dt class="mb-1 text-black text-xs  w-24">Address</dt>
                                        <input @if ($inputsDisabled == true)  @endif
                                        wire:model.defer="addReceiverData.address"
                                        class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                        type="text" />
                                    </div>
                                    <div class="flex-row mt-2 hidden sm:flex">
                                        <dt class="mb-1 text-black text-xs w-24">Pincode</dt>
                                        <input @if ($inputsDisabled == true) @endif
                                        wire:model.defer="addReceiverData.pincode" oninput="if (this.value.length === 6) { @this.set('addReceiverData.pincode', this.value); @this.call('cityAndStateByPincode'); }"
                                        wire:blur="cityAndStateByPincode"
                                        class="hsn-box h-7 w-1/4  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                        type="text" />

                                        <dt class="mb-1 text-black text-xs mt-1 ml-5 mr-10">City</dt>

                                        <input @if ($inputsDisabled == true)  @endif
                                        wire:model="addReceiverData.city"
                                        class="hsn-box h-7 w-1/4  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                        type="text" />
                                    </div>
                                    <div class="flex mt-2 flex-none sm:hidden">
                                        <dt class="mb-1 text-black text-xs  w-24">Pincode</dt>
                                        <input @if ($inputsDisabled == true) @endif
                                        wire:model.defer="addReceiverData.pincode" oninput="if (this.value.length === 6) { @this.set('addReceiverData.pincode', this.value); @this.call('cityAndStateByPincode'); }"
                                        wire:blur="cityAndStateByPincode"
                                        class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                        type="text" />

                                    </div>
                                    <div class="flex mt-2 flex-none sm:hidden">


                                        <dt class="mb-1 text-black text-xs  w-24">City</dt>

                                        <input @if ($inputsDisabled == true)  @endif
                                        wire:model="addReceiverData.city"
                                        class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                        type="text" />
                                    </div>

                                    <div class="flex flex-row mt-2">

                                       <dt class="mb-1 text-black text-xs  w-24">State</dt>
                                        <input @if ($inputsDisabled == true) @endif
                                        wire:model.defer="addReceiverData.state"
                                        class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                        type="text" />
                                    </div>
                                    <div class="flex flex-row mt-2">
                                        <dt class="mb-1 text-black text-xs  w-24">Gst</dt>
                                        <input @if ($inputsDisabled == true)  @endif
                                        wire:model.defer="addReceiverData.gst_number"
                                        class="hsn-box h-7 w-2/3  text-xs rounded-lg bg-white border border-gray-400 text-black focus:outline-none"
                                        type="text" />
                                    </div>
                                </div>
                            </div>
                            <!-- Add more fields as needed -->
                        </div>

                        @endif

                    </div>

                </div>
                @if($updateForm == true)
                @if( $pdfData->challan_templete == 1 || $pdfData->challan_templete == 5 || $pdfData->challan_templete == 6 && $updateForm == true)
                <div class=" grid-rows-3 ml-20 hidden sm:block ">
                    <div class="flex flex-row">
                        <dt class="w-24 mb-1 text-black text-xs font-semibold my-auto ">
                            Date
                        </dt>
                        <dd class="pl-2 text-xs inline-block  text-black capitalize hidden sm:block">
                            <div class="relative">
                                @if ($selectedUser == !null)
                                    <input wire:model.defer="createChallanRequest.challan_date"
                                        type="date"
                                        class="bg-gray-50 p-1 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                                        placeholder="Select date">
                                @endif
                            </div>
                        </dd>
                    </div>
                    <div class="flex flex-row mt-3">
                        <dt class="w-24 mb-1 text-black text-xs font-semibold whitespace-nowrap">
                            Series No.
                        </dt>
                        @if ($selectedUser != null)
                            <dd class="pl-2 text-xs capitalize
                                @if (isset($selectedUser['challanSeries']))
                                    @if ($selectedUser['challanSeries'] == 'Not Assigned')
                                        text-red-700
                                    @else
                                        text-black
                                    @endif
                                @else
                                    text-black
                                @endif">
                                {{ $selectedUser['challanSeries'] ?? null }}{{ '-' }}
                                <span class="inline-flex items-center">
                                    @livewire('series-number-input', ['challanSeries' => $selectedUser['challanSeries'], 'seriesNumber' => $selectedUser['seriesNumber'], 'method' => 'challan'])
                                </span>
                            </dd>
                        @endif
                    </div>
                    @if ($selectedUser != null && $selectedUser['gst'] != null)
                    <div class="flex flex-row mt-3">
                        <dt
                            class="w-24 mb-1 text-black text-xs font-semibold  whitespace-nowrap">
                            GST
                        </dt>
                        @if ($selectedUser == !null)
                            <dd class="text-xs ml-2 capitalize text-black"> {{ $selectedUser['gst'] ?? null }} </dd>
                        @endif
                    </div>
                    @endif

                    @if($additionalNumberPermission)
                                <div class="flex flex-row mt-3">
                                    {{-- <dt class="mb-1  w-24 text-xs font-semibold text-black">Add Number</dt> --}}
                                          {{-- <input type="text" placeholder="Additional Number" wire:model.defer="createChallanRequest.additional_phone_number" class="input input-bordered input-sm w-full max-w-xs bg-white border border-gray-400"  @if ($inputsDisabled == true) disabled="" @endif     maxlength="10" />  --}}
                                          <input @if ($inputsDisabled == true) disabled="" @endif placeholder="Add number"
                                          wire:model.defer="createChallanRequest.additional_phone_number"
                                          class="hsn-box h-7 w-32 rounded-lg bg-white border border-gray-400 text-xs  text-black focus:outline-none"
                                          min="0" type="number" maxlength="10"/>
                                </div>
                                @endif
                </div>
                @endif
                @endif


            </div>
        </div>
        <div wire:loading    class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <span   class="loading loading-spinner loading-md"></span>
        </div>
        <!-- Column 2 -->
        @unless($pdfData->challan_templete == 1 || $pdfData->challan_templete == 5 || $pdfData->challan_templete == 6)
        @if($updateForm == true )
        <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
            <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                {{-- <h5 class="mb-3 text-base text-[#686464] text-center text-gray-900 md:text-xl dark:text-white">
                    Ship To
                </h5> --}}

                <div x-data="{ search: '', selectedUserDetails: null }" wire:ignore.self>
                    <!-- Button to toggle dropdown -->
                    <div class="relative" >
                    <button id="dropdownDetailSearchButton outlined" data-dropdown-toggle="dropdownDetailSearch"
                        data-dropdown-placement="bottom" data-dropdown-trigger="click"
                        class=" flex w-full text-black border border-gray-400  bg-white focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg text-xs px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                        type="button">
                        @if (!$userSelected)
                            <span x-cloak>Select Address</span>
                        @elseif ($receiverAddress == '')
                            <span>Default Address</span>
                        @else
                            <span>{{ strToUpper($receiverAddress) }}</span>
                        @endif
                        <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <label for="outlined"
                    class="absolute text-sm rounded-2xl font-bold text-black dark:text-gray-400 duration-300 transform -translate-y-3 scale-75 top-1 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-1 peer-focus:scale-75 peer-focus:-translate-y-3 start-1 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
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
                                    <svg class="w-4 h-4 text-black font-semibold" aria-hidden="true"
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
                            {{-- @dd($detail) --}}
                                <li x-show="search === '' || '{{ strtolower($detail->location_name ?? null) }}'.includes(search.toLowerCase())"
                                    wire:click="selectUserAddress('{{ json_encode($detail) }}','{{ json_encode($selectedUserDetails) }}')">
                                    <div
                                        class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label
                                            class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $detail->location_name ?? null }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                </div>
                <div class="grid gap-2">
                    @if ($selectedUser && ($selectedUser['phone'] || $receiverPhone))
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold ">Phone</dt>
                            <dd class="pl-2 text-xs text-[#686464] text-black capitalize">
                                @if ($receiverAddress == '')
                                    {{ $selectedUser['phone'] }}
                                @else
                                    {{ strtoupper($receiverPhone) }}
                                @endif
                            </dd>
                        </div>
                    @endif
                    @if ($selectedUser && $selectedUser['email'])
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold ">Email</dt>
                            <dd class="pl-2 text-xs text-[#686464] text-black capitalize">
                                {{ $selectedUser['email'] }}
                            </dd>
                        </div>
                    @endif

                    @if ($selectedUser && $selectedUser['gst'])
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold ">GST</dt>
                            <dd class="pl-2 text-xs text-[#686464] text-black capitalize">
                                {{ $selectedUser['gst'] }}
                            </dd>
                        </div>
                    @endif
                    <div class="flex flex-row">
                        <dt class="mb-1 text-black text-xs font-semibold ">Address</dt>
                        <dd class="pl-2 text-xs text-[#686464] text-black capitalize">
                            {{-- @if(isset($receiverAddress)) --}}
                            @if ($selectedUser)
                            @if ($receiverAddress == '')
                                    @if(!empty($selectedUser['address']))
                                    {{ $selectedUser['address'] }}
                                @endif

                                @if(!empty($selectedUser['city']))
                                    @if(!empty($selectedUser['address'])), @endif
                                    {{ $selectedUser['city'] }}
                                @endif

                                @if(!empty($selectedUser['state']))
                                    @if(!empty($selectedUser['address']) || !empty($selectedUser['city'])), @endif
                                    {{ $selectedUser['state'] }}
                                @endif
                            @else
                                {{ ucfirst($receiverAddress) }}
                            @endif
                        @endif
                        </dd>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endunless
    </div>
    @if($inputsDisabled == false)
    <div  x-data="{ open: false }">
        @if($disabledButtons)
        <div class="border-b border-gray-300 pb-4">
            <button wire:click.prevent="addRow" @if ($inputsDisabled == true) disabled="" @endif
            class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem] text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">Add New Row</button>
            <!-- Trigger button -->
            @if($availableStock)
            <button @click="open = true" type="button"
                        class="rounded-full bg-yellow-500 px-3 py-1 text-[0.6rem] sm:text-xs shadow-lg text-black hover:bg-yellow-700 sm:h-5 sm:h-7"
                        style="background-color: #e5f811;">
                        <span class="hidden sm:inline">Add From Stock</span>
                        <span class="inline sm:hidden">Stock</span>
            </button>
            @endif
            @if(auth()->user()->barcode)
            <input class="text-black text-xs rounded-lg h-6 w-1/3 sm:w-auto" @if ($inputsDisabled == true) disabled="" @endif wire:model="barcode" type="text" placeholder="Scan Barcode">
            @endif
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

                                </div>
                                <table class="w-full text-xs text-left text-black dark:text-gray-400 overflow-x-auto">
                                    <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">Action</th>
                                        <th scope="col" class="va-b px-2 py-1 text-xs border-2 border-gray-300">S. No.</th>
                                        @foreach ($columnDisplayNames as $index => $columnName)
                                        @if (!empty($columnName))
                                        <th class="va-b px-2 py-2 text-xs border border-gray-300 whitespace-nowrap">
                                            @if ($index >= 3 && $index <= 6)
                                            {{ $index - 2 }} ({{ ucfirst($columnName)}})
                                            @else
                                            {{ ucfirst($columnName) }}
                                            @endif
                                        </th>
                                        @endif
                                        @endforeach
                                    </thead>
                                            @php
                                                $availableStock = json_decode(json_encode($availableStock));
                                            @endphp
                                            <tbody class="stock-table">
                                                @foreach ($availableStock as $key => $product)
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
                                                    @if (!empty($columnDisplayNames[$index]))
                                                    @php
                                                    $column = (object) $column;
                                                    @endphp
                                                    @if ($index > 6)
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
                                                </tr>
                                                @endif
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
        @endif

        <!-- Modal content -->

    </div>
    @endif
    <div class="bg-[#ebebeb] py-2 ">
        <div class="flex flex-col">
            <div class="overflow-x-auto ">
                <div class="min-w-full py-2 align-middle inline-block">
                    <div class="overflow-hidden rounded-lg bg-[#ebebeb] p-3  max-w-full" wire:ignore.self>

                        <table class="min-w-full w-full">
                            <thead>
                                <tr class="border-b border-gray-300 text-left whitespace-nowrap">
                                    <th class="px-2  text-xs text-black">#</th>

                                    @foreach ($panelColumnDisplayNames as $columnName)
                                        @if (!empty($columnName))
                                            <th class="px-2  text-xs text-black">{{ $columnName }}
                                                @if ($columnName == 'Article')
                                                <span class="text-red-500">*</span>
                                            @endif
                                            </th>
                                        @endif
                                    @endforeach


                                    <th class="px-2  text-xs text-black">Unit</th>
                                    {{-- <th class="px-2  text-xs text-black">Details</th> --}}


                                   <th class="px-2  text-xs text-black">Rate</th>
                                    <th class="px-2  text-xs text-black">Qty
                                        <span class="text-red-500">*</span>
                                     <span> @if(isset($errorQty) && $errorQty)
                                        {{-- <span class="text-red-500 text-xs">{{ $errorQty[$index] }}</span> --}}
                                    @endif</span></th>



                                    @if($pdfData->challan_templete == 4 )
                                    <th class="px-2 text-xs text-black">Tax(%)</th>
                                     @endif
                                   <th class="px-2  text-xs text-black">Total Amount</th>
                                    <th class="px-2  text-xs text-black"></th>
                                </tr>
                                @php
                                    $nonEmptyColumnCount = 0;
                                        foreach ($panelColumnDisplayNames as $columnName) {
                                            if (!empty($columnName)) {
                                                $nonEmptyColumnCount++;
                                            }
                                        }
                                    @endphp
                                    @if($pdfData->challan_templete == 4 )
                                    <tr>
                                        <td colspan="{{ $nonEmptyColumnCount + 2 }}"></td>
                                        <td colspan="2"  >
                                            <div class="flex items-center @if($hideWithoutTax == false) hidden @endif">
                                                <label for="calculateTax" class=" ml-2 text-[0.6rem] font-semibold text-black dark:text-gray-300">Without Tax</label>
                                                <input wire:model.defer="calculateTax" id="calculateTax" type="checkbox" @if ($inputsDisabled == true) disabled="" @endif
                                                    class="w-4 h-4 ml-2 text-blue-600  bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            </div>
                                        </td>
                                    </tr>
                                    @endif

                            </thead>
                            <tbody>
                                @php
                                    $errorQtyIndex = [];
                                @endphp
                                {{-- @dump($createChallanRequest['order_details']); --}}
                                @if (isset($createChallanRequest['order_details']))
                                @foreach ($createChallanRequest['order_details'] as $index => $row)
                                    <tr>
                                        <td class="px-1 py-2"><input @if ($inputsDisabled == true) disabled="" @endif
                                                value="{{ $index + 1 }}"
                                                class="hsn-box h-7 resize-x w-10 rounded-lg bg-white border border-gray-400 text-xs  text-black focus:outline-none"
                                                type="text" /></td>

                                        @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                                            @if (!empty($columnName))
                                                @php
                                                    // count($panelColumnDisplayNames)
                                                    $this->createChallanRequest['order_details'][$index]['columns'][$key]['column_name'] = $columnName;
                                                @endphp
                                            <td class="px-1 py-2">
                                                @if($columnName == 'Article')
                                                <div x-data="{ search: '', showDropdown: false }" @click.away="showDropdown = false">
                                                    <!-- Input field with search functionality -->
                                                    <label class="input-sizer" wire:ignore>
                                                        <input type="text"
                                                            @if ($inputsDisabled == true) disabled @endif
                                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                            @keyup="search = $event.target.value.toLowerCase(); showDropdown = true;"
                                                            class="rounded-lg bg-white text-black text-xs h-7 border border-gray-400"
                                                            onInput="this.parentNode.dataset.value = this.value.slice(0, 20)"
                                                            maxlength="20" size="9" />
                                                    </label>
                                                    @error('article.' . $index)
                                                    <div class="text-xs text-red-500">{{ $message }}</div>
                                                    @enderror

                                                    @if($inputsDisabled == false)
                                                    <!-- Filtered list based on search -->
                                                    <ul x-show="search !== '' && showDropdown" class="absolute mt-1 sm:w-72 max-h-64 overflow-auto bg-white rounded-lg shadow-lg z-10" x-cloak x-data="{ hasMatchingResult: false }" x-init="$watch('search', value => { hasMatchingResult = false; })">
                                                        @php
                                                            $showAddButton = false;
                                                        @endphp
                                                        @foreach($availableStock as $product)
                                                            @php
                                                                $product = (object) $product;
                                                                $articleDetails = collect($product->details)->where('column_name', 'Article');
                                                            @endphp
                                                            @foreach($articleDetails as $detail)
                                                                @php
                                                                    $detail = (object) $detail;
                                                                @endphp
                                                                <li class="cursor-pointer p-0.5 hover:bg-gray-100" x-show="'{{ strtolower($detail->column_value) }}'.includes(search)" x-init="$watch('search', value => { if ('{{ strtolower($detail->column_value) }}'.includes(value)) { hasMatchingResult = true; } })">
                                                                    <div class="flex items-center rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                                        <input type="checkbox"
                                                                        class="product-checkbox w-6 h-6 text-blue-600 bg-gray-100 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 border-solid"
                                                                        onclick="window.selectProduct({{ json_encode($product) }}, '{{ $product->id }}')"
                                                                        data-product-id="{{ $product->id }}">
                                                                        <label class="w-full px-2 p-1.5 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $detail->column_value }}</label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        @endforeach
                                                        <!-- Add button placed here, outside the product loop but inside the UL -->
                                                        <!-- Check if there are any matching results -->
                                                        <li class="p-2 sticky bottom-0 bg-white" x-show="hasMatchingResult">
                                                            <button type="button" @click="sendData(); showDropdown = false;" class="text-white bg-gray-900 hover:bg-orange hover:text-black focus:ring-2 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs px-5 py-2.5 text-center dark:hover:bg-orange dark:focus:ring-blue-800 mr-5 ml-auto">Add</button>
                                                        </li>
                                                    </ul>
                                                    @endif
                                                </div>

                                                @else
                                                    {{-- <input
                                                        @if ($inputsDisabled == true) disabled @endif
                                                        wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                        class="hsn-box h-7 w-24 dynamic-width-input rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                                        type="text"
                                                    /> --}}
                                                    <label class="input-sizer" wire:ignore>
                                                        <input type="text"
                                                            @if ($inputsDisabled == true) disabled @endif
                                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                            class="rounded-lg bg-white text-black text-xs h-7"
                                                            onInput="this.parentNode.dataset.value = this.value.slice(0, 20)"
                                                            maxlength="50" size="9" />
                                                    </label>

                                                @endif
                                            </td>
                                            @endif






                                        @endforeach
                                        {{-- <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.5.0/css/pico.min.css"> --}}
                                        {{-- <td class="max-w-sm mx-auto" wire:ignore.self>
                                            <select wire:ignore wire:model="createChallanRequest.order_details.{{ $index }}.unit" class=" bg-white border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block text-xs p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">Select Unit</option>
                                                @foreach (['pcs', 'mtr', 'ltr', 'kg', 'gms', 'cartons', 'mm', 'ml', 'bags', 'dozens'] as $unit)
                                                <option value="{{ $unit }}" @if(isset($createChallanRequest['order_details'][$index]['unit']) && strtolower($createChallanRequest['order_details'][$index]['unit']) == strtolower($unit)) selected @endif>{{ ucfirst($unit) }}</option>

                                                 @endforeach
                                            </select>
                                        </td> --}}

                                        {{-- @php
                                    // Predefined units
                                    $predefinedUnits = [
                                        ['short_name' => 'pcs', 'unit' => 'pieces'],
                                        ['short_name' => 'mtr', 'unit' => 'meters'],
                                        ['short_name' => 'ltr', 'unit' => 'liters'],
                                        ['short_name' => 'kg', 'unit' => 'kilograms'],
                                        ['short_name' => 'gms', 'unit' => 'grams'],
                                        ['short_name' => 'cartons', 'unit' => 'cartons'],
                                        ['short_name' => 'mm', 'unit' => 'millimeters'],
                                        ['short_name' => 'ml', 'unit' => 'milliliters'],
                                        ['short_name' => 'bags', 'unit' => 'bags'],
                                        ['short_name' => 'dozens', 'unit' => 'dozens']
                                    ];

                                    // Merge predefined units with units from the database
                                    $allUnits = array_merge($units, $predefinedUnits);
                                @endphp

                                <td class="max-w-sm mx-auto" wire:ignore.self>
                                    <select wire:ignore wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit" class="bg-white border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block text-xs p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Unit</option>
                                        @foreach ($allUnits as $unit)
                                            @php
                                                $unit = (object) $unit;
                                            @endphp
                                            <option value="{{ $unit->short_name }}" data-display="{{ ucfirst($unit->unit) }} ({{ $unit->short_name }})">{{ $unit->short_name }}</option>
                                        @endforeach
                                    </select>
                                </td> --}}
                                        <td class="max-w-sm mx-auto" wire:ignore.self>
                                            <select wire:ignore wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit" class="bg-white border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block text-xs p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">Select Unit</option>
                                                @foreach ($units as $unit)
                                                @php
                                                    $unit = (object) $unit;
                                                @endphp
                                                <option value="{{ $unit->short_name }}" data-display="{{ ucfirst($unit->unit) }} ({{ $unit->short_name }})">{{ $unit->short_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <script>
                                        document.addEventListener('livewire:load', function () {
                                            let debounceTimer;

                                            function initializeSelect2() {
                                                $('.js-example-basic-single').each(function() {
                                                    if ($(this).data('select2')) {
                                                        $(this).select2('destroy');
                                                    }

                                                    $(this).select2({
                                                        templateResult: formatUnit,
                                                        templateSelection: formatUnitSelection
                                                    }).on('change', function (e) {
                                                        const $select = $(this);
                                                        const value = $select.val();
                                                        const model = $select.attr('wire:model');

                                                        clearTimeout(debounceTimer);
                                                        debounceTimer = setTimeout(() => {
                                                            @this.set(model, value);
                                                            @this.$refresh();
                                                        }, 500); // 500ms debounce
                                                    });
                                                });
                                            }

                                            initializeSelect2();

                                            Livewire.hook('message.processed', (message, component) => {
                                                initializeSelect2();
                                            });

                                            function formatUnit(unit) {
                                                if (!unit.id) {
                                                    return unit.text;
                                                }
                                                var $unit = $('<span>' + unit.text + '</span>');
                                                return $unit;
                                            }

                                            function formatUnitSelection(unit) {
                                                if (!unit.id) {
                                                    return unit.text;
                                                }
                                                var $unit = $('<span>' + unit.id + '</span>');
                                                return $unit;
                                            }
                                        });
                                        </script>
                                            {{-- <script>
                                            document.addEventListener('alpine:init', () => {
                                                Alpine.data('searchInput', (units, index) => ({
                                                    isOpen: false,
                                                    search: "",
                                                    sourceData: units,
                                                    selectedUnit: "",
                                                    rowIndex: index,

                                                    init() {
                                                        this.$watch('search', value => {
                                                            this.$wire.set(`createChallanRequest.order_details.${this.rowIndex}.unit`, value, true);
                                                        });
                                                    },

                                                    get getItems() {
                                                        const filterItems = this.sourceData.filter((item) => {
                                                            return item.unit.toLowerCase().startsWith(this.search.toLowerCase());
                                                        });

                                                        this.isOpen = filterItems.length < this.sourceData.length && filterItems.length > 0;
                                                        return filterItems;
                                                    },

                                                    updateSearch(event) {
                                                        this.search = event.target.value;
                                                        this.selectedUnit = event.target.value;
                                                    },

                                                    selectUnit(unit) {
                                                        this.selectedUnit = unit;
                                                        this.search = unit;
                                                        this.$refs.details.removeAttribute('open'); // Close the details element
                                                        this.$nextTick(() => {
                                                            this.$wire.set(`createChallanRequest.order_details.${this.rowIndex}.unit`, unit, true);
                                                        });
                                                    },

                                                    cleanSearch(e) {
                                                        this.search = "";
                                                        this.$wire.set(`createChallanRequest.order_details.${this.rowIndex}.unit`, "", true);
                                                    },

                                                    closeSearch() {
                                                        this.$refs.details.removeAttribute('open'); // Close the details element
                                                    }

                                                }));

                                            });
                                            </script> --}}

                                            {{-- @foreach ($createChallanRequest['order_details'] as $index => $row) --}}
                                            {{-- <button wire:click.prevent="toggleInput" wire:click.prevent="dispatchBrowserEvent('inputToggled', {showInput: true})">Open Input</button> --}}
                                        <td hidden class="px-1 py-2">
                                            <input @if ($inputsDisabled == true) disabled="" @endif
                                                class="hsn-box h-7 w-25 rounded-lg bg-white border border-gray-400 text-xs  text-black focus:outline-none"
                                                type="text" />
                                        </td>

                                        <td class="px-2 py-2">
                                            <input @if ($inputsDisabled == true) disabled="" @endif
                                                class="rate hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs  text-black focus:outline-none"
                                                min="0" type="number" wire:model.defer="createChallanRequest.order_details.{{ $index }}.rate" data-index="{{ $index }}"/>
                                        </td>

                                        <td class="px-2 py-2">
                                            <input @if ($inputsDisabled == true) disabled="" @endif
                                                class="qty hsn-box h-7 w-24 flex rounded-lg  text-center  text-xs  text-black  @if(isset($errorQty[$index]) && $errorQty[$index]) bg-red-500 @else bg-white border border-gray-400 @endif "
                                                min="1" type="number" wire:model.defer="createChallanRequest.order_details.{{ $index }}.qty" data-index="{{ $index }}" />
                                                @error('qty.' . $index)
                                                <div class="text-xs text-red-500">{{ $message }}</div>
                                                @enderror
                                            </td>

                                        @if($pdfData->challan_templete == 4 )
                                            <td class="px-2 py-2 ">
                                                <input @if ($inputsDisabled == true) disabled="" @endif class=" tax hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none" min="0" type="number" wire:model.defer="createChallanRequest.order_details.{{ $index }}.tax" data-index="{{ $index }}" data-tax-index="{{ $index }}"/>
                                            </td>
                                        @endif
                                        {{-- Item Code Hidden Field --}}
                                        <input type="hidden" wire:model.defer="createChallanRequest.order_details.{{ $index }}.item_code" value="{{ $row['item_code'] }}" />


                                        <!-- Total -->
                                        <td class="px-2 py-2" wire:ignore>
                                            <input @if ($inputsDisabled == true) disabled @endif x-ignore
                                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.total_amount"
                                                class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none total"
                                                type="number" data-index="{{ $index }}" readonly/>
                                        </td>
                                        <td class="px-2 py-2">
                                            <button type="button" wire:click.prevent="removeRow({{ $index }})"
                                                class="bg-yellow-500 px-2 py-1 text-sm text-black hover:bg-yellow-700"
                                                style="background-color: #e5f811;">X</button>
                                        </td>
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
                                        <td colspan="{{ $nonEmptyColumnCount + 3 }}"></td>
                                        <td colspan="2">
                                            <div class="flex items-center">
                                                @if(isset($errorQty[$index]) && $errorQty[$index])
                                                    <span class="text-red-500 text-[0.6rem]">Less Stock Available</span>
                                                    @php
                                                        $errorQtyIndex[$index] = true;
                                                    @endphp
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif

                            </tbody>
                        </table>

                        @foreach ($createChallanRequest['order_details'] as $index => $row)
                        <div id="totals-container" class="text-right text-xs text-black mr-5" wire:ignore></div>
                        @endforeach

                        {{-- <script>
                                function debounce(func, wait) {
                                    let timeout;
                                    return function (...args) {
                                        clearTimeout(timeout);
                                        timeout = setTimeout(() => func.apply(this, args), wait);
                                    };
                                }

                                window.onload = function () {
                                    document.getElementById('calculateTax').checked = true;
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

                                    const discount = parseFloat(document.querySelector('.discount').value) || 0;
                                    const discountAmount = (totalAmount * discount) / 100;
                                    const discountedTotalAmount = totalAmount - discountAmount;

                                    document.querySelectorAll('.qty').forEach((input, index) => {
                                        const rate = parseFloat(document.querySelector(`.rate[data-index="${index}"]`).value) || 0;
                                        const qty = parseFloat(document.querySelector(`.qty[data-index="${index}"]`).value) || 0;
                                        let tax = parseFloat(document.querySelector(`.tax[data-index="${index}"]`).value) || 0;
                                        let totalWithoutTax = rate * qty;

                                        if (!document.getElementById('calculateTax').checked) {
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
                                    });

                                    for (const tax in rowTotals) {
                                        const totalWithoutTax = (rowTotals[tax] / (1 + (tax / 100))).toFixed(2);
                                        const discountedTotalAmounts = (totalWithoutTax - rowDiscounts[tax]).toFixed(2);
                                        const taxAmounts = rowTaxAmounts[tax].toFixed(2);

                                        totalsContainer.innerHTML += `
                                            <div>
                                                ${tax > 0 ? `<span>Sales at ${tax}%: ${totalWithoutTax}</span><br>
                                                ${rowDiscounts[tax] > 0 ? `<span>Discount at ${discount}%: ${rowDiscounts[tax].toFixed(2)}</span><br>
                                                <span>Net Sale: ${discountedTotalAmounts}</span><br>` : ''}
                                                <span>SGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span><br>
                                                <span>CGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span>` : ''}
                                            </div> &nbsp`;
                                    }

                                    const totalQtyField = document.querySelector('.totalQtyField');
                                    if (totalQtyField) {
                                        totalQtyField.value = totalQty;
                                    }

                                    const totalAmountField = document.querySelector('.totalAmountField');
                                    if (totalAmountField) {
                                        totalAmountField.value = discountedTotalAmount.toFixed(2);
                                    }

                                   // Update hidden inputs to ensure values are submitted with the form
                                    const hiddenTotalQtyField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.total_qty"]');
                                    // console.log(hiddenTotalQtyField);
                                    if (hiddenTotalQtyField) {
                                        hiddenTotalQtyField.value = totalQty;
                                        // Trigger Livewire to recognize the change
                                        hiddenTotalQtyField.dispatchEvent(new Event('input'));
                                    }
                                    // var totalQtyField = document.querySelector('.total_qty');
                                    // var hiddenTotalQtyField = document.querySelector('input[type="hidden"][wire\\:model="createChallanRequest.total_qty"]');
                                    // var totalAmountField = document.querySelector('.total_amount');
                                    // var hiddenTotalField = document.querySelector('input[type="hidden"][wire\\:model="createChallanRequest.total"]');


                                    const hiddenTotalField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.total"]');
                                    // console.log(hiddenTotalField);
                                    if (hiddenTotalField) {
                                        hiddenTotalField.value = discountedTotalAmount.toFixed(2);
                                        // Trigger Livewire to recognize the change
                                        hiddenTotalField.dispatchEvent(new Event('input'));
                                    }
                                }

                                document.addEventListener('DOMContentLoaded', function () {
                                    document.body.addEventListener('input', debounce(calculateTotal, 300));
                                    document.getElementById('calculateTax').addEventListener('change', calculateTotal);
                                });

                                function calculateTotal(event) {
                                    if (event.target.matches('.rate, .qty, .tax, .total, .discount') || event.target.id === 'calculateTax') {
                                        document.querySelectorAll('.qty').forEach((input, index) => {
                                            const rate = parseFloat(document.querySelector(`.rate[data-index="${index}"]`).value) || 0;
                                            const qty = parseFloat(document.querySelector(`.qty[data-index="${index}"]`).value) || 0;
                                            const tax = parseFloat(document.querySelector(`.tax[data-index="${index}"]`).value) || 0;
                                            const discount = parseFloat(document.querySelector('.discount').value) || 0;
                                            let totalWithoutTax = rate * qty;
                                            const taxAmount = (totalWithoutTax * tax) / 100;

                                            let total;

                                            if (document.getElementById('calculateTax').checked) {
                                                total = totalWithoutTax + taxAmount;
                                            } else {
                                                total = totalWithoutTax;
                                                totalWithoutTax = parseFloat((totalWithoutTax * 100 / (100 + tax)).toFixed(2));
                                            }

                                            document.querySelector(`.total[data-index="${index}"]`).value = total.toFixed(2);
                                        });

                                        updateTotals();
                                    }
                                }
                        </script> --}}
                        <script>
                            function debounce(func, wait) {
                                let timeout;
                                return function (...args) {
                                    clearTimeout(timeout);
                                    timeout = setTimeout(() => func.apply(this, args), wait);
                                };
                            }

                            // window.onload = function () {
                            //     const calculateTaxCheckbox = document.getElementById('calculateTax');
                            //     if (calculateTaxCheckbox) {
                            //         calculateTaxCheckbox.checked = true;
                            //     }
                            //     updateTotals();
                            // };

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
                            });

                            for (const tax in rowTotals) {
                                const totalWithoutTax = (rowTotals[tax] / (1 + (tax / 100))).toFixed(2);
                                const discountedTotalAmounts = (totalWithoutTax - rowDiscounts[tax]).toFixed(2);
                                const taxAmounts = rowTaxAmounts[tax].toFixed(2);

                                totalsContainer.innerHTML += `
                                    <div>
                                        ${tax > 0 ? `<span>Sales at ${tax}%: ${totalWithoutTax}</span><br>
                                        ${rowDiscounts[tax] > 0 ? `<span>Discount at ${discount}%: ${rowDiscounts[tax].toFixed(2)}</span><br>
                                        <span>Net Sale: ${discountedTotalAmounts}</span><br>` : ''}
                                        <span>SGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span><br>
                                        <span>CGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span>` : ''}
                                    </div> &nbsp`;
                            }

                            const totalQtyField = document.querySelector('.totalQtyField');
                            if (totalQtyField) {
                                totalQtyField.value = totalQty;
                            }

                            let finalTotalAmount = discountedTotalAmount;
                            const roundOffCheckbox = document.getElementById('vue-checkbox-list');
                            let roundOffAmount = 0;

                            if (roundOffCheckbox && roundOffCheckbox.checked) {
                                roundOffAmount = Math.round(discountedTotalAmount) - discountedTotalAmount;
                                finalTotalAmount = Math.round(discountedTotalAmount);
                            }

                            const totalAmountField = document.querySelector('.totalAmountField');
                            if (totalAmountField) {
                                totalAmountField.value = finalTotalAmount.toFixed(2);
                            }

                            // Update hidden inputs to ensure values are submitted with the form
                            const hiddenTotalQtyField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.total_qty"]');
                            if (hiddenTotalQtyField) {
                                hiddenTotalQtyField.value = totalQty;
                                // Trigger Livewire to recognize the change
                                hiddenTotalQtyField.dispatchEvent(new Event('input'));
                            }

                            const hiddenTotalField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.total"]');
                            if (hiddenTotalField) {
                                hiddenTotalField.value = finalTotalAmount.toFixed(2);
                                // Trigger Livewire to recognize the change
                                hiddenTotalField.dispatchEvent(new Event('input'));
                            }

                            const hiddenRoundOffField = document.querySelector('input[type="hidden"][wire\\:model\\.defer="createChallanRequest.round_off"]');
                            if (hiddenRoundOffField) {
                                hiddenRoundOffField.value = roundOffAmount.toFixed(2);
                                hiddenRoundOffField.dispatchEvent(new Event('input'));
                            }

                            // Update total amount in words input field
                            const totalAmountInWords = numberToIndianRupees(finalTotalAmount);
                            const totalAmountInWordsInput = document.getElementById('totalAmountInWords');
                            if (totalAmountInWordsInput) {
                                totalAmountInWordsInput.value = totalAmountInWords;
                            }

                            // Update round-off amount input field
                            const roundOffAmountInput = document.getElementById('roundOffAmount');
                            if (roundOffAmountInput) {
                                roundOffAmountInput.value = roundOffAmount.toFixed(2);
                            }
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
                                    setInterval(updateTotals, 1000);
                        </script>


                    </div>
                </div>
            </div>
        </div>
       {{-- @foreach ($errorQtyIndex as $in => $result)

       @endforeach --}}

        @if ($challanSave)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="alert-border-3" class="p-2 mb-4 text-xs text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <span class="font-medium">Success:</span> {{ $challanSave }}
            </div>

        @endif
        <div class="flex justify-center space-x-4 lg:text-lg text-xs m-2" >
            {{-- Save Button --}}
            {{-- Save Button --}}
            @php
                $mainUser = json_decode($mainUser);
            @endphp
            @if ($mainUser->team_user != null)
                @if ($mainUser->team_user->permissions->permission->sender->create_challan == 1)
                    <button type="button"
                        @if ($action == 'save') wire:click.prevent='challanCreate' @elseif($action == 'edit') wire:click.prevent='challanModify' @endif
                        @if ($inputsDisabled == true || (isset($errorQty[$index]) && $errorQty[$index])) disabled @endif
                        @if ($inputsResponseDisabled == false) disabled @endif
                        @if($disabledButtons == false) disabled @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == false || ($inputsDisabled == true || (isset($errorQty[$index]) && $errorQty[$index]))) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2"
                        wire:loading.attr="disabled" wire:target="challanCreate, challanModify"
                        wire:click="disableSaveButton">
                        Save
                    </button>
                @endif
            @else
                <button type="button"
                    @if ($action == 'save') wire:click.prevent='challanCreate' @elseif($action == 'edit') wire:click.prevent='challanModify' @endif
                    @if ($inputsDisabled == true || in_array($index, $errorQtyIndex)) disabled @endif
                    @if ($inputsResponseDisabled == false) disabled @endif
                    @if($disabledButtons == false) disabled @endif
                    class="rounded-full btn-size
                    @if (in_array($index, $errorQtyIndex))
                        bg-gray-300 text-black
                    @elseif ($inputsResponseDisabled == true)
                        bg-gray-900 text-white
                    @else
                        bg-gray-300 text-black
                    @endif
                    lg:px-8 px-4 px-4 py-2">
                    Save
                </button>
            @endif

            {{-- Edit Button --}}
            @if ($mainUser->team_user != null)
                @if ($mainUser->team_user->permissions->permission->sender->modify_challan == 1)
                    <button wire:click.prevent='challanEdit' type="button"
                        @if ($inputsResponseDisabled == true) disabled="" @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gr px-4ay-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
                @endif
            @else
                <button wire:click.prevent='challanEdit' type="button"
                    @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
            @endif

            @if($sendButtonDisabled == true)
            {{-- Send Button --}}
            @if ($mainUser->team_user != null)
                @if ($mainUser->team_user->permissions->permission->sender->send_challan == 1)
                    <button wire:click.prevent='sendChallan({{ $challanId }})'
                        @if ($inputsResponseDisabled == true) disabled="" @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
                @endif
            @else
                <button wire:click.prevent='sendChallan({{ $challanId }})'
                    @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
            @endif
            @endif
            {{-- @dump($teamMembers) --}}
            @if($teamMembers != null)
            {{-- SFP Button --}}
            <button wire:click.prevent='updateVariable(`challan_id`,{{ $challanId }})' onclick="my_modal_1.showModal()" href="javascript:void(0);"

            @if ($inputsResponseDisabled == true) disabled="" @endif
            class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>

            @endif
        </div>
    </div>



<script>
document.addEventListener("DOMContentLoaded", function() {
const articleFilter = document.getElementById("article-filter");
const unitFilter = document.getElementById("unit-filter");
const tableRows = document.querySelectorAll(".stock-table tr");

if (articleFilter) {
    articleFilter.addEventListener("change", function() {
        const selectedArticle = articleFilter.value.toLowerCase();

        tableRows.forEach((row) => {
            const articleCell = row.querySelector(".Article");
            if (!selectedArticle || articleCell.textContent.toLowerCase().includes(selectedArticle)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
}
function showMessage(message) {
    // Assuming you have an element with the id "message-container"
    var messageContainer = document.getElementById("message-container");
    if (messageContainer) {
        messageContainer.innerText = message;
    }
}
  // Function to show the button after page load
function showAddButton() {
    document.getElementById('addButton').style.display = 'block';
}
// Add an event listener for page load
window.addEventListener('load', showAddButton);

if (unitFilter) {
    unitFilter.addEventListener("change", function() {
        const selectedUnit = unitFilter.value.toLowerCase();

        tableRows.forEach((row) => {
            const unitCell = row.querySelector(".Unit");
            if (!selectedUnit || unitCell.textContent.toLowerCase() === selectedUnit) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
}
});

document.addEventListener('input', function(event) {
if (event.target.classList.contains('dynamic-width-input')) {
    autoAdjustWidth(event.target);
}
});

function autoAdjustWidth(input) {
input.style.width = '24';
input.style.width = (input.scrollWidth + 1) + 'px';
}


// Event listener to detect tab change and reinitialize dropdown
document.addEventListener('livewire:load', function () {
Livewire.hook('message.processed', (message, component) => {
initFlowbite();
});
});
const multiSelectInput = document.getElementById('multi-select-input');
const multiSelectDropdown = document.getElementById('multi-select-dropdown');
const options = multiSelectDropdown.querySelectorAll('input[type="checkbox"]');
const selectedValues = [];

multiSelectInput.addEventListener('click', () => {
multiSelectDropdown.classList.toggle('hidden');
});

options.forEach(option => {
option.addEventListener('change', () => {
if (option.checked) {
    selectedValues.push({ id: option.value, name: option.dataset.name });
} else {
    const index = selectedValues.findIndex(item => item.id === option.value);
    if (index !== -1) {
        selectedValues.splice(index, 1);
    }
}
multiSelectInput.value = selectedValues.map(item => item.name).join(', ');
});
});

document.addEventListener('click', (e) => {
if (!multiSelectInput.contains(e.target) && !multiSelectDropdown.contains(e.target)) {
multiSelectDropdown.classList.add('hidden');
}
});
</script>
<dialog id="my_modal_1" class="modal" wire:ignore>
<div class="modal-box  ">
<form method="dialog" class="h-full">
    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>

<h3 class="font-bold text-lg">Send For Processing</h3>
<div class="grid grid-cols-2 gap-4 mt-2 text-xs">
    <!-- Left side (Dropdown) -->
    <div class="relative">
        <input
            id="multi-select-input"
            class="w-full px-4 py-2 h-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white border border-gray-400"
            placeholder="Select Team Members..."
            readonly
        >
        <div
            id="multi-select-dropdown"
            class="absolute z-10 w-full mt-1 text-xs bg-white rounded-md shadow-lg hidden"
            >
            <div class="max-h-60 overflow-y-auto">
                <ul class="py-1">
                    @if (isset($teamMembers) && is_array($teamMembers))
                        @php
                            $addedOwners = [];
                            function arrayToObject($array) {
                                if (is_array($array)) {
                                    return (object) array_map('arrayToObject', $array);
                                }
                                return $array;
                            }
                        @endphp
                        @foreach ($teamMembers as $team)
                            @php $team = arrayToObject($team); @endphp
                            @if ($team !== null && $team->id !== auth()->id())
                                <li>
                                    <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            value="{{ $team->id }}" wire:model.defer="team_user_ids"
                                            data-name="{{ $team->team_user_name }}"
                                            class="multi-select-option form-checkbox h-5 w-5 border border-solid text-blue-500"
                                            x-on:change="selected = $el.checked ? [...selected, $el.value] : selected.filter(id => id !== $el.value); isButtonEnabled = selected.length > 0"
                                        >
                                        <span class="ml-2 text-gray-700">{{ $team->team_user_name }}</span>
                                    </label>
                                </li>
                                @if (isset($team->owner) && !in_array($team->owner->id, $addedOwners) && $team->owner->id !== auth()->id())
                                    <li>
                                        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                value="{{ $team->owner->id }}" wire:model.defer="admin_ids"
                                                data-name="Admin"
                                                class="multi-select-option form-checkbox h-5 w-5 text-blue-500"
                                                x-on:change="selected = $el.checked ? [...selected, $el.value] : selected.filter(id => id !== $el.value); isButtonEnabled = selected.length > 0"
                                            >
                                            <span class="ml-2 text-gray-700">Admin</span>
                                        </label>
                                    </li>
                                    @php $addedOwners[] = $team->owner->id; @endphp
                                @endif
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Right side (Comment box) -->
    <div class="relative">
        <textarea
            wire:model.defer="comment"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Comment" maxlength="100" oninput="if(this.value.length > 100) this.value = this.value.slice(0, 100);"
        ></textarea>
        <div class="text-center mt-2 text-[0.6rem]">Less than 100 words only</div>
    </div>
</div>


<div class="flex justify-end">
    <button  type="button" wire:click='sfpChallan'
        class="text-white btn btn-sm mt-6 btn-circle btn-ghost bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs border-2 border-gray-300 px-10 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
</div>
</div>
</dialog>

<script>
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
<style>
.input-sizer {
display: inline-grid;
vertical-align: top;
align-items: center;
/* position: relative; */
/* padding: .25em .5em; */
margin: 1px;
width: max-content;
}

.input-sizer::after,
input {
width: auto;
min-width: 1em;
grid-area: 1 / 2;
font: inherit;
padding: 0.25em;
margin: 0;
resize: none;
background: none;
appearance: none;
border: none;
}

.input-sizer::after {
content: attr(data-value) ' ';
visibility: hidden;
white-space: pre-wrap;
}
</style>


</div>
