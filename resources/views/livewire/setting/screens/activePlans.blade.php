<div class="mt-5">
    @if (session('success'))
        <div  x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="success-alert" class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">

            <div class="ms-3 text-sm ">
                <span class="font-medium">Success:</span>  {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-border-3" aria-label="Close">
              <span class="sr-only">Dismiss</span>
              <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
              </svg>
            </button>
        </div>
    @endif
    <div id="accordion-collapse" data-accordion="collapse">
        {{-- @dd($activePlan) --}}
        @if(isset($activePlan))
        @foreach (json_decode($activePlan) as $index => $order)

            @php
                // Normalize the index to create a web-friendly ID
                $normalizedIndex = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $index)));
            @endphp
            <h2 id="accordion-collapse-heading-{{ $normalizedIndex }}">
                <button type="button"
                    class="flex items-center justify-between w-full p-3 font-normal text-left text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-[#F0AC49] dark:hover:bg-gray-800"
                    data-accordion-target="#accordion-collapse-body-{{ $normalizedIndex }}" aria-expanded="false"
                    aria-controls="accordion-collapse-body-{{ $normalizedIndex }}">
                    <span class="text-black">{{ str_replace('_', ' ', $index) }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-collapse-body-{{ $normalizedIndex }}" class="hidden"
                aria-labelledby="accordion-collapse-heading-{{ $normalizedIndex }}">
                <div class="border border-b-0 text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <div class="overflow-auto">
                        <table class="table-auto w-full border-collapse border border-gray-300 ">
                            <thead class="text-black text-left bg-white">
                                <tr>
                                    <th class="py-1 px-4 border border-gray-300 whitespace-nowrap">Order Id</th>
                                    <th class="py-1 px-4 border border-gray-300">Packages</th>
                                    <th class="py-1 px-4 border border-gray-300">Plan</th>
                                    <th class="py-1 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($order) --}}
                                @foreach ($order as $order_detail)
                                    <tr class="{{ $loop->odd ? 'bg-[#e6e7e8]' : '' }}">
                                        <td class="py-1 px-4 border border-gray-300 text-black">#{{$order_detail->id}}</td>
                                        <td class="py-1 px-4 border border-gray-300">
                                            <ul class="list-disc pl-5 whitespace-nowrap text-xs text-black">
                                                @foreach ($order_detail->feature_usage_records as $usage_record)
                                                {{-- @dd($order_detail) --}}
                                                {{-- @if (in_array($feature['feature_name'], ['Create Challan', 'Challan Series No', 'Received Challan', 'Received Return Challan'])) --}}
                                                    <li>
                                                        {{ $usage_record->feature->feature_name }} -
                                                        <span class="font-semibold">
                                                            @if ($usage_record->usage_count === null)
                                                                0
                                                            @else
                                                                {{ $usage_record->usage_count }}
                                                            @endif
                                                        </span>
                                                        out of
                                                        <span class="font-semibold">
                                                            @if ($usage_record->usage_limit === null)
                                                                0
                                                            @else
                                                                {{ $usage_record->usage_limit }}
                                                            @endif
                                                        </span>
                                                        used.
                                                    </li>
                                                    {{-- @endif --}}
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="py-1 px-4 border border-gray-300 whitespace-nowrap text-xs text-black">
                                            <span
                                                class="font-semibold">{{ $order_detail->plan->plan_name }}
                                            </span>
                                            <br>
                                            Purchased on: <span
                                                class="font-semibold">{{ date('Y/m/j ', strtotime($order_detail->purchase_date)) }}
                                            </span><br>


                                            Validity: <span
                                                class="font-semibold">{{ $order_detail->plan->validity_days }}</span><br>
                                            Valid Till: <span
                                                class="font-semibold">{{ date('Y/m/j', strtotime($order_detail->purchase_date . ' + ' . $order_detail->plan->validity_days . ' days')) }}</span>
                                        </td>
                                        <td class="py-1 px-4 border border-gray-300 whitespace-nowrap text-xs text-black">
                                          {{$order_detail->status}}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        @endif
    </div>
    <script>
        // JavaScript remains the same, but now it targets the normalized IDs
        document.addEventListener('DOMContentLoaded', function() {
            const accordionButtons = document.querySelectorAll('[data-accordion-target]');
            accordionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-accordion-target');
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.classList.toggle('hidden');
                    } else {
                        console.error('Accordion target not found:', targetId);
                    }
                });
            });
        });
    </script>
</div>
