<div class="border-dashed rounded-lg dark:border-gray-700 flex flex-col w-full">
    {{-- <div class="flex flex-col w-full"> --}}


        {{-- <div class="grid mb-8  md:mb-12 grid-cols-2 rounded-2xl gap-4 p-12 max-w-5xl mx-auto"> --}}
            @if (isset($UserDetails))
            @php
            $uniquePanelNames = collect($UserDetails)->unique('panel.panel_name');
            $allPanelNames = ['Sender', 'Receiver', 'Seller', 'Buyer', 'Receipt_Note'];
            // $allPanelNames = ['Sender', 'Receiver', 'Seller', 'Buyer'];
            // dd($uniquePanelNames);
        @endphp

            @foreach ($allPanelNames as $plan)

            @php
            $user = json_decode($this->user);
            // dd($user);
            @endphp
            {{-- {{
            dd($user,auth()->user(),$plan,strtolower($plan->panel->panel_name),isset(auth()->user()->{strtolower($plan->panel->panel_name)}))
            }} --}}
            @php
                $plan = $uniquePanelNames->firstWhere('panel.panel_name', $plan);
                // dump($plan);
                $uniquePanelNames = collect($UserDetails)->unique('panel.panel_name');
            @endphp

            @if ($plan && strtolower($plan->panel->panel_name))

            @if ($user->team_user != null)
            @php
            $active_plan = array_unique((array)
            $user->team_user->permissions->permission->{strtolower($plan->panel->panel_name)});
            @endphp
            @if (isset($user->team_user->permissions->permission->{strtolower($plan->panel->panel_name)}))
            @if (count(array_unique((array)
            $user->team_user->permissions->permission->{strtolower($plan->panel->panel_name)})) ===
            1)
            @if (reset($active_plan) != 0)
            <div class="flex items-center p-1.5 sm:mb-6 sm:mt-2 mb-1 border-b border-gray-400">
                <span wire:click='panelRedirect({{ $plan->panel->id }})'
                    class="mr-2 rounded-xl cursor-pointer sm:hidden bg-orange w-40 sm:border sm:border-gray-500 p-1 border-none text-center text-black">{{ strtoupper(str_replace('_', ' ', $plan->panel->panel_name)) }}</span>

            </div>

            <div class="gap-2 max-w-6xl mx-auto sm:indicator grid ">

                <div class="stats shadow rounded">
                    <div wire:click='panelRedirect({{ $plan->panel->id }})'
                        class="stat justify-start bg-orange cursor-pointer  w-32 sm:w-44 hidden sm:block">
                        <div class="text-black whitespace-nowrap text-center">{{ strtoupper(str_replace('_', ' ', $plan->panel->panel_name)) }}</div>
                    </div>

                    <div class="stat justify-start bg-white w-44 overflow-hidden"
                    @if($plan->panel->panel_name == 'Sender')
                        wire:click="featureRedirect('sent_challan', '2', '{{ $plan->panel->id }}')"
                    @elseif($plan->panel->panel_name == 'Receiver')
                        wire:click="featureRedirect('sent_return_challan', '8', '{{ $plan->panel->id }}')"
                    @elseif($plan->panel->panel_name == 'Seller')
                        wire:click="featureRedirect('sent_invoice', '13', '{{ $plan->panel->id }}')"
                    @elseif($plan->panel->panel_name == 'Buyer')
                        wire:click="featureRedirect('buyer_template', null, '{{ $plan->panel->id }}')"
                    @elseif($plan->panel->panel_name == 'Receipt_Note')
                        wire:click="featureRedirect('receipt_note', 23, '{{ $plan->panel->id }}')"
                    @endif

                    >
                        @php
                        $name = '';
                        $pending = '';
                        switch($plan->panel->panel_name) {
                        case 'Sender':
                        $name = 'Challan Sent';
                        $pending = 'Sent Pending';
                        $sentChallan = $sentChallan;
                        $sentCount = $sentCount;
                        break;
                        case 'Receiver':
                        // @dump($receivedChallan);
                        $name = 'Received Challan';
                        $pending = 'Received Pending';
                        $r_sentChallan = $receivedChallan;
                        // $rsentCount = $rsentCount;
                        break;
                        case 'Seller':
                        $name = 'Invoice Sent';
                        $pending = 'Sent Pending';

                        break;
                        case 'Buyer':
                        $name = 'Received Invoice';
                        $pending = 'Received Pending';

                        break;
                        case 'Receipt_Note':
                        $name = 'Receipt Note';
                        $pending = 'Pending';
                        break;
                        }
                        @endphp

                        <div class="stat-title text-black sm:text-sm text-xs bg-white overflow-hidden">{{ $name }}</div>
                        <div class="stat-value text-xs text-purple-700 font-normal overflow-hidden">
                            @if($plan->panel->panel_name == 'Sender'){{$draftChallanCounts}}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$sentReturnChallanCounts}}
                            @elseif($plan->panel->panel_name == 'Seller'){{$sentInvoiceCounts}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Receipt_Note'){{$sentGoodsReceiptCounts}}
                            @endif
                        </div>
                        {{-- <div class="stat-value text-xs text-purple-700 font-normal overflow-hidden">
                            @if($plan->panel->panel_name == 'Sender')
                                <a href="{{ route('sender.route') }}">{{$sentChallan}}</a>
                            @elseif($plan->panel->panel_name == 'Receiver')
                                <a href="{{ route('receiver.route') }}">{{$r_sentChallan}}</a>
                            @elseif($plan->panel->panel_name == 'Seller')
                                <a href="{{ route('seller.route') }}">{{$sentInvoiceCounts}}</a>
                            @elseif($plan->panel->panel_name == 'Buyer')
                                <a href="{{ route('buyer.route') }}">-</a>
                            @endif
                        </div> --}}
                        {{-- <div class="stat-desc">Jan 1st - Feb 1st</div> --}}
                    </div>

                    <div class="stat justify-start sm:text-sm text-xs bg-white w-44">

                        <div class="stat-title text-black">{{ $pending }}</div>
                        <div class="stat-value text-xs font-normal text-red-500">
                            @if($plan->panel->panel_name == 'Sender'){{ $draftChallanCounts }}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$draftReturnChallanCounts}}
                            @elseif($plan->panel->panel_name == 'Seller'){{$draftInvoiceCounts}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Receipt_Note'){{$draftGoodsReceiptCounts}}
                            @endif
                        </div>
                        {{-- <div class="stat-desc">↗︎ 400 (22%)</div> --}}
                    </div>


                </div>
                @php
                $name_r = '';
                $pending_r = '';
                switch($plan->panel->panel_name) {
                case 'Sender':
                $name_r = 'Challans Received';
                $pending_r = 'Received Pending';
                $receivedChallan = $receivedChallan;
                $receivedCount = $receivedCount;
                break;
                case 'Receiver':
                $name_r = 'Challans Sent';
                $pending_r = 'Sent Pending';
                break;
                case 'Seller':
                $name_r = 'PO Received';
                $pending_r = 'Received Pending';
                break;
                case 'Buyer':
                $name_r = 'PO Sent';
                $pending_r = 'Sent Pending';
                break;
                case 'Receipt_Note':
                $name_r = 'Receipt Note';
                $pending_r = 'Pending';
                    break;
                }
                @endphp
                 <div class="stats shadow rounded">


                    <div class="stat justify-start bg-white w-44">
                        {{-- <div class="stat-figure text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="inline-block w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div> --}}
                        {{-- @dd($sentChallan); --}}


                        <div class="stat-title text-black sm:text-sm text-xs   bg-white">{{ $name_r }}</div>
                        <div class="stat-value text-sm text-purple-700 font-normal">
                            @if($plan->panel->panel_name == 'Sender'){{$receivedChallan}}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$sentReturnChallan}}
                            @elseif($plan->panel->panel_name == 'Seller'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Receipt_Note'){{'-'}}
                            @endif</div>
                        {{-- <div class="stat-desc">Jan 1st - Feb 1st</div> --}}
                    </div>

                    <div class="stat justify-start sm:text-sm text-xs bg-white w-44">

                        <div class="stat-title text-black">{{ $pending_r }}</div>
                        <div class="stat-value text-xs font-normal text-red-500">
                            @if($plan->panel->panel_name == 'Sender'){{ $receivedCount }}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$receiverSentCount}}
                            @elseif($plan->panel->panel_name == 'Seller'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Receipt_Note'){{'-'}}
                            @endif</div>
                        {{-- <div class="stat-desc">↗︎ 400 (22%)</div> --}}
                    </div>


                </div>

            </div>
            @endif
            @else
            <div class="flex items-center p-1.5 sm:mb-6 sm:mt-2 mb-1 border-b border-gray-400">
                <span wire:click='panelRedirect({{ $plan->panel->id }})'
                    class="mr-2 rounded-xl cursor-pointer sm:hidden bg-orange w-40 sm:border sm:border-gray-500 p-1 border-none text-center text-black">{{ strtoupper(str_replace('_', ' ', $plan->panel->panel_name)) }}</span>

            </div>



            <div class="gap-2 max-w-6xl mx-auto sm:indicator grid ">

                <div class="stats shadow rounded">
                    <div wire:click='panelRedirect({{ $plan->panel->id }})'
                        class="stat justify-start bg-orange cursor-pointer  w-32 sm:w-44 hidden sm:block">
                        <div class="text-black whitespace-nowrap text-center">{{ strtoupper(str_replace('_', ' ', $plan->panel->panel_name)) }}</div>
                    </div>

                    <div class="stat justify-start bg-white w-44 overflow-hidden">
                        {{-- <div class="stat-figure text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="inline-block w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div> --}}
                        {{-- @dd($sentChallan); --}}
                        @php
                        $name = '';
                        $pending = '';
                        switch($plan->panel->panel_name) {
                        case 'Sender':
                        $name = 'Challan Sent';
                        $pending = 'Sent Pending';
                        $sentChallan = $sentChallan;
                        $sentCount = $sentCount;
                        break;
                        case 'Receiver':
                        // @dump($receivedChallan);
                        $name = 'Received Challan';
                        $pending = 'Received Pending';
                        $r_sentChallan = $receivedChallan;
                        // $rsentCount = $rsentCount;
                        break;
                        case 'Seller':
                        $name = 'Invoice Sent';
                        $pending = 'Sent Pending';

                        break;
                        case 'Buyer':
                        $name = 'Received Invoice';
                        $pending = 'Received Pending';

                        break;
                        case 'Receipt Note':
                        $name = 'Receipt Note';
                        $pending = 'Pending';
                        break;
                        }
                        @endphp

                        <div class="stat-title text-black sm:text-sm text-xs bg-white overflow-hidden">{{ $name }}</div>
                        <div class="stat-value text-xs text-purple-700 font-normal overflow-hidden">
                            @if($plan->panel->panel_name == 'Sender'){{$draftChallanCounts}}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$sentReturnChallanCounts}}
                            @elseif($plan->panel->panel_name == 'Seller'){{$sentInvoiceCounts}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Receipt Note'){{'-'}}
                            @endif
                        </div>
                        {{-- <div class="stat-value text-xs text-purple-700 font-normal overflow-hidden">
                            @if($plan->panel->panel_name == 'Sender')
                                <a href="{{ route('sender.route') }}">{{$sentChallan}}</a>
                            @elseif($plan->panel->panel_name == 'Receiver')
                                <a href="{{ route('receiver.route') }}">{{$r_sentChallan}}</a>
                            @elseif($plan->panel->panel_name == 'Seller')
                                <a href="{{ route('seller.route') }}">{{$sentInvoiceCounts}}</a>
                            @elseif($plan->panel->panel_name == 'Buyer')
                                <a href="{{ route('buyer.route') }}">-</a>
                            @endif
                        </div> --}}
                        {{-- <div class="stat-desc">Jan 1st - Feb 1st</div> --}}
                    </div>

                    <div class="stat justify-start sm:text-sm text-xs bg-white w-44">

                        <div class="stat-title text-black">{{ $pending }}</div>
                        <div class="stat-value text-xs font-normal text-red-500">
                            @if($plan->panel->panel_name == 'Sender'){{ $draftChallanCounts }}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$draftReturnChallanCounts}}
                            @elseif($plan->panel->panel_name == 'Seller'){{$sentInvoiceCount}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @endif
                        </div>
                        {{-- <div class="stat-desc">↗︎ 400 (22%)</div> --}}
                    </div>


                </div>
                @php
                $name_r = '';
                $pending_r = '';
                switch($plan->panel->panel_name) {
                case 'Sender':
                $name_r = 'Challans Received';
                $pending_r = 'Received Pending';
                $receivedChallan = $receivedChallan;
                $receivedCount = $receivedCount;
                break;
                case 'Receiver':
                $name_r = 'Challans Sent';
                $pending_r = 'Sent Pending';
                break;
                case 'Seller':
                $name_r = 'PO Received';
                $pending_r = 'Received Pending';
                break;
                case 'Buyer':
                $name_r = 'PO Sent';
                $pending_r = 'Sent Pending';
                break;
                case 'Receipt_Note':
                $name_r = 'Receipt Note';
                $pending_r = 'Pending';
                    break;
                }
                @endphp
                 <div class="stats shadow rounded">


                    <div class="stat justify-start bg-white w-44">
                        {{-- <div class="stat-figure text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="inline-block w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div> --}}
                        {{-- @dd($sentChallan); --}}


                        <div class="stat-title text-black sm:text-sm text-xs   bg-white">{{ $name_r }}</div>
                        <div class="stat-value text-sm text-purple-700 font-normal">
                            @if($plan->panel->panel_name == 'Sender'){{$receivedChallan}}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$sentReturnChallan}}
                            @elseif($plan->panel->panel_name == 'Seller'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @endif</div>
                        {{-- <div class="stat-desc">Jan 1st - Feb 1st</div> --}}
                    </div>

                    <div class="stat justify-start sm:text-sm text-xs bg-white w-44">

                        <div class="stat-title text-black">{{ $pending_r }}</div>
                        <div class="stat-value text-xs font-normal text-red-500">
                            @if($plan->panel->panel_name == 'Sender'){{ $receivedCount }}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$receiverSentCount}}
                            @elseif($plan->panel->panel_name == 'Seller'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @endif</div>
                        {{-- <div class="stat-desc">↗︎ 400 (22%)</div> --}}
                    </div>


                </div>

            </div>
            @endif
            @endif

            @else
            {{-- @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) ||
            is_null($user->city))
            <script>
                window.location.href = "{{ route('profile') }}";
            </script>
            @php
            return;
            @endphp
            @endif --}}
            {{-- @dump($plan) --}}
            {{-- // Get the last items in the arrays
            $lastSender = end($UserDetails['Sender']);
            $lastReceiver = end($UserDetails['Receiver']);
            $lastSeller = end($UserDetails['Seller']);
            $lastBuyer = end($UserDetails['Buyer']);
        @endphp

        @if (
            (
                strtolower($plan->panel->panel_name) &&
                isset(auth::user()->{strtolower($plan->panel->panel_name)}) &&
                auth::user()->{strtolower($plan->panel->panel_name)} == 1
            ) || (
                // Check expiry_date for each array
                $lastSender['expiry_date'] < now() &&
                $lastReceiver['expiry_date'] < now() &&
                $lastSeller['expiry_date'] < now() &&
                $lastBuyer['expiry_date'] < now() &&
                !in_array($plan->plan_id, [3, 11, 10, 19])
            )
        ) --}}
                {{-- @dump($plan) --}}
                {{-- ADMIN PANEL DASHBOARD --}}
                @if (auth()->check() && !auth::user()->team_user && auth::user()->sender == false && auth::user()->receiver == false && auth::user()->seller == false && auth::user()->buyer == false)
                    <script>window.location = "{{ route('all-feature') }}";</script>
                @endif
                {{-- @dump($plan)  --}}
            @if (
                (strtolower($plan->panel->panel_name) &&
                isset(auth::user()->{strtolower($plan->panel->panel_name)}) &&
                auth::user()->{strtolower($plan->panel->panel_name)} == 1) ||
                ($plan->expiry_date < now() && !in_array($plan->plan_id, [50, 11, 10, 19, 58]))
            )

            <div class="flex items-center p-1.5 sm:mb-6 sm:mt-2 mb-1 border-b border-gray-400">
                <span wire:click='panelRedirect({{ $plan->panel->id }})'
                    class="mr-2 rounded-xl cursor-pointer sm:hidden bg-orange w-40 sm:border sm:border-gray-500 p-1 border-none text-center text-black">
                    {{ strtoupper(str_replace('_', ' ', $plan->panel->panel_name)) }}</span>
            </div>



            <div class="gap-2 max-w-6xl mx-auto sm:indicator grid ">
                {{-- @dump($plan->panel->panel_name) --}}
                <div class="stats shadow rounded bg-orange">
                    <div wire:click='panelRedirect({{ $plan->panel->id }})'
                        class="stat justify-start bg-orange cursor-pointer my-auto w-32 sm:w-44 hidden sm:block">
                        <div class="text-black whitespace-nowrap text-center">{{ strtoupper(str_replace('_', ' ', $plan->panel->panel_name)) }}</div>
                    </div>

                    <div class="stat justify-start bg-white w-44 overflow-hidden"

                    >

                        @php
                        $name = '';
                        $pending = '';
                        switch($plan->panel->panel_name) {
                        case 'Sender':
                        $name = 'Challan Sent';
                        $pending = 'Sent Pending';
                        $sentChallan = $sentChallan;
                        $sentCount = $sentCount;
                        break;
                        case 'Receiver':
                        // @dump($receivedChallan);
                        $name = 'Received Challan';
                        $pending = 'Received Pending';
                        $r_sentChallan = $receivedChallan;
                        // $rsentCount = $rsentCount;
                        break;
                        case 'Seller':
                        $name = 'Invoice Sent';
                        $pending = 'Sent Pending';

                        break;
                        case 'Buyer':
                        $name = 'Received Invoice';
                        $pending = 'Received Pending';

                        break;
                        case 'Receipt_Note':
                        $name = 'Receipt Note';
                        $pending = 'Pending';
                        break;
                        }
                        @endphp
                            <div class="cursor-pointer"
                                @if($plan->panel->panel_name == 'Sender')
                                    wire:click="featureRedirect('sent_challan', '2', '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Receiver')
                                    wire:click="featureRedirect('received_return_challan', '8', '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Seller')
                                    wire:click="featureRedirect('sent_invoice', '13', '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Buyer')
                                    wire:click="featureRedirect('all_invoice', 18, '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Receipt_Note')
                                    wire:click="featureRedirect('sent-goods-receipt', 23, '{{ $plan->panel->id }}')"
                                @endif
                            >
                            <div class="stat-title text-black sm:text-sm text-xs bg-white overflow-hidden">{{ $name }}</div>
                                <div class="stat-value text-xs text-purple-700 font-normal overflow-hidden">
                                    @if($plan->panel->panel_name == 'Sender'){{$sentChallanCounts}}
                                    @elseif($plan->panel->panel_name == 'Receiver'){{$sentReturnChallanCounts}}
                                    @elseif($plan->panel->panel_name == 'Seller'){{$sentInvoiceCounts}}
                                    @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                                    @elseif($plan->panel->panel_name == 'Receipt_Note'){{$sentGoodsReceiptCounts}}
                                    @endif
                                </div>
                            </div>
                        {{-- <div class="stat-value text-xs text-purple-700 font-normal overflow-hidden">
                            @if($plan->panel->panel_name == 'Sender')
                                <a href="{{ route('sender.route') }}">{{$sentChallan}}</a>
                            @elseif($plan->panel->panel_name == 'Receiver')
                                <a href="{{ route('receiver.route') }}">{{$r_sentChallan}}</a>
                            @elseif($plan->panel->panel_name == 'Seller')
                                <a href="{{ route('seller.route') }}">{{$sentInvoiceCounts}}</a>
                            @elseif($plan->panel->panel_name == 'Buyer')
                                <a href="{{ route('buyer.route') }}">-</a>
                            @endif
                        </div> --}}
                        {{-- <div class="stat-desc">Jan 1st - Feb 1st</div> --}}
                    </div>

                    <div class="stat justify-start sm:text-sm text-xs bg-white w-44 cursor-pointer"

                                @if($plan->panel->panel_name == 'Sender')
                                    wire:click="featureRedirect('sent_challan', '2', '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Receiver')
                                    wire:click="featureRedirect('received_return_challan', '8', '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Seller')
                                    wire:click="featureRedirect('sent_invoice', '13', '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Buyer')
                                    wire:click="featureRedirect('all_invoice', 18, '{{ $plan->panel->id }}')"
                                @elseif($plan->panel->panel_name == 'Receipt_Note')
                                    wire:click="featureRedirect('sent-goods-receipt', 23, '{{ $plan->panel->id }}')"
                                @endif
                            >


                        <div class="stat-title text-black">{{ $pending }}</div>
                        <div class="stat-value text-xs font-normal text-red-500">
                            @if($plan->panel->panel_name == 'Sender'){{ $draftChallanCounts }}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$draftReturnChallanCounts}}
                            @elseif($plan->panel->panel_name == 'Seller'){{$draftInvoiceCounts}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Receipt_Note'){{$draftGoodsReceiptCounts}}

                            @endif
                        </div>
                        {{-- <div class="stat-desc">↗︎ 400 (22%)</div> --}}
                    </div>


                </div>
                @php
                $name_r = '';
                $pending_r = '';
                switch($plan->panel->panel_name) {
                case 'Sender':
                $name_r = 'Challans Received';
                $pending_r = 'Received Pending';
                $receivedChallan = $receivedChallan;
                $receivedCount = $receivedCount;
                break;
                case 'Receiver':
                $name_r = 'Challans Sent';
                $pending_r = 'Sent Pending';
                break;
                case 'Seller':
                $name_r = 'PO Received';
                $pending_r = 'Received Pending';
                break;
                case 'Buyer':
                $name_r = 'PO Sent';
                $pending_r = 'Sent Pending';
                break;
                case 'Receipt_Note':
                $name_r = 'Receipt Note';
                $pending_r = 'Pending';
                    break;
                }
                @endphp
                 <div class="stats shadow rounded">


                    <div class="stat justify-start bg-white w-44 cursor-pointer"
                        @if($plan->panel->panel_name == 'Sender')
                            wire:click="featureRedirect('received_challan', '3', '{{ $plan->panel->id }}')"
                        @elseif($plan->panel->panel_name == 'Receiver')
                            wire:click="featureRedirect('sent_return_challan', '9', '{{ $plan->panel->id }}')"
                        @elseif($plan->panel->panel_name == 'Seller')
                            wire:click="featureRedirect('purchase_order_seller', '14', '{{ $plan->panel->id }}')"
                        @elseif($plan->panel->panel_name == 'Buyer')
                            wire:click="featureRedirect('purchase_order', 19, '{{ $plan->panel->id }}')"
                        @endif
                    >
                        {{-- <div class="stat-figure text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="inline-block w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div> --}}
                        {{-- @dd($sentChallan); --}}


                        <div class="stat-title text-black sm:text-sm text-xs   bg-white">{{ $name_r }}</div>
                        <div class="stat-value text-sm text-purple-700 font-normal">
                            @if($plan->panel->panel_name == 'Sender'){{$receivedChallan}}
                            @elseif($plan->panel->panel_name == 'Receiver'){{$sentReturnChallan}}
                            @elseif($plan->panel->panel_name == 'Seller'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{$sentPo}}
                            @elseif($plan->panel->panel_name == 'Receipt_Note'){{'-'}}
                            @endif</div>
                        {{-- <div class="stat-desc">Jan 1st - Feb 1st</div> --}}
                    </div>

                    <div class="stat justify-start sm:text-sm text-xs bg-white w-44 cursor-pointer"
                        @if($plan->panel->panel_name == 'Sender')
                            wire:click="featureRedirect('received_challan', '3', '{{ $plan->panel->id }}')"
                        @elseif($plan->panel->panel_name == 'Receiver')
                            wire:click="featureRedirect('sent_return_challan', '9', '{{ $plan->panel->id }}')"
                        @elseif($plan->panel->panel_name == 'Seller')
                            wire:click="featureRedirect('purchase_order_seller', '14', '{{ $plan->panel->id }}')"
                        @elseif($plan->panel->panel_name == 'Buyer')
                            wire:click="featureRedirect('purchase_order', 19, '{{ $plan->panel->id }}')"
                        @endif
                    >

                        <div class="stat-title text-black">{{ $pending_r }}</div>
                        <div class="stat-value text-xs font-normal text-red-500">
                            @if($plan->panel->panel_name == 'Sender')
                            {{-- {{ $receivedCount }} --}}
                            @elseif($plan->panel->panel_name == 'Receiver')
                            {{-- {{$receiverSentCount}} --}}
                            @elseif($plan->panel->panel_name == 'Seller'){{'-'}}
                            @elseif($plan->panel->panel_name == 'Buyer'){{$draftPo}}
                            @endif</div>
                    </div>


                </div>

            </div>

            @endif
            @endif
            @endif
            @endforeach
            @endif


        @if (isset($UserDetails))
            @php
                $uniquePanelNames = collect($UserDetails)->unique('panel.panel_name');
                $allPanelNames = ['Sender', 'Receiver', 'Seller', 'Buyer', 'Receipt_Note'];
            @endphp

            @foreach($allPanelNames as $panelName)
                @php
                    $plan = $uniquePanelNames->firstWhere('panel.panel_name', $panelName);
                    // dd($plan);
                    $uniquePanelNames = collect($UserDetails)->unique('panel.panel_name');
                    // dd($uniquePanelNames);
                @endphp

                @if(!$plan)
                    <!-- Show the expiredPlan for this panel -->
                    <div class="flex items-center p-1.5 sm:mb-6 sm:mt-2 mb-1 border-b border-gray-400 ">
                        <span
                            class="mr-2 rounded-xl cursor-pointer sm:hidden bg-orange w-40 sm:border sm:border-gray-500 p-1 border-none text-center text-black">{{
                            $panelName }}</span>

                    </div>
                    <div id="expiredMessage" class="hidden">
                        <div id="alert-2" class="flex items-center p-2 mb-4 text-red-600 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 gap-2 max-w-4xl mx-auto alert grid "     role="alert">
                            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                            </svg>
                            <span class="sr-only">Info</span>
                            <div class="ms-3 text-sm font-medium">
                                This plan has expired. <a onclick="window.location='{{ route('pricing') }}'" class="font-semibold underline hover:no-underline">Subscribe now</a>. to keep using.
                            </div>
                            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-2" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            </button>
                        </div>
                    </div>
                    <script>
                        function showExpiredMessage() {
                            document.getElementById('expiredMessage').classList.remove('hidden');
                        }
                    </script>
                    {{-- <span class="gap-2 max-w-6xl mx-auto sm:indicator grid">This plan has expired. Subscribe now to keep using</span>  --}}
                    <div class="gap-2 max-w-6xl mx-auto sm:indicator grid ">





                        <div class="stats shadow rounded opacity-50">

                            <div onclick="showExpiredMessage()"  class="stat justify-start  bg-orange cursor-pointer   w-32 sm:w-44 hidden sm:block">
                                <div class="text-black text-center">{{ strtoupper(str_replace('_', ' ', $panelName)) }}</div>
                            </div>

                            <div class="stat justify-start bg-white w-44 overflow-hidden"
                            >
                                {{-- <div class="stat-figure text-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        class="inline-block w-8 h-8 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div> --}}
                                {{-- @dd($sentChallan); --}}
                                @php
                                $name = '';
                                $pending = '';
                                switch($panelName) {
                                case 'Sender':
                                $name = 'Challan Sent';
                                $pending = 'Sent Pending';
                                $sentChallan = $sentChallan;
                                $sentCount = $sentCount;
                                break;
                                case 'Receiver':
                                // @dump($receivedChallan);
                                $name = 'Received Challan';
                                $pending = 'Received Pending';
                                $r_sentChallan = $receivedChallan;
                                // $rsentCount = $rsentCount;
                                break;
                                case 'Seller':
                                $name = 'Invoice Sent';
                                $pending = 'Sent Pending';

                                break;
                                case 'Buyer':
                                $name = 'Received Invoice';
                                $pending = 'Received Pending';

                                break;
                                case 'Receipt_Note':
                                $name = 'Receipt Note';
                                $pending = 'Pending';
                                break;
                                }
                                @endphp
                                    <div class="cursor-pointer" onclick="showExpiredMessage()"
                                        @if($panelName == 'Sender')

                                        @elseif($panelName == 'Receiver')

                                        @elseif($panelName == 'Seller')

                                        @elseif($panelName == 'Buyer')

                                        @elseif($panelName == 'Receipt_Note')
                                        @endif
                                    >
                                    <div class="stat-title text-black sm:text-sm text-xs bg-white overflow-hidden">{{ $name }}</div>
                                        <div class="stat-value text-xs text-purple-700 font-normal overflow-hidden">
                                            @if($panelName == 'Sender'){{$draftChallanCounts}}
                                            @elseif($panelName == 'Receiver'){{$sentReturnChallanCounts}}
                                            @elseif($panelName == 'Seller'){{$sentInvoiceCounts}}
                                            @elseif($panelName == 'Buyer'){{'-'}}
                                            @elseif($panelName == 'Receipt_Note'){{'-'}}
                                            @endif
                                        </div>
                                    </div>

                            </div>

                            <div class="stat justify-start sm:text-sm text-xs bg-white w-44 cursor-pointer" onclick="showExpiredMessage()"

                                        @if($panelName == 'Sender')

                                        @elseif($panelName == 'Receiver')

                                        @elseif($panelName == 'Seller')

                                        @elseif($panelName == 'Buyer')
                                        @elseif($panelName == 'Receipt_Note')

                                        @endif
                                    >


                                <div class="stat-title text-black">{{ $pending }}</div>
                                <div class="stat-value text-xs font-normal text-red-500">
                                    @if($panelName == 'Sender')
                                    {{-- {{ $sentCount }} --}}
                                    @elseif($panelName == 'Receiver')
                                    {{-- {{$rsentCount}} --}}
                                    @elseif($panelName == 'Seller')
                                    {{-- {{$sentInvoiceCount}} --}}
                                    @elseif($panelName == 'Buyer'){{'-'}}
                                    @elseif($panelName == 'Receipt_Note'){{'-'}}
                                    @endif
                                </div>
                                {{-- <div class="stat-desc">↗︎ 400 (22%)</div> --}}
                            </div>


                        </div>
                        @php
                        $name_r = '';
                        $pending_r = '';
                        switch($panelName) {
                        case 'Sender':
                        $name_r = 'Challans Received';
                        $pending_r = 'Received Pending';
                        $receivedChallan = $receivedChallan;
                        $receivedCount = $receivedCount;
                        break;
                        case 'Receiver':
                        $name_r = 'Challans Sent';
                        $pending_r = 'Sent Pending';
                        break;
                        case 'Seller':
                        $name_r = 'PO Received';
                        $pending_r = 'Received Pending';
                        break;
                        case 'Buyer':
                        $name_r = 'PO Sent';
                        $pending_r = 'Sent Pending';
                        break;
                        case 'Receipt_Note':
                        $name_r = 'Receipt Note';
                        $pending_r = 'Pending';
                        }
                        @endphp
                         <div class="stats shadow rounded opacity-50">


                            <div class="stat justify-start bg-white w-44 cursor-pointer" onclick="showExpiredMessage()"
                                @if($panelName == 'Sender')

                                @elseif($panelName == 'Receiver')

                                @elseif($panelName == 'Seller')

                                @elseif($panelName == 'Buyer')
                                @elseif ($panelName == 'Receipt_Note')
                                @endif
                            >
                                {{-- <div class="stat-figure text-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        class="inline-block w-8 h-8 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div> --}}
                                {{-- @dd($sentChallan); --}}


                                <div class="stat-title text-black sm:text-sm text-xs   bg-white">{{ $name_r }}</div>
                                <div class="stat-value text-sm text-purple-700 font-normal">
                                    @if($panelName == 'Sender'){{$receivedChallan}}
                                    @elseif($panelName == 'Receiver'){{$sentReturnChallan}}
                                    @elseif($panelName == 'Seller'){{'-'}}
                                    @elseif($panelName == 'Buyer'){{$sentPo}}
                                    @endif</div>
                                {{-- <div class="stat-desc">Jan 1st - Feb 1st</div> --}}
                            </div>

                            <div class="stat justify-start sm:text-sm text-xs bg-white w-44 cursor-pointer" onclick="showExpiredMessage()"
                                @if($panelName == 'Sender')

                                @elseif($panelName == 'Receiver')

                                @elseif($panelName == 'Seller')

                                @elseif($panelName == 'Buyer')

                                @endif
                            >

                                <div class="stat-title text-black">{{ $pending_r }}</div>
                                <div class="stat-value text-xs font-normal text-red-500">
                                    @if($panelName == 'Sender')
                                    {{-- {{ $receivedCount }} --}}
                                    @elseif($panelName == 'Receiver')
                                    {{-- {{$receiverSentCount}} --}}
                                    @elseif($panelName == 'Seller'){{'-'}}
                                    @elseif($panelName == 'Buyer'){{$draftPo}}
                                    @endif</div>
                            </div>


                        </div>

                    </div>
                @else
                    <!-- Your code to handle each panel name -->
                @endif
            @endforeach
        @endif

    <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    {{-- <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg> --}}
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Your Plan has Expired</h3>
                    <button data-modal-hide="popup-modal" type="button" class="text-white bg-orange hover:bg-orange focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center" onclick="window.location='{{ route('pricing') }}'">
                        Buy Plan
                    </button>
                    <button data-modal-hide="popup-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No, cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
