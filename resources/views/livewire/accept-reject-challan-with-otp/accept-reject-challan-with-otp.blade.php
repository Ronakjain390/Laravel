<div>
    <div class="absolute inset-0 flex items-center justify-center h-20rem">
        @if(session('Action Performed'))
        {{-- @dump($challanId); --}}
        <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
            <div
                class=" border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800  sm:p-6 md:p-8">
                <div class="col-span-12 place-content-evenly gap-4">

                    <div class="container flex justify-center">
                        <a href="{{ route('home') }}">
                            <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-10" src="{{asset('image/Vector.png')}}"
                                alt="TheParchi" />
                        </a>
                    </div>
                    <p class="text-xs text-black text-center">This {{$challanHeading ?? 'Challan'}} has been {{$status}}ed. </p>
                    <div class="card-actions justify-end">
                        <a href="{{route('login')}}" class="text-black text-center mx-auto mt-5 underline">Back to
                            Home</a>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- <div class="mx-auto w-full md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl sm:w-36"> --}}
            <div class="mx-auto md:w-1/2 lg:w-5/12 xl:w-3/12 max-w-xl w-80">
                {{-- @dump($challanId); --}}
                <div
                    class=" border border-gray-200 bg-[#e7e7e7] p-4 shadow dark:border-gray-700 dark:bg-gray-800  sm:p-6 md:p-8">
                    <div class="col-span-12 place-content-evenly gap-4">

                        <div class="container flex justify-center">
                            <a href="{{ route('home') }}">
                                <img class="w-40 mb-8 md:mb-12 mt-8 md:mt-10" src="{{asset('image/Vector.png')}}"
                                    alt="TheParchi" />
                            </a>
                        </div>



                        <div x-data="{ showModal: false }">
                            <p class="text-sm text-black p-1.5 text-center">theparchi.com <b>{{$challanHeading ?? 'Challan'}}</b> Received from <b>{{$sender}}.</b></p>
                            <div class="col-span-12 flex">
                                <button x-on:click="showModal = true"  wire:click="acceptReject('accept')"
                                    class="mb-3 w-full rounded-md bg-black py-1 text-xs text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8 mr-2"
                                    type="button">ACCEPT</button>
                                <button x-on:click="showModal = true"  wire:click="acceptReject('reject')"
                                    class="mb-3 w-full rounded-md bg-black py-1 text-xs text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                    type="button">REJECT</button>
                            </div>
                            <!-- Modal -->

                            @if ($showOtpModal)
                            <div x-show="showModal" x-on:click.outside="showModal = false"
                                class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
                                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>
                                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all">
                                    <!-- Modal content -->
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                @if ($successMessage)
                                                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" class="p-1 text-xs text-green-800 rounded-lg bg-green-500 dark:bg-gray-800 dark:text-green-400"
                                                    role="alert">
                                                    <span class="text-xs"></span> {{ $successMessage }}
                                                </div>
                                                @endif
                                                @if ($errorMessage)
                                                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" class="p-1 text-xs text-red-800 rounded-lg bg-red-500 dark:bg-gray-800 dark:text-red-400"
                                                    role="alert">
                                                    <span class="text-xs"></span> {{ $errorMessage }}
                                                </div>
                                                @endif
                                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                                    Verify Yourself
                                                </h3>
                                                <p class="text-xs text-black">Enter the OTP sent to your email/Phone</p>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500">
                                                        <input type="text" class="hsn-box font-mono text-xs font-normal text-black focus:outline-none w-full rounded-lg"
                                                        wire:model="otp" placeholder="OTP" value="" />
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse flex">
                                        <button x-on:click="showModal = false"
                                            class="w-full items-center mr-2 h-8 px-2 py-2 inline-flex justify-center rounded-md border border-transparent shadow-sm   bg-black text-xs font-medium text-[#E5F811] hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm"
                                            type="button">
                                            Close
                                        </button>
                                        <button wire:click='validateOTPForLogin'
                                        class=" px-2 w-full rounded-lg bg-black py-1 text-xs text-[#E5F811] shadow-md hover:shadow-lg focus:ring focus:ring-opacity-50 h-8"
                                        type="submit">Verify OTP</button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
          window.showModal = false;
        });
    </script>
</div>

