<div class="flex h-screen w-screen">
    <div class="flex-1 bg-[#8159a9]"></div>
    <div class="flex-1 bg-[#bc6060]"></div>
    <!-- Centered Content (positioned absolute) -->
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
            <div class="border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                <div class="col-span-12 grid gap-4">
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
                                {{-- <span class="font-medium">Success:</span> {{ $successMessage }} --}}
                            </div>
                        @endif
                    </div>
                    <div class="container flex justify-center">
                        <a href="{{ route('home') }}">
                            <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-12" src="{{asset('image/Vector.png')}}" alt="TheParchi" />
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

                        <div class="col-span-12 mb-3 md:col-span-12 relative" x-data="{ showPassword: false }">
                            <input :type="showPassword ? 'text' : 'password'" id="password"
                                class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full pr-10"
                                wire:model='password' name="password" placeholder="Password" required />
                            <button x-cloak type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">

                                <svg :class="{'hidden': showPassword, 'block': !showPassword }" class="h-5 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                    <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                  </svg>

                                  <svg :class="{'block': showPassword, 'hidden': !showPassword }" class="h-5 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.933 13.909A4.357 4.357 0 0 1 3 12c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 21 12c0 1-3 6-9 6-.314 0-.62-.014-.918-.04M5 19 19 5m-4 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                  </svg>
                            </button>
                        </div>

                        <div class="col-span-12">
                            <button
                                class="mb-3 w-full rounded-full bg-black  text-lg font-semibold text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                wire:click='userLogin' type="submit">Login</button>
                        </div>

                        <div class="col-span-12 flex justify-center m-2 mb-10">
                            <a class="submit login-but text-center text-[#414344] hover:underline"
                                href="{{ route('login') }}">Login as Admin</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
