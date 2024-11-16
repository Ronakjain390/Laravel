<div class="card-body overflow-auto ml-3 h-full">
    <div class="row">
        <div class="">
            @if ($errorMessage)
            @foreach (json_decode($errorMessage) as $error)
            <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <span class="font-medium">Error:</span> {{ $error[0] }}
            </div>
            @endforeach
            @if (session('error'))
                @foreach (session('error') as $error)
                    <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span class="font-medium">Error:</span> {{ $error[0] }}
                    </div>
                @endforeach
            @endif

            @endif
            @if ($successMessage)
            <div class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <span class="font-medium">Success:</span> {{ $successMessage }}
            </div>
            @endif

        </div>
        @php
        $allPlansData = json_decode($allPlansData);
        // dd($allPlansData);
        @endphp
        {{-- @dump($allPlansData) --}}
        <div class="col-12">
            <div class="card">
                <div class="flex items-center justify-between bg-white dark:bg-gray-900">

                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative m-2">
                        <div class="flex pointer-events-none absolute inset-y-0 left-0 items-center pl-3">
                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" id="table-search-users"
                            class="block w-80 rounded-lg border border-gray-300 bg-gray-50 p-2 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                            placeholder="Search" />
                    </div>
                </div>
                <div class="flex border-t mt-2">
                    <button type="button"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-1.5 text-center me-2 mb-2">Monthly
                        Packages</button>
                    <button type="button"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-1.5 text-center me-2 mb-2">Yearly
                        Packages</button>
                    <button type="button" data-modal-target="medium-modal" data-modal-toggle="medium-modal"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-1.5 text-center me-2 mb-2">New
                        Packages M</button>

                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0 whitespace-nowrap" style="height: 300px;">
                    <table  class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="va-b px-2 py-2 text-sm">S.NO</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">SORT NO</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">PACKAGE NAME</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">PACKAGE COST</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">CATEGORY</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">HEADING 1</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">HEADING 1 VALUE</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">HEADING 2</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">HEADING 2 VALUE</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">INVOICE/CHALAN SERIES NO.</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">NO OF INVOICE/CHALAN SERIES NO.</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">USERS</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">TOP-UPS STATUS</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">VALIDITY</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">INLINE COMMENT</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">COMMENT</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">ACTION</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allPlansData as $key => $data)
                            {{-- @dd($data); --}}
                            <tr
                                class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

                                <td class="px-2 py-2 text-sm"> {{ ++$key }} </td>
                                <td class="px-2 py-2 text-sm"> </td>
                                <td class="px-2 py-2 text-sm">{{ $data->plan_name }}</td>
                                <td class="px-2 py-2 text-sm">{{ $data->price }}</td>
                                <td class="px-2 py-2 text-sm">
                                    @if ($data->panel_id == 1)
                                    Sender
                                    @elseif ($data->panel_id == 2)
                                    Receiver
                                    @elseif($data->panel_id == 3)
                                    Seller
                                    @elseif($data->panel_id == 4)
                                    Buyer
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    @if (count($data->features) > 0)
                                    {{ $data->features[0]->feature_name }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    @if (count($data->features) > 0)
                                    {{ $data->features[0]->feature_usage_limit }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    @if (count($data->features) > 1)
                                    {{ $data->features[1]->feature_name }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    @if (count($data->features) > 1)
                                    {{ $data->features[1]->feature_usage_limit }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    @if (count($data->features) > 2)
                                    {{ $data->features[2]->feature_name }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    @if (count($data->features) > 2)
                                    {{ $data->features[2]->feature_usage_limit }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-sm">{{ $data->user ??''}} </td>
                                <td class="px-2 py-2 text-sm">{{ $data->topup ??''}} </td>
                                <td class="px-2 py-2 text-sm">{{ $data->validity_days ??''}} </td>
                                <td class="px-2 py-2 text-sm">N/A </td>
                                <td class="px-2 py-2 text-sm">{{ $data->comment ?? ''}} </td>
                                <td class="px-2 py-2 text-sm">

                                    <button id="dropdownDefaultButton-{{ $key }}"
                                        data-dropdown-toggle="dropdown-{{ $key }}"
                                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 py-1.5 mr-5 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                        type="button">Select <svg class="w-2.5  ml-2.5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg></button>
                                    <div id="dropdown-{{ $key }}"
                                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                            aria-labelledby="dropdownDefaultButton-{{ $key }}">
                                            {{-- @if(isset($data->user->added_by)) --}}
                                            {{-- @if($data->user->added_by == $data->user_id) --}}
                                            {{-- <li> @dump($data) --}}
                                                <a href="#" wire:click="editPackage({{ json_encode($data) }} )"
                                                    data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                    class="block px-4 py-2 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                            </li>
                                            {{-- @endif --}}
                                            {{-- @endif --}}
                                            <li>
                                                <a href="javascript:void(0);"
                                                    wire:click="$emit('triggerDelete', {{ $data->id }})"
                                                    class="block px-4 py-2 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                            </li>

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @php
    $allPanelData = json_decode($allPanelData);
    @endphp
    {{-- @dump($allPanelData); --}}
    <!-- Default Modal -->
    <div id="medium-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full overflow-auto"
        wire:ignore.self>
        <div class="relative w-full max-w-xl max-h-full">
            <!-- Modal content -->

            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg text-gray-900 dark:text-white">
                        New Package[Monthly]
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="medium-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">New Package[Monthly] </span>
                        </button>
                    </h3>
                </div>


               <!-- Modal body -->
                <div class="p-6 space-y-6" wire:ignore.self>
                    <div>
                        <div id="address-fields">
                            <!-- Example Address Card with Delete Button -->
                            <div wire:key="detail-{{ $key }}" class="mt-4 border rounded-lg p-4 border-gray-300 relative">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="select" class="block text-sm font-medium">Select Panel</label>
                                        <select id="select1" wire:model="selectedPanel"
                                            wire:change="selectPanel($event.target.value)"
                                            class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                                            <option value="">Select Panel</option>
                                            @foreach ($allPanelData as $panel)
                                                <option value="{{ json_encode($panel) }}">{{ $panel->panel_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="heading2" class="block text-sm font-medium mt-1 p-1">Package Name</label>
                                        <input wire:model.defer='planData.plan_name' type="text" name="plan_name"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Package Name">
                                    </div>

                                    @if ($heading1Feature)
                                        <div>
                                            <p class="block text-sm font-medium mt-1 p-1">{{ $heading1Feature }}</p>
                                            <input wire:model.defer='planData.feature_usage_limit.0' type="number"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Heading 1 Value">
                                        </div>
                                    @endif

                                    @if ($heading2Feature)
                                        <div>
                                            <p class="block text-sm font-medium mt-1 p-1">{{ $heading2Feature }}</p>
                                            <input wire:model.defer='planData.feature_usage_limit.1' type="number"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Heading 2 Value">
                                        </div>
                                    @endif

                                    @if ($seriesNumberFeature)
                                        <div>
                                            <p class="block text-sm font-medium mt-1 p-1">{{ $seriesNumberFeature }}</p>
                                            <input wire:model.prevent='planData.feature_usage_limit.2' type="number"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Challan/Invoice Series Value">
                                        </div>
                                        <!-- Add More Features Button -->
                                        <div class="mt-10">
                                            <button wire:click="addFeature" id="toggleColumns" data-tooltip-target="tooltip-toggleColumns" class="px-2 py-0 rounded bg-orange text-black">+</button>
                                            <div id="tooltip-toggleColumns" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                Add feature
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Dynamically Added Features -->
                                    @foreach ($additionalFeatures as $index => $feature)
                                        <div class="flex items-center space-x-2 col-span-2">
                                            <div class="w-full">
                                                <input wire:model="additionalFeatures.{{ $index }}.name" type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                    placeholder="Feature Name">
                                            </div>
                                            <div class="w-full">
                                                <input wire:model="additionalFeatures.{{ $index }}.usage_limit" type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                    placeholder="Usage Limit">
                                            </div>
                                            <button wire:click="removeFeature({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                    <div>
                                        <label for="users" class="block text-sm font-medium">Users</label>
                                        <input type="number" wire:model.defer="planData.user"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Users">
                                    </div>

                                    <div>
                                        <label for="topups" class="block text-sm font-medium">Top-Ups</label>
                                        <input wire:model.defer='planData.topup' type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Top-Ups">
                                    </div>

                                    <div>
                                        <label for="validity_days" class="block text-sm font-medium">Validity Days</label>
                                        <select wire:model="planData.validity_days" id="select" name="validity_days"
                                            class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                                            <option value="">Select Days</option>
                                            <option value="30">30-Days</option>
                                            <option value="365">365-Days</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="price" class="block text-sm font-medium">Price</label>
                                        <input wire:model.defer='planData.price' type="number"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Package Cost">
                                    </div>
                                    <div>
                                        <label for="price" class="block text-sm font-medium">Price</label>
                                        <input wire:model.defer='planData.discounted_price' type="number"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Discounted Price">
                                    </div>

                                    <div>
                                        <label for="comment" class="block text-sm font-medium mt-6">Comment [150 Words Max]</label>
                                        <input wire:model.defer='planData.comment' type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Comment">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="medium-modal" wire:click.prevent="createPackage" type="button"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Create
                        Package</button>
                    <button data-modal-hide="medium-modal" type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Default Modal -->
    <div id="edit-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
        wire:ignore.self>
        <div class="relative w-full max-w-xl max-h-full">
            <!-- Modal content -->
            {{-- @dd($planData); --}}
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        Edit Package
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="edit-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">New Package[Monthly] </span>
                        </button>
                    </h3>
                </div>


                <!-- Modal body -->
                <div class="p-6 space-y-6" wire:ignore.self>
                    <div>
                        <div id="address-fields">
                            <!-- Example Address Card with Delete Button -->

                            <div wire:key="detail-{{ $key }}"
                                class="mt-4 border rounded-lg p-4 border-gray-300 relative">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    

                                    <div>
                                        <label for="heading2" class="block text-sm font-medium mt-1 p-1">Package
                                            Name</label>
                                        <input wire:model.prevent='planData.plan_name' type="text" name="plan_name"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Package Name">
                                    </div>
                                    {{-- <div>
                                        <label for="heading1" class="block text-sm font-medium">Heading 1</label>
                                        <input type="text" wire:model="heading1Feature" name="heading1"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div> --}}
                                    @if ($heading1Feature)
                                    <div>

                                        <p class="block text-sm font-medium mt-1 p-1">{{ $heading1Feature }}</p>

                                        <input wire:model.prevent='planData.feature_usage_limit.0' type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Heading 1 Value">
                                    </div>
                                    @endif
                                    <div>
                                        <label for="select2" class="block text-sm font-medium">Select Panel
                                        </label>
                                        <select id="select2" wire:model="selectedPanel2"
                                            class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                                            <option value="">Select Panel </option>
                                            @foreach ($allPanelData as $panel)
                                            <option value="{{ $panel->id }}">{{ $panel->panel_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="heading1" class="block text-sm font-medium">Heading 2</label>
                                        <input type="text" wire:model="heading2Feature"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    @if ($heading2Feature)
                                    <div class="w-full">

                                        <p class="block text-sm font-medium mt-1 p-1"> {{ $heading2Feature }}</p>

                                        <input wire:model.prevent='planData.feature_usage_limit.1' type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Heading 2 Value">
                                    </div>
                                    @endif

                                    <div>
                                        <label for="series_number" class="block text-sm font-medium">Invoice/Challan
                                            Series Number</label>
                                        <input type="text" wire:model="seriesNumberFeature"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                                    </div>


                                    @if ($seriesNumberFeature)
                                    <div>
                                        <p class="block text-sm font-medium mt-1 p-1"> {{ $seriesNumberFeature }}</p>
                                        <input wire:model.prevent='planData.feature_usage_limit.2' type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Challan/Invoice Series Value">
                                    </div>
                                    @endif
                                    <div class="mt-1">
                                        <label for="users" class="block text-sm font-medium">Users</label>
                                        <input type="text" wire:model.prevent="planData.user"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Users">

                                    </div>
                                  
                                    <div>
                                        <label for="users" class="block text-sm font-medium">Top-Ups</label>
                                        <input wire:model.prevent='planData.topup' type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Popups">
                                    </div>
                                     
                                    <div>
                                        <label for="tan" class="block text-sm font-medium">Price</label>
                                        <input wire:model.prevent='planData.price' type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Package Cost">
                                    </div>
                                    <div>
                                        <label for="validity_days" class="block text-sm font-medium">Validity Days</label>
                                        <select wire:model="planData.validity_days" id="select" name="validity_days"
                                            class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                                            <option value="">Select Days</option>
                                            <option value="30">30-Days</option>
                                            <option value="365">365-Days</option>
                                        </select>
                                    </div>
                                  
                                </div>
                                 
                                <div>
                                    <label for="tan" class="block text-sm font-medium mt-6">Comment [150 Words
                                        Max]</label>
                                    <input wire:model.prevent='planData.comment' type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Comment">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="edit-modal" wire:click.prevent="updatePackage" type="button"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Update</button>
                    <button data-modal-hide="edit-modal" type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('livewire:load', function(e) {
        @this.on('triggerDelete', id => {
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6fc5e0",
                cancelButtonColor: "#d33",
                confirmButtonText: "Delete",
            }).then((result) => {
                if (result.value) {
                    @this.call('deletePackage', id);
                    console.log('hello');
                } else {
                    console.log("Canceled");
                }
            });
        });
    });
</script>