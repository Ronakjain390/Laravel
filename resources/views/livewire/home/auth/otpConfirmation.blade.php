{{-- <div class="flex h-screen w-screen" x-data="{ showMessage: true }" x-init="setTimeout(() => showMessage = false, 7000)">
    <div class="flex-1 bg-[#8159a9]"></div>
    <div class="flex-1 bg-[#bc6060]"></div>
    <div class="absolute inset-0 flex items-center justify-center h-20rem">
        <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
            <div class="border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                <div class="col-span-12 place-content-evenly gap-4">
                    <div class="container flex justify-center" x-show="showMessage">
                        @if ($errorMessage)
                            @foreach (json_decode($errorMessage) as $error)
                                <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-500 dark:bg-gray-800 dark:text-red-400"
                                    role="alert">
                                    <span class="font-medium">Error:</span> {{ $error }}
                                </div>
                            @endforeach
                        @endif

                        @if ($successMessage)
                            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-500 dark:bg-gray-800 dark:text-green-400"
                                role="alert">
                                <span class="font-medium">Success:</span> {{ $successMessage }}
                            </div>
                        @endif
                    </div>
                    <div class="container flex justify-center">
                        <a href="{{route('home')}}">
                            <img src="{{asset('image/Vector.png')}}" class="splash-logo h-56 " />
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-4 mt-2">
                        <div class="col-span-12 mb-3 md:col-span-12">
                            <input type="text"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full @error('otp') border-red-500 @enderror"
                                wire:model="otp" placeholder="OTP" value="" />
                            @error('otp')
                                <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-span-12 mb-3 text-left md:col-span-6">
                            <a href="{{route('login')}}" class="text-[#BC6060]"><small>Login with Password</small></a>
                        </div>
                        <div class="col-span-12 mb-3 text-right md:col-span-6">
                            <a href="https://theparchi.com/customuser/reset" class="text-gray-500"><small
                                    class="l-your-pass">Lost your password?</small></a>
                        </div>
                        <div class="col-span-12">
                            <button wire:click='validateOTPForLogin'
                                class="mb-3 w-full rounded-full bg-black py-1 text-xs text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                type="submit">LOGIN WITH OTP</button>
                        </div>
                        <div class="col-span-12 flex justify-center">
                            <a class="submit login-but text-center text-[#414344] hover:underline"
                                href="{{route('register')}}">Not a User? SIGN UP</a>
                        </div>
                        <div class="col-span-12 flex justify-center m-2">
                            <a class="submit login-but text-center text-[#414344] hover:underline"
                                href="https://theparchi.com/customuser/e-login">Login as Sub User</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="flex h-screen w-screen">
    <div class="flex-1 bg-[#8159a9]"></div>
    <div class="flex-1 bg-[#bc6060]"></div>
    <!-- Centered Content (positioned absolute) -->
    <div class="absolute inset-0 flex items-center justify-center h-20rem">
        <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
            <div
                class="border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                <div class="col-span-12 place-content-evenly gap-4">
                    <div class="container flex justify-center">
                        @if ($errorMessage)
                            <div x-show="showMessage" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-500 dark:bg-gray-800 dark:text-red-400" role="alert">
                                <span class="font-medium">Error:</span> {{ $errorMessage }}
                            </div>
                        @endif

                        @if ($successMessage)
                            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-500 dark:bg-gray-800 dark:text-green-400"
                                role="alert">
                                <span class="font-medium">Success:</span> {{ $successMessage }}
                            </div>
                        @endif
                    </div>

                    <div class="container flex justify-center">

                        <a href="{{route('home')}}">
                            <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-12" src="{{asset('image/Vector.png')}}" alt="TheParchi" />
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-4 mt-2">
                        <div class="col-span-12 mb-3 md:col-span-12">
                            <input type="text"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full @error('otp') border-red-500 @enderror"
                                wire:model="otp" placeholder="OTP" value="" />
                            @error('otp')
                                <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-span-12 mb-3 text-left md:col-span-6">
                            <a href="{{route('login')}}" class="text-[#BC6060]"><small>Login with Password</small></a>
                        </div>
                        <div class="col-span-12 mb-3 text-right md:col-span-6">
                            <a href="https://theparchi.com/customuser/reset" class="text-gray-500"><small
                                    class="l-your-pass">Lost your password?</small></a>
                        </div>
                        <div class="col-span-12">
                            <button wire:click='validateOTPForLogin'
                                class="mb-3 w-full rounded-full bg-black py-1 text-xs text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                type="submit">LOGIN WITH OTP</button>
                        </div>
                        <div class="col-span-12 flex justify-center">
                            <a class="submit login-but text-center text-[#414344] hover:underline"
                                href="{{route('register')}}">Not a User? SIGN UP</a>
                        </div>
                        <div class="col-span-12 flex justify-center m-2">
                            <a class="submit login-but text-center text-[#414344] hover:underline"
                            href="{{ route('teamlogin') }}">Login as Sub User</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>