@if (isset($sidenav)) 
@foreach ($sidenav as $nav)
    @php
        $user = json_decode($this->user);
        $template = isset($nav['template']) ? $nav['template']['template_page_name'] : 'index';
        // dd($user->team_user->permissions->permission->{strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']))}->{strtolower(str_replace(' ', '_', $nav['feature_name']))}, $sidenav, $nav);
        // dd($user, $user );
    @endphp
   @if ($user->team_user && isset($user->team_user->permissions->permission->{strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']))}->{strtolower(str_replace(' ', '_', $nav['feature_name']))}))
        @if ($user->team_user->permissions->permission->{strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']))}->{strtolower(str_replace(' ', '_', $nav['feature_name']))} == 1)
            <li>
                <a 
                    wire:click="featureRedirect('{{ $template }}', '{{ $nav['id'] }}')" 
                    @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
             disabled @endif
                    @endif
                    class="
            @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
            cursor-not-allowed
            @else @endif
            @else

            @endif
            flex px-1 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg text-sm items-center dark:focus:ring-gray-500 group drop-shadow-lg">
                    @if (isset($nav['feature_icon']))
                        <svg class="w-5 h-5 text-black transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 22 21">
                            <path
                                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                            <path
                                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                        </svg>
                    @endif
                    <span class="flex-1 ml-1 whitespace-nowrap text-sm">{{ $nav['feature_name'] }}</span>
                    @if ($nav['status'] != 'active')
                        <span
                            class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs  mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Disabled</span>
                    @endif
                    @if ($nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0)
                        @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                            <span
                                class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs  mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Expired</span>
                        @endif
                    @endif
                    @if (isset($nav['total_count']))
                        <span
                            class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs    rounded-full dark:bg-green-900 dark:text-green-300">{{ $nav['total_count'] }}</span>
                    @endif
                    
                </a>

            </li>
        @endif
    @else
            @if(isset($activePlan))
                @php 
                    $lastUsageRecordChallan = null;
                    $lastUsageRecordInvoice = null;
                    $lastUsageRecordReturnChallan = null;
                    $lastUsageRecordPurchaseOrder = null;
                @endphp
            @foreach(json_decode($activePlan) as $order)
                    @if(is_array($order))
                        @foreach($order as $orderDetail)
                            @foreach($orderDetail->feature_usage_records as $usageRecord)
                                @if($usageRecord->feature->feature_name === 'Create Challan')
                                    @php $lastUsageRecordChallan = $usageRecord; @endphp
                                @elseif($usageRecord->feature->feature_name === 'Create Invoice')
                                    @php $lastUsageRecordInvoice = $usageRecord; @endphp
                                @elseif($usageRecord->feature->feature_name === 'Create Return Challan')
                                    @php $lastUsageRecordReturnChallan = $usageRecord; @endphp
                                @elseif($usageRecord->feature->feature_name === 'Purchase Order')
                                    @php $lastUsageRecordPurchaseOrder = $usageRecord; @endphp
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                @endforeach
            @endif  
        <li>
                    <a @if ( (
                                $nav['feature_name'] === 'Create Challan' && 
                                isset($lastUsageRecordChallan) && 
                                $lastUsageRecordChallan->usage_count === $lastUsageRecordChallan->usage_limit
                            ) || (
                                $nav['feature_name'] === 'Create Invoice' && 
                                isset($lastUsageRecordInvoice) && 
                                $lastUsageRecordInvoice->usage_count === $lastUsageRecordInvoice->usage_limit
                            ) || (
                                $nav['feature_name'] === 'Create Return Challan' && 
                                isset($lastUsageRecordReturnChallan) && 
                                $lastUsageRecordReturnChallan->usage_count === $lastUsageRecordReturnChallan->usage_limit
                            ) || (
                                $nav['feature_name'] === 'Purchase Order' && 
                                isset($lastUsageRecordPurchaseOrder) && 
                                $lastUsageRecordPurchaseOrder->usage_count === $lastUsageRecordPurchaseOrder->usage_limit
                            )
                        )
                            onclick="showExpiredMessage()" 
                @else
            
                wire:click="featureRedirect('{{ $template }}', '{{ $nav['id'] }}')"
                {{-- href="{{ route('sent-challan') }}" --}}
                @endif
                @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0 ) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
             disabled @endif
                @endif
                @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                onclick="window.location.href = '{{ route('profile') }}'; return false;" 
            
            @endif
                class="
            @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
            cursor-not-allowed
            @else @endif
                @else
                @endif
                {{-- Show alert for count over --}}
                    flex px-1  font-normal hover:text-white py-2 hover:bg-orange @if (
                    (
                        $nav['feature_name'] === 'Create Challan' && 
                        isset($lastUsageRecordChallan) && 
                        $lastUsageRecordChallan->usage_count === $lastUsageRecordChallan->usage_limit
                    ) || (
                        $nav['feature_name'] === 'Create Invoice' && 
                        isset($lastUsageRecordInvoice) && 
                        $lastUsageRecordInvoice->usage_count === $lastUsageRecordInvoice->usage_limit
                    ) || (
                        $nav['feature_name'] === 'Create Return Challan' && 
                        isset($lastUsageRecordReturnChallan) && 
                        $lastUsageRecordReturnChallan->usage_count === $lastUsageRecordReturnChallan->usage_limit
                    ) || (
                        $nav['feature_name'] === 'Purchase Order' && 
                        isset($lastUsageRecordPurchaseOrder) && 
                        $lastUsageRecordPurchaseOrder->usage_count === $lastUsageRecordPurchaseOrder->usage_limit
                    ) )  text-gray-500 bg-gray-300 @else  text-black bg-grays @endif border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg text-sm items-center dark:focus:ring-gray-500 group drop-shadow-lg">
                @if (isset($nav['feature_icon']))
                    <svg class="w-5 h-5 text-black transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 22 21">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                        <path
                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                    </svg>
                @endif  
                        <span class="flex-1 ml-1 whitespace-nowrap text-sm">{{ $nav['feature_name'] }}</span>
                        @if ($nav['status'] != 'active')
                            <span
                                class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs  mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Disabled</span>
                        @endif
                        @if ($nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0)
                            @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                                <span
                                    class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs  mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Expired</span>
                            @endif
                        @endif
                        @if (isset($nav['total_count']))
                            <span
                                class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs    rounded-full dark:bg-green-900 dark:text-green-300">{{ $nav['total_count'] }}</span>
                        @endif
                       
                        {{-- @if (isset($sentChallan) && $nav['feature_name'] === 'Sent Challan')
                        <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs  rounded-full dark:bg-green-900 dark:text-green-300"> {{ ($sentChallan) }}</span>
                        @endif
                        @if (isset($sentReturnChallan) && $nav['feature_name'] === 'Sent Return Challan')
                        <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs  rounded-full dark:bg-green-900 dark:text-green-300"> {{ count($sentReturnChallan) }}</span>
                        @endif
                        @if (isset($invoiceData) && $nav['feature_name'] === 'Sent Invoice' )
                        <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs  rounded-full dark:bg-green-900 dark:text-green-300"> {{ ($invoiceData) }}</span>
                        @endif
                        @if (isset($receiverDatas) && $nav['feature_name'] === 'All Receiver' || $nav['feature_name'] === 'View Buyer')
                        <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs   rounded-full dark:bg-green-900 dark:text-green-300"> {{ count($receiverDatas) }}</span>
                        @endif
                        @if (isset($receivedChallan) && $receivedChallan && ($nav['feature_name'] === 'Received Return Challan' || $nav['feature_name'] === 'Received Challan'))
                            <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs  rounded-full dark:bg-green-900 dark:text-green-300"> {{ ($receivedChallan) }}</span>
                        @endif
                        @if (isset($seriesNoData) && $nav['feature_name'] === 'Challan Series No' )
                        <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs  rounded-full dark:bg-green-900 dark:text-green-300"> {{ count($seriesNoData) }}</span>
                        @endif
                        @if (isset($allInvoice) && $nav['feature_name'] === 'All Invoice')
                        <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs  rounded-full dark:bg-green-900 dark:text-green-300"> {{ count($allInvoice) }}</span>
                        @endif
                        @if (isset($sellerDatas) && $nav['feature_name'] === 'View Seller')
                        <span class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs  rounded-full dark:bg-green-900 dark:text-green-300"> {{ count($sellerDatas) }}</span>
                        @endif --}}
                    
            </a> 
            
                @if ( (
                                $nav['feature_name'] === 'Create Challan' && 
                                isset($lastUsageRecordChallan) && 
                                $lastUsageRecordChallan->usage_count === $lastUsageRecordChallan->usage_limit
                            ) || (
                                $nav['feature_name'] === 'Create Invoice' && 
                                isset($lastUsageRecordInvoice) && 
                                $lastUsageRecordInvoice->usage_count === $lastUsageRecordInvoice->usage_limit
                            ) || (
                                $nav['feature_name'] === 'Create Return Challan' && 
                                isset($lastUsageRecordReturnChallan) && 
                                $lastUsageRecordReturnChallan->usage_count === $lastUsageRecordReturnChallan->usage_limit
                            ) || (
                                $nav['feature_name'] === 'Purchase Order' && 
                                isset($lastUsageRecordPurchaseOrder) && 
                                $lastUsageRecordPurchaseOrder->usage_count === $lastUsageRecordPurchaseOrder->usage_limit
                            )
                        )
                <div id="expiredMessage"   class="text-xs text-red-400 mt-2 hidden">Your Usage Limit Exceeded: <a onclick="window.location='{{ route('pricing') }}'"  class="font-semibold underline hover:no-underline">Top-Up!</a> </div>
                @endif
        </li>
    @endif
@endforeach
{{-- <li>
    <a 
        wire:click="featureRedirect('challan_design', '44')"
        class=" flex drop-shadow-lg px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  text-sm rounded-lg  items-center dark:focus:ring-gray-500 group">
        <span class="">Challan Design</span>
    </a>
</li> --}}
@endif