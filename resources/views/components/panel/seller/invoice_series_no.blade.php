<div class="max-w-full mb-4 border-b border-gray-200 dark:border-gray-700  rounded-lg  dark:bg-gray-800">
    <!-- <div class=" mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800"> -->
        @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400"
             role="alert">
            <span class="font-medium">Success:</span> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
             role="alert">
            <span class="font-medium">Error:</span> {{ session('error') }}
        </div>
    @endif
    <div id="alert-container" class="hidden">
        <div id="success-alert" class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs hidden"></div>
        <div id="error-alert" class="modal-content items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs hidden"></div>
    </div>
    <!-- <div class=" mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800"> -->
        <div id="myTabContent" class="rounded-lg dark:bg-gray-800 p-2" wire:ignore>
            <div class="grid gap-4">
                <div>
                    <label for="series_number" class="block text-sm font-semibold text-black">Prefix Number</label>
                    <input type="text" wire:model.defer="sellerInvoiceSeriesData.series_number" id="series_number" maxlength="20" name="series_number" class="mt-1 p-2 text-sm h-10 block w-full text-black rounded-md dark:bg-gray-700 dark:text-black focus:" placeholder="Series Number" required>
                    <div id="series_number_error" class="text-red-500 text-sm mt-1"></div>
                </div>
                <div class="flex space-x-4">
                    <div class="flex-1">
                        <label for="valid_from" class="block text-sm font-semibold text-black">
                            Valid From <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 p-1.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input id="valid_from" wire:model.defer="sellerInvoiceSeriesData.valid_from" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date" required>
                        </div>
                        <div id="valid_from_error" class="text-red-500 text-sm mt-1"></div>
                    </div>


                    <div class="flex-1">
                        <label for="valid_till" class="block text-sm font-semibold text-black">
                            Valid Till <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 p-1.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>

                            <input id="valid_till" wire:model.defer="sellerInvoiceSeriesData.valid_till" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date" required>                </div>
                    <div id="valid_till_error" class="text-red-500 text-sm mt-1"></div>
                </div>
                </div>

                <div class="relative">
                    <label for="receiver_user_id" class="block text-sm font-medium text-black">Assign Buyer</label>
                    <select id="select" wire:model.defer="sellerInvoiceSeriesData.assigned_to_b_id" class="mt-1 p-2 text-xs pr-8 h-10 block w-full rounded-md bg-gray-100  focus:border-gray-500 focus:bg-white focus:ring-0">
                        <option value="">Select</option>
                        @foreach ($buyerDatas as $buyerData)
                        @if (isset($buyerData->id))
                        <option value="{{ $buyerData->id }}">{{ $buyerData->buyer_name }}</option>
                        @endif
                        @endforeach
                    </select>
                    <!-- Added missing select_error div -->
                    <div id="select_error" class="text-red-500 text-sm mt-1"></div>
                </div>

                <div class="flex">
                    <button type="button" id="submit-button" class="rounded-full bg-gray-800 px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">Add</button>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('show-error', event => {
                const errorAlert = document.getElementById('error-alert');
                const alertContainer = document.getElementById('alert-container');
                errorAlert.textContent = event.detail.message;
                errorAlert.classList.remove('hidden');
                alertContainer.classList.remove('hidden');
                setTimeout(() => {
                    errorAlert.classList.add('hidden');
                    alertContainer.classList.add('hidden');
                }, 8000);
            });

            window.addEventListener('show-success', event => {
                const successAlert = document.getElementById('success-alert');
                const alertContainer = document.getElementById('alert-container');
                successAlert.textContent = event.detail.message;
                successAlert.classList.remove('hidden');
                alertContainer.classList.remove('hidden');
                setTimeout(() => {
                    successAlert.classList.add('hidden');
                    alertContainer.classList.add('hidden');
                }, 8000);
            });

            const submitButton = document.getElementById('submit-button');
            const seriesNumberInput = document.getElementById('series_number');
            const validFromInput = document.getElementById('valid_from');
            const validTillInput = document.getElementById('valid_till');
            const selectInput = document.getElementById('select');

            function clearErrorMessage(input, errorElementId) {
                input.addEventListener('input', function () {
                    input.classList.remove('border-red-500');
                    document.getElementById(errorElementId).textContent = '';
                });
            }

            clearErrorMessage(seriesNumberInput, 'series_number_error');
            clearErrorMessage(validFromInput, 'valid_from_error');
            clearErrorMessage(validTillInput, 'valid_till_error');
            clearErrorMessage(selectInput, 'select_error');

            submitButton.addEventListener('click', function () {
                let isValid = true;

                // Clear previous error messages
                document.getElementById('series_number_error').textContent = '';
                document.getElementById('valid_from_error').textContent = '';
                document.getElementById('valid_till_error').textContent = '';
                document.getElementById('select_error').textContent = '';

                // Validate series number
                if (!seriesNumberInput.value) {
                    isValid = false;
                    seriesNumberInput.classList.add('border-red-500');
                    document.getElementById('series_number_error').textContent = 'Prefix Number is required.';
                }

                // Validate valid from date
                if (!validFromInput.value) {
                    isValid = false;
                    validFromInput.classList.add('border-red-500');
                    document.getElementById('valid_from_error').textContent = 'Valid From date is required.';
                }

                // Validate valid till date
                if (!validTillInput.value) {
                    isValid = false;
                    validTillInput.classList.add('border-red-500');
                    document.getElementById('valid_till_error').textContent = 'Valid Till date is required.';
                }

                // Validate select input
                if (!selectInput.value) {
                    isValid = false;
                    selectInput.classList.add('border-red-500');
                    document.getElementById('select_error').textContent = 'Assign Receiver is required.';
                }

                if (isValid) {
                    @this.call('invoiceSeries');
                }
            });

            // Initialize datepicker for valid_from
            new Datepicker(validFromInput, {
                autohide: true,
                format: 'yyyy-mm-dd',
            });

            // Initialize datepicker for valid_till
            new Datepicker(validTillInput, {
                autohide: true,
                format: 'yyyy-mm-dd',
            });

            // Update Livewire component when date is selected
            validFromInput.addEventListener('changeDate', function (event) {
                @this.set('sellerInvoiceSeriesData.valid_from', event.target.value);
                validFromInput.classList.remove('border-red-500');
                document.getElementById('valid_from_error').textContent = '';
            });

            validTillInput.addEventListener('changeDate', function (event) {
                @this.set('sellerInvoiceSeriesData.valid_till', event.target.value);
                validTillInput.classList.remove('border-red-500');
                document.getElementById('valid_till_error').textContent = '';
            });
        });
        </script>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

        <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400 ">
            <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                       #
                    </th>

                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Prefix Number
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Assigned To
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Status
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Valid From
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Valid Till
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Action
                    </th>
                </tr>
            </thead>

            {{-- @if (count($seriesNoData) > 0) --}}
            @foreach ($seriesNoData as $index => $data)
            @php
            $data = (object) $data;
            @endphp
            <tbody>
                <tr class="{{ $index % 2 === 0 ? 'bg-[#E9E6E6]' : 'bg-white' }} bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                    <td class="w-4 px-3 py-3">
                        <div class="font-normal text-gray-500">{{ $index + 1 }}</div>
                    </td>
                    <td class="px-3 py-3 ">
                        {{ $data->series_number ?? '' }}
                    </td>
                    <td class="px-3 py-3 ">
                        @if ($data->default != '1')
                        {{ $data->assigned_to_name ?? '' }}
                        @else
                        Default
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full
                                @if ($data->assigned_to_id !== null)
                                    bg-green-500
                                @elseif ($data->default == 1)
                                    bg-green-500
                                @else
                                    bg-red-500
                                @endif
                                mr-2">
                            </div>
                            @if ($data->assigned_to_id !== null)
                                Assigned
                            @elseif ($data->default == 1)
                                Default
                            @else
                                Not Assigned
                            @endif
                        </div>
                    </td>
                    <td class="px-2 whitespace-nowrap">
                        {{ date('j-m-Y', strtotime($data->valid_from) ) }}
                    </td>
                    <td class="px-2  whitespace-nowrap">
                        {{ date('j-m-Y', strtotime($data->valid_till) ) }}
                    </td>
                    {{-- @dump($user); --}}
                    @php
                        $authUser = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user();
                        $userName = strtoupper(substr($authUser->name, 0, 4));
                        $companyName = strtoupper(substr($authUser->company_name, 0, 4));
                    @endphp

                    <td class="">
                        @if (!Str::startsWith(strtoupper($data->series_number), [$userName, $companyName]))
                            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-1 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg></button>
                            <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                    <li>
                                        <a href="javascript:void(0)" class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white" @click="openDropdown = null; $wire.selectInvoiceSeries('{{ json_encode($data) ?? '' }}').then(() => setTimeout(() => $dispatch('open-edit-modal'), 300))">Edit</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $data->id ?? '' }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </td>
                </tr>
            </tbody>
            @endforeach
        </table>

    </div>
    <div x-cloak x-data="{ showEditModal: false }" x-on:open-edit-modal.window="showEditModal = true"
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

            <div  x-show="showEditModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" wire:ignore.self>

                <div>
                    <h1  class="text-black text-lg">Edit Prefix </h1>
                </div>
                <div class="absolute top-0 right-0 pt-4 pr-4 text-black ">
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
                    <div class="p-6 grid sm:grid-cols-2 gap-4 text-black">
                        <div>
                            <label for="series_number" class="block text-sm font-medium ">Prefix Number </label>
                            <input wire:model.prevent="updateInvoiceSeriesData.series_number" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div>
                            <label for="company_name" class="block text-sm font-medium ">Valid From</label>
                            <div class="relative">

                                <input wire:model.prevent="updateInvoiceSeriesData.valid_from" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Select date">
                            </div>
                        </div>
                        <div>
                            <label for="company_name" class="block text-sm font-medium">Valid Till</label>
                            <div class="relative">

                                <input wire:model.prevent="updateInvoiceSeriesData.valid_till" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ">
                            </div>
                        </div>
                        <div>
                            <label for="company_name" class="block text-sm font-medium">Receiver Name</label>
                            <div class="relative">

                                <input wire:model.prevent="updateInvoiceSeriesData.assigned_to_name" type="text" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full cursor-not-allowed  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Receiver Name">
                            </div>
                        </div>
                        <div class="relative" wire:ignore>
                            <label for="receiver_user_id" class="block text-sm font-medium">Update Receiver</label>

                            <select id="select" wire:model="updateInvoiceSeriesData.assigned_to_b_id" class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 focus:border-gray-500 focus:bg-white focus:ring-0">
                                <option value="">Select</option>
                                @foreach ($buyerDatas as $buyerData)
                                    @php
                                        // Ensure $buyerData is an object
                                        $buyerData = is_array($buyerData) ? (object)$buyerData : $buyerData;
                                    @endphp
                                    @if(isset($buyerData->id) && isset($buyerData->buyer_name))
                                        <option value="{{ $buyerData->id }}">{{ $buyerData->buyer_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button data-modal-hide="edit-modal" type="button" wire:click.prevent='updatePanelSeries()' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                    <button data-modal-hide="edit-modal" type="button" @click="showEditModal = false" wire:click.prevent='resetInvoiceSeries()' class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Default Modal -->
    {{-- <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
        <div class="relative w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        Edit Prefix
                        <button type="button" wire:click='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                    </h3>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <div>
                        <label for="series_number" class="block text-sm font-medium ">Prefix Number </label>
                        <input wire:model.prevent="updateInvoiceSeriesData.series_number" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium ">Valid From</label>
                        <div class="relative">

                            <input wire:model.prevent="updateInvoiceSeriesData.valid_from" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Select date">
                        </div>
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium">Valid Till</label>
                        <div class="relative">

                            <input wire:model.prevent="updateInvoiceSeriesData.valid_till" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ">
                        </div>
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium">Receiver Name</label>
                        <div class="relative">

                            <input wire:model.prevent="updateInvoiceSeriesData.assigned_to_name" type="text" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Receiver Name">
                        </div>
                    </div>

                    <div class="relative">
                        <label for="receiver_user_id" class="block text-sm font-medium">Update Receiver</label>

                        <select id="select" wire:model.prevent="updateInvoiceSeriesData.assigned_to_b_id" class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100  focus:border-gray-500 focus:bg-white focus:ring-0">
                            <option value="">Select</option>
                            @foreach ($buyerDatas as $buyerData)
                            @if (isset($buyerData->id))
                            <option value="{{ $buyerData->id }}">{{ $buyerData->buyer_name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="edit-modal" type="button" wire:click.prevent='updatePanelSeries()' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                    <button data-modal-hide="edit-modal" type="button" wire:click.prevent='resetInvoiceSeries()' class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>
        </div>
    </div> --}}


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
                    @this.call('deleteInvoiceSeries', id);
                    console.log('hello');
                } else {
                    console.log("Canceled");
                }
            });
        });
    });
</script>