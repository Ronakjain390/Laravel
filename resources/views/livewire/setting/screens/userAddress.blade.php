<div class=" ml-5">
    <div class="">
        @if ($errorMessage)
        {{-- {{dd($errorMessage)}} --}}
        @foreach (json_decode($errorMessage) as $error)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="success-alert" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
            role="alert">
            <span class="">Error:</span> {{ $error[0] }}
        </div>
        @endforeach

        @endif
        @if ($successMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="success-alert" class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
            role="alert">
            <span class="">Success:</span> {{ $successMessage }}
        </div>
        @endif

    </div>
    <h1 class="border-b-2 border-solid text-lg">Registered Address</h1>

    <a  wire:click="openModal"
        class="block px-4 py-2 mt-5 w-40 bg-white rounded-xl text-black border border-black hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white text-xs">
        Add New Address</a>
    <div class="mt-5 text-sm">

        @php
        $userAddress = (object) $userAddress;
        @endphp

        <div class="m-2 border-b-2 border-gray-200">
            <p class="">Primary Address</p>
            <p class="text-gray-500"> Phone:{{$userAddress->{0}->phone}} | Address: {{$userAddress->{0}->address}}, {{$userAddress->{0}->city}},
                {{$userAddress->{0}->state}} - ({{$userAddress->{0}->pincode}}) </p>
        </div>
        @foreach ($userAddress->{0}->details as $index=> $data)
        <div class="m-2 border-b-2 border-gray-200 flex justify-between items-center">
            <div>
                <p class="">{{ $data->location_name }}</p>
                <p class="text-gray-500"> Phone:{{$data->phone}} | Address: {{$data->address}}, {{$data->city}},
                    {{$data->state}} - ({{$data->pincode}}) </p>
            </div>

            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}"
                class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300  rounded-lg text-sm px-2.5 py-1.5 mr-5 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                type="button">Select <svg class="w-2.5  ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg></button>
            <div id="dropdown-{{ $index }}"
                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                    aria-labelledby="dropdownDefaultButton-{{ $index }}">
                    {{-- @if(isset($data->user->added_by)) --}}
                    {{-- @if($data->user->added_by == $data->user_id) --}}
                    {{-- <li> @dump($data) --}}
                        <a href="#" wire:click="editAddress({{ json_encode($data) }} )"
                            {{-- data-modal-target="edit-modal" data-modal-toggle="edit-modal" --}}
                            onclick="my_modal_1.showModal()"
                            class="block px-4 py-2 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                    </li>
                    {{-- @endif --}}
                    {{-- @endif --}}
                    <li>
                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $data->id }})"
                            class="block px-4 py-2 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                    </li>

                </ul>
            </div>
        </div>
        @endforeach


    </div>

    <!-- Default Modal -->
    <dialog id="my_modal_1" class="modal" wire:ignore.self>
        <div class="relative w-full max-w-6xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl  text-gray-900 dark:text-white">
                        Add Address
                        <button type="button" onclick="my_modal_1.close()"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="location_name" class="block text-sm ">Location Name <span class="text-red-500">*</span></label>
                            <input wire:model.prevent='addAddress.location_name' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('location_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm ">Phone <span class="text-red-500">*</span></label>
                            <input wire:model.prevent='addAddress.phone' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('phone')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="address" class="block text-sm ">Address <span class="text-red-500">*</span></label>
                            <input wire:model.prevent='addAddress.address' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('address')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="pincode" class="block text-sm ">Pincode <span class="text-red-500">*</span></label>
                            {{-- <input wire:model.prevent='addAddress.pincode' type="text" wire:keyup="cityAndStateByPincode"  id="pincode"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> --}}
                                <input type="text"x-bind:readonly="!editable" x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}" wire:model.defer="addAddress.pincode"
                                wire:keydown.enter="cityAndStateByPincode" wire:ignore.self id="pincode" name="pincode"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"

                                oninput="if (this.value.length === 6) { @this.set('addAddress.pincode', this.value); @this.call('cityAndStateByPincode'); }">
                                @error('pincode')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="state" class="block text-sm ">State <span class="text-red-500">*</span></label>
                            <input wire:model.prevent='addAddress.state' type="text"  id="state"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('state')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="city" class="block text-sm ">City <span class="text-red-500">*</span></label>
                            <input wire:model.prevent='addAddress.city' type="text"  id="city"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('city')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button  wire:click.prevent="createAddress" type="button"
                        class="@if ($inputsResponseDisabled == true) bg-gray-800 text-white @else text-white bg-gray-900  @endif  hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300  rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Save
                        Changes</button>
                    <button onclick="my_modal_1.close()" type="button"
                        class="text-gray-500 bg-white hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm  px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>
        </div>

    </dialog>

    @if ($openSearchModal == true)
    <div
        x-show="openSearchModal"
        x-on:keydown.escape.window="openSearchModal = false"
        x-on:close.stop="openSearchModal = false"
        class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-xl text-black">
            <div class="mb-4">
                <h1 class="text-lg text-black border-b border-gray-400">{{ $searchModalHeading }}</h1>
                <div class="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="location_name" class="block text-sm ">Location Name <span class="text-red-500">*</span> </label>
                            <input wire:model.defer='addAddress.location_name' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('location_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm ">Phone <span class="text-red-500">*</span> </label>
                            <input wire:model.defer='addAddress.phone' type="number"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('phone')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="address" class="block text-sm ">Address <span class="text-red-500">*</span> </label>
                            <input wire:model.defer='addAddress.address' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('address')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="pincode" class="block text-sm ">Pincode <span class="text-red-500">*</span> </label>
                            <input wire:model.defer='addAddress.pincode' type="text" wire:keyup="cityAndStateByPincode"  id="pincode"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('pincode')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="state" class="block text-sm ">State <span class="text-red-500">*</span> </label>
                            <input wire:model.defer='addAddress.state' type="text"  id="state"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('state')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="city" class="block text-sm ">City <span class="text-red-500">*</span> </label>
                            <input wire:model.defer='addAddress.city' type="text"  id="city"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @error('city')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <div>

                        </div>

                    </div>
                    @error('comment')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                    <button x-on:click="openSearchModal = false" wire:click="closeTagModal"
                            class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500 transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        Cancel
                    </button>
                    <button wire:click="{{ $searchModalAction }}"
                            {{-- x-bind:disabled="isSaveDisabled()" --}}
                            class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        {{ $searchModalButtonText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Default Modal -->
    <div id="edit-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
        wire:ignore.self>
        <div class="relative w-full max-w-6xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl  text-gray-900 dark:text-white">
                        Edit Address
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="edit-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="p-6 ">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="location_name" class="block text-sm ">Location Name</label>
                            <input wire:model.prevent='addAddress.location_name' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm ">Phone</label>
                            <input wire:model.prevent='addAddress.phone' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div>
                            <label for="address" class="block text-sm ">Address</label>
                            <input wire:model.prevent='addAddress.address' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>

                        <div>
                            <label for="pincode" class="block text-sm ">Pincode</label>
                            <input wire:model.prevent='addAddress.pincode' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div>
                            <label for="state" class="block text-sm ">State</label>
                            <input wire:model.prevent='addAddress.state' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div>
                            <label for="city" class="block text-sm ">City</label>
                            <input wire:model.prevent='addAddress.city' type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="edit-modal" wire:click.prevent="updateAddress" type="button"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300  rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Save
                        Changes</button>
                    <button data-modal-hide="edit-modal" type="button"
                        class="text-gray-500 bg-white hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm  px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
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
                    @this.call('deleteAddress', id);
                    console.log('hello');
                } else {
                    console.log("Canceled");
                }
            });
        });
    });
    document.addEventListener('livewire:load', function () {
        Livewire.on('addressAdded', function () {
            document.getElementById('my_modal_1').close();
        });
    });

    window.addEventListener('closeModal', event => {
        document.getElementById('my_modal_1').close();
    });
</script>
</div>
