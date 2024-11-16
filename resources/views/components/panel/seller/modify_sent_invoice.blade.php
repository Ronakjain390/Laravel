<div class="rounded-lg dark:border-gray-700 mt-4">
    MODIFY
    @if (!empty($showData) && isset($showData['original']['data']))
    @php
    $data = $showData['original']['data'];
    @endphp

    <h1>Invoice Details</h1>

    <p>Invoice ID: {{ $data['id'] }}</p>
    <p>Invoice Series: {{ $data['invoice_series'] }}</p>
    <p>Series Number: {{ $data['series_num'] }}</p>
    <p>Seller ID: {{ $data['seller_id'] }}</p>
    <!-- Add more fields as needed -->

    <h2>Order Details</h2>
    @if (!empty($data['order_details']))
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Quantity</th>
                <!-- Add more headers as needed -->
            </tr>
        </thead>
        <tbody>
            @foreach ($data['order_details'] as $orderDetail)
            <tr>
                <td>{{ $orderDetail['id'] }}</td>
                <td>{{ $orderDetail['unit'] }}</td>
                <td>{{ $orderDetail['rate'] }}</td>
                <td>{{ $orderDetail['qty'] }}</td>
                <!-- Add more columns as needed -->
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No order details found.</p>
    @endif
    @else
    <p>No data available.</p>
    @endif

    MODIFY
    @if (!empty($showData) && isset($showData['original']['data']))
    @php
    $data = $showData['original']['data'];
    @endphp
    <form wire:submit.prevent='invoiceCreate'>
        <!-- First Row - Responsive 2 Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <!-- Column 1 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#e2dfdf]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        Seller
                    </h5>

                    <div class="w-full text-gray-900 dark:text-white">

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-6">
                                <div class="grid gap-2">
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400 w-1/4">Date :</dt> {{ $data['invoice_date'] }}
                                        <!-- <input wire:model="create_invoice_request.invoice_date" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 w-32" placeholder="Select date"> -->
                                        <!-- <dd class="pl-4 text-sm font-semibold text-black capitalize" wire:model="create_challan_request.invoice_date">{{ date('j F , Y') }}</dd> -->


                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Invoice No :</dt>
                                        <dd class="pl-2 text-sm font-semibold capitalize ">
                                            {{ $data['invoice_series'] }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Address</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">{{ $data['invoice_series'] }}</dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Email</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">{{ $data['invoice_series'] }}</dd>
                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Phone :</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">{{ $data['invoice_series'] }}</dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">GST</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">{{ $data['invoice_series'] }}</dd>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endif
            <!-- Column 2 -->

            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800" x-data="{ showFields: true }">
                <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#e2dfdf]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        Buyer
                    </h5>
                    <div x-data="{ search: '', selectedUser: null }" wire:ignore>
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom" data-dropdown-trigger="hover" class="text-white flex w-full bg-[#24292F] hover:bg-[#24292F]/90 focus:ring-4 focus:outline-none focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-gray-500 dark:hover:bg-[#050708]/30 mr-2 mb-2" type="button">
                            Click To Choose Buyer
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div x-data="{ search: '', selectedUser: null }" id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                            <!-- Search input -->
                            <div class="p-3">
                                <label for="input-group-search" class="sr-only">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                    </div>
                                    <input x-model="search" type="text" id="input-group-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search user">
                                </div>
                            </div>
                            <!-- Filtered list based on search -->
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">
                                @foreach ($billTo as $user)
                                <!-- {{-- {{dd($user->details[0])}} --}} -->
                                <li x-show="search === '' || '{{ strtolower($user->buyer_name ?? null) }}'.includes(search.toLowerCase())" wire:click="selectUser('{{ $user->invoice_number->series_number ?? 'Not Assigned' }}', '{{ $user->details[0]->address ?? null }}', '{{ $user->email ?? null }}', '{{ $user->details[0]->phone ?? null }}', '{{ $user->details[0]->gst_number ?? null }}','{{ $user->buyer_name ?? null }}', '{{ json_encode($user ?? null) }}')">
                                    <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">{{ $user->buyer_name ?? null }}</label>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <button class="flex w-full items-center p-3 text-sm font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
                                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                    <path d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-2V5a1 1 0 0 0-2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 0 0 2 0V9h2a1 1 0 1 0 0-2Z" />
                                </svg>
                                Add user
                            </button>
                        </div>
                    </div>



                    <!-- Add your input fields here -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="grid gap-2">
                            <div class="flex flex-row">
                                <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4 whitespace-nowrap">Address Name :</dt>
                                <dd class="pl-5 text-sm font-semibold text-black capitalize">Default</dd>
                            </div>
                            <div class="flex flex-row">
                                <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Address :</dt>
                                <dd class="pl-5 text-sm font-semibold text-black capitalize">{{ $selectedUser['address'] ?? null }}</dd>
                            </div>
                            <div class="flex flex-row">
                                <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">City :</dt>
                                <dd class="pl-5 text-sm font-semibold text-black capitalize">{{ $selectedUser['city'] ?? null }}</dd>
                            </div>
                            <div class="flex flex-row">
                                <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">State :</dt>
                                <dd class="pl-5 text-sm font-semibold text-black capitalize">{{ $selectedUser['state'] ?? null }}</dd>
                            </div>
                            <div class="flex flex-row">
                                <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Pincode :</dt>
                                <dd class="pl-5 text-sm font-semibold text-black capitalize">{{ $selectedUser['pincode'] ?? null }}</dd>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
</div>
</div>

</div>
<div class="rounded-lg bg-[#e2dfdf] p-6 shadow-md">
    <div class="border-b border-gray-400 pb-4">
        <button wire:click.prevent="addRow" @if ($inputsDisabled==true ) disabled="" @endif class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700" style="background-color: #e5f811;">Add New Row</button>
    </div>
    <div class="border-b border-gray-400">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-300 text-left">
                    <th class="px-2 font-normal">S.No.</th>
                    <th class="px-2 font-normal">Article</th>
                    <th class="px-2 font-normal">HSN</th>
                    <th class="px-2 font-normal">Unit</th>
                    <th class="px-2 font-normal">Details</th>

                    <!-- @foreach ($ColumnDisplayNames as $columnName)
                        <th class="px-2  font-normal">{{ $columnName }}</th>
                        @endforeach -->
                    <th class="px-2 font-normal">Rate</th>

                    <th class="px-2 font-normal">Qty</th>
                    <th class="px-2 font-normal">Tax</th>
                    @if ($discountEntered) <th class="px-2 font-normal">Discount</th> @endif


                    <th class="px-2 font-normal">Total Amount</th>
                    <th class="px-2 font-normal"></th>
                </tr>

            </thead>
            <tbody>
                {{-- {{dd($create_invoice_request['order_details'])}} --}}
                @foreach ($create_invoice_request['order_details'] as $index => $row)
                {{-- <form wire:submit.prevent> --}}

                {{-- {{dd($index,$row,$create_invoice_request['order_details'])}} --}}
                <tr>
                    <td class="px-1 py-2"><input @if ($inputsDisabled==true ) disabled="" @endif value="{{$index+1 }}" class="hsn-box h-7 w-10 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" /></td>
                    @foreach ($ColumnDisplayNames as $key => $columnName)
                    @php
                    $this->create_invoice_request['order_details'][$index]['columns'][$key]['column_name'] = $columnName;
                    @endphp
                    <td class="px-1 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="create_invoice_request.order_details.{{ $index }}.columns.{{$key}}.column_value" class="hsn-box h-7 w-25 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" />
                    </td>
                    @endforeach
                    <td class="px-1 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif type="text" wire:model="create_invoice_request.order_details.{{ $index }}.unit" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" value="" list="units" /><datalist id="units">
                            <option>Pcs</option>
                            <option>Mtr</option>
                            <option>Ltr</option>
                            <option>Kg</option>
                            <option>Dozen</option>
                        </datalist>
                    </td>
                    <td class="px-1 py-2"><input @if ($inputsDisabled==true ) disabled="" @endif wire:model="create_invoice_request.order_details.{{ $index }}.details" class="hsn-box h-7 w-25 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" /></td>
                    <td class="px-2 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="create_invoice_request.order_details.{{ $index }}.rate" wire:keyup="updateTotalAmount({{ $index }})" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" />
                    </td>
                    <td class="px-2 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="create_invoice_request.order_details.{{ $index }}.qty" wire:keyup="updateTotalAmount({{ $index }})" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" />
                    </td>
                    <!-- Tax -->
                    <td class="px-2 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="create_invoice_request.order_details.{{ $index }}.tax" wire:keyup="updateTotalAmount({{ $index }})" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" placeholder="Tax %" />
                    </td>

                    <!-- discount -->
                    @if ($discountEntered)
                    <td class="px-2 py-2">
                        <input @if ($inputsDisabled) disabled="" @endif wire:model="create_invoice_request.order_details.{{ $index }}.discount" wire:keyup="updateTotalAmount({{ $index }})" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" placeholder="Discount" />
                    </td>
                    @endif
                    <td class="px-2 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="create_invoice_request.order_details.{{ $index }}.total_amount" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" disabled />
                    </td>
                    <td class="px-2 py-2">
                        <button wire:click="removeRow({{ $index }})" class="bg-yellow-500 px-4 py-1 text-black hover:bg-yellow-700" style="background-color: #e5f811;">X</button>
                    </td>
                </tr>
                {{-- </form> --}}
                @endforeach
            </tbody>
        </table>
        @php
        $prevTax = null;
        @endphp
        @foreach ($create_invoice_request['order_details'] as $index => $orderDetail)
        <!-- Check if tax value is different from the previous row -->
        @if ($orderDetail['tax'] != $prevTax)
        <td class="px-2 py-2">
            <div class="text-xs font-medium text-right pr-6 pt-2">
                <p>Total Sale at {{ $orderDetail['tax'] }}% : {{ number_format($orderDetail['total_amount'], 2) }}</p>
                <p>CGST at {{ $orderDetail['cgst_rate'] }}% : {{ number_format($orderDetail['cgst'], 2) }}</p>
                <p>SGST at {{ $orderDetail['sgst_rate'] }}% : {{ number_format($orderDetail['sgst'], 2) }}</p>

            </div>
        </td>
        @endif

        <!-- Other columns for each row -->
        <!-- ... -->

        <!-- Update the previous tax value -->
        @php
        $prevTax = $orderDetail['tax'];
        @endphp
        @endforeach


        <tbody>
            <div class="mb-1 grid grid-cols-12 border-t border-gray-400">
                <div class="col-span-1 py-2">Comment</div>
                <div class="col-span-10 py-2">
                    <input @if ($inputsDisabled==true ) disabled="" @endif wire:model='create_invoice_request.comment' class="hsn-box h-8 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" />
                </div>
            </div>
            <!-- discount -->
            <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                <div class="col-span-1 py-2">Discount</div>
                <div class="col-span-9 py-2">
                    <!-- <input @if ($inputsDisabled==true ) disabled="" @endif class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" value="{{ $totalAmount }}" disabled style="background-color: #423E3E;" /> -->
                </div>

                <div class="col-span-2 flex items-center justify-between">
                    <div class="col-span-1 px-4 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif class="hsn-box h-8 w-full rounded-lg text-center font-mono text-xs font-normal  bg-gray-300  focus:outline-none" type="text" value="Discount %" disabled />
                    </div>
                    <div class="col-span-1 px-4 py-2">
                        <input @if ($inputsDisabled) @endif wire:model="create_invoice_request.order_details.{{ $index }}.discount" wire:keyup="updateTotalAmount({{ $index }}); $emit('discountEntered', {{ $index }})" class="hsn-box h-8 w-full rounded-lg text-center font-mono text-xs font-normal bg-gray-300 focus:outline-none" type="text" />
                    </div>

                </div>
            </div>
            <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                <div class="col-span-1 py-2">Total</div>
                <div class="col-span-9 py-2">
                    <input @if ($inputsDisabled==true ) disabled="" @endif class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" value="{{ $totalAmount }}" disabled style="background-color: #423E3E;" />
                </div>
                <div class="col-span-2 flex items-center justify-between">
                    <div class="col-span-1 px-4 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif class="hsn-box h-8 w-full rounded-lg text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" value="{{ $create_invoice_request['total_qty'] }}" disabled style="background-color: #423E3E;" />
                    </div>
                    <div class="col-span-1 px-4 py-2">
                        <input @if ($inputsDisabled==true ) disabled="" @endif class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" value="{{ $create_invoice_request['total'] }}" disabled style="background-color: #7449F0;" />
                    </div>
                </div>
            </div>
        </tbody>
        <div class="flex justify-center space-x-4">
            <button type="submit" class="rounded-full bg-gray-300 px-8 py-2 text-black hover:bg-yellow-700 hover:text-white">Save</button>
            <button class="rounded-full bg-gray-300 px-8 py-2 text-black hover:bg-yellow-700 hover:text-white">Edit</button>
            <button class="rounded-full bg-gray-300 px-8 py-2 text-black hover:bg-yellow-700 hover:text-white">Send</button>
            <button class="rounded-full bg-gray-300 px-8 py-2 text-black hover:bg-yellow-700 hover:text-white">SFP</button>
        </div>
    </div>
    </form>
</div>
