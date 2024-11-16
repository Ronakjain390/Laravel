<div>
    @dd($data, 'datac')
@php
    $initialRows = collect($create_invoice_request['order_details'])->map(function ($detail) {
        $row = [
            'id' => $detail['id'],
            'unit' => $detail['unit'],
            'rate' => $detail['rate'],
            'tax' => $detail['tax'],
            'quantity' => $detail['qty'],
            'total' => $detail['total_amount'],
        ];

        foreach ($detail['columns'] as $column) {
            $row[$column['column_name']] = $column['column_value'];
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
    @if($inputsDisabled == false)
    <div  x-data="{ open: false }">
        @if($disabledButtons)
        <div class="border-b border-gray-300 pb-4">
            <button wire:click.prevent="addRow" @if ($inputsDisabled == true) disabled="" @endif
            class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem] text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">Add New Row</button>
            <!-- Trigger button -->
            {{-- @if($stock)
            <button @click="open = true" type="button" @if ($inputsDisabled == true) disabled="" @endif class="rounded-full bg-yellow-500 px-2 py-1 sm:text-xs shadow-lg text-[0.6rem]  text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">
            Add From Stock
            </button>
           @endif --}}
            {{-- <input class="text-black text-xs rounded-lg h-6 w-1/3 sm:w-auto" @if ($inputsDisabled == true) disabled="" @endif wire:model="barcode" type="text" placeholder="Scan Barcode"> --}}
        </div>
        @endif
        {{-- $rateWithTax = $selectedProductDetails['rate'];
        $taxPercentage = $selectedProductDetails['tax'];
        $rateWithoutTax = $rateWithTax * (100 / (100 + $taxPercentage));

        $rateWithoutTax = round($rateWithoutTax, 2);

        // Assign the calculated rate to the rate field
        $selectedProductDetails['rate'] = $rateWithoutTax;

         Yes. So there is a value --}}
        <!-- Modal content -->

    </div>
    @endif
    <script>

        window.authUser = @json(auth()->user());
        const stateFromBlade = @json($state ?? '');
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        window.onload = function() {
            const calculateTaxCheckbox = document.getElementById('calculateTax');
            if (calculateTaxCheckbox) {
                calculateTaxCheckbox.checked = true;
            }
            updateTotals();
        };
        let selectedUserState;

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

                // totalsContainer.innerHTML += `
            //     <div>
            //         ${tax > 0 ? `<span>Sales at ${tax}%: ${totalWithoutTax}</span><br>
                //         ${rowDiscounts[tax] > 0 ? `<span>Discount at ${discount}%: ${rowDiscounts[tax].toFixed(2)}</span><br>
            //         <span>Net Sale: ${discountedTotalAmounts}</span><br>` : ''}
                //         <span>SGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span><br>
                //         <span>CGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span>` : ''}
            //     </div> &nbsp`;
                totalsContainer.innerHTML += `<div>`;

                if (tax > 0) {
                    totalsContainer.innerHTML += `<span>Sales at ${tax}%: ${totalWithoutTax}</span><br>`;

                    if (rowDiscounts[tax] > 0) {
                        totalsContainer.innerHTML +=
                            `<span>Discount at ${discount}%: ${rowDiscounts[tax].toFixed(2)}</span><br>`;
                        totalsContainer.innerHTML += `<span>Net Sale: ${discountedTotalAmounts}</span><br>`;
                    }

                    // Normalize the state strings to ensure they match correctly
                    const normalizedAuthUserState = authUser.state.trim().toUpperCase();
                    const normalizedSelectedUserState = stateFromBlade.trim().toUpperCase();

                    console.log(normalizedAuthUserState, normalizedSelectedUserState);
                    if (normalizedAuthUserState === normalizedSelectedUserState) {
                        // Intra-state: calculate SGST and CGST
                        totalsContainer.innerHTML +=
                            `<span>SGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span><br>`;
                        totalsContainer.innerHTML +=
                            `<span>CGST ${(tax / 2).toFixed(2)}%: ${(taxAmounts / 2).toFixed(2)}</span>`;
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
                const totalAmount = (discountedTotalAmount + roundOffAmount).toFixed(2);
                totalAmountField.value = `₹ ${totalAmount}`;
            }

            // Update hidden inputs to ensure values are submitted with the form
            const hiddenTotalQtyField = document.querySelector(
                'input[type="hidden"][wire\\:model\\.defer="create_invoice_request.total_qty"]');
            if (hiddenTotalQtyField) {
                hiddenTotalQtyField.value = totalQty === 0 ? null : totalQty;
                hiddenTotalQtyField.dispatchEvent(new Event('input'));
            }

            const hiddenTotalField = document.querySelector(
                'input[type="hidden"][wire\\:model\\.defer="create_invoice_request.total"]');
            if (hiddenTotalField) {
                hiddenTotalField.value = (discountedTotalAmount + roundOffAmount).toFixed(2);
                hiddenTotalField.dispatchEvent(new Event('input'));
            }

            const roundOffAmountField = document.getElementById('roundOffAmount');
            if (roundOffAmountField) {
                roundOffAmountField.value = roundOffAmount.toFixed(2);
            }

            const hiddenRoundOffField = document.querySelector(
                'input[type="hidden"][wire\\:model\\.defer="create_invoice_request.round_off"]');
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

        document.addEventListener('DOMContentLoaded', function() {
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
            @if ($mainUser->team_user->permissions->permission->sender->create_challan == 1)
                <button type="button" id="add"
                @if ($action == 'save') @click.prevent="saveRows" @elseif($action == 'edit') @click.prevent='editData' @endif
                x-bind:disabled="selectUser"
                class="rounded-full btn-size lg:px-8 px-4 py-2 @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900  text-white @endif "
                >
                Save
                </button>
            @endif
        @else
            <button type="button" id="add"
                @if ($action == 'save') @click.prevent="saveRows" @elseif($action == 'edit') @click.prevent='editData' @endif
                x-bind:disabled="selectUser"
                class="rounded-full btn-size lg:px-8 px-4 py-2 @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900  text-white @endif "
                >
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
                <button wire:click.prevent='sendChallan({{ $invoiceId }})'
                    @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
            @endif
        @else
            <button wire:click.prevent='sendChallan({{ $invoiceId }})'
                @if ($inputsResponseDisabled == true) disabled="" @endif
                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
        @endif
        @endif
        {{-- @dump($teamMembers) --}}
        @if($teamMembers != null)
        {{-- SFP Button --}}
        <button
        wire:click="$emit('openSfpModal', {{ $invoiceId }}, 'challan_id')"
        @if ($inputsResponseDisabled == true) disabled="" @endif
        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>

        @endif
    </div>
</div>

