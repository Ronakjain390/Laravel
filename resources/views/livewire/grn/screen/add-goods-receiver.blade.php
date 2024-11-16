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
                    {{ $manuallyAdded->receiver_special_id[0] }}
                @endif

                </div>
            @endif
            @if(session()->has('message'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-green-800 mb-1 rounded-lg bg-green-200 dark:bg-gray-800 dark:text-green-400"
                    role="alert">
                    <span class="text-sm">Success:</span> {{ session('message') }}
                </div>
            @endif
            {{-- Check if there are any errors --}}
            @if($errors->any())
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <ul>
                @foreach ($errors->all() as $error)
                <li class="text-sm">Error: {{$error }} </li>
                @endforeach
                </ul>

            </div>
            @endif


        </div>

        <div class=" rounded-lg dark:bg-gray-800 ">
            @if(!empty($addReceiverData))
                <div class="" id="receiver-manually" role="tabpanel" aria-labelledby="receiver-manually">
                    <div >

                        <div class="grid gap-4">
                            <div>
                                <label for="receiver_name" class="block text-sm text-black ">Name <span
                                        class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addReceiverData.receiver_name" wire:ignore.self
                                    id="receiver_name" name="receiver_name"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:"
                                    placeholder="Enter Receiver Name">
                            </div>


                            <div>


                                <label for="address" class="block text-sm text-black">Address <span
                                        class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addReceiverData.address" wire:ignore.self
                                    id="address" name="address"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                                    placeholder="Enter Address">
                            </div>


                            <div>
                                <label for="pincode" class="block text-sm text-black">Pincode <span
                                        class="text-red-600">*</span></label>
                                        <input type="text" wire:model.defer="addReceiverData.pincode"
                                        wire:keydown.enter="cityAndStateByPincode" wire:ignore.self id="pincode" name="pincode"
                                        class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                                        placeholder="Area Pincode"
                                        oninput="if (this.value.length === 6) { @this.set('addReceiverData.pincode', this.value); @this.call('cityAndStateByPincode'); }">

                            </div>
                            <div>
                                <label for="city" class="block text-sm text-black">City <span
                                        class="text-red-600">*</span></label>
                                <input type="text" wire:model.defer="addReceiverData.city" wire:ignore.self id="city"
                                    name="city"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                                    placeholder="City">
                            </div>
                            <div>
                                <label for="state" class="block text-sm text-black">State <span
                                        class="text-red-600">*</span></label>
                                <input type="text" wire:model="addReceiverData.state" wire:ignore.self id="state"
                                    name="state"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                                    placeholder="State">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm text-black">Phone Number</label>
                                <input type="number" wire:model.defer="addReceiverData.phone" wire:ignore.self
                                    id="phone" name="phone"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                                    placeholder="Enter Phone Number"
                                    oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);" >
                                @error('addReceiverData.phone')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm text-black">E- Mail</label>
                                <input type="text" wire:model.defer="addReceiverData.email" wire:ignore.self
                                    id="email" name="email"
                                    class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                                    placeholder="Enter Email Id">
                                    <div id="email_error" class="text-red-500 text-sm mt-1"></div>
                                @error('addReceiverData.email')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
          @endif
            </div>


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
        <div >
            <!-- Plus icon to toggle the input boxes -->
            <div class="{{ $showInputBoxes ? '' : 'hidden' }}relative mt-2">
                <!-- <div class="mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-white dark:bg-gray-800"> -->
                <div id="inputBoxes" class="hidden mt-2 bg-[#e9e6e6] p-6 rounded-lg">
                    <!-- Add your input boxes here -->
                    <div>
                        <label for="company_name" class="block text-sm text-black mt-2">Company Name</label>
                        <input type="text" wire:model.defer="addReceiverData.company_name" wire:ignore.self
                            id="company_name" name="company_name"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                            placeholder="Company Number">
                    </div>
                    {{-- <div>
                        <label for="tan" class="block text-sm text-black mt-2">Organisation Type</label>
                        <input type="text" wire:model.defer="addReceiverData.tan" wire:ignore.self id="tan"
                            name="tan"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500">
                    </div> --}}
                    <div>
                        <label for="organisation_type" class="block text-sm text-black mt-2">Organisation Type</label>

                        <input type="text" wire:model.defer="addReceiverData.organisation_type" name="organisation_type"
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
                        <input type="text" wire:model.defer="addReceiverData.gst_number"  id="gst_number"
                            name="gst_number"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500"
                            placeholder="Enter Gst Number">
                            @error('addReceiverData.gst_number')
                            <p class="text-red-500 text-sm">Receiver Already added</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pancard" class="block text-sm text-black mt-2">Pancard</label>
                        <input type="text" wire:model.defer="addReceiverData.pancard" wire:ignore.self
                            id="pancard" name="pancard"
                            class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md bg-white border-transparent focus:border-gray-500 "
                            placeholder="Enter Pancard Number">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center">
        <button type="button" wire:click="callAddReceiverManually" id="add"
            class="rounded-full w-full bg-gray-900 px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black ">Add
            Receiver</button>
        </div>
        @endif
 </div>
 <script>
    document.getElementById('toggleInput').addEventListener('click', function() {
        var inputBoxes = document.getElementById('inputBoxes');
        inputBoxes.classList.toggle('hidden');
    });

</script>
 {{-- @livewire('livewire-ui.debug') --}}
