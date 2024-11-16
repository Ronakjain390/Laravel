<div class="card-body overflow-auto ml-3 h-full">
    <div class="row">
        @if($message)
    <div class="alert alert-success">
        {{ $message }}
    </div>
@endif
        @php
            $allTopupData = json_decode($allTopupData);
            // dd($allTopupData);
        @endphp
        {{-- @dump($allTopupData) --}}
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
                    {{-- <button type="button"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-1.5 text-center me-2 mb-2"></button> --}}
                    <button type="button"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-1.5 text-center me-2 mb-2">New Title</button>
                    <button type="button" data-modal-target="medium-modal" data-modal-toggle="medium-modal"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-1.5 text-center me-2 mb-2">New Topup</button>

                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0 whitespace-nowrap" style="height: 300px;">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="va-b px-2 py-2 text-sm">S.NO</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">CATEGORY</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">HEADING 1</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">PRICE</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">VALUE</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">COMMENT</th>
                                <th scope="col" class="va-b px-2 py-2 text-sm">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allTopupData as $key => $data)
                                <tr
                                    class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

                                    <td class="px-2 py-2 text-sm"> {{ ++$key }} </td>
                                    <td class="px-2 py-2 text-sm">
                                        @if ($data->feature->panel_id == 1)
                                            Sender
                                        @elseif ($data->feature->panel_id == 2)
                                            Receiver
                                        @elseif($data->feature->panel_id == 3)
                                            Seller
                                        @elseif($data->feature->panel_id == 4)
                                            Buyer
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-sm">

                                        {{ $data->feature->feature_name }}

                                    </td>
                                    <td class="px-2 py-2 text-sm">{{ $data->price }}</td>
                                    <td class="px-2 py-2 text-sm">{{ $data->usage_limit }}</td>

                                    <td class="px-2 py-2 text-sm">{{ $data->comment ?? '' }} </td>
                                    <td class="px-2 py-2 text-sm"></td>

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
    <!-- Default Modal -->
    <div id="medium-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
        wire:ignore.self>
        <div class="relative w-full max-w-xl max-h-full">
            <!-- Modal content -->

            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        New Top-Up
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="medium-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">New Top-Up </span>
                        </button>
                    </h3>
                </div>


                <!-- Modal body -->
                <div class="p-6 space-y-6" wire:ignore.self>
                    <div>
                        <div id="address-fields">
                            <!-- Example Address Card with Delete Button -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="select" class="block text-sm font-medium">Select Panel
                                    </label>
                                    <select id="select1" wire:model="selectedPanel"
                                        wire:change="selectPanel($event.target.value)"
                                        class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                                        <option value="">Select Panel</option>
                                        @foreach ($allPanelData as $panel)
                                            <option value="{{ json_encode($panel) }}">{{ $panel->panel_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                {{-- <div>
                                    <label for="heading2" class="block text-sm font-medium mt-1 p-1">Package
                                        Name</label>
                                    <input
                                        wire:model.prevent='topupData.plan_name'
                                        type="text" name="plan_name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Package Name">
                                </div> --}}
                                {{-- @if ($heading1Feature)
                                <div>
                                   
                                    <p class="block text-sm font-medium mt-1 p-1">{{ $heading1Feature }}</p>
                                
                                    <input
                                        wire:model.prevent='topupData.feature_usage_limit.0'
                                        type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Heading 1 Value">
                                </div>
                                @endif --}}
                                
                                {{-- @if ($heading2Feature)
                                <div class="w-full">
                                    
                                    <p class="block text-sm font-medium mt-1 p-1"> {{ $heading2Feature }}</p>
                               
                                    <input
                                        wire:model.prevent='topupData.feature_usage_limit.1'
                                        type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Heading 2 Value">
                                </div>
                                @endif --}}

                                

                                {{-- @dump($seriesNumberFeature); --}}
                                @if ($seriesNumberFeature)
                                <div>
                                    <p class="block text-sm font-medium mt-1 p-1">Heading 1</p>
                                
                                    <select wire:model="topupData.feature_id" id="select" name="validity_days"
                                            class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                            
                                        <option value="">Select Feature</option>
                            
                                        <option value="{{ $selected1FeatureIds }}">
                                            {{ $heading1Feature }}
                                        </option>
                            
                                        <option value="{{ $selected2FeatureIds }}">
                                            {{ $heading2Feature }}
                                        </option>
                            
                                        <option value="{{ $selected3FeatureIds }}">
                                            {{ $seriesNumberFeature }}
                                        </option>
                            
                                    </select>
                                </div>
                            @endif
                            
                                {{-- <div>
                                    <label for="users" class="block text-sm font-medium">Users</label>
                                    <input 
                                        type="text" wire:model.prevent="topupData.user"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Users">
                                        
                                </div> --}}
                                
                                <div>
                                    <label for="usage_limit" class="block text-sm font-medium">Value</label>
                                    <input wire:model.prevent='topupData.usage_limit'
                                        type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Value">
                                </div>
                                
                                {{-- <div>
                                    <label for="validity_days" class="block text-sm font-medium">Validity Days</label>
                                    <select wire:model="topupData.validity_days" id="select" name="validity_days"
                                        class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                                        <option value="">Select Days</option>
                                        <option value="30">30-Days</option>
                                        <option value="365">365-Days</option>
                                    </select>
                                </div> --}}
                                
                               
                                <div>
                                    <label for="tan" class="block text-sm font-medium">Price</label>
                                    <input wire:model.prevent='topupData.price'
                                        type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Price">
                                </div>
                                <div>
                                    <label for="tan" class="block text-sm font-medium mt-6">Comment [150 Words
                                        Max]</label>
                                    <input wire:model.prevent='topupData.comment'
                                        type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Comment">
                                </div>
                                {{-- <div>
                                    <label for="tan" class="block text-sm font-medium"></label>
                                    <input wire:model.prevent=''
                                        type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Package Sort">
                                </div> --}}
                            </div>
                           
                        </div>
                    </div>
                    <div
                        class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="medium-modal" wire:click.prevent="createTopup" type="button"
                            class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Create
                            Package</button>
                        <button data-modal-hide="medium-modal" type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
