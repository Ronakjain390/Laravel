<div class="max-w-2xl mx-auto">
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800">
        <div>
            @php
            $manuallyAdded = json_decode($manuallyAdded);
            // dd($manuallyAdded);
            @endphp
            @if($manuallyAdded)

                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <span class="text-sm">Error:</span> @if(is_array($manuallyAdded))
                    {{ $manuallyAdded[0] }}
                @else
                    {{ $manuallyAdded->seller_special_id[0] }}
                @endif

                </div>
            @endif
            <div>
                @php
                    $manuallyAdded = json_decode($manuallyAdded);
                    // dd($manuallyAdded);
                @endphp
                @if ($manuallyAdded)

                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                        class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                        role="alert">
                        <span class="text-sm">Error:</span>
                        @if (is_array($manuallyAdded))
                            {{ $manuallyAdded[0] }}
                        @else
                            {{ $manuallyAdded->receiver_special_id[0] }}
                        @endif

                    </div>
                @endif
                @if ($success)
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                        class="p-4 text-xs text-green-800 mb-1 rounded-lg bg-green-200 dark:bg-gray-800 dark:text-green-400"
                        role="alert">
                        <span class="text-sm">Success:</span> {{ $success }}
                    </div>
                @endif

            <div id="successModal" style="display: none;">
                <div class="modal-content">
                    <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs" id="successMessage"></p>
                </div>
            </div>
            <div id="errorModal" style="display: none;">
                <div class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs">
                    <p class="mt-3 " id="errorMessage">\
                    </p>
                    {{-- @if($errorFileUrl)

                    <a class="hover:cursor-pointer hover:underline mt-2 bg-gray-800 text-white px-2 rounded ml-1" href="{{ $errorFileUrl }}"  download>Download</a>
                    @endif --}}
                </div>

            </div>

        <script>
            window.addEventListener('show-error-message', event => {
                // Set the message in the modal
                document.getElementById('errorMessage').textContent = event.detail[0];

                // Show the modal (you might need to use your specific modal's show method)
                document.getElementById('errorModal').style.display = 'block';

                // Optionally, hide the modal after a few seconds
                setTimeout(() => {
                    document.getElementById('errorModal').style.display = 'none';
                }, 15000);
            });

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
        </div>
        <div class="sm:flex items-center mb-4">
            <ul class="flex whitespace-nowrap text-xs sm:text-sm text-center" id="myTab" role="tablist">
                <li class="mr-2" role="presentation">
                    <button wire:click="setActiveTab('receiver-manually')"
                        class="border-b border-t rounded-xl px-2 py-1 text-sm text-white {{ $activeTab === 'receiver-manually' ? 'bg-orange' : 'bg-gray-700' }}  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700"
                        aria-controls="receiver-manually"
                        aria-selected="{{ $activeTab === 'receiver-manually' ? 'true' : 'false' }}">
                        Add Manually
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button wire:click="setActiveTab('receiver-code')"
                        class="border-b border-t rounded-xl px-2 py-1 text-sm text-white {{ $activeTab === 'receiver-code' ? 'bg-orange' : 'bg-gray-700' }}  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 text-bl"
                        aria-controls="receiver-code"
                        aria-selected="{{ $activeTab === 'receiver-code' ? 'true' : 'false' }}">
                        Seller Code
                    </button>
                </li>
            </ul>
        </div>


        @if ($activeTab === 'receiver-manually')
        <div class=" rounded-lg dark:bg-gray-800 ">
            @if(!empty($addSellerData))
                <div class="" id="receiver-manually" role="tabpanel" aria-labelledby="receiver-manually">
                    <div >


                        <div class="grid gap-4">
                            <div>
                                <label for="seller_name" class="block text-sm text-black">Name <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addSellerData.seller_name" wire:ignore.self
                                    id="seller_name" name="seller_name"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addSellerData.seller_name') border-red-500 @enderror"
                                    placeholder="Enter Buyer Name">
                                @error('addSellerData.seller_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="address" class="block text-sm text-black">Address <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addSellerData.address" wire:ignore.self
                                    id="address" name="address"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addSellerData.address') border-red-500 @enderror"
                                    placeholder="Enter Address">
                                @error('addSellerData.address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="pincode" class="block text-sm text-black">Pincode <span class="text-red-600">*</span></label>
                                <input type="number" wire:model.defer="addSellerData.pincode"
                                    wire:keydown.enter="cityAndStateByPincode" wire:ignore.self id="pincode" name="pincode"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addSellerData.pincode') border-red-500 @enderror"
                                    placeholder="Area Pincode"
                                    oninput="if (this.value.length > 6) this.value = this.value.slice(0, 6); if (this.value.length === 6) { @this.set('addSellerData.pincode', this.value); @this.call('cityAndStateByPincode'); }">
                                @error('addSellerData.pincode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm text-black">City <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addSellerData.city" wire:ignore.self id="city"
                                    name="city"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addSellerData.city') border-red-500 @enderror"
                                    placeholder="City">
                                @error('addSellerData.city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="state" class="block text-sm text-black">State <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addSellerData.state" wire:ignore.self id="state"
                                    name="state"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addSellerData.state') border-red-500 @enderror"
                                    placeholder="State">
                                @error('addSellerData.state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                                <label for="number" class="block text-sm text-black">Phone Number</label>
                                <input type="number" wire:model.defer="addSellerData.phone" wire:ignore.self
                                    id="phone" name="phone"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                                    placeholder="Enter Phone Number"  max="9999999999" maxlength="10"
                                    oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"
                                    >
                                @error('addSellerData.phone')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm text-black">E- Mail</label>
                                <input type="text" wire:model.defer="addSellerData.email" wire:ignore.self
                                    id="email" name="email"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                                    placeholder="Enter Email Id">
                                    <div id="email_error" class="text-red-500 text-sm mt-1"></div>
                                @error('addSellerData.email')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
          @endif
            </div>
            @endif


        @if ($activeTab === 'receiver-code')
        <div id="myTabContent" class=" p-4 rounded-lg dark:bg-gray-800 ">
                <div class="mt-2" id="receiver-code" role="tabpanel" aria-labelledby="receiver-code">
                    <div>
                        <label for="receiver_code" class="block text-sm text-black">Seller Code</label>
                        <input type="text" wire:model.defer="addSellerData.seller_special_id"
                            id="seller_special_id" name="seller_special_id"
                            class="mt-1 p-2 h-8  block w-full rounded-md bg-white border-transparent text-black focus:border-gray-500"
                            placeholder="If Seller is TheParchi User">
                            @error('addSellerData.seller_special_id')
                                    <p class="text-red-500 text-sm">Seller Already added</p>
                                @enderror
                    </div>


                    <div class="flex justify-center">
                        <button type="button" wire:click="callAddSeller"
                            class="rounded-full w-full bg-gray-900 px-8 py-2 mt-4 text-white hover:bg-yellow-200 hover:text-black">Add
                            Seller</button>
                    </div>
                </div>

            </div>
            @endif


    </div>
    {{-- @if ($activeTab == 'receiver-code') --}}
    @if ($activeTab === 'receiver-manually')
    <div class="max-w-5xl mt-4 mx-auto " id="receiver-manually" role="tabpanel"
        aria-labelledby="receiver-manually-tab">
        <div class="flex justify-end items-center">
            <label for="toggleInput" class="block text-xs font-italic mr-3">Additional Info</label>
            <button id="toggleInput" class="p-2 rounded-full bg-[#E5F881] focus:outline-none focus:ring-2 ">
                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
        </div>
        <div wire:ignore.self>
            <!-- Plus icon to toggle the input boxes -->
            <div class="{{ $showInputBoxes ? '' : 'hidden' }}relative mt-2">
                <!-- <div class="mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-white dark:bg-gray-800"> -->
                <div id="inputBoxes" class="hidden mt-2 bg-[#e9e6e6] p-6 rounded-lg">
                    <!-- Add your input boxes here -->
                    <div>
                        <label for="company_name" class="block text-sm text-black mt-2">Company Name</label>
                        <input type="text" wire:model.defer="addSellerData.company_name" wire:ignore.self
                            id="company_name" name="company_name"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                            placeholder="Company Number">
                    </div>
                    {{-- <div>
                        <label for="tan" class="block text-sm text-black mt-2">Organisation Type</label>
                        <input type="text" wire:model.defer="addSellerData.tan" wire:ignore.self id="tan"
                            name="tan"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500">
                    </div> --}}
                    <div>
                        <label for="organisation_type" class="block text-sm text-black mt-2">Organisation Type</label>

                        <input type="text" wire:model.defer="addSellerData.organisation_type" name="organisation_type"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                            list="organisation_type" placeholder="Organisation Type">

                        <datalist id="organisation_type">
                            <option value="Private Limited Company">
                            <option value="Public Limited Company">
                            <option value="Sole Proprietorship Firm">
                            <option value="Partnerships Firm">
                            <option value="Limited liability company (LLC)">
                            <option value="One Person Company">
                            <option value="NGO">
                            <option value="Unregistered">
                        </datalist>
                    </div>

                    <div>
                        <label for="gst_number" class="block text-sm text-black mt-2">GST</label>
                        <input type="text" wire:model.defer="addSellerData.gst_number"  id="gst_number"
                            name="gst_number"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                            placeholder="Enter Gst Number">
                            @error('addSellerData.gst_number')
                            <p class="text-red-500 text-sm">Seller Already added</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pancard" class="block text-sm text-black mt-2">Pancard</label>
                        <input type="text" wire:model.defer="addSellerData.pancard" wire:ignore.self
                            id="pancard" name="pancard"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                            placeholder="Enter Pancard Number">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center">
        <button type="button" wire:click="validateAndAddBuyer"  id="add"
            class="rounded-full w-full bg-gray-900 px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black ">Add
            Seller</button>
        </div>
        @endif
 </div>
 <!-- Javascript function for Open inputs -->
 <script>
    document.getElementById('toggleInput').addEventListener('click', function() {
        var inputBoxes = document.getElementById('inputBoxes');
        inputBoxes.classList.toggle('hidden');
    });
    // document.addEventListener('DOMContentLoaded', function () {
    //     const emailInput = document.getElementById('email');
    //     const emailError = document.getElementById('email_error');
    //     const addSellerButton = document.getElementById('add-seller-button');

    //     emailInput.addEventListener('input', function () {
    //         const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    //         if (!emailPattern.test(emailInput.value)) {
    //             emailError.textContent = 'Invalid email address.';
    //             emailInput.classList.add('border-red-500');
    //             addSellerButton.disabled = true;
    //         } else {
    //             emailError.textContent = '';
    //             emailInput.classList.remove('border-red-500');
    //             addSellerButton.disabled = false;
    //         }
    //     });
    // });
 </script>
 {{-- @livewire('livewire-ui.debug') --}}
