<div class="relative overflow-x-auto shadow-md sm:rounded-lg"
    x-cloak x-init="
        $watch('selectPage', value => selectPageUpdated(value));
        Livewire.on('pageChanged', () => {
            stockInPage = @entangle('stockInPage');
            selectPageUpdated(selectPage);
        });
    "
    x-data="{
        stockInPage: @entangle('stockInPage'),
        allStock: @entangle('allStock'),
        selectPage: false,
        selectAll: false,
        dropdownOpen: false,
        openDropdown: null,
        checked: [],
        deleteMultiple() {
            Livewire.emit('deleteMultiple', this.checked);
            this.checked = [];
        },
        moveMultipleStock() {
            Livewire.emit('moveMultipleStock', this.checked);
            this.checked = [];
        },
        selectPageUpdated(value) {
            if (value) {
                this.checked = this.stockInPage;
            } else {
                this.selectAll = false;
                this.checked = [];
            }
        },
        selectAllItems() {
            this.selectPage = true;
            this.checked = this.allStock;
        },
    }" wire:ignore.self>
    @if ($errorMessage)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" wire:key="error-{{ now() }}" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
        {{ $errorMessage }}
        @if ($errorData)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" wire:key="error-{{ now() }}" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">

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
    <div  x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" wire:key="successMessage-{{ now() }}" id="success-alert" class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <div class="ms-3 text-sm ">
            <span class="font-medium">Success:</span>  {{ $successMessage }}
        </div>
    </div>
    @endif
    <div>

         {{-- <button wire:click="exportData" class="btn btn-primary">Export Data</button> --}}
    </div>
    <div x-data="checkboxes()" x-init="initCheckboxes({{ $productsOut->pluck('id') }})" class="min-w-full overflow-auto">
        <div class="fixed-header-container">
            <div class="filters flex items-center mb-5 p-2 space-x-2 sticky top-0 bg-white z-10">
                <!-- ... (keep your existing filter code) ... -->
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" x-data="{ showSelected: false }">
                    <thead class="bg-gray-50 sticky top-0 whitespace-nowrap">
                        <tr>
                            @foreach ($columnDisplayNamesOut as $index => $columnName)
                                <th x-show="!showSelected" scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-300">
                                    {{ ucfirst($columnName) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($productsOut as $key => $product)
                        <tr>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $key + 1 }}</td>
                            @if ($product->product)
                                @foreach($product->product->details as $index => $detail)
                                    @if ($index < 3)
                                        <td class="px-2 py-2 border border-gray-300 whitespace-nowrap text-xs text-gray-800">{{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::lower($detail->column_value), 20) }}</td>
                                    @endif
                                @endforeach
                            @endif
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->item_code }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ ucfirst($product->product->category ?? null) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ ucfirst($product->product->warehouse ?? null) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->product ? ucfirst($product->product->location) : 'N/A' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->product->unit ?? null }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->product->qty ?? null }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->product->rate ?? null }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->qty_out }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->challan ? $product->challan->receiver : 'N/A' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">
                                {{ $product->challan ? $product->challan->challan_series . '-' . $product->challan->series_num : 'N/A' }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ ucfirst($product->out_method) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->created_at->format('j-m-Y') }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-800 border border-gray-300">{{ $product->created_at->format('h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    <div class="mt-4">
        {{ $productsOut->links() }}
    </div>
 </div>
 <script>
    document.addEventListener('livewire:load', function () {
        initializeSelect2();

        Livewire.hook('message.processed', (message, component) => {
            initializeSelect2();
        });
    });

    function initializeSelect2() {
        $('.js-example-basic-multiple').each(function() {
            let $this = $(this);
            let options = {
                placeholder: $this.data('placeholder'),
                allowClear: true
            };

            $this.select2(options);

            $this.on('change', function (e) {
                let data = $(this).val();
                let wireModel = $(this).attr('wire:model');
                if (wireModel) {
                    @this.set(wireModel, data);
                }
            });
        });
    }

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

        let isValid = false;

        if (selectedOption === 'category' && categoryValue !== 'null') {
            isValid = true;
        } else if (selectedOption === 'warehouse' && warehouseValue !== 'null') {
            isValid = true;
        } else if (selectedOption === 'location' && locationValue !== 'null') {
            isValid = true;
        }

        document.getElementById('confirm-button').disabled = !isValid;
        document.getElementById('move-quantity-input').disabled = !isValid;

        if (isValid) {
            validateQuantity();
        }
    }

    function validateQuantity() {
        const moveQtyInput = document.getElementById('move-quantity-input');
        const currentQty = parseInt(document.getElementById('current-qty').textContent);
        const moveQty = parseInt(moveQtyInput.value);

        if (moveQty > currentQty) {
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('confirm-button').disabled = true;
        } else {
            document.getElementById('error-message').style.display = 'none';
            document.getElementById('confirm-button').disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initFlowbite();
        console.log('DOM Loaded');
        Alpine.start();
    });

    document.addEventListener('livewire:update', function () {
        console.log('Livewire reloaded 21');
        initFlowbite();
    });

    document.addEventListener('wire:navigated', function () {
        console.log('Livewire navigated 21');
        initFlowbite();
    });

    function confirmDeletion() {
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
                Livewire.emit('deleteMultiple', this.checked);
                dropdownOpen = false;
            }
        });
    }

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
</script>
<style>
    .fixed-header-container {
        height: calc(100vh - 190px); /* Adjust this value based on your layout */
        overflow-y: hidden;
    }

    .table-container {
        overflow-y: auto;
        max-height: calc(100vh - 300px); /* Adjust this value based on your layout */
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
    .fixed-header-container {
        height: calc(100vh - 200px); /* Adjust this value based on your layout */
        overflow-y: auto;
    }


    th, td {
        min-width: 100px; /* Adjust this value as needed */
        max-width: 200px; /* Adjust this value as needed */
        overflow: hidden;
        text-overflow: ellipsis;
    }

</style>
