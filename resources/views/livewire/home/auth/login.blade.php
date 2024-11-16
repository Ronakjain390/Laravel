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
    <div class="absolute inset-0 flex items-center justify-center h-20rem">
            <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
                <div class=" border border-gray-200 bg-[#e7e7e7] px-4 shadow dark:border-gray-700 dark:bg-gray-800  sm:p-6 md:p-8">
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
                            @if(session('Challan'))
                            <div class="alert alert-success py-1 rounded-lg">
                                Challan{{ ucfirst(session('Challan')) . 'ed' }}
                            </div>
                            @endif
                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif


                        </div>
                        <div class="container flex justify-center">
                            <a href="{{ route('home') }}">
                                <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-10" src="{{asset('image/Vector.png')}}" alt="TheParchi" />
                            </a>
                        </div>
                        <div class="grid grid-cols-12 gap-4 mt-2">
                            <div class="col-span-12 mb-3 md:col-span-12">
                                <input type="email"
                                    class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full rounded"
                                    wire:model='emailOrPhone' name="email" placeholder="E-mail or Mobile Number"
                                    value="" />
                                <small class="text-muted emailError hidden">Please enter a valid email address</small>
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 relative">
                                <input type="password" id="password"
                                    class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full rounded pr-10"
                                    wire:model='password' name="password" placeholder="Password" required />

                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center " id="togglePassword">
                                    <svg class="h-4 w-4 text-gray-500 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2 12s3-6 10-6 10 6 10 6-3 6-10 6-10-6-10-6z"></path>
                                    </svg>
                                </div>


                            </div>

                            <div class="mb-md-4  mb-3 text-left col-span-3 whitespace-nowrap ">
                                <a href="{{ route('otplogin') }}" class="text-[#BC6060]"><small>Login with OTP</small></a>
                            </div>
                            <div class="col text-right mb-3 mb-md-4 col-span-9">
                                <a href="{{route('forgotpassword')}}" class="text-gray-500"><small
                                        class="l-your-pass">Lost your password?</small></a>
                            </div>
                            <div class="col-span-12">
                                <button
                                    class="mb-3 w-full rounded-full bg-black pb-2 text-lg font-semibold text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                    wire:click='userLogin' type="submit">Login</button>
                            </div>
                            <div class="col-span-12 flex justify-center">
                                <a href="{{ route('register') }}"
                                    class="submit login-but text-center text-[#414344] hover:underline"
                                    href="https://theparchi.com/customuser/e-login">Not a User ? SIGN UP</a>
                            </div>
                            <div class="col-span-12 flex justify-center m-2 mb-10 ">
                                <a class="submit login-but text-center text-[#414344] hover:underline"
                                    href="{{ route('teamlogin') }}">Login as Team Member</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });
    </script>
    </div>


{{-- <div class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <form class="space-y-6" action="#">
        <h5 class="text-xl font-medium text-gray-900 dark:text-white">Sign in to our platform</h5>
        <div>
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="name@company.com" required>
        </div>
        <div>
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your password</label>
            <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
        </div>
        <div class="flex items-start">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" required>
                </div>
                <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Remember me</label>
            </div>
            <a href="#" class="ms-auto text-sm text-blue-700 hover:underline dark:text-blue-500">Lost Password?</a>
        </div>
        <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login to your account</button>
        <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
            Not registered? <a href="#" class="text-blue-700 hover:underline dark:text-blue-500">Create account</a>
        </div>
    </form>
</div> --}}

