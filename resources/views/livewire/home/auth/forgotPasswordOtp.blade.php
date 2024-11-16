<div class="flex h-screen w-screen">
    <div class="flex-1 bg-[#8159a9]"></div>
    <div class="flex-1 bg-[#bc6060]"></div>
    <!-- Centered Content (positioned absolute) -->
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
            <div
                class="border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                <div class="col-span-12 place-content-evenly gap-4">
                    
                    <div class="container flex justify-center">
                        <a href="{{route('home')}}">
                            <img src="{{asset('image/Vector.png')}}" class="splash-logo h-56 " />
                        </a>
                    </div>
                    <div x-data="{ otp: '', isOtpValid() { return this.otp.length === 4; } }" class="grid grid-cols-12 gap-4 mt-2">
                        <div class="col-span-12 mb-3 md:col-span-12 mt-4">
                            <input type="number"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full"
                                x-model="otp" wire:model="otp" placeholder="OTP" value="" />
                            <div class="container flex justify-center mb-2 rounded text-sm text-red-600">
                                @if($errorMessage)
                                    <div class="alert-danger">
                                        <strong class="font-medium">{{ $errorMessage }}</strong>
                                        @if(!empty($errorDetails))
                                            <ul>
                                                @foreach($errorDetails as $field => $errors)
                                                    @foreach($errors as $error)
                                                        <li class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-500 dark:bg-gray-800 dark:text-red-400">{{ $error }}</li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endif 
                            </div>
                        </div>
                    
                        <div class="col-span-12">
                            <button wire:click='validateOTPForLogin'
                                class="mb-3 w-full rounded-full py-1 text-xs text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                type="submit" x-bind:disabled="!isOtpValid()" :class="{'bg-gray-700': !isOtpValid(), 'bg-black': isOtpValid()}" wire:loading.attr="disabled">
                                Verify OTP
                            </button>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
