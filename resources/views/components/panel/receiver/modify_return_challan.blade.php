<div class="rounded-lg dark:border-gray-700 mt-4">
    @php
    $mainUser = json_decode($this->mainUser);
    $panelName = strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']));
    $hideSuccessMessage = true;
    @endphp
   
        <!-- First Row - Responsive 2 Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4" wire:ignore>
            <!-- Column 1 -->
            <div class="items-center justify-center h-auto rounded bg-gray-50 dark:bg-gray-800" wire:ignore>
                <div class="w-full p-2 border border-gray-200 rounded-lg shadow sm:p-4 bg-[#ebebeb]">
                    <h5 class="mb-3 text-base font-semibold text-center text-gray-900 md:text-xl dark:text-white">
                        SENDER
                    </h5>

                    <div class="w-full text-gray-900 dark:text-white">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12">
                                <div class="grid gap-2">
                                    <div class="flex flex-row">
                                        <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Date</dt>
                                        <dd class="pl-4 text-xs  text-black capitalize">
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
                                        <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Challan No</dt>
                                        <dd class="pl-4 text-xs  capitalize 
                                    text-black ">
                                            {{ $createChallanRequest['challan_series'] ?? null }}-{{
                                            $createChallanRequest['series_num'] }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Name</dt>
                                        <dd class="pl-4 text-xs  text-black capitalize">
                                            {{  $challanModifyData['sender_user']['name'] ?? null }}</dd>
                                    </div>

                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Address</dt>
                                        <dd class="pl-4 text-xs  text-black capitalize">
                                            {{ $challanModifyData['sender_user']['address'] ?? null }}</dd>
                                    </div>


                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Email</dt>
                                        <dd class="pl-4 text-xs  text-black capitalize">
                                            {{ $challanModifyData['sender_user']['email'] ?? null }}</dd>
                                    </div>
                                    <div class="flex flex-row ">
                                        <dt class="mb-1 text-black text-xs font-semibold  dark:text-gray-400 w-1/4">Phone</dt>
                                        <dd class="pl-4 text-xs  text-black capitalize">
                                            {{ $challanModifyData['sender_user']['phone'] ?? null }}</dd>
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

                    <div x-data="{ search: '', selectedUserDetails: null }">
                        <!-- Button to toggle dropdown -->
                        <button id="dropdownDetailSearchButton" data-dropdown-toggle="dropdownDetailSearch"
                            data-dropdown-placement="bottom" data-dropdown-trigger="click"
                            class=" flex w-full bg-white  text-black border border-gray-400 hover:bg-orange focus:ring-2 focus:outline-none focus:ring-[#372b2b]/50 font-medium rounded-lg text-xs px-5 py-1.5 text-center items-center justify-center dark:focus:ring-gray-900 dark:hover:bg-[#050708]/30 mr-2 mb-2"
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
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
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
                                    wire:click="selectUserAddress('{{ json_encode($detail) }}')">
                                    <div
                                        class="flex items-center pl-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <label
                                            class="w-full py-2 ml-2 text-xs font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{
                                            $detail->address ?? null }}</label>
                                    </div>
                                </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                    <div class="grid gap-2">
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Phone</dt>
                            <dd class="pl-2 text-xs text-black capitalize word-break: break-word; overflow-wrap: break-word;">
                                {{ $createChallanRequest['receiver_user']['phone'] ?? null }}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Email</dt>
                            <dd class="pl-2 text-xs text-black capitalize word-break: break-word; overflow-wrap: break-word;">
                                {{ $createChallanRequest['receiver_user']['email'] ?? null}}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">GST</dt>
                            <dd class="pl-2 text-xs  text-black capitalize">
                                {{ $createChallanRequest['receiver_user']['gst_number'] ?? null}}</dd>
                        </div>
                        <div class="flex flex-row">
                            <dt class="mb-1 text-black text-xs font-semibold dark:text-gray-400 w-1/4">Address</dt>
                            <dd class="pl-2 text-xs  text-black capitalize">
                                {{ $createChallanRequest['receiver_user']['address'] ?? null}}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($challanSave)
        <div id="alert-border-3" class="p-2 mb-4 text-xs text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
            role="alert">
            <span class="font-medium">Success:</span> {{ $challanSave }}
        </div>
        
        @endif
        {{-- <div class="rounded-lg bg-[#ebebeb] p-6 shadow-md "> --}}
            {{-- <div class="border-b border-gray-300 pb-4">
                <button wire:click.prevent="addRow" @if ($inputsDisabled==true) disabled="" @endif
                    class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700"
                    style="background-color: #e5f811;">Add New Row</button>

            </div> --}}
        {{-- </div> --}}
        <div class="bg-[#ebebeb] py-2"  >

            {{-- <button wire:click.prevent="addRow"  @if ($inputsDisabled==true) disabled="" @endif
            class="rounded-full bg-yellow-500 px-4 text-black hover:bg-yellow-700"
            style="background-color: #e5f811;">Add New Row</button> --}}
            <div class="flex flex-col">
                <div class="overflow-x-auto ">
                    <div class="min-w-full py-2 align-middle inline-block">
                        <div class="overflow-hidden rounded-lg bg-[#ebebeb] p-3  max-w-full" >

                            <table class="min-w-full w-full">
                                <thead>
                                    <tr class="border-b border-gray-300 text-left">
                                        <th class="px-2 text-xs font-semibold">S.No.</th>
                                        @foreach ($panelColumnDisplayNames as $columnName)
                                        @if (!empty($columnName))
                                        <th class="px-2 text-xs font-semibold">{{ $columnName }}</th>
                                        {{-- @dd($columnName); --}}
                                        @endif
                                        @endforeach
                                        <th class="px-2 text-xs font-semibold">Unit</th>
                                        {{-- <th class="px-2 text-xs font-semibold">Details</th> --}}

                                        <th class="px-2 text-xs font-semibold">Rate</th>
                                        <th class="px-2 text-xs font-semibold">Qty</th>
                                        <th class="px-2 text-xs font-semibold">Total Amount</th>
                                        <th class="px-2 text-xs font-semibold"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($createChallanRequest['order_details'] as $index => $row)
                                    <tr wire:key="row-{{ $row['id'] }}">
                                        {{-- @dump($index) --}}
                                        <td class="px-1 py-2"><input @if ($inputsDisabled==true) disabled="" @endif
                                                value="{{ $index + 1 }}"
                                                class="hsn-box h-7 w-10 rounded-lg  bg-white  text-black border border-gray-400  text-xs focus:outline-none"
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
                                                class="hsn-box h-7 w-24 dynamic-width-input rounded-lg  bg-white  text-black border border-gray-400  text-xs focus:outline-none"
                                                type="text" />
                                        </td>
                                        @endif
                                        @endforeach
                                        @endif
                                        <td class="px-1 py-2">
                                            <input @if ($inputsDisabled==true) disabled="" @endif type="text"
                                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.unit"
                                                class="hsn-box h-7 w-24 rounded-lg  bg-white  text-black border border-gray-400  text-xs focus:outline-none"
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


                                        <td class="px-2 py-2">
                                            <input @if ($inputsDisabled==true) disabled="" @endif
                                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.rate"
                                                wire:keyup="updateTotalAmount({{ $index }})"
                                                class="hsn-box h-7 w-24 rounded-lg  bg-white  text-black border border-gray-400  text-xs focus:outline-none"
                                                min="0" type="number" />
                                        </td>
                                        {{-- @dump($createChallanRequest['order_details'][$index], isset($this->errorQty[$index]) ) --}}
                                        <td class="px-2 py-2">
                                            <input @if ($inputsDisabled==true) disabled="" @endif
                                                wire:model="createChallanRequest.order_details.{{ $index }}.qty"
                                                wire:keyup="updateTotalAmount({{ $index }})"
                                                class="hsn-box h-7 w-24 rounded-lg  bg-white  text-black border border-gray-400  text-xs @if(isset($this->errorQty[$index]) && $this->errorQty[$index]) bg-red-500 @else bg-gray-300 @endif  focus:outline-none"
                                                min="1" type="number" />
                                        </td>
                                        <td class="px-2 py-2">
                                            <input @if ($inputsDisabled==true) disabled="" @endif
                                                wire:model.defer="createChallanRequest.order_details.{{ $index }}.total_amount"
                                                class="hsn-box h-7 w-24 rounded-lg  bg-white  text-black border border-gray-400  text-xs focus:outline-none"
                                                type="number" disabled />
                                        </td>
                                        <td class="px-2 py-2">
                                            <button type="button" wire:click.prevent="removeRow({{ $index }})"
                                                class="bg-yellow-500 px-4 py-1 text-black hover:bg-yellow-700"
                                                style="background-color: #e5f811;">X</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>


                       
                            <div class=" mb-4 grid grid-cols-12 border-t-2 border-gray-300">
                                <div class="py-4 col text-xs text-black font-semibold">Comment</div>
                                <div class="lg:col-span-10 py-2 col-span-10 w-full">
                                        <input @if ($inputsDisabled==true) disabled="" @endif
                                            wire:model='createChallanRequest.comment'
                                            class="hsn-box h-8 w-full rounded-lg  bg-white  text-black border border-gray-400  text-xs focus:outline-none"
                                            type="text" />
                                    </div>
                                </div>
                                <div class="mb-4 grid grid-cols-12 gap-2 border-b-2 border-gray-300">
                                    <div class="lg:col-span-1 py-4 text-xs text-black font-semibold ">Total</div>
                                    <div class="lg:col-span-8 col-span-8 py-2">
                                        <input @if ($inputsDisabled==true) disabled="" @endif
                                            class="hsn-box h-8 w-11/12 rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" wire:model="createChallanRequest.total_words" disabled
                                              />
                                    </div>
                                    <div class="col-span-2 flex items-center justify-between ">
                                        <div class="lg:col-span-1 col-span-2 w-24 py-2">
                                            <input @if ($inputsDisabled==true) disabled="" @endif
                                                class="hsn-box h-8 w-full rounded-lg text-center font-mono text-xs font-normal text-black focus:outline-none"
                                                type="text" wire:model="createChallanRequest.total_qty" disabled
                                                  />
                                        </div>
                                        <div class="lg:col-span-1 col-span-2 w-24 py-2">
                                            <input @if ($inputsDisabled==true) disabled="" @endif
                                                class="hsn-box h-8 w-full rounded-lg  text-center font-mono text-xs font-normal text-black focus:outline-none"
                                                type="text" wire:model="createChallanRequest.total" disabled
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                </div>
                            
                              
                        <div class="flex justify-center space-x-4 lg:text-lg text-xs">
                            {{-- Save Button --}}
                            {{-- @if ($mainUser->team_user != null)
                            @if ($mainUser->team_user->permissions->permission->sender->create_challan == 1)
                            <button type="button" @if ($action=='save' ) wire:click.prevent='saveChallanModify'
                                @elseif($action=='edit' ) wire:click.prevent='saveChallanModify' @elseif($action == 'edit') @endif @if ($inputsDisabled==true) disabled="" @endif @if ($inputsResponseDisabled==false) disabled
                                @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black  @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2  "
                                wire:loading.attr="disabled" wire:target="saveChallanModify, saveChallanModify"
                                wire:click="disableSaveButton">
                                Save
                            </button>
                            @endif
                            @else
                            <button type="button" @if ($action=='save' ) wire:click.prevent='saveChallanModify'
                                @elseif($action=='edit' ) wire:click.prevent='saveChallanModify' @endif @if  ($inputsDisabled==true) disabled="" @endif @if ($inputsResponseDisabled==false) disabled
                                @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == false) bg-gray-300 text-black  @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2  ">Save</button>
                            @endif --}}

                            @if ($mainUser->team_user != null)
                            @if ($mainUser->team_user->permissions->permission->receiver->modify_return_challan == 1)
                                <button type="button"
                                    @if ($action == 'save') wire:click.prevent='saveChallanModify' @elseif($action == 'edit') wire:click.prevent='saveChallanModify' @endif
                                    @if ($inputsDisabled == true || (isset($errorQty[$index]) && $errorQty[$index])) disabled="" @endif
                                    @if ($inputsResponseDisabled == false)  @endif
                                    class="rounded-full btn-size @if ($inputsResponseDisabled == false || ($inputsDisabled == true || (isset($errorQty[$index]) && $errorQty[$index]))) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2"
                                    wire:loading.attr="disabled" wire:target="saveChallanModify, saveChallanModify"
                                    wire:click="disableSaveButton">
                                    Save
                                </button>
                            @endif
                        @else
                            <button type="button"
                                @if ($action == 'save') wire:click.prevent='saveChallanModify' @elseif($action == 'edit') wire:click.prevent='saveChallanModify' @endif
                                @if ($inputsDisabled == true || (isset($errorQty[$index]) && $errorQty[$index])) disabled="" @endif
                                @if ($inputsResponseDisabled == false)   @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == false || ($inputsDisabled == true || (isset($errorQty[$index]) && $errorQty[$index]))) bg-gray-300 text-black @endif @if ($inputsDisabled == true) bg-gray-300 text-black @endif @if ($inputsResponseDisabled == true) bg-gray-900 text-white @endif lg:px-8 px-4 px-4 py-2">Save</button>
                        @endif

                            {{-- Edit Button --}}
                            {{-- @if ($mainUser->team_user != null)
                            @if ($mainUser->team_user->permissions->permission->sender->modify_challan == 1)
                            <button wire:click.prevent='challanEdit' type="button" @if ($inputsResponseDisabled==true)
                                disabled="" @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gr px-4ay-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
                            @endif
                            @else
                            <button wire:click.prevent='challanEdit' type="button" @if ($inputsResponseDisabled==true)
                                disabled="" @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 px-4 py-2 text-black ">Edit</button>
                            @endif --}}

                            {{-- Send Button --}}
                            @if ($mainUser->team_user != null)
                            @if ($mainUser->team_user->permissions->permission->receiver->send_challan == 1)
                            <button wire:click.prevent='sendChallan({{ $challanId }})' @if ($inputsResponseDisabled==true) disabled="" @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
                            @endif
                            @else
                            <button wire:click.prevent='sendChallan({{ $challanId }})' @if ($inputsResponseDisabled==true) disabled="" @endif
                                class="rounded-full btn-size @if ($inputsResponseDisabled == true) bg-gray-300 text-black @else bg-gray-900 text-white @endif bg-gray-300 lg:px-8 px-4 py-2 text-black ">Send</button>
                            @endif

                            {{-- SFP Button --}}
                            <button @if ($inputsResponseDisabled==true) disabled="" @endif
                                class="rounded-full btn-size bg-gray-300 lg:px-8 px-4 py-2 text-black ">SFP</button>
                            </div>
                        </div>
                    </div>
                <div>
            </div>
        </div>

    <!-- Modal for adding custom unit -->

    {{-- MODAL --}}

    {{-- SCRIPT --}}
  
    {{-- SCRIPT --}}


</div>