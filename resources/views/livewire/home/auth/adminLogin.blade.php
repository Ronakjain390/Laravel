{{-- <div class="flex h-screen w-screen">
    <div class="flex-1 bg-[#8159a9]"></div>
    <div class="flex-1 bg-[#bc6060]"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="mx-auto w-3/12 max-w-xl">
            <div
                class="rounded-lg border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                <div class="col-span-12 place-content-evenly gap-4">
                     <div class="container flex justify-center">
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

                        <a href="{{ route('home') }}">
                            <img src="{{asset('image/Vector.png')}}" class="splash-logo h-56 " />
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-4 mt-2">
                        <div class="col-span-12 mb-3 md:col-span-12">
                            <input type="email"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full"
                                wire:model='emailOrPhone' name="email" placeholder="E-mail or Mobile Number"
                                value="" />
                            <small class="text-muted emailError hidden">Please enter a valid email address</small>
                        </div>
                        <div class="col-span-12 mb-3 md:col-span-12">
                            <input type="password" id="password"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full"
                                wire:model='password' name="password" placeholder="Password" required />
                        </div>
                        <div class="mb-md-4  mb-3 text-left col-span-3 whitespace-nowrap ">
                            <a href="{{ route('otplogin') }}" class="text-[#BC6060]"><small>Login with OTP</small></a>
                        </div>
                        <div class="col text-right mb-3 mb-md-4 col-span-9">
                            <a href="https://theparchi.com/customuser/reset" class="text-gray-500"><small
                                    class="l-your-pass">Lost your password?</small></a>
                        </div>
                        <div class="col-span-12">
                            <button
                                class="mb-3 w-full rounded-full bg-black py-1 text-lg font-semibold text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                wire:click='userLogin' type="submit">Login</button>
                        </div>
                        <div class="col-span-12 flex justify-center">
                            <a href="{{ route('register') }}"
                                class="submit login-but text-center text-[#414344] hover:underline"
                                href="https://theparchi.com/customuser/e-login">Not a User ? SIGN UP</a>
                        </div>
                        <div class="col-span-12 flex justify-center m-2">
                            <a class="submit login-but text-center text-[#414344] hover:underline"
                                href="{{ route('teamlogin') }}">Login as Team Member</a>
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
    <div class="absolute inset-0 flex items-center justify-center">
        {{-- <div class="mx-auto w-full md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl sm:w-36"> --}}
            <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
            <div class="rounded-lg border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800  sm:p-6 md:p-8">
                <div class="col-span-12 place-content-evenly gap-4">
                    <div class="container flex justify-center">
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
                        <a href="{{ route('home') }}">
                            <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-12" src="{{asset('image/Vector.png')}}" alt="TheParchi" />
                        </a>
                        ADMIN
                    </div>
                    <div class="grid grid-cols-12 gap-4 mt-2">
                        <div class="col-span-12 mb-3 md:col-span-12">
                            <input type="email"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full"
                                wire:model='emailOrPhone' name="email" placeholder="E-mail or Mobile Number"
                                value="" />
                            <small class="text-muted emailError hidden">Please enter a valid email address</small>
                        </div>
                        <div class="col-span-12 mb-3 md:col-span-12">
                            <input type="password" id="password"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full"
                                wire:model='password' name="password" placeholder="Password" required />
                        </div>
                        <div class="mb-md-4  mb-3 text-left col-span-3 whitespace-nowrap ">
                            <a href="{{ route('otplogin') }}" class="text-[#BC6060]"><small>Login with OTP</small></a>
                        </div>
                        <div class="col text-right mb-3 mb-md-4 col-span-9">
                            <a href="https://theparchi.com/customuser/reset" class="text-gray-500"><small
                                    class="l-your-pass">Lost your password?</small></a>
                        </div>
                        <div class="col-span-12">
                            <button
                                class="mb-3 w-full rounded-full bg-black py-1 text-lg font-semibold text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                wire:click='adminLogin' type="submit">Login</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
