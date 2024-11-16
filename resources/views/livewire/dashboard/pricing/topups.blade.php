<div class="max-w-6xl mt-20 mx-auto sm:ml-0 md:ml-20">
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
        <ul class="flex flex-wrap text-center text-sm font-medium " id="myTab" data-tabs-toggle="#myTabContent"
            role="tablist">
            <li
                class="inline sm:block hidden-block items-center mr-1 px-2 py-1.5 text-white bg-[#4a4f58] border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">
                <a href="{{ route('pricing') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
            </li>
            <li class="group sm:block hidden" role="presentation">
                <button
                    class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-1' ? 'bg-[#f0ac49]' : '' }}"
                    wire:click="changeTab('panel-1')" id="sender-tab" data-tabs-target="#panel-1" type="button"
                    role="tab" aria-controls="panel-1"
                    aria-selected="{{ $activeTab === 'panel-1' ? 'true' : 'false' }}">Sender</button>
            </li>
            <!-- Repeat similar buttons for other tabs with proper wire:click and active checks -->
            <li class="group sm:block hidden" role="presentation">
                <button
                    class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-2' ? 'bg-[#f0ac49]' : '' }}"
                    wire:click="changeTab('panel-2')" id="receiver-tab" data-tabs-target="#panel-2" type="button"
                    role="tab" aria-controls="panel-2"
                    aria-selected="{{ $activeTab === 'panel-2' ? 'true' : 'false' }}">Receiver</button>
            </li>
            <li class="group sm:block hidden" role="presentation">
                <button
                    class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-3' ? 'bg-[#f0ac49]' : '' }}"
                    wire:click="changeTab('panel-3')" id="seller-tab" data-tabs-target="#panel-3" type="button"
                    role="tab" aria-controls="panel-3"
                    aria-selected="{{ $activeTab === 'panel-3' ? 'true' : 'false' }}">Seller</button>
            </li>
            <li class="group sm:block hidden" role="presentation">
                <button
                    class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-4' ? 'bg-[#f0ac49]' : '' }}"
                    wire:click="changeTab('panel-4')" id="buyer-tab" data-tabs-target="#panel-4" type="button"
                    role="tab" aria-controls="panel-4"
                    aria-selected="{{ $activeTab === 'panel-4' ? 'true' : 'false' }}">Buyer</button>
            </li>
            <select class="select select-bordered border-black bg-white text-black font-semibold select-xs w-2/4 max-w-xs p-1 h-8 mr- sm:hidden" wire:model="activeTab" >
                <option value="panel-1" {{ $activeTab === 'panel-1' ? 'selected' : '' }}>Sender</option>
                <option value="panel-2" {{ $activeTab === 'panel-2' ? 'selected' : '' }}>Receiver</option>
                <option value="panel-3" {{ $activeTab === 'panel-3' ? 'selected' : '' }}>Seller</option>
                <option value="panel-4" {{ $activeTab === 'panel-4' ? 'selected' : '' }}>Buyer</option>
            </select>
            <li role="presentation" class="ml-auto hidden sm:block">
                <a href="{{ route('pricing') }}"
                    class="px-4 py-2 text-white bg-[#dc3545] inline-block p-2 border-b-2 border-transparent rounded-xl hover:text-white dark:hover:text-white hover:bg-[#dc3545] ml-auto">Packages</a>
            </li>
            <!-- Add more tab buttons for other panels if needed -->
        </ul>

    </div>
    {{-- @dd($plans) --}}
    <div id="myTabContent" class="mt-10" wire:ignore>
        @foreach ([1, 2, 3, 4] as $panelId)
            {{-- @php
                $filteredPlans = array_filter($plans, function ($plan) use ($panelId) {
                    return $plan['panel_id'] == $panelId;
                });
            @endphp --}}
            @php
                $filteredPlans = array_filter($plans, function ($plan) use ($panelId) {
                    // Assuming 'feature_id' is the key to filter on
                    return $plan['feature']['panel_id'] == $panelId;
                });
            @endphp

            {{-- @dump($filteredPlans, $panelId) --}}

            <div class="hidden p-4 rounded-lg" id="panel-{{ $panelId }}" role="tabpanel"
                aria-labelledby="tab-{{ $panelId }}">
                <div class="col-md-8 package offset-md-2 my-2">
                    <div
                        class="overflow-hidden rounded-lg bg-white shadow-lg transition-transform hover:scale-105 hover:antialiased">
                        <div class="px-6 py-4 text-center">
                            <ul class="font-medium text-gray-700">
                                <li class="flex items-center justify-between py-2">
                                    <div class="w-1/4">Details</div>
                                    <div class="w-1/4">Quantity</div>
                                    <div class="w-1/4">Amount</div>
                                    <div class="w-1/4"></div>
                                </li>
                        </div>
                    </div>
                </div>
                @if ($panelId == 1)
                    @foreach ($filteredPlans as $plan)
                        {{-- @if (isset($plan['additional_feature_topups']) && is_array($plan['additional_feature_topups'])) --}}
                            {{-- @foreach ($plan['additional_feature_topups'] as $topup) --}}
                                <div class="col-md-8 package offset-md-2 my-2">
                                    <div
                                        class="overflow-hidden rounded-lg bg-white shadow-lg transition-transform hover:scale-105 hover:antialiased">
                                        <div class="px-6 py-4 text-center">
                                            <ul class="font-medium text-gray-700">
                                                <li class="flex items-center justify-between py-2">
                                                    <div class="w-1/4">{{ $plan['feature']['feature_name'] }}
                                                    </div>
                                                    <div class="w-1/4">{{ $plan['usage_limit'] }}</div>
                                                    <div class="w-1/4">₹ {{ $plan['price'] }}</div>
                                                    <div class="w-1/4">
                                                        <a href=""
                                                            class="inline-block rounded-md bg-[#d16d4e] px-2 py-1 text-sm text-white hover:bg-[#a0553e]">Buy
                                                            Now</a>
                                                        <a wire:click="addToCart({{ $plan['id'] }})"
                                                            class="hidden rounded-md bg-[#b3bd3a] px-2 py-1 text-sm text-white hover:bg-[#7d8429] lg:inline-block">Add
                                                            to Cart</a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            {{-- @endforeach --}}
                        {{-- @endif --}}
                    @endforeach
                    </ul>
                @elseif ($panelId == 2)
                    @foreach ($filteredPlans as $plan)
                        {{-- @if (isset($plan['additional_feature_topups']) && is_array($plan['additional_feature_topups']))
                            @foreach ($plan['additional_feature_topups'] as $topup) --}}
                                <div class="col-md-8 package offset-md-2 my-2">
                                    <div
                                        class="overflow-hidden rounded-lg bg-white shadow-lg transition-transform hover:scale-105 hover:antialiased">
                                        <div class="px-6 py-4 text-center">
                                            <ul class="font-medium text-gray-700">
                                                <li class="flex items-center justify-between py-2">
                                                    <div class="w-1/4">{{ $plan['feature']['feature_name'] }}
                                                    </div>
                                                    <div class="w-1/4">{{ $plan['usage_limit'] }}</div>
                                                    <div class="w-1/4">₹ {{ $plan['price'] }}</div>
                                                    <div class="w-1/4">
                                                        <a href=""
                                                            class="inline-block rounded-md bg-[#d16d4e] px-2 py-1 text-sm text-white hover:bg-[#a0553e]">Buy
                                                            Now</a>
                                                        <a href=""
                                                            class="hidden rounded-md bg-[#b3bd3a] px-2 py-1 text-sm text-white hover:bg-[#7d8429] lg:inline-block">Add
                                                            to Cart</a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            {{-- @endforeach
                        @endif --}}
                    @endforeach
                    </ul>
                @elseif ($panelId == 3)
                    @foreach ($filteredPlans as $plan)
                        {{-- @if (isset($plan['additional_feature_topups']) && is_array($plan['additional_feature_topups'])) --}}
                            {{-- @foreach ($plan['additional_feature_topups'] as $topup) --}}
                                <div class="col-md-8 package offset-md-2 my-2">
                                    <div
                                        class="overflow-hidden rounded-lg bg-white shadow-lg transition-transform hover:scale-105 hover:antialiased">
                                        <div class="px-6 py-4 text-center">
                                            <ul class="font-medium text-gray-700">
                                                <li class="flex items-center justify-between py-2">
                                                    <div class="w-1/4">{{ $plan['feature']['feature_name'] }}
                                                    </div>
                                                    <div class="w-1/4">{{ $plan['usage_limit'] }}</div>
                                                    <div class="w-1/4">₹ {{ $plan['price'] }}</div>
                                                    <div class="w-1/4">
                                                        <a href=""
                                                            class="inline-block rounded-md bg-[#d16d4e] px-2 py-1 text-sm text-white hover:bg-[#a0553e]">Buy
                                                            Now</a>
                                                        <a href=""
                                                            class="hidden rounded-md bg-[#b3bd3a] px-2 py-1 text-sm text-white hover:bg-[#7d8429] lg:inline-block">Add
                                                            to Cart</a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            {{-- @endforeach --}}
                        {{-- @endif --}}
                    @endforeach
                    </ul>
                @elseif ($panelId == 4)
                    @foreach ($filteredPlans as $plan)
                        {{-- @if (isset($plan['additional_feature_topups']) && is_array($plan['additional_feature_topups'])) --}}
                            {{-- @foreach ($plan['additional_feature_topups'] as $topup) --}}
                                <div class="col-md-8 package offset-md-2 my-2">
                                    <div
                                        class="overflow-hidden rounded-lg bg-white shadow-lg transition-transform hover:scale-105 hover:antialiased">
                                        <div class="px-6 py-4 text-center">
                                            <ul class="font-medium text-gray-700">
                                                <li class="flex items-center justify-between py-2">
                                                    <div class="w-1/4">{{ $plan['feature']['feature_name'] }}
                                                    </div>
                                                    <div class="w-1/4">{{ $plan['usage_limit'] }}</div>
                                                    <div class="w-1/4">₹ {{ $plan['price'] }}</div>
                                                    <div class="w-1/4">
                                                        <a href=""
                                                            class="inline-block rounded-md bg-[#d16d4e] px-2 py-1 text-sm text-white hover:bg-[#a0553e]">Buy
                                                            Now</a>
                                                        <a href=""
                                                            class="hidden rounded-md bg-[#b3bd3a] px-2 py-1 text-sm text-white hover:bg-[#7d8429] lg:inline-block">Add
                                                            to Cart</a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            {{-- @endforeach --}}
                        {{-- @endif --}}
                    @endforeach
                    </ul>



            </div>
    </div>
</div>
@endif
</div>
@endforeach

</div>
</div>
