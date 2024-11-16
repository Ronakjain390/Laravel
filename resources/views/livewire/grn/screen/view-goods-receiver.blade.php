<div>
    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs" id="successMessage"></p>
        </div>
    </div>
    <div id="errorModal" style="display: none;">
        <div class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs">
            <p class="mt-3 " id="errorMessage">\
            </p>
        </div>

    </div>
    <script>
         window.addEventListener('show-success-message', event => {
            // Set the message in the modal
            document.getElementById('successMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 15000);
        });
    </script>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border h-screen">

        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 ">
            <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                {{-- <tr class="whitespace-nowrap"> --}}
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    #
                </th>

                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Receiver Name
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Special Id
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Address
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    State
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Pincode
                </th>

                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Email
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Phone
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Pancard
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    GST
                </th>
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Added On
                </th>
                {{-- <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Status
                </th> --}}
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Action
                </th>
                {{-- </tr> --}}
            </thead>
            {{-- @dd($receiverDatas); --}}
            @foreach ($receiverDatas as $index => $data)
            {{-- @dd($data->user->added_by) --}}
            @php
                $data = (object) $data;
            @endphp
                <tbody>
                    <tr
                        class="@if ($index % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0 ">
                        <td class="px-2">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-2">
                            {{ $data->receiver_name }}
                        </td>
                        <td class="px-2 ">
                            {{-- {{ $data->receiver_special_id }} --}}
                        </td>
                        <td class="px-2 ">
                            <p> {{ $data->details[0]->address ?? '' }} </p>
                        </td>

                        <td class="px-2 ">
                            {{ $data->details[0]->state ?? '' }}
                        </td>
                        <td class="px-2 ">
                            {{ $data->details[0]->pincode ?? '' }}
                        </td>
                        <td class="px-2 ">
                            {{ $data->details[0]->email ?? '' }}
                        </td>

                        <td class="px-2 ">
                            {{ $data->details[0]->phone ?? '' }}
                        </td>
                        <td class="px-2 ">
                        </td>
                        <td class="px-2 ">
                            {{ $data->details[0]->gst_number ?? '' }}
                        </td>
                        <td class="px-2">
                            {{ date('j/m/Y', strtotime($data->created_at)) }}
                        </td>

                        {{-- <td class="px-2 ">
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-green-500 mr-2"></div> Online
                            </div>
                        </td> --}}

                        <td class="">
                            <button id="dropdownDefaultButton-{{ $index }}"
                                data-dropdown-toggle="dropdown-{{ $index }}"
                                class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                type="button">Select <svg class="w-2.5  ml-2.5" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg></button>
                            <div id="dropdown-{{ $index }}"
                                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                    @if(isset($data->user->added_by))
                                    @if($data->user->added_by == $data->user_id)
                                    <li>
                                        <a href="#" wire:click="selectReceiver({{ json_encode($data) }} )"
                                            data-modal-target="medium-modal" data-modal-toggle="medium-modal"
                                            class="block px-4 py-2 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                    </li>
                                    @endif
                                    @endif
                                    <li>
                                        <a href="javascript:void(0);"
                                            wire:click="$emit('triggerDelete', {{ $data->id }})"
                                            class="block px-4 py-2 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                    </li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach


        </table>

    </div>
    <!-- Default Modal -->
    {{-- <div id="medium-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
        wire:ignore.self>
        <div class="relative w-full max-w-6xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        Edit Receiver
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="medium-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <div>
                        <label for="receiver_name" class="block text-md font-medium">Receiver Name</label>
                        <input @if ($selectReceiver['added_by'] !== null) disabled @endif
                            wire:model.prevent="selectReceiver.receiver_name" type="text"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <!-- Addresses Section -->
                    <div>
                        <label class="block text-md font-medium">Addresses</label>
                        <!-- Add Address Button at the Top -->
                        <button type="button"
                            class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                            wire:click.prevent='addNewReceiverDetail'>Add Address</button>

                        <!-- Address Cards -->
                        <div id="address-fields">
                            <!-- Example Address Card with Delete Button -->
                            @foreach ($selectReceiver['details'] as $key => $detail)
                                <div wire:key="detail-{{ $key }}"
                                    class="mt-4 border rounded-lg p-4 border-gray-300 relative">
                                    <button type="button"
                                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2 py-1 absolute top-1 right-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                        wire:click.prevent="removeReceiverDetail({{ $key }})">Delete</button>
                                    <input type="hidden"
                                        wire:model.prevent="selectReceiver.details.{{ $key }}.id">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="location_name" class="block text-sm font-medium">Location Name</label>
                                            <input wire:model.prevent='selectReceiver.details.{{ $key }}.location_name'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="address" class="block text-sm font-medium">Address</label>
                                            <input wire:model.prevent='selectReceiver.details.{{ $key }}.address'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="phone" class="block text-sm font-medium">Phone</label>
                                            <input wire:model.prevent='selectReceiver.details.{{ $key }}.phone'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="gst_number" class="block text-sm font-medium">GST Number</label>
                                            <input
                                                wire:model.prevent='selectReceiver.details.{{ $key }}.gst_number'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="pincode" class="block text-sm font-medium">Pincode</label>
                                            <input wire:model.prevent='selectReceiver.details.{{ $key }}.pincode'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="state" class="block text-sm font-medium">State</label>
                                            <input wire:model.prevent='selectReceiver.details.{{ $key }}.state'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="city" class="block text-sm font-medium">City</label>
                                            <input wire:model.prevent='selectReceiver.details.{{ $key }}.city'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="bank_name" class="block text-sm font-medium">Bank Name</label>
                                            <input
                                                wire:model.prevent='selectReceiver.details.{{ $key }}.bank_name'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="branch_name" class="block text-sm font-medium">Branch Name</label>
                                            <input
                                                wire:model.prevent='selectReceiver.details.{{ $key }}.branch_name'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="bank_account_no" class="block text-sm font-medium">Bank Account
                                                No</label>
                                            <input
                                                wire:model.prevent='selectReceiver.details.{{ $key }}.bank_account_no'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="ifsc_code" class="block text-sm font-medium">IFSC Code</label>
                                            <input
                                                wire:model.prevent='selectReceiver.details.{{ $key }}.ifsc_code'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="tan" class="block text-sm font-medium">TAN</label>
                                            <input wire:model.prevent='selectReceiver.details.{{ $key }}.tan'
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Add other fields (phone, gst_number, state, city, bank_name, etc.) here -->
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="medium-modal" wire:click.prevent="updateReceiverDetail" type="button"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Save
                        Changes</button>
                    <button data-modal-hide="medium-modal" type="button"
                        class="text-gray-500 bg-white hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>
        </div>
    </div> --}}

    <script>
        // JavaScript function to remove an address card
        function removeAddress(button) {
            const addressCard = button.parentElement;
            addressCard.remove();
        }
    </script>
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
                        @this.call('deleteReceiver', id);
                        console.log('hello');
                    } else {
                        console.log("Canceled");
                    }
                });
            });
        });
    </script>
</div>
