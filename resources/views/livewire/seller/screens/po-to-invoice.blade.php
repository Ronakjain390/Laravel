<div>
@php
    $initialRows = collect($create_invoice_request['order_details'])->map(function ($detail) {
        $row = [
            'id' => $detail['id'] ?? null, // Add a fallback here
            'unit' => $detail['unit'] ?? '',
            'rate' => $detail['rate'] ?? 0,
            'tax' => $detail['tax'] ?? 0,
            'quantity' => $detail['qty'] ?? 0,
            'total' => $detail['total_amount'] ?? 0,
        ];

        foreach ($detail['columns'] ?? [] as $column) {
            $row[$column['column_name']] = $column['column_value'] ?? '';
        }

        return $row;
    });
@endphp

<div x-data="invoiceComponent('{{ auth()->user()->state }}', {{ json_encode($panelUserColumnDisplayNames) }}, {{ $initialRows->toJson() }}, '{{ $context }}', @entangle('selectUser'))"
    x-init="init()">
    @php
        $mainUser = json_decode($this->mainUser);

    @endphp
    {{-- @dump($data) --}}
    <div class="max-w-7xl mx-auto  rounded-md">
        <!-- Top Section (Buyer and Address) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                <div x-data="{ addUser: false, inputsDisabled: true }">
                    <div x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class="  flex w-full bg-white border border-gray-400 text-xs hover:bg-orange  text-black focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50   rounded-lg   px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                            type="button">


                            @if ($buyerName == '')
                                <span x-cloak=" Select Buyer  Others"></span>
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
                                        class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search user">
                                </div>
                            </div>
                            <!-- Filtered list based on search -->
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownSearchButton">
                                {{-- @dump($billTo); --}}
                                {{-- @foreach ($billTo as $user)
                                <li x-show="search === '' || '{{ strtolower($user->buyer_name ?? null) }}'.includes(search.toLowerCase())"
                                    wire:click.prevent="selectUser('{{ $user->invoice_number->series_number ?? 'Not Assigned' }}', '{{ $user->details[0]->address ?? null }}', '{{ $user->user->email ?? null }}', '{{ $user->details[0]->phone ?? null }}', '{{ $user->details[0]->gst_number ?? null }}','{{ $user->details[0]->state ?? null }}','{{ $user->buyer_name ?? 'Select Buyer' }}', '{{ json_encode($user ?? null) }}')"
                                    x-on:click="selectedUser = '{{ $user->buyer_name ?? 'Select Receiver' }}';  selectedUserState = '{{ $user->details[0]->state ?? '' }}'; console.log('{{ $user->seller_name ?? 'Receiver Name is Empty' }}')">
                                    <div
                                        class="flex items-center  rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label
                                            class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded dark:text-gray-300">{{ $user->buyer_name ?? null }}</label>
                                    </div>
                                </li>
                            @endforeach --}}
                            </ul>
                            <button wire:click="updateField"
                                class="flex w-full items-center p-3 text-xs font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
                                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 18">
                                    <path
                                        d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-2V5a1 1 0 0 0-2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 0 0 2 0V9h2a1 1 0 1 0 0-2Z" />
                                </svg>
                                Others
                            </button>
                        </div>
                    </div>
                    <dd class="pl-2 text-xs inline-block text-black text-black capitalize hidden ">
                        <div class="relative">
                            @if ($selectedUser == !null)
                                <input wire:model.defer="create_invoice_request.invoice_date"
                                    type="date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                                    placeholder="Select date">
                            @endif
                        </div>
                    </dd>
                    <div class="w-full text-gray-900 dark:text-white">
                        {{-- @if ($updateForm == true) --}}
                        <div class="grid gap-2">
                            <div class="flex flex-row items-end items-center">
                                <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 whitespace-nowrap">Series No
                                </dt>
                                {{-- <dd class=" text-xs text-black text-black capitalize"> --}}
                                {{-- {{ $selectedUser['phone'] ?? null }} --}}
                                @if ($selectedUser == !null)
                                    <dd
                                        class=" text-xs text-black text-black capitalize flex items-center whitespace-nowrap
                                        @if (isset($selectedUser['invoiceSeries'])) @if ($selectedUser['invoiceSeries'] == 'Not Assigned')
                                                text-red-700
                                            @else
                                                text-black @endif
                                                @else
                                                text-black
                                        @endif">
                                        {{ $selectedUser['invoiceSeries'] ?? null }}{{ '-' }}{{ $selectedUser['invoiceNumber'] }}
                                        {{-- @livewire('series-number-input', ['challanSeries' => $selectedUser['invoiceSeries'], 'seriesNumber' => $selectedUser['invoiceNumber'], 'method' => 'invoice']) --}}
                                    </dd>
                                @endif
                                {{-- </dd> --}}
                            </div>


                            <div class="flex flex-row">
                                <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4">Po No</dt>
                                <dd class=" text-xs text-black text-black capitalize">
                                    {{ $purchase_order_series ?? null }}
                                </dd>
                            </div>
                            <div class="flex flex-row">
                                <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4">Phone</dt>
                                <dd class=" text-xs text-black text-black capitalize">
                                    {{ $selectedUser['phone'] ?? null }}
                                </dd>
                            </div>
                            <div class="flex flex-row">
                                <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4">Email</dt>
                                <dd class=" text-xs text-black text-black capitalize">
                                    {{ $selectedUser['email'] ?? null }}
                                </dd>
                            </div>
                            {{-- <div class="flex flex-row">
                                <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400 w-1/4">Date : </dt>
                                <dd class="pl-4 text-sm font-semibold text-black capitalize">
                                    {{ date('j F , Y') }} --}}
                                    <input type="hidden" name="invoice_date" wire:model="create_invoice_request.invoice_date" value="{{ date('Y-m-d') }} ">
                                {{-- </dd>
                            </div> --}}

                        </div>
                    </div>
                </div>
                <div>
                </div>
            </div>
            {{-- @if ($updateForm == true) --}}
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">


                    <div x-data="{ search: '', selectedUserDetails: null }">
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownDetailSearchButton" data-dropdown-toggle="dropdownDetailSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class="  flex w-full bg-white border border-gray-400 text-xs hover:bg-orange  text-black focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg  px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                            type="button">
                            Select Address
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div x-data="{ search: '', selectedUserDetails: null }" id="dropdownDetailSearch"
                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
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
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">
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
                    {{-- @dump($selectedUser) --}}
                    <div class="grid gap-2">
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">Address :</dt>
                            <dd class="pl-5 text-xs  text-black capitalize">
                                {{ $selectedUser['address'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">City :</dt>
                            <dd class="pl-5 text-xs  text-black capitalize">
                                {{ $city ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">State :</dt>
                            <dd class="pl-5 text-xs  text-black capitalize">
                                {{ $state ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs dark:text-gray-400 w-24">Pincode :</dt>
                            <dd class="pl-5 text-xs  text-black capitalize">
                                {{ $pincode ?? null }}</dd>
                        </div>
                    </div>
                </div>
            </div>
            {{-- @endif --}}
        </div>
    </div>
    {{-- @dd($stock) --}}

    <!-- Table Section -->
    <div wire:loading    class="fixed z-30 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
        <span   class="loading loading-spinner loading-md"></span>
    </div>
    <div x-data="{ open: false }" class=" m-2 text-xs">
        <!-- Add Row Button -->
        <div class="flex items-center justify-between sm:justify-start"  >
            <div class="sm:p-4 p-1.5">
                <button class="rounded-full bg-yellow-500 px-3 py-1 text-[0.6rem] sm:text-xs shadow-lg text-black hover:bg-yellow-700 sm:h-5 sm:h-7"
                style="background-color: #e5f811;" @click="addRow()">
                <span class="hidden sm:inline">Add New Row</span>
                <span class="inline sm:hidden">+</span>
            </button>
            </div>
            {{-- @if ($stocks) --}}
                <button @click="open = true" type="button"
                    class="rounded-full bg-yellow-500 px-3 py-1 text-[0.6rem] sm:text-xs shadow-lg text-black hover:bg-yellow-700 sm:h-5 sm:h-7"
                    style="background-color: #e5f811;">
                    <span class="hidden sm:inline">Add From Stock</span>
                    <span class="inline sm:hidden">Stock</span>
                </button>
            {{-- @endif --}}
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

                                <table
                                    class="w-full text-[0.6rem] sm:text-xs text-left text-black dark:text-gray-400 overflow-x-auto">
                                    <thead
                                        class="text-[0.6rem] sm:text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                        <th scope="col"
                                            class="va-b px-2 py-1 text-[0.6rem] sm:text-xs border-2 border-gray-300">
                                            Action</th>
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
                                                        <div class="pl-0 border border-gray-400 border-solid">
                                                            <input type="checkbox" value="{{ $product }}"
                                                                x-model="checked"
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
        </div>




<div class="bg-[#ebebeb] rounded-lg shadow  overflow-x-auto text-xs">

    <div class="w-full overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-xs font-semibold text-black">
                    <th class="p-2 text-left">#</th>
                    @foreach($panelUserColumnDisplayNames as $columnName)
                        @if($columnName !== '')
                            <th class="p-2 text-left">{{ $columnName }}</th>
                        @endif
                    @endforeach
                    <th class="p-2 text-left">Unit</th>
                    <th class="p-2 text-left">Rate</th>
                    {{-- @if($pdfData->challan_templete == 4) --}}
                        <th class="p-2 text-left">Tax (%)</th>
                    {{-- @endif --}}
                    <th class="p-2 text-left">Qty</th>
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
                {{-- @dump($panelUserColumnDisplayNames) --}}
                {{-- {{dd($create_invoice_request['order_details'])}} --}}
                <template x-for="(row, index) in rows" :key="index">
                    <tr class="items-center">
                        <td class="p-2 pr-0">
                            <span x-text="index + 1" class="block text-xs font-semibold text-black"></span>
                        </td>
                        <template x-for="(columnName, key) in panelUserColumnDisplayNames.filter(name => name !== '')" :key="key">
                            <td class="p-2">
                                <input
                                    x-model="row[columnName]"
                                    type="text"
                                    class="p-1 sm:w-full w-24 rounded-md text-xs text-black border"
                                    :class="{ 'border-red-500': articleErrors[row.id] && columnName === 'Article', 'border-gray-300': !articleErrors[row.id] || columnName !== 'Article' }"
                                    :placeholder="columnName"
                                    x-bind:disabled="selectUser"
                                    @input="if(columnName === 'Article') validateArticle(row)"
                                />
                                <template x-if="columnName === 'Article' && articleErrors[row.id]">
                                    <p class="text-red-500 text-xs mt-1" x-text="articleErrors[row.id]"></p>
                                </template>
                            </td>
                        </template>
                        <td class="p-2">
                            <select x-model="row.unit" x-bind:disabled="selectUser"
                                class="p-1 w-24 rounded-md text-xs text-black border border-gray-300">
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
                        </td>
                        <td class="p-2">
                            <input x-model="row.rate" x-bind:disabled="selectUser" type="number"
                                class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                                placeholder="Rate" @input="calculateTotal(row)" />
                        </td>
                        {{-- @if($pdfData->challan_templete == 4) --}}
                        <td class="p-2">
                            <input x-model="row.tax" type="number" class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                                placeholder="Tax %" @input="calculateTotal(row)" />
                        </td>
                        {{-- @endif --}}
                        <td class="p-2">
                            <input x-model="row.quantity" x-bind:disabled="selectUser" type="number"
                                class="p-1 sm:w-full w-24 rounded-md text-xs text-black border border-gray-300"
                                placeholder="Qty" @input="calculateTotal(row)" />
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
        <input x-model="row.item_code" type="text" class="p-1 hidden w-full rounded-md text-xs text-black border border-gray-300"
            placeholder="Item Code" />
    </div>

            <!-- Dynamic Headers and Inputs in one row -->


    <div class="sm:flex block  justify-between">
        <!-- Left Side: Comment Input Box -->
        <div class="flex flex-grow items-center sm:mr-10  sm:p-2  sm:pr-8 ">
            <label class="flex-grow font-semibold text-black w-5 sm:w-10"></label>
            <input @if ($inputsDisabled == true) disabled @endif
                wire:model.defer='create_invoice_request.comment' placeholder="Comment"
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
                <input x-model="discount" x-bind:disabled="selectUser" @input="updateTotals" type="text" class="border text-black text-xs border-gray-300 w-24 p-1 rounded-md flex-grow" />
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


    <!-- Buttons -->
    @if ($save)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="alert-border-3" class="p-2 mb-4 text-[0.6rem] sm:text-xs text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
        role="alert">
        <span class="font-medium">Success:</span> {{ $save }}
    </div>

    @endif
    {{-- @dump($inputsDisabled) --}}
    <div class="flex justify-center space-x-4 lg:text-lg text-[0.6rem] sm:text-xs m-2" >
        {{-- Save Button --}}
        {{-- Save Button --}}
        @if ($mainUser->team_user != null)
            @if ($mainUser->team_user->permissions->permission->seller->create_invoice == 1)
                <button type="button" id="add"
                @if ($action == 'save') @click.prevent="submitData" @elseif($action == 'edit') @click.prevent='editData' @endif
                x-bind:disabled="selectUser"
                class="rounded-full btn-size lg:px-8 px-4 py-2 @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900  text-white @endif "
                >
                Save
                </button>
            @endif
        @else
            <button type="button" id="add"
                @if ($action == 'save') @click.prevent="submitData" @elseif($action == 'edit') @click.prevent='editData' @endif
                x-bind:disabled="selectUser"
                class="rounded-full btn-size lg:px-8 px-4 py-2 @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900  text-white @endif "
                >
                Save
            </button>
        @endif

        {{-- Edit Button --}}
        @if ($mainUser->team_user != null)
            @if ($mainUser->team_user->permissions->permission->seller->modify_invoice == 1)
                <button wire:click.prevent='invoiceEdit' type="button"

                    class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gr px-4ay-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
            @endif
        @else
            <button wire:click.prevent='invoiceEdit' type="button"

                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
        @endif

        @if($sendButtonDisabled == true)
        {{-- Send Button --}}
        @if ($mainUser->team_user != null)
            @if ($mainUser->team_user->permissions->permission->seller->send_invoice == 1)
                <button wire:click.prevent='sendInvoice({{ $invoiceId }})'

                    class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
            @endif
        @else
            <button wire:click.prevent='sendInvoice({{ $invoiceId }})'

                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
        @endif
        @endif
        {{-- @dump($teamMembers) --}}
        @if($teamMembers != null)
        {{-- SFP Button --}}
        <button
        wire:click="$emit('openSfpModal', {{ $invoiceId }}, 'invoice_id')"

        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>

        @endif
    </div>
       <livewire:components.sfp-component :panelType="'invoice'"/>
</div>
