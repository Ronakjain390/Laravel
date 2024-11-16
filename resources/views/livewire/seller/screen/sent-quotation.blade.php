<div>


    @php
    // dd(json_decode($mainUser));
    $mainUser = json_decode($mainUser);
    @endphp


    <div wire:init="loadData">
        @if ($this->isLoading)

        @include('livewire.sender.screens.placeholders')

        @else
        <div x-data="{
            showSelected: false,
            allItemIds: @entangle('allItemIds'),
            selectedProducts: [],
            selectedCount: 0,
            allChecked: false,
            selectPage: false,
            selectAll: false,
            successMessage: '',
            errorMessage: '',
            allStock: [], // This should be populated with all available items
            toggleAll() {
                this.allChecked = !this.allChecked;
                this.selectedProducts = this.allChecked
                    ? Array.from(document.querySelectorAll('input[type=checkbox][data-id]')).map(el => parseInt(el.dataset.id))
                    : [];
                this.updateSelectedCount();
                this.selectPage = this.allChecked;
            },
            toggleProduct(id) {
                const index = this.selectedProducts.indexOf(id);
                if (index === -1) {
                    this.selectedProducts.push(id);
                } else {
                    this.selectedProducts.splice(index, 1);
                }
                this.updateSelectedCount();
                this.allChecked = this.selectedProducts.length === document.querySelectorAll('input[type=checkbox][data-id]').length;
                this.selectPage = this.allChecked;
            },
            updateSelectedCount() {
                this.selectedCount = this.selectedProducts.length;
                this.showSelected = this.selectedCount > 0;
            },
            resetSelection() {
                this.selectedProducts = [];
                this.allChecked = false;
                this.selectPage = false;
                this.selectAll = false;
                this.updateSelectedCount();
                this.showSelected = false;
            },
            selectAllItems() {
                this.selectAll = true;
                this.selectedProducts = this.allItemIds;
                this.updateSelectedCount();
            },
            showSuccessMessage(message) {
                this.successMessage = message;
                setTimeout(() => this.successMessage = '', 3000);
            },
            showErrorMessage(message) {
                this.errorMessage = message;
                setTimeout(() => this.errorMessage = '', 3000);
            }
        }"
        x-init="
            updateSelectedCount();
            Livewire.on('resetSelection', () => resetSelection());
            // Populate allStock with all available items
            allStock = @json($estimates->pluck('id'));
        "
        class="min-w-full overflow-auto bg-white shadow-md rounded-lg"
        >

    <div wire:loading class="fixed z-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  bg-opacity-50 ">
        <span class="loading loading-spinner loading-md"></span>
    </div>
    @if ($errorMessage)
    {{-- {{dd($errorMessage)}} --}}
    @foreach (json_decode($errorMessage) as $error)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">Error:</span> {{ $error[0] }}
    </div>
    @endforeach
    @endif
    @if(($this->persistedTemplate != 'create_invoice') && ($this->persistedTemplate != 'modify_invoice'))
    @if ($successMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Success:</span> {{ $successMessage }}
        </div>
    @endif
    @endif
    @if (Session::get('message'))
        @if (json_decode(Session::get('message')))
            @php
                $decodedMessage = (array) json_decode(Session::get('message'));
            @endphp
            @foreach ($decodedMessage as $msg)
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <span class="font-medium">Error:</span> {{ $msg }}
                </div>
            @endforeach
        @else
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <span class="font-medium">Success:</span> {{ Session::get('message') }}
            </div>
        @endif
    @endif
    <div x-data="{ open: false, exportOption: 'current_page' }" class="hidden sm:flex rounded-md pb-2 shadow-sm justify-end" role="group">
        {{-- <button type="button"
            class="rounded-l-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Bulk
            Action</button> --}}
        {{-- <button wire:click="innerFeatureRedirect('invoice_design', null)" type="button"
            class="border-b border-t border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Invoice
            Design</button> --}}
        {{-- <button wire:click="innerFeatureRedirect('deleted_sent_invoice', null)" type="button"
            class="border border-gray-900  px-4 py-1 text-sm rounded-l-lg text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Deleted
            Invoice</button>
        <button wire:click="innerFeatureRedirect('detailed_sent_invoice', null)" type="button"
            class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed
            View</button>
            <a  @click="open = true"
            class="rounded-r-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</a> --}}
            <div x-show="open" class="fixed inset-0 flex items-center justify-center text-black bg-gray-800 bg-opacity-60">
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
                        {{-- <div>
                            <label class="inline-flex items-center">
                                <input type="radio" x-model="exportOption" value="filtered_data" class="form-radio text-blue-600" {{ $totalChallansCount ? '' : 'disabled' }}>
                                <span class="ml-2 {{ $totalChallansCount ? '' : 'text-gray-400' }}">{{ $totalChallansCount }} Filtered Data </span>
                            </label>
                        </div> --}}
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
        <div x-show="(selectPage || selectAll) && selectedCount > 0" class="bg-gray-100 border-t border-b border-gray-500 text-black text-sm px-4 py-3 my-2">
            <template x-if="selectedCount < allItemIds.length">
                <span>
                    You have selected <strong x-text="selectedCount"></strong> items. Do you want to Select All
                    <strong x-text="allItemIds.length"></strong> items?
                    <a href="#" @click.prevent="selectAllItems" class="ml-2 text-gray-600 hover:text-gray-800 underline">Select All</a>
                </span>
            </template>
            <template x-if="selectedCount === allItemIds.length">
                <span>
                    All <strong x-text="selectedCount"></strong> items are selected.
                    <a href="#" @click.prevent="resetSelection" class="ml-2 text-gray-600 hover:text-gray-800 underline">Unselect All</a>
                </span>
            </template>
        </div>

        <div wire:loading class="fixed z-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  bg-opacity-50 ">
            <span class="loading loading-spinner loading-md"></span>
        </div>
    <table  class="w-full text-xs text-left text-gray-500 dark:text-gray-400" x-data="{ showSelected: false }">
        <div wire:loading class="fixed z-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  z-30 bg-opacity-50 ">
            <span class="loading loading-spinner loading-md"></span>
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
        <thead  class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
            <th class="px-1 py-1 w-0 text-left text-xs font-medium text-black uppercase tracking-wider">
                <label class="inline-flex items-center">
                    <input type="checkbox"
                        class="form-checkbox"
                        @click="toggleAll"
                        :checked="allChecked"
                    >
                </label>

            </th>
            <th x-show="selectedCount > 0" class="flex items-center">
                <span x-show="selectedCount > 0" class="px-1 py-1  text-left text-xs font-medium text-black uppercase tracking-wider">

                    <span x-show="selectedCount > 0" class="text-black lowercase text-xs text-left whitespace-nowrap">Selected: <span x-text="selectedCount"></span></span>
                </span>
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
                                <li id="addTagButton" class="px-3 py-1 hover:bg-gray-200 cursor-pointer" @click="$wire.emit('openTagModal', selectedProducts, 'addTags')">
                                    Add Tags
                                </li>
                                <li id="addCommentButton" class="px-3 py-1 hover:bg-gray-200 cursor-pointer border-b" @click="$wire.emit('openCommentModal', selectedProducts, 'addComment')" >
                                    Add Comment
                                </li>
                                {{-- <li class="px-3 py-1 hover:bg-gray-200 cursor-pointer border-b" @click="$wire.set('selectedProducts', selectedProducts); $wire.handleAction('send', 'variableForSend');">
                                    Send
                                </li> --}}
                            </li>
                        </ul>
                    </div>
                </span>
            </th>
                @include('components.assets.tableComponent.th')
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
                        @if(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->payment_status)
                        <th x-show="selectedCount > 0"></th>
                        @endif
                        @if(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->tags)
                        <th x-show="selectedCount > 0"></th>
                        @endif
                        {{-- <th x-show="selectedCount > 0"></th> --}}

        </thead>

    </div>
        <tbody class="text-black">
            @if (count($estimates))
            @foreach ($estimates as $key => $columnName)
                @php
                    $columnName = (object) $columnName;
                    $mainUser = json_decode($this->mainUser);
                    // dump($columnName);
                @endphp
                @if (isset($columnName->pdf_url))
                <tr ondblclick="window.open('https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}', '_blank')"
                class=" cursor-pointer whitespace-nowrap @if ($key % 2 == 0)  bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif">
                <td class="px-1 py-1 whitespace-nowrap">
                    <input type="checkbox"
                        class="form-checkbox"
                        :checked="selectedProducts.includes({{ $columnName->id }})"
                        @click="toggleProduct({{ $columnName->id }})"
                        data-id="{{ $columnName->id }}"
                    >
                </td>

                        <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ ++$key }}</td>


                <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->estimate_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">
                    @if(!empty($columnName->statuses) && isset($columnName->statuses[0]))
                        @php
                            $columnName->statuses[0] = (object) $columnName->statuses[0];
                        @endphp
                        @if($columnName->statuses[0]->status != 'draft')
                            @if(isset($columnName->invoice_date) && !empty($columnName->invoice_date))
                                {{ date('j F Y', strtotime($columnName->invoice_date)) }}
                                <p class="text-[8px]">{{ date('h:i A', strtotime($columnName->invoice_date)) }}</p>
                            @else
                                {{ date('j F Y', strtotime($columnName->created_at)) }}
                                <p class="text-[8px]">{{ date('h:i A', strtotime($columnName->created_at)) }}</p>
                            @endif
                        @endif
                    @else
                    @endif
                </td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->seller }}
                    @if (isset($columnName->statuses[0]->team_user_name) && $columnName->statuses[0]->team_user_name != null)
                        ({{ ucfirst($columnName->statuses[0]->team_user_name) }})
                    @endif
                </td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->buyer }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->total_qty }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->total }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->receiver_details->state ?? '' }}</td>

                {{-- <td class="px-2  text-[0.6rem] border-2 border-gray-300">
                    @if (isset($columnName->statuses[0]))
                        {{ $columnName->statuses[0]->status }} By {{ $columnName->statuses[0]->user_name }} <br> on
                        {{ date('j F Y, h:i A', strtotime($columnName->statuses[0]->created_at)) }}
                    @endif
                </td> --}}
                <td class="px-2 text-[0.6rem] border-2 border-gray-300  ">
                    <div class="flex justify-between items-center">
                        <div class="{{ !empty($columnName->statuses) && isset($columnName->statuses[0]) && $columnName->statuses[0]->status == 'reject' ? 'bg-red-500' : '' }}">
                            @if (!empty($columnName->statuses) && isset($columnName->statuses[0]))
                                @php
                                    $columnName->statuses[0] = (object) $columnName->statuses[0];
                                @endphp
                                @if ($columnName->statuses[0]->status == 'draft')
                                    <span class="text-red-500">Created</span>
                                @elseif($columnName->statuses[0]->status == 'sent')
                                    Sent
                                @elseif($columnName->statuses[0]->status == 'self_accept')
                                    Self Delivered
                                @elseif($columnName->statuses[0]->status == 'accept')
                                    Accepted By {{ $columnName->statuses[0]->user_name }}
                                @elseif($columnName->statuses[0]->status == 'reject')
                                    Rejected By {{ $columnName->statuses[0]->user_name }}
                                @elseif($columnName->statuses[0]->status == 'partially_self_return')
                                    Partial Self Returned
                                @elseif($columnName->statuses[0]->status == 'self_return')
                                    Self Returned
                                @endif
                                @if ($columnName->statuses[0]->status != 'sent')
                                    <p class="whitespace-nowrap" style="font-size: smaller">
                                        {{ date('j F Y, h:i A', strtotime($columnName->created_at)) }}</p>
                                @endif
                            @endif
                        </div>
                        <div class="ml-2">
                            <span x-data="{ open: false }" @click="open = true">
                                <svg class="h-4 text-gray-800 dark:text-white cursor-pointer" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>

                                <div x-show="open" class="fixed inset-0 z-50 p-2 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80" @click.away="open = false">
                                    <div class="relative p-0.5 w-full max-w-md  bg-white rounded-lg shadow dark:bg-gray-700 flex flex-col" @click.stop>
                                        <!-- Modal header -->
                                        <div class="flex-shrink-0 flex items-center justify-between p-2 px-3 rounded-t dark:border-gray-600 border-b">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status Details</h3>
                                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-500 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="open = false">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                                <span class="sr-only">Close modal</span>
                                            </button>
                                        </div>
                                        <!-- Modal body -->
                                        <div class="flex-grow p-4 md:p-5 overflow-y-auto max-h-64">
                                            <ol class="relative border-s border-gray-200 dark:border-gray-600 ms-3.5 mb-4 md:mb-5">
                                                @if(isset($columnName))
                                                @php $totalStatuses = count($columnName->statuses); @endphp
                                                    @foreach($columnName->statuses as $st => $status)
                                                        <li class="mb-5 ms-8 text-left">
                                                            <span class="absolute flex items-center justify-center w-6 h-6 text-black text-xs bg-gray-300 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                                                {{ $totalStatuses - $st }}
                                                            </span>
                                                            <span class="text-xs text-black">
                                                                @if($status->status === 'draft')
                                                                    Created
                                                                @elseif($status->status === 'accept')
                                                                    Accepted
                                                                @elseif($status->status === 'reject')
                                                                    Rejected
                                                                @else
                                                                    {{ ucfirst($status->status) }}
                                                                @endif
                                                            </span>
                                                            <time class="block mb-3 text-xs leading-none text-black">
                                                                {{ date('j F Y, h:i A', strtotime($status->created_at ?? '')) }}
                                                            </time>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>

                </td>
                <td class="border-2 border-gray-300" x-data="{ open: false }" @click="open = true">
                    <a
                        {{-- data-modal-target="sfp-{{ json_encode($columnName->sfpBy) }}"
                        data-modal-toggle="sfp-{{ json_encode($columnName->sfpBy) }}" --}}
                        class="block px-4


                        @if (count($columnName->sfpBy) > 0) text-white
                    @foreach ($columnName->sfpBy as $sfp)
                        @if ($columnName->statuses[0]->status == 'sent')
                        bg-green-600
                        @elseif ($columnName->statuses[0]->status == 'accept' || $columnName->statuses[0]->status == 'reject')
                        bg-green-600
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                            bg-red-600
                            @else
                            bg-red-600
                            @endif
                        @endif
                    @endforeach

                        hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 border-2 border-gray-300 px-2 .5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">@if (count($columnName->sfpBy) > 0)
                        @if ($columnName->statuses[0]->status == 'sent')
                        Done
                        @elseif ($columnName->statuses[0]->status == 'accept' || $columnName->statuses[0]->status == 'reject')
                        Done
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                Processing
                            @elseif (isset($sfp->sfp_to_id) && $sfp->sfp_to_id == Auth::user()->id)
                                Check
                            @else
                                Processing
                            @endif
                        @endif

                        @else
                    @endif



                <!-- SFP Data Show and open modal  -->
                <div x-show="open" class="fixed inset-0 z-50 p-2 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80" @click.away="open = false">
                    <div class="relative p-0.5 w-full max-w-md max-h-full bg-white rounded-lg shadow dark:bg-gray-700" @click.stop>
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-2 px-3 rounded-t dark:border-gray-600 border-b">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SFP Process</h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-500 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="open = false">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5">
                            <ol class="relative border-s border-gray-200 dark:border-gray-600 ms-3.5 mb-4 md:mb-5">
                                @if(isset($columnName))
                                    @foreach($columnName->sfpBy as $sf => $sfp)
                                        <li class="mb-5 ms-8 text-left">
                                            <span class="absolute flex items-center justify-center w-6 h-6 text-black text-xs bg-gray-300 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                                {{ ++$sf }}
                                            </span>
                                            @if(!empty($sfp->comment))
                                                    <h3 class="flex items-start  text-sm  text-black dark:text-white">Comment: {{ $sfp->comment }} </h3>
                                                @endif
                                            <span class="text-xs text-black"> {{ ucfirst($sfp->status) }} By {{ ucfirst($sfp->sfp_by_name) }} @if(!empty($sfp->sfp_to_name))   to {{ ucfirst($sfp->sfp_to_name) }} @endif </span>
                                            <time class="block mb-3 text-xs   leading-none text-black">
                                                {{ date('j F Y, h:i A', strtotime($sfp->created_at ?? '')) }}
                                            </time>
                                        </li>
                                    @endforeach
                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
                </td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300 whitespace-normal" x-data="{ open: false }">
                    @php
                        $statusComments = json_decode($columnName->status_comment, true);
                        $firstComment = (is_array($statusComments) && isset($statusComments[0])) ? $statusComments[0] : null;
                    @endphp
                    @if($firstComment !== null && isset($firstComment['comment']) && isset($firstComment['name']) && $firstComment['date'] && $firstComment['time'] && !empty(trim($firstComment['comment'])))
                        <h3 @click="open = true" class="flex items-start mb-1 text-xs text-gray-900 dark:text-white cursor-pointer">{{ Str::limit(ucfirst($firstComment['comment']), 4, '...') }}</h3>
                    @endif

                    <!-- Modal Backdrop and Content -->
                    <div x-show="open" class="fixed inset-0 z-50 p-2 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80" @click.away="open = false">
                        <div class="relative p-0.5 w-full max-w-md max-h-full bg-white rounded-lg shadow dark:bg-gray-700" @click.stop>
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-2 px-3 rounded-t dark:border-gray-600 border-b">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-500 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="open = false">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-4 md:p-5">
                                <ol class="relative border-s border-gray-200 dark:border-gray-600 ms-3.5 mb-4 md:mb-5">
                                    @php $statusComments = json_decode($columnName->status_comment, true); @endphp
                                    @if(is_array($statusComments))
                                        @foreach($statusComments as $statusComment)
                                            <li class="mb-10 ms-8">
                                                @if(isset($statusComment['comment']) && isset($statusComment['name']) && $statusComment['date'] && $statusComment['time'] && !empty(trim($statusComment['comment'])))
                                                    <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                    <h3 class="flex items-start mb-1 text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($statusComment['comment']) }}</h3>
                                                    <time class="block mb-3 text-xs font-normal leading-none text-gray-500 dark:text-gray-400">
                                                        {{ ucfirst($statusComment['name']) }} {{ date('d-m-y', strtotime($statusComment['date'])) }} {{ $statusComment['time'] }}
                                                    </time>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ol>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300">
                    {{-- @dd($columnName->tags) --}}
                    @if(isset($columnName) && $columnName->tableTags)
                        @foreach ($columnName->tableTags as $tagsArray )
                        <span class="bg-gray-300 rounded-lg p-1 mr-1">
                            {{$tagsArray->name}}
                        </span>
                        @endforeach
                    @endif
                </td>
                <td class="px-2 p-0 text-[0.6rem] border-2 border-gray-300">
                    <button id="dropdownDefaultButton-{{ $key }}"
                        data-dropdown-toggle="dropdown-{{ $key }}"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-2 py-1 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                        type="button">Select <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg></button>
                    <!-- Dropdown menu -->
                    <div id="dropdown-{{ $key }}"
                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">
                        @if (isset($columnName->statuses[0]))
                            <ul class="py-1 text-[0.6rem] border-2 border-gray-300 text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton-{{ $key }}">
                                {{-- @if ($columnName->statuses[0]->status == 'sent') --}}
                                @if((isset($columnName->statuses[0]->status) && $columnName->statuses[0]->status !== 'sent'))
                                    @if($teamMembers != null)
                                        <li>

                                            <a href="javascript:void(0);"
                                            wire:click="$emit('openSfpModal', { challanId: {{ $columnName->id ?? 'null' }}, type: 'sent_quotation' })"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">SFP</a>
                                            </li>
                                    @endif
                                @endif
                                @if ($mainUser->team_user != null)
                                            @if ($mainUser->team_user->permissions->permission->seller->view_invoice == 1)
                                            <li>
                                                @if($isMobile)
                                                @if (isset($columnName->pdf_url))
                                                    <a target="_blank"
                                                    href="https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                                                @endif
                                                @else
                                                @if (isset($columnName->pdf_url))
                                                    <a target="_blank"
                                                    href="{{ Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5)) }}"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                                                @endif
                                                @endif
                                            </li>
                                            @endif
                                        @else
                                        <li>
                                            @if($isMobile)
                                            @if (isset($columnName->pdf_url))
                                                <a target="_blank"
                                                href="https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                                            @endif
                                            @else
                                            @if (isset($columnName->pdf_url))
                                                <a target="_blank"
                                                href="{{ Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5)) }}"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                                            @endif
                                            @endif
                                        </li>
                                        @endif
                                        @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->seller->delete_sent_quotation == 1)
                                        <li>
                                            <a x-data
                                            wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'addComment')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                            Add Comment
                                        </a>
                                        </li>
                                        @else

                                        <li>
                                            <a x-data
                                            wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'addComment')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                            Add Comment
                                        </a>
                                        </li>
                                        @endif
                                        @endif


                                {{-- @endif --}}
                                {{-- @if ($columnName->statuses[0]->status == 'draft')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->seller->modify_invoice == 1)
                                            <li>
                                                <a href="#"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Update</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Update</a>
                                        </li>
                                    @endif
                                @endif --}}
                                @if($columnName->buyer_id)
                                @if ($columnName->statuses[0]->status == 'draft')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->seller->send_quotation == 1)
                                        <li>
                                            <a x-data
                                               wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'sendEstimate')"
                                               class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                              Send
                                            </a>
                                        </li>
                                        @endif
                                    @else
                                    <li>
                                        <a x-data
                                           wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'sendEstimate')"
                                           class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                          Send
                                        </a>
                                    </li>
                                    @endif
                                @endif
                                @endif
                                {{-- @if ($columnName->statuses[0]->status == 'modified')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->seller->send_quotation == 1)
                                            <li>
                                                <a href="#" wire:click="reSendInvoice('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#" wire:click="reSendInvoice('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                        </li>
                                    @endif
                                @endif --}}

                                {{-- @if (!is_null($mainUser) && property_exists($mainUser, 'team_user')) --}}
                                {{-- @if ($columnName->statuses[0]->status == 'sent')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->seller->accept_invoice == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="selfAcceptInvoice('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                    Delivery</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#" wire:click="selfAcceptInvoice('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                Delivery</a>
                                        </li>
                                    @endif
                                    @endif --}}
                                    {{-- @endif --}}
                                {{-- @if ($columnName->statuses[0]->status == 'reject') --}}
                                {{-- @if (!empty($columnName->statuses) && isset($columnName->statuses[0]) && ($columnName->statuses[0]->status == 'draft' || $columnName->statuses[0]->status == 'reject'))
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->seller->modify_invoice == 1)
                                        <li>
                                            <a href="#"
                                                wire:click="innerFeatureRedirect('modify_invoice', {{ $columnName->id }})"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                        </li>
                                    @endif
                                @else
                                    <li>
                                        <a href="#"
                                            wire:click="innerFeatureRedirect('modify_invoice',{{ $columnName->id }})"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                    </li>
                                @endif
                                @endif --}}
                                @if ($columnName->statuses[0]->status == 'draft' || $columnName->statuses[0]->status == 'reject')
                                    @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->seller->delete_sent_quotation == 1)
                                      <li>
                                            <a
                                            href="javascript:void(0);"
                                            data-delete-id="{{ $columnName->id }}"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white delete-invoice">
                                            Delete
                                            </a>
                                        </li>
                                        @endif
                                    @else
                                        <li>
                                            <a
                                            href="javascript:void(0);"
                                            onclick="confirmDelete({{ $columnName->id }})"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">
                                            Delete
                                        </a>
                                        </li>
                                    @endif
                                @endif
                                @if ($mainUser->team_user != null)
                                @if ($mainUser->team_user->permissions->permission->seller->add_quotation_tags == 1)
                                <li>
                                    <a x-data
                                       wire:click="$emit('openTagModal', {{ $columnName->id }}, 'addTags')"
                                       class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                       Add Tag
                                    </a>
                                </li>
                                @else
                                <li>
                                    <a x-data
                                       wire:click="$emit('openTagModal', {{ $columnName->id }}, 'addTags')"
                                       class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                       Add Tag
                                    </a>
                                </li>
                                @endif
                                @endif


                                <li>
                                    <a href="javascript:void(0);"
                                        wire:click="poToInvoice({{ $columnName->id }})"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">
                                        Convert to Invoice
                                     </a>
                                </li>


                            </ul>
                        @endif
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
            @endif
        </tbody>

    </table>
    <livewire:components.tag-component :panelId="6" :tableId="11" />
    <livewire:components.comment-component :panelId="6" :tableId="11" />
    <livewire:components.sfp-component :panelType="'quotation'"/>

        </div>

    </div>
      </div>

      {{$estimates->links()}}
      </div>
      @endif
</div>
<script>




     window.addEventListener('show-error-message', event => {
            // Set the message in the modal
            document.getElementById('errorMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('errorModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('errorModal').style.display = 'none';
            }, 10000);
        });

        window.addEventListener('show-success-message', event => {
            // Set the message in the modal
            document.getElementById('successMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 10000);
        });


    function confirmDelete(id) {
   Swal.fire({
       title: 'Are you sure?',
       text: "You won't be able to revert this!",
       icon: 'warning',
       showCancelButton: true,
       confirmButtonColor: '#3085d6',
       cancelButtonColor: '#d33',
       confirmButtonText: 'Yes, delete it!'
   }).then((result) => {
       if (result.isConfirmed) {
           @this.call('deleteInvoice', id);
           Swal.fire(
               'Deleted!',
               'Your file has been deleted.',
               'success'
           )
       }
   })
}



    document.addEventListener('livewire:update', function() {
        console.log('Livewire reloaded 21');
        initFlowbite();
    });



</script>

