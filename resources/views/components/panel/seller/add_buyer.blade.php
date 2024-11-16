<div class="max-w-2xl mx-auto"x-data="{
    formValid: false,
    validateForm() {
        this.formValid =
            this.$wire.addBuyerData.buyer_name &&
            this.$wire.addBuyerData.address &&
            this.$wire.addBuyerData.pincode &&
            this.$wire.addBuyerData.city &&
            this.$wire.addBuyerData.state;
    }
}" x-init="validateForm()" >
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
                    {{ $manuallyAdded->buyer_special_id[0] }}
                @endif

                </div>
            @endif
            @if($success)
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-green-800 mb-1 rounded-lg bg-green-200 dark:bg-gray-800 dark:text-green-400"
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
                    <p class="mt-3 " id="errorMessage">
                    </p>
                    @if($errorFileUrl)

                    <a class="hover:cursor-pointer hover:underline mt-2 bg-gray-800 text-white px-2 rounded ml-1" href="{{ $errorFileUrl }}"  download>Download</a>
                    @endif
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
            <button
                class="px-4 p-1.5 w-auto text-center font-medium text-sm text-black {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}"
                wire:click="$set('activeTab', 'tab1')" {{ $activeTab === 'tab1' ? 'disabled' : '' }}>Add Manually</button>
            <button
                class="px-4 p-1.5 w-auto text-center font-medium text-sm text-black {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}"
                wire:click="$set('activeTab', 'tab2')" {{ $activeTab === 'tab2' ? 'disabled' : '' }}>Buyer Code</button>
            <button
                class="px-4 p-1.5 w-auto text-center font-medium text-sm text-black {{ $activeTab === 'tab3' ? 'bg-orange text-white rounded-lg' : '' }}"
                wire:click="$set('activeTab', 'tab3')" {{ $activeTab === 'tab3' ? 'disabled' : '' }}>Bulk Buyer</button>


            {{-- <ul class="flex whitespace-nowrap text-xs sm:text-sm text-center" id="myTab" role="tablist">
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
                        Buyer Code
                    </button>
                </li>
            </ul> --}}
        </div>


        @if ($activeTab === 'tab1')
        <div class=" rounded-lg dark:bg-gray-800 ">
            @if(!empty($addBuyerData))
                <div class="" id="receiver-manually" role="tabpanel" aria-labelledby="receiver-manually">
                    <div >

                        <div class="grid gap-4">
                            <div>
                                <label for="buyer_name" class="block text-sm text-black">Name <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addBuyerData.buyer_name" wire:ignore.self
                                    id="buyer_name" name="buyer_name"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addBuyerData.buyer_name') border-red-500 @enderror"
                                    placeholder="Enter Buyer Name">
                                @error('addBuyerData.buyer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="address" class="block text-sm text-black">Address <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addBuyerData.address" wire:ignore.self
                                    id="address" name="address"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addBuyerData.address') border-red-500 @enderror"
                                    placeholder="Enter Address">
                                @error('addBuyerData.address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="pincode" class="block text-sm text-black">Pincode <span class="text-red-600">*</span></label>
                                <input type="number" wire:model.defer="addBuyerData.pincode"
                                    wire:keydown.enter="cityAndStateByPincode" wire:ignore.self id="pincode" name="pincode"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addBuyerData.pincode') border-red-500 @enderror"
                                    placeholder="Area Pincode"
                                    oninput="if (this.value.length > 6) this.value = this.value.slice(0, 6); if (this.value.length === 6) { @this.set('addBuyerData.pincode', this.value); @this.call('cityAndStateByPincode'); }">
                                @error('addBuyerData.pincode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm text-black">City <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addBuyerData.city" wire:ignore.self id="city"
                                    name="city"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addBuyerData.city') border-red-500 @enderror"
                                    placeholder="City">
                                @error('addBuyerData.city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="state" class="block text-sm text-black">State <span class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addBuyerData.state" wire:ignore.self id="state"
                                    name="state"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 @error('addBuyerData.state') border-red-500 @enderror"
                                    placeholder="State">
                                @error('addBuyerData.state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                                <label for="number" class="block text-sm text-black">Phone Number</label>
                                <input type="number" wire:model.defer="addBuyerData.phone" wire:ignore.self
                                    id="phone" name="phone"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                                    placeholder="Enter Phone Number"  max="9999999999" maxlength="10"
                                    oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"
                                    >
                                @error('addBuyerData.phone')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm text-black">E- Mail</label>
                                <input type="text" wire:model.defer="addBuyerData.email" wire:ignore.self
                                    id="email" name="email"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                                    placeholder="Enter Email Id">
                                    <div id="email_error" class="text-red-500 text-sm mt-1"></div>
                                @error('addBuyerData.email')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
          @endif
            </div>



        @elseif ($activeTab === 'tab2')
        <div id="myTabContent" class=" p-4 rounded-lg dark:bg-gray-800 ">
                <div class="mt-2" id="receiver-code" role="tabpanel" aria-labelledby="receiver-code">
                    <div>
                        <label for="receiver_code" class="block text-sm text-black">Buyer Code</label>
                        <input type="text" wire:model="addBuyerData.buyer_special_id"
                            id="buyer_special_id" name="buyer_special_id"
                            class="mt-1 p-2 h-8  block w-full rounded-md bg-white border-transparent text-black focus:border-gray-500"
                            placeholder="If Buyer is TheParchi User">
                            @error('addBuyerData.buyer_special_id')
                                    <p class="text-red-500 text-sm">Buyer Already added</p>
                                @enderror
                    </div>


                    <div class="flex justify-center">
                        <button type="button" wire:click="callAddBuyer"
                            class="rounded-full w-full bg-gray-900 px-8 py-2 mt-4 text-white hover:bg-yellow-200 hover:text-black">Add
                            Buyer</button>
                    </div>
                </div>

            </div>
            @elseif($activeTab === 'tab3')
            <div id="myTabContent" class=" p-4 rounded-lg dark:bg-gray-800 ">
                <div class="mt-2"  role="tabpanel" >
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow-md">
                        <div class="text-blue-700 font-semibold mb-2 flex flex-col md:flex-row justify-between items-center">
                            <a href="{{ route('buyer.exportColumns') }}"
                                class="bg-[#E2DFDF] text-[0.6rem] text-black hover:bg-orange mb-5 py-1 lg:py-2 px-4 rounded-lg">
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
        </div>
        </div>
        @endif
    {{-- @if ($activeTab == 'receiver-code') --}}
    @if ($activeTab === 'tab1')
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
                        <input type="text" wire:model.defer="addBuyerData.company_name" wire:ignore.self
                            id="company_name" name="company_name"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                            placeholder="Company Number">
                    </div>
                    {{-- <div>
                        <label for="tan" class="block text-sm text-black mt-2">Organisation Type</label>
                        <input type="text" wire:model.defer="addBuyerData.tan" wire:ignore.self id="tan"
                            name="tan"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500">
                    </div> --}}
                    <div>
                        <label for="organisation_type" class="block text-sm text-black mt-2">Organisation Type</label>

                        <input type="text" wire:model.defer="addBuyerData.organisation_type" name="organisation_type"
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
                        <input type="text" wire:model.defer="addBuyerData.gst_number"  id="gst_number"
                            name="gst_number"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                            placeholder="Enter Gst Number">
                            @error('addBuyerData.gst_number')
                            <p class="text-red-500 text-sm">Buyer Already added</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pancard" class="block text-sm text-black mt-2">Pancard</label>
                        <input type="text" wire:model.defer="addBuyerData.pancard" wire:ignore.self
                            id="pancard" name="pancard"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                            placeholder="Enter Pancard Number">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center">
        <button type="button" wire:click="validateAndAddBuyer" id="add"
            class="rounded-full w-full bg-gray-900 px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">
            Add Buyer
        </button>
    </div>
        @endif
 </div>
 <!-- Javascript function for Open inputs -->
 <script>
    document.getElementById('toggleInput').addEventListener('click', function() {
        var inputBoxes = document.getElementById('inputBoxes');
        inputBoxes.classList.toggle('hidden');
    });

 </script>
 {{-- @livewire('livewire-ui.debug') --}}
