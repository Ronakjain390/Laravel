<div class="rounded-lg dark:border-gray-700 mt-4">
    <form wire:submit.prevent='invoiceCreate'>
        <!-- First Row - Responsive 2 Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4" wire:ignore>
            <!-- Column 1 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800" wire:ignore>
                <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        BUYER
                    </h5>
                    @php
                    $challanModifyData = json_decode($challanModifyData, true);
                    // Set the second parameter to true to get an associative array
                    $createChallanRequest = $challanModifyData ?? [];

                    // dd($createChallanRequest);
                    // $createChallanRequest['order_details'];
                    @endphp
                    <div class="w-full text-gray-900 dark:text-white">

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-6">
                                <div class="grid gap-2">
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400 w-1/4">Date : </dt>
                                        <dd class="pl-4 text-sm font-semibold text-black capitalize">
                                            {{ date('j F , Y') }}
                                            <input type="hidden" name="invoice_date" value="{{ date('Y-m-d') }} ">
                                        </dd>
                                    </div>
                                    {{-- @php
                                        $selectedUser = json_decode($this->selectedUser);
                                        @dump($selectedUser['purchase_order_series'])
                                    @endphp --}}
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400 w-1/4">PO No:</dt>
                                         <dd
                                            class="pl-4 text-sm font-semibold text-black capitalize">
                                            {{ $createChallanRequest['purchase_order_series'] ?? null }}-{{ $createChallanRequest['series_num'] }}
                                        </dd>


                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Name : </dt>
                                        <dd class="pl-4 text-sm font-semibold text-black capitalize">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->name ?? null }}</dd>
                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Address : </dt>
                                        <dd class="pl-4 text-sm font-semibold text-black capitalize">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->address ?? null }}</dd>
                                    </div>


                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Email : </dt>
                                        <dd class="pl-4 text-sm font-semibold text-black capitalize">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->email ?? null }}</dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Phone : </dt>
                                        <dd class="pl-4 text-sm font-semibold text-black capitalize">
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
                        Seller
                    </h5>

                    <div x-data="{ search: '', selectedUserDetails: null }">
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownDetailSearchButton" data-dropdown-toggle="dropdownDetailSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class="text-black border border-gray-400 flex w-full bg-white hover:bg-orange focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                            type="button">
                            @if ($buyerName == '')
                            <span x-cloak>{{ ucfirst($createChallanRequest ? $createChallanRequest['buyer_name'] : 'Click To Choose')}}</span>
                        @else
                        <span> {{ strToUpper($buyerName) }}</span>
                        @endif
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
                                <li x-show="search === '' || '{{ strtolower($user->seller_name ?? null) }}'.includes(search.toLowerCase())" wire:click="selectUser('{{ $user->invoice_number->purchase_order_series ?? 'Not Assigned' }}', '{{ $user->details[0]->address ?? null }}', '{{ $user->user->email ?? null }}', '{{ $user->details[0]->phone ?? null }}', '{{ $user->details[0]->gst_number ?? null }}','{{ $user->seller_name ?? null }}', '{{ json_encode($user ?? null) }}')">
                                    <div class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">{{ $user->seller_name ?? null }}</label>
                                    </div>
                                </li>
                                @endforeach
                                @endif
                            </ul>

                        </div>
                    </div>
                    <div class="grid gap-2">
                        <div class="flex flex-row">
                            <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Phone</dt>
                            <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                {{ $selectedUser['phone'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Email</dt>
                            <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                {{ $selectedUser['email'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">GST</dt>
                            <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                {{ $selectedUser['gst'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Address</dt>
                            <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                {{ $selectedUser['address'] ?? null }}</dd>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="rounded-lg bg-[#ebebeb] p-6 shadow-md">
            <div class="border-b border-gray-300 pb-4">
                <button wire:click.prevent="addRow" @if ($inputsDisabled == true) disabled="" @endif
                    class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700"
                    style="background-color: #e5f811;">Add New Row</button>
                {{-- <a data-modal-target="addProductModal" data-modal-toggle="addProductModal"
                    @if ($inputsDisabled == true) disabled="" @endif
                    class="rounded-full bg-yellow-500 py-1 px-4 text-black hover:bg-yellow-700"
                    style="background-color: #e5f811;">Add From Stock</a> --}}
            </div>
            <div class="overflow-auto">
                <div>
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-300 text-left">
                                <th class="px-2 font-normal">S.No.</th>
                                @foreach ($panelColumnDisplayNames as $columnName)
                                @if (!empty($columnName))
                                <th class="px-2 font-normal">{{ $columnName }}</th>
                                {{-- @dd($columnName); --}}
                                @endif
                                @endforeach
                                <th class="px-2 font-normal">Unit</th>


                                <th class="px-2 font-normal">Rate</th>
                                <th class="px-2 font-normal">Qty</th>
                                <th class="px-2 font-normal">Tax</th>
                                <th class="px-2 font-normal">Total Amount</th>
                                <th class="px-2 font-normal"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($createChallanRequest['order_details'] as $index => $row)
                                <tr>
                                    <td class="px-1 py-2"><input @if ($inputsDisabled == true) disabled="" @endif
                                            value="{{ $index + 1 }}"
                                            class="hsn-box h-7 w-10 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" /></td>
                                            @if($panelColumnDisplayNames)
                                    @foreach ($panelColumnDisplayNames as $key => $columnName)
                                    @if (!empty($columnName))
                                        @php
                                            $this->createChallanRequest['order_details'][$index]['columns'][$key]['column_name'] = $columnName;
                                        @endphp
                                        <td class="px-1 py-2">
                                            <input @if ($inputsDisabled == true) disabled="" @endif
                                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                class="hsn-box h-7 -24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                                type="text" />
                                        </td>
                                        @endif
                                        @endforeach
                                        @endif
                                    <td class="px-1 py-2">
                                        <input @if ($inputsDisabled == true) disabled="" @endif type="text"
                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit"
                                            class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
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

                                    <td class="px-1 py-2"><input @if ($inputsDisabled == true) disabled="" @endif
                                            value=""
                                            class="hsn-box h-7 w-25 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" /></td>
                                            <td class="px-2 py-2">
                                                <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="createChallanRequest.order_details.{{ $index }}.rate" wire:keyup="updateTotalAmount({{ $index }})" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" />
                                            </td>
                                            <td class="px-2 py-2">
                                                <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="createChallanRequest.order_details.{{ $index }}.qty" wire:keyup="updateTotalAmount({{ $index }})" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" />
                                            </td>
                                            <!-- Tax -->
                                            <td class="px-2 py-2">
                                                <input @if ($inputsDisabled==true ) disabled="" @endif wire:model="createChallanRequest.order_details.{{ $index }}.tax" wire:keyup="updateTotalAmount({{ $index }})" class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="number" placeholder="Tax %" />
                                            </td>
                                    <td class="px-2 py-2">
                                        <input @if ($inputsDisabled == true) disabled="" @endif
                                            wire:model.defer="createChallanRequest.order_details.{{ $index }}.total_amount"
                                            class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                            type="number" disabled />
                                    </td>
                                    <td class="px-2 py-2">
                                        <button type="button" wire:click.prevent="removeRow({{ $index }})"
                                            class="bg-yellow-500 px-2 py-1 text-black hover:bg-yellow-700"
                                            style="background-color: #e5f811;">X</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @php
                    $prevTax = null;
                    @endphp

                    @foreach ($createChallanRequest['order_details'] as $index => $orderDetail)
                    @if ($orderDetail['tax'] != $prevTax)
                    <td class="px-2 py-2">
                        <div class="text-xs font-medium text-right pr-6 pt-2">
                            @if ($calculateTax)
                            <p>Total Sale at {{ $orderDetail['tax'] }}% : {{ number_format($orderDetail['total_without_tax'], 2) }}</p>
                            @else
                            {{-- <p>Total Sale at {{ $orderDetail['tax'] }}% : {{ number_format($orderDetail[$key]['net_price'], 2) }}</p> --}}
                            @endif
                            {{-- <p>CGST at {{ $orderDetail[$key]['cgst_rate'] }}% : {{ number_format($orderDetail['cgst'], 2) }}</p> --}}
                            {{-- <p>SGST at {{ $orderDetail[$key]['sgst_rate'] }}% : {{ number_format($orderDetail['sgst'], 2) }}</p> --}}


                        </div>
                    </td>
                    @endif

                    @php
                    $prevTax = $orderDetail['tax'];
                    @endphp
                    @endforeach

                </div>

                    <div class="mb-1 grid grid-cols-12 border-t-2 border-gray-300 w-full">
                        <div class="col-span-1 py-2">Comment</div>
                        <div class="col-span-10 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                wire:model.defer='createChallanRequest.comment'
                                class="hsn-box h-8 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                type="text" />
                        </div>
                    </div>
                    <div class="mb-4 grid grid-cols-12 border-t-2 border-gray-300 w-full">
                        <div class="col-span-1 py-2">Total</div>
                        <div class="col-span-8 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                class="hsn-box h-8 w-11/12 rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                {{-- type="text" value="{{ $createChallanRequest['total_words'] }}" disabled --}}
                                style="background-color: #423E3E;" />
                        </div>
                        <div class="col-span-2 flex items-center justify-between ">
                            <div class="col-span-1 w-24 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif
                                    class="hsn-box h-8 w-full rounded-lg text-center font-mono text-xs font-normal text-black focus:outline-none"
                                    {{-- type="text" value="{{ $createChallanRequest['total_qty'] }}" disabled --}}
                                    style="background-color: #423E3E;" />
                            </div>
                            <div class="col-span-1 w-24 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif
                                    class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                    type="text" value="{{ $createChallanRequest['total'] }}" disabled
                                    style="background-color: #7449F0;" />
                            </div>
                        </div>
                    </div>
            </div>
            <div class="flex justify-center space-x-4">
                {{-- {{dd($mainUser)}} --}}
                @php
                    $mainUser = json_decode($mainUser);
                @endphp
                    @if ($mainUser->team_user != null)
                        @if ($mainUser->team_user->permissions->permission->seller->create_purchase_order == 1)
                            <button type="button"
                                @if ($action == 'save') wire:click.prevent='savePurchaseOrder' @elseif($action == 'edit') wire:click.prevent='savePurchaseOrder' @endif
                                @if ($inputsDisabled == true) disabled="" @endif
                                class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif lg:px-8 px-4 py-2  ">Save</button>
                        @endif
                    @else
                        <button type="button"
                            @if ($action == 'save') wire:click.prevent='savePurchaseOrder' @elseif($action == 'edit') wire:click.prevent='savePurchaseOrder' @endif
                            @if ($inputsDisabled == true) disabled="" @endif
                            class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif lg:px-8 px-4 py-2  ">Save</button>
                    @endif

                @if ($mainUser->team_user != null)
                    @if ($mainUser->team_user->permissions->permission->seller->modify_purchase_order == 1)
                        <button wire:click.prevent='purchaseOrderEdit' type="button"
                            @if ($inputsResponseDisabled == true) disabled="" @endif
                            class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Edit</button>
                    @endif
                @else
                    <button wire:click.prevent='purchaseOrderEdit' type="button"
                        @if ($inputsResponseDisabled == true) disabled="" @endif
                        class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Edit</button>
                @endif

                @if ($mainUser->team_user != null)
                    @if ($mainUser->team_user->permissions->permission->seller->send_purchase_order == 1)
                        <button wire:click.prevent='sendPurchaseOrder({{ $purchaseOrderId }})'
                            @if ($inputsResponseDisabled == true) disabled="" @endif
                            class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
                    @endif
                @else
                    <button wire:click.prevent='sendPurchaseOrder({{ $purchaseOrderId }})'
                        @if ($inputsResponseDisabled == true) disabled="" @endif
                        class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
                @endif
                {{-- <button @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button> --}}
            </div>
        </div>
    </form>

</div>
