<div class="max-w-6xl mx-auto" x-data="{
    activeTab: '{{ $activeTab }}',
    setActiveTab(tab) {
        this.activeTab = tab;
        localStorage.setItem('activeTab', tab);
        $wire.changeTab(tab);
    }
}" x-init="$watch('activeTab', value => localStorage.setItem('activeTab', value))">

    <div class="border-gray-200 dark:border-gray-700">
        @if ($message)
        <div class="alert alert-success p-1.5 rounded-lg text-sm">
            {{-- <div x-data="{show: true}" x-data="show" x-init="setTimeout(() => show = false, 3000)" class="alert alert-success"> --}}
            {{ $message }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    </div>
    <div class="navbar bg-gray-200">
        <div class="navbar-start sm:block hidden">
            <ul class="flex flex-wrap text-center text-sm" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                @foreach(['Sender', 'Receiver', 'Seller', 'Buyer', 'Receipt Note'] as $index => $tabName)
                    <li class="group" role="presentation">
                        <button
                            class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white"
                            :class="{ 'bg-[#f0ac49]': activeTab === 'panel-{{ $index + 1 }}' }"
                            @click="setActiveTab('panel-{{ $index + 1 }}')"
                            id="{{ strtolower($tabName) }}-tab"
                            data-tabs-target="#panel-{{ $index + 1 }}"
                            type="button"
                            role="tab"
                            aria-controls="panel-{{ $index + 1 }}"
                            :aria-selected="activeTab === 'panel-{{ $index + 1 }}'"
                        >
                            {{ $tabName }}
                        </button>
                    </li>
                @endforeach
            </ul>

        </div>
        <select class="select select-bordered border-black bg-white text-black font-semibold select-xs w-full max-w-xs p-1 h-8 mr- sm:hidden" x-model="activeTab" @change="setActiveTab($event.target.value)">
            <option value="panel-1">Sender</option>
            <option value="panel-2">Receiver</option>
            <option value="panel-3">Seller</option>
            <option value="panel-4">Buyer</option>
            <option value="panel-5">Receipt Note</option>
        </select>
        <div class="navbar-center">

              <button wire:click="togglePlanType(false)" class="{{ $showAnnualPlans ? 'bg-white text-black' : 'bg-orange text-white' }} rounded-l-lg border border-gray-900  px-4 py-1 text-sm   bg-[#343a40]  focus:z-10 ">Monthly</button>

                <button wire:click="togglePlanType(true)" class="{{ $showAnnualPlans ? 'bg-orange text-white' : 'bg-white text-black' }} rounded-r-lg border border-gray-900  px-4 py-1 text-sm   bg-[#343a40]  focus:z-10 ">Yearly</button>


        </div>
        {{-- <div class="navbar-end">
            <li role="presentation" class=" mr-10 hidden sm:block ml-auto">
                <a href="{{ route('topup') }}"
                    class="px-4 py-1 text-white bg-[#dc3545] inline-block p-2 border-b-2 border-transparent rounded-xl hover:text-white dark:hover:text-white hover:bg-[#dc3545] ml-auto">Top-Ups</a>
            </li>
        </div> --}}

      </div>

    <div id="myTabContent" class="mt-10" >
        @foreach ([1, 2, 3, 4, 5] as $panelId)
            <div class="{{ $activeTab === 'panel-' . $panelId ? '' : 'hidden' }} p-4 rounded-lg" id="panel-{{ $panelId }}" role="tabpanel" aria-labelledby="tab-{{ $panelId }}">
                <div class="sm:flex flex-wrap -mx-4 {{ $showAnnualPlans ? '' : 'justify-between' }}">
                    {{-- @dd($filtered); --}}
                    @foreach ($filtered as $plan)
                        @if ($plan['panel_id'] == $panelId)
                            {{-- <h1>Panel {{ $panelId }}</h1> --}}
                            <!-- Your content for each panel here -->
                            <div class="flex flex-wrap justify-center">
                                <div class="w-full sm:w-96 sm:px-0 md:px-4 mb-4">
                                    <div
                                        class="w-full max-w-sm p-8 bg-gray-50 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mt-4 mr-4 flex flex-col justify-between h-full transition-transform hover:scale-105 hover:antialiased">
                                        <div>
                                            <h5 class="text-center text-xl text-gray-700 dark:text-gray-400">{{ $plan['plan_name'] }}</h5>
                                            <div class="flex justify-center text-gray-900 dark:text-white">
                                                <span class="text-xl text-gray-500 dark:text-gray-400">Rs</span>
                                                @if($plan['plan_name'] != 'Free')
                                                <span class="line-through text-2xl tracking-tight">{{$plan['discounted_price']}}</span>
                                                <span class="text-3xl tracking-tight">{{ $plan['price'] }}</span>
                                                @else
                                                <span class="text-3xl tracking-tight">{{ $plan['price'] }}</span>
                                                @endif
                                                {{-- <span class="ml-1 mt-6 font-normal text-gray-500 dark:text-gray-400">{{ $plan['validity_days'] == 365}}</span><br> --}}
                                                <span class="text-[0.6rem] leading-tight mt-6 font-normal text-gray-500 dark:text-gray-400">+ taxes</span>
                                            </div>
                                            <div class="text-center">
                                            </div>
                                            <ul role="list" class="space-y-2 my-2">
                                                @php $featureNameDisplayed = false; @endphp
                                                <ul role="list" class="space-y-2 ">
                                                    @php
                                                        $displayedFeatures = []; // An array to keep track of displayed features
                                                    @endphp
                                                    {{-- @dd($plan['features']) --}}
                                                    @foreach ($plan['features'] as $feature)
                                                    {{-- @dd($feature); --}}
                                                    {{-- @if (in_array($feature['feature_name'], ['Received Return Challan','Taxes', 'Create Challan', 'Challan Series No', 'Create Return Challan', 'Received Challan', 'Create Invoice', 'Purchase Order', 'Invoice Series No', 'New Purchase Order', 'All Invoice']) &&
                                                            !in_array($feature['feature_name'], $displayedFeatures)) --}}
                                                    <li class="flex space-x-3 justify-center">
                                                        <span
                                                            class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                                                            {{ $feature['feature_name'] }}
                                                            @if(!empty($feature['feature_usage_limit']))
                                                                - {{ $feature['feature_usage_limit'] }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                    {{-- @php
                                                        $displayedFeatures[] = $feature['feature_name']; // Add the displayed feature to the array
                                                    @endphp
                                                @endif --}}
                                                    @endforeach
                                                </ul>

                                                <li class="flex space-x-3 justify-center">
                                                    <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Users - {{ $plan['user'] ?? '' }}</span>
                                                </li>
                                                @if(!empty($plan['topup']))
                                                    <li class="flex space-x-3 justify-center">
                                                        <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Top-Ups - {{ $plan['topup'] }}</span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="flex justify-center mt-4">
                                            @if($plan['plan_name'] != 'Free')
                                            <button type="button"
                                                wire:click="addToCart({{ $plan['id'] }})"
                                                onclick="handleButtonClick(event, '{{ route('profile') }}')"
                                                class="mr-4 rounded-xl bg-[#B3BD3A] px-5 py-1.5 text-sm text-white hover:bg-[#7d8429] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                                                Add To Cart
                                            </button>

                                            <button type="button"
                                                wire:click="buyNow({{ $plan['id'] }})"
                                                onclick="handleButtonClick(event, '{{ route('profile') }}')"
                                                class="rounded-xl border-2 border-gray-900 bg-white px-5 py-1.5 text-sm text-black hover:bg-orange font-bold focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                                                Buy Now
                                            </button>
                                            @else
                                            <button type="button"
                                                wire:click="tryNow({{$panelId}})"
                                                onclick="handleButtonClick(event, '{{ route('profile') }}')"
                                                class="rounded-xl border-2 border-gray-900 bg-white px-5 py-1.5 text-sm text-black hover:bg-[#f0ac49] font-bold focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                                                Try Now
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- @dump($plan) --}}
                        @endif
                    @endforeach
                </div>
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
                class="text-sm text-[#007bff]  hover:underline hover:bg-[#F0AC49] hover:text-white">Pricing
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
    <script>
         var authUser = @json(auth()->user());
        function handleButtonClick(event, redirectUrl) {
            console.log('Button clicked');
            // Check if user information is null, and redirect to the profile page if needed
            if (
                isNull(authUser.address) ||
                isNull(authUser.pincode) ||
                isNull(authUser.state) ||
                isNull(authUser.city)
            ) {
                event.preventDefault();
                window.location.href = redirectUrl;
            }
        }

        // Helper function to check if a value is null or undefined
        function isNull(value) {
            return value === null || value === undefined;
        }
    </script>

</div>
