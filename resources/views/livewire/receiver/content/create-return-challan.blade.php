<div x-data="invoiceComponent('{{ auth()->user() }}', {{ json_encode($panelUserColumnDisplayNames) }}, {{ json_encode($rows) }}, '{{ $context}}' )"
    x-init="$watch('rows', value => $wire.set('rows', value, true))" >

    @php
        $mainUser = json_decode($this->mainUser);
        $panel = Session::get('panel');
        // $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;        $permission = Auth::user()->permissions;
        $hideSuccessMessage = true;
    @endphp

     <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <!-- Column 1 -->
        <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
            <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                    Receiver
                </h5>

                <div class="w-full text-gray-900 dark:text-white">

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <div class="grid gap-2">
                                {{-- <div class="flex flex-row">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Date</dt>
                                    <dd class="pl-2 text-xs  text-black capitalize">
                                        {{ date('j, F , Y') }}</dd>

                                </div> --}}
                                {{-- @dd($challanSeries); --}}
                                @php
                                    $selectedUser = json_decode($selectedUser);
                                @endphp
                                <div class="flex flex-row">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Challan No.</dt>
                                    <dd
                                        class="pl-2 text-xs  capitalize @if (isset($selectedUser->series_number->series_number)) @if ($selectedUser->series_number->series_number == 'Not Assigned') text-red-700 @else text-black @endif
                                @else
                                text-black @endif">
                                        {{ $challanSeries ?? null }}-{{ $selectedUser->series_num ?? null }}
                                    </dd>
                                </div>

                                <div class="flex flex-row ">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Name</dt>
                                    <dd class="pl-2 text-xs  text-black capitalize">

                                        {{ Auth::guard(Auth::getDefaultDriver())->user()->name ?? null }}</dd>
                                </div>

                                <div class="flex flex-row ">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Address</dt>
                                    <dd class="pl-2 text-xs  text-black capitalize">
                                        {{ Auth::guard(Auth::getDefaultDriver())->user()->address ?? null }}</dd>
                                </div>


                                <div class="flex flex-row ">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Email</dt>
                                    <dd class="pl-2 text-xs  text-black capitalize">
                                        {{ Auth::guard(Auth::getDefaultDriver())->user()->email ?? null }}</dd>
                                </div>
                                <div class="flex flex-row ">
                                    <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Phone</dt>
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
                    Sender
                </h5> --}}
                <div x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                    <!-- Button to toggle dropdown -->
                    <div class="relative" >
                    <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch"
                        data-dropdown-placement="bottom" data-dropdown-trigger="click"
                        class="  flex w-full bg-white border border-gray-400 text-xs hover:bg-orange  text-black focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg   px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                        type="button">
                        {{-- Click To Choose --}}
                        @if ($senderName == '')
                                <span x-cloak>  Select Sender  </span>
                            @elseif ($senderName === 'Others')
                                <span>Others</span>
                            @else
                                <span>{{ strtoupper($senderName) }}</span>
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
                    <div x-data="{ search: '', selectedUser: null }" id="dropdownSearch"
                        class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
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
                                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search user">
                            </div>
                        </div>
                        {{-- @dd($Senders); --}}
                        <!-- Filtered list based on search -->
                        <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                            aria-labelledby="dropdownSearchButton">
                            @foreach ($Senders as $user)
                            {{-- @dump($user) --}}
                                {{-- {{dump($user)}} --}}
                                <li class="cursor-pointer"
                                    x-show="search === '' || '{{ strtolower($user->sender ?? null) }}'.includes(search.toLowerCase())"
                                    wire:click.prevent="selectUser('{{ $user->sender ?? '' }}', '{{ $user->phone ?? null }}', '{{ $user->email ?? null }}', '{{ $user->address ?? null }}', '{{ $user->gst_number ?? null }}','{{ $user->sender_id ?? null }}')"
                                    x-on:click = "selectUser = true; selectedUserState = '{{ $user->details[0]->state ?? null }}'; authUserState = '{{ $authUserState }}';"
                                    >
                                    <div
                                        class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                        <label
                                            class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $user->sender ?? null }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="grid gap-2">
                    <div class="flex flex-row">
                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Phone</dt>
                        <dd class="pl-2 text-xs  text-black capitalize">
                            {{ $selectedUser->phone ?? null }}
                        </dd>
                    </div>
                    <div class="flex flex-row">
                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Email</dt>
                        <dd class="pl-2 text-xs  text-black capitalize">
                            {{ $selectedUser->email ?? null }}
                        </dd>
                    </div>
                    <div class="flex flex-row">
                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">GST</dt>
                        <dd class="pl-2 text-xs  text-black capitalize">
                            {{ $selectedUser->gst_number ?? null }}</dd>
                    </div>
                    <div class="flex flex-row">
                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Address</dt>
                        <dd class="pl-2 text-xs  text-black capitalize">
                            {{ $selectedUser->address ?? null }}</dd>
                    </div>
                    <div class="flex flex-row">
                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Date</dt>
                        @if ($selectedUser == !null)
                        <input wire:model.defer="createChallanRequest.challan_date"
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

    <div x-data="{ open: false }" class=" m-2 text-xs">
        <div class="">
            <button class="rounded-full bg-yellow-500 px-3 py-1 text-[0.6rem] sm:text-xs shadow-lg text-black hover:bg-yellow-700 sm:h-5 sm:h-7"
            style="background-color: #e5f811;" @click="addRow()"  x-show="selectUser" >
            <span class="hidden sm:inline">Add New Row</span>
            <span class="inline sm:hidden">+</span>
            </button>

            <div class="bg-[#ebebeb] rounded-lg shadow  overflow-x-auto text-xs">

                <div class="flex flex-col w-full overflow-auto">
                    <!-- Table Headers -->
                    <div class="flex items-center gap-1">
                    <div class="w-10 p-2 pr-0">
                        <span class="block text-xs font-semibold text-black">#</span>
                    </div>

                    <!-- Dynamic Fields Headers -->
                    <template x-for="(columnName, key) in panelUserColumnDisplayNames.filter(name => name !== '')" :key="key">
                        <div class="w-24 p-2 flex-grow">
                        <label x-text="columnName" class="block text-xs w-24 sm:w-full font-semibold text-black"></label>
                        </div>
                    </template>

                    <!-- Static Fields Headers -->
                    <div class="w-24 p-2 flex-grow">
                        <label class="block text-xs w-24 sm:w-full font-semibold text-black">Unit</label>
                    </div>
                    <div class="w-24 p-2 flex-grow">
                        <label class="block text-xs w-24 sm:w-full font-semibold text-black">Rate</label>
                    </div>
                    {{-- @if($pdfData->challan_templete == 4 )
                    <div class="w-24 p-2 flex-grow">
                        <label class="block text-xs w-24 sm:w-full font-semibold text-black">Tax (%)</label>
                    </div>
                    @endif --}}

                    <div class="w-24 p-2 flex-grow">
                        <label class="block text-xs w-24 sm:w-full font-semibold text-black">Qty</label>
                    </div>
                    <div class="w-24 p-2 flex-grow">
                        <label class="block text-xs w-24 sm:w-full font-semibold text-black">Total Amount</label>
                    </div>
                    </div>
                    <div class="flex items-center gap-1 ">
                    <!-- Dynamic Fields Placeholder for alignment -->
                    <template x-for="(columnName, key) in panelUserColumnDisplayNames.filter(name => name !== '')" :key="key">
                        <div class="w-24 flex-grow"></div>
                    </template>

                    <div class="font-semibold text-black w-10"></div>

                    <!-- Placeholder for Tax -->
                    {{-- <div class="w-24 flex-grow"></div> --}}
                    <!-- Total Quantity -->
                    {{-- @if($pdfData->challan_templete == 4 )
                    <div class="w-24 sticky bottom-0 flex-grow">
                        <label for="withoutTax" class="ml-2 text-[0.6rem] font-semibold text-black dark:text-gray-300">Without Tax</label>
                        <input x-model="calculateTax" type="checkbox"
                            class="w-4  ml-2 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    @endif --}}

                    <!-- Placeholder for Unit -->
                    {{-- <div class="w-24 flex-grow"></div> --}}

                    <!-- Placeholder for Rate -->
                    <div class="w-24 flex-grow"></div>

                    <!-- Total Amount -->
                    <div class="w-24 sticky bottom-0 flex-grow">
                        {{-- <label class="block text-xs text-black font-semibold">Total Amount</label> --}}
                    </div>

                    <div class="w-10 "></div>
                </div>
                    <!-- Table Rows -->
                    <template x-for="(row, index) in rows" :key="index">
                    <div class="flex items-center gap-1">
                        <!-- Index Number -->
                        <div class="w-10 p-2 pr-0">
                        <span x-text="index + 1" class="block text-xs font-semibold text-black"></span>
                        </div>

                        <!-- Dynamic Fields Inputs -->
                        <template x-for="(columnName, key) in panelUserColumnDisplayNames.filter(name => name !== '')" :key="key">
                        <div class="w-24 p-2 flex-grow">
                            <input x-model="row[columnName]" type="text"
                            class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                            :placeholder="columnName" x-bind:disabled="!selectUser" />
                        </div>
                        </template>

                        <!-- Static Fields Inputs -->
                        <div class="w-24 p-2 flex-grow">
                        <select x-model="row.unit" x-bind:disabled="!selectUser"
                            class="p-1 w-24 sm:w-full rounded-md text-xs text-black border border-gray-300">
                            <option disabled value=""></option>
                            @foreach ($units as $unit)
                            @php
                            $unit = (object) $unit;
                            @endphp
                            <option value="{{ $unit->short_name }}" data-display="{{ ucfirst($unit->unit) }} ({{ $unit->short_name }})">
                                {{ $unit->short_name }}
                            </option>
                            @endforeach
                        </select>
                        {{-- <input type="hidden" wire:model="unit" x-model="row.unit"> --}}

                        </div>
                        <div class="w-24 p-2 flex-grow">
                        <input x-model="row.rate" x-bind:disabled="!selectUser" type="number"
                            class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                            placeholder="Rate" @input="calculateTotal(row)" />
                        </div>
                        {{-- @if($pdfData->challan_templete == 4 )
                        <div class="w-24 p-2 flex-grow">
                        <input x-model="row.tax" type="number" class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                            placeholder="Tax %" @input="calculateTotal(row)" />
                        </div>
                        @endif --}}
                        <div class="w-24 p-2 flex-grow">
                        <input x-model="row.quantity" x-bind:disabled="!selectUser" type="number"
                            class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                            placeholder="Qty" @input="calculateTotal(row)" />
                        </div>
                        <input x-model="row.item_code" type="text" class="p-1 hidden sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                            placeholder="Item Code " />
                        <div class="w-24 p-2 flex-grow">
                            <input type="text" x-bind:value="calculateTotal(row)" class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300 block bg-white" readonly />
                        </div>
                        <div class="w-10 p-2 pr-0">
                        <button x-show="rows.length >= 2" @click="deleteRow(index)"
                            class="border bg-yellow-600 hover:bg-yellow-700 text-black rounded-md px-2 py-1 inline-block"
                            style="background-color: #e5f811;">X</button>
                        </div>
                    </div>
                    </template>
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
                        <input type="text" x-bind:value="totalAmountInWords" disabled class="sm:block hidden text-xs text-black bg-white border border-gray-300 rounded-md p-1 w-full" />
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
    </div>

</div>
