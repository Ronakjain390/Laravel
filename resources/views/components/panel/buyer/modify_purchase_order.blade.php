<div class="rounded-lg dark:border-gray-700 mt-4">
    @php
    $mainUser = json_decode($this->mainUser);
    // $panelName = strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']));
    $hideSuccessMessage = true;
    @endphp
    <form>
        <!-- First Row - Responsive 2 Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <!-- Column 1 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        SELLER
                    </h5>
                 
                    <div class="w-full text-gray-900 dark:text-white">
                            {{-- <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-6">
                                    <div class="grid gap-2">
                                        <div class="flex flex-row">
                                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Date</dt>
                                            <dd class="pl-4 text-xs text-black ">
                                                {{ date('j F , Y') }}</dd>

                                        </div>
                                        @php
                                        $challanModifyData = json_decode($challanModifyData, true); // Set the second parameter to true to get an associative array
                                        $createChallanRequest = $challanModifyData ?? []; 
                                    
                                        // dd($createChallanRequest);
                                        // $createChallanRequest['order_details'];
                                    @endphp
                                        <div class="flex flex-row">
                                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Series No.</dt>
                                            <dd
                                                class="pl-4 text-xs text-black 
                                        ">
                                                {{ $createChallanRequest['invoice_series'] ?? null }}-{{ $createChallanRequest['series_num'] }}
                                            </dd>
                                        </div>
                                        <div class="flex flex-row ">
                                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Name</dt>
                                            <dd class="pl-4 text-xs text-black ">
                                                {{ Auth::guard(Auth::getDefaultDriver())->user()->name ?? null }}</dd>
                                        </div>

                                        <div class="flex flex-row ">
                                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Address</dt>
                                            <dd class="pl-4 text-xs text-black ">
                                                {{ Auth::guard(Auth::getDefaultDriver())->user()->address ?? null }}</dd>
                                        </div>


                                        <div class="flex flex-row ">
                                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Email</dt>
                                            <dd class="pl-4 text-xs text-black ">
                                                {{ Auth::guard(Auth::getDefaultDriver())->user()->email ?? null }}</dd>
                                        </div>
                                        <div class="flex flex-row ">
                                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Phone</dt>
                                            <dd class="pl-4 text-xs text-black ">
                                                {{ Auth::guard(Auth::getDefaultDriver())->user()->phone ?? null }}</dd>
                                        </div>
                                    </div>
                                </div>

                            </div> --}}
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12">
                                <div class="grid gap-2">
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs dark:text-gray-400 w-1/4 font-semibold">Date</dt>
                                        <dd class="pl-2 text-xs  text-black capitalize">
                                            {{ date('j, F , Y') }}</dd>
    
                                    </div>
                                    {{-- @dd($challanSeries); --}}
                                    @php
                                          $challanModifyData = json_decode($challanModifyData, true); // Set the second parameter to true to get an associative array
                                          $createChallanRequest = $challanModifyData ?? []; 
                                    @endphp
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Series No.</dt>
                                        <dd
                                            class="pl-2 text-xs text-black 
                                     ">
                                            {{ $createChallanRequest['purchase_order_series'] ?? null }}-{{ $createChallanRequest['series_num'] }}
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
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        Ship To
                    </h5>

                    {{-- <li x-show="search === '' || '{{ strtolower($detail->address ?? null) }}'.includes(search.toLowerCase())"
                    wire:click="selectUser('{{ $selectedUser['challanSeries'] }}', '{{ $detail->address ?? null }}', '{{ $selectedUser['email'] }}', '{{ $detail->phone ?? null }}', '{{ $detail->gst_number ?? null }}')">
                    <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                        <label class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $detail->address ?? null }}</label>
                    </div>
                    </li> --}}


                    <div x-data="{ search: '', selectedUserDetails: null }">
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownDetailSearchButton" data-dropdown-toggle="dropdownDetailSearch" data-dropdown-placement="bottom" data-dropdown-trigger="click" class="text-black border border-gray-400 flex w-full bg-white hover:bg-orange focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg text-xs px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover-bg-[#050708]/30 mr-2 mb-2" type="button">
                            {{ ucfirst($createChallanRequest ? $createChallanRequest['buyer_name'] : 'Click To Choose')}}
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div x-data="{ search: '', selectedUserDetails: null }" id="dropdownDetailSearch" class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                            <!-- Search input -->
                            <div class="p-3">
                                <label for="input-address-search" class="sr-only">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                    </div>
                                    <input x-model="search" type="text" id="input-address-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search user">
                                </div>
                            </div>
                            <!-- Filtered list based on search -->
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDetailSearchButton">
                                @if(isset($Sellers))
                                @foreach ($Sellers as $user)
                                <li x-show="search === '' || '{{ strtolower($user->seller_name ?? null) }}'.includes(search.toLowerCase())" wire:click="selectUser('{{ $user->invoice_number->purchase_order_series ?? 'Not Assigned' }}', '{{ $user->details[0]->address ?? null }}', '{{ $user->user->email ?? null }}', '{{ $user->details[0]->phone ?? null }}', '{{ $user->details[0]->gst_number ?? null }}','{{ $user->seller_name ?? null }}', '{{ json_encode($user ?? null) }}')">
                                    <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">{{ $user->seller_name ?? null }}</label>
                                    </div>
                                </li>
                                @endforeach
                                @endif
                            </ul>
                            {{-- <button
                                class="flex w-full items-center p-3 text-sm font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
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
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Address :</dt>
                            <dd class="pl-5 text-xs text-black ">{{ $selectedUser['address'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">City :</dt>
                            <dd class="pl-5 text-xs text-black ">{{ $selectedUser['city'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">State :</dt>
                            <dd class="pl-5 text-xs text-black ">{{ $selectedUser['state'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Pincode :</dt>
                            <dd class="pl-5 text-xs text-black ">{{ $selectedUser['pincode'] ?? null }}</dd>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="rounded-lg bg-[#ebebeb] p-6 shadow-md">
            {{-- <div class="border-b border-gray-400 pb-4">
                <button wire:click.prevent="addRow" @if ($inputsDisabled==true ) disabled="" @endif class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700" style="background-color: #e5f811;">Add New Row</button>
                <button @if ($inputsDisabled==true ) disabled="" @endif class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700" style="background-color: #e5f811;">Add From Stock</button>
            </div> --}}
            <div class="border-b border-gray-400 ">
            <div class="overflow-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-300 text-left">
                            <th class="px-2 font-normal text-black text-sm">S.No.</th>
                            @foreach ($panelColumnDisplayNames as $columnName)
                            @if (!empty($columnName))
                            <th class="px-2 font-normal text-black text-sm">{{ $columnName }}</th>
                            {{-- @dd($columnName); --}}
                            @endif
                            @endforeach
                            <th class="px-2 font-normal text-black text-sm">Unit</th>

                           
                            <th class="px-2 font-normal text-black text-sm">Rate</th>

                            <th class="px-2 font-normal text-black text-sm">Qty</th>
                            <th class="px-2 font-normal text-black text-sm">Tax</th>
                            @if ($discountEntered) <th class="px-2 font-normal text-black text-sm">Discount</th> @endif


                            <th class="px-2 font-normal text-black text-sm">Total Amount</th>
                            <th class="px-2 font-normal text-black text-sm"></th>
                        </tr>
                        {{-- <tr>
                            <td colspan="5"></td>
                            <td colspan="5">
                                <div class="flex items-center">
                                    <input wire:model.defer="calculateTax"  id="calculateTax" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="calculateTax" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Without Tax</label>
                                </div>
                            </td>
                        </tr> --}}
                    </thead>
                    <tbody>
                        {{-- {{dd($createChallanRequest['order_details'])}} --}}
                        @foreach ($createChallanRequest['order_details'] as $index => $row)
                        {{-- <form wire:submit.prevent> --}}

                        {{-- {{dd($index,$row,$createChallanRequest['order_details'])}} --}}
                        <tr>
                            <td class="px-1 py-2"><input @if ($inputsDisabled==true) disabled="" @endif
                                value="{{ $index + 1 }}"
                                class="hsn-box h-7 w-10 rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                type="text" /></td>
                        @if($panelColumnDisplayNames)
                        @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                        @if (!empty($columnName))
                        @php
                        // count($panelColumnDisplayNames)
                        $this->createChallanRequest['order_details'][$index]['columns'][$key]['column_name']
                        = $columnName;
                        @endphp
                        <td class="px-1 py-2">
                            <input @if ($inputsDisabled==true) disabled="" @endif
                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                class="hsn-box h-7 w-24 dynamic-width-input rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                type="text" />
                        </td>
                        @endif
                        @endforeach
                        @endif
                        <td class="px-1 py-2">
                            <input @if ($inputsDisabled==true) disabled="" @endif type="text"
                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit"
                                class="hsn-box h-7 w-24 rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                value="" list="units" />
                                <datalist id="units">
                                    <option value="Pcs">Pcs</option>
                                    <option value="Mtr">Mtr</option>
                                    <option value="Ltr">Ltr</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Dozen">Dozen</option>
                                    <option class="add-unit" value="Add Unit">Add Unit</option>
                                    
                                </datalist>
                        </td>
                        <td class="px-2 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.rate"
                                class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none rate"
                                type="number" data-index="{{ $index }}" />
                        </td>
                        <td class="px-2 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.qty"
                                class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none qty"
                                type="number" data-index="{{ $index }}" />
                        </td>
                        <!-- Tax --> 
                        <td class="px-2 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.tax"
                                class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none tax"
                                type="number" placeholder="Tax %" data-index="{{ $index }}" data-tax-index="{{ $index }}" />
                        </td>

                        <!-- Total -->
                        <td class="px-2 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.total_amount"
                                class="hsn-box h-7 w-24 rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none total"
                                type="number" data-index="{{ $index }}" />
                        </td>

                        {{-- <td class="px-2 py-2">
                            <button type="button" wire:click.prevent="removeRow({{ $index }})"
                                class="bg-yellow-500 px-2 py-1 text-sm text-black hover:bg-yellow-700"
                                style="background-color: #e5f811;">X</button>
                        </td> --}}
                        </tr>
                        {{-- </form> --}}
                        @endforeach
                    </tbody>
                </table>
                @foreach ($createChallanRequest['order_details'] as $index => $row)
                                    <div id="totals-container" class="text-right text-xs text-black mr-5" wire:ignore></div>
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
                
                        const totalAmountField = document.querySelector('.totalAmountField');
                        if (totalAmountField) {
                            totalAmountField.value = discountedTotalAmount.toFixed(2);
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
                            hiddenTotalField.value = discountedTotalAmount.toFixed(2);
                            // Trigger Livewire to recognize the change
                            hiddenTotalField.dispatchEvent(new Event('input'));
                        }
                    }
                
                    document.addEventListener('DOMContentLoaded', function () {
                        document.body.addEventListener('input', debounce(calculateTotal, 300));
                        const calculateTaxCheckbox = document.getElementById('calculateTax');
                        if (calculateTaxCheckbox) {
                            calculateTaxCheckbox.addEventListener('change', calculateTotal);
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
                </script>
                    




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
    {{-- <!-- discount -->
    <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
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
    </div> --}}
    <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
        <div class="col-span-1 py-2 text-xs text-black font-semibold">Total</div>
        <div class="col-span-8 py-2">
            <input @if ($inputsDisabled==true ) disabled="" @endif class="hsn-box h-8 w-11/12 rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" wire:model="createChallanRequest.total_words"  disabled />
        </div>
        
        <div class="col-span-2 flex items-center justify-between">
            <div class="col-span-1 w-24 py-2">
                <input 
                    @if ($inputsDisabled == true) readonly @endif
                    class="hsn-box h-8 w-full rounded-lg bg-white border border-gray-400 text-xs text-black focus:outline-none totalQtyField"
                    type="text"  readonly
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
                    type="text"  readonly
                />
                <input type="hidden" wire:model.defer="createChallanRequest.total" />
            </div>  
        </div>
        
        
        
        
        
    </div>
</tbody>

            </div>
              

                <div class="flex justify-center space-x-4 lg:text-lg text-xs pb-2">
                   

                    @if ($mainUser->team_user != null)
                    @if ($mainUser->team_user->permissions->permission->seller->create_invoice == 1)
                    <button type="button" @if ($action=='save' ) wire:click.prevent='savePurchaseOrder'
                        @elseif($action=='edit' ) wire:click.prevent='savePurchaseOrder' @endif @if ($inputsDisabled==true) disabled="" @endif @if ($inputsResponseDisabled==false) disabled
                        @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black  @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2  "
                        wire:loading.attr="disabled" wire:target="savePurchaseOrder, savePurchaseOrder"
                        wire:click="disableSaveButton">
                        Save
                    </button>
                    @endif
                    @else
                    <button type="button" @if ($action=='save' ) wire:click.prevent='savePurchaseOrder'
                        @elseif($action=='edit' ) wire:click.prevent='savePurchaseOrder' @endif @if  ($inputsDisabled==true) disabled="" @endif @if ($inputsResponseDisabled==false) disabled
                        @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black  @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2  ">Save</button>
                    @endif


                    

                    @if ($mainUser->team_user != null)
                            @if ($mainUser->team_user->permissions->permission->seller->modify_invoice == 1)
                            <button wire:click.prevent='invoiceEdit' type="button" @if ($inputsResponseDisabled==true)
                                disabled="" @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gr px-4ay-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
                            @endif
                            @else
                            <button wire:click.prevent='invoiceEdit' type="button" @if ($inputsResponseDisabled==true)
                                disabled="" @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
                            @endif

                    {{-- <button wire:click.prevent='sendPurchaseOrder({{ $purchaseOrderId }})' @if ($inputsResponseDisabled==true) disabled="" @endif class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button> --}}
                     

                @if ($mainUser->team_user != null)
                            @if ($mainUser->team_user->permissions->permission->seller->send_invoice == 1)
                            <button wire:click.prevent='sendPurchaseOrder({{ $purchaseOrderId }})'
                    @if ($inputsResponseDisabled || strpos($successMessage, 'Success: Your Feature usage limit is over or expired.') !== false)
                        disabled
                    @endif
                    class="rounded-full @if ($inputsResponseDisabled || strpos($successMessage, 'Success: Your Feature usage limit is over or expired.') !== false)  bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black " >
                    Send    
                </button>
                            @endif
                            @else
                            <button wire:click.prevent='sendPurchaseOrder({{ $purchaseOrderId }})'
                    @if ($inputsResponseDisabled || strpos($successMessage, 'Success: Your Feature usage limit is over or expired.') !== false)
                        disabled
                    @endif
                    class="rounded-full @if ($inputsResponseDisabled || strpos($successMessage, 'Success: Your Feature usage limit is over or expired.') !== false)  bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black " >
                    Send
                </button>
                            @endif
                


                    <button @if ($inputsResponseDisabled==true) disabled="" @endif class="rounded-full bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>
                </div>
            </div>
    </form>
    </form>

 
    <!-- Modal for adding custom unit -->

    {{-- MODAL --}}

    {{-- SCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const articleFilter = document.getElementById("article-filter");
            const unitFilter = document.getElementById("unit-filter");
            const tableRows = document.querySelectorAll(".stock-table tr");

            articleFilter.addEventListener("change", function() {
                const selectedArticle = articleFilter.value.toLowerCase();

                tableRows.forEach((row) => {
                    const articleCell = row.querySelector(".Article");
                    // console.log(articleCell);
                    if (!selectedArticle || articleCell.textContent.toLowerCase().includes(
                            selectedArticle)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });

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
        });
    </script>
    <script>
        // Function to show the modal
        // function showModal() {
        //     console.log( document.getElementById("addUnitModal"));
        //     document.getElementById("addUnitModal").classList.remove("hidden");
        // }

        // // Function to hide the modal
        // function hideModal() {
        //     document.getElementById("addUnitModal").classList.add("hidden");
        // }

        // // Add event listener to the "Add Unit" option
        // const addUnitOption = document.querySelector('.add-unit');

        // addUnitOption.addEventListener("click", showModal);

        // // Add event listener to the "Save" button
        // const saveCustomUnitButton = document.getElementById("saveCustomUnitButton");
        // saveCustomUnitButton.addEventListener("click", () => {
        //     const customUnitInput = document.getElementById("customUnitInput");
        //     const customUnit = customUnitInput.value;

        //     // Do something with the customUnit value, e.g., update your data or add it to the dropdown

        //     // Close the modal
        //     hideModal();
        // });
    </script>
    {{-- SCRIPT --}}


</div>
