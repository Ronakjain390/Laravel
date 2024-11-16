<div class="bg-white" x-data="{
    activeTab: localStorage.getItem('activeTab') || 'tab3',
    setActiveTab(tab) {
        this.activeTab = tab;
        localStorage.setItem('activeTab', tab);
    }
}" x-init="$watch('activeTab', value => localStorage.setItem('activeTab', value));">

    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative"
                id="successMessage"></p>
        </div>
    </div>
    <div id="errorModal" style="display: none;">
        <div
            class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative">
            <p class="mt-3" id="errorMessage"></p>
            @if ($errorFileUrl)
                <a class="hover:cursor-pointer hover:underline mt-2 bg-gray-800 text-white px-2 rounded ml-1"
                    href="{{ $errorFileUrl }}" download>Download</a>
            @endif
        </div>
    </div>

    <script>
        window.addEventListener('show-error-message', event => {
            document.getElementById('errorMessage').textContent = event.detail.message;
            document.getElementById('errorModal').style.display = 'block';
            setTimeout(() => {
                document.getElementById('errorModal').style.display = 'none';
            }, 10000);
        });

        window.addEventListener('show-success-message', event => {
            document.getElementById('successMessage').textContent = event.detail.message;
            document.getElementById('successModal').style.display = 'block';
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 10000);
        });
    </script>

    <div class="text-black">
        <div class="border-b border-gray-400 text-black text-sm flex flex-col sm:flex-row sm:hidden">
            <select class="px-2 my-2 w-full text-center rounded-lg text-xs" x-model="activeTab">
                <option value="tab1">Add New Products</option>
                <option value="tab2">Update Products</option>
                <option value="tab3">Available Stock</option>
                <option value="tab4">Stock Out</option>
                <option value="tab5">Deleted Stock</option>
            </select>
        </div>

        <div class="border-b p-1.5 border-gray-400 text-black text-sm hidden sm:flex">
            @php
                $mainUser = json_decode($this->mainUser);
            @endphp

            @if ($mainUser->team_user != null)
                {{-- <button class="px-4 p-1.5 w-auto text-center"
                    :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab3' }"
                    @click="setActiveTab('tab3')">Available Stock</button>
                @if ($mainUser->team_user->permissions->permission->stock->add_stock == 1)
                    <button class="px-4 p-1.5 w-auto text-center"
                        :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab1' }"
                        @click="setActiveTab('tab1')">Add New Products</button>
                @endif
                @if ($mainUser->team_user->permissions->permission->stock->update_stock == 1)
                    <button class="px-4 p-1.5 w-auto text-center"
                        :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab2' }"
                        @click="setActiveTab('tab2')">Update Products</button>
                @endif
                <button class="px-4 p-1.5 w-auto text-center"
                    :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab4' }"
                    @click="setActiveTab('tab4')">Stock Out</button>
                <button class="px-4 p-1.5 w-auto text-center"
                    :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab5' }"
                    @click="setActiveTab('tab5')">Deleted Stock</button> --}}
                    <div class="flex flex-auto justify-between">
                        <div>
                            <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab3' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab3')">Available Stock</button>
                            <button class="px-4 p-1.5 w-auto text-center
                            {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Add New Products</button>
                            <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Update Products</button>
                            <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab4' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab4')">Stock Out</button>
                            <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab5' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab5')">Deleted Stock</button>
                        </div>
                        <div>
                            <button wire:loading class="px-4 p-1.5 w-auto text-center">
                                <span class="loading loading-spinner loading-sm"></span>
                            </button>
                        </div>
                    </div>
            @else
                {{-- <div class="flex flex-auto justify-between">
                    <div>
                        <button class="px-4 p-1.5 w-auto text-center"
                            :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab3' }"
                            @click="setActiveTab('tab3')">Available Stock</button>
                        <button class="px-4 p-1.5 w-auto text-center"
                            :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab1' }"
                            @click="setActiveTab('tab1')">Add New Products</button>
                        <button class="px-4 p-1.5 w-auto text-center"
                            :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab2' }"
                            @click="setActiveTab('tab2')">Update Products</button>
                        <button class="px-4 p-1.5 w-auto text-center"
                            :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab4' }"
                            @click="setActiveTab('tab4')">Stock Out</button>
                        <button class="px-4 p-1.5 w-auto text-center"
                            :class="{ 'bg-orange text-white rounded-lg': activeTab === 'tab5' }"
                            @click="setActiveTab('tab5')">Deleted Stock</button>
                    </div>
                    <div>
                        <button wire:loading class="px-4 p-1.5 w-auto text-center"><span
                                class="loading loading-spinner loading-sm"></span></button>
                    </div>
                </div> --}}
                <div class="flex flex-auto justify-between">
                    <div>
                        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab3' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab3')">Available Stock</button>
                        <button class="px-4 p-1.5 w-auto text-center
                        {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Add New Products</button>
                        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Update Products</button>
                        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab4' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab4')">Stock Out</button>
                        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab5' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab5')">Deleted Stock</button>
                    </div>
                    <div>
                        <button wire:loading class="px-4 p-1.5 w-auto text-center">
                            <span class="loading loading-spinner loading-sm"></span>
                        </button>
                    </div>
                </div>

            @endif
        </div>

        <div>

        @if ($activeTab === 'tab3')
        {{-- <div x-show="activeTab === 'tab3'"> --}}
        <div wire:key="tab3">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100">
                <livewire:dashboard.stock.available-stock :moveWarehouses="$moveWarehouses" :moveCategories="$moveCategories"
                   />
            </div>
        </div>
        @elseif($activeTab === 'tab1')
        <div wire:key="tab1" >
            <!-- Content for Tab 1 -->
            <div class="flex-grow md:ml-2 mt-2">
                <!-- Add new products form -->
                <div class="bg-white border border-gray-300 rounded-lg p-2 shadow-md">
                    <p class="font-semibold text-base">Bulk Product Upload </p>
                    <div
                        class="text-blue-700 font-semibold mb-2 flex flex-col md:flex-row justify-between items-center">
                        <a href="{{ route('products.exportColumns') }}"
                            class="bg-[#E2DFDF] text-xs text-black hover:bg-orange mb-5 py-1 lg:py-2 px-4 rounded-lg">
                            Download Sample Sheet
                        </a>
                        <form wire:submit.prevent="productUpload" enctype="multipart/form-data" class="mt-2 md:mt-0">
                            <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                <input wire:model="uploadFile"
                                    class="block w-full md:w-96 mb-5 p-1 text-[0.6rem] text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                    id="small_size" type="file" style="width: 100%;">
                                <div
                                    class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                    @if ($uploadFile)
                                        <button
                                            class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                            type="submit" wire:loading.attr="disabled">
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
                <div>
                    <div>
                        <div class="bg-white border border-gray-300 rounded-lg p-2 mt-5 shadow-md">
                            <div id="singleProductModal" style="display: none;">
                                <div class="modal-content">
                                    <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative"
                                        id="singleProductAdd"></p>
                                </div>
                            </div>

                            <div x-data="{ open: false }" x-init="$nextTick(() => { open = false; })" wire:key="single-product-form">
                                <div class="mt-5">
                                    <p class="font-semibold text-base">Single Product Upload </p>
                                    <button @click="open = !open"
                                        class="bg-[#E2DFDF] text-xs text-black hover:bg-orange my-6 py-1 lg:py-2 px-4 rounded-lg font-semibold">
                                        Add Product
                                    </button>
                                </div>
                                <div x-show="open" x-cloak @click.outside="open = false" class="mt-3">
                                    <div class="flex space-x-4 flex-wrap grid sm:grid-cols-2">
                                        @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                                        @if (!empty($columnName))
                                            <div class="ml-4">
                                                <label for="item-code-{{ $key }}"
                                                    class="block mb-2 text-sm text-gray-900 dark:text-white">
                                                    {{ $columnName }}
                                                    @if ($columnName === 'Article')
                                                        <span class="text-red-600">*</span>
                                                    @endif
                                                </label>
                                                <input
                                                    wire:model.defer="createChallanRequest.columns.{{ $key }}.column_value"
                                                    id="item-code-{{ $key }}"
                                                    class="block w-full p-2 text-gray-900 border rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                                                    @error('createChallanRequest.columns.' . $key . '.column_value') border-red-500 @enderror"
                                                    type="text" />
                                                @error('createChallanRequest.columns.' . $key . '.column_value')
                                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif
                                    @endforeach

                                        <!-- Additional fields -->
                                        <div>
                                            <label for="item-code"
                                                class="block mb-2 text-sm text-gray-900 dark:text-white">Item Code<span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" wire:model.defer="createChallanRequest.item_code"
                                                id="item-code"
                                                class="block w-full p-2 text-gray-900 border rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                                                @error('createChallanRequest.item_code') border-red-500 @enderror">
                                            @error('createChallanRequest.item_code')
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="category"
                                                class="block mb-2 text-sm text-gray-900 dark:text-white">Category</label>
                                            <input type="text" wire:model.defer="createChallanRequest.category"
                                                id="category"
                                                class="block w-full p-2 text-gray-900 border  rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label for="warehouse"
                                                class="block mb-2 text-sm text-gray-900 dark:text-white">Warehouse</label>
                                            <input type="text" wire:model.defer="createChallanRequest.warehouse"
                                                id="warehouse"
                                                class="block w-full p-2 text-gray-900 border  rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label for="location"
                                                class="block mb-2 text-sm text-gray-900 dark:text-white">Location</label>
                                            <input type="text" wire:model.defer="createChallanRequest.location"
                                                id="location"
                                                class="block w-full p-2 text-gray-900 border  rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label for="rate"
                                                class="block mb-2 text-sm text-gray-900 dark:text-white">Price</label>
                                            <div class="flex items-center">
                                                <select wire:model.defer="createChallanRequest.with_tax"
                                                    class="bg-gray-50 border  text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block p-1.5 rounded-l-lg dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                    <option value="false">With Tax</option>
                                                    <option value="true">Without Tax</option>
                                                </select>
                                                <input type="number" wire:model.defer="createChallanRequest.rate"
                                                    id="rate"
                                                    class="block w-full p-2 text-gray-900 border  rounded-r-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="tax"
                                                class="block mb-2 text-sm text-gray-900 dark:text-white">Tax</label>
                                            <input type="number" wire:model.defer="createChallanRequest.tax"
                                                id="tax"
                                                class="block w-full p-2 text-gray-900 border  rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label for="qty"
                                                class="block mb-2 text-sm text-gray-900 dark:text-white">Qty<span
                                                    class="text-red-600">*</span></label>
                                            <input type="number" wire:model.defer="createChallanRequest.qty"
                                                id="qty"
                                                class="block w-full p-2 text-gray-900 border rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                                                @error('createChallanRequest.qty') border-red-500 @enderror">
                                            @error('createChallanRequest.qty')
                                                <span class="text-red-600 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="my-7 ml-5">
                                        <button wire:click="storeProduct"
                                            class="btn-sm border border-gray-200 rounded-lg bg-black text-white hover:bg-orange"
                                            >
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        [x-cloak] {
                            display: none !important;
                        }
                    </style>

                    <script>
                        document.addEventListener('livewire:load', function() {
                            function validateForm() {
                                const article = document.getElementById('item-code-0').value.trim();
                                const itemCode = document.getElementById('item-code').value.trim();
                                const qty = document.getElementById('qty').value.trim();
                                const category = document.getElementById('category').value.trim();
                                const warehouse = document.getElementById('warehouse').value.trim();
                                const location = document.getElementById('location').value.trim();
                                const rate = document.getElementById('rate').value.trim();
                                const tax = document.getElementById('tax').value.trim();
                                const addButton = document.getElementById('addProductButton');

                                if (article && itemCode && qty) {
                                    addButton.disabled = false;
                                    addButton.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                                    addButton.classList.add('bg-black', 'text-white');
                                } else {
                                    addButton.disabled = true;
                                    addButton.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                                    addButton.classList.remove('bg-black', 'text-white');
                                }
                            }

                            const inputs = ['item-code-0', 'item-code', 'qty', 'category', 'warehouse', 'location', 'rate', 'tax'];
                            inputs.forEach(id => {
                                document.getElementById(id).addEventListener('input', validateForm);
                            });
                        });
                    </script>

                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-lg p-2 mt-5 shadow-md">
                {{-- <h1 class="text-md font-bold" >Uploaded Sheet Logs</h1> --}}
                <p class="font-semibold text-base">Uploaded Sheet Logs </p>
                <table class="w-full mt-5">
                    <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="text-left text-sm ">#</th>
                            <th class="text-left text-sm ">File Name</th>
                            {{-- <th class="text-left text-sm ">Type</th> --}}
                            <th class="text-left text-sm ">Status</th>
                            <th class="text-left text-sm ">Date & Time</th>
                            <th class="text-left text-sm ">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($sheets->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center">No data present</td>
                            </tr>
                        @else
                            @foreach ($sheets as $sheet)
                                <tr>
                                    <td class="text-sm">{{ $loop->iteration }}</td>
                                    <td class="text-sm">{{ $sheet->file_name }}</td>
                                    <td class="text-sm">{{ $sheet->status }}</td>
                                    <td class="text-sm">{{ $sheet->uploaded_at }}</td>
                                    <td class="hover:underline cursor-pointer text-sm"
                                        wire:click="downloadFile('{{ $sheet->file_path }}')">Download</td>
                                </tr>
                            @endforeach
                        @endif

                </table>
            </div>
            <script>
                window.addEventListener('single-message', event => {
                    // Set the message in the modal
                    document.getElementById('singleProductAdd').textContent = event.detail[0];

                    // Show the modal (you might need to use your specific modal's show method)
                    document.getElementById('singleProductModal').style.display = 'block';

                    // Optionally, hide the modal after a few seconds
                    setTimeout(() => {
                        document.getElementById('singleProductModal').style.display = 'none';
                    }, 5000);
                });
            </script>
        </div>

        @elseif($activeTab == 'tab2')
        <div wire:key="tab2" >
            <!-- Content for Tab 2 -->

            <div class="flex-grow md:ml-2 mt-2">
                <!-- Add new products form -->
                <div class="bg-white border border-gray-300 rounded-lg p-2">
                    <div
                        class="text-blue-700 font-semibold mb-2 flex flex-col md:flex-row justify-between items-center">
                        <a href="{{ route('products.exportProducts') }}"
                            class="bg-[#E2DFDF] text-xs text-black hover:bg-orange mb-5 py-1 lg:py-2 px-4 rounded-lg">
                            Download Stock Sheet
                        </a>
                        <form wire:submit.prevent="productUpdate" enctype="multipart/form-data" class="mt-2 md:mt-0">
                            <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                <input wire:model="updateFile"
                                    class="block w-full md:w-96 mb-5 p-1 text-[0.6rem] text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                    id="small_size" type="file" style="width: 100%;">
                                <div
                                    class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                    @if ($updateFile)
                                        <button
                                            class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                            type="submit" wire:loading.attr="disabled">
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

                {{-- @foreach ($sheets as $sheet)
                    {{ $sheet->file_name }}
                    <button wire:click="downloadFile('{{ $sheet->file_path }}')">Download</button>
                    {{ $sheet->user_id}}
                @endforeach --}}
            </div>
        </div>
        @elseif($activeTab == 'tab4')
        <div wire:key="tab4" >
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100">

                <livewire:dashboard.stock.stock-out  />
            </div>
        </div>
        @elseif($activeTab == 'tab5')
        <div wire:key="tab5" >
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">
                <livewire:dashboard.stock.deleted-stock />
            </div>
        </div>
        @endif



        </div>


    </div>

</div>

    <script>
        // Event listener to detect tab change and reinitialize dropdown
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', (message, component) => {
                if (window.Alpine) {
                    window.Alpine.initTree(document);
                }
                initFlowbite();
                initSelect2();
            });
            window.addEventListener('tab-changed', event => {
                if (window.Alpine) {
                    window.Alpine.initTree(document.body);
                }
            });
        });

        function initSelect2() {
            ['article', 'item-code', 'location', 'category', 'warehouse'].forEach(id => {
                $(`#${id}-select`).select2({
                    placeholder: id.charAt(0).toUpperCase() + id.slice(1).replace('-', ' '),
                    allowClear: true
                });
            });
        }


        // ... other existing scripts ...
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
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                const visibleProductCheckboxes = document.querySelectorAll(
                    '.product-checkbox:not([value="all"]):not([style*="display: none"])');

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
            console.log('Selected Products:', this.selectedProducts); // Add this line
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
        document.addEventListener('DOMContentLoaded', function() {
            $('#article-select').select2({
                placeholder: "Article",
                allowClear: true
            });

            $('#item-code-select').select2({
                placeholder: "Item Code",
                allowClear: true
            });

            $('#location-select').select2({
                placeholder: "Location",
                allowClear: true
            });

            $('#category-select').select2({
                placeholder: "Category",
                allowClear: true
            });

            $('#warehouse-select').select2({
                placeholder: "Warehouse",
                allowClear: true
            });

        });

        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', (message, component) => {
                $('#article-select').select2({
                    placeholder: "Article",
                    allowClear: true
                });

                $('#item-code-select').select2({
                    placeholder: "Item Code",
                    allowClear: true
                });

                $('#location-select').select2({
                    placeholder: "Location",
                    allowClear: true
                });

                $('#category-select').select2({
                    placeholder: "Category",
                    allowClear: true
                });

                $('#warehouse-select').select2({
                    placeholder: "Warehouse",
                    allowClear: true
                });


            });
        });
    </script>
