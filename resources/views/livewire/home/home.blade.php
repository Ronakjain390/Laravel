<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Parchi | Login</title>
    <!-- Include Tailwind CSS -->
    <link rel="icon" href="https://theparchi.com/Vector.png" type="image/gif" sizes="16x16">
    <!-- Add custom styles -->
    {{-- @livewireStyles --}}

</head>

<body class="bg-black text-white">
    <div class="min-h-screen flex">
        <!-- Left Background -->
        <div class="flex-1 bg-[#8159a9]  sm:block">
            {{-- @if ($errorMessage)
            @foreach (json_decode($errorMessage) as $error)
            <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-500 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Error:</span> {{ $error[0] }}
            </div>
            @endforeach
            @endif
            @if ($successMessage)
                <div class="p-4 text-sm text-green-800 rounded-lg bg-green-500 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">Success:</span> {{ $successMessage }}
                </div>
            @endif --}}
        </div>
        <!-- Right Background -->
        <div class="flex-1 bg-[#bc6060]  sm:block"></div>
        <!-- Centered Content -->
        <div class="flex-1 flex items-center justify-center absolute inset-0 ">
            <div class="max-w-xl w-full p-6 md:p-8">
                <div class="bg-[#e7e7e7] rounded-lg border border-gray-200 p-4 shadow dark:border-gray-700 dark:bg-gray-800 sm:p-6 md:p-8 "
                    autocomplete="off">
                    <div class="text-center">
                        <a href="{{route('home')}}">
                            <img src="{{asset('image/Vector.png')}}" class="h-52 mx-auto mb-6" />
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 mb-4">
                            <input type="text"
                                class="w-full hsn-box font-mono text-xs font-normal text-black focus:outline-none"
                                wire:model.prevent='loginCred.email_or_phone' autocomplete="false"
                                placeholder="E-mail or Mobile Number" required value="" />
                        </div>
                        <div class="col-span-12 mb-4">
                            <div class="relative">
                                <input type="password"
                                    class="w-full hsn-box font-mono text-xs font-normal text-black focus:outline-none pr-10"
                                    wire:model.prevent='loginCred.pass' autocomplete="false" placeholder="Password"
                                    required />
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <button type="button" onclick="togglePasswordVisibility()" class="text-[#BC6060]">
                                        <i id="passwordToggle" class="fa fa-eye"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-span-3 mb-4 text-left">
                            <a class="text-[#BC6060] hover:text-black"><small class="whitespace-nowrap">Login with
                                    OTP</small></a>
                        </div>
                        <div class="col-span-9 mb-4 text-right">
                            <a href="https://theparchi.com/customuser/reset"
                                class="text-gray-500 hover:text-black"><small class="l-your-pass">Lost your
                                    password?</small></a>
                        </div>
                        <div class="col-span-12 mb-4">
                            {{-- <button type="button" wire:click.prevent='userLogin' class="w-full rounded-full bg-black py-1 text-lg font-semibold text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50">Login</button> --}}
                        </div>
                        {{-- <div class="col-span-12 mb-4 text-center">
                            <a href="{{ route('register') }}" class="text-muted hover:text-black"><small
                                    class="font-semibold">Not a User? SIGN UP</small></a>
                        </div>
                        <div class="col-span-12 text-center">
                            <a href="https://theparchi.com/sub_seller/sub-user-login"
                                class="text-muted hover:text-black"><small class="font-semibold">Login as Sub
                                    User</small></a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript libraries -->

    {{-- @livewireScripts --}}

</body>

</html>
