
<div class="bg-[#f2f3f4]">
        <div class="card-body overflow-auto text-xs text-black p-0">

            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="text-black text-left bg-white">
                    <tr>
                        <th class="py-2 px-2 w-32 border border-gray-300 whitespace-nowrap">Order Id</th>
                        <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Date</th>
                        <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Plan</th>
                        <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Amount</th>
                        <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Details</th>
                        <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($this->UserDetails)
                        @foreach ($this->UserDetails as $index => $order)
                        {{-- @dd($order->pdf_url); --}}
                        @if ($order->amount > 0)
                            @if (isset($order->feature_usage_records))
                                <tr>
                                    @php
                                        $plan = $order->plan;
                                        // dd($plan );
                                    @endphp
                                    <td class="py-2 px-2 border border-gray-300 whitespace-nowrap"># {{ $order->id }}</td>
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">
                                        {{ date('j/m/Y  ', strtotime($order->created_at)) }}
                                    </td>
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">{{ $plan->panel->panel_name }} -
                                        {{ $plan->plan_name }} </td>
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">₹ {{ $order->amount }}</td>
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">
                                        <a wire:click="openPlanModal({{ json_encode($order) }})"
                                           class="w-full md:w-auto text-white bg-black focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm ml-3 px-4 py-1 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">View</a>
                                    </td>

                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">
                                        @if (isset($order->pdf_url))
                                                    <a target="_blank"
                                                        href="{{ Storage::disk('s3')->temporaryUrl($order->pdf_url, now()->addMinutes(5)) }}"
                                                        class="block px-4 py-1  dark:hover:bg-gray-600 dark:hover:text-white"><svg class="hover:text-black" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1 12C2.73 7.61 7 4.5 12 4.5C17 4.5 21.27 7.61 23 12C21.27 16.39 17 19.5 12 19.5C7 19.5 2.73 16.39 1 12ZM20.82 12C19.17 8.63 15.79 6.5 12 6.5C8.21 6.5 4.83 8.63 3.18 12C4.83 15.37 8.2 17.5 12 17.5C15.8 17.5 19.17 15.37 20.82 12ZM12 9.5C13.38 9.5 14.5 10.62 14.5 12C14.5 13.38 13.38 14.5 12 14.5C10.62 14.5 9.5 13.38 9.5 12C9.5 10.62 10.62 9.5 12 9.5ZM7.5 12C7.5 9.52 9.52 7.5 12 7.5C14.48 7.5 16.5 9.52 16.5 12C16.5 14.48 14.48 16.5 12 16.5C9.52 16.5 7.5 14.48 7.5 12Z" fill="black" fill-opacity="0.54"/>
                                                        </svg>
                                                    </a>
                                        @endif
                                    </td>

                                </tr>
                            @endif
                            @endif
                        @endforeach
                    @endisset

                </tbody>
            </table>
        </div>
    </div>
    <div x-data="{ showModal: @entangle('showModal') }" x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
        <div class="fixed bg-black opacity-50"></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg text-xs text-black overflow-auto">
            <div class="bg-[#f2f3f4]">
                <div class="card-body p-0">
                    <table class="table-auto w-full border-collapse border border-gray-300 whitespace-nowrap">
                        <thead class="text-black text-left bg-white">
                            <tr>
                                <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Order Id</th>
                                <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Features</th>
                                <th class="py-2 px-4 border border-gray-300 whitespace-nowrap">Plan</th>
                                <th class="py-2 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($this->orderDetail))
                                <tr class="bg-[#e6e7e8]">
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap"># {{ $this->orderDetail['id'] }}</td>
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">
                                        <ul class="list-disc pl-5">
                                            @foreach ($this->orderDetail['feature_usage_records'] as $feature_detail)
                                                @if (isset($feature_detail['feature']['feature_name']))
                                                    <li>
                                                        {{ $feature_detail['feature']['feature_name'] }} :
                                                        <span class="font-semibold">
                                                            @if ($feature_detail['usage_limit'] === null)
                                                                0
                                                            @else
                                                                {{ $feature_detail['usage_limit'] }}
                                                            @endif
                                                        </span>
                                                        limit
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">
                                        {{ $this->orderDetail['plan']['panel']['panel_name'] }} -
                                        {{ $this->orderDetail['plan']['plan_name'] }}
                                    </td>
                                    <td class="py-2 px-4 border border-gray-300 whitespace-nowrap">
                                        Purchased on: <span class="font-semibold">{{ date('Y/m/j ', strtotime($this->orderDetail['purchase_date'])) }}</span><br>
                                        Validity: <span class="font-semibold">{{ $this->orderDetail['plan']['validity_days'] }}</span><br>
                                        Valid Till: <span class="font-semibold">{{ date('Y/m/j ', strtotime($this->orderDetail['expiry_date'])) }}</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button @click="showModal = false" type="button"
                        class="text-gray-700 bg-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                    Close
                </button>
            </div>
        </div>
    </div>
    <!-- Large Modal -->
    <div id="medium-modal" tabindex="-1" wire:ignore.self
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-5xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-2.5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">

                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover-text-white"
                        data-modal-hide="medium-modal" onclick="window.location.reload();">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>

                </div>
                <!-- Modal body -->
                <div class="bg-[#f2f3f4]">
                    <div class="card-body">
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <thead class="text-black text-left bg-white">
                                <tr>
                                    <th class="py-2 px-4 border border-gray-300">Order Id</th>
                                    <th class="py-2 px-4 border border-gray-300">Features</th>
                                    <th class="py-2 px-4 border border-gray-300">Plan</th>
                                    <th class="py-2 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($this->orderDetail))
                                    <tr class="bg-[#e6e7e8]">
                                        <td class="py-2 px-4 border border-gray-300"># {{ $this->orderDetail['id'] }}
                                        </td>
                                        <td class="py-2 px-4 border border-gray-300">
                                            {{-- {{dd($this->orderDetail)}} --}}
                                            <ul class="list-disc pl-5">
                                                @foreach ($this->orderDetail['feature_usage_records'] as $feature_detail)
                                                    @if (isset($feature_detail['feature']['feature_name']))
                                                        <li>
                                                            {{ $feature_detail['feature']['feature_name'] }} :
                                                            <span class="font-semibold">
                                                                @if ($feature_detail['usage_limit'] === null)
                                                                    0
                                                                @else
                                                                    {{ $feature_detail['usage_limit'] }}
                                                                @endif
                                                            </span>
                                                            limit
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="py-2 px-4 border border-gray-300">
                                            {{ $this->orderDetail['plan']['panel']['panel_name'] }} -
                                            {{ $this->orderDetail['plan']['plan_name'] }}
                                        </td>

                                        <td class="py-2 px-4 border border-gray-300">
                                            Purchased on: <span
                                                class="font-semibold">{{ date('Y/m/j ', strtotime($this->orderDetail['purchase_date'])) }}
                                            </span><br>


                                            Validity: <span
                                                class="font-semibold">{{ $this->orderDetail['plan']['validity_days'] }}</span><br>
                                            Valid Till: <span
                                                class="font-semibold">{{ date('Y/m/j ', strtotime($this->orderDetail['expiry_date'])) }}</span>
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>

                    </div>
                </div>
                <!-- Modal footer -->
                {{-- <div class="flex items-center p-4 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="large-modal-{{ $index }}" type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Decline</button>
                </div> --}}
            </div>
        </div>
    </div>

</div>
