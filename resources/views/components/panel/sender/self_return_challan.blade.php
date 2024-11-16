<div class="rounded-lg dark:border-gray-700 mt-4">
    @php
        $mainUser = json_decode($this->mainUser);
        $panelName = strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']));
    @endphp
    <form>
        <!-- First Row - Responsive 2 Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <!-- Column 1 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800">
                <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        SENDER
                    </h5>

                    <div class="w-full text-gray-900 dark:text-white">
                        <div class="col-span-12 md:col-span-6">
                            <div class="col-span-6">
                                <div class="grid gap-2">
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs font-semibold  w-1/4">Date</dt>
                                        <dd class="pl-4 text-xs text-black whitespace-nowrap">
                                            {{ date('j F , Y') }}</dd>

                                    </div>
                                    @php
                                    $challanModifyData = json_decode($challanModifyData, true);
                                    // Set the second parameter to true to get an associative array
                                    $createChallanRequest = $challanModifyData ?? [];

                                    // dd($createChallanRequest);
                                    // $createChallanRequest['order_details'];
                                    @endphp
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs font-semibold w-1/4 whitespace-nowrap">Challan No</dt>
                                        <dd
                                            class="pl-4 text-xs   text-black
                                      ">
                                            {{ $createChallanRequest['challan_series'] ?? null }}-{{ $createChallanRequest['series_num'] }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold w-1/4">Name</dt>
                                        <dd class="pl-4 text-xs text-black">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->name ?? null }}</dd>
                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold w-1/4">Address</dt>
                                        <dd class="pl-4 text-xs text-black">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->address ?? null }}</dd>
                                    </div>


                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold w-1/4">Email</dt>
                                        <dd class="pl-4 text-xs text-black">
                                            {{ Auth::guard(Auth::getDefaultDriver())->user()->email ?? null }}</dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold w-1/4">Phone</dt>
                                        <dd class="pl-4 text-xs text-black">
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
                        Ship To
                    </h5> --}}

                    <div x-data="{ search: '', selectedUserDetails: null }">
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownDetailSearchButton" data-dropdown-toggle="dropdownDetailSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class=" flex w-full b bg-white border border-gray-400 text-xs hover:bg-orange focus:ring-2 focus:outline-none text-black focus:ring-[#372b2b]/50 font-medium rounded-lg  px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                            type="button">
                            {{ ucfirst($createChallanRequest ? $createChallanRequest['receiver'] : 'Click To Choose')}}
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
                                @foreach ($selectedUserDetails as $detail)
                                    <li x-show="search === '' || '{{ strtolower($detail->address ?? null) }}'.includes(search.toLowerCase())"
                                        wire:click="selectUserAddress('{{ json_encode($detail) }}')">
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
                        @if(!empty($createChallanRequest['receiver_user']['phone']))
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold w-1/4">Phone</dt>
                            <dd class="pl-2 text-xs text-black">
                                {{ $createChallanRequest['receiver_user']['phone'] }}</dd>
                        </div>
                        @endif

                        @if(!empty($createChallanRequest['receiver_user']['email']))
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold w-1/4">Email</dt>
                            <dd class="pl-2 text-xs text-black">
                                {{ $createChallanRequest['receiver_user']['email'] }}</dd>
                        </div>
                        @endif

                        @if(!empty($createChallanRequest['receiver_user']['gst_number']))
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold w-1/4">GST</dt>
                            <dd class="pl-2 text-xs text-black">
                                {{ $createChallanRequest['receiver_user']['gst_number'] }}</dd>
                        </div>
                        @endif

                        @if(!empty($createChallanRequest['receiver_user']['address']))
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold w-1/4">Address</dt>
                            <dd class="pl-2 text-xs text-black">
                                {{ $createChallanRequest['receiver_user']['address'] }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div wire:loading    class="fixed z-30 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <span   class="loading loading-spinner loading-md"></span>
        </div>
        <div class="rounded-lg bg-[#ebebeb] p-6 shadow-md ">
            {{-- <div class="border-b border-gray-300 pb-4">
                <button wire:click.prevent="addRow" @if ($inputsDisabled == true) disabled="" @endif
                    class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700"
                    style="background-color: #e5f811;">Add New Row</button>

            </div> --}}
            {{-- <div class="flex flex-col"> --}}
            <div class=" overflow-x-auto">
                <div class="min-w-full py-2 align-middle inline-block">
                    <div class="overflow-hidden rounded-lg bg-[#ebebeb] p-3  max-w-full" wire:ignore.self>
                <table class="min-w-full w-full">
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
                            {{-- <th class="px-2 font-normal">Details</th> --}}

                            <th class="px-2 font-normal">Rate</th>
                            <th class="px-2 font-normal">Qty</th>
                            <th class="px-2 font-normal">Total Amount</th>
                            <th class="px-2 font-normal"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($createChallanRequest['order_details'] as $index => $row)
                        <tr>
                            {{-- @dump($index) --}}
                            <td class="px-1 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif
                                    value="{{ $index + 1 }}"
                                    class="hsn-box h-7 w-10 rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                    type="text" /></td>
                                    @if($panelColumnDisplayNames)
                                    @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                                    @if (!empty($columnName))
                                        @php
                                            // count($panelColumnDisplayNames)
                                            $this->createChallanRequest['order_details'][$index]['columns'][$key]['column_name'] = $columnName;
                                        @endphp
                                        <td class="px-1 py-2">
                                            <input @if ($inputsDisabled == true) disabled="" @endif
                                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.columns.{{ $key }}.column_value"
                                                class="hsn-box h-7 w-24 rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                                type="text" />
                                        </td>
                                    @endif
                                @endforeach
                            @endif
                            <td class="px-1 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif type="text"
                                    wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit"
                                    class="hsn-box h-7 w-24 rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
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

                            {{-- <td class="px-1 py-2"><input @if ($inputsDisabled == true) disabled="" @endif
                                    value=""
                                    class="hsn-box h-7 w-24 rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                    type="text" /></td> --}}
                            <td class="px-2 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif
                                    wire:model.defer="createChallanRequest.order_details.{{ $index }}.rate"
                                    wire:keyup="updateTotalAmount({{ $index }})"
                                    class="hsn-box h-7 w-24 rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                    min="0" type="number" />
                            </td>
                            <td class="px-2 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif
                                    wire:model="createChallanRequest.order_details.{{ $index }}.qty"
                                    wire:keyup="updateTotalAmount({{ $index }})"
                                    class="hsn-box h-7 w-24 rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                    min="1" type="number"
                                    {{-- oninput="checkQuantity(this, {{ $createChallanRequest['order_details'][$index]['remaining_qty'] }})"  --}}
                                    />
                                {{-- <span id="error-{{ $index }}" class="text-red-500 text-[0.6rem]" style="display: none;">Less Stock Available</span> --}}
                            </td>

                            {{-- <script>
                                function checkQuantity(input, remainingQty) {
                                    var errorSpan = document.getElementById('error-' + input.getAttribute('wire:model').split('.')[2]);
                                    if (parseInt(input.value) > remainingQty) {
                                        errorSpan.style.display = 'inline';
                                    } else {
                                        errorSpan.style.display = 'none';
                                    }
                                }
                            </script> --}}
                            <td class="px-2 py-2">
                                <input @if ($inputsDisabled == true) disabled="" @endif
                                    wire:model.defer="createChallanRequest.order_details.{{ $index }}.total_amount"
                                    class="hsn-box h-7 w-24 rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                    type="number" disabled />
                            </td>
                            <td class="px-2 py-2">
                                <button type="button" wire:click="removeItem({{ $index }}, {{ $newRecord }})"
                                    class="remove-row bg-yellow-500 px-4 py-1 text-black hover:bg-yellow-700"
                                    style="background-color: #e5f811;">X</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <script>
                    // document.addEventListener('DOMContentLoaded', function() {
                    //                 // Select all 'X' buttons
                    //                 var removeButtons = document.querySelectorAll('.remove-row');

                    //                 // Add a click event listener to each button
                    //                 removeButtons.forEach(function(button) {
                    //                     button.addEventListener('click', function(event) {
                    //                         // Prevent the default button action
                    //                         event.preventDefault();

                    //                         // Remove the corresponding table row
                    //                         event.target.closest('tr').remove();
                    //                     });
                    //                 });
                    //             });
                </script>

                <div class=" mb-4 grid grid-cols-12 border-t-2 border-gray-300">
                    <div class="py-4 col text-xs text-black">Comment</div>
                    <div class="lg:col-span-10 py-2 col-span-10 w-full">
                        <input @if ($inputsDisabled == true) disabled="" @endif
                            wire:model.defer='createChallanRequest.comment'
                            class="hsn-box h-8 w-full rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                            type="text" />
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                    <div class="lg:col-span-1 py-4 text-xs text-black ">Total</div>
                    <div class="lg:col-span-8 col-span-8 py-2">
                        <input @if ($inputsDisabled == true) disabled="" @endif
                            class="hsn-box h-8 w-11/12 rounded-lg   bg-white border border-gray-400 text-xs text-black focus:outline-none"
                            type="text"  wire:model="createChallanRequest.total_words" disabled
                             />
                    </div>
                    <div class="col-span-2 flex items-center justify-between ">
                        <div class="lg:col-span-1 col-span-2 w-24 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                class="hsn-box h-8 w-full rounded-lg  bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                type="text"   wire:model="createChallanRequest.total_qty" disabled
                                 />
                        </div>
                        <div class="lg:col-span-1 col-span-2 w-24 py-2">
                            <input @if ($inputsDisabled == true) disabled="" @endif
                                class="hsn-box h-8 w-full rounded-lg   bg-white border border-gray-400 text-xs text-black focus:outline-none"
                                type="text" wire:model="createChallanRequest.total" disabled
                                  />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        {{-- @dd($challanModifyData['id']); --}}
           {{-- Save Button --}}
           <div class="flex justify-center space-x-4 lg:text-lg text-sm">
            {{-- @if ($mainUser->team_user != null)
                    @if ($mainUser->team_user->permissions->permission->sender->create_challan == 1)
                        <button type="button"
                            @if ($action == 'save') wire:click.prevent="selfReturnChallan('{{ $challanModifyData['id'] }}')" @elseif($action == 'edit') wire:click.prevent="selfReturnChallan('{{ $challanModifyData['id'] }}')" @endif
                            @if ($inputsDisabled == true) disabled="" @endif @if ($inputsResponseDisabled == false) disabled   @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black  @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2  "
                            wire:loading.attr="disabled" wire:target="saveChallanModify, saveChallanModify" wire:click="disableSaveButton">
                            Save
                        </button>
                    @endif
                @else
                <button type="button"
                @if ($action == 'save') wire:click.prevent="save" @elseif($action == 'edit')  @endif
                @if ($inputsDisabled || $saveButtonDisabled) disabled @endif

                class="rounded-full btn-size @if ($inputsDisabled || $saveButtonDisabled) bg-gray-300 text-black @else bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2 ">
                Save
            </button> --}}
            @if ($mainUser->team_user != null)
                    @if ($mainUser->team_user->permissions->permission->sender->create_challan == 1)
                        <button type="button"
                            @if ($action == 'save') wire:click.prevent='saveSelfReturnChallan' @elseif($action == 'edit') wire:click.prevent='save' @endif
                            @if ($inputsDisabled == true) disabled="" @endif
                            @if ($inputsResponseDisabled == false) disabled @endif
                            class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2   "
                            wire:loading.attr="disabled" wire:target="save"
                            wire:click="disableSaveButton">
                            Return
                        </button>
                    @endif
                @else
                    <button type="button"
                        @if ($action == 'save') wire:click.prevent='saveSelfReturnChallan' @elseif($action == 'edit') wire:click.prevent='saveSelfReturnChallan' @endif
                        @if ($inputsDisabled == true) disabled="" @endif
                        @if ($inputsResponseDisabled == false) disabled @endif
                        class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2  ">Save</button>
                @endif



                {{-- @endif --}}

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

        {{-- Send Button --}}
        <button wire:click.prevent="selfReturnChallan('{{ $challanModifyData['id'] }}')"
        @if ($inputsResponseDisabled == true) disabled="" @endif
        wire:loading.attr="disabled"
        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif
        lg:px-8 px-4 py-2 text-black ">
        Confirm
        </button>
        {{-- SFP Button --}}
        @if($teamMembers != null)
        <button  x-on:click.prevent="$wire.updateVariable('challan_sfp', {{ $challanModifyData['id'] }})"

        @if ($inputsResponseDisabled == true) disabled="" @endif
        class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>

        @endif

        </div>
    </form>


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
