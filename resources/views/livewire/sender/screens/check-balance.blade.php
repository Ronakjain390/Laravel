<div>
    <div class="rounded-lg bg-gray-100 pb-2 flex justify-end text-black">
        <!-- Export Modal -->
        <div x-data="{ open: false, exportOption: 'current_page' }" class="py-0.5">
            <a @click="open = true"
               class="rounded-l-lg border border-gray-900 px-4 py-1 text-sm text-black focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">
                Export
            </a>

            <div x-show="open" class="fixed inset-0 flex items-center justify-center text-black bg-gray-800 bg-opacity-60 z-40">
                <div class="bg-white p-6 rounded-lg w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4">Export Options</h2>
                    <div class="mb-4">
                        <label class="block mb-2">Select Export Option:</label>

                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" x-model="exportOption" value="current_page" class="form-radio text-blue-600">
                                <span class="ml-2">Current Page</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" x-model="exportOption" value="filtered_data" class="form-radio text-blue-600" {{ $totalChallansCount ? '' : 'disabled' }}>
                                <span class="ml-2 {{ $totalChallansCount ? '' : 'text-gray-400' }}">{{ $totalChallansCount }} Filtered Data </span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" x-model="exportOption" value="all_data" class="form-radio text-blue-600">
                                <span class="ml-2">All Data</span>
                            </label>
                        </div>

                    </div>
                    <div class="flex justify-end">
                        <button @click="open = false" class="mr-2 px-4 py-2 text-sm text-red-400 rounded">Cancel</button>
                        <button @click="() => { $wire.export(exportOption); open = false; }" class="px-2 py-1 bg-gray-900  text-white rounded">Export</button>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" wire:click="innerFeatureRedirect('sent_challan', null)" class="rounded-r-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Back</button>
</div>
    @if(session()->has('error'))
    <div id="success-alert" class="flex items-center p-2 mb-4 text-red-800 border-t-4 border-red-300 bg-[#eddbd4] dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
        <div class="ms-3 text-sm font-medium">
            {{ session()->get('error') }}
        </div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-border-3" aria-label="Close">
          <span class="sr-only">Dismiss</span>
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
        </button>
    </div>
    @endif
    @php
    $mainUser = json_decode($mainUser);
    $serialNumber = 0;
    $totalBalance = 0;
    $totalSentQty = 0;
    $totalReceivedQty = 0;
    $groupedData = [];

    // Group data by "Challan Id"
    foreach ($organizedData as $data) {
        $challanId = $data['Challan Id'];
        if (!isset($groupedData[$challanId])) {
            $groupedData[$challanId] = [];
        }
        $groupedData[$challanId][] = $data;
    }

    // Calculate totals
    foreach ($groupedData as $group) {
        $mainData = $group[0];
        $totalBalance += $mainData['Balance'] ?? 0;
        $totalSentQty += array_sum(array_column($group, 'QTY Sent'));
        $totalReceivedQty += array_sum(array_column($group, 'Recvd QTY'));
    }

@endphp
    @if(session()->has('success'))
    <div id="success-alert" class="flex items-center p-2 mb-4 text-green-800 border-t-4 border-green-300 bg-green-200 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <div class="ms-3 text-sm font-medium">
            {{ session()->get('success') }}
        </div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-border-3" aria-label="Close">
          <span class="sr-only">Dismiss</span>
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
        </button>
    </div>
    @endif

    <div x-data="{
        showSelected: false,
        selectedCount: 0,
        showSuccessMessage(message) {
            this.successMessage = message;
            setTimeout(() => this.successMessage = '', 3000);
        },
        showErrorMessage(message) {
            this.errorMessage = message;
            setTimeout(() => this.errorMessage = '', 3000);
        }
    }" class="overflow-x-auto">
        <div class="relative">
            <!-- Pagination Above Table -->
                        <table  class="w-full text-xs text-left text-gray-500 dark:text-gray-400" x-data="{ showSelected: false }">
                            <div wire:loading class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  bg-opacity-50 ">
                                <span class="loading loading-spinner loading-md"></span>
                            </div>

                            <div>
                                <thead  class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    {{-- <th class="px-1 py-1 w-0 text-left text-xs font-medium text-black uppercase tracking-wider">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" @click="showSelected = !showSelected" x-on:click="toggleAll" x-bind:checked="allChecked">
                                        </label>
                                    </th> --}}
                                    <th x-show="selectedCount > 0">
                                        <span x-show="selectedCount > 0" class="px-1 py-1  text-left text-xs font-medium text-black uppercase tracking-wider">
                                            {{-- <label class="inline-flex items-center">
                                                <input type="checkbox" class="form-checkbox" @click="showSelected = !showSelected" x-on:click="toggleAll" x-bind:checked="allChecked">
                                            </label> --}}
                                            <span x-show="selectedCount > 0" class="text-black lowercase text-xs text-left whitespace-nowrap">Selected: <span x-text="selectedCount"></span></span>
                                        </span>
                                    </th>
                                <div x-show="selectedCount > 0" >

                                    @include('components.assets.tableComponent.th')
                                </div>
                                    <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                                <th x-show="selectedCount > 0"></th>
                                            <th x-show="selectedCount > 0">
                                                <div class="w-full justify-center flex">
                                                    {{-- <span x-show="selectedCount > 0" class="px-1 py-1  text-left text-xs font-medium text-black uppercase tracking-wider">
                                                        <label class="inline-flex items-center">
                                                            <input type="checkbox" class="form-checkbox" @click="showSelected = !showSelected" x-on:click="toggleAll" x-bind:checked="allChecked">
                                                        </label>
                                                        <span x-show="selectedCount > 0" class="text-black lowercase text-xs text-left">Selected: <span x-text="selectedCount"></span></span>
                                                    </span> --}}

                                                    <span x-show="selectedCount > 0" >
                                                        <button id="dropdownMenuIconHorizontalButton" data-dropdown-toggle="dropdownDotsHorizontal" class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100   dark:text-white   dark:bg-gray-800 dark:hover:bg-gray-700 " type="button">
                                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                                                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                                                            </svg>
                                                        </button>

                                                        <!-- Dropdown menu -->
                                                        <div id="dropdownDotsHorizontal" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconHorizontalButton">
                                                                <li>
                                                                    <li class="px-3 py-1 hover:bg-gray-200 cursor-pointer" @click="$wire.set('selectedProducts', selectedProducts); $wire.tagModal('selectedProducts', 'addTags');">
                                                                        Add Tags
                                                                    </li>
                                                                    <li class="px-3 py-1 hover:bg-gray-200 cursor-pointer border-b" @click="$wire.set('selectedProducts', selectedProducts); $wire.handleAction('addComment', 'variableForAddComment');">
                                                                        Add Comment
                                                                    </li>
                                                                    <li class="px-3 py-1 hover:bg-gray-200 cursor-pointer border-b" @click="$wire.set('selectedProducts', selectedProducts); $wire.handleAction('send', 'variableForSend');">
                                                                        Send
                                                                    </li>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </span>
                                                </div>
                                            </th>
                                </thead>

                            </div>

                <tbody class="text-black">
                    <!-- Total Row added here -->
                    <tr class="bg-gray-100 font-bold text-black">
                        <td colspan="5" class="text-right">Total</td>
                        <td class="border-r bg-gray-100">{{ $totalSentQty }}</td>
                        <td colspan="4"></td>
                        <td class="border-r bg-gray-100">{{ $totalReceivedQty }}</td>
                        <td></td>
                        <td class="border-r bg-gray-100">{{ $totalBalance }}</td>
                        <td colspan="2"></td>
                    </tr>
                    {{-- @dd($groupedData) --}}
                    <!-- Table data rows -->
                    @foreach ($groupedData as $challanId => $group)
                        @php
                        $mainData = $group[0];
                        $rowCount = count($group);
                        $serialNumber++;
                        $prevChallanNo = null;
                        $prevArticle = null;
                        @endphp
                        @foreach ($group as $index => $data)
                        {{-- @dump($data) --}}
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                @if ($index == 0)
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $serialNumber }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $mainData['Receiver'] }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ date('d-m-Y', strtotime($mainData['Sent Date'])) }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $data['Challan No.'] !== $prevChallanNo ? $data['Challan No.'] : '' }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $data['Article'] !== $prevArticle ? $data['Article'] : '' }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $data['QTY Sent'] }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $data['Challan Status'] }}</td>
                                @endif
                                <td class="border-r px-2 border-gray-400">{{ $data['Recvd Challan No.'] ?? '' }}</td>
                                <td class="border-r px-2 border-gray-400">{{ isset($data['Recvd Date']) ? date('d-m-Y', strtotime($data['Recvd Date'])) : '' }}</td>
                                <td class="border-r px-2 border-gray-400">{{ $data['RecvArticle'] ?? '' }}</td>
                                <td class="border-r px-2 border-gray-400">{{ $data['Recvd QTY'] ?? '' }}</td>
                                <td class="border-r px-2 border-gray-400">{{ $data['Return Challan Status'] ?? '' }}</td>
                                @if ($index == 0)
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $mainData['Balance'] ?? '' }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">{{ $mainData['Margin QTY'] ?? '' }}</td>
                                    <td class="border-r px-2 border-gray-400 whitespace-nowrap" rowspan="{{ $rowCount }}">
                                        @if(isset($mainData['Balance']) && $mainData['Balance'] != 0 && !empty($data['Recvd Challan No.']))
                                <a href="javascript:void(0)" onclick="confirmAcceptMargin({{ $challanId }})"
                                    class="px-4 py-1 text-[0.6rem] rounded-lg hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">
                                    Accept Margin
                                </a>
                            @endif
                                    </td>
                                @endif
                            </tr>
                            @php
                                $prevChallanNo = $data['Challan No.'];
                                $prevArticle = $data['Article'];
                            @endphp
                        @endforeach
                        {{-- @php
                            $totalBalance += $mainData['Balance'] ?? 0;
                            $totalSentQty += array_sum(array_column($group, 'QTY Sent'));
                            $totalReceivedQty += array_sum(array_column($group, 'Recvd QTY'));
                        @endphp --}}
                    @endforeach
                </tbody>
            </table>

            {{ $challans->links() }}
        </div>
    </div>

    <script>
        function confirmAcceptMargin(challanId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to accept the margin!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Accept it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('acceptMargin', challanId);
                }
            })
        }
    </script>
</div>
