{{-- <div class="login relative bg-black text-white">
    <div class="container h-screen max-w-none">
        <div class="flex h-full">
            <!-- Left Column -->
            <div class="w-4/12 bg-[#5ccf8a]"></div>
            <div class="w-2/4 bg-[#bc6060]"></div>



            <!-- Grid Section -->
            <div class="grid w-full grid-flow-col grid-rows-3 bg-[#8159a9]">
                <div class="... row-span-3"></div>
                <div class="col-span-2 row-span-2 bg-[#d16d4e]"></div>
                <div class="col-span-2 bg-[#b3bd3a]"></div>
            </div>

            <div class="absolute inset-0 flex items-center justify-center">
                <div class="mx-auto w-3/12 max-w-xl">
                    <div
                        class="rounded-lg border border-gray-200 bg-[#e7e7e7]  shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                        <div class="col-span-12 grid place-content-evenly ga" name="myform">
                            @if ($errorMessage)
                                @foreach (json_decode($errorMessage) as $error)
                                    <div class="container flex justify-center">

                                        <div class="row  text-sm  mb-1 rounded-lg bg-red-500 dark:bg-gray-800 dark:text-red-500"
                                            role="alert">
                                            <span class="text-sm">Error:</span> {{ $error[0] }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            @if ($successMessage)
                                <div class="container flex justify-center">

                                    <div class="row  text-sm text-green-800 rounded-lg bg-green-500 dark:bg-gray-800 dark:text-green-400"
                                        role="alert">
                                        <span class="text-sm">Success:</span> {{ $successMessage }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="container flex justify-center">
                            <a href="{{route('home')}}">
                                <img src="{{asset('image/Vector.png')}}" class="splash-logo h-60" />
                            </a>
                        </div>
                        <div class="grid grid-cols-12 ga mt-2">
                            <div class="col-span-12 ">
                                <input  value="" wire:model="name"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black  text-xs w-full"
                                    type="text" placeholder="Name">
                                <small class="text-muted usernameError hidden">Name can only contain alphabets,
                                    numbers, or spaces</small>
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 ">
                                <input  type="email"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black  text-xs w-full"
                                    wire:model="email" placeholder="E-mail" value="" />
                                <small class="text-muted emailError hidden">Please enter a valid email
                                    address</small>
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 ">
                                <input  type="number"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black  text-xs w-full"
                                    wire:model="phone" max="999999999999" placeholder="Phone Number" value="" />
                                <small class="text-muted phoneError hidden">Phone number should be 10 digits
                                    only</small>
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 ">
                                <input  type="password" id="password"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black  text-xs w-full"
                                    wire:model="password" placeholder="Password" required />
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 ">
                                <input  type="password" id="confirm_password"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black  text-xs w-full"
                                    wire:model="password_confirmation" placeholder="Confirm Password" required />
                                <div class="">
                                    <small class="text-muted passwordError hidden">Passwords don't match! Please try
                                        again.</small>
                                </div>
                            </div>
                            <div class="col-span-12">
                                <button wire:click='userRegister'
                                    class="mb-3 w-full bg-black py-1 text-lg font-semibold text-[#109B3B] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 rounded-full"
                                    type="submit">Register</button>
                            </div>
                            <div class="col-span-12  flex justify-center h-8">
                                <a class="submit login-but  text-center text-[#414344] hover:underline"
                                    href="{{ route('login') }}">Log in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div> --}}


<div class="login relative text-white">
    <div class="container h-screen max-w-none">
        <div class="flex h-full">
            <!-- Left Column -->
            <div class="hidden md:block w-4/12 bg-[#5ccf8a]"></div>
            <div class="hidden md:block w-2/4 bg-[#bc6060]"></div>

            <!-- Grid Section -->
            <div class="grid w-full grid-flow-col grid-rows-3 bg-[#8159a9]">
                <div class="... row-span-3"></div>
                <div class="hidden md:block col-span-2 row-span-2 bg-[#d16d4e]"></div>
                <div class="hidden md:block col-span-2 bg-[#b3bd3a]"></div>
            </div>

            <div class="absolute inset-0 flex items-center justify-center">
                <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
                    <div class=" border border-gray-200 bg-[#e7e7e7] shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8">
                        <div class="col-span-12 grid place-content-evenly ga" name="myform">
                            @php
                                $data = json_decode($errorMessage);
                                // dd($data->name[0]);
                            @endphp
                           
                            @if ($successMessage)
                                <div class="container flex justify-center">
                                    <div
                                        class="row  text-sm text-green-800 rounded-lg bg-green-500 dark:bg-gray-800 dark:text-green-400"
                                        role="alert">
                                        <span class="text-sm">Success:</span> {{ $successMessage }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="container flex justify-center">
                            <a href="{{ route('home') }}">
                                <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-12" src="{{asset('image/Vector.png')}}" alt="TheParchi" />
                            </a>
                        </div>
                        <div class="grid grid-cols-12 md:col-span-12">
                            <div class="col-span-12 mb-3 md:col-span-12 px-4">
                                <input value="" wire:model="name"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black text-xs w-full rounded @error('name') border-red-500 @enderror"
                                    type="text" placeholder="Name">
                                @error('name') 
                                    <small class="text-red-500">{{ $message }}</small> 
                                @enderror
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 px-4">
                                <input type="email"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black text-xs w-full rounded @error('email') border-red-500 @enderror"
                                    wire:model="email" placeholder="E-mail" value="" />
                                <small class="text-muted emailError hidden">Please enter a valid email address</small>
                                @error('email') 
                                    <small class="text-red-500">{{ $message }}</small> 
                                @enderror
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 px-4">
                                <input type="number"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black text-xs w-full rounded @error('phone') border-red-500 @enderror"
                                    wire:model="phone" max="999999999999" placeholder="Phone Number" value="" />
                                <small class="text-muted phoneError hidden">Phone number should be 10 digits only</small>
                                @error('phone') 
                                    <small class="text-red-500">{{ $message }}</small> 
                                @enderror
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 px-4">
                                <input type="password" id="password"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black text-xs w-full rounded @error('password') border-red-500 @enderror"
                                    wire:model="password" placeholder="Password" required />
                                @error('password') 
                                    <small class="text-red-500">{{ $message }}</small> 
                                @enderror
                            </div>
                            <div class="col-span-12 mb-3 md:col-span-12 px-4">
                                <input type="password" id="confirm_password"
                                    class="focus:outline-none font-mono font-normal hsn-box text-black text-xs w-full rounded @error('password_confirmation') border-red-500 @enderror"
                                    wire:model="password_confirmation" placeholder="Confirm Password" required />
                                @error('password_confirmation') 
                                    <small class="text-red-500">{{ $message }}</small> 
                                @enderror
                            </div>
                            <div class="col-span-12 px-4 py-4">
                                <button id="registerButton" wire:click='userRegister'
                                    class="mb-3 w-full bg-black @if($errors->any()) disabled cursor-not-allowed bg-gray-300 @endif py-1 text-lg font-semibold text-[#109B3B] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 rounded-full"
                                    type="submit" @if($errors->any()) disabled @endif onclick="disableButton()">Register</button>
                            </div>
                            <div class="col-span-12 flex justify-center h-8 mb-8">
                                <a class="submit login-but text-center text-[#414344] hover:underline"
                                    href="{{ route('login') }}">Log in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function disableButton() {
            document.getElementById('registerButton').disabled = true;
        }
    </script>
</div>
