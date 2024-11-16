{{-- @livewire(setting.screens.tabs-component) --}}
{{-- <livewire:setting.screens.tabs-component /> --}}
{{-- @include('user.setting.tabsComponent') --}}

<div class="max-w-2xl  mx-auto  text-black z-10">
    {{-- <form class="grid gap-1.5" wire:submit.prevent="updateData"> --}}
        <div x-data="{
                editable: false,
                highlightEdit: false,
                attemptedEdit() {
                    if (!this.editable) {
                        this.highlightEdit = true;
                        setTimeout(() => this.highlightEdit = false, 15000);
                    }
                }
            }">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700 p-2 rounded-lg bg-[#e9e6e6] dark:bg-gray-800">

            <div class="flex justify-end">
               {{-- <span class="text-sm"> My Profile</span> --}}
               <button
                    @click="editable = !editable; highlightEdit = false;"
                    :class="{ 'animate-pulse bg-red-600 text-white': highlightEdit }"
                    class="text-black text-sm hover:underline transition-all duration-300 px-2 py-1 rounded"
                >
                <span x-text="editable ? 'Cancel' : 'Edit'"></span>
            </button>
            </div>

            <div id="myTabContent" class=" p-1.5 rounded-lg dark:bg-gray-800 ">

                <div class="" id="seller-manually" role="tabpanel" aria-labelledby="seller-manually-tab" >
                    <div class="mt-2">

                        @if($error)
                        <div x-data="{ show: true }"
                                x-init="setTimeout(() => show = false, 5000)"
                                x-show="show"
                                wire:key="error-{{ now() }}" {{-- Ensure this div is re-initialized by changing the key --}}
                                id="success-alert"
                                class="p-1.5 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                                {{ $error }}
                            </div>
                        @endif

                        @if($successMessage)
                        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)"
                            wire:key="successMessage-{{ now() }}"
                            x-show="show" id="success-alert" class="p-1.5 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                            {{ $successMessage }}
                        </div>
                        @endif

                                 {{-- @dump($updateProfileData); --}}
                            <div class="relative ">
                                <label for="special_id" class="block text-sm font-medium">User Code</label>
                                <div class="relative">
                                    <input type="text"  value="{{$updateProfileData['special_id']}}" name="special_id" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500" disabled>

                                    <button onclick="copyToClipboard('{{ $updateProfileData['special_id'] }}')" data-tooltip-target="tooltip-bottom-profile" data-tooltip-placement="bottom" class="absolute right-2 top-1/2 transform -translate-y-1/2 focus:outline-none cursor-pointer">
                                        <svg class="w-3.5 h-3.5 cursor-pointer" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20" title="Copy">
                                            <path d="M5 9V4.13a2.96 2.96 0 0 0-1.293.749L.879 7.707A2.96 2.96 0 0 0 .13 9H5Zm11.066-9H9.829a2.98 2.98 0 0 0-2.122.879L7 1.584A.987.987 0 0 0 6.766 2h4.3A3.972 3.972 0 0 1 15 6v10h1.066A1.97 1.97 0 0 0 18 14V2a1.97 1.97 0 0 0-1.934-2Z"></path>
                                            <path d="M11.066 4H7v5a2 2 0 0 1-2 2H0v7a1.969 1.969 0 0 0 1.933 2h9.133A1.97 1.97 0 0 0 13 18V6a1.97 1.97 0 0 0-1.934-2Z"></path>
                                        </svg>
                                    </button>
                                    <div id="tooltip-bottom-profile" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Copy
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>


                            </div>

                            <div>
                                <label for="seller_name" class="block text-sm font-medium ">Name <span class="text-red-600">*</span></label>
                                <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.name"  name="name" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus: ">
                            </div>
                            <div>
                                <label for="company_name" class="block text-sm font-medium ">Company Name</label>
                                <input type="text" x-bind:readonly="!editable"
                                    x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                                    @focus="attemptedEdit"
                                    @click="attemptedEdit" wire:model.defer="updateProfileData.company_name" name="company_name" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                            </div>
                            <div>
                                <div class="flex justify-between mt-1">
                                    <label for="phone" class="block text-sm font-medium">Phone Number</label>
                                @if ($phoneVerification)
                                <svg class="w-6 h-6 text-green-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                </svg>
                                @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="text" x-bind:readonly="!editable"
                                        x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                                        @focus="attemptedEdit"
                                        @click="attemptedEdit" wire:model.defer="updateProfileData.phone" name="phone" class="mt-1 p-2 h-8 block text-xs w-full rounded-md border-transparent focus:border-gray-500" disabled>
                                    @if(!$phoneVerification)
                                        @if(!$otpSent)
                                            <button wire:click="sendOTP('phone')" class="ml-2 p-1 rounded-md whitespace-nowrap bg-white text-black text-xs hover:bg-orange border border-black">Verify Phone</button>
                                        @else
                                            {{-- <button wire:click="sendOTP('phone')" class="ml-2 p-1 rounded-md whitespace-nowrap bg-white text-black text-xs hover:bg-orange border border-black" x-bind:disabled="resendTimer > 0" x-text="resendTimer > 0 ? `Resend OTP in ${resendTimer} seconds` : 'Resend OTP'">Resend</button> --}}
                                            <button wire:click="sendOTP('phone')" class="ml-2 p-1 rounded-md whitespace-nowrap bg-white text-black text-xs hover:bg-orange border border-black" @if($resendDisabled) disabled @endif>Resend OTP</button>
                                        @endif

                                    @endif
                                </div>

                                @if($otpSent)
                                    <div class="mt-2">
                                        <label for="otp" class="block text-sm font-medium">Enter OTP</label>
                                        <div class="flex items-center">
                                            <input type="text" wire:model.defer="otp" name="otp" class="mt-1 p-2 h-8 block text-xs w-full rounded-md border-transparent focus:border-gray-500 bg-yellow-100">
                                            <button wire:click="verifyPhoneOTP" class="ml-2 p-1 rounded-md whitespace-nowrap bg-green-500 text-white">Confirm OTP</button>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex justify-between">
                                    <label for="email" class="block text-sm font-medium">E-Mail</label>
                                @if ($emailVerification)
                                <svg class="w-6 h-6 text-green-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                  </svg>
                                @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model="updateProfileData.email" id="email" name="email" class="mt-1 p-2 h-8 block text-xs w-full rounded-md border-transparent focus:border-gray-500">
                                    @if(!$emailVerification)
                                        @if(!$emailOTPSent)
                                            <button wire:click="sendOTP('email')" class="ml-2 p-1 rounded-md whitespace-nowrap bg-white text-black text-xs hover:bg-orange border border-black">Verify Email</button>
                                        @else
                                            <button wire:click="sendOTP('email')" class="ml-2 p-1 rounded-md whitespace-nowrap bg-white text-black text-xs hover:bg-orange border border-black" @if($resendDisabled) disabled @endif>Resend OTP</button>
                                        @endif

                                    @endif
                                </div>

                                @if($emailOTPSent)
                                    <div class="mt-2">
                                        <label for="otp" class="block text-sm font-medium">Enter OTP</label>
                                        <div class="flex items-center">
                                            <input type="text" wire:model.defer="otp" name="otp" class="mt-1 p-2 h-8 block text-xs w-full rounded-md border-transparent focus:border-gray-500 bg-yellow-100">
                                            <button wire:click="verifyOTP" class="ml-2 p-1 rounded-md whitespace-nowrap bg-green-500 text-white">Confirm OTP</button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div>

                                <label for="address" class="block text-sm font-medium">Address <span class="text-red-600">*</span></label>
                                <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit"  wire:model.defer="address"  name="address" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                            </div>
                            <div>
                                <label for="pincode" class="block text-sm font-medium">Pincode <span
                                        class="text-red-600">*</span></label>
                                        <input type="text"x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.pincode"
                                        wire:keydown.enter="cityAndStateByPincode" wire:ignore.self id="pincode" name="pincode"
                                        class="mt-1 p-2 h-8 block text-xs w-full text-black rounded-md  border-transparent focus:border-gray-500 "
                                        placeholder="Area Pincode"
                                        oninput="if (this.value.length === 6) { @this.set('updateProfileData.pincode', this.value); @this.call('cityAndStateByPincode'); }">

                            </div>
                                <div>
                                    {{-- @dump($updateProfileData['city']) --}}
                                    <label for="city" class="block text-sm font-medium">City <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit"   wire:model.defer="city"  id="city"
                                        name="city"
                                        class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 "
                                        placeholder="City">
                                </div>
                                <div>
                                    <label for="state" class="block text-sm font-medium">State <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit"  wire:model.defer="state"  id="state"
                                        name="state"
                                        class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 "
                                        placeholder="State">
                                </div>
                    </div>
                </div>
            </div>
        </div>

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
            <div>
                <!-- Plus icon to toggle the input boxes -->
                <div class="relative mt-2">
                    <!-- <div class="mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-white dark:bg-gray-800"> -->
                    <div id="inputBoxes" class="hidden mt-2 bg-[#e9e6e6] p-3 rounded-lg">
                        <!-- Add your input boxes here -->
                        <div>
                            <label for="pancard" class="block text-sm font-medium">Pancard</label>
                            <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.pancard"  name="pancard" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                        </div>
                        <div>
                            <label for="gst_number" class="block text-sm font-medium">Gst Number</label>
                            <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.gst_number"  name="gst_number" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                        </div>

                        <div>
                            <label for="bank_name" class="block text-sm font-medium">Bank Name</label>
                            <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.bank_name" name="bank_name" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                        </div>
                        <div>
                            <label for="branch_name" class="block text-sm font-medium">Branch Name</label>
                            <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.branch_name"  name="branch_name" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                        </div>
                        <div>
                            <label for="bank_account_no" class="block text-sm font-medium">Bank Account Number</label>
                            <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.bank_account_no"  name="bank_account_no" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                        </div>
                        <div>
                            <label for="ifsc_code" class="block text-sm font-medium">IFSC Code.</label>
                            <input type="text" x-bind:readonly="!editable"
                               x-bind:class="{'bg-white': !editable, 'bg-[#aeaaaa]': editable}"
                               @focus="attemptedEdit"
                               @click="attemptedEdit" wire:model.defer="updateProfileData.ifsc_code"  name="ifsc_code" class="mt-1 p-2 h-8 block text-xs w-full rounded-md  border-transparent focus:border-gray-500 ">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @if(Auth::getDefaultDriver() !== 'team-user')
            <div class="flex justify-center">
                <button @click="editable = false" x-show="editable" type="button"  wire:click.prevent="updateData" class="rounded-full w-full bg-gray-900 px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">Update</button>
            </div>
        @endif

    </div>


</div>
{{-- <form> --}}
    @if (Auth::getDefaultDriver() !== 'team-user')
        @if (is_null(auth()->user()->address) || is_null(auth()->user()->pincode) || is_null(auth()->user()->state) || is_null(auth()->user()->city))
        <!-- Tailwind CSS modal container -->
        <div id="addressDetailsModal" class="fixed inset-0 z-50 max-w-2xl mx-auto flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <!-- Modal overlay -->
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <!-- Modal content -->
            <div class="relative mx-auto my-6  w-full">
                <!-- Your modal content here -->
                <div class="bg-white rounded-lg shadow-lg relative flex flex-col w-full outline-none focus:outline-none">
                    <div class="flex items-start justify-between p-5 border-b border-solid border-blueGray-200 rounded-t">
                        <h3 class="text-2xl font-semibold text-black">
                            Notification
                        </h3>
                        <button class="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none" onclick="closeModal()">
                            <span class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">×</span>
                        </button>
                    </div>
                    <div class="relative p-6 flex-auto">
                        <!-- Your notification message -->
                        <p>Please complete your profile before proceeding.</p>
                    </div>
                    <div class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
                        <button class="text-blue-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none" onclick="closeModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
<!-- Javascript function for Open inputs -->
<script>
    document.getElementById('toggleInput').addEventListener('click', function() {
        var inputBoxes = document.getElementById('inputBoxes');
        inputBoxes.classList.toggle('hidden');
    });

    function copyToClipboard(text) {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
    }
    function openModal() {
            document.getElementById('addressDetailsModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addressDetailsModal').style.display = 'none';
        }

        // Open the modal when the window loads
        window.onload = function () {
            openModal();
        };
        function startCountdown() {
            if (resendTimer.value > 0) {
                const intervalId = setInterval(() => {
                    if (resendTimer.value > 0) {
                        resendTimer.value--;
                    } else {
                        clearInterval(intervalId);
                    }
                }, 1000);
            }
        }

        // Watch for changes on resendTimer to start countdown
        $watch('resendTimer', value => {
            if (value === 120) { // Assuming 120 is the starting value
                startCountdown();
            }
        });

</script>
</div>
