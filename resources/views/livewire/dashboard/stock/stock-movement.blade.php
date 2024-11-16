<div
    >

    @if($showConfirmationModal)
    <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Select an option to move the Stock
                    </h3>
                    <div class="mt-2">
                        <div class="flex flex-col space-y-2">
                            <button wire:click="selectOption('category')" class="hover:bg-orange textblack border border-gray-400 text-black py-1.5 px-4 rounded">
                                Category
                            </button>
                            <button wire:click="selectOption('warehouse')" class="hover:bg-orange textblack border border-gray-400 text-black py-1.5 px-4 rounded">
                                Warehouse
                            </button>
                            <button wire:click="selectOption('location')" class="hover:bg-orange textblack border border-gray-400 text-black py-1.5 px-4 rounded">
                                Location
                            </button>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="cancelConfirmation" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div x-data="{ isOpen: @entangle('isOpen') }" x-show="isOpen"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60">
        <div class="bg-white rounded shadow-lg w-full h-full flex flex-col">
            <div class="p-6 flex-none">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-black text-xl">Bulk Stock Movement</h1>
                    <div>
                        <button type="submit" form="updateQuantitiesForm" class="bg-gray-800 text-white px-4 py-2 rounded text-xs">Move</button>
                        <button type="button" x-on:click="isOpen = false" wire:click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded text-xs">Cancel</button>
                    </div>
                </div>
            </div>
            <div id="errorModal" style="display: none;">
                <div
                    class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative">
                    <p class="mt-3" id="errorMessage"></p>

                </div>
            </div>

            <div class="flex-grow overflow-auto px-6 pb-6">
                <form id="updateQuantitiesForm" wire:submit.prevent="updateQuantities">
                    <table class="min-w-full divide-y divide-gray-200" style="table-layout: fixed;">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="va-b px-1.5 py-2 text-left text-xs border border-gray-300 text-black">Article</th>
                                <th class="va-b px-1.5 py-2 text-left text-xs border border-gray-300 text-black">Item Code</th>
                                <th class="va-b px-1.5 py-2 text-left text-xs border border-gray-300 text-black">Unit</th>
                                <th class="va-b px-1.5 py-2 text-left text-xs border border-gray-300 text-black"
                                    x-data="{
                                        selectedNewOption: '',
                                        showMessage: false,
                                        errorMessage: '',
                                        currentOption: '{{ strtolower($selectedOption) }}',
                                        availableOptions: {{ json_encode(array_map('strtolower', $availableOptions)) }},

                                        handleChange(event) {
                                            this.selectedNewOption = event.target.value.toLowerCase();
                                            this.showMessage = false;
                                            this.errorMessage = '';

                                            if (this.selectedNewOption === '') {
                                                return;
                                            }

                                            // if (this.availableOptions.includes(this.selectedNewOption)) {
                                            //     this.errorMessage = 'You cannot move stock to an available location or warehouse.';
                                            //     this.showMessage = true;
                                            // } else {
                                            //     this.errorMessage = 'All the quantity will move to the selected option.';
                                            //     this.showMessage = true;
                                            // }

                                            console.log('Selected:', this.selectedNewOption, 'Message:', this.errorMessage, 'Available Options:', this.availableOptions);
                                        }
                                    }"
                                    x-init="
                                        $nextTick(() => {
                                            console.log('Initial availableOptions:', availableOptions);
                                        });
                                    "
                                >
                                    <!-- Displaying the selected option in uppercase -->
                                    {{ ucfirst($selectedOption) }}

                                    <!-- Dropdown Menu for available options -->
                                    <select
                                        class="ml-2 p-2 mb-6 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        x-on:change="handleChange($event)"
                                        wire:model="selectedNewOption"
                                    >
                                        <option value="">Select an option</option>
                                        @foreach($newOptions as $option)
                                            <option value="{{ strtolower($option) }}">{{ ucfirst($option) }}</option>
                                        @endforeach
                                    </select>
                                    <div x-show="showMessage" x-text="errorMessage" class="text-red-500 mt-2"></div>


                                    <!-- Message display -->
                                    <div x-show="showMessage" x-text="errorMessage"
                                        :class="{'text-red-500': errorMessage.includes('cannot move'), 'text-blue-500': !errorMessage.includes('cannot move')}"
                                        class="mt-1 text-sm">
                                    </div>

                                    <!-- Comma-separated list of options -->
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ implode(', ', array_map('ucfirst', $availableOptions)) }}
                                    </div>
                                </th>


                                <th class="va-b px-1.5 py-2 text-left text-xs border border-gray-300 text-black">Available Qty</th>
                                <th class="va-b px-1.5 py-2 text-left text-xs border border-gray-300 text-black relative group">
                                    Move Qty
                                    <span class="ml-1 cursor-help">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </span>
                                    <div class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 z-10 mt-1 -ml-2">
                                        If left blank, the whole quantity will move
                                    </div>
                                </th>
                                {{-- <th class="va-b px-1.5 py-2 text-left text-xs border border-gray-300 text-black">New {{ ucfirst($selectedOption) }}</th> --}}
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $key => $product)
                            <tr>
                                <td class="va-b px-2 py-2 text-xs border border-gray-300">
                                    {{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::upper($product->details[0]->column_value ?? ''), 20) }}
                                </td>
                                <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->item_code }}</td>
                                <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ ucfirst($product->unit) }}</td>
                                <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">
                                    {{ $product->$selectedOption }}
                                </td>
                                <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300">{{ $product->qty }}</td>
                                <td class="px-1 py-1 whitespace-nowrap text-[11px] text-gray-800 border border-gray-300 w-20">
                                    <input type="number" wire:model.defer="newQuantities.{{ $key }}"
                                           wire:change="updateQuantity({{ $product->id }}, $event.target.value)"
                                           x-data="{ maxQty: {{ $product->qty }} }"
                                           x-on:input="if ($event.target.value > maxQty) $event.target.value = maxQty"
                                           class="bg-gray-300 border-0 h-7 w-24">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
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
</div>

