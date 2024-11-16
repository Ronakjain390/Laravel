<div class="max-w-7xl mt-20 ml-20 mx-auto">
    <div class="border-gray-200 dark:border-gray-700">
        @if ($message)
        <div class="alert alert-success">
            {{-- <div x-data="{show: true}" x-data="show" x-init="setTimeout(() => show = false, 3000)" class="alert alert-success"> --}}
            {{ $message }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

        {{-- <a title="Cart" class="text-dark" href="{{ route('checkout') }}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
             
        </a> --}}
        <ul class="flex flex-wrap text-center text-sm font-medium" id="myTab" data-tabs-toggle="#myTabContent"
            role="tablist">
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-1' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-1')" id="sender-tab" data-tabs-target="#panel-1" type="button" role="tab" aria-controls="panel-1" aria-selected="{{ $activeTab === 'panel-1' ? 'true' : 'false' }}">Sender</button>
            </li>
            <!-- Repeat similar buttons for other tabs with proper wire:click and active checks -->
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-2' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-2')" id="receiver-tab" data-tabs-target="#panel-2" type="button" role="tab" aria-controls="panel-2" aria-selected="{{ $activeTab === 'panel-2' ? 'true' : 'false' }}">Receiver</button>
            </li>
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-3' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-3')" id="seller-tab" data-tabs-target="#panel-3" type="button" role="tab" aria-controls="panel-3" aria-selected="{{ $activeTab === 'panel-3' ? 'true' : 'false' }}">Seller</button>
            </li>
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-4' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-4')" id="buyer-tab" data-tabs-target="#panel-4" type="button" role="tab" aria-controls="panel-4" aria-selected="{{ $activeTab === 'panel-4' ? 'true' : 'false' }}">Buyer</button>
            </li>        
            <li role="presentation" class="ml-auto mr-10">
                <a href="{{ route('topup') }}"
                    class="px-4 py-2 text-white bg-[#dc3545] inline-block p-2 border-b-2 border-transparent rounded-xl hover:text-white dark:hover:text-white hover:bg-[#dc3545] ml-auto">Top-Ups</a>
            </li>
            <span class="ms-3 m-2 text-sm font-medium text-gray-900 dark:text-gray-300">Monthly</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input wire:click="toggleValidity" type="checkbox" value="" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Yearly</span>
              </label>
            <!-- Add more tab buttons for other panels if needed -->
        </ul>

    </div>
    {{-- <label class="relative inline-flex items-center cursor-pointer">
        <input wire:click="toggleValidity" type="checkbox" value="" class="sr-only peer">
        <!-- Your toggle switch styling here -->
    </label> --}}

{{-- <label class="relative inline-flex items-center cursor-pointer">
    <input wire:click="toggleValidity" type="checkbox" value="" class="sr-only peer">
    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
  </label> --}}
  {{-- @php dump($filtered); @endphp --}}

    <div id="myTabContent" class="mt-10" wire:ignore>
        @foreach ([1, 2, 3, 4] as $panelId)
            @php
                $filtered = array_filter($plans, function ($plan) use ($panelId) {
                    return $plan['panel_id'] == $panelId;
                });
                // {{-- dump($filtered) --}}
            @endphp

            <div class="hidden p-4 rounded-lg" id="panel-{{ $panelId }}" role="tabpanel"
                aria-labelledby="tab-{{ $panelId }}">
                {{-- SENDER --}}
                @if ($panelId == 1)
                <div class="flex flex-wrap -mx-4">
        @foreach ($filtered as $plan)
            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/3 xl:w-1/3 px-4 mb-4">
                            <div
                                class="w-full max-w-sm p-4 bg-gray-50 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                <div class="h-80">
                                <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                    {{ $plan['plan_name'] }} </h5>
                                <div class="flex justify-center text-gray-900 dark:text-white">
                                    <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                    <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                    <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>

                                </div>
                                <div class="text-center">
                                    <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                        GST
                                        Applicable</span>
                                </div>
                                <ul role="list" class="space-y-5 my-7">
                                    @php $featureNameDisplayed = false; @endphp
                                    <ul role="list" class="space-y-5 my-7">
                                        @php
                                            $displayedFeatures = []; // An array to keep track of displayed features
                                        @endphp

                                        @foreach ($plan['features'] as $feature)
                                            @if (in_array($feature['feature_name'], ['Received Return Challan', 'Create Challan']) &&
                                                    !in_array($feature['feature_name'], $displayedFeatures))
                                                <li class="flex space-x-3 justify-center">
                                                    <span
                                                        class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                        {{ $feature['feature_name'] }} -
                                                        {{ $feature['feature_usage_limit'] }}
                                                    </span>
                                                </li>
                                                @php
                                                    $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                @endphp
                                            @endif
                                        @endforeach
                                    </ul>

                                    <li class="flex space-x-3 justify-center ">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                            -
                                            {{ $plan['user'] ?? '' }}</span>
                                    </li>
                                    <li class="flex space-x-3 justify-center">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                            - {{ $plan['topup'] }}</span>
                                    </li>
                                    </li>
                                </ul>
                                </div>
                                <div class="flex justify-center mt-4">
                                    @if($plan['plan_name'] != 'Free')
                                      <button type="button" wire:click="addToCart({{ $plan['id'] }})" onclick="window.location.reload();"  class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Add To Cart</button>

                                    <button type="button" wire:click="buyNow({{ $plan['id'] }})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Byu
                                        Now</button>
                                        @else

                                        <button type="button" wire:click="tryNow({{$panelId}})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Try Now</button>
                                @endif
                                </div>
                               
                            </div>
                </div>
                            <!-- Content for panel 1 -->
                            <!-- Update content for Panel 1 here -->
                        @endforeach
                    </div>
                    {{-- RECEIVER --}}
                @elseif ($panelId == 2)
                    <div class="flex flex-wrap -mx-4">
    @foreach ($filtered as $plan)
        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/3 xl:w-1/3 px-4 mb-4">
                            <div class="w-full max-w-sm p-4 bg-gray-50 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                <div class="h-80">
                                <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                    {{ $plan['plan_name'] }} </h5>
                                <div class="flex justify-center text-gray-900 dark:text-white">
                                    <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                    <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                    <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>

                                </div>
                                <div class="text-center">
                                    <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                        GST
                                        Applicable</span>
                                </div>
                                
                                <ul role="list" class="space-y-5 my-7">
                                    @php $featureNameDisplayed = false; @endphp
                                    <ul role="list" class="space-y-5 my-7">
                                        @php
                                            $displayedFeatures = []; // An array to keep track of displayed features
                                        @endphp
                                        @foreach ($plan['features'] as $feature)
                                            @if (in_array($feature['feature_name'], ['Create Return Challan', 'Received Challan', 'Challan Series No']) &&
                                                    !in_array($feature['feature_name'], $displayedFeatures))
                                                <li class="flex space-x-3 justify-center">
                                                    <span
                                                        class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                        {{ $feature['feature_name'] }} -
                                                        {{ $feature['feature_usage_limit'] }}
                                                    </span>
                                                </li>
                                                @php
                                                    $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                @endphp
                                            @endif
                                        @endforeach
                                    </ul>
                                
                                    <li class="flex space-x-3 justify-center ">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                            -
                                            1</span>
                                    </li>
                                    <li class="flex space-x-3 justify-center">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                            -No</span>
                                    </li>
                                    </li>
                                </ul>
                            </div>
                                <div class="flex  justify-center mt-4">
                                    @if($plan['plan_name'] != 'Free')
                                    <button type="button" wire:click="addToCart({{ $plan['id'] }})" onclick="window.location.reload();" 
                                        class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Add
                                        To Cart</button>
                                    <button type="button" wire:click="buyNow({{ $plan['id'] }})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Byu
                                        Now</button>
                                        @else
                                        <button type="button" wire:click="tryNow({{$panelId}})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Try Now</button>
                                        @endif
                                </div>
                            
                            </div>
        </div>
                            <!-- Content for panel 2 -->
                            <!-- Update content for Panel 2 here -->
                        @endforeach
                    </div>
                    
                    {{-- SELLER --}}
                @elseif ($panelId == 3)
                    <div class="flex flex-wrap -mx-4">
    @foreach ($filtered as $plan)
        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/3 xl:w-1/3 px-4 mb-4">
                            <div
                                class="w-full max-w-sm p-4 bg-gray-50 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                <div class="h-80">
                                <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                    {{ $plan['plan_name'] }} </h5>
                                <div class="flex justify-center text-gray-900 dark:text-white">
                                    <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                    <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                    <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>

                                </div>
                                <div class="text-center">
                                    <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                        GST
                                        Applicable</span>
                                </div>
                                <ul role="list" class="space-y-5 my-7">
                                    @php $featureNameDisplayed = false; @endphp
                                    <ul role="list" class="space-y-5 my-7">
                                        @php
                                            $displayedFeatures = []; // An array to keep track of displayed features
                                        @endphp
                                        @foreach ($plan['features'] as $feature)
                                            @if (in_array($feature['feature_name'], ['Create Invoice', 'Purchase Order', 'Invoice Series No']) &&
                                                    !in_array($feature['feature_name'], $displayedFeatures))
                                                <li class="flex space-x-3 justify-center">
                                                    <span
                                                        class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                        {{ $feature['feature_name'] }} -
                                                        {{ $feature['feature_usage_limit'] }}
                                                    </span>
                                                </li>
                                                @php
                                                    $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                @endphp
                                            @endif
                                        @endforeach
                                    </ul>

                                    <li class="flex space-x-3 justify-center ">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                            -
                                            1</span>
                                    </li>
                                    <li class="flex space-x-3 justify-center">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                            -No</span>
                                    </li>
                                    </li>
                                </ul>
                                </div>
                                <div class="flex justify-center mt-4">
                                    @if($plan['plan_name'] != 'Free')
                                    <button type="button" wire:click="addToCart({{ $plan['id'] }})" onclick="window.location.reload();" 
                                        class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Add
                                        To Cart</button>
                                    <button type="button" wire:click="buyNow({{ $plan['id'] }})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Byu
                                        Now</button>
                                        @else 
                                        <button type="button" wire:click="tryNow({{$panelId}})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Try Now</button>
                                    @endif
                                </div>

                            </div>
</div>
                            <!-- Content for panel 2 -->
                            <!-- Update content for Panel 2 here -->
                        @endforeach
                    </div>
                    {{-- BUYER --}}
                @elseif ($panelId == 4)
                    <div class="flex flex-wrap -mx-4">
                        @foreach ($filtered as $plan)
                            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/3 xl:w-1/3 px-4 mb-4">
                            <div class="w-full max-w-sm p-4 bg-gray-50 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4  transition-transform hover:scale-105 hover:antialiased whitespace-nowrap">
                                <div class="h-80">
                                <h5 class="text-center text-xl font-medium text-gray-700 dark:text-gray-400">
                                    {{ $plan['plan_name'] }} </h5>
                                <div class="flex justify-center text-gray-900 dark:text-white">
                                    <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                    <span class="text-5xl tracking-tight">{{ $plan['price'] }}</span>
                                    <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">/Mo</span>

                                </div>
                                <div class="text-center">
                                    <span class="text-xs font-normal leading-tight text-gray-500 dark:text-gray-400">*
                                        GST
                                        Applicable</span>
                                </div>
                                <ul role="list" class="space-y-5 my-7">
                                    @php $featureNameDisplayed = false; @endphp
                                    <ul role="list" class="space-y-5 my-7">
                                        @php
                                            $displayedFeatures = []; // An array to keep track of displayed features
                                        @endphp
                                        @foreach ($plan['features'] as $feature)
                                            @if (in_array($feature['feature_name'], ['Create Invoice', 'Purchase Order', 'Invoice Series No']) &&
                                                    !in_array($feature['feature_name'], $displayedFeatures))
                                                <li class="flex space-x-3 justify-center">
                                                    <span
                                                        class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                        {{ $feature['feature_name'] }} -
                                                        {{ $feature['feature_usage_limit'] }}
                                                    </span>
                                                </li>
                                                @php
                                                    $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                @endphp
                                            @endif
                                        @endforeach
                                    </ul>

                                    <li class="flex space-x-3 justify-center ">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users
                                            -
                                            1</span>
                                    </li>
                                    <li class="flex space-x-3 justify-center">

                                        <span
                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups
                                            -No</span>
                                    </li>
                                    </li>
                                </ul>
                                </div>
                                <div class="flex justify-center mt-4">
                                    @if($plan['plan_name'] != 'Free')
                                    <button type="button" wire:click="addToCart({{ $plan['id'] }})" onclick="window.location.reload();" 
                                        class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Add
                                        To Cart</button>
                                    <button type="button" wire:click="buyNow({{ $plan['id'] }})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Byu
                                        Now</button>
                                    @else
                                        <button type="button" wire:click="tryNow({{$panelId}})"
                                        class="rounded-xl bg-[#d16d4e] px-5 py-1.5 text-sm font-medium text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">Try Now</button>
                                        @endif
                                </div>

                            </div>
</div>
                            <!-- Content for panel 2 -->
                            <!-- Update content for Panel 2 here -->
                        @endforeach
        </div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="col-span-12 mt-4">
        <div class="py-0 px-3 px-md-5">
            <a href="#" class="text-sm text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">About
                us | </a>
            <a href="#"
                class="text-sm text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Contact us |</a>
            <a href="{{ route('pricing') }}"
                class="text-sm text-[#007bff] font-medium hover:underline hover:bg-[#F0AC49] hover:text-white">Pricing
                |</a>
            <a href="#"
                class="text-sm text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Privacy Policy |</a>
            <a href="#" class="text-sm text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Terms
                &amp; Conditions |</a>
            <a href="#"
                class="text-sm text-[#007bff] hover:underline hover:bg-[#F0AC49] hover:text-white">Cancellation
                Policy</a>
        </div>
    </div>

    {{-- <script>
        Livewire.on('reload-page', function(){

        setTimeout(function(){
            location.reload();
        }, 3000);
    });
    </script> --}}
</div>
