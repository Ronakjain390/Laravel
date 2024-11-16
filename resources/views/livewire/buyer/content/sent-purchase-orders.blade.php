<div>

    <div wire:init="loadData">
        @if ($this->isLoading)

        @include('livewire.sender.screens.placeholders')

        @else

        @php
        // dd(json_decode($mainUser));
        $mainUser = json_decode($mainUser);
        @endphp
        <div class="hidden sm:flex rounded-md pb-2 shadow-sm justify-end" role="group">
            <button wire:click="innerFeatureRedirect('new_purchase_order', 22)" type="button" class="rounded-l-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">New Purchase Order</button>
            {{-- <button wire:click="innerFeatureRedirect('po_design', null)" type="button"
                class="border-b border-t border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">PO
                Design</button> --}}
            <button wire:click="innerFeatureRedirect('detailed_purchase_order_buyer', null)" type="button" class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed View</button>
            {{-- <a  href="{{ route('buyer.exportPurchaseOrder') }}"
            class="rounded-r-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</a> --}}
             <div x-data="{ open: false, exportOption: 'current_page' }" class="py-0.5">
                @if ($mainUser && $mainUser->team_user != null)
                @if ($mainUser->team_user->permissions->permission->buyer->export == 1)

            <a @click="open = true"
            class="rounded-r-lg border border-gray-600  px-4 py-1 text-sm  text-black bg-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</a>
            @endif
            @else
            <a @click="open = true"
            class="rounded-r-lg border border-gray-600  px-4 py-1 text-sm  text-black bg-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</a>
           @endif
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
        </div>
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
            allStock = @json($purchaseOrders->pluck('id'));
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
                <div wire:loading class="fixed top-1/2 left-1/2 z-30 transform -translate-x-1/2 -translate-y-1/2  bg-opacity-50 ">
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
                        <th x-show="selectedCount > 0">
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
                                    <th x-show="selectedCount > 0"></th>

                    </thead>

                </div>
                <tbody class="text-black">
                    @if (count($purchaseOrders))
                    @foreach ($purchaseOrders as $key => $columnName)
                        @php
                            $columnName = (object) $columnName;
                            $mainUser = json_decode($this->mainUser);
                            // dd($columnName->tableTags);
                        @endphp
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
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $key + 1 }}</td>


                                <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->purchase_order_series }}-{{ $columnName->series_num }}</td>


                                {{-- <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ date('h:i A', strtotime($columnName->created_at)) }}</td> --}}
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ date('j F Y', strtotime($columnName->created_at)) }}
                                    <p class="text-[8px]">{{ date('h:i A', strtotime($columnName->created_at)) }}</p>
                                </td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->seller_name }}
                                    @if (isset($columnName->statuses[0]->team_user_name) && $columnName->statuses[0]->team_user_name != null)
                                            ({{ ucfirst($columnName->statuses[0]->team_user_name) }})
                                        @endif
                                </td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->buyer_name }}</td>

                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->total }}</td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->total_qty }}</td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap">{{ $columnName->receiver_details->state ?? '' }}</td>

                                {{-- <td class="px-2  text-[0.6rem] border-2 border-gray-300">
                                    @if (isset($columnName->statuses[0]))
                                        {{ $columnName->statuses[0]->status }} By {{ $columnName->statuses[0]->user_name }} <br> on
                                        {{ date('j F Y, h:i A', strtotime($columnName->statuses[0]->created_at)) }}
                                    @endif
                                </td> --}}
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 whitespace-nowrap
                                @if(isset($columnName->statuses[0]) && $columnName->statuses[0]->status == 'reject') bg-red-500 @endif">
                                    @if (isset($columnName->statuses[0]))
                                            @php

                                            $columnName->statuses[0] = (object) $columnName->statuses[0];
                                        @endphp
                                    @if ($columnName->statuses[0]->status == 'draft')
                                    <span class="text-red-500">Draft</span>
                                        @elseif($columnName->statuses[0]->status == 'created')
                                        Created
                                        @elseif($columnName->statuses[0]->status == 'sent')
                                        Sent
                                        @elseif($columnName->statuses[0]->status == 'self_accept')
                                        Self Delivered
                                        @elseif($columnName->statuses[0]->status == 'accept')
                                        Accepted By {{ $columnName->statuses[0]->user_name }}
                                        @elseif($columnName->statuses[0]->status == 'reject')
                                        Rejected By {{ $columnName->statuses[0]->user_name }}
                                        @elseif($columnName->statuses[0]->status == 'return')
                                        Self Returned
                                    @endif
                                        <p class="text-[0.6rem] ">
                                            {{ date('j F Y, h:i A', strtotime($columnName->statuses[0]->created_at)) }}</p>
                                    @endif
                                </td>

                                {{-- <td class="px-2  text-[0.6rem] border-2 border-gray-300">

                                    @if(isset($columnName) && $columnName->tableTags)
                                        @foreach ($columnName->tableTags as $tagsArray )
                                        <span class="bg-gray-300 rounded-lg p-1 mr-1">
                                            {{$tagsArray->name}}
                                        </span>
                                        @endforeach
                                    @endif
                                </td> --}}
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

                                <td class="px-2  text-[0.6rem] border-2 border-gray-300" >
                                    {{-- @dd($columnName->tags) --}}
                                    @if(isset($columnName) && $columnName->tableTags)
                                        @foreach ($columnName->tableTags as $tagsArray )
                                        <span class="bg-gray-300 rounded-lg p-1 mr-1">
                                            {{$tagsArray->name}}
                                        </span>
                                        @endforeach
                                    @endif
                                </td>
                                <td x-show="selectedCount === 0" class="px-2 p-0 text-[0.6rem] border-2 border-gray-300">
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
                                                        <a x-data
                                                            wire:click="$emit('openSfpModal', { challanId: {{ $columnName->id ?? 'null' }}, type: 'sent_purchase_order' })"
                                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                                SFP
                                                        </a>
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
                                                        {{-- @if (isset($columnName->pdf_url))
                                                        <li>
                                                            <a href="#"
                                                                onclick="printPdf('{{ Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5)) }}')"
                                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Print</a>
                                                        </li>
                                                        @endif --}}
                                                        @if ($mainUser->team_user != null)
                                                        @if ($mainUser->team_user->permissions->permission->buyer->add_po_comment == 1)
                                                        <li>
                                                            <a x-data
                                                            wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'addComment')"
                                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                            Add Comment
                                                        </a>
                                                        </li>
                                                        @endif
                                                    @else
                                                    <li>
                                                        <a x-data
                                                        wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'addComment')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                        Add Comment
                                                    </a>
                                                    </li>
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

                                                @if ($columnName->statuses[0]->status == 'draft')
                                                    @if ($mainUser->team_user != null)
                                                        @if ($mainUser->team_user->permissions->permission->buyer->send_purchase_order == 1)
                                                        <li>
                                                            <a x-data
                                                               wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'sendPO')"
                                                               class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                              Send
                                                            </a>
                                                        </li>
                                                        @endif
                                                    @else
                                                    <li>
                                                        <a x-data
                                                           wire:click="$emit('openCommentModal', {{ $columnName->id }}, 'sendPO')"
                                                           class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                          Send
                                                        </a>
                                                    </li>
                                                    @endif
                                                @endif

                                                @if ($mainUser->team_user != null)
                                                    @if ($mainUser->team_user->permissions->permission->buyer->add_po_tags == 1)
                                                    <li>
                                                        <a x-data
                                                           wire:click="$emit('openTagModal', {{ $columnName->id }}, 'addTags')"
                                                           class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                           Add Tag
                                                        </a>
                                                    </li>
                                                    @endif
                                                @else
                                                <li>
                                                    <a x-data
                                                       wire:click="$emit('openTagModal', {{ $columnName->id }}, 'addTags')"
                                                       class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                       Add Tag
                                                    </a>
                                                </li>
                                                @endif


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
                                                {{-- @if ($mainUser->team_user != null)
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
                                                @endif --}}
                                                {{-- @endif --}}
                                                {{-- @if ($columnName->statuses[0]->status == 'draft' || $columnName->statuses[0]->status == 'reject')
                                                    @if ($mainUser->team_user != null)
                                                        @if ($mainUser->team_user->permissions->permission->seller->modify_invoice == 1)
                                                            <li>
                                                                <a href="#" wire:click="deleteInvoice('{{ $columnName->id }}')"
                                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                            </li>
                                                        @endif
                                                    @else
                                                        <li>
                                                            <a href="#" wire:click="deleteInvoice('{{ $columnName->id }}')"
                                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                        </li>
                                                    @endif
                                                @endif --}}
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
        </div>
        @if ($sfpModal == true)
        <div x-data="{ sfpModal: @entangle('sfpModal') }"
            x-show="sfpModal"
            x-on:keydown.escape.window="sfpModal = false"
            x-on:close.stop="sfpModal = false"
            class="fixed inset-0 flex items-center justify-center px-2.5 z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60" wire:ignore.self>
            <div class="bg-white p-6 rounded shadow-lg w-full max-w-md" x-data="{ selected: [], isButtonEnabled: false }">
                <div class="mb-4">
                    <h1 class="text-lg text-black border-b border-gray-400">Send For Processing </h1>
                    <div class="">

                        <div class="p-4 md:p-5 text-center">
                            {{-- <h1 class="text-lg text-black text-left">Add tags</h1> --}}
                            <form class="max-w-md mx-auto mt-5">
                                <div class="grid  gap-4 mt-2 text-xs">
                                    <!-- Left side (Dropdown) -->
                                    <div class="relative text-left">
                                        <label for="" class="test-xs text-black">Select Team Members</label> <br>
                                        <select class="js-example-basic-multiple" name="team_user_ids[]" multiple="multiple" wire:model.defer="team_user_ids">
                                            <option disabled>Select Team Members...</option>
                                            @if (isset($teamMembers) && is_array($teamMembers))
                                                @php
                                                    $addedOwners = [];
                                                    function arrayToObject($array) {
                                                        if (is_array($array)) {
                                                            return (object) array_map('arrayToObject', $array);
                                                        }
                                                        return $array;
                                                    }
                                                @endphp
                                                @foreach ($teamMembers as $team)
                                                    @php $team = arrayToObject($team); @endphp
                                                    @if ($team !== null && $team->id !== auth()->id())
                                                        <!-- Team Member Option -->
                                                        <option value="{{ $team->id }}" data-name="{{ $team->team_user_name }}">
                                                            {{ $team->team_user_name }}
                                                        </option>

                                                        @if (isset($team->owner) && !in_array($team->owner->id, $addedOwners) && $team->owner->id !== auth()->id())
                                                            <!-- Team Owner Option -->
                                                            <option value="{{ $team->owner->id }}" data-name="Admin">
                                                                Admin ({{ $team->owner->name }})
                                                            </option>
                                                            @php $addedOwners[] = $team->owner->id; @endphp
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>

                                    </div>

                                    <!-- Right side (Comment box) -->
                                    <div class="relative">
                                        <textarea oninput="if(this.value.length > 100) this.value = this.value.slice(0, 100);"
                                            wire:model.defer="comment"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] w-2/3  rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Comment"
                                        ></textarea>
                                        <div class="text-center mt-2 text-[0.6rem]">Less than 100 words only</div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        </div>

                </div>
                <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                    <button wire:click="closeSfpModal" x-on:click.self="sfpModal = false"
                            class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        Cancel
                    </button>
                    <button wire:click="sfpInvoice"
                            class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs   text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                    Send
                    </button>
                </div>
            </div>
        </div>
        @endif
        <livewire:components.tag-component :panelId="4" :tableId="7" />
        <livewire:components.comment-component :panelId="4" :tableId="7" />
        <livewire:components.sfp-component :panelType="'sent_purchase_order'"/>
        @endif
    </div>

    <script>
         function initializeSelect2() {
                    $('.js-example-basic-multiple').select2().on('change', function (e) {
                        @this.set('team_user_ids', $(this).val());
                    });
                }

                document.addEventListener('livewire:update', function() {
                    initializeSelect2();
                    @this.on('openSfpModal', () => {
                        setTimeout(() => {
                            $('.js-example-basic-multiple').select2({
                                templateResult: formatState
                            }).on('change', function (e) {
                                @this.set('team_user_ids', $(this).val());
                            });
                        }, 100);
                    });
                    console.log('Livewire Update');
                    //
                    initializeMultiSelect();
                    window.dispatchEvent(new CustomEvent('challansUpdated'));
                });

                document.addEventListener('DOMContentLoaded', function() {
                    initializeMultiSelect();
                    initializeSelect2();
                    console.log('DOMContentLoaded');
                });

                document.addEventListener('livewire:load', function () {
                    console.log('Livewire Load');
                    initializeSelect2();
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

            window.addEventListener('downloadCompleted', function() {
                console.log('Download completed');
                window.location.reload();
            });
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
    </script>
</div>
