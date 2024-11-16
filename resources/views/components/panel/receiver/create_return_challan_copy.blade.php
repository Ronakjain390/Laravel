<div class="rounded-lg dark:border-gray-700 mt-4">

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
                                    wire:click.prevent="selectUser('{{ $user->sender ?? '' }}', '{{ $user->phone ?? null }}', '{{ $user->email ?? null }}', '{{ $user->address ?? null }}', '{{ $user->gst_number ?? null }}','{{ $user->sender_id ?? null }}')">
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
    <div  x-data="{ open: false }">
        <div class="border-b border-gray-300 pb-4">
            <button wire:click.prevent="addRow" @if ($inputsDisabled == true) disabled="" @endif
            class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem] text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">Add New Row</button>
            <!-- Trigger button -->
            {{-- <button @click="open = true" type="button" @if ($inputsDisabled == true) disabled="" @endif class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem]  text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">
            Add From Stock
            </button>
            <input class="text-black text-xs rounded-lg h-6 w-1/3 sm:w-auto" @if ($inputsDisabled == true) disabled="" @endif wire:model="barcode" type="text" placeholder="Scan Barcode"> --}}
        </div>
    </div>
    <div class="bg-[#ebebeb] py-2 ">

        <div class="flex flex-col">
            <div class="overflow-x-auto ">
                <div class="min-w-full py-2 align-middle inline-block">
                    <div class="overflow-hidden rounded-lg bg-[#ebebeb] p-3  max-w-full" wire:ignore.self>

                    <table class="min-w-full w-full">
                        <thead>
                            <tr class="border-b border-gray-300 text-left">
                                <th class="px-2 text-xs text-black font-semibold">S.No.</th>
                                @foreach ($panelColumnDisplayNames as $columnName)
                                    @if (!empty($columnName))
                                        <th class="px-2 text-xs text-black font-semibold">{{ $columnName }}
                                            @if ($columnName == 'Article')
                                            <span class="text-red-500">*</span>
                                        @endif
                                        </th>
                                    @endif
                                @endforeach
                                <th class="px-2 text-xs text-black font-semibold">Unit</th>
                                <th class="px-2 text-xs text-black font-semibold">Rate</th>
                                <th class="px-2 text-xs text-black font-semibold">Qty
                                    @if ($columnName == 'Article')
                                    <span class="text-red-500">*</span>
                                @endif
                                </th>
                                {{-- <th class="px-2 text-xs text-black font-semibold ">Tax</th> --}}
                                <th class="px-2 text-xs text-black font-semibold">Total Amount</th>
                                <th class="px-2 text-xs text-black font-semibold"></th>
                            </tr>
                            @php
                                $nonEmptyColumnCount = 0;
                                foreach ($panelColumnDisplayNames as $columnName) {
                                    if (!empty($columnName)) {
                                        $nonEmptyColumnCount++;
                                    }
                                }
                            @endphp
                            {{-- <tr>
                                <td colspan="{{ $nonEmptyColumnCount + 2 }}"></td>
                                <td colspan="2">
                                    <div class="flex items-center">
                                        <label for="calculateTax" class="ml-2 text-[0.6rem] font-semibold text-black dark:text-gray-300">Without Tax</label>
                                        <input  id="calculateTax" type="checkbox"
                                            class="w-4 h-4 ml-2 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                </td>
                            </tr> --}}

                        </thead>
                        @if (session('error'))
                            <div  x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" id="error-alert" class="flex items-center p-2 mb-4 text-red-800 rounded-lg bg-red-500 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
                                <div class="ms-3 text-sm text-white">
                                    <span class="font-medium">Error:</span> {{ session('error') }}
                                </div>
                                {{-- <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-border-3" aria-label="Close">
                                <span class="sr-only">Dismiss</span>
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                </button> --}}
                            </div>
                        @endif
                        <tbody>
                            @php
                            $receivedArticles = json_decode($receivedArticles);
                            // dd($receivedArticles);
                        @endphp
                            {{-- {{dd($createChallanRequest['order_details'])}} --}}
                            @foreach ($createChallanRequest['order_details'] as $index => $row)
                                {{-- <form wire:submit.prevent> --}}

                                    <tr>
                                        <td class="px-1 py-2"><input @if ($inputsDisabled == true) disabled="" @endif
                                                value="{{ $index + 1 }}"
                                                class="hsn-box h-7 w-10 rounded-lg bg-white border border-gray-400 text-xs   text-black focus:outline-none"
                                                type="text" /></td>
                                            {{-- @dd($panelColumnDisplayNames); --}}

                                            @php $articleColumnRendered = false; @endphp


                                            @foreach ($panelColumnDisplayNames as $key => $columnName)
                                                @php
                                                    $this->createChallanRequest['order_details'][$index]['columns'][$key]['column_name'] = $columnName;
                                                @endphp
                                                {{-- @dd($columnName); --}}
                                            @if (!empty($columnName))

                                                @if ($columnName == 'Article' && !$articleColumnRendered)
                                                    @php $articleColumnRendered = true; @endphp
                                                    <td class="px-1 py-2">
                                                        <input @if ($inputsDisabled == true) disabled="" @endif type="text"
                                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                            class="hsn-box h-7 w-24 dynamic-width-input rounded-lg bg-white {{ $columnName == 'Article' && $errors->has('article.' . $index) ? 'border-2 border-red-300' : 'border-gray-400' }} text-xs text-black font-normal text-black focus:outline-none"
                                                            value="" list="articles-{{ $index }}" />

                                                        <datalist id="articles-{{ $index }}">
                                                            @if (isset($receivedArticles))
                                                                @foreach ($receivedArticles as $article)
                                                                    @if(!empty($article->statuses) && in_array($article->statuses[0]->status, ['accept', 'self_delivered', 'partially_self_return', 'return']))
                                                                        @foreach ($article->order_details as $detail)
                                                                            @if($detail->remaining_qty != 0 && !in_array($detail->columns[0]->column_value, $selectedArticles))
                                                                                <option data-detail="{{ json_encode($detail) }}"
                                                                                    value="{{ $detail->columns[0]->column_value }}">
                                                                                    {{ $detail->columns[0]->column_value }}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                                <script>
                                                                    document.querySelector('input[list="articles-{{ $index }}"]').addEventListener('input', function(event) {
                                                                        var enteredValue = event.target.value;
                                                                        var options = document.querySelectorAll('#articles-{{ $index }} option');
                                                                        var selectedOption = null;

                                                                        options.forEach(function(option) {
                                                                            if (option.value === enteredValue) {
                                                                                selectedOption = option;
                                                                            }
                                                                        });

                                                                        if (selectedOption) {
                                                                            var detailData = JSON.parse(selectedOption.getAttribute('data-detail'));
                                                                            console.log(detailData);
                                                                            detailData.challan_order_detail_id = detailData.id; // Add the id as challan_order_detail_id
                                                                            @this.selectArticle(detailData, {{ $index }});
                                                                            @this.call('addSelectedArticle', detailData.columns[0].column_value);
                                                                            updateTotals();
                                                                        }
                                                                    });
                                                                </script>
                                                            @endif
                                                        </datalist>
                                                    </td>
                                                @else
                                                    <td class="px-1 py-2">
                                                        <input @if ($inputsDisabled == true) disabled="" @endif
                                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                            class="hsn-box h-7 w-24 dynamic-width-input rounded-lg bg-white border border-gray-400 text-xs   text-black font-normal text-black focus:outline-none"
                                                            type="text" />
                                                    </td>
                                                @endif
                                            @endif
                                        @endforeach

                                    <td class="max-w-sm mx-auto">
                                        <select @if ($inputsDisabled == true) disabled="" @endif wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit" class="bg-gray-50 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block text-xs p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="">Select Unit</option>
                                            @foreach (['pcs', 'mtr', 'ltr', 'kg', 'gms', 'cartons', 'mm', 'ml', 'bags', 'dozens'] as $unit)
                                                <option value="{{ $unit }}" @if(isset($createChallanRequest['order_details'][$index]['unit']) && strtolower($createChallanRequest['order_details'][$index]['unit']) == strtolower($unit)) selected @endif>{{ ucfirst($unit) }}</option>
                                            @endforeach
                                        </select>
                                    </td>


                                    <td class="px-2 py-2">
                                        <input
                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.rate"
                                            class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none rate"
                                            type="number" data-index="{{ $index }}" />
                                    </td>
                                    <td class="px-2 py-2">
                                        <input
                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.remaining_qty"
                                            class="hsn-box h-7 w-24 rounded-lg bg-white {{  $errors->has('qty.' . $index) ? 'border-2 border-red-300' : 'border-gray-400' }} text-xs text-black focus:outline-none qty"
                                            type="number" data-index="{{ $index }}" />
                                    </td>
                                    <!-- Tax -->
                                    <td class="px-2 py-2 hidden">
                                        <input
                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.tax"
                                            class="hsn-box h-7 hidden w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none tax"
                                            type="number" placeholder="Tax %" data-index="{{ $index }}" data-tax-index="{{ $index }}" />
                                    </td>

                                    <!-- Total -->
                                    <td class="px-2 py-2">
                                        <input @if ($inputsDisabled == true) disabled="" @endif
                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.total_amount"
                                            class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none total"
                                            type="number" data-index="{{ $index }}" readonly />
                                    </td>

                                    <td class="px-2 py-2">
                                        <button type="button" wire:click.prevent="removeRow({{ $index }})"
                                            class="bg-yellow-500 px-2 py-1 text-sm text-black hover:bg-yellow-700"
                                            style="background-color: #e5f811;">X</button>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>




                {{-- @dump($this->createChallanRequest['total_without_tax']) --}}
                <div class="text-xs font-medium text-right pr-6 pt-2">
                    @if($createChallanRequest['order_details'])
                {{-- <p class="text-xs text-right">Net Sale : {{   $this->createChallanRequest['total'] }} </p> --}}
                    @endif
                </div>



            {{-- <!-- Blade template section for displaying calculations -->
            <div  class="text-right text-xs text-black">
                <span id="tax">  </span>
                <span id="total"></span>
            </div>
            <div class="text-right text-xs text-black">
                <span id="sgst" ></span>
                <span id="sgstAmount"></span>
            </div>
            <div class="text-right text-xs text-black">
                <span id="cgst" ></span>
                <span id="cgstAmount"></span>
            </div>
            <div class="text-right text-xs text-black">
                <span id="discount" ></span>
                <span id="cgstAmount"></span>
            </div> --}}

            @foreach ($createChallanRequest['order_details'] as $index => $row)
                <!-- Blade template section for displaying calculations -->
                <div id="totals-container" class="text-right text-xs text-black mr-5"  wire:ignore></div>
            @endforeach

            <script>
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




            {{-- @dump( ( isset($selectedUser['state']) && isset(Auth::user()->state) && strtoupper($selectedUser['state']) == strtoupper(Auth::user()->state)) , isset($selectedUser['state']), Auth::user()->state) --}}
                <tbody>
                    <div class="mb-1 grid grid-cols-12 border-t border-gray-400">
                        <div class="col-span-1 py-2 text-xs text-black font-semibold">Comment</div>
                        <div class="col-span-10 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                wire:model='createChallanRequest.comment'
                                class="hsn-box h-8 w-full rounded-lg bg-white border border-gray-400 text-xs   text-black focus:outline-none"
                                type="text" />
                        </div>
                    </div>
                    <!-- discount -->
                    <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300 hidden">
                        <div class="col-span-1 py-2 mr-1 text-xs text-black font-semibold">Discount</div>
                        <div class="col-span-8 py-2">
                            <!-- <input @if ($inputsDisabled == true) disabled="" @endif class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" value="{{ $totalAmount }}" disabled style="background-color: #423E3E;" /> -->
                        </div>

                        <div class="col-span-2 flex items-center justify-between">
                            <div class="col-span-1 w-24 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif
                                    class="hsn-box h-8 w-24 rounded-lg bg-white border border-gray-400 text-xs   text-black  focus:outline-none"
                                    type="text" value="Discount %" disabled />
                            </div>

                            <td class="px-2 py-2">
                                <input
                                    wire:model.defer="discount_total_amount"
                                    class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none discount"
                                    type="number" data-index="{{ $index }}" />
                            </td>
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
                                type="text" id="roundOffAmount" readonly value="Round Off Amount" />
                         <input type="hidden" wire:model.defer="createChallanRequest.round_off" />
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                        <div class="col-span-1 py-2 text-xs text-black font-semibold">Total</div>
                        <div class="col-span-8 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                class="hsn-box h-8 w-11/12 rounded-lg  bg-white border border-gray-400 text-xs   text-black focus:outline-none"
                                type="text" id="totalAmountInWords"  disabled
                                 />
                        </div>

                        <div class="col-span-2 flex items-center justify-between">
                            <div class="col-span-1 w-24 py-2">
                                <input
                                    @if ($inputsDisabled == true) readonly @endif
                                    class="hsn-box h-8 w-full rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none totalQtyField"
                                    type="text" value="{{ $createChallanRequest['total_qty'] }}" readonly
                                />
                                <input type="hidden" wire:model.defer="createChallanRequest.total_qty" />
                            </div>
                            {{-- <div class="col-span-1 w-24 py-2">
                                <input
                                    @if ($inputsDisabled == true) readonly @endif
                                    class="hsn-box h-8 w-full rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none totalAmountField"
                                    type="text" value="{{ $createChallanRequest['total_qty'] }}" readonly
                                />
                                <input type="hidden" wire:model="createChallanRequest.total_qty" />
                            </div> --}}
                            <div class="col-span-1 w-24 py-2">
                                <input
                                    @if ($inputsDisabled == true) readonly @endif
                                    class="hsn-box h-8 w-full rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none totalAmountField"
                                    type="text" value="{{ $createChallanRequest['total'] }}" readonly
                                />
                                <input type="hidden" wire:model.defer="createChallanRequest.total" />
                            </div>
                        </div>


                    </div>
                </tbody>
            </div>
        </div>
    </div>

            @if ($successMessage)
            <div class="p-2 mb-4 text-xs text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <span class="font-medium">Success:</span> {{ $successMessage }}
            </div>
        @endif
            <div class="flex justify-center space-x-4">
                @if ($mainUser->team_user != null)
                @if ($mainUser->team_user->permissions->permission->receiver->create_return_challan == 1)
                    <button type="button"
                        @if ($action == 'save') wire:click.prevent='challanCreate' @elseif($action == 'edit') wire:click.prevent='challanModify' @endif
                        @if ($inputsDisabled == true) disabled="" @endif
                        @if ($inputsResponseDisabled == false) disabled @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2   "
                        wire:loading.attr="disabled" wire:target="challanCreate, challanModify"
                        >
                        Save
                    </button>
                        @endif
                    @else
                        <button type="button"
                            @if ($action == 'save') wire:click.prevent='challanCreate' @elseif($action == 'edit') wire:click.prevent='challanModify' @endif
                            @if ($inputsDisabled == true) disabled="" @endif
                            @if ($inputsResponseDisabled == false) disabled @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2  ">Save</button>
                    @endif

                    {{-- Edit Button --}}
                    @if ($mainUser->team_user != null)
                        @if ($mainUser->team_user->permissions->permission->receiver->modify_challan == 1)
                            <button wire:click.prevent='challanEdit' type="button"
                                @if ($inputsResponseDisabled == true) disabled="" @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gr px-4ay-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
                        @endif
                    @else
                        <button wire:click.prevent='challanEdit' type="button"
                            @if ($inputsResponseDisabled == true) disabled="" @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8  px-4 py-2 text-black ">Edit</button>
                    @endif
                        {{-- <button wire:click.prevent='sendInvoice({{ $invoiceId }})' @if ($inputsResponseDisabled == true) disabled="" @endif class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button> --}}
                        {{-- @if($sendButtonDisabled == true) --}}
                        <button wire:click.prevent='sendChallan({{ $challanId }})'
                            @if (
                                $inputsResponseDisabled ||
                                    strpos($successMessage, 'Success: Your Feature usage limit is over or expired.') !== false) disabled @endif
                            class="rounded-full @if (
                                $inputsResponseDisabled ||
                                    strpos($successMessage, 'Success: Your Feature usage limit is over or expired.') !== false) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">
                            Send
                        </button>
                        {{-- @endif --}}


                        <button  x-on:click.prevent="$wire.updateVariable('challan_sfp', {{ $challanId }})" href="javascript:void(0);"

                        @if ($inputsResponseDisabled == true) disabled="" @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>

            </div>
        </div>
    </div>

    @if ($sfpModal == true)
                    <div x-data="{ sfpModal: @entangle('sfpModal') }"
                    x-show="sfpModal"
                    x-on:keydown.escape.window="sfpModal = false"
                    x-on:close.stop="sfpModal = false"
                    class="fixed inset-0 flex items-center justify-center px-2.5 z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
                    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                        <div class="mb-4">
                            <h1 class="text-lg text-black border-b border-gray-400">Send For Proccessing </h1>
                            <div class="">

                                <div class="p-4 md:p-5 text-center">
                                    {{-- <h1 class="text-lg text-black text-left">Add tags</h1> --}}
                                    <form class="max-w-md mx-auto mt-5">
                                        <div class="grid grid-cols-2 gap-4 mt-2 text-xs">
                                            <!-- Left side (Dropdown) -->
                                            <div class="relative">
                                                <input
                                                    class="multi-select-input w-full px-4 py-2 h-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white border border-gray-400"
                                                    placeholder="Select Team Members..."
                                                    readonly
                                                >
                                                <div
                                                    class="multi-select-dropdown absolute z-10 w-full mt-1 text-xs bg-white rounded-md shadow-lg hidden"
                                                >
                                                    <div class="max-h-60 overflow-y-auto">
                                                        <ul class="py-1">
                                                            @if (isset($teamMembers) && is_array($teamMembers))
                                                                @foreach ($teamMembers as $team)
                                                                    @php $team = (object) $team; @endphp
                                                                    @if ($team !== null && $team->id !== auth()->id())
                                                                        <li>
                                                                            <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    value="{{ $team->id }}"   wire:model.defer="team_user_ids"
                                                                                    data-name="{{ $team->team_user_name }}"
                                                                                    class="multi-select-option form-checkbox h-5 w-5 text-blue-500"
                                                                                >
                                                                                <span class="ml-2 text-gray-700">{{ $team->team_user_name }}</span>
                                                                            </label>
                                                                        </li>
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
                                                    placeholder="Comment"
                                                ></textarea>
                                                <div class="text-center mt-2 text-[0.6rem]">Less than 100 words only</div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                                </div>

                        </div>
                        <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                            <button wire:click="closeSfpModal"
                                    class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                                Cancel
                            </button>
                            <button wire:click="sfpChallan"
                                    class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs   text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                               Send
                            </button>
                        </div>
                    </div>
                    <div x-on:click.self="sfpModal = false" class="inset-0 bg-black opacity-50"></div>
                    </div>
                    @endif
</div>
<script>



    document.addEventListener('livewire:update', function() {
        console.log('Livewire Update');
        initializeMultiSelect();
        window.dispatchEvent(new CustomEvent('challansUpdated'));
    });

    document.addEventListener('DOMContentLoaded', function() {
        initializeMultiSelect();
    });

    function initializeMultiSelect() {
        const multiSelectInputs = document.querySelectorAll('.multi-select-input');
        const multiSelectDropdowns = document.querySelectorAll('.multi-select-dropdown');

        multiSelectInputs.forEach((input, index) => {
            const dropdown = multiSelectDropdowns[index];
            const options = dropdown.querySelectorAll('.multi-select-option');
            const selectedValues = [];

            input.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            options.forEach(option => {
                option.addEventListener('change', () => {
                    if (option.checked) {
                        selectedValues.push({ id: option.value, name: option.dataset.name });
                    } else {
                        const valueIndex = selectedValues.findIndex(item => item.id === option.value);
                        if (valueIndex !== -1) {
                            selectedValues.splice(valueIndex, 1);
                        }
                    }
                    input.value = selectedValues.map(item => item.name).join(', ');
                });
            });

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    }
</script>
</div>
