<div>
    <div class="border-b p-1.5 border-gray-400 text-black text-lg font-medium hidden sm:flex justify-between">
        <div>
        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Sender</button>
        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Receiver</button>
        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab3' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab3')">Seller</button>
        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab4' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab4')">Buyer</button>
        <button class="px-4 p-1.5 w-auto text-center {{ $activeTab === 'tab5' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab5')">Receipt Note</button>

        </div>
        {{-- <a href="{{ route('whatsapp-logs') }}"  class="px-4 p-1.5 w-auto text-center text-sm text-blue-500 hover:underline ">Deductions Details</a> --}}
    </div>
    <div class="border-b border-gray-400 text-black text-sm flex flex-col sm:flex-row sm:hidden px-2">
        <select class="px-2 my-2 w-full text-center rounded-lg text-xs" wire:model="activeTab">
            <option value="tab1">Sender</option>
            <option value="tab2"> Receiver </option>
            <option value="tab3"> Seller </option>
            <option value="tab4"> Buyer </option>
            <option value="tab5"> Receipt Note </option>
        </select>
    </div>

    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs" id="successMessage"></p>
        </div>
    </div>
    <div id="errorModal" style="display: none;">
        <div class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs">
            <p class="mt-3 " id="errorMessage">\
            </p>
        </div>
    </div>

    {{-- @if(session()->has('error'))
    <div x-data="{ show: true }"
        x-show="show"
        id="error-alert"
        class="flex items-center p-2 mb-4 text-red-800 rounded-lg bg-red-500 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
        role="alert">
        <div class="ms-3 text-sm text-white">
            <span class="font-medium">Error:</span> {{ session('error') }}
        </div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"
                @click="show = false; Livewire.emit('forgetErrorSession');"
                aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif

    @if(session()->has('success'))
    <div x-data="{ show: true }"
        x-show="show"
        id="success-alert"
        class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
        role="alert">
        <div class="ms-3 text-sm">
            <span class="font-medium">Success:</span> {{ session('success') }}
        </div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"
                @click="show = false; Livewire.emit('forgetSuccessSession');"
                aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif --}}


    <style>
        table, th, td {
          border:1px solid black;
        }
        </style>

    <div class="flex w-full justify-center">
        <div class="p-4 w-full max-w-2xl rounded-2xl bg-white">
            <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-white dark:bg-gray-900 text-gray-900 dark:text-white" data-inactive-classes="text-black dark:text-gray-400">
                @php
                    $roles = ['sender', 'receiver', 'seller', 'buyer', 'receipt_note'];
                    $channels = ['whatsapp', 'email'];
                @endphp

                @foreach ($roles as $index => $role)
                    @if ($activeTab === 'tab' . ($index + 1))
                        @foreach ($channels as $channel)
                            <h2 id="accordion-flush-heading-{{ $role }}-{{ $channel }}">
                                <div>
                                    <button type="button" class="flex items-center justify-between w-full py-5 font-medium rtl:text-right shadow-md text-black border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 gap-3 rounded-lg px-3" wire:click="toggleAccordion('{{ $role }}', '{{ $channel }}')" aria-expanded="{{ $accordionState[$role][$channel] ? 'true' : 'false' }}" aria-controls="accordion-flush-body-{{ $role }}-{{ $channel }}">
                                        <span class="text-left text-sm font-bold text-black">
                                            @if($channel == 'whatsapp')
                                                {{ 'WhatsApp' }}
                                            @else
                                                {{ 'Email' }}
                                            @endif
                                            <br>
                                            <span class="text-black  align-items-center">
                                                @if($channel == 'whatsapp')
                                                    Balance - {{ '₹'. ($whatsappBalance ?? '0') }}
                                                    <span class="rounded text-white bg-black mt-2 text-sm px-2 p-1 font-normal" wire:click="handleAction()">
                                                        Add Balance
                                                    </span>
                                                @endif
                                            </span>
                                        </span><br>

                                       <span>

                                        <svg data-accordion-icon class="w-3 h-3 {{ $accordionState[$role][$channel] ? '' : 'rotate-180' }} shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                        </svg>
                                       </span>
                                    </button>
                                </div>
                            </h2>
                            <div id="accordion-flush-body-{{ $role }}-{{ $channel }}" class="{{ $accordionState[$role][$channel] ? '' : 'hidden' }}" aria-labelledby="accordion-flush-heading-{{ $role }}-{{ $channel }}">
                                <div class="py-5 border-b border-gray-200 dark:border-gray-700">
                                    @if(isset($permissions[$role][$channel]))
                                    @foreach ($permissions[$role][$channel] as $action => $value)
                                        <div class="form-control">
                                            <label class="label cursor-pointer border-b">
                                                <span class="label-text">
                                                    @if(strpos($action, 'Sfp') !== false)
                                                        {{ strtoupper(str_replace('_', ' ', $action)) }}
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $action)) }}
                                                    @endif
                                                </span>
                                                <input type="checkbox" value="" class="sr-only peer" id="{{ $role . '_' . $action }}" wire:model="permissions.{{ $role }}.{{ $channel }}.{{ $action }}" {{ $value ? 'checked' : '' }}>
                                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>

                                        </div>
                                    @endforeach
                                @endif
                                <div class="  text-black text-sm flex flex-col sm:flex-row justify-end">
                                    @if($channel == 'whatsapp')
                                    <a href="{{ route('whatsapp-logs') }}"  class="p-1.5 w-auto text-right text-sm text-blue-500 hover:underline ">Deductions Details</a>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>

    </div>

   <!-- Alpine.js Component -->
   @if ($isOpen)
   <div x-data="{ isOpen: @entangle('isOpen') }"
        x-show="isOpen"
        x-on:keydown.escape.window="isOpen = false"
        x-on:close.stop="isOpen = false"
        x-init="$nextTick(() => initializePayButton())"
        class="fixed inset-0 flex items-center justify-center px-2 z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
       <div class="bg-white p-6 rounded shadow-lg w-80 sm:w-96">
           <h1 class="text-sm sm:text-lg text-black border-b border-gray-400">WhatsApp Notification Topup</h1>
           <form class="space-y-4" id="payment-form" wire:submit.prevent="applyCoupon">
               @csrf
               <div>
                   <div class="w-full">
                       <div class="relative w-full min-w-[200px] h-10">
                           <input wire:model.debounce.800ms ="amount" name="amount" x-on:input="amount = $event.target.value"
                               class="peer w-full h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-xs text-black px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                               placeholder=" " />
                           <label
                               class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-xs text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-black peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">Enter Amount for Topup
                           </label>
                       </div>
                   </div>
               </div>
               <div>
                @if(!$discountedAmount)
                <div class="fle x justify-between" x-data="{couponCode: ''}">
                    <div>
                        <div class="relative w-full min-w-[200px] h-10">
                          <input wire:model.defer="couponCode" name="couponCode" x-on:change="applyCoupon()" x-model="couponCode"
                            class="peer w-full h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent  text-black focus:border-t-transparent text-xs px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                            placeholder=" " /><label
                            class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-xs text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-black peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">Enter Coupon Code
                          </label>
                        </div>
                      </div>
                    <button type="submit"   x-bind:disabled="couponCode.trim() === ''"
                    class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                    Apply
                    </button>
                </div>
                @endif
            </div>
                @if($error)
                <div class="text-xs text-red-600">{{ $error }}</div>
                @endif
               @if ($discountedAmount)
               <div class="flex justify-between">
                <div class="text-xs text-green-600">Discount Applied: Saved ₹{{$deductionAmount}}
               </div>
               <span class="text-xs text-red-600 hover:underline cursor-pointer" wire:click = "removeCoupon" >Remove </span>
            </div>
               @endif
               @if($amount)
                <div class="flex justify-between">
                     <div class="text-xs text-black">Total Amount:</div>
                     <div class="text-xs text-black">₹{{ $amount }}</div>
                </div>
                @if ($discountedAmount)
                <div class="flex justify-between">
                     <div class="text-xs text-black">Discount:</div>
                     <div class="text-xs text-black"> - ₹{{ $deductionAmount }}</div>
                </div>
                @endif

                <div class="flex justify-between">
                     <div class="text-xs text-black">Grand total:</div>
                     <div class="text-xs text-black">₹{{ $discountedAmount ?? $amount }}</div>
                </div>
                @endif
               <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                   <button x-on:click="isOpen = false" type="button" wire:click="closeModal"
                           class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500 transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                       Cancel
                   </button>
                   <button id="rzp-button1" type="button"
                           class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                           x-on:click="initializeRazorpay(); isOpen = false;">
                       Pay
                   </button>
               </div>
           </form>
       </div>
       <div x-on:click.self="isOpen = false" class="inset-0 bg-black opacity-50"></div>
   </div>
   @endif

   {{-- <script>
   function initializeRazorpay() {
       let amount = @this.discountedAmount ?? @this.amount;
       // Initialize Razorpay with the amount
         const razorpay = new Razorpay({
              key: '{{ config('services.razorpay.key') }}',
              amount: amount * 100,
              currency: 'INR',
              name: 'The Parchi',
              description: 'Topup WhatsApp Balance',
              image: '/image/Vector.png',
              handler: function(response) {
                // Send the payment details to the server
                @this.payWithRazorpay(response.razorpay_payment_id);
              },
              prefill: {
                name: '{{ auth()->user()->name }}',
                email: '{{ auth()->user()->email }}',
              },
              theme: {
                color: '#7f5be8',
              },
         });
            razorpay.open();


   }
   </script> --}}


<!-- JavaScript to Initialize Pay Button -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function initializePayButton() {
        const payButton = document.getElementById('rzp-button1');
        const amountInput = document.querySelector('input[name="amount"]');

        console.log("Initializing Pay button.");

        if (payButton) {
            console.log("Pay button found. Adding event listener.");

            payButton.disabled = true;
            payButton.classList.add('bg-gray-400');

            amountInput.addEventListener('input', () => {
                if (amountInput.value.trim() !== '') {
                    payButton.disabled = false;
                    payButton.classList.remove('bg-gray-400');
                    payButton.classList.add('bg-black');
                } else {
                    payButton.disabled = true;
                    payButton.classList.remove('bg-black');
                    payButton.classList.add('bg-gray-400');
                }
            });

            payButton.onclick = function(e) {
                e.preventDefault();
                console.log('Pay button clicked');

                const amount = @this.discountedAmount ?? amountInput.value;
                if (!amount) {
                    alert('Please enter an amount.');
                    return;
                }

                const razorKey = "{{ config('services.razor.key') }}";
                if (!razorKey) {
                    alert('Razorpay key is missing. Please contact support.');
                    return;
                }

                const options = {
                    "key": razorKey,
                    "amount": amount * 100, // Convert to paise
                    "name": "The Parchi",
                    "description": "The Parchi",
                    "image": "/image/Vector.png",
                    "handler": function(response) {
                        fetch('/payment-initiate-wallet', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature,

                                amount: amount,
                                deductionAmount: @this.deductionAmount,
                                user_id: {{ auth()->user()->id }}
                            })
                        }).then(response => response.json())
                        .then(data => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    },
                    "prefill": {
                        "name": "{{ auth()->user()->name }}",
                        "email": "{{ auth()->user()->email }}"
                    },
                    "theme": {
                        "color": "#7f5be8"
                    }
                };

                const rzp1 = new Razorpay(options);
                rzp1.open();
            };
        } else {
            console.error("Pay button not found.");
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log("Document loaded. Initializing script.");
        initializePayButton();
    });

    window.addEventListener('show-error-message', event => {
            // Set the message in the modal
            document.getElementById('errorMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('errorModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('errorModal').style.display = 'none';
            }, 8000);
        });

        window.addEventListener('show-success-message', event => {
            // Set the message in the modal
            document.getElementById('successMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 8000);
        });
    </script>

    {{-- <div id="authentication-modal"  tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore.self>
        <div class="relative p-3 w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-3 md:p-3 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Topup WhatsApp Balance</h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-3 md:p-3">
                    <form class="space-y-4" id="payment-form" method="POST">
                        @csrf
                        <div>
                            <div class="w-full">
                                <div class="relative w-full min-w-[200px] h-10">
                                    <input name="amount"
                                           class="peer w-full h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                                           placeholder=" " />
                                    <label
                                        class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-black peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">Amount
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="rzp-button1"
                                class=" text-white bg-black hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Pay
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div> --}}

    {{-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('rzp-button12');
    const amountInput = document.querySelector('input[name="amount"]');

    console.log("Document loaded. Initializing script.");

    if (payButton) {
        console.log("Pay button found. Adding event listener.");

        payButton.disabled = true;
        payButton.classList.add('bg-gray-400');

        amountInput.addEventListener('input', () => {
            if (amountInput.value.trim() !== '') {
                payButton.disabled = false;
                payButton.classList.remove('bg-gray-400');
                payButton.classList.add('bg-black');
            } else {
                payButton.disabled = true;
                payButton.classList.remove('bg-black');
                payButton.classList.add('bg-gray-400');
            }
        });

        payButton.onclick = function(e) {
            e.preventDefault();
            console.log('Pay button clicked');

            const amount = amountInput.value;
            if (!amount) {
                alert('Please enter an amount.');
                return;
            }

            const razorKey = "{{ config('services.razor.key') }}";
            if (!razorKey) {
                alert('Razorpay key is missing. Please contact support.');
                return;
            }

            const options = {
                "key": razorKey,
                "amount": amount * 100, // Convert to paise
                "name": "The Parchi",
                "description": "The Parchi",
                "image": "/image/Vector.png",
                "handler": function(response) {
                    fetch('/payment-initiate-wallet', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                            amount: amount,
                            user_id: {{ auth()->user()->id }}
                        })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                },
                "prefill": {
                    "name": "{{ auth()->user()->name }}",
                    "email": "{{ auth()->user()->email }}"
                },
                "theme": {
                    "color": "#7f5be8"
                }
            };

            const rzp1 = new Razorpay(options);
            rzp1.open();
        };
    } else {
        console.error("Pay button not found.");
    }

    document.body.addEventListener('click', function(e) {
        const toggleElement = e.target.closest('[data-modal-toggle]');
        if (toggleElement) {
            const modalID = toggleElement.getAttribute('data-modal-toggle');
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
                const isHidden = modal.classList.contains('hidden');
                modal.setAttribute('aria-hidden', isHidden.toString());
            }
        }
    });
});
</script> --}}


</div>


