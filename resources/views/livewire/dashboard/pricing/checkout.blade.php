{{-- @unless (!session()->has('cart.items'))
    {{redirect()->route('pricing')}}

@endunless --}}
{{-- @if (session()->has('cart.items'))
    @php $cartItemsCount = count(session('cart.items')); @endphp

    <p>Number of items in the cart: {{ $cartItemsCount }}</p>
@endif --}}
<div class="bg-gray-100">
    <div class="max-w-6xl mx-auto text-black">
        <div class="">
            {{-- <div class="flex flex-row items-center mx-0">
                <div class="w-full py-1">
                    <p class="text-xl text-left font-semibold flex flex-col md:flex-row mx-0 md:mx-5 sm:pl-10 billing-details">Bill To</p>
                </div>
            </div> --}}
            @if ($errorMessage)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                {{ $errorMessage }}
                {{-- @if ($errorData)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-xs text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">

                <ul>
                    @foreach (json_decode($errorData) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
            </div>
        @endif
            {{-- @if($error)
            <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Error:</span> {{ $error }}
            </div>
            @endif --}}
            <div class="flex flex-col md:flex-row mx-0 md:mx-5 mt-2 md:mt-4  billing-details bg-white px-2">

                <div class="md:w-1/2">
                    <div class="flex flex-col">
                        <div class="flex flex-row">
                            <div class="w-1/3">
                                <p class="left-text font-semibold">Name:</p>
                            </div>
                            <div class="w-2/3">
                                <p class="right-text text-muted">{{ Auth::user()->company_name ?? (Auth::user()->name ?? Auth::user()->team_user_name) }}</p>
                            </div>
                        </div>
                        <div class="flex flex-row">
                            <div class="w-1/3">
                                <p class="left-text font-semibold">Address:</p>
                            </div>
                            <div class="w-2/3">
                                <p class="right-text text-muted">{{ Auth::user()->address ?? '' }}</p>
                            </div>
                        </div>
                        <div class="flex flex-row">
                            <div class="w-1/3">
                                <p class="left-text font-semibold">Pincode:</p>
                            </div>
                            <div class="w-2/3">
                                <p class="right-text text-muted">{{ Auth::user()->pincode ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <div class="flex flex-col">
                        <div class="flex flex-row">
                            <div class="w-1/3">
                                <p class="left-text font-semibold">City:</p>
                            </div>
                            <div class="w-2/3">
                                <p class="right-text text-muted">{{ Auth::user()->city ?? '' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-row">
                            <div class="w-1/3">
                                <p class="left-text font-semibold">State:</p>
                            </div>
                            <div class="w-2/3">
                                <p class="right-text text-muted">{{ Auth::user()->state ?? '' }}</p>
                            </div>
                        </div>
                        {{-- <div class="flex flex-row">
                            <div class="w-1/3">
                                <p class="left-text font-semibold">GST:</p>
                            </div>
                            <div class="w-2/3">
                                <p class="right-text text-muted" wire:ignore.self>
                                    @if ($showInput)
                                        <form  wire:submit="saveGstNumber">

                                        <input wire:model.defer="userData.gst_number"
                                            class="hsn-box h-7 rounded-lg bg-gray-300 text-center text-xs font-normal text-black focus:outline-none"
                                            type="text" />
                                        <button type="submit" class="bg-[#007bff] text-white h-7 w-1/3 rounded-xl">Save</button>
                                        </form>
                                        @else
                                        @if (Auth::user()->gst_number)
                                            {{ Auth::user()->gst_number }}
                                        @else
                                            <a class="text-[#17a2b8]" wire:click.prevent="toggleInput">Add GST
                                                Number</a>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div> --}}
                        <div class="flex flex-row">
                            <div class="w-1/3">
                                <p class="left-text font-semibold">GST:</p>
                            </div>
                            <div class="w-2/3">
                                <p class="right-text text-muted" wire:ignore.self>
                                    @if ($showInput)

                                        @else
                                        @if (Auth::user()->gst_number)
                                            {{ Auth::user()->gst_number }}
                                        @else
                                            <a data-modal-target="authentication-modal" data-modal-toggle="authentication-modal" class="text-[#17a2b8]" >Add GST
                                                Number</a>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @if(!$showInput)
            <!-- Main modal -->
            <div id="authentication-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore.self>
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Add Gst Number
                            </h3>
                            <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-3">
                            <form class="space-y-4" wire:submit.prevent="updateProfile">
                                <div>
                                    <label for="company_name " class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Name </label>
                                    <input type="text" name="company_name " id="company_name " wire:model.defer="company_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="name@company.com" required />
                                </div>
                                <div>
                                    <label for="gst_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gst Number</label>
                                    <input type="text" name="gst_number" id="gst_number" wire:model.defer="gst_number" placeholder="123551121212112" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required />
                                </div>
                                <div>
                                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Registered Address</label>
                                    <input type="text" name="address" id="address" wire:model.defer="address" placeholder="Registered Address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required />
                                </div>
                                <div class="flex gap-3">
                                    <div>
                                    <label for="pincode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pincode</label>
                                    <input type="text" name="pincode" id="pincode" wire:model="pincode" placeholder="123456" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required />

                                    </div>
                                    <div>
                                        <label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City </label>
                                    <input type="text" name="city" wire:click="cityAndStateByPincode" id="city" wire:model.defer="city" placeholder="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required />

                                    </div>
                                </div>

                                <div>
                                    <label for="state" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">State </label>
                                    <input type="text" name="state" id="state" wire:model.defer="state" placeholder="state" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required />
                                </div>

                                <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if (session()->get('cart.items') != null)
            <div class="flex flex-col    mx-0 md:mx-5  space-y-4  dark:bg-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold w-full border-t border-gray-600"></h2>
                <ul class="flex flex-col divide-y dark:divide-gray-700">
                    @foreach ($plans as $index => $plan)
                    {{-- @dump( $plan); --}}
                    <li class="flex flex-col sm:flex-row sm:justify-between bg-white px-2 mt-2">
                        <div class="flex w-full space-x-2 sm:space-x-4">
                            {{-- <img class="flex-shrink-0 object-cover w-20 h-20 dark:border-transparent rounded outline-none sm:w-32 sm:h-32 dark:bg-gray-500" src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?ixlib=rb-1.2.1&amp;ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&amp;auto=format&amp;fit=crop&amp;w=1350&amp;q=80" alt="Polaroid camera"> --}}
                            <div class="flex flex-col justify-between w-full pb-4">
                                <div class="flex justify-between w-full pb-2 space-x-2">
                                    <div class="space-y-1">
                                        <h3 class="text-lg font-semibold leadi sm:pr-8">{{ $plans[$index]->plan_name ?? '' }}
                                            @if ($plans[$index]->panel_id ?? null == '1')
                                            (Sender)
                                            @elseif ($plans[$index]->panel_id ?? null == '2')
                                            (Receiver)
                                            @elseif ($plans[$index]->panel_id ?? null == '3')
                                            (Seller)
                                            @elseif ($plans[$index]->panel_id ?? null == '4')
                                            (Buyer)

                                           @endif
                                        </h3>
                                        <p>@if ($planValidityDays[$index] <= 30)
                                            Month
                                        @else
                                            Years
                                        @endif
                                            {{-- <input  wire:model.prevent="formattedValidityDate"

                                            class="hsn-box h-7 w-1/3 dynamic-width-input rounded-lg bg-gray-300 font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" /></p> --}}
                                            <p> Valid Till  :
                                            <span>{{$formattedValidityDate[0]}}</span>
                                            </p>
                                                {{-- @dump($planPrices[$index]) --}}
                                                <p>Price
                                                    <span class="font-semibold" >{{$planPrices[$index]}}</span>

                                                    </p>
                                                    {{-- <input  wire:model.prevent="planPrices.{{ $index }}"

                                                    class="hsn-box h-7 w-1/3 dynamic-width-input rounded-lg bg-gray-300 font-mono text-xs font-normal text-black focus:outline-none"
                                                    type="text" /></p> --}}
                                                    {{-- <p>Total Amount
                                                        <input  wire:model.prevent="planTotalAmounts.{{ $index }}"

                                                        class="hsn-box h-7 w-1/3 dynamic-width-input rounded-lg bg-gray-300 font-mono text-xs font-normal text-black focus:outline-none"
                                                        type="text" /></p> --}}

                                    </div>

                                    <div class="text-right">
                                        <p class="text-lg mr-2"> Rs
                                            <span class="font-semibold" > {{$planTotalAmounts[$index]}} </span>
                                            {{-- <input  wire:model.prevent="planTotalAmounts.{{ $index }}"

                                            class="hsn-box h-7 w-1/3 dynamic-width-input rounded-lg bg-gray-300 font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" /> --}}
                                        </p>
                                        <p class="text-sm mt-16 dark:text-gray-600"><button  wire:click.defer="removeFromSession({{ $index }})" type="button" class=" items-center px-2 py-1 pl-0 space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4 fill-current">
                                                <path d="M96,472a23.82,23.82,0,0,0,23.579,24H392.421A23.82,23.82,0,0,0,416,472V152H96Zm32-288H384V464H128Z"></path>
                                                <rect width="32" height="200" x="168" y="216"></rect>
                                                <rect width="32" height="200" x="240" y="216"></rect>
                                                <rect width="32" height="200" x="312" y="216"></rect>
                                                <path d="M328,88V40c0-13.458-9.488-24-21.6-24H205.6C193.488,16,184,26.542,184,40V88H64v32H448V88ZM216,48h80V88H216Z"></path>
                                            </svg>
                                            {{-- <span  >Remove</span> --}}
                                        </button> </p>

                                    </div>

                                </div>

                                <div class="flex text-sm divide-x">
                                    {{-- <button type="button" class="flex items-center px-2 py-1 pl-0 space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4 fill-current">
                                            <path d="M96,472a23.82,23.82,0,0,0,23.579,24H392.421A23.82,23.82,0,0,0,416,472V152H96Zm32-288H384V464H128Z"></path>
                                            <rect width="32" height="200" x="168" y="216"></rect>
                                            <rect width="32" height="200" x="240" y="216"></rect>
                                            <rect width="32" height="200" x="312" y="216"></rect>
                                            <path d="M328,88V40c0-13.458-9.488-24-21.6-24H205.6C193.488,16,184,26.542,184,40V88H64v32H448V88ZM216,48h80V88H216Z"></path>
                                        </svg>
                                        <span  wire:click.defer="removeFromSession({{ $index }})" >Remove</span>
                                    </button> --}}
                                    {{-- <button type="button" class="flex items-center px-2 py-1 space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4 fill-current">
                                            <path d="M453.122,79.012a128,128,0,0,0-181.087.068l-15.511,15.7L241.142,79.114l-.1-.1a128,128,0,0,0-181.02,0l-6.91,6.91a128,128,0,0,0,0,181.019L235.485,449.314l20.595,21.578.491-.492.533.533L276.4,450.574,460.032,266.94a128.147,128.147,0,0,0,0-181.019ZM437.4,244.313,256.571,425.146,75.738,244.313a96,96,0,0,1,0-135.764l6.911-6.91a96,96,0,0,1,135.713-.051l38.093,38.787,38.274-38.736a96,96,0,0,1,135.765,0l6.91,6.909A96.11,96.11,0,0,1,437.4,244.313Z"></path>
                                        </svg>
                                        <span>Add to favorites</span>
                                    </button> --}}
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach

                </ul>
                <div class="space-y-1 text-right text-xs">
                    <p>Total amount:
                        <span class="">&#8377; {{ $withoutGst }}</span>
                    </p>

                 <p>

                    <div wire:ignore.self>
                        <div class="flex justify-end ">
                            {{-- <span class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-s-lg dark:bg-gray-600 dark:text-white dark:border-gray-600"></span> --}}
                            @if($discountDisabled == false)
                            <div class="relative">
                                <input type="text" wire:model.defer="couponDataset.code" aria-describedby="helper-text-explanation"  class="bg-gray-50 border border-e-0 border-gray-300 text-gray-500 dark:text-gray-400 text-xs border-s-0 focus:ring-blue-500 focus:border-blue-500 block   dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Apply Coupon" />
                            </div>

                                <span id="default-icon">
                                    <button wire:click.prevent="applyCoupon"
                                    @if ($discountDisabled == true) disabled="" @endif
                                    class=" btn-size  @if($discountDisabled == false)  bg-gray-900 text-white  @else bg-gray-300   @endif     px-4 py-2   ">Apply</button>
                                <span id="success-icon" class="hidden inline-flex items-center">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                    </svg>
                                </span>
                            </button>
                        @else
                            <span class="mr-5 text-xs   py-2">Saved &#8377;{{ $discountedAmount }} </span>
                            <button wire:click.prevent="removeCoupon"  type="button"

                            class="   text-black   bg-gray-300    lg:px-2 px-1   ">Remove</button>
                        @endif
                            </div>
                            {{-- <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                @if (session('message'))


                                {{ session('message') }}

                                @endif</p>  --}}
                        <span>
                            @if (session('message'))
                            <p> Discount:
                                <span> - &#8377;{{ $discountedAmount }} </span>
                            </p>
                            @endif
                        </span>
                    </div>


                    @if (strToUpper(Auth::user()->state) == 'UTTAR PRADESH')
                    <p class="text-xs dark:text-gray-400">
                        <span>CGST at 9% :</span>
                        <span>&#8377; {{ $cgstAmount }}</span>

                    </p>
                    <p class="text-xs dark:text-gray-400">
                        <span>SGST at 9% :</span>
                        <span>&#8377; {{ $sgstAmount }}</span>

                    </p>
                    @else
                    <p class="text-xs dark:text-gray-400">
                        <span>IGST at 18% :</span>
                        <span>&#8377; {{ $igstAmount }}</span>

                    </p>
                    @endif
                    {{-- @dump( $this->totalAmountWithGst); --}}
                    {{-- <input wire:model="totalAmountWithGst" type="text"> --}}
                    <p>Grand Total:
                        <span class="font-semibold">&#8377; {{ $totalAmountWithGst }}</span>
                    </p>
                </div>

            </div>


        </div>
        <div class="flex justify-end space-x-4 mr-4 mt-2"  >
            {{-- @livewire('razorpay-checkout') --}}
             {{-- @livewire('razorpay-checkout', [
                 'amountWithGst' => $totalAmountWithGst,
                 ]) --}}
                 {{-- @foreach($plans as $plan)
                 @livewire('razorpay-checkout', [
                     'amountWithGst' => $totalAmountWithGst,
                     'panel_id' => $plan->panel_id,
                     'plan_id' => $plan->id,
                     'section_id' => $plan->section_id,
                 ])
             @endforeach --}}
{{--
             @livewire('razorpay-checkout', [
             'amountWithGst' => $totalAmountWithGst,
             'discount' => $discount, // Pass the discount
             'plans' => $plans,
             'planIds' => $planIds, // Pass the array of plan IDs
             'topupIds' => $topupIds, // Pass the array of topup IDs
             ]) --}}

             {{-- <button type="button" class="px-6 py-2 border rounded-md dark:bg-violet-400 dark:text-gray-900 dark:border-violet-400">
                 <span class="sr-only sm:not-sr-only">Continue to</span>Checkout
             </button> --}}
            {{-- <button id="rzp-button1">Pay Now</button>

            <script src="https://checkout.razorpay.com/v1/checkout.js"></script> --}}

            {{-- <button id="rzp-button1">Pay Now</button>

            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

            <script>
                document.getElementById('rzp-button1').onclick = function(e){
                    var options = {
                        "key": "{{ env('RAZOR_KEY') }}", // Replace with your key
                        "amount": @this.totalAmountWithGst * 100, // Amount in paise
                        "currency": "INR",
                        "name": "The Parchi",
                        "description": "The Parchi",
                        "image": "/image/Vector.png",
                        "handler": function (response){
                            // Handle the payment response here
                            fetch('{{ route("payment.initiatePayment") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id
                                })
                            }).then(function(response) {
                                return response.json();
                            }).then(function(data) {
                                // Handle the response from the server
                            });
                        },
                        "prefill": {
                            "name": "{{ auth()->user()->name }}",
                            "email": "{{ auth()->user()->email }}",
                            "contact": "{{ auth()->user()->phone }}"
                        },
                        "notes": {
                            "address": "note value"
                        },
                        "theme": {
                            "color": "#7f5be8"
                        }
                    };
                    var razorpay = new Razorpay(options);
                    razorpay.open();
                    e.preventDefault();
                }
            </script> --}}
            <form id="payment-form" action="/payment-initiate" method="POST">
                @csrf
                <input type="hidden" custom="Hidden Element" name="hidden"/>
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                <input type="hidden" name="amount" value="{{ $totalAmountWithGst}}">
                 <!-- Add the plan IDs to the form -->
                @if($planIds)
                    @foreach(array_unique($planIds) as $planId)
                        <input type="hidden" name="plan_ids[]" value="{{ $planId }}">
                    @endforeach
                @endif

                @if($topupIds)
                    @foreach($topupIds as $topupId)
                        <input type="hidden" name="topup_ids[]" value="{{ $topupId }}">
                    @endforeach
                @endif
                <button class="w-20" id="rzp-button1">Pay</button>
            </form>

            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                document.getElementById('payment-form').onsubmit = function(e){
                    e.preventDefault();

                    var options = {
                        "key": "{{ config('services.razor.key') }}",
                        "amount": @this.totalAmountWithGst * 100,
                        "currency": "INR",
                        "name": "The Parchi",
                        "description": "The Parchi",
                        "image": "/image/Vector.png",
                        "handler": function (response){
                            // Add the payment ID to the form
                            var form = document.getElementById('payment-form');
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'razorpay_payment_id';
                            input.value = response.razorpay_payment_id;
                            form.appendChild(input);

                            // Submit the form
                            form.submit();
                        },
                        "prefill": {
                            "name": "{{ auth()->user()->name }}",
                            "email": "{{ auth()->user()->email }}"
                        },
                        "theme": {
                            "color": "#7f5be8"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                }
            </script>
         </div>
         @else

         <h1 class="text-black text-xl text-center mt-10" >No plan added in the cart</h1>
         @endif
        </div>

    </div>
    {{-- @parent --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('reloadPage', function () {
                window.location.reload();
            });

        });

        // Example using JavaScript and AJAX


    </script>
    <style>
        #rzp-button1 {
        background-color: #F0AC49;
        color: black;
        padding: 3px;
        border-radius: 10%;
        border: 1px solid black;
        }

        </style>
    {{-- <script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="{{ env('RAZOR_KEY') }}"
    data-amount="{{ $amountWithGst * 100 }}"
    data-currency="INR"
    data-order_id=""
    data-name="The Parchi"
    data-description="The Parchi"
    data-image="/image/Vector.png"
    data-prefill.name="{{ auth()->user()->name }}"
    data-prefill.email="{{ auth()->user()->email }}"
    data-theme.color="#7f5be8"
></script> --}}
