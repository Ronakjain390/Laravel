<div>

    @php
    // dd(json_decode($mainUser));
    $mainUser = json_decode($mainUser);
    @endphp


    <div wire:init="loadData">
        @if ($this->isLoading)

        @include('livewire.sender.screens.placeholders')

        @else

    {{-- <div  class="rounded-md pb-2 shadow-sm justify-end hidden sm:flex" role="group">
        <button type="button" wire:click="innerFeatureRedirect('detailed_received_return_challan', null)"
       class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed
       View</button>
   </div> --}}


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
        allStock = @json($challans->pluck('id'));
    "
    class="min-w-full overflow-auto bg-white shadow-md rounded-lg"
    >

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

       <table  class="w-full text-xs text-left text-gray-500 dark:text-gray-400" x-data="{ showSelected: false }">
        <div wire:loading class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  z-30 bg-opacity-50 ">
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
        <div>
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
        </thead>
        </div>
        <tbody class="text-black">
            @php $i=1; @endphp
            @foreach ($challans as $key => $columnName)
            {{-- @dd($columnName); --}}
                @php
                    $columnName = (object) $columnName;

                @endphp
                @if (isset($columnName->statuses) && count($columnName->statuses) > 0)
                    @php
                        $latestStatus = (object) $columnName->statuses[0];
                    @endphp
                    {{-- @if ($latestStatus->status == 'sent') --}}
                    @if (in_array($latestStatus->status, ['sent', 'accept', 'modified', 'self_accept']))
                    @if (isset($columnName->pdf_url))
                        <tr ondblclick="window.open('https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}', '_blank')"
                            class="cursor-pointer whitespace-nowrap @if ($key % 2 == 0) bg-[#e9e6e6] dark:bg-gray-800 @endif dark:border-gray-700 dark:bg-gray-800 whitespace-nowrap ">

                            <td class="px-1 py-1 whitespace-nowrap">
                                <input type="checkbox"
                                    class="form-checkbox"
                                    :checked="selectedProducts.includes({{ $columnName->id }})"
                                    @click="toggleProduct({{ $columnName->id }})"
                                    data-id="{{ $columnName->id }}"
                                >
                            </td>
                            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $i }}</td>

                            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->challan_series }}-{{ $columnName->series_num }}
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                                    @if(!empty($columnName->statuses) && isset($columnName->statuses[0]))
                                        @php
                                            $columnName->statuses[0] = (object) $columnName->statuses[0];
                                        @endphp
                                        @if($columnName->statuses[0]->status != 'draft')
                                            {{ date('j F Y', strtotime($columnName->created_at)) }}
                                            <p class="text-[8px]">{{ date('h:i A', strtotime($columnName->created_at)) }}</p>
                                        @endif
                                    @endif


                                </td>
                            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->sender }}</td>
                            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->receiver }}</td>
                            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->total_qty }}</td>
                            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->total }}</td>
                            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->receiver_details->state ?? null }}</td>
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
                                                Received
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
                            bg-red-600
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
                            Processing
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
                         <!-- Modal Backdrop and Content -->
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
                            {{-- SFP RECEIVER SENT RETURN CHALLAN --}}
                            {{-- <div id="sfp_rReceived_challan-{{ json_encode($columnName->sfp) }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative p-4 w-full max-w-md max-h-full">
                                    <!-- Modal content -->
                                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                        <!-- Modal header -->
                                        <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SFP Process</h3>
                                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="sfp_rReceived_challan-{{ json_encode($columnName->sfp) }}">
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
                                                    @foreach($columnName->sfp as $sf => $sfp)
                                                        <li class="mb-5 ms-8">
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
                            </div> --}}
                            {{-- @dump(json_decode($columnName->status_comment)) --}}
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
                            <span class="bg-gray-300 rounded-lg p-1 whitespace-nowrap ">
                                {{$tagsArray->name}}
                            </span>
                            @endforeach
                            @else
                            <span class="text-gray-500">+</span>

                        @endif
                    </td>

                            <td>
                                <button id="dropdownDefaultButton-{{ $key }}"
                                    data-dropdown-toggle="dropdown-{{ $key }}"
                                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-5 py-1 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                    type="button">Select <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg></button>
                                <!-- Dropdown menu -->
                                <div id="dropdown-{{ $key }}"
                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">

                                    <ul class="py-1 text-[0.6rem] border-2 border-gray-300 text-gray-700 dark:text-gray-200"
                                        aria-labelledby="dropdownDefaultButton-{{ $key }}">
                                        @if((isset($columnName->statuses[0]->status) && $columnName->statuses[0]->status == 'sent'))
                                            @if($teamMembers != null)
                                            <li>
                                                <a href="javascript:void(0);"
                                                    wire:click="$emit('openSfpModal', {{ $columnName->id }}, 'received_return_challan')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">SFP</a>
                                            </li>
                                            @endif
                                        @endif

                                        @if ($mainUser && $mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->receiver->view_sent_challans_tables == 1)
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
                                    <li>
                                        <a x-data
                                        x-on:click.prevent="$wire.openModal({{ $columnName->id }}, 'addComment')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                            Add Comment
                                        </a>
                                    </li>
                                        @if (isset($columnName->statuses) && count($columnName->statuses) > 0)
                                            @php
                                                $latestStatus = (object) $columnName->statuses[0];
                                            @endphp

                                            @if (in_array($latestStatus->status, ['sent']))
                                            {{-- @if (in_array($latestStatus->status, ['sent', 'draft', 'accept', 'modified', 'reject'])) --}}
                                                {{-- @php
                                                    $mainUser = json_decode($mainUser);
                                                @endphp --}}
                                                @if ($mainUser && $mainUser->team_user != null)
                                                    @if ($mainUser->team_user->permissions->permission->receiver->accept_received_challan == 1)
                                                    <li>
                                                        <a x-data
                                                        x-on:click.prevent="$wire.openModal({{ $columnName->id }}, 'accept')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                            Accept
                                                        </a>
                                                    </li>
                                                    @endif
                                                @else
                                                <li>
                                                    <a x-data
                                                    x-on:click.prevent="$wire.openModal({{ $columnName->id }}, 'accept')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                        Accept
                                                    </a>
                                                </li>
                                                @endif

                                                @if ($mainUser && $mainUser->team_user != null)
                                                    @if ($mainUser->team_user->permissions->permission->receiver->reject_received_challan == 1)
                                                    <li>
                                                        <a x-data
                                                        x-on:click.prevent="$wire.openModal({{ $columnName->id }}, 'reject')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                            Reject
                                                        </a>
                                                    </li>
                                                    @endif
                                                @else
                                                <li>
                                                    <a x-data
                                                    x-on:click.prevent="$wire.openModal({{ $columnName->id }}, 'reject')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                        Reject
                                                    </a>
                                                </li>
                                                @endif
                                            @endif
                                        @endif
                                        <li>
                                            <a x-data
                                            wire:click="tagModal({{ $columnName->id }}, 'addTags')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                Add Tag
                                            </a>
                                        </li>
                                    </ul>


                                </div>

                            </td>

                        </tr>
                        @endif
                        @php ++$i; @endphp
                    @endif
                @endif
            @endforeach
             {{-- SFP SENT RETURN CHALLAN MODAL --}}
             <div id="receiver-received-challan" tabindex="-1"
                class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                wire:ignore>
                <div class="relative w-full max-w-lg max-h-full">
                 <!-- Modal content -->
                 <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                     <!-- Modal header -->
                     <div class="flex mt-20 justify-between p-4 border-b rounded-t dark:border-gray-600">
                         <h3 class=" font-medium text-gray-900 dark:text-white">
                             Send For Processing

                         </h3>
                         <button type="button"
                             class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-[0.6rem]  w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                             data-modal-hide="receiver-received-challan">
                             <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 14 14">
                                 <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                     d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                             </svg>
                             <span class="sr-only">Close modal</span>
                         </button>
                     </div>
                     <!-- Modal body -->
                     <div class="p-6 space-y-6">
                         <div class="grid grid-cols-2 gap-4 mt-2 text-xs">
                             <!-- Left side (Dropdown) -->
                             <div class="relative">
                                 <input
                                     class="multi-select-input w-full px-4 py-2 h-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white border border-gray-400"
                                     placeholder="Select Team Members..."
                                     readonly
                                 >
                                 <div
                                     class="multi-select-dropdown absolute z-10 w-full mt-1 text-xs bg-white rounded-md shadow-lg hidden"
                                 >
                                     <div class="max-h-60 overflow-y-auto">
                                         <ul class="py-1">
                                             @if (isset($teamMembers) && is_array($teamMembers))
                                                 @foreach ($teamMembers as $team)
                                                     @php $team = (object) $team; @endphp
                                                     @if ($team !== null && $team->id !== auth()->id())
                                                         <li>
                                                             <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                                                 <input
                                                                     type="checkbox"
                                                                     value="{{ $team->id }}"   wire:model.defer="team_user_ids"
                                                                     data-name="{{ $team->team_user_name }}"
                                                                     class="multi-select-option form-checkbox h-5 w-5 text-blue-500"
                                                                 >
                                                                 <span class="ml-2 text-gray-700">{{ $team->team_user_name }}</span>
                                                             </label>
                                                         </li>
                                                     @endif
                                                 @endforeach
                                             @endif
                                         </ul>
                                     </div>
                                 </div>
                             </div>

                             <!-- Right side (Comment box) -->
                             <div class="relative">
                                 <textarea
                                     wire:model.defer="comment"
                                     class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                     placeholder="Comment"
                                 ></textarea>
                                 <div class="text-center mt-2 text-[0.6rem]">Less than 100 words only</div>
                             </div>
                         </div>
                         <div class="flex justify-end">
                             <button  type="button" wire:click='sfpChallan'
                                 class="text-white btn btn-sm mt-6 btn-circle btn-ghost bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs border-2 border-gray-300 px-10 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
                         </div>
                     </div>
                </div>
             </div>
        </tbody>

      </table>

      <livewire:components.sfp-component :panelType="'received_return_challan'"/>
    <livewire:components.tag-component :panelId="2" :tableId="4" />
    <livewire:components.comment-component :panelId="2" :tableId="4" />
    </div>
      {{ $challans->links() }}

    <!-- Modal -->

    <div x-data="{ isOpen: @entangle('isOpen') }"
    x-show="isOpen"
    x-on:keydown.escape.window="isOpen = false"
    x-on:close.stop="isOpen = false"
    class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
    <div class="bg-white p-6 rounded shadow-lg w-80 sm:w-96">
    <div class="mb-4">
        <h1 class="text-lg text-black border-b border-gray-400">{{ $modalHeading }}</h1>
        <div class="">
            <div class="relative w-full min-w-[200px] h-10 mt-5">
                <input class="peer w-full text-black h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                        placeholder=" "
                        wire:model.defer="status_comment" />
                <label class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">
                    Comment
                </label>
            </div>
        </div>
        @error('comment')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    </div>
    <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
        <button x-on:click="isOpen = false"
                class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
            Cancel
        </button>
        <button wire:click="{{ $modalAction }}"
                class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs   text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
            {{ $modalButtonText }}
        </button>
    </div>
    </div>
    <div x-on:click.self="isOpen = false" class="inset-0 bg-black opacity-50"></div>
    </div>

    {{-- Tag Modal --}}
    @if ($openSearchModal == true)
    <div x-data="{
        openSearchModal: @entangle('openSearchModal'),
        selectedTags: @entangle('selectedTags'),
        temporarySelectedTags: @entangle('selectedTags').defer,
        isSaveDisabled() {
            return this.temporarySelectedTags.length === 0;
        }
    }"
    x-show="openSearchModal"
    x-on:keydown.escape.window="openSearchModal = false"
    x-on:close.stop="openSearchModal = false"
    class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <div class="mb-4">
            <h1 class="text-lg text-black border-b border-gray-400">{{ $searchModalHeading }}</h1>
            <div class="">
                <div class="relative w-full min-w-[200px] h-10 mt-5">
                    <input class="peer w-full text-black h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                        placeholder=" "
                        wire:model.debounce.500ms="searchTerm" />
                    <label class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">
                        Search
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                    @if(!$isSearchTermMatched && !empty($searchTerm))
                        <button class="flex text-black text-sm" wire:click="createTag">
                            {{ $searchTerm }}
                            <div class="flex text-black text-xs gap-1 whitespace-nowrap ml-2 hover:underline">
                                <svg class="w-4 h-5 text-green-500  m-auto  dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>

                                </svg>
                                <span class="my-auto text-green-500">Create Tag</span>
                            </div>
                        </button>
                    @endif
                </div>
                <div class="mt-4">
                    <p class="text-black text-base">Available Tags:</p>
                    <div>
                        @foreach($tagss as $tag)
                            <label class="inline-flex items-center mt-3 text-sm">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-gray-600"
                                    x-model="temporarySelectedTags"
                                    :value="{{ $tag->id }}"
                                    @if(in_array($tag->id, $selectedTags)) checked @endif>
                                <span class="ml-2 text-gray-700 text-sm">{{ $tag->name }}</span>
                            </label> <br>
                        @endforeach
                    </div>
                    <div class="pagination-container">
                        @if($tagss)
                            {{ $tagss->links() }}
                        @endif
                    </div>
                </div>
                @error('comment')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                <button x-on:click="openSearchModal = false" wire:click="closeTagModal"
                        class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500 transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                    Cancel
                </button>
                <button wire:click="{{ $searchModalAction }}"
                        x-bind:disabled="isSaveDisabled()"
                        class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                    {{ $searchModalButtonText }}
                </button>
            </div>
        </div>
    </div>
</div>
    @endif
    </div>

</div>
@endif

<script>
    window.addEventListener('show-error-message', event => {
            // Set the message in the modal
            document.getElementById('errorMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('errorModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('errorModal').style.display = 'none';
            }, 15000);
        });

        window.addEventListener('show-success-message', event => {
            // Set the message in the modal
            document.getElementById('successMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 15000);
        });

    document.addEventListener('livewire:update', function() {
        console.log('Livewire Update');
        initializeMultiSelect();
    });

    document.addEventListener('DOMContentLoaded', function() {
        initializeMultiSelect();
    });

    function initializeMultiSelect() {
        const multiSelectInputs = document.querySelectorAll('.multi-select-input');
        const multiSelectDropdowns = document.querySelectorAll('.multi-select-dropdown');

        multiSelectInputs.forEach((input, index) => {
            const dropdown = multiSelectDropdowns[index];
            const options = dropdown.querySelectorAll('.multi-select-option');
            const selectedValues = [];

            input.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            options.forEach(option => {
                option.addEventListener('change', () => {
                    if (option.checked) {
                        selectedValues.push({ id: option.value, name: option.dataset.name });
                    } else {
                        const valueIndex = selectedValues.findIndex(item => item.id === option.value);
                        if (valueIndex !== -1) {
                            selectedValues.splice(valueIndex, 1);
                        }
                    }
                    input.value = selectedValues.map(item => item.name).join(', ');
                });
            });

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    }

</script>
