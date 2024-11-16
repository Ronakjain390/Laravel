<div class="rounded-lg dark:border-gray-700 mt-4">
    {{-- <div
        class="p-1 mb-2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 flex items-center justify-between">
        <!-- Left side: Download Challan Csv Sheet button -->
        <a href="{{ route('sender.exportColumns') }}"
            class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-500 rounded hover:bg-green-600 focus:ring-2 focus:ring-offset-2 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            Download Upload CSV File
            <svg class="w-3 h-3 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M1 5h12m0 0L9 1m4 4L9 9" />
            </svg>
        </a>

        <!-- Right side: Upload CSV Sheet input group -->
        <div class="flex items-center space-x-2 w-5/12">
            <div class="w-full">
                <form wire:submit.prevent="bulkChallantUpload" enctype="multipart/form-data">
                    <div class="relative">
                        <input type="file" wire:model="file"
                            class="appearance-none block bg-white border border-gray-300 w-full rounded py-1 px-2 leading-tight focus:outline-none focus:border-blue-500">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded"
                                type="submit">
                                Bulk Challan Create
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}


@php
    $createChallanRequest = (array) json_decode($createChallanRequest,true);
    // dd($createChallanRequest['order_details']);
@endphp
    <form>
        <!-- First Row - Responsive 2 Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <!-- Column 1 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#e2dfdf]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        Receiver
                    </h5>
                    <div x-data="{ search: '', selectedUser: null }" wire:ignore>
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class="text-white flex w-full bg-[#24292F] hover:bg-[#24292F]/90 focus:ring-4 focus:outline-none focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-gray-500 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                            type="button">
                            Click To Choose
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
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
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
                            <!-- Filtered list based on search -->
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownSearchButton">
                                @foreach ($billTo as $user)
                                    <li class="cursor-pointer"
                                        x-show="search === '' || '{{ strtolower($user->receiver_name ?? null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="selectUser('{{ $user->series_number->series_number ?? 'Not Assigned' }}', '{{ $user->details[0]->address ?? null }}', '{{ $user->user->email ?? null }}', '{{ $user->details[0]->phone ?? null }}', '{{ $user->details[0]->gst_number ?? null }}','{{ $user->receiver_name ?? null }}', '{{ json_encode($user ?? null) }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $user->receiver_name ?? null }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <button
                                class="flex w-full items-center p-3 text-sm font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
                                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 18">
                                    <path
                                        d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-2V5a1 1 0 0 0-2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 0 0 2 0V9h2a1 1 0 1 0 0-2Z" />
                                </svg>
                                Add user
                            </button>
                        </div>
                    </div>
                    <div class="w-full text-gray-900 dark:text-white">

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-6">
                                <div class="grid gap-2">
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Phone</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                            {{ $selectedUser['phone'] ?? null }}</dd>
                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Email</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                            {{ $selectedUser['email'] ?? null }}</dd>
                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">GST</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                            {{ $selectedUser['gst'] ?? null }}</dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400 w-1/4">Address</dt>
                                        <dd class="pl-2 text-sm font-semibold text-black capitalize">
                                            {{ $selectedUser['address'] ?? null }}</dd>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-6">
                                <div class="grid">
                                    <div class="flex flex-row py-2">
                                        <dt class="w-1/4 mb-1 text-gray-500 md:text-sm dark:text-gray-400">Challan
                                            Series</dt>
                                        <dd
                                            class="pl-2 text-sm font-semibold capitalize @if (isset($selectedUser['challanSeries'])) @if ($selectedUser['challanSeries'] == 'Not Assigned') text-red-700 @else text-black @endif
@else
text-black @endif">

                                            {{ $selectedUser['challanSeries'] ?? null }}-{{ $selectedUser['seriesNumber'] ?? null }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-row py-2">
                                        <dt class="w-1/4 mb-1 text-gray-500 md:text-sm dark:text-gray-400">
                                            Date
                                        </dt>
                                        <dd class="pl-2 text-sm inline-block font-semibold text-black capitalize">
                                            <div class="relative">
                                                <input wire:model="createChallanRequest.challan_date"
                                                    type="date"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 w-32"
                                                    placeholder="Select date"
                                                    >
                                            </div>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
            <!-- Column 2 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full h-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#e2dfdf]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        Ship To
                    </h5>
                    <div x-data="{ search: '', selectedUser: null }" wire:ignore>
                        <!-- Button to toggle dropdown -->
                        <button id="userDetailsButton" data-dropdown-toggle="userDetails"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class="text-white flex w-full bg-[#24292F] hover:bg-[#24292F]/90 focus:ring-4 focus:outline-none focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-gray-500 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                            type="button">
                            Choose Another Address
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div x-data="{ search: '', selectedUser: null }" id="userDetails"
                            class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                            <!-- Search input -->
                            <div class="p-3">
                                <label for="input-group-search" class="sr-only">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                    </div>
                                    <input x-model="search" type="text" id="input-group-search"
                                        class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search user">
                                </div>
                            </div>
                            <!-- Filtered list based on search -->
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownSearchButton">

                                @foreach ($selectedUserDetails as $detail)
                                    <li x-show="search === '' || '{{ strtolower($detail->address ?? null) }}'.includes(search.toLowerCase())"
                                        wire:click="selectUser('{{ $selectedUser['challanSeries'] }}', '{{ $detail->address ?? null }}', '{{ $selectedUser['email'] }}', '{{ $detail->phone ?? null }}', '{{ $detail->gst_number ?? null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $detail->address ?? null }}</label>
                                        </div>
                                    </li>
                                @endforeach
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

        <div class="rounded-lg bg-[#e2dfdf] p-6 shadow-md">
            <div class="border-b border-gray-300 pb-4">
                <button wire:click.prevent="addRow"
                    class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700"
                    style="background-color: #e5f811;">Add New Row</button>
                <a data-modal-target="addProductModal" data-modal-toggle="addProductModal"

                    class="rounded-full bg-yellow-500 py-1 px-4 text-black hover:bg-yellow-700"
                    style="background-color: #e5f811;">Add From Stock</a>
            </div>
            <div class=" overflow-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-300 text-left">
                            <th class="px-2 font-normal">S.No.</th>
                            <th class="px-2 font-normal">Article</th>
                            <th class="px-2 font-normal">HSN</th>
                            <th class="px-2 font-normal">Unit</th>
                            <th class="px-2 font-normal">Details</th>

                            <!-- @foreach ($panelColumnDisplayNames as $columnName)
<th class="px-2  font-normal">{{ $columnName }}</th>
@endforeach -->
                            <th class="px-2 font-normal">Rate</th>
                            <th class="px-2 font-normal">Qty</th>
                            <th class="px-2 font-normal">Total Amount</th>
                            <th class="px-2 font-normal"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- {{dd($createChallanRequest)}} --}}
                        @foreach ($createChallanRequest['order_details'] as $index => $row)
                            {{-- <form wire:submit.prevent> --}}

                            {{-- {{dd($createChallanRequest['order_details'][$index])}} --}}
                            <tr>
                                <td class="px-1 py-2"><input
                                        value="{{ $index + 1 }}"
                                        class="hsn-box h-7 w-10 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        type="text" /></td>
                                @foreach ($panelColumnDisplayNames as $key => $columnName)
                                    @php
                                        $createChallanRequest['order_details'][$index]['columns'][$key]['column_name'] = $columnName;
                                    @endphp
                                    <td class="px-1 py-2">
                                        <input
                                            wire:model="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                            class="hsn-box h-7 w-25 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" />
                                    </td>
                                @endforeach
                                <td class="px-1 py-2">
                                    <input  type="text"
                                        wire:model="createChallanRequest.order_details.{{ $index }}.unit" value="{{$createChallanRequest['order_details'][$index]['unit']}}"
                                        class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                         list="units" />
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

                                <td class="px-1 py-2"><input

                                        class="hsn-box h-7 w-25 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        type="text" /></td>
                                <td class="px-2 py-2">
                                    <input
                                        wire:model="createChallanRequest.order_details.{{ $index }}.rate"
                                        wire:keyup="updateTotalAmount({{ $index }})" value="{{$createChallanRequest['order_details'][$index]['rate']}}"
                                        class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        min="0" type="number" />
                                </td>
                                <td class="px-2 py-2">
                                    <input
                                        wire:model="createChallanRequest.order_details.{{ $index }}.qty" value="{{$createChallanRequest['order_details'][$index]['qty']}}"
                                        wire:keyup="updateTotalAmount({{ $index }})"
                                        class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        min="1" type="number" />
                                </td>
                                <td class="px-2 py-2">
                                    <input
                                        wire:model="createChallanRequest.order_details.{{ $index }}.total_amount" value="{{$createChallanRequest['order_details'][$index]['total_amount']}}"
                                        class="hsn-box h-7 w-24 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        type="number" disabled />
                                </td>
                                <td class="px-2 py-2">
                                    <button type="button" wire:click.prevent="removeRow({{ $index }})"
                                        class="bg-yellow-500 px-4 py-1 text-black hover:bg-yellow-700"
                                        style="background-color: #e5f811;">X</button>
                                </td>
                            </tr>
                            {{-- </form> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>

            <tbody>
                {{-- {{dd($createChallanRequest)}} --}}
                <div class="mb-1 grid grid-cols-12 border-t-2 border-gray-300">
                    <div class="col-span-1 py-2">Comment</div>
                    <div class="col-span-10 py-2">
                        <input
                            wire:model='createChallanRequest.comment' value="{{$createChallanRequest['comment']}}"
                            class="hsn-box h-8 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                            type="text" />
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                    <div class="col-span-1 py-2">Total</div>
                    <div class="col-span-8 py-2">
                        <input
                            class="hsn-box h-8 w-11/12 rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                            type="text" value="{{ $createChallanRequest['total_words'] }}" disabled
                            style="background-color: #423E3E;" />
                    </div>
                    <div class="col-span-2 flex items-center justify-between ">
                        <div class="col-span-1 w-24 py-2">
                            <input
                                class="hsn-box h-8 w-full rounded-lg text-center font-mono text-xs font-normal text-black focus:outline-none"
                                type="text" value="{{ $createChallanRequest['total_qty'] }}" disabled
                                style="background-color: #423E3E;" />
                        </div>
                        <div class="col-span-1 w-24 py-2">
                            <input
                                class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                type="text" value="{{ $createChallanRequest['total'] }}" disabled
                                style="background-color: #7449F0;" />
                        </div>
                    </div>
                </div>
            </tbody>
            {{-- wire:click.prevent="sendChallan('{{ $columnName->id }}')" --}}
            <div class="flex justify-center space-x-4">
                <button type="button"  @if ($action == "save") wire:click.prevent='challanCreate' @elseif($action == "edit") wire:click.prevent='challanModify' @endif

                    class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif px-8 py-2  ">Save</button>

                <button wire:click.prevent='challanEdit' type="button" @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 px-8 py-2 text-black ">Edit</button>

                <button wire:click.prevent='sendChallan({{ $challanId }})'
                    @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 px-8 py-2 text-black ">Send</button>

                <button @if ($inputsResponseDisabled == true) disabled="" @endif
                    class="rounded-full bg-gray-300 px-8 py-2 text-black ">SFP</button>
            </div>
        </div>
    </form>

    {{-- MODAL --}}
    {{-- Add from stock --}}

    <div id="addProductModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
        wire:ignore.self>
        <div class="relative w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Add From Stock
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="addProductModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <div class="flex space-x-4">
                        <label for="article-filter" class="text-gray-600 dark:text-gray-400">Filter Article:</label>
                        <select id="article-filter" class="border rounded px-2 py-1">
                            <option >All</option>

                            @foreach ($products as $key => $product)
                                @php
                                    $product = (object) $product;
                                    $product_details = (object) $product->details[0];
                                @endphp
                                <option value="{{ $product_details->column_value }}">
                                    {{ $product_details->column_value }}</option>
                            @endforeach
                            <!-- Add options for articles here -->
                        </select>
                        <label for="unit-filter" class="text-gray-600 dark:text-gray-400">Filter Unit:</label>
                        <select id="unit-filter" class="border rounded px-2 py-1">
                            <option >All</option>

                            @foreach ($products as $key => $product)
                                @php
                                    $product = (object) $product;
                                @endphp
                                <option value="{{ $product->unit }}">
                                    {{ $product->unit }}</option>
                            @endforeach
                            <!-- Add options for units here -->
                        </select>
                    </div>
                    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 h-screen">
                        <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            <th scope="col" class="va-b px-2 py-2 text-xs">S. No.</th>
                            @foreach ($ColumnDisplayNames as $columnName)
                                <th scope="col" class="va-b px-2 py-2 text-xs">{{ $columnName }}</th>
                            @endforeach
                            <th scope="col" class="va-b px-2 py-2 text-xs ">Action</th>

                        </thead>
                        <tbody class="stock-table">

                            @foreach ($products as $key => $product)
                                @php
                                    $product = (object) $product;
                                @endphp
                                <tr
                                    class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif">

                                    <th scope="row"
                                        class="flex items-center whitespace-nowrap px-2 py-2 text-xs text-gray-900 dark:text-white">
                                        <div class="pl-0">
                                            <div class="text-base font-xs">{{ ++$key }}</div>

                                        </div>
                                    </th>
                                    @foreach ($product->details as $column)
                                        @php
                                            $column = (object) $column;
                                        @endphp
                                        <td class="px-2 py-2 text-xs {{ $column->column_name }}">
                                            {{ $column->column_value }}</td>
                                    @endforeach
                                    <td class="px-2 py-2 text-xs">{{ $product->item_code }}</td>
                                    <td class="px-2 py-2 text-xs Unit">{{ $product->unit }}</td>
                                    <td class="px-2 py-2 text-xs">{{ $product->qty }}</td>
                                    <td class="px-2 py-2 text-xs">{{ $product->rate }}</td>
                                    <td class="px-2 py-2 text-xs">
                                        <div class="flex items-center">
                                            {{-- <input wire:click='selectFromStock({{ json_encode($product) }})'
                                                type="checkbox"
                                                class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"> --}}
                                            <input wire:model="selectedProductP_ids.{{ $product->id }}"
                                                wire:click="selectFromStock({{ json_encode($product) }},{{ $product->id }})"
                                                type="checkbox"
                                                class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">

                                            <label for="default-checkbox"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"></label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="addProductModal" wire:click.defer='addFromStock' type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add</button>
                    <button data-modal-hide="addProductModal" type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>
        </div>

    </div>
    {{-- Add from stock --}}

    <!-- Modal for adding custom unit -->
    <div id="addUnitModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>

        <div class="modal-container bg-white w-96 mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <!-- Modal content -->
            <div class="modal-content py-4 text-left px-6">
                <h1 class="text-lg font-semibold mb-4">Add Custom Unit</h1>
                <input type="text" id="customUnitInput" class="w-full border rounded p-2 mb-4"
                    placeholder="Enter custom unit">
                <button id="saveCustomUnitButton" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Save
                </button>
            </div>
        </div>
    </div>
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
