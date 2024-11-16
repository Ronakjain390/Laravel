<div>


    @php
    // dd(json_decode($mainUser));
    $mainUser = json_decode($mainUser);
    @endphp
    <div class="justify-end hidden sm:flex">
        <a href="javascript:history.back()" class="border border-gray-900 dark:border-white dark:focus:bg-gray-700 dark:hover:bg-gray-700 dark:hover:text-white dark:text-white focus:bg-gray-900 focus:ring-2 focus:ring-gray-500 focus:text-white focus:z-10 mb-2 mr-2 px-4 py-1 rounded-r-lg rounded-lg text-black text-sm">Back</a>
    </div>
    <div wire:init="loadData">
        @if ($this->isLoading)

        @include('livewire.sender.screens.placeholders')

        @else

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
        }"
        class="min-w-full overflow-auto bg-white shadow-md rounded-lg"
        >
    <div x-data="checkboxes()" x-init="initCheckboxes({{ $challans->pluck('id') }})" class="min-w-full overflow-auto">
        <div>
            @if (Session::has('sentMessage'))
            @php
                $sentMessage = Session::get('sentMessage');
            @endphp
            @if (isset($sentMessage['type']) && $sentMessage['type'] === 'error' && is_array($sentMessage['content']))
                @foreach ($sentMessage['content'] as $msg)
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span class="font-medium">Error:</span> {{ $msg }}
                    </div>
                @endforeach
                @elseif (isset($sentMessage['type']) && $sentMessage['type'] === 'success')
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
                        <div class="ms-3 text-sm">
                            <span class="font-medium">Success:</span> {{ $sentMessage['content'] }}
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-border-3" aria-label="Close">
                            <span class="sr-only">Dismiss</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif
            @endif
        </div>
        <table  class="w-full text-xs text-left text-gray-500 dark:text-gray-400" x-data="{ showSelected: false }">
            <div wire:loading class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  bg-opacity-50 ">
                <span class="loading loading-spinner loading-md"></span>
            </div>
            <div x-data="checkboxes()" x-init="initCheckboxes({{ $challans->pluck('id') }})" class="min-w-full overflow-auto">

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
                <tbody class="text-black"  x-data="{ open: false, modalContent: {} }">
                    @if ($challans && count($challans))
                        @foreach ($challans as $counter => $columnName)
                            @php
                                $columnName = (object) $columnName;
                                // dd($columnName);
                                $mainUser = json_decode($this->mainUser);
                                $panel = Session::get('panel');
                                // $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
                            @endphp
                            @if (isset($columnName->pdf_url))
                            <tr ondblclick="window.open('https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}', '_blank')"
                                class=" cursor-pointer whitespace-nowrap @if ($counter % 2 == 0) bg-[#e9e6e6] dark:border-gray-700 hover:cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif">
                            {{-- <td class="p-1">
                                     <input type="checkbox"
                                class="form-checkbox"
                                :checked="selectedProducts.includes({{ $columnName->id }})"
                                @click="toggleProduct({{ $columnName->id }})">
                                </td> --}}
                            <td class="px-2  text-[0.6rem] border-2 border-gray-300 p-1.5 ">
                                    {{-- {{ $columnName->custom_item_number }} --}}
                                    {{ $counter + 1 }}
                                </div>
                                </td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                                {{-- <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td> --}}
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

                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                                    {{ ucfirst($columnName->sender) }}
                                    @if (isset($columnName->statuses[0]->team_user_name) && $columnName->statuses[0]->team_user_name != null)
                                        ({{ ucfirst($columnName->statuses[0]->team_user_name) }})
                                    @endif
                                </td>
                                </td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->total_qty }}</td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->total }} </td>
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver_details->state ?? null }}</td>
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
                                                    @elseif ($columnName->statuses[0]->status == 'deleted')
                                                    Deleted
                                                @endif
                                                @if ($columnName->statuses[0]->status != 'sent')
                                                <p style="font-size: smaller">
                                                    @if ($columnName->statuses[0]->status == 'deleted')
                                                        {{ date('j F Y, h:i A', strtotime($columnName->deleted_at)) }}
                                                    @else
                                                        {{ date('j F Y, h:i A', strtotime($columnName->created_at)) }}
                                                    @endif
                                                </p>
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
                                                                                @elseif($status->status === 'self_accept')
                                                                                    Self Delivered
                                                                                @else
                                                                                    {{ ucfirst($status->status) }}
                                                                                @endif
                                                                                by:
                                                                                @if(!empty($status->team_user_name))
                                                                                    {{ htmlspecialchars($status->team_user_name) }}
                                                                                @else
                                                                                    {{ htmlspecialchars($status->user_name) }}
                                                                                @endif
                                                                            </span>
                                                                            <time class="block mb-3 text-xs leading-none text-black">
                                                                                @if ($columnName->statuses[0]->status == 'deleted')
                                                                                {{ date('j F Y, h:i A', strtotime($columnName->deleted_at)) }}
                                                                            @else
                                                                                {{ date('j F Y, h:i A', strtotime($columnName->created_at)) }}
                                                                            @endif
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
                                {{-- @dump(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->payment_status) --}}
                                @if(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->payment_status)
                                <td class="px-2  text-[0.6rem] border-2 border-gray-300">
                                        @if(isset($columnName) && $columnName->deliveryStatus)
                                            @foreach ($columnName->deliveryStatus as $tagsArray )
                                            <span class="bg-gray-300 rounded-lg p-1 mr-1">
                                                {{$tagsArray->name}}
                                            </span>
                                            @endforeach
                                        @endif
                                    </td>
                                    @endif

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
                            <!-- Status Show and open modal -->
                                <td class="px-2 text-[0.6rem] border-2 border-gray-300 whitespace-normal" x-data="{ open: false }">
                                    @php
                                        $statusComments = json_decode($columnName->status_comment, true);
                                        $firstComment = (is_array($statusComments) && isset($statusComments[0])) ? $statusComments[0] : null;
                                        $commentCount = is_array($statusComments) ? count($statusComments) . ' '. 'Comment' : 0;
                                    @endphp
                                    @if($firstComment !== null && isset($firstComment['comment']) && isset($firstComment['name']) && $firstComment['date'] && $firstComment['time'] && !empty(trim($firstComment['comment'])))
                                        {{-- <h3 @click="open = true" class="flex items-start mb-1 text-xs text-gray-900 dark:text-white cursor-pointer">{{ Str::limit(ucfirst($firstComment['comment']), 4, '...') }}</h3> --}}
                                        <h3 @click="open = true" class="flex items-start mb-1 text-[0.6rem] text-gray-900 dark:text-white cursor-pointer"> {{ $commentCount }} </h3>
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
                                @if(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->tags)
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
                                    @endif


                            </tr>
                            @endif


                            {{-- <div id="delivery-status-{{$columnName->id}}" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore.self>
                                <div class="relative p-4 w-full max-w-2xl max-h-full">
                                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                        <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-11 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delivery-status-{{$columnName->id}}">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                        <div class="p-4 md:p-5 text-center">
                                            <h1 class="text-lg text-black text-left">Payment Status</h1>
                                            <form class="max-w-md mx-auto mt-5">
                                                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 p-2 start-0 flex items-center ps-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                                        </svg>
                                                    </div>
                                                    <input type="search" id="default-search" wire:model.debounce.300ms="searchQuery"  class="block w-full px-8 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Tags..." required />
                                                </div>
                                            </form>
                                            @if(!empty($deliveryStatus))
                                                <ul>
                                                    @foreach($deliveryStatus as $newTag)
                                                    <li class="join">
                                                        <button wire:click="createDeliveryStatus('{{ $newTag }}', '{{ $columnName->id }}')" class="text-black flex text-sm">
                                                            {{ $newTag }}
                                                            @if($newTag)
                                                            <svg class="w-5 m-auto text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                            </svg>
                                                            @endif
                                                        </button>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            <h1 class="text-justify text-sm text-black mt-4 font-semibold ">Available Status</h1>
                                            <div class="flex flex-col mt-3">
                                                @forelse($availableDeliveryStatus as $deliveryStatus)
                                                    <div class="flex items-center mb-4 text-sm font-normal">
                                                        <input type="checkbox" id="tag-{{ $deliveryStatus->id }}" wire:model="selectedDeliveryStatus" value="{{ $deliveryStatus->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @if($columnName->tags->contains($deliveryStatus)) checked @endif>
                                                        <label for="tag-{{ $deliveryStatus->id }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $deliveryStatus->name }}</label>
                                                    </div>
                                                @empty
                                                    <p>No tags found.</p>
                                                @endforelse
                                            </div>

                                            <div class="flex mt-10 justify-end gap-3">
                                                <button wire:click="assignDeliveryStatus({{$columnName->id}})" class="text-white bg-black hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-1.5 text-center">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                                <script>
                                    document.addEventListener('alpine:init', () => {
                                        Alpine.data('signaturePad', () => ({
                                            signaturePadInstance: null,
                                            init() {
                                                this.signaturePadInstance = new SignaturePad(this.$refs.signature_canvas);
                                                this.$nextTick(() => {
                                                    this.$refs.signature_canvas.width = 350;
                                                    this.$refs.signature_canvas.height = 450;
                                                });
                                            },
                                            upload() {
                                                let columnId = document.getElementById('column-id').value;
                                                @this.set('signature', this.signaturePadInstance.toDataURL('image/png'));
                                                @this.set('columnId', columnId);
                                                console.log(columnId)
                                                // console.log(this.signaturePadInstance.toDataURL('image/png'));
                                                @this.call('saveSignature');
                                            },
                                            clear() {
                                                this.signaturePadInstance.clear();
                                            }
                                        }))
                                    });
                                </script>
                        @endforeach

                    @endif
                    @if ($openPaymentStatusModal == true)
                    <div x-data="{ openPaymentStatusModal: @entangle('openPaymentStatusModal') }"
                    x-show="openPaymentStatusModal"
                    x-on:keydown.escape.window="openPaymentStatusModal = false"
                    x-on:close.stop="openPaymentStatusModal = false"
                    class="fixed inset-0 flex items-center justify-center px-2.5 z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
                    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                        <div class="mb-4">
                            <h1 class="text-lg text-black border-b border-gray-400">{{ $searchModalHeading }}</h1>
                            <div class="">

                                <div class="p-4 md:p-5 text-center">
                                    {{-- <h1 class="text-lg text-black text-left">Add tags</h1> --}}
                                    <form class="max-w-md mx-auto mt-5">
                                        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 p-2 start-0 flex items-center ps-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                                </svg>
                                            </div>
                                            <input type="search" id="default-search" wire:model.debounce.300ms="searchQuery"  class="block w-full px-8 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Tags..." required />
                                        </div>
                                    </form>
                                    @if(!empty($deliveryStatus))
                                    <ul>
                                        @foreach($deliveryStatus as $newTag)
                                        <li class="join">
                                            <button wire:click="createdeliveryStatus('{{ $newTag }}', '{{ $columnName->id }}')" class="text-black flex text-sm">
                                                {{ $newTag }}
                                                @if($newTag)
                                                <svg class="w-5 m-auto text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                @endif
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>
                                @endif
                                    <h1 class="text-justify text-sm text-black mt-4 font-semibold ">Available Tags</h1>
                                    <div class="flex flex-col mt-3">
                                        @forelse($availableDeliveryStatus as $deliveryStatus)
                                        <div class="flex items-center mb-4 text-sm font-normal">
                                            <input type="checkbox" id="tag-{{ $deliveryStatus->id }}" wire:model="selectedDeliveryStatus" value="{{ $deliveryStatus->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @if($columnName->tags->contains($deliveryStatus)) checked @endif>
                                            <label for="tag-{{ $deliveryStatus->id }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $deliveryStatus->name }}</label>
                                        </div>
                                    @empty
                                        <p>No tags found.</p>
                                    @endforelse
                                    </div>
                                </div>
                                </div>
                            @error('comment')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                            <button wire:click="closeTagModal"
                                    class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                                Cancel
                            </button>
                            <button wire:click="assignDeliveryStatus({{$columnName->id}})"
                                    class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs   text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                                {{ $searchModalButtonText }}
                            </button>

                        </div>
                    </div>
                    <div x-on:click.self="openPaymentStatusModal = false" class="inset-0 bg-black opacity-50"></div>
                    </div>
                    @endif


                    @if ($sfpModal == true)
                    <div x-data="{ sfpModal: @entangle('sfpModal') }"
                    x-show="sfpModal"
                    x-on:keydown.escape.window="sfpModal = false"
                    x-on:close.stop="sfpModal = false"
                    class="fixed inset-0 flex items-center justify-center px-2.5 z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
                    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md" x-data="{ selected: [], isButtonEnabled: false }">
                        <div class="mb-4">
                            <h1 class="text-lg text-black border-b border-gray-400">Send For Processing </h1>
                            <div class="">

                                <div class="p-4 md:p-5 text-center">
                                    {{-- <h1 class="text-lg text-black text-left">Add tags</h1> --}}
                                    <form class="max-w-md mx-auto mt-5">
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
                                                                        <li>
                                                                            <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    value="{{ $team->id }}" wire:model.defer="team_user_ids"
                                                                                    data-name="{{ $team->team_user_name }}"
                                                                                    class="multi-select-option form-checkbox h-5 w-5 text-blue-500"
                                                                                    x-on:change="selected = $el.checked ? [...selected, $el.value] : selected.filter(id => id !== $el.value); isButtonEnabled = selected.length > 0"
                                                                                >
                                                                                <span class="ml-2 text-gray-700">{{ $team->team_user_name }}</span>
                                                                            </label>
                                                                        </li>
                                                                        @if (isset($team->owner) && !in_array($team->owner->id, $addedOwners) && $team->owner->id !== auth()->id())
                                                                            <li>
                                                                                <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        value="{{ $team->owner->id }}" wire:model.defer="admin_ids"
                                                                                        data-name="Admin"
                                                                                        class="multi-select-option form-checkbox h-5 w-5 text-blue-500"
                                                                                        x-on:change="selected = $el.checked ? [...selected, $el.value] : selected.filter(id => id !== $el.value); isButtonEnabled = selected.length > 0"
                                                                                    >
                                                                                    <span class="ml-2 text-gray-700">Admin</span>
                                                                                </label>
                                                                            </li>
                                                                            @php $addedOwners[] = $team->owner->id; @endphp
                                                                        @endif
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
                                    </form>

                                </div>
                                </div>

                        </div>
                        <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                            <button wire:click="closeSfpModal"
                                    class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                                Cancel
                            </button>
                            <button wire:click="sfpChallan"
                            :disabled="!isButtonEnabled"
                            :class="{ 'bg-black': isButtonEnabled, 'bg-gray-400': !isButtonEnabled }"
                            class="middle none center rounded-lg py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        Send
                    </button>
                        </div>
                    </div>
                    <div x-on:click.self="sfpModal = false" class="inset-0 bg-black opacity-50"></div>
                    </div>
                    @endif


                        {{-- <div id="edit-modal" tabindex="-1"
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
                                        data-modal-hide="edit-modal">
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
                                                                    @if (isset($team->owner) && !in_array($team->owner->id, $addedOwners) && $team->owner->id !== auth()->id())
                                                                        <li>
                                                                            <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    value="{{ $team->owner->id }}"   wire:model.defer="admin_ids"
                                                                                    data-name="Admin"
                                                                                    class="multi-select-option form-checkbox h-5 w-5 text-blue-500"
                                                                                >
                                                                                <span class="ml-2 text-gray-700">Admin</span>
                                                                            </label>
                                                                        </li>
                                                                        @php $addedOwners[] = $team->owner->id; @endphp
                                                                    @endif
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
                        </div> --}}
                    </div>
                </tbody>
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

                {{-- @if($columnName)
                    <div x-data="{ BulkisOpen: @entangle('BulkisOpen') }"
                    x-show="BulkisOpen"
                    x-on:keydown.escape.window="BulkisOpen = false"
                    x-on:close.stop="BulkisOpen = false"
                    class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
                    <div class="relative p-4 w-full max-w-2xl max-h-full">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-11 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="tag-{{$columnName->id}}">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>`
                            </button>
                            <div class="p-4 md:p-5 text-center">
                                <h1 class="text-lg text-black text-left">{{ $bulkmodalHeading }}</h1>

                                @php
                                // Ensure $tags is always treated as an array
                                if (!is_array($tags)) {
                                    // Assuming $tags is a comma-separated string of tags
                                    $tags = explode(',', $tags);
                                }
                            @endphp

                            @if(!empty($tags))
                                <ul>
                                    @foreach($tags as $newTag)
                                    <li class="join">
                                        <button wire:click="createTag('{{ $newTag }}', '{{ $columnName->id }}')" class="text-black flex text-sm whitespace-nowrap">
                                            {{ $newTag }}
                                            @if($newTag)
                                            <svg class="w-5 m-auto text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                            </svg>
                                            @endif
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            @endif

                                <h1 class="text-justify text-sm text-black mt-4 font-semibold "> Available {{$bulkSubHeading}}</h1>
                                <div class="flex flex-col mt-3">
                                    @php
                                        // Ensure $bulkAvailableEntities is an array or object
                                        $bulkAvailableEntities = $bulkAvailableEntities ?? [];
                                    @endphp

                                    @forelse($bulkAvailableEntities as $tag)
                                        <div class="flex items-center mb-4 text-sm font-normal">
                                            <input type="checkbox" id="tag-{{ $tag->id }}" wire:model="selectedTags" value="{{ $tag->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @if($columnName->tags->contains($tag)) checked @endif>
                                            <label for="tag-{{ $tag->id }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $tag->name }}</label>
                                        </div>
                                    @empty
                                        <p>No tags found.</p>
                                    @endforelse
                                </div>

                                <div class="flex mt-10 justify-end gap-3">
                                    <button wire:click="{{ $BulkModalAction }}" class="py-1.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                                    <button wire:click="{{$bulkActions}}" class="text-white bg-black hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-1.5 text-center">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                @endif
                 --}}

            <div>

            </div>


        </table>

        {{ $challans->links() }}
        @endif
    </div>
    </div>
</div>
</div>
</div>

</div>









</div>
<script>



document.addEventListener('livewire:update', function() {
    console.log('Livewire Update');

    initializeMultiSelect();
    window.dispatchEvent(new CustomEvent('challansUpdated'));
});

document.addEventListener('DOMContentLoaded', function() {
    initializeMultiSelect();
});



function initializeAlpine() {
    Alpine.data('checkboxes', () => ({
        selectedProducts: [],
        currentPageIds: [],
        showSelected: false,

        initCheckboxes(ids) {
            this.currentPageIds = ids;
        },

        get allChecked() {
            return this.currentPageIds.length > 0 && this.currentPageIds.every(id => this.selectedProducts.includes(id));
        },

        get selectedCount() {
            return this.selectedProducts.length;
        },

        toggleAll() {
            if (this.allChecked) {
                this.selectedProducts = this.selectedProducts.filter(id => !this.currentPageIds.includes(id));
            } else {
                this.selectedProducts = [...new Set([...this.selectedProducts, ...this.currentPageIds])];
            }
            this.updateAllChecked();
            this.updateShowSelected();
        },

        updateAllChecked() {
            this.allChecked = this.currentPageIds.length > 0 && this.currentPageIds.every(id => this.selectedProducts.includes(id));
            this.updateShowSelected();
        },

        updateShowSelected() {
            this.showSelected = this.selectedProducts.length > 0;
        },

        toggleProduct(productId) {
            const index = this.selectedProducts.indexOf(productId);
            if (index === -1) {
                this.selectedProducts.push(productId);
                console.log(this.selectedProducts);
            } else {
                this.selectedProducts.splice(index, 1);
                console.log(this.selectedProducts);
            }
            this.updateAllChecked();
        }
    }));

    window.addEventListener('page-updated', event => {
        Alpine.find(document.querySelector('[x-data="checkboxes()"]')).initCheckboxes(event.detail.ids);
    });

    function uncheckAllCheckboxes() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }
}

document.getElementById('addTagButton').addEventListener('click', uncheckAllCheckboxes);
document.getElementById('addCommentButton').addEventListener('click', uncheckAllCheckboxes);


</script>
