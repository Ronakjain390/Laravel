<div class="max-w-5xl mx-auto my-auto">
    @if($updateMessage)
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 text-center">
            {{ $updateMessage . '.' }} <a href="/dashboard" class="underline font-semibold">Go to Dashboard</a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
        @foreach(['Seller', 'Buyer', 'Sender', 'Receiver', 'Receipt_Note'] as $role)
        @php
        // $role = str_replace('_', ' ', $role);
        // dump($role);
    @endphp
            {{-- @dump($activeUsers); --}}
        @php
            $roleData = null;
            if (array_key_exists($role, $activeUsers)) {
                $roleData = collect($activeUsers[$role])->last(); // Get the last entry for the current role
                // dump($roleData);
            }
        @endphp

        @if($roleData && in_array($roleData['panel_id'] ?? null, [1, 2, 3, 4, 5]) && ($roleData['status'] ?? 'inactive') == 'active' && in_array($roleData['plan_id'] ?? null, [50, 11, 10, 19, 58]))
            <div class="card bg-[#f9fafb] shadow-xl text-center w-96 mx-auto px-10">
                <div class="card-body">
                     @php
                        // Remove underscores for display purposes
                        $displayRole = str_replace('_', ' ', $role);
                    @endphp
                    <h2 class="card-title mx-auto text-black">{{ strtoupper($displayRole) }}</h2>
                    <p class="mb-4 text-[#6b7280]">
                        @switch($role)
                            @case('Sender')
                                Make paperless delivery challan and get instant confirmation on delivery of Goods.
                                @break
                            @case('Receiver')
                                Receive and send back Goods after the job-work is done with digital records.
                                @break
                            @case('Seller')
                                Easy GST invoicing with instant approval from buyer. No more signed physical paper records.
                                @break
                            @case('Buyer')
                                All your records of bought Goods & service are now paperless.
                                @break
                            @case('Receipt_Note')
                                Create and send digital receipt note to your buyer.
                                @break
                        @endswitch
                    </p>
                    <div class="card-actions justify-center">
                        @if(($roleData['status'] ?? 'inactive') == 'active')
                            <label class="switch relative items-center cursor-pointer">
                                <input type="checkbox" id="togBtn" wire:model="has{{ strtolower($role) }}" wire:change="toggleRole('{{ strtolower($role) }}')">
                                <div class="slider round">
                                    <span class="on">TRIAL ACTIVE</span>
                                    <span class="off">START TRIAL</span>
                                </div>
                            </label>
                        @endif
                    </div>
                    <a wire:click="navigateToPricing('{{ strtolower($role) }}')" class="rounded-xl bg-white border border-black text-black px-5 py-2 w-48 text-sm font-medium hover:bg-orange focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 inline-block mt-4 mx-auto">
                        Buy Plan
                    </a>
                </div>
            </div>
        @elseif($roleData && in_array($roleData['panel_id'] ?? null, [1, 2, 3, 4, 5]) && ($roleData['status'] ?? 'inactive') == 'active')
            <div class="card bg-[#f9fafb] shadow-xl text-center w-96 mx-auto px-10">
                <div class="card-body">
                     @php
                        // Remove underscores for display purposes
                        $displayRole = str_replace('_', ' ', $role);
                    @endphp
                    <h2 class="card-title mx-auto text-black">{{ strtoupper($displayRole) }}</h2>
                    <p class="mb-4 text-[#6b7280] h-24 overflow-auto">
                        @switch($role)
                            @case('Sender')
                                Make paperless delivery challan and get instant confirmation on delivery of Goods.
                                @break
                            @case('Receiver')
                                Receive and send back Goods after the job-work is done with digital records.
                                @break
                            @case('Seller')
                                Easy GST invoicing with instant approval from buyer. No more signed physical paper records.
                                @break
                            @case('Buyer')
                                All your records of bought Goods & service are now paperless.
                                @break
                            @case('Receipt_Note')
                                Create and send digital receipt note to your buyer.
                                @break
                        @endswitch
                    </p>
                    <p class="mb-4 text-black py-2 bg-orange rounded-lg w-40 mx-auto my-6  h-10 overflow-auto">
                        Plan Active
                    </p>
                </div>
            </div>
        @else
            <div class="card bg-[#f9fafb] shadow-xl text-center w-96 mx-auto px-10">
                <div class="card-body">
                    <h2 class="card-title mx-auto text-black">{{ strtoupper($role) }}</h2>
                    <p class="mb-4 text-[#6b7280]">
                        @switch($role)
                            @case('Sender')
                                Make paperless delivery challan and get instant confirmation on delivery of Goods.
                                @break
                            @case('Receiver')
                                Receive and send back Goods after the job-work is done with digital records.
                                @break
                            @case('Seller')
                                Easy GST invoicing with instant approval from buyer. No more signed physical paper records.
                                @break
                            @case('Buyer')
                                All your records of bought Goods & service are now paperless.
                                @break
                            @case('Receipt_Note')
                                Create and send digital receipt note to your buyer.
                                @break
                        @endswitch
                    </p>
                    <p class="mb-4 text-[#6b7280]">
                        Plan Expired
                    </p>
                    <a href="{{ route('pricing') }}" class="rounded-xl bg-white border border-black text-black px-5 py-2 w-48 text-sm font-medium hover:bg-orange focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 inline-block mt-4 mx-auto">
                        Buy Plan
                    </a>
                </div>
            </div>
        @endif
    @endforeach
    </div>
    <style>
        .switch {
      position: relative;
      display: inline-block;
      width: 77%;
      height: 34px;
    }

    .switch input {display:none;}

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #756c6c;
      -webkit-transition: .4s;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 2px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #F0AC49;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(100%);
      -ms-transform: translateX(100%);
      transform: translateX(580%);
    }

    /*------ ADDED CSS ---------*/
    .on
    {
      display: none;
    }

    .on, .off
    {
      color: white;
      position: absolute;
      transform: translate(-50%,-50%);
      top: 50%;
      left: 50%;
      font-size: 10px;
      font-family: Verdana, sans-serif;
    }

    input:checked+ .slider .on
    {display: block;}

    input:checked + .slider .off
    {display: none;}

    /*--------- END --------*/

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;}

      @media (max-width: 768px) {
            .switch {
                width: 60%;
            }
        }

        @media (max-width: 480px) {
            .switch {
                width: 60%;
            }
        }
        @media (max-width: 425px) {
            .card{
                width: 22rem;
            }
            .switch {
                width: 89%;
            }
        }
        @media (max-width: 375px) {
            .switch {
                width: 89%;
            }
        }
        @media (max-width: 320px) {
            .switch {
                width: 89%;
            }
        }
    </style>
</div>

