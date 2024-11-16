<div class="bg-white">
    {{-- @dd(json_decode($errorMessage)); --}}
    

    @if ($errorMessage)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
        {{ $errorMessage }}
        @if ($errorData)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
        
        <ul>
            @foreach (json_decode($errorData) as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    </div>
@endif

    @if ($successMessage)
    <div  x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="success-alert" class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <div class="ms-3 text-sm ">
            <span class="font-medium">Success:</span>  {{ $successMessage }}
        </div> 
    </div>
    @endif
    
    {{-- @livewire('modal.modal', ['title' => 'Forgot Password', 'modalSize' => 'md']) --}}
    {{-- <livewire:modal.modal title="Forgot Password" modalSize="md">
 
    </livewire:modal.modal> --}}
          

    <div class="text-black  ">
        <div class="border-b border-gray-400 text-black text-sm flex flex-col sm:flex-row sm:hidden">
            <select class="px-2 my-2 w-full text-center rounded-lg text-xs" wire:model="activeTab">
                <option value="tab1">Add New Products</option>
                <option value="tab2">Update Products</option>
                <option value="tab3">Available Stock</option>
                <option value="tab4">Stock Out</option>
            </select>
        </div>
        {{-- @php 
         
        $mainUser = json_decode($this->mainUser); 
     
        @endphp
        @if ($mainUser->team_user != null)
                    @dump($mainUser)
                @if ($mainUser->team_user->permissions->permission->stock->add_stock == 1)

                @endif
                @endif --}}
        <div class="border-b p-1.5 border-gray-400 text-black text-sm hidden sm:flex">
         
            @php
                $mainUser = json_decode($this->mainUser);
            @endphp

            @if ($mainUser->team_user != null)
                @if ($mainUser->team_user->permissions->permission->stock->add_stock == 1)
                    <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Add New Products</button>
                @endif
                @if ($mainUser->team_user->permissions->permission->stock->update_stock == 1)
                    <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Update Products</button>
                @endif
                {{-- @if ($mainUser->team_user->permissions->permission->stock->view_stock == 1) --}}
                    <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab3' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab3')">Available Stock</button>
                {{-- @endif --}}
                {{-- @if ($mainUser->team_user->permissions->permission->stock->out_stock == 1) --}}
                    <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab4' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab4')">Stock Out</button>
                {{-- @endif --}}
            @else <!-- replace is_admin with your actual admin check -->
               <div class="flex flex-auto justify-between">
                <div>
                <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Add New Products</button>
                <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Update Products</button>
                <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab3' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab3')">Available Stock</button>
                <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab4' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab4')">Stock Out</button>
                
                </div>

                <div>
                    <button wire:loading class="px-4 p-1.5 w-auto text-center"><span class="loading loading-spinner loading-sm"></span></button>
                </div>
               </div>
            @endif
        </div>
        @if ($activeTab === 'tab1')
            <!-- Content for Tab 1 -->
            <div class="flex-grow md:ml-2 mt-2">
                <!-- Add new products form -->
                <div class="bg-white border border-gray-300 rounded-lg p-2 shadow-md">
                    <p class="font-semibold text-base">Bulk Product Upload </p>
                    <div class="text-blue-700 font-semibold mb-2 flex flex-col md:flex-row justify-between items-center">
                        <a href="{{ route('products.exportColumns') }}"
                            class="bg-[#E2DFDF] text-xs text-black hover:bg-orange mb-5 py-1 lg:py-2 px-4 rounded-lg">
                            Download Sample Sheet
                        </a>
                        <form wire:submit.prevent="productUpload" enctype="multipart/form-data" class="mt-2 md:mt-0">
                            <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                <input wire:model="uploadFile" class="block w-full md:w-96 mb-5 p-1 text-[0.6rem] text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                    @if($uploadFile)
                                        <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                type="submit"
                                                wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="productUpload">Upload</span>
                                            <span wire:loading wire:target="productUpload">Uploading...</span>
                                        </button>
                                         
                                    @endif
                                    <span wire:loading wire:target="uploadFile">Processing...</span>
                                </div>
                            </div>
                        </form>
                    </div>
                   
                </div>
                <div class="bg-white border border-gray-300 rounded-lg p-2 mt-5 shadow-md ">
                   
                        <div x-data="{ open: false }" >
                            <div class="mt-5">
                                <p class="font-semibold text-base">Single Product Upload </p>
                                <button @click="open = ! open" class="bg-[#E2DFDF] text-xs text-black hover:bg-orange my-6 py-1 lg:py-2 px-4 rounded-lg font-semibold ">Add Product</button>
                            </div>
                            <div class="mt-3" x-show="open" @click.outside="open = false">
                                <div class="flex space-x-4 flex-wrap grid grid-cols-4 sm:grid-cols-2">
                                    
                                   
                                    @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                                        @if (!empty($columnName))
                                            @php
                                                $this->createChallanRequest['columns'][$key]['column_name'] = $columnName;
                                            @endphp
                                            <div class=" ml-4">
                                                <label for="item-code" class="block mb-2 text-sm  text-gray-900 dark:text-white">{{ $columnName }}
                                                    @if ($columnName === 'Article')
                                                        <span class="text-red-600">*</span>
                                                    @endif
                                                </label>
                                                <input 
                                                    wire:model.defer="createChallanRequest.columns.{{ $key }}.column_value"
                                                    class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                    type="text" />
                                                    @error("createChallanRequest.columns.{$key}.column_value")
                                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                                    @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                
                                    <div>
                                        <label for="item-code" class="block mb-2 text-sm  text-gray-900 dark:text-white">Item Code<span
                                            class="text-red-600">*</span></label>
                                        <input type="text" wire:model.defer="createChallanRequest.item_code" id="item-code" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        @error('createChallanRequest.item_code')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="category" class="block mb-2 text-sm  text-gray-900 dark:text-white">Category</label>
                                        <input type="text" wire:model.defer="createChallanRequest.category" id="category" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                       
                                    </div>
                                    <div>
                                        <label for="warehouse" class="block mb-2 text-sm  text-gray-900 dark:text-white">Warehouse</label>
                                        <input type="text" wire:model.defer="createChallanRequest.warehouse" id="warehouse" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                       
                                    </div>
                                    <div>
                                        <label for="location" class="block mb-2 text-sm  text-gray-900 dark:text-white">Location</label>
                                        <input type="text" wire:model.defer="createChallanRequest.location" id="location" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                       
                                    </div>
                                  
                                    <div>
                                        <label for="location" class="block mb-2 text-sm  text-gray-900 dark:text-white">Unit</label>
                                        <div x-data="{ open: false, selectedOption: '', showModal: false }">
                                            <button
                                                @click="open = !open"
                                                class="bg-gray-50 border border-gray-400 text-gray-900 w-full rounded-lg focus:ring-blue-500 focus:border-blue-500 block  text-xs p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            >
                                                <span x-text="selectedOption || 'Select Unit'"></span>
                                            </button>
                                        
                                            <div x-show="open"
                                                @click.outside="open = false"
                                                class="absolute z-10 mt-2 w-1/4 bg-white rounded shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="max-h-60 overflow-y-auto" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                                    @foreach($unit as $option)
                                                        <a href="#" x-on:click.prevent="selectedOption = '{{ $option['short_name'] }}'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">{{ $option['short_name'] }}</a>
                                                    @endforeach
                                                </div>
                                                <a
                                                    href="#"
                                                    x-on:click.prevent="showModal = true; open = false" wire:click="modalShow"
                                                    class="flex items-center rounded-b-lg border-t border-gray-200 bg-gray-50 p-3 text-sm font-medium text-blue-600 hover:bg-gray-100 hover:underline dark:border-gray-600 dark:bg-gray-700 dark:text-blue-500 dark:hover:bg-gray-600"
                                                >
                                                     
                                                    Add Unit
                                                </a>
                                            </div>
                                             
                                            @if($modalShow)
                                            <!-- Modal -->
                                            <div 
                                                x-show="showModal"
                                                class="fixed inset-0 flex items-center justify-center z-50 w-full"
                                                style="background-color: rgba(0,0,0,.5);" wire:ignore>
                                                <div class="bg-white p-4 rounded-lg md:w-96 w-11/12">
                                                    <h2 class="font-medium sm:font-semibold mb-4">Add New Unit</h2>
                                                    <div x-data="{ unitName: '' }">
                                                        <form>
                                                            <div class="mb-4">
                                                                <input type="text" id="unitName" x-model="unitName" wire:model="unitName"
                                                                    class="shadow appearance-none border rounded text-sm w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                                    placeholder="Enter unit name"
                                                                />
                                                            </div>
                                                            <div class="flex justify-end mt-5 py-2">
                                                                <button
                                                                    type="button"
                                                                    @click="showModal = false" wire:click="modalHide"
                                                                    class="bg-white text-black text-xs sm:text-sm font-medium py-1 px-2 rounded mr-2 shadow"
                                                                >
                                                                    Cancel
                                                                </button>
                                                                <button type="button" wire:click="createNewUnit" :disabled="!unitName" :class="{'bg-gray-400 text-gray-400 cursor-not-allowed': !unitName, 'bg-white text-black': unitName}" class="text-xs sm:text-sm font-medium py-1 px-2 rounded shadow">
                                                                    Create
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                           
                                        </div>
                                        {{-- <script>
                                            document.addEventListener('alpine:init', () => {
                                                Alpine.data('dropdown', () => ({
                                                    open: false,
                                                    selectedOption: '',
                                                    showModal: false,
                                        
                                                    createNewUnit() {
                                                        // Add your logic to create a new unit here
                                                        console.log('Creating new unit...');
                                                        this.showModal = false;
                                                    },
                                                }));
                                            });
                                        </script> --}}
                                        
                                                                           
                                    </div>
                                    
                               
                                    <div>
                                        <label for="rate" class="block mb-2 text-sm  text-gray-900 dark:text-white">Rate<span
                                            class="text-red-600">*</span></label>
                                        <input type="text" wire:model.defer="createChallanRequest.rate" id="rate" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="qty" class="block mb-2 text-sm  text-gray-900 dark:text-white">Qty<span
                                            class="text-red-600">*</span></label>
                                        <input type="text" wire:model.defer="createChallanRequest.qty" id="qty" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>

                                    <div class="flex space-x-4 mt-2"> 
                                </div> 
                            </div>
                            <div class="my-7 ml-5">
                                <button wire:click="storeProduct" class="btn btn-sm border border-gray-200 bg-black text-white rounded-lg hover:bg-orange" >Add </button>
                            </div>
                    </div>
                    
                </div>
            </div>
        @elseif ($activeTab === 'tab2')
            <!-- Content for Tab 2 -->
            
            <div class="flex-grow md:ml-2 mt-2">
                <!-- Add new products form -->
                <div class="bg-white border border-gray-300 rounded-lg p-2">
                    <div class="text-blue-700 font-semibold mb-2 flex flex-col md:flex-row justify-between items-center">
                        <a  href="{{ route('products.exportProducts') }}"
                            class="bg-[#E2DFDF] text-xs text-black hover:bg-orange mb-5 py-1 lg:py-2 px-4 rounded-lg">
                            Download Stock Sheet
                        </a>
                        <form wire:submit.prevent="productUpdate" enctype="multipart/form-data" class="mt-2 md:mt-0">
                            <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                <input wire:model="updateFile" class="block w-full md:w-96 mb-5 p-1 text-[0.6rem] text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                    @if($updateFile)
                                    <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                            type="submit"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="productUpdate">Upload</span>
                                        <span wire:loading wire:target="productUpdate">Uploading...</span>
                                    </button>
                                    
                                @endif
                                <span wire:loading wire:target="updateFile">Processing...</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @elseif ($activeTab === 'tab3')
       
            <!-- Content for Tab 3 -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <div class="relative text-xs  shadow-md sm:rounded-lg flex justify-between ">
                    <div class="flex bg-white dark:bg-gray-900 mb-3 justify-between" >
                       <div class="flex"> 
                        <h5 class="mr-2" style="align-self: center;">Filter: </h5>
                        
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }"    wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <button id="dropdownArticleSearch" data-dropdown-toggle="dropdownArticleButton" x-init="$nextTick(() => initDropdown())"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full   bg-white rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2 "
                                    type="button">
                                    <span x-cloak>Article
                                    
                                    </span>
                                    @if (empty($Article))
                                        <!-- Button content -->
                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    @endif
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($Article))
                                            ({{ $Article }})
                                            <span wire:click="updateVariable('Article', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div  x-data="{ search: '', updateVariable: null }" id="dropdownArticleButton" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="article" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownArticleSearch">
                                    <li class="cursor-pointer "
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('Article','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($articles as $atcl)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($atcl ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('Article','{{ $atcl }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $atcl ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <button id="dropdownCodeSearch" data-dropdown-toggle="dropdownItemCodeSearch"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-  whitespace-nowrap bg-white   focus:outline-none   rounded-lg  px-2 py-1 text-center items-center justify-center  mr-2"
                                    type="button">
                                    <span x-cloak>Item Code <small>
                                     
                                        </small></span>
                                    @if (empty($item_code))
                                        <!-- Button content -->
                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    @endif
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($item_code))
                                            ({{ $item_code }})
                                            <span wire:click="updateVariable('item_code', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownItemCodeSearch" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="item_code" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownCodeSearch">
                                    <li class="cursor-pointer "
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('item_code','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($item_codes as $code)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($code ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('item_code','{{ $code }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $code ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                      
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }"    wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <button id="dropdownCategorySearch" data-dropdown-toggle="dropdownCategoryButton" x-init="$nextTick(() => initDropdown())"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2   "
                                    type="button">
                                    <span x-cloak>Category
                                       
                                    </span>
                                    @if (empty($category))
                                        <!-- Button content -->
                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    @endif
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($category))
                                            ({{ $category }})
                                            <span wire:click="updateVariable('category', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                                
                                
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownCategoryButton" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="article" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownCategorySearch">
                                    <li class="cursor-pointer "
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('category','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($categories as $cat)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($cat ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('category','{{ $cat }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $cat ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <!-- Button to toggle dropdown -->
                                <button id="dropdownLocationSearch" data-dropdown-toggle="dropdownLocateSearch"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
                                    type="button">
                                    <span x-cloak>Location </span>
                                        @if (empty($location))
                                    <!-- Button content -->
                                    <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                    @endif

                        
                                <small class="flex text-xs ml-1">
                                    @if (!empty($location))
                                        ({{ $location }})
                                        <span wire:click="updateVariable('location', null)"  class="cursor-pointer ml-2">X</span>
                                    @endif
                                </small>
                                </button>
                            </div>
                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownLocateSearch" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="location" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownLocationSearch">
                                    <li class="cursor-pointer"
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('location','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($locations as $loc)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($loc ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('location','{{ $loc }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $loc ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                    <button id="dropdownWarehouseSearch" data-dropdown-toggle="dropdownWareSearch"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
                                    type="button">
                                    <span x-cloak>Warehouse</span>
                                    <!-- Button content -->
                                    @if (empty($warehouse))
                                    <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                    @endif
                                    {{-- onclick="window.location.reload();" --}}
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($warehouse))
                                            ({{ $warehouse }})
                                            <span wire:click="updateVariable('warehouse', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                               
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownWareSearch" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="warehouse" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownLocationSearch" >
                                    <li class="cursor-pointer"
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('warehouse','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($warehouses as $ware)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($ware ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('warehouse','{{ $ware }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $ware ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                       </div>
                    </div>
                    @if ($mainUser->team_user != null)

                        @if ($mainUser->team_user->permissions->permission->stock->delete_stock == 1)
                            <button id="dropdownMenuIconHorizontalButton" data-dropdown-toggle="dropdownDotsHorizontal" class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600" type="button"> 
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                                </svg>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div id="dropdownDotsHorizontal" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow  dark:bg-gray-700 dark:divide-gray-600">
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconHorizontalButton">
                                    <li>
                                    <a href="#" onclick="deleteSelectedItems()"  class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg class="h-6 " xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"  viewBox="0 0 24 24">
                                        <path d="M 10 2 L 9 3 L 3 3 L 3 5 L 21 5 L 21 3 L 15 3 L 14 2 L 10 2 z M 4.3652344 7 L 6.0683594 22 L 17.931641 22 L 19.634766 7 L 4.3652344 7 z"></path>
                                        </svg></a>
                                    </li> 
                                </ul> 
                            </div> 
                        @endif
                    @else
                        <button id="dropdownMenuIconHorizontalButton" data-dropdown-toggle="dropdownDotsHorizontal" class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600" type="button"> 
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                            <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div id="dropdownDotsHorizontal" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow  dark:bg-gray-700 dark:divide-gray-600">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconHorizontalButton">
                                <li>
                                <a href="#" onclick="deleteSelectedItems()"  class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg class="h-5" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"  viewBox="0 0 24 24">
                                    <path d="M 10 2 L 9 3 L 3 3 L 3 5 L 21 5 L 21 3 L 15 3 L 14 2 L 10 2 z M 4.3652344 7 L 6.0683594 22 L 17.931641 22 L 19.634766 7 L 4.3652344 7 z"></path>
                                    </svg></a>
                                </li> 
                            </ul> 
                        </div> 
                        </div>
                    @endif
                    </div>
                    <span id="selectedCount" class="text-xs">
                          
                    </span>
                </div>
                <div class="relative text-xs  overflow-auto shadow-md sm:rounded-lg h-screen">
                    <div>
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                
                                <th class="px-2"><input type="checkbox" id="selectAllCheckbox" class="product-checkbox" value="all">
                                </th>
                                <th scope="col" class="va-b px-2 py-2 text-xs border border-gray-300">#</th>
                                {{-- @foreach ($ColumnDisplayNames as $columnName)
                                    <th scope="col" class="va-b px-2 py-2 text-xs">{{ $columnName }}</th>
                                @endforeach --}}
                                @if (!in_array('Article', $ColumnDisplayNames))
                                    <th class="va-b px-2 py-2 text-xs border border-gray-300">Article</th>
                                @endif
    
                                @if (!in_array('hsn' || 'Hsn', $ColumnDisplayNames))
                                    <th class="va-b px-2 py-2 text-xs border border-gray-300">HSN</th>
                                @endif
                               
                            @foreach ($ColumnDisplayNames as $index => $columnName)
                                @if (!empty($columnName))
                                    <th class="va-b px-2 py-2 text-xs border border-gray-300 whitespace-nowrap">
                                        @if ($index >= 3 && $index <= 6)
                                            {{ $index - 2 }} ({{ ucfirst($columnName)}})
                                        @else
                                            {{ ucfirst($columnName) }}
                                        @endif
                                    </th>
                                @endif
                            @endforeach
                            @foreach ($InvoiceColumnDisplayNames as $index => $columnName)
                                @if ($index >= 3 && !empty($columnName))
                                    <th class="va-b px-2 py-2 text-xs border border-gray-300 whitespace-nowrap">
                                        @if ($index <= 6)
                                            {{ $index - 2 }} ({{ ucfirst($columnName)}})
                                        @else
                                            {{ ucfirst($columnName) }}
                                        @endif
                                    </th>
                                @endif
                            @endforeach
                
    
                        
                             
                                <th scope="col" class="va-b px-2 py-2 text-xs ">Action</th>
    
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $product)
                                    <tr
                                        class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                                        <td class="px-2 py-1 text-xs border border-gray-300">
                                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                        </td>
                                        
                                        <td class="px-2 py-1 text-xs border border-gray-300">
                                            {{ ++$key }}</div>
                                        </td>
    
                                        {{-- For Showing all the columns --}}
                                        {{-- @php
                                        $product = (object) $product;
                                        @endphp
     
                                        @foreach ($product->details as $column)
                                        @php
                                        $column = (object) $column;
                                        @endphp
                                            <td class="px-2 py-1 text-xs border border-gray-300">
                                                {{ $column->column_value }}</td>
                                        @endforeach --}}
                                        {{-- {{ dd($product) }} --}}
                                        {{-- For Showing all the columns --}}
    
     
    
    
                                        @php
                                        $product = (object) $product;
                                        @endphp
                                        @foreach ($product->details as $index => $column)
                                        @php
                                        $column = (object) $column;
                                        @endphp
                                            @if (!empty($ColumnDisplayNames[$index]))
                                                <td class="va-b px-2 py-2 text-xs border border-gray-300">
                                                    @if ($index <= 2)
                                                        {{ $column->column_value }}
                                                    @elseif ($index >= 3 && $index <= 6)
                                                        {{ $column->column_value}}
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                     
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->item_code }}</td>
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->category ?? null }}</td>
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->warehouse ?? null }}</td>
    
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ ucfirst($product->location) }}</td>
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ ucfirst($product->unit) }}</td>
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->qty }}</td>
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->rate }}</td>
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ date('j-m-Y', strtotime($product->created_at))  }}</td>
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{  date('h:i A', strtotime($product->created_at))  }}</td>
                                        {{-- <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->tax }}</td> --}}
                                        <td class="">
                                            {{-- @if (!Str::startsWith($data->series_number, 'CH')) --}}
                    
                                            <button id="dropdownDefaultButton-{{ $key }}" data-dropdown-toggle="dropdown-{{ $key }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3 py-1 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-1.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                                {{-- @endif --}}
                    
                                            <div id="dropdown-{{ $key }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    @if ($mainUser->team_user != null)

                                                        @if ($mainUser->team_user->permissions->permission->stock->edit_stock == 1)
                                                            <li>
                                                                <a href="javascript:void(0);"   wire:click.prevent="selectChallanSeries('{{ json_encode($product) ?? '' }}')" data-modal-target="edit-modal" data-modal-toggle="edit-modal" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                                            </li>
                                                
                                                        
                                                        @endif

                                                        @if ($mainUser->team_user->permissions->permission->stock->delete_stock == 1)
                                                        <li>
                                                            <a href="javascript:void(0);"    wire:click="$emit('triggerDelete', {{ $product->id ?? '' }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                        </li>
                                                        @endif
                                                    @else
                                                    <li>
                                                        <a href="javascript:void(0);"   wire:click.prevent="selectChallanSeries('{{ json_encode($product) ?? '' }}')" data-modal-target="edit-modal" data-modal-toggle="edit-modal" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                                    </li>
                                                  
                                                    <li>
                                                        <a href="javascript:void(0);"    wire:click="$emit('triggerDelete', {{ $product->id ?? '' }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>
                                                    @endif
                                             
     
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Default Modal -->
                <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
                    <div class="relative w-full max-w-lg max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-2 border-b rounded-t dark:border-gray-600">
                                <h3 class="text-lg text-gray-900 dark:text-white">
                                    Edit Product

                                </h3>
                                <button type="button" wire:click.prevent='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                                <div class="p-6 space-y-6 ">
                                    <div class="-mx-2 grid grid-cols-2">
                                        @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                                            @if (!empty($columnName))
                                                @php
                                                    $this->editChallanRequest['details']['column_name'] = $columnName;
                                                @endphp
                                                    <div class="px-2">
                                                        <label for="item-code" class="block mb-1 text-xs  text-gray-900 dark:text-white">{{ $columnName }}
                                                            @if ($columnName === 'Article')
                                                                <span class="text-red-600">*</span>
                                                            @endif
                                                        </label>
                                                        <input 
                                                            wire:model.defer="editChallanRequest.details.{{ $key }}.column_value"
                                                            class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                            type="text" />
                                                            @error("editChallanRequest.details.{$key}.column_value")
                                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                                            @enderror
                                                            
                                                    </div>
                                                    @endif
                                        @endforeach
                                        <div class="px-2">
                                            <label for="item-code" class="block mb-1 text-xs  text-gray-900 dark:text-white">Item Code<span
                                                class="text-red-600">*</span></label>
                                            <input type="text" wire:model.defer="editChallanRequest.item_code" id="item-code" disabled class="block w-full cursor-not-allowed p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            @error('editChallanRequest.item_code')
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="px-2">
                                            <label for="category" class="block mb-1 text-xs  text-gray-900 dark:text-white">Category</label>
                                            <input type="text" wire:model.defer="editChallanRequest.category" id="category" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        
                                        </div>
                                        <div class="px-2">
                                            <label for="warehouse" class="block mb-1 text-xs  text-gray-900 dark:text-white">Warehouse</label>
                                            <input type="text" wire:model.defer="editChallanRequest.warehouse" id="warehouse" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        
                                        </div>
                                        <div class="px-2">
                                            <label for="location" class="block mb-1 text-xs  text-gray-900 dark:text-white">Location</label>
                                            <input type="text" wire:model.defer="editChallanRequest.location" id="location" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        
                                        </div>
                                        <div class="px-2">
                                            <label for="unit" class="block mb-1 text-xs  text-gray-900 dark:text-white">Unit</label>
                                            <input type="text" wire:model.defer="editChallanRequest.unit" id="unit" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        
                                
                                        <div class="px-2">
                                            <label for="rate" class="block mb-1 text-xs  text-gray-900 dark:text-white">Rate</label>
                                            <input type="text" wire:model.defer="editChallanRequest.rate" id="rate" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div class="px-2">
                                            <label for="qty" class="block mb-1 text-xs  text-gray-900 dark:text-white">Qty</label>
                                            <input type="text" wire:model.defer="editChallanRequest.qty" id="qty" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        </div>
                            
                                <!-- Modal footer -->
                                <div class="flex items-center p-2 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                    <button data-modal-hide="edit-modal" type="button" wire:click.prevent='editProduct' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                                    {{-- <button data-modal-hide="edit-modal" type="button" wire:click.prevent='resetChallanSeries()' class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button> --}}
                                </div>
                            </div>
                        </div>
                                                
            
                                    
                    </div>
                    </div>
                </div>
            </div>
             
            
        @elseif ($activeTab === 'tab4')
            <!-- Content for Tab 4 -->
            <div class="bg-white shadow-md rounded-lg p-4 overflow-auto">
                <div class="relative text-xs overflow-x-auto shadow-md sm:rounded-lg h-screen">
                    <div class="flex bg-white dark:bg-gray-900 mb-3" >
                        <h5 class="mr-2" style="align-self: center;">Filter: </h5>
                        
                        <div class="mr-2 flex" x-data="{ search: '', selectedUser: null }"    wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <button id="dropdownArticleSearch" data-dropdown-toggle="dropdownArticleButton" x-init="$nextTick(() => initDropdown())"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full   bg-white rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2 "
                                    type="button">
                                    <span x-cloak>Article
                                    
                                    </span>
                                    @if (empty($Article))
                                        <!-- Button content -->
                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    @endif
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($Article))
                                            ({{ $Article }})
                                            <span wire:click="updateVariable('Article', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownArticleButton" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="article" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownArticleSearch">
                                    <li class="cursor-pointer "
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('Article','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($articles as $atcl)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($atcl ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('Article','{{ $atcl }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $atcl ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <button id="dropdownCodeSearch" data-dropdown-toggle="dropdownItemCodeSearch"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-  whitespace-nowrap bg-white   focus:outline-none   rounded-lg  px-2 py-1 text-center items-center justify-center  mr-2"
                                    type="button">
                                    <span x-cloak>Item Code <small>
                                     
                                        </small></span>
                                    @if (empty($item_code))
                                        <!-- Button content -->
                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    @endif
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($item_code))
                                            ({{ $item_code }})
                                            <span wire:click="updateVariable('item_code', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownItemCodeSearch" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="item_code" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownCodeSearch">
                                    <li class="cursor-pointer "
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('item_code','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($item_codes as $code)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($code ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('item_code','{{ $code }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $code ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                      
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }"    wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <button id="dropdownCategorySearch" data-dropdown-toggle="dropdownCategoryButton" x-init="$nextTick(() => initDropdown())"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2   "
                                    type="button">
                                    <span x-cloak>Category
                                       
                                    </span>
                                    @if (empty($category))
                                        <!-- Button content -->
                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    @endif
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($category))
                                            ({{ $category }})
                                            <span wire:click="updateVariable('category', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                                
                                
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownCategoryButton" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="article" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownCategorySearch">
                                    <li class="cursor-pointer "
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('category','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($categories as $cat)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($cat ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('category','{{ $cat }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $cat ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <!-- Button to toggle dropdown -->
                                <button id="dropdownLocationSearch" data-dropdown-toggle="dropdownLocateSearch"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
                                    type="button">
                                    <span x-cloak>Location </span>
                                        @if (empty($location))
                                    <!-- Button content -->
                                    <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                    @endif

                        
                                <small class="flex text-xs ml-1">
                                    @if (!empty($location))
                                        ({{ $location }})
                                        <span wire:click="updateVariable('location', null)"  class="cursor-pointer ml-2">X</span>
                                    @endif
                                </small>
                                </button>
                            </div>
                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownLocateSearch" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="location" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownLocationSearch">
                                    <li class="cursor-pointer"
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('location','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($locations as $loc)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($loc ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('location','{{ $loc }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $loc ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                    <button id="dropdownWarehouseSearch" data-dropdown-toggle="dropdownWareSearch"
                                    data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-black flex w-full bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
                                    type="button">
                                    <span x-cloak>Warehouse</span>
                                    <!-- Button content -->
                                    @if (empty($warehouse))
                                    <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                    @endif
                                    {{-- onclick="window.location.reload();" --}}
                                    <small class="flex text-xs ml-1">
                                        @if (!empty($warehouse))
                                            ({{ $warehouse }})
                                            <span wire:click="updateVariable('warehouse', null)"  class="cursor-pointer ml-2">X</span>
                                        @endif
                                    </small>
                                </button>
                               
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownWareSearch" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="warehouse" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownLocationSearch" >
                                    <li class="cursor-pointer"
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('warehouse','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($warehouses as $ware)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($ware ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('warehouse','{{ $ware }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $ware ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                       
                        {{-- <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <button id="dropdownLocationSearch" data-dropdown-toggle="dropdownLocateSearch"
                                data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                class="text-black flex w-full border border-gray-900  bg-white hover:bg-orange  text-xs rounded-lg text-[0.6rem] px-2 py-1 text-center items-center justify-center  mr-2 mb-2"
                                type="button">
                                <span x-cloak>Location<small>
                                        @if (!empty($location))
                                            ({{ $location }})
                                        @endif
                                    </small></span>
                                <!-- Button content -->
                                <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>

                            </button>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownLocateSearch" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="location" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownLocationSearch">
                                    <li class="cursor-pointer"
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('location','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($locations as $loc)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($loc ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('location','{{ $loc }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $loc ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div> --}}
                        
                        
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <div class="flex border border-gray-900 rounded-lg text-xs">
                                <button id="dropdownOutMethodSearch" data-dropdown-toggle="dropdownOutMethodButton"
                                        data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                        class="text-black flex w-full    bg-white    text-xs rounded-lg   px-2 py-1 text-center items-center justify-center  mr-2  "
                                        type="button">
                                    
                                    <span x-cloak>Out Method</span>
                                        <!-- Button content -->
                                        @if (empty($OutMethod))
                                        <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                        @endif
                                        {{-- onclick="window.location.reload();" --}}
                                        <small class="flex text-xs ml-1">
                                            @if (!empty($OutMethod))
                                                ({{ $OutMethod }})
                                                <span wire:click="updateVariable('OutMethod', null)"  class="cursor-pointer ml-2">X</span>
                                            @endif
                                        </small>
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div x-data="{ search: '', updateVariable: null }" id="dropdownOutMethodButton" wire:ignore.self
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                <!-- Search input -->
                                <div class="p-3">
                                    <label for="input-group-search" class="sr-only">Search</label>
                                    <div class="relative text-xs">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-black dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" id="input-group-search"
                                            class="block w-full p-2 pl-10 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Search user">
                                    </div>
                                </div>
                                <input type="hidden" wire:model="out_method" style="display: none;">
                                <!-- Filtered list based on search -->
                                <ul class="h-48 px-3 pb-3 overflow-y-auto text-xs text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownOutMethodSearch">
                                    <li class="cursor-pointer "
                                        x-show="search === '' || '{{ strtolower(null) }}'.includes(search.toLowerCase())"
                                        wire:click.prevent="updateVariable('OutMethod','{{ null }}')">
                                        <div
                                            class="flex items-center pl-2 rounded  hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                            <label
                                                class="w-full py-2 ml-2 text-xs  text-gray-900 rounded cursor-pointer dark:text-gray-300">All</label>
                                        </div>
                                    </li>
                                    @foreach ($outMethods as $method)
                                        <li class="cursor-pointer"
                                            x-show="search === '' || '{{ strtolower($method ?? null) }}'.includes(search.toLowerCase())"
                                            wire:click.prevent="updateVariable('OutMethod','{{ $method }}')">
                                            <div
                                                class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                <label
                                                    class="w-full py-2 ml-2 text-xs text-xs text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $method ?? null }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="mr-2" x-data="{ search: '', selectedUser: null }" wire:ignore.self>
                            <!-- Button to toggle dropdown -->
                            <button id="dropdownOutDateSearch" data-dropdown-toggle="dropdownOutDateButton" x-init="$nextTick(() => initDropdown())"
                                data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                class="text-black flex w-full border border-gray-900 bg-white hover:bg-orange rounded-lg text-xs px-2 py-1 text-center items-center justify-center  mr-2 mb-2"
                                type="button">
                                <span x-cloak>Out Date<small>
                                        @if (!empty($OutDate))
                                            ({{ $OutDate }})
                                        @endif
                                    </small></span>
                                <!-- Button content -->
                                <svg class="w-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-data="{ from: null, to: null, selectedDate: null }" id="dropdownOutDateButton"
                                class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700" wire:ignore.self>
                                <!-- Date input -->
                                <div class="p-3">
                                    <label for="date-from"
                                        class="text-xs  text-black dark:text-gray-300">From</label>
                                    <input x-model="from" type="date" id="date-from" wire:model="from" wire:change="updateVariable('from', $event.target.value)"
                                        class="block  p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </div>
                                <div class="p-3">
                                    <label for="date-to"
                                        class="text-xs  text-black dark:text-gray-300">To</label>
                                    <input x-model="to" type="date" id="date-to" wire:model="to" wire:change="updateVariable('to', $event.target.value)"
                                        class="block  p-2 text-xs text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            <th scope="col" class="va-b px-2 py-2 text-xs border border-gray-300 whitespace-nowrap">#</th>
                            @foreach ($ColumnDisplayNames as $index => $columnName)
                            @if ($index < 3)
                                <th scope="col" class="va-b px-2 py-2 text-xs border border-gray-300">{{ $columnName }}</th>
                            @endif
                            @endforeach
                            @if (!in_array('Article', $ColumnDisplayNames))
                                <th class="va-b px-2 py-2 text-xs border border-gray-300">Article</th>
                            @endif

                            @if (!in_array('hsn' || 'Hsn', $ColumnDisplayNames))
                                <th class="va-b px-2 py-2 text-xs border border-gray-300">HSN</th>
                            @endif
                             
                @foreach ($InvoiceColumnDisplayNamesTab4 as $index => $columnName)
                    {{-- @if ($index >= 3) --}}
                        <th class="va-b px-2 py-2 text-xs border border-gray-300 whitespace-nowrap">
                            {{ucfirst($columnName)}}
                        </th>
                    {{-- @endif --}}
                @endforeach
            

                    
                            {{-- Show static columns if not present --}}

                            {{-- <th scope="col" class="va-b px-2 py-2 text-xs border border-gray-300 ">Item Code</th>
                            <th scope="col" class="va-b px-2 py-2 text-xs border border-gray-300 ">Unit</th>
                            <th scope="col" class="va-b px-2 py-2 text-xs border border-gray-300 ">Qty</th>
                            <th scope="col" class="va-b px-2 py-2 text-xs border border-gray-300 ">Rate</th> --}}
                            {{-- <th class="va-b px-2 py-2 text-xs border border-gray-300">Location</th> --}}

                        </thead>
                        <tbody>
                            @foreach ($OutData as $key => $product)
                                <tr
                                    class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
 
                                            <td class="px-2 py-1 text-xs border border-gray-300">{{ ++$key }}</td>

                                     
                                    @foreach($product->product->details as $index => $detail)
                                    @if ($index < 3)
                                        <td class="px-2 py-1 text-xs border border-gray-300"> {{ $detail->column_value }}</td>
                                        @endif
                                    @endforeach

                                
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->product->item_code }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ ucfirst($product->product->category ?? null) }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ ucfirst($product->product->warehouse ?? null) }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ ucfirst($product->product->location) }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->product->unit }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->product->qty }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->product->rate }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->qty_out }}</td>

                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->challan->receiver }}</td>
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ $product->challan->challan_series }}-{{$product->challan->series_num}}</td>


                                    <td class="px-2 py-1 text-xs border border-gray-300">{{ ucfirst($product->out_method) }}</td>
                                    @if ($product->out_at)
                                        <td class="px-2 py-1 text-xs border border-gray-300">{{ date('j-m-Y', strtotime($product->out_at))  }}</td>
                                    @else
                                        <td class="px-2 py-1 text-xs border border-gray-300">N/A</td>
                                    @endif
                                    <td class="px-2 py-1 text-xs border border-gray-300">{{  date('h:i A', strtotime($product->created_at))  }}</td>
                                   
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    </div>
    </div>

    <script>
        // Listen for click events on the delete button
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
                        @this.call('deleteProduct', id);
                        console.log('hello');
                    } else {
                        console.log("Canceled");
                    }
                });
            });
        });
 
       
    
    // Event listener to detect tab change and reinitialize dropdown
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', (message, component) => {
            // initDropdown();
            initSelectAllCheckbox();
            initFlowbite();
        });
    });

   
    </script>
<script>
    let selectedIds = [];
 
    // Function to reinitialize the select-all checkbox
    function initSelectAllCheckbox() {
  // Get the select-all checkbox and individual product checkboxes
  const selectAllCheckbox = document.getElementById('selectAllCheckbox');
  const productCheckboxes = document.querySelectorAll('.product-checkbox:not([value="all"])');
  const selectedCountSpan = document.getElementById('selectedCount');

  // Add event listener to the select-all checkbox
  selectAllCheckbox.addEventListener('change', function () {
    const isChecked = this.checked;
    const visibleProductCheckboxes = document.querySelectorAll('.product-checkbox:not([value="all"]):not([style*="display: none"])');

    visibleProductCheckboxes.forEach(checkbox => {
      checkbox.checked = isChecked;
      const productId = checkbox.value;
      if (isChecked && !selectedIds.includes(productId)) {
        selectedIds.push(productId);
      } else if (!isChecked && selectedIds.includes(productId)) {
        selectedIds = selectedIds.filter(id => id !== productId);
      }
    });

    selectedCountSpan.textContent = selectedIds.length + ' Selected';
  });

  // Add event listener to checkboxes to update selectedIds array
  productCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const productId = this.value;
      if (this.checked && !selectedIds.includes(productId)) {
        selectedIds.push(productId);
      } else if (!this.checked && selectedIds.includes(productId)) {
        selectedIds = selectedIds.filter(id => id !== productId);
      }
      selectedCountSpan.textContent = selectedIds.length + ' Selected';
    });
  });
}
 
    
    // Function to handle deletion of selected items
    function deleteSelectedItems() {
        if (selectedIds.length > 0) {
            Swal.fire({
            title: "Are you sure?",
            text: "Are you sure you want to delete the selected items?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#6fc5e0",
            cancelButtonColor: "#d33",
            confirmButtonText: "Delete",
            }).then((result) => {
            if (result.value) {
                // Send selectedIds array to Livewire component for deletion
                Livewire.emit('deleteSelected', selectedIds);
                selectedIds = [];
                console.log(selectedIds);
             // Uncheck the "Select All" checkbox
            document.getElementById('selectAllCheckbox').checked = false;
            } else {
                console.log("Canceled");
            }
            });
        } else {
            alert("Please select at least one item to delete.");
        }
       
    }
 </script>
