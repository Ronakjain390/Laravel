<div class="flex h-screen w-screen">
    <div class="flex-1 bg-[#8159a9]"></div>
    <div class="flex-1 bg-[#bc6060]"></div>
    <!-- Centered Content (positioned absolute) -->
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
            <div class="border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                {{-- <form method="POST" class="col-span-12 place-content-evenly gap-4" name="myform" x-on:submit="return formValidation(this);"> --}}
                    <div class="container flex justify-center">
                        <a href="{{ route('home') }}">
                            <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-12" src="{{asset('image/Vector.png')}}" alt="Bonnie image" />
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-4 mt-10">
                     
                        <div class="col-span-12 mb-3 md:col-span-12">
                            <input type="text"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full rounded"
                                wire:model="emailOrPhone" placeholder="Email Or Mobile Number" value="" />
                            <small class="text-muted emailError hidden">Please enter a valid phone number</small>
                        </div>
                        {{-- <div class="col-span-12 mb-3 text-left md:col-span-6">
                            <a href="{{ route('otplogin') }}" class="text-[#BC6060]">Login with OTP</a>
                        </div> --}}
                        <div class="col-span-12 mb-3 text-center">
                            <a  href="{{ route('login') }}" class="text-gray-500">Log in</a>
                        </div>
                        <div class="col-span-12">
                            <button wire:click='sendOtp' class="mb-3 w-full rounded-full bg-black py-1 text-xs text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8" type="submit">SEND OTP</button>
                        </div>
                        <div class="col-span-12 flex justify-center">
                            <a class="submit login-but text-center text-[#414344] hover:underline" href="https://theparchi.com/customuser/e-login">Not a User? SIGN UP</a>
                        </div>
                        <div class="col-span-12 flex justify-center m-2">
                            <a class="submit login-but text-center text-[#414344] hover:underline" href="https://theparchi.com/customuser/e-login">Login as Sub User</a>
                        </div>
                    </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>
