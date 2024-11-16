<div class="relative overflow-x-auto shadow-md sm:rounded-lg" x-cloak
x-data="{
    showSelected: false,
    allItemIds: @entangle('allItemIds'),
    selectedProducts: [],
    selectedCount: 0,
    allChecked: false,
    selectPage: false,
    selectAll: false,
    successMessage: '',
    errorMessage: '',
    allStock: [], // This should be populated with all available items
    toggleAll() {
        this.allChecked = !this.allChecked;
        this.selectedProducts = this.allChecked
            ? Array.from(document.querySelectorAll('input[type=checkbox][data-id]')).map(el => parseInt(el.dataset.id))
            : [];
        this.updateSelectedCount();
        this.selectPage = this.allChecked;
    },
    toggleProduct(id) {
        const index = this.selectedProducts.indexOf(id);
        if (index === -1) {
            this.selectedProducts.push(id);
        } else {
            this.selectedProducts.splice(index, 1);
        }
        this.updateSelectedCount();
        this.allChecked = this.selectedProducts.length === document.querySelectorAll('input[type=checkbox][data-id]').length;
        this.selectPage = this.allChecked;
    },
    updateSelectedCount() {
        this.selectedCount = this.selectedProducts.length;
        this.showSelected = this.selectedCount > 0;
    },
    resetSelection() {
        this.selectedProducts = [];
        this.allChecked = false;
        this.selectPage = false;
        this.selectAll = false;
        this.updateSelectedCount();
        this.showSelected = false;
    },
    selectAllItems() {
        this.selectAll = true;
        this.selectedProducts = this.allItemIds;
        this.updateSelectedCount();
    },
    showSuccessMessage(message) {
        this.successMessage = message;
        setTimeout(() => this.successMessage = '', 3000);
    },
    showErrorMessage(message) {
        this.errorMessage = message;
        setTimeout(() => this.errorMessage = '', 3000);
    },
    deleteMultiple() {
            // Call the SwalFire confirm modal
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to delete the selected items?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('deleteMultiple', this.selectedProducts);
                    this.selectedProducts = [];
                    this.selectAll = false;
                    this.selectPage = false;
                    this.updateSelectedCount();
                    this.showSelected = false;

                }
            });
        },
    moveMultipleStock() {
        Livewire.emit('openAdditiveModal', this.selectedProducts);
        this.selectedProducts = [];
        this.selectAll = false;
        this.selectPage = false;
        this.updateSelectedCount();
        this.showSelected = false;
        this.allChecked = false;
    },
}"
x-init="
updateSelectedCount();
Livewire.on('resetSelection', () => resetSelection());
Livewire.on('dataUpdated', () => {
    selectedProducts = selectedProducts.filter(id => filteredItemIds.includes(id));
    updateSelectedCount();
    allChecked = selectedProducts.length === filteredItemIds.length;
});
"
wire:ignore.self>

    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 border border-green-400  text-gray-800 px-4 py-3 rounded relative"
                id="successMessage"></p>
        </div>
    </div>
    <div id="errorModal" style="display: none;">
        <div
            class="modal-content flex items-end bg-red-100 border border-red-400 text-gray-800 px-4 py-3 rounded relative">
            <p class="mt-3 " id="errorMessage">\
            </p>

        </div>

    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">




        <div class="fixed-header-container">
            <div class="filters flex items-center mb-5 p-2 space-x-2 sticky top-0 bg-white ">
                <div>
                    <h1>Filters</h1>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div>
                        <select id="article-select" wire:model="article" style="width: 130px" data-placeholder="Article"
                            multiple
                            class="js-example-basic-multiple bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option class="text-[0.6rem]" disabled value="">Article</option>
                            @foreach ($articles as $article)
                                <option class="text-[0.6rem]" value="{{ $article }}">{{ $article }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select id="item-code-select" wire:model="item_code" style="width: 130px"
                            data-placeholder="Item Code" multiple
                            class="js-example-basic-multiple bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option class="text-[0.6rem]" disabled value="">Item Code</option>
                            @foreach ($item_codes as $item_code)
                                <option class="text-[0.6rem]" value="{{ $item_code }}">{{ $item_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select id="location-select" wire:model="location" style="width: 130px"
                            data-placeholder="Location" multiple
                            class="js-example-basic-multiple bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option class="text-[0.6rem]" disabled value=""> Location</option>
                            @foreach ($locations as $location)
                                <option class="text-[0.6rem]" value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select id="category-select" wire:model="category" style="width: 130px"
                            data-placeholder="Category" multiple
                            class="js-example-basic-multiple bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option class="text-[0.6rem]" disabled value=""> Category</option>
                            @foreach ($categories as $category)
                                <option class="text-[0.6rem]" value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select id="warehouse-select" wire:model="warehouse" style="width: 130px"
                            data-placeholder="Warehouse" multiple
                            class="js-example-basic-multiple bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option class="text-[0.6rem]" disabled value=""> Warehouse</option>
                            @foreach ($warehouses as $warehouse)
                                <option class="text-[0.6rem]" value="{{ $warehouse }}">{{ $warehouse }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Dynamic Filters based on ColumnDisplayNames -->
                    @foreach ($dynamicFilters as $columnName => $filterValues)
                        <div class="filters">
                            {{-- <label for="{{ strtolower($columnName) }}-select">{{ $columnName }}</label> --}}
                            <select id="{{ strtolower($columnName) }}-select" wire:model="filters.{{ $columnName }}"
                                style="width: 130px" multiple
                                class="js-example-basic-multiple bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                data-placeholder="{{ $columnName }}">
                                <option class="text-[0.6rem]" disabled value="">Select {{ $columnName }}
                                </option>
                                @foreach ($filterValues as $value)
                                    <option class="text-[0.6rem]" value="{{ $value }}">{{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- <div class="col-md-10 mb-3" x-transition>
                <div x-show="selectAll && selectPage">
                    You are currently selecting <strong x-text="checked.length"></strong> items.
                </div>
                <div x-show="selectPage && !selectAll">
                    You have selected <strong x-text="checked.length"></strong> items, Do you want to Select All
                    <strong x-text="allStock.length"></strong> items?
                    <a href="#" @click="selectAllItems" class="ml-2">Select All</a>
                </div>
            </div> --}}
            <div x-show="(selectPage || selectAll) && selectedCount > 0" class="bg-gray-100 border-t border-b border-gray-500 text-black text-sm px-4 py-3 my-2">
                <template x-if="selectedCount < allItemIds.length">
                    <span>
                        You have selected <strong x-text="selectedCount"></strong> items. Do you want to Select All
                        <strong x-text="allItemIds.length"></strong> items?
                        <a href="#" @click.prevent="selectAllItems" class="ml-2 text-gray-600 hover:text-gray-800 underline">Select All</a>
                    </span>
                </template>
                <template x-if="selectedCount === allItemIds.length">
                    <span>
                        All <strong x-text="selectedCount"></strong> items are selected.
                        <a href="#" @click.prevent="resetSelection" class="ml-2 text-gray-600 hover:text-gray-800 underline">Unselect All</a>
                    </span>
                </template>
            </div>
            <!-- Table -->
            <div x-data="checkboxes()" x-init="initCheckboxes({{ $availableStock->pluck('id') }})" class="table-container text-black">
                <table class="w-full">
                    <thead class="sticky top-0">
                        <th class="va-b px-2 py-2 text-xs border border-gray-300 pl-0">
                            <input type="checkbox" @click="toggleAll"
                                        :checked="allChecked"
                                class="product-checkbox w-4 h-4 border ml-2  text-purple-600 bg-gray-100 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </th>
                            @foreach ($MergedColumnDisplayNames as $index => $columnName)
                                @if (!empty($columnName))
                                    <th x-show="selectedCount === 0" class="va-b px-2 py-2 text-xs border border-gray-300 ">
                                        {{ ucfirst($columnName) }}
                                    </th>
                                @endif
                            @endforeach
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Item Code</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Warehouse</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Category</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Location</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Unit</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">
                                <div class ="flex pr-2" wire:click.stop="sortBy('qty')">
                                    Qty
                                    <span style="margin-left: 5px;" class="text-[0.6rem] grid grid-rows-1 grid-cols-1">
                                        @if($sortField === 'qty')
                                            @if($sortDirection === 'asc')
                                                <div class="flex flex-col">
                                                    <span class="text-black leading-none">&#9650;</span>
                                                    <span class="text-gray-500 leading-none cursor-pointer">&#9660;</span>
                                                </div>
                                            @else
                                                <div class="flex flex-col">
                                                    <span class="text-gray-500 leading-none cursor-pointer">&#9650;</span>
                                                    <span class="text-black leading-none">&#9660;</span>
                                                </div>
                                            @endif
                                        @else
                                            <div class="flex flex-col">
                                                <span class="text-gray-500 leading-none cursor-pointer">&#9650;</span>
                                                <span class="text-gray-500 leading-none cursor-pointer">&#9660;</span>
                                            </div>
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th x-show="selectedCount === 0" data-tooltip-target="tooltip-right" data-tooltip-placement="right"
                                class="va-b px-1.5 py-2 text-left text-xs border-t border-gray-300 items-end flex">Price
                                {{-- <button data-tooltip-target="tooltip-right" data-tooltip-placement="right" type="button" class="ms-3 mb-2 md:mb-0 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Tooltip right</button> --}}
                                <svg class="h-3.5 text-gray-800 dark:text-white cursor-pointer" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <div id="tooltip-right" role="tooltip"
                                    class="absolute z-10 text-[0.6rem] invisible inline-block px-3 py-2 text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Inclusive of all taxes
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </th>

                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Tax(%)</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Date</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Time</th>
                            <th x-show="selectedCount === 0" class="va-b px-1.5 py-2 text-left text-xs border border-gray-300">Action</th>


                        <th x-cloak x-show="selectedCount > 0" class="flex" x-transition>
                            <div class="flex">

                                <div>
                                    {{-- <button x-show="selectedCount > 0" @click="dropdownOpen = !dropdownOpen"
                                        class="bg-gray-800 flex items-center m-1 p-1 px-2 rounded text-white text-xs">
                                        Selected (<span x-text="selectedCount"></span>)
                                        <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    </button> --}}
                                    <button id="dropdownMenuIconHorizontalButton" data-dropdown-toggle="dropdownDotsHorizontal" class="inline-flex bg-gray-400 items-center p-2 text-sm font-medium text-center text-gray-900  rounded-lg hover:bg-gray-100   dark:text-white   dark:bg-gray-800 dark:hover:bg-gray-700 " type="button">
                                        Selected (<span x-text="selectedCount"></span>)
                                        <svg class="w-5 h-5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                            <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                                        </svg>
                                    </button>
                                    <div id="dropdownDotsHorizontal" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600">
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200 text-left shadow" aria-labelledby="dropdownMenuIconHorizontalButton">
                                            <li>
                                                <li  class="px-3 py-1 hover:bg-gray-200 cursor-pointer"  @click="deleteMultiple();">
                                                    Delete
                                                </li>
                                                <li  class="px-3 py-1 hover:bg-gray-200 cursor-pointer border-b"  @click="moveMultipleStock();" >
                                                    Move Stock
                                                </li>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </th>

                    </thead>
                    <tbody>
                        {{-- @dd($availableStock) --}}
                        @foreach ($availableStock as $key => $stock)
                            {{-- @dump($stock->id) --}}
                            <tr>
                                <td class="border border-gray-300 px-2 text-center">
                                    <input type="checkbox" :checked="selectedProducts.includes({{ $stock->id }})"
                                    @click="toggleProduct({{ $stock->id }})"
                                    data-id="{{ $stock->id }}"
                                        class="w-4 h-4 text-purple-600  rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </td>
                                @foreach ($stock->details as $index => $detail)
                                    @php
                                        $detail = (object) $detail;
                                    @endphp
                                    @if (!empty($MergedColumnDisplayNames[$index]))
                                        <td class="va-b px-2 py-2 text-xs border border-gray-300">
                                            @if ($index <= 2)
                                                {{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::lower($detail->column_value), 20) }}
                                            @elseif ($index >= 3)
                                                {{ $detail->column_value }}
                                            @endif
                                        </td>
                                    @endif
                                    {{-- @if (!empty($InvoiceColumnDisplayNames[$index]))
                                        <td class="va-b px-2 py-2 text-xs border border-gray-300">
                                            @if ($index >= 7 && $index <= 11)
                                                {{ $detail->column_value }}
                                            @endif
                                        </td>
                                    @endif --}}
                                @endforeach
                                {{-- @if ($index < 3)
                                    <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800">
                                        {{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::lower($detail->column_value), 20) }}
                                    </td>
                                @endif --}}

                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->item_code }}</td>

                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->warehouse ?? '' }}</td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->category ?? '' }}</td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ ucfirst($stock->location) }}</td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ ucfirst($stock->unit) }}</td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->qty }}</td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->rate }}</td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->tax }}</td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->created_at ? $stock->created_at->format('j-m-Y') : 'N/A' }}
                                </td>
                                <td
                                    class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $stock->created_at ? $stock->created_at->format('h:i A') : 'N/A' }}
                                </td>
                                <td x-show="selectedCount === 0" class="px-2  text-[0.6rem] border-2 border-gray-300"
                                    x-data="{ open: false }">
                                    {{-- @dump($key) --}}
                                    <button id="dropdownDefaultButton-{{ $key }}"
                                        data-dropdown-toggle="dropdown-{{ $key }}"
                                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-2 py-1 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                        type="button">Select <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg></button>
                                    <!-- Dropdown menu -->
                                    <div id="dropdown-{{ $key }}"
                                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">

                                        <ul class=" text-[0.6rem] border-2 border-gray-300 text-gray-700 dark:text-gray-200"
                                            aria-labelledby="dropdownDefaultButton-{{ $key }}">
                                            <li>
                                                <a href="javascript:void(0)"
                                                   class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white"
                                                   @click="openDropdown = null; $wire.selectChallanSeries('{{ json_encode($stock) ?? '' }}').then(() => setTimeout(() => $dispatch('open-edit-modal'), 300))">
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)"
                                                   @click="openDropdown = null; confirmDelete({{ $stock->id }})"
                                                   class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">
                                                    Delete
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                href="javascript:void(0);"
                                                        @click="openDropdown = null; showModal = true"
                                                        wire:click="singleStockMove('{{ json_encode($stock) }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Move</a>

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

<div class="mt-4">
    {{ $availableStock->links('livewire::tailwind') }}
</div>
<!-- Single Stock Edit -->
<div x-data="{ showEditModal: false }" x-on:open-edit-modal.window="showEditModal = true"
    x-on:close-edit-modal.window="showEditModal = false">
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="showEditModal = false" type="button"
                        class="text-gray-400 bg-white rounded-md hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Close</span>
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            Edit Product
                        </h3>
                        <div class="gap-3 grid grid-cols-2 mt-2">
                            @foreach ($panelUserColumnDisplayNames as $key => $columnName)
                                @if (!empty($columnName))
                                    <div class="mb-4">
                                        <label for="{{ $key }}"
                                            class="block text-sm font-medium text-gray-700">{{ $columnName }}</label>
                                        <input type="text" id="{{ $key }}"
                                            wire:model.defer="editChallanRequest.details.{{ $key }}.column_value"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                @endif
                            @endforeach
                            <div class="mb-4">
                                <label for="item-code" class="block text-sm font-medium text-gray-700">Item
                                    Code</label>
                                <input type="text" id="item-code" wire:model.defer="editChallanRequest.item_code"
                                    disabled
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-100 cursor-not-allowed">
                            </div>
                            <div class="mb-4">
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <input type="text" id="category" wire:model.defer="editChallanRequest.category"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="warehouse"
                                    class="block text-sm font-medium text-gray-700">Warehouse</label>
                                <input type="text" id="warehouse" wire:model.defer="editChallanRequest.warehouse"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input type="text" id="location" wire:model.defer="editChallanRequest.location"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                                <input type="text" id="unit" wire:model.defer="editChallanRequest.unit"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="rate" class="block text-sm font-medium text-gray-700">Rate</label>
                                <input type="number" id="rate" wire:model.defer="editChallanRequest.rate"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="tax" class="block text-sm font-medium text-gray-700">Tax(%)</label>
                                <input type="number" id="tax" wire:model.defer="editChallanRequest.tax"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="qty" class="block text-sm font-medium text-gray-700">Qty</label>
                                <input type="number" id="qty" wire:model.defer="editChallanRequest.qty"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="editProduct"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update
                    </button>
                    <button type="button" @click="showEditModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- @dump($selectedIds) --}}



{{-- Single Stock Movement --}}
@if ($singleStockModal == true)
    <!-- Modal for Moving Stock -->
    <div id="move-stock-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-10">
        <div class="bg-white rounded-lg p-6 w-11/12 md:w-1/2 lg:w-1/3 text-center">
            <div class="mb-4">
                <h1 class="text-lg text-black pb-3 border-b border-gray-400">Move Category and Warehouse</h1>

                <div class="mt-5">
                    <label class="inline-flex items-center">
                        <input type="radio" name="change-option" value="category" onclick="toggleOptions()"
                            checked>
                        <span class="ml-2">Change Category</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="radio" name="change-option" value="warehouse" onclick="toggleOptions()">
                        <span class="ml-2">Change Warehouse</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="radio" name="change-option" value="location" onclick="toggleOptions()">
                        <span class="ml-2">Change Location</span>
                    </label>
                </div>

                <div id="category-option" class="relative w-full min-w-[200px] h-10 mt-5 flex items-center">
                    <label for="category-select" class="w-1/3">Select Category:</label>
                    <select id="category-select" wire:model.defer="moveCategories"
                        class="js-example-basic-single w-2/3" name="category" onchange="validateForm()">
                        <option value="null">Select</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="warehouse-option" class="relative w-full min-w-[200px] h-10 mt-5 flex items-center"
                    style="display: none;">
                    <label for="warehouse-select" class="w-1/3">Select Warehouse:</label>
                    <select id="warehouse-select" wire:model.defer="moveWarehouses"
                        class="js-example-basic-single w-2/3" name="warehouse" onchange="validateForm()">
                        <option value="null">Select</option>
                        @foreach ($warehouses as $ware)
                            <option value="{{ $ware }}">{{ ucfirst($ware) }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="location-option" class="relative w-full min-w-[200px] h-10 mt-5 flex items-center"
                    style="display: none;">
                    <label for="location-select" class="w-1/3">Select Location:</label>
                    <select id="location-select" wire:model.defer="moveLocations"
                        class="js-example-basic-single w-2/3" name="location" onchange="validateForm()">
                        <option value="null">Select</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative w-full min-w-[200px] h-10 mt-5 flex items-center">
                    <label class="w-1/3">Current Quantity:</label>
                    <p id="current-qty">{{ $singleStockMoveData['qty'] ?? 'N/A' }}</p>
                </div>

                <div class="relative w-full min-w-[200px] h-10 mt-5 flex items-center">
                    <label for="move-quantity-input" class="w-1/3">Move Quantity:</label>
                    <input wire:model.defer="moveQty" id="move-quantity-input" type="number" class="w-2/3"
                        oninput="validateQuantity()">
                </div>

                <div id="error-message" class="text-red-500 mt-2" style="display: none;">
                    Quantity cannot be greater than the available stock.
                </div>

                <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500 mt-5">
                    <button x-on:click="openSearchModal = false" wire:click="closeModal"
                        class="ml-4 px-4 py-2.5 font-sans text-xs text-red-500 transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        Cancel
                    </button>
                    <button id="confirm-button" wire:click="singleStockMoveConfirm"
                        class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        disabled>
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif



<livewire:dashboard.stock.stock-movement :selectedIds="$selectedIds" />
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('editModal', () => ({
            showEditModal: false,
            init() {
                this.$watch('showEditModal', value => {
                    if (value) {
                        document.body.classList.add('overflow-hidden');
                    } else {
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            }
        }));
    });

    function toggleOptions() {
        const selectedOption = document.querySelector('input[name="change-option"]:checked').value;
        document.getElementById('category-option').style.display = selectedOption === 'category' ? 'flex' : 'none';
        document.getElementById('warehouse-option').style.display = selectedOption === 'warehouse' ? 'flex' : 'none';
        document.getElementById('location-option').style.display = selectedOption === 'location' ? 'flex' : 'none';
        validateForm();
    }

    function validateForm() {
        const selectedOption = document.querySelector('input[name="change-option"]:checked').value;
        const categoryValue = document.getElementById('category-select').value;
        const warehouseValue = document.getElementById('warehouse-select').value;
        const locationValue = document.getElementById('location-select').value;
        const moveQtyInput = document.getElementById('move-quantity-input');

        let isValid = false;

        if (selectedOption === 'category' && categoryValue !== 'null') {
            isValid = true;
        } else if (selectedOption === 'warehouse' && warehouseValue !== 'null') {
            isValid = true;
        } else if (selectedOption === 'location' && locationValue !== 'null') {
            isValid = true;
        }

        moveQtyInput.disabled = !isValid;

        if (isValid && moveQtyInput.value) {
            validateQuantity();
        } else {
            document.getElementById('confirm-button').disabled = true;
        }
    }

    function validateQuantity() {
        const moveQtyInput = document.getElementById('move-quantity-input');
        const currentQty = parseInt(document.getElementById('current-qty').textContent);
        const moveQty = parseInt(moveQtyInput.value);

        if (isNaN(moveQty) || moveQty <= 0) {
            document.getElementById('error-message').textContent = 'Please enter a valid quantity.';
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('confirm-button').disabled = true;
        } else if (moveQty > currentQty) {
            document.getElementById('error-message').textContent =
                'Quantity cannot be greater than the available stock.';
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('confirm-button').disabled = true;
        } else {
            document.getElementById('error-message').style.display = 'none';
            document.getElementById('confirm-button').disabled = false;
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="change-option"]').forEach(radio => {
            radio.addEventListener('change', toggleOptions);
        });

        document.getElementById('category-select').addEventListener('change', validateForm);
        document.getElementById('warehouse-select').addEventListener('change', validateForm);
        document.getElementById('location-select').addEventListener('change', validateForm);
        document.getElementById('move-quantity-input').addEventListener('input', validateQuantity);

        // Initialize Flowbite on page load
        initFlowbite();
        console.log('DOM Loaded arstg');
        Alpine.start();

        initializeDynamicSelects();

        // Initialize select2 for specific select inputs
        $('#article-select').select2({
            placeholder: "Article",
            allowClear: true
        });

        $('#item-code-select').select2({
            placeholder: "Item Code",
            allowClear: true
        });

        $('#location-select').select2({
            placeholder: " Location",
            allowClear: true
        });

        $('#category-select').select2({
            placeholder: " Category",
            allowClear: true
        });

        $('#warehouse-select').select2({
            placeholder: " Warehouse",
            allowClear: true
        });
    });

    document.addEventListener('livewire:load', function() {
        Livewire.hook('message.processed', (message, component) => {
            // Initialize Flowbite and Select2 after Livewire updates
            initFlowbite();
            console.log('processed');
            Alpine.start();
            initializeDynamicSelects();
            $('.js-example-basic-multiple').select2();

            // Listen for changes and update Livewire properties
            $('.js-example-basic-multiple').on('change', function(e) {
                let data = $(this).val();
                @this.set($(this).attr('wire:model'), data);
            });

            // Reinitialize the confirmDelete function
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    confirmDelete(id);
                });
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Loaded wesdf');
        initializeDynamicSelects();
        validateForm();
        initFlowbite();
    });

    document.addEventListener('livewire:update', function() {
        console.log('Livewire reloaded 21');
        validateForm();
        initFlowbite();
    });

    document.addEventListener('wire:navigated', function() {
        console.log('Livewire navigated 21');
        validateForm();
        initFlowbite();
    });
    // New function to initialize dynamic selects
    function initializeDynamicSelects() {
        $('.js-example-basic-multiple').each(function() {
            let selectElement = $(this);
            let placeholderText = selectElement.attr('data-placeholder');

            // Check if Select2 is not already initialized
            if (!selectElement.hasClass("select2-hidden-accessible")) {
                selectElement.select2({
                    placeholder: placeholderText,
                    allowClear: true
                });

                // Listen for changes and update Livewire properties
                selectElement.on('change', function(e) {
                    let data = $(this).val();
                    @this.set($(this).attr('wire:model'), data);
                });
            }
        });
    }

    // Button to delete multiple records
    // function confirmDeletion() {
    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: "You won't be able to revert this!",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Yes, delete it!'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             Livewire.emit('deleteMultiple', this.checked);
    //             dropdownOpen = false;
    //         }
    //     });
    // }

    // Delete single record
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('deleteProduct', id);
            }
        });
    }

    window.addEventListener('show-error-message', event => {
        document.getElementById('errorMessage').innerHTML = event.detail.message;
        document.getElementById('errorModal').style.display = 'block';

        setTimeout(() => {
            document.getElementById('errorModal').style.display = 'none';
        }, 10000);
    });

    window.addEventListener('show-success-message', event => {
        document.getElementById('successMessage').innerHTML = event.detail.message;
        document.getElementById('successModal').style.display = 'block';

        setTimeout(() => {
            document.getElementById('successModal').style.display = 'none';
        }, 10000);
    });



    document.addEventListener('livewire:load', function() {
        Livewire.hook('message.processed', (message, component) => {
            // Initialize select2 for all static select inputs
            console.log('processed 2');
            if (console.log('processed 2')) {
                // Re-apply styles
                const style = document.createElement('style');
                style.textContent = `
                    .fixed-header-container {
                        height: calc(100vh - 190px);
                        overflow-y: hidden;
                    }

                    .table-container {
                        overflow-y: auto;
                        max-height: calc(100vh - 300px);
                    }

                    thead {
                        position: sticky;
                        top: 0;
                        z-index: 1;
                        background-color: white;
                    }

                    tbody {
                        overflow-y: auto;
                    }
                `;
                document.head.appendChild(style);
            }
            $('#article-select').select2({
                placeholder: "Article",
                allowClear: true,
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

            // Initialize select2 for dynamic select inputs
            $('.js-example-basic-multiple').each(function() {
                let selectElement = $(this);
                let placeholderText = selectElement.attr(
                    'data-placeholder'); // Use data-placeholder attribute for dynamic filters

                selectElement.select2({
                    placeholder: placeholderText, // Set the dynamic placeholder
                    allowClear: true
                });

                // Listen for changes and update Livewire properties
                selectElement.on('change', function(e) {
                    let data = $(this).val();
                    @this.set($(this).attr('wire:model'), data);
                });
            });
        });
    });
</script>
<style>
    .fixed-header-container {
        height: calc(100vh - 190px);
        /* Adjust this value based on your layout */
        overflow-y: hidden;
    }

    .table-container {
        overflow-y: auto;
        max-height: calc(100vh - 300px);
        /* Adjust this value based on your layout */
    }

    thead {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: white;
    }

    th,
    td {
        min-width: 0px !important;
    }

    tbody {
        overflow-y: auto;
    }

    function initializeAlpine() {
        Alpine.data('checkboxes', () => ({
            selectedProducts: [],
            currentPageIds: [],
            showSelected: false,

            initCheckboxes(ids) {
                this.currentPageIds = ids;
            },

            get allChecked() {
                return this.currentPageIds.length > 0 && this.currentPageIds.every(id => this.selectedProducts.includes(id));
            },

            get selectedCount() {
                return this.selectedProducts.length;
            },

            toggleAll() {
                if (this.allChecked) {
                    this.selectedProducts = this.selectedProducts.filter(id => !this.currentPageIds.includes(id));
                } else {
                    this.selectedProducts = [...new Set([...this.selectedProducts, ...this.currentPageIds])];
                }
                this.updateAllChecked();
                this.updateShowSelected();
            },

            updateAllChecked() {
                this.allChecked = this.currentPageIds.length > 0 && this.currentPageIds.every(id => this.selectedProducts.includes(id));
                this.updateShowSelected();
            },

            updateShowSelected() {
                this.showSelected = this.selectedProducts.length > 0;
            },

            toggleProduct(productId) {
                const index = this.selectedProducts.indexOf(productId);
                if (index === -1) {
                    this.selectedProducts.push(productId);
                    console.log(this.selectedProducts);
                } else {
                    this.selectedProducts.splice(index, 1);
                    console.log(this.selectedProducts);
                }
                this.updateAllChecked();
            }
        }));

        window.addEventListener('page-updated', event => {
            Alpine.find(document.querySelector('[x-data="checkboxes()"]')).initCheckboxes(event.detail.ids);
        });

        function uncheckAllCheckboxes() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    }
</style>
