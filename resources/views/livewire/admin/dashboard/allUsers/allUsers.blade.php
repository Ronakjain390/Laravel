<div class="card-body overflow-auto  h-full">
    <div class="row">
        @if(session('success'))
    <div class="alert alert-success bg-green-500">
        {{ session('success') }}
    </div>
@endif
       
        {{-- @dd($tableTdData) --}}
        
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
                        <input type="text" id="table-search-users" wire:model.live="searchTerm"
                            class="block w-80 rounded-lg border border-gray-300 bg-gray-300 p-2 pl-10 text-xs text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                            placeholder="Search" />
                            
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0 whitespace-nowrap" style="height: 300px;">
                    <table
                        class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            <tr >
                                <th scope="col" class="va-b px-2  text-xs">S.NO</th>
                                <th scope="col" class="va-b px-2  text-xs">ID</th>
                                <th scope="col" class="va-b px-2  text-xs">NAME</th>
                                <th scope="col" class="va-b px-2  text-xs">USER ID</th>
                                <th scope="col" class="va-b px-2  text-xs">COMPANY NAME</th>
                                <th scope="col" class="va-b px-2  text-xs">TYPE</th>
                                <th scope="col" class="va-b px-2  text-xs">ADDED BY</th>
                                <th scope="col" class="va-b px-2  text-xs">EMAIL</th>
                                <th scope="col" class="va-b px-2  text-xs">Date</th>
                                <th scope="col" class="va-b px-2  text-xs">Time</th>
                                <th scope="col" class="va-b px-2  text-xs">ACTIVE PLANS</th>
                                <th scope="col" class="va-b px-2  text-xs">SELLER</th>
                                <th scope="col" class="va-b px-2  text-xs">BUYER</th>
                                <th scope="col" class="va-b px-2  text-xs">SENDER</th>
                                <th scope="col" class="va-b px-2  text-xs">RECEIVER</th>
                                {{-- <th>RETAIL</th> --}}
                                <th scope="col" class="va-b px-2  text-xs">PHONE</th>
                                <th scope="col" class="va-b px-2  text-xs">STATE</th>
                                <th scope="col" class="va-b px-2  text-xs">CITY</th>
                                <th scope="col" class="va-b px-2  text-xs">Challans</th>
                                <th scope="col" class="va-b px-2  text-xs">Return Challans</th>
                                <th scope="col" class="va-b px-2  text-xs">Invoice</th>
                                <th scope="col" class="va-b px-2  text-xs">Purchase Orders</th>
                                <th scope="col" class="va-b px-2  text-xs">Actions</th> 
                            </tr>
                        </thead>
                        {{-- @dd($tableTdData) --}}
                        <tbody>
                            @foreach ($this->tableTdData as $key => $data)
                            {{-- @dd($data) --}}
                            {{-- @dd($user) --}}
                            {{-- @dd($user->plans
       
                            {{-- @dump($data->sender) --}}
                                <tr class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                                    <td class="px-2  text-xs"><b>{{ $data->custom_item_number }}</b></td>
                                    <td class="px-2  text-xs"><b>{{ $data->id }}</b></td>
                                    <td class="px-2  text-xs">{{ $data->name }}</td>
                                    <td class="px-2  text-xs"> {{ $data->special_id }} </td>
                                    <td class="px-2  text-xs"> {{ $data->company_name }} </td>
                                    <td class="px-2  text-xs"> @if($data->added_by === null) Direct @else Indirect @endif </td>
                                    <td class="px-2  text-xs"> {{$data->added_by_name}} </td>
                                    <td class="px-2  text-xs"> {{ $data->email }} </td>
                                    <td class="px-2  text-xs"> {{ date('h:i A', strtotime($data->created_at)) }} </td>
                                    <td class="px-2  text-xs"> {{ date('j F Y', strtotime($data->created_at)) }} </td>
                                    <td class="px-2  text-xs"> </td>
                                    <td class="px-2  text-xs">
                                        {{-- @if(isset($data->plans))
                                        @if(isset($data->plans[0]) && $data->plans[0]->panel->status == 'active') --}}
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" name="sender" class="sr-only peer" wire:model="allUsers.sender" wire:click="updateSender({{ $data->id }})">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                        {{-- @else
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" wire:model="toggleDataset.sender" wire:click="updateSender({{ $data->id }})">
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                        @endif --}}
                                    </td>
                                    <td class="px-2  text-xs">
                                        <label class="relative inline-flex items-center cursor-pointer"> 
                                            <input type="checkbox" value="" name="receiver" class="sr-only peer"  wire:model="toggleDataset.receiver" wire:click="updateSender({{ $data->id }})" >
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                    </td>
                                    <td class="px-2  text-xs">
                                        <label class="relative inline-flex items-center cursor-pointer"> 
                                            <input type="checkbox" value="" name="seller" class="sr-only peer"  wire:model="toggleDataset.seller" wire:click="updateSender({{ $data->id }})" >
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                    </td>
                                    <td class="px-2  text-xs">
                                        <label class="relative inline-flex items-center cursor-pointer"> 
                                            <input type="checkbox" value="" name="buyer" class="sr-only peer"  wire:model="toggleDataset.buyer" wire:click="updateSender({{ $data->id }})" >
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                    </td>
                                    {{-- <td class="px-2  text-xs">
                                        

                                        @if(isset($data->plans[1]) && $data->plans[0]->panel->status == 'active')
                                         <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" name="receiver" class="sr-only peer"  wire:model="toggleDataset.receiver" wire:click="updateSender({{ $data->id }})">
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                        @else
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" wire:model="toggleDataset.receiver" wire:click="updateSender({{ $data->id }})">
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                        @endif
                                    </td>
                                    <td class="px-2  text-xs"> 
                                        @if(isset($data->plans[2]) && $data->plans[0]->panel->status == 'active')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                    @else
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                        <span
                                            class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                    </label>
                                    @endif
                                    </td>
                                    <td class="px-2  text-xs"> 
                                        @if(isset($data->plans[3]) && $data->plans[0]->panel->status == 'active')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                    @else
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="" class="sr-only peer" >
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                        <span
                                            class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                    </label> --}}
                                    {{-- @endif --}}
                                    </td>
                                    {{-- <td class="px-2  text-xs"> <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                            <span
                                                class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label> 
                                    </td> --}}
                                        {{-- @endif --}}
                                    <td class="px-2  text-xs"> {{ $data->phone }} </td>
                                    <td class="px-2  text-xs"> {{ $data->state }} </td>
                                    <td class="px-2  text-xs"> {{ $data->city }} </td>
                                    <td class="px-2  text-xs"> {{ $data->challan_count }}</td>
                                    <td class="px-2  text-xs"> {{ $data->returnchallans_count }}</td>
                                    <td class="px-2  text-xs"> {{ $data->invoice_count }}</td>
                                    <td class="px-2  text-xs"> {{ $data->purchaseorders_count }}</td>
                                    <td class="">
                                        <button id="dropdownDefaultButton-{{ $key }}"
                                            data-dropdown-toggle="dropdown-{{ $key }}"
                                            class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-xs px-2.5 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                            type="button">Select <svg class="w-2.5  ml-2.5" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m1 1 4 4 4-4" />
                                            </svg></button>
                                        <div id="dropdown-{{ $key }}"
                                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                            <ul class="py-1 text-xs text-gray-700 dark:text-gray-200"
                                                aria-labelledby="dropdownDefaultButton-{{ $key }}">
                                                <li>
                                                    <a href="#" wire:click.prevent="selectPlan({{ $data->id }})"
                                                        data-modal-target="medium-modal" data-modal-toggle="medium-modal"
                                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Topup</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);"
                                                        wire:click="$emit('triggerDelete', {{ $data->id }})"
                                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);"
                                                        wire:click="$emit('removeUser', {{ $data->id }})"
                                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Remove</a>
                                                </li>
                
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example" class=" text-center ">
                        <ul class="inline-flex -space-x-px text-xs">
                    {{-- @php
                        $paginateLinks = (object) $paginateLinks;
                    @endphp --}}
     

            @foreach ($paginateLinks as $link)
                <li>
                    <a href="{{ $link->url }}" class="block py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white {{ $link->active ? 'text-purple-600 border-purple-300 bg-purple-50 hover:bg-purple-100 hover:text-purple-700' : '' }}">
                        {{ $link->label }}
                    </a>
                </li>
            @endforeach

          
                        </ul>
                    </nav>
                </div>
                
            </div>
            
        </div>
    </div>
    <!-- Default Modal -->
    <div id="medium-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
        wire:ignore>
        <div class="relative w-full max-w-7xl max-h-full">
            <!-- Modal content -->

            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        Select Plan
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-xs w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
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
                        <div>
                            <div class="border-gray-200 dark:border-gray-700">
                                {{-- @if ($message)
                                <div class="alert alert-success">
                                    {{ $message }}
                                </div>
                            @endif
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif --}}
                        
                                {{-- <a title="Cart" class="text-dark" href="{{ route('checkout') }}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                  
                                </a> --}}
                                <ul class="flex flex-wrap text-center text-sm " id="myTab" data-tabs-toggle="#myTabContent"
                                role="tablist">
                                    <li class="group" role="presentation">
                                        <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-1' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-1')" id="sender-tab" data-tabs-target="#panel-1" type="button" role="tab" aria-controls="panel-1" aria-selected="{{ $activeTab === 'panel-1' ? 'true' : 'false' }}">Sender</button>
                                    </li>
                                    <!-- Repeat similar buttons for other tabs with proper wire:click and active checks -->
                                    <li class="group" role="presentation">
                                        <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-2' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-2')" id="receiver-tab" data-tabs-target="#panel-2" type="button" role="tab" aria-controls="panel-2" aria-selected="{{ $activeTab === 'panel-2' ? 'true' : 'false' }}">Receiver</button>
                                    </li>
                                    <li class="group" role="presentation">
                                        <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-3' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-3')" id="seller-tab" data-tabs-target="#panel-3" type="button" role="tab" aria-controls="panel-3" aria-selected="{{ $activeTab === 'panel-3' ? 'true' : 'false' }}">Seller</button>
                                    </li>
                                    <li class="group" role="presentation">
                                        <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-4' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-4')" id="buyer-tab" data-tabs-target="#panel-4" type="button" role="tab" aria-controls="panel-4" aria-selected="{{ $activeTab === 'panel-4' ? 'true' : 'false' }}">Buyer</button>
                                    </li> 
                                </ul>
                        
                            </div>
                            
                        
                            <div id="myTabContent" class="mt-10">
                                @foreach ([1, 2, 3, 4] as $panelId)
                                    @php
                                        $filteredPlans = array_filter($plans, function ($plan) use ($panelId) {
                                            return $plan['panel_id'] == $panelId;
                                        });
                                    @endphp
                                    {{-- @dump($filteredPlans, $panelId) --}}
                        
                                    <div class="hidden p-4 rounded-lg" id="panel-{{ $panelId }}" role="tabpanel"
                                        aria-labelledby="tab-{{ $panelId }}">
                                        {{-- SENDER --}}
                                        @if ($panelId == 1)
                                            <div class="flex flex-wrap">
                                                @foreach ($filteredPlans as $plan)
                                                    <div
                                                        class="w-full max-w-sm p-4 bg-gray-300 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                                        <div class="h-80">
                                                        <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                                            {{ $plan['plan_name'] }} </h5>
                                                        <div class="flex justify-center text-gray-900 dark:text-white">
                                                            <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                                            <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                                            <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>
                        
                                                        </div>
                                                        <div class="text-center">
                                                            <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                                                GST
                                                                Applicable</span>
                                                        </div>
                                                        <ul role="list" class="space-y-5 my-7">
                                                            @php $featureNameDisplayed = false; @endphp
                                                            <ul role="list" class="space-y-5 my-7">
                                                                @php
                                                                    $displayedFeatures = []; // An array to keep track of displayed features
                                                                @endphp
                        
                                                                @foreach ($plan['features'] as $feature)
                                                                    @if (in_array($feature['feature_name'], ['Received Return Challan', 'Create Challan']) &&
                                                                            !in_array($feature['feature_name'], $displayedFeatures))
                                                                        <li class="flex space-x-3 justify-center">
                                                                            <span
                                                                                class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                                                {{ $feature['feature_name'] }} -
                                                                                {{ $feature['feature_usage_limit'] }}
                                                                            </span>
                                                                        </li>
                                                                        @php
                                                                            $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                        
                                                            <li class="flex space-x-3 justify-center ">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                                                    -
                                                                    {{ $plan['user'] ?? '' }}</span>
                                                            </li>
                                                            <li class="flex space-x-3 justify-center">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                                                    - {{ $plan['topup'] }}</span>
                                                            </li>
                                                            </li>
                                                        </ul>
                                                        </div>
                                                        {{-- <div class="flex justify-center mt-4">
                                                              <button type="button"  wire:click.prevent="addToCart({{ $plan['id'] }})"  class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Apply Now</button>
                                                              
                                                        </div> --}}
                                                        <div x-data="{ open: false }" class="text-center">
                                                            <button class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900" @click="open = ! open">Apply Now</button>
                                                         
                                                            <div class="mt-3"
                                                                x-show="open"
                                                                x-transition:enter="transition ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 scale-90"
                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                x-transition:leave="transition ease-in duration-300"
                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                x-transition:leave-end="opacity-0 scale-90"
                                                            > 
                                                            <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'paid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Paid</button>

                                                            <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'unpaid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Unpaid</button>

                                                        </div>
                                                        </div>
                        
                                                    </div>
                                                    <!-- Content for panel 1 -->
                                                    <!-- Update content for Panel 1 here -->
                                                @endforeach
                                            </div>
                                            {{-- RECEIVER --}}
                                        @elseif ($panelId == 2)
                                            <div class="flex flex-wrap">
                                                @foreach ($filteredPlans as $plan)
                                                    <div class="w-full max-w-sm p-4 bg-gray-300 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                                        <div class="h-80">
                                                        <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                                            {{ $plan['plan_name'] }} </h5>
                                                        <div class="flex justify-center text-gray-900 dark:text-white">
                                                            <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                                            <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                                            <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>
                        
                                                        </div>
                                                        <div class="text-center">
                                                            <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                                                GST
                                                                Applicable</span>
                                                        </div>
                                                        
                                                        <ul role="list" class="space-y-5 my-7">
                                                            @php $featureNameDisplayed = false; @endphp
                                                            <ul role="list" class="space-y-5 my-7">
                                                                @php
                                                                    $displayedFeatures = []; // An array to keep track of displayed features
                                                                @endphp
                                                                @foreach ($plan['features'] as $feature)
                                                                    @if (in_array($feature['feature_name'], ['Create Return Challan', 'Received Challan', 'Challan Series No']) &&
                                                                            !in_array($feature['feature_name'], $displayedFeatures))
                                                                        <li class="flex space-x-3 justify-center">
                                                                            <span
                                                                                class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                                                {{ $feature['feature_name'] }} -
                                                                                {{ $feature['feature_usage_limit'] }}
                                                                            </span>
                                                                        </li>
                                                                        @php
                                                                            $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        
                                                            <li class="flex space-x-3 justify-center ">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                                                    -
                                                                    1</span>
                                                            </li>
                                                            <li class="flex space-x-3 justify-center">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                                                    -No</span>
                                                            </li>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div x-data="{ open: false }" class="text-center">
                                                        <button class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900" @click="open = ! open">Apply Now</button>
                                                     
                                                        <div
                                                            x-show="open"
                                                            x-transition:enter="transition ease-out duration-300"
                                                            x-transition:enter-start="opacity-0 scale-90"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-300"
                                                            x-transition:leave-start="opacity-100 scale-100"
                                                            x-transition:leave-end="opacity-0 scale-90"
                                                        > 
                                                        <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'paid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Paid</button>

                                                        <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'unpaid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Unpaid</button>

                                                    </div>
                                                    </div>
                                                    
                                                    </div>
                        
                                                    <!-- Content for panel 2 -->
                                                    <!-- Update content for Panel 2 here -->
                                                @endforeach
                                            </div>
                                            {{-- SELLER --}}
                                        @elseif ($panelId == 3)
                                            <div class="flex flex-wrap">
                        
                                                @foreach ($filteredPlans as $plan)
                                                    <div
                                                        class="w-full max-w-sm p-4 bg-gray-300 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                                        <div class="h-80">
                                                        <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                                            {{ $plan['plan_name'] }} </h5>
                                                        <div class="flex justify-center text-gray-900 dark:text-white">
                                                            <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                                            <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                                            <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>
                        
                                                        </div>
                                                        <div class="text-center">
                                                            <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                                                GST
                                                                Applicable</span>
                                                        </div>
                                                        <ul role="list" class="space-y-5 my-7">
                                                            @php $featureNameDisplayed = false; @endphp
                                                            <ul role="list" class="space-y-5 my-7">
                                                                @php
                                                                    $displayedFeatures = []; // An array to keep track of displayed features
                                                                @endphp
                                                                @foreach ($plan['features'] as $feature)
                                                                    @if (in_array($feature['feature_name'], ['Create Invoice', 'Purchase Order', 'Invoice Series No']) &&
                                                                            !in_array($feature['feature_name'], $displayedFeatures))
                                                                        <li class="flex space-x-3 justify-center">
                                                                            <span
                                                                                class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                                                {{ $feature['feature_name'] }} -
                                                                                {{ $feature['feature_usage_limit'] }}
                                                                            </span>
                                                                        </li>
                                                                        @php
                                                                            $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                        
                                                            <li class="flex space-x-3 justify-center ">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                                                    -
                                                                    1</span>
                                                            </li>
                                                            <li class="flex space-x-3 justify-center">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                                                    -No</span>
                                                            </li>
                                                            </li>
                                                        </ul>
                                                        </div>
                                                        <div x-data="{ open: false }" class="text-center">
                                                            <button class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900" @click="open = ! open">Apply Now</button>
                                                         
                                                            <div
                                                                x-show="open"
                                                                x-transition:enter="transition ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 scale-90"
                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                x-transition:leave="transition ease-in duration-300"
                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                x-transition:leave-end="opacity-0 scale-90"
                                                            > 
                                                            <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'paid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Paid</button>

                                                            <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'unpaid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Unpaid</button>

                                                        </div>
                                                        </div>
                        
                                                    </div>
                        
                                                    <!-- Content for panel 2 -->
                                                    <!-- Update content for Panel 2 here -->
                                                @endforeach
                                            </div>
                        
                                            {{-- BUYER --}}
                                        @elseif ($panelId == 4)
                                            <div class="flex  flex-wrap">
                                                @foreach ($filteredPlans as $plan)
                                                    <div
                                                        class="w-full max-w-sm p-4 bg-gray-300 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                                        <div class="h-80">
                                                        <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                                            {{ $plan['plan_name'] }} </h5>
                                                        <div class="flex justify-center text-gray-900 dark:text-white">
                                                            <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                                            <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                                            <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>
                        
                                                        </div>
                                                        <div class="text-center">
                                                            <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                                                GST
                                                                Applicable</span>
                                                        </div>
                                                        <ul role="list" class="space-y-5 my-7">
                                                            @php $featureNameDisplayed = false; @endphp
                                                            <ul role="list" class="space-y-5 my-7">
                                                                @php
                                                                    $displayedFeatures = []; // An array to keeAdd To p track of displayed features
                                                                @endphp
                                                                @foreach ($plan['features'] as $feature)
                                                                    @if (in_array($feature['feature_name'], ['Create Invoice', 'Purchase Order', 'Invoice Series No']) &&
                                                                            !in_array($feature['feature_name'], $displayedFeatures))
                                                                        <li class="flex space-x-3 justify-center">
                                                                            <span
                                                                                class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                                                {{ $feature['feature_name'] }} -
                                                                                {{ $feature['feature_usage_limit'] }}
                                                                            </span>
                                                                        </li>
                                                                        @php
                                                                            $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                        
                                                            <li class="flex space-x-3 justify-center ">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                                                    -
                                                                    1</span>
                                                            </li>
                                                            <li class="flex space-x-3 justify-center">
                        
                                                                <span
                                                                    class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                                                    -No</span>
                                                            </li>
                                                            </li>
                                                        </ul>
                                                        </div>
                                                        <div x-data="{ open: false }" class="text-center">
                                                            <button class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900" @click="open = ! open">Apply Now</button>
                                                         
                                                            <div
                                                                x-show="open"
                                                                x-transition:enter="transition ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 scale-90"
                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                x-transition:leave="transition ease-in duration-300"
                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                x-transition:leave-end="opacity-0 scale-90"
                                                            > 
                                                            <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'paid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Paid</button>

                                                            <button type="button" wire:click.prevent="addToCart({{ $plan['id'] }}, 'unpaid')" class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Unpaid</button>

                                                        </div>
                                                        </div>
                        
                                                    </div>
                        
                                                    <!-- Content for panel 2 -->
                                                    <!-- Update content for Panel 2 here -->
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            {{-- <div class="col-span-12 mt-4">
                                <div class="py-0 px-3 px-md-5">
                                    <a href="#" class="text-xs text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">About
                                        us | </a>
                                    <a href="#"
                                        class="text-xs text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Contact us |</a>
                                    <a href="{{ route('pricing') }}"
                                        class="text-xs text-[#007bff] font-medium hover:underline hover:bg-[#F0AC49] hover:text-white">Pricing
                                        |</a>
                                    <a href="#"
                                        class="text-xs text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Privacy Policy |</a>
                                    <a href="#" class="text-xs text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Terms
                                        &amp; Conditions |</a>
                                    <a href="#"
                                        class="text-xs text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Cancellation
                                        Policy</a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    {{-- <div
                        class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="medium-modal" wire:click.prevent="createTopup" type="button"
                            class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-xs px-5  m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Create
                            Package</button>
                        <button data-modal-hide="medium-modal" type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-5 .5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                    </div> --}}
                </div>

            </div>
        </div>
        
    </div>

    <script>
        document.addEventListener('livewire:load', function(e) {
            @this.on('removeUser', id => {
                Swal.fire({
                    title: "Are you sure?",
                    text: "Are you sure you want to remove?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#6fc5e0",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Remove",
                }).then((result) => {
                    if (result.value) {
                        @this.call('removeUser', id);
                        console.log('hello');
                    } else {
                        console.log("Canceled");
                    }
                });
            });
        });

    </script>
</div>
