
@php
    // dd(json_decode($mainUser));
    $mainUser = json_decode($mainUser);
@endphp
{{-- <div wire:loading.except="updateVariable"    class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
    <span   class="loading loading-spinner loading-md"></span>
</div> --}}

@if ($this->persistedTemplate == 'sent_challan')
    {{-- @dd($challans->links()); --}}

    <!-- SENT CHALLAN ACTION SFP -->
<!-- Main modal -->
<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 p-2 px-3">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-1 border-b rounded-t dark:border-gray-600">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                      Send Challan
                  </h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="default-modal">
                      <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                      </svg>
                      <span class="sr-only">Close modal</span>
                  </button>
              </div>
              <!-- Modal body -->
              <form class="p-1.5">
                  <div class="grid gap-4 mb-4 grid-cols-2">
                      <div class="col-span-2">
                          <label for="comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Comment</label>
                          <input type="text" name="comment" id="comment" wire:model.defer="status_comment"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter Comment" >
                      </div>

                  </div>
                <button type="button" wire:click="" data-id="" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

                </button>
              </form>
        </div>
    </div>
</div>
    <div id="edit-modal" tabindex="-1"
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

@endif

{{-- <script>
    function setModalData(id) {
        // Set the value of the comment input in the modal
        // document.getElementById('comment').value = id;
        document.querySelector('[wire\\:click]').setAttribute('wire:click', "sendChallan('" + id + "')");
    }
</script> --}}
{{-- <script>
    function setModalData(id) {
        // Set the value of the comment input in the modal
        // document.getElementById('comment').value = id;
        var sendButton = document.querySelector('button[data-id]');
        sendButton.setAttribute('wire:click', "sendChallan('" + id + "')");
    }
</script> --}}

    <script>

        function setModalData(id, action) {
            var sendButton = document.querySelector('button[data-id]');
            var methodToCall;
            var modalTitle;
            var buttonTitle;

            switch ("{{ $this->persistedTemplate }}") {
                case 'sent_challan':
                    if (action === 'addComment') {
                        console.log('addComment', id);
                        methodToCall = 'addComment';
                        modalTitle = 'Add Comment';
                        buttonTitle = 'Add Comment';
                    } else {
                        methodToCall = 'sendChallan';
                        modalTitle = 'Send Challan';
                        buttonTitle = 'Send';
                    }
                    break;
                case 'received_return_challan':
                    if (action === 'addComment') {
                        methodToCall = 'addCommentReceivedReturnChallan';
                        modalTitle = 'Add Comment';
                        buttonTitle = 'Add Comment';
                    } else {
                        methodToCall = action === 'accept' ? 'receivedChallanAccept' : 'receivedChallanReject';
                        modalTitle = action === 'accept' ? 'Accept Challan' : 'Reject Challan';
                        buttonTitle = action === 'accept' ? 'Accept' : 'Reject';
                    }
                    break;
                case 'received_challan':
                    if (action === 'addComment') {
                        methodToCall = 'addCommentReceivedChallan';
                        modalTitle = 'Add Comment';
                        buttonTitle = 'Add Comment';
                    } else {
                        methodToCall = action === 'accept' ? 'receivedChallanAccept' : 'receivedChallanReject';
                        modalTitle = action === 'accept' ? 'Accept Challan' : 'Reject Challan';
                        buttonTitle = action === 'accept' ? 'Accept' : 'Reject';
                    }
                    break;
                case 'sent_return_challan':
                    if (action === 'addComment') {
                        methodToCall = 'addCommentSentReturnChallan';
                        modalTitle = 'Add Comment';
                        buttonTitle = 'Add Comment';
                    } else {
                        methodToCall = 'sendChallan';
                        modalTitle = 'Send Challan';
                        buttonTitle = 'Send';
                    }
                    break;
                    case 'sent_invoice':
                    if (action === 'addComment') {
                        methodToCall = 'addCommentSentInvoice';
                        modalTitle = 'Add Comment';
                        buttonTitle = 'Add Comment';
                    } else {
                        methodToCall = 'sendInvoice';
                        modalTitle = 'Send Invoice';
                        buttonTitle = 'Send';
                    }
                    break;
                case 'all_invoice':
                    if (action === 'addComment') {
                        methodToCall = 'addCommentReceivedInvoice';
                        modalTitle = 'Add Comment';
                        buttonTitle = 'Add Comment';
                    } else {
                        methodToCall = action === 'accept' ? 'acceptPurchaseOrder' : 'rejectPurchaseOrder';
                        modalTitle = action === 'accept' ? 'Accept Invoice' : 'Reject Invoice';
                        buttonTitle = action === 'accept' ? 'Accept' : 'Reject';
                    }
                    break;
                default:
                    console.error('Invalid persistedTemplate value');
                    sendButton.textContent = buttonTitle;
                    return;
            }

            sendButton.setAttribute('wire:click', methodToCall + "('" + id + "')");
            // Set the modal title
            document.querySelector('#crud-modal h3').textContent = modalTitle;

            document.querySelector('#default-modal h3').textContent = modalTitle;
            // Set the button title
            sendButton.textContent = buttonTitle;
        }
    </script>

  <!-- Main modal -->
  <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
      <div class="relative p-4 w-full max-w-md max-h-full">
          <!-- Modal content -->
          <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 p-2">
              <!-- Modal header -->
              <div class="flex items-center justify-between p-1 border-b rounded-t dark:border-gray-600">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                      Send Challan
                  </h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                      <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                      </svg>
                      <span class="sr-only">Close modal</span>
                  </button>
              </div>
              <!-- Modal body -->
              <form class="p-1.5">
                  <div class="grid gap-4 mb-4 grid-cols-2">
                      <div class="col-span-2">
                          <label for="comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Comment</label>
                          <input type="text" name="comment" id="comment" wire:model.defer="status_comment"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter Comment" >
                      </div>

                  </div>
                <button type="button" wire:click="" data-id="" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

                </button>
              </form>
          </div>
      </div>
  </div>

@if ($this->persistedTemplate == 'view_sfp_sender_challan')
    @if (count($tableTdData))
        @foreach ($tableTdData as $key => $columnName)
            @php
                $columnName = (object) $columnName;
                $columnName->statuses[0] = (object) $columnName->statuses[0];
                $mainUser = json_decode($this->mainUser);
                $panel = Session::get('panel');
                                $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
            @endphp
            <tr
                class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    {{ ++$key }}</div>
                </td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                    @php
                        if (isset($columnName->statuses[0])) {
                            $columnName->statuses[0] = (object) $columnName->statuses[0];
                        }
                    @endphp
                    @if(!empty($columnName->statuses) && $columnName->statuses[0]->status != 'draft')
                    {{-- @if ($columnName->statuses[0]->status != 'draft') --}}
                    {{ date('d-m-Y', strtotime($columnName->created_at)) }}
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

                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td> --}}
                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    <a href="#my_modal_8"
                        wire:click="updateVariable('challan_sfp', {{ json_encode($columnName->sfp) }})"

                        class="block px-4 py-1 @if (count($columnName->sfp) > 0) dark:hover:text-white @endif  text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 ">Check</a>
                </td> --}}
                <td class="border-2 border-gray-300">
                    <a href="#my_modal_8"
                        wire:click="updateVariable('challan_sfp', {{ json_encode($columnName->sfp) }})"
                        class="block px-4
                        {{-- @foreach ($columnName->sfp as $sfp)
                        @if($sfp->sfp_by_id == Auth::user()->id) bg-red-600 @else bg-green-500 @endif
                        @endforeach --}}

                        @if (count($columnName->sfp) > 0) text-white
                    @foreach ($columnName->sfp as $sfp)
                        @if ($columnName->statuses[0]->status == 'sent')
                        bg-green-600
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                            bg-red-600
                            @else
                            bg-red-600
                            @endif
                        @endif
                    @endforeach

                         hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 border-2 border-gray-300 px-2 .5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">@if (count($columnName->sfp) > 0)
                        @if ($columnName->statuses[0]->status == 'sent')
                        Done
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                Processing
                            @else
                                Check
                            @endif
                        @endif

                        @else
                    @endif
                </td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300 whitespace-normal "
                    data-modal-target="timeline-modal-challan-sfp-{{ $columnName->id }}"
                    data-modal-toggle="timeline-modal-challan-sfp-{{ $columnName->id }}"
                    >
                    @php
                        $statusComments = json_decode($columnName->status_comment, true);
                        $firstComment = (is_array($statusComments) && isset($statusComments[0])) ? $statusComments[0] : null;
                    @endphp
                    @if($firstComment !== null && isset($firstComment['comment']) && isset($firstComment['name']) && $firstComment['date'] && $firstComment['time'] && !empty(trim($firstComment['comment'])))
                        <h3 class="flex items-start mb-1 text-xs text-gray-900 dark:text-white cursor-pointer">{{ Str::limit(ucfirst($firstComment['comment']), 4, '...') }}</h3>
                    @endif
                </td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    <button id="dropdownDefaultButton-{{ $key }}"
                        data-dropdown-toggle="dropdown-{{ $key }}"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                        type="button">Select <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
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
                                            wire:click="updateVariable('challan_id', {{ $columnName->id }})"

                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">SFP</a>
                                    </li>
                                    @endif
                                @endif
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->sender->view_challan == 1)
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
                                {{-- @endif --}}
                                @if ($columnName->statuses[0]->status == 'draft')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->sender->modify_challan == 1)
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
                                @endif
                                {{-- @if ($columnName->statuses[0]->status == 'draft') --}}
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->sender->send_challan == 1)
                                        <li>
                                            <a href="#" wire:click="sendChallan('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                        </li>
                                    @endif
                                @else
                                    <li>
                                        <a href="#" wire:click="sendChallan('{{ $columnName->id }}')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                    </li>
                                @endif
                                {{-- @endif --}}
                                @if ($columnName->statuses[0]->status == 'modified')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->sender->send_challan == 1)
                                            <li>
                                                <a href="#" wire:click="reSendChallan('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#" wire:click="reSendChallan('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                        </li>
                                    @endif
                                @endif
                                @if ($columnName->statuses[0]->status == 'sent')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->sender->accept_challan == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="selfAcceptChallan('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                    Delivery</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#" wire:click="selfAcceptChallan('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                Delivery</a>
                                        </li>
                                    @endif
                                @endif
                                {{-- @if ($columnName->statuses[0]->status == 'reject') --}}
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->sender->modify_challan == 1)
                                        <li>
                                            <a href="#"
                                                wire:click="innerFeatureRedirect('modify_challan' {{ $columnName->id }})"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                        </li>
                                    @endif
                                @else
                                    <li>
                                        <a href="#"
                                            wire:click="innerFeatureRedirect('modify_challan', {{ $columnName->id }})"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                    </li>
                                @endif
                                {{-- @endif --}}
                            </ul>
                        @endif
                    </div>
                </td>




            </tr>
            <div id="timeline-modal-challan-sfp-{{ $columnName->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-1.5 px-3 rounded-t dark:border-gray-600">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="timeline-modal-challan-sfp-{{ $columnName->id }}">
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
            </div>
        @endforeach
    @endif
@endif

@if ($this->persistedTemplate == 'received_challan')
    @php $i=1; @endphp
    @foreach ($tableTdData as $key => $columnName)
        @php
            $columnName = (object) $columnName;
            $challanId = $columnName->id;

        @endphp
        @if (isset($columnName->statuses) && count($columnName->statuses) > 0)
            @php
                $latestStatus = (object) $columnName->statuses[0];
                $columnName->receiver_user = (object) $columnName->receiver_user;
            @endphp

            {{-- @if ($latestStatus->status == 'sent') --}}
            @if (in_array($latestStatus->status, ['sent', 'accept', 'modified', 'reject','self_accept','self_return','partially_self_return']))
                <tr
                    class="@if ($key % 2 == 0) bg-[#e9e6e6] dark:bg-gray-800 @endif dark:border-gray-700 dark:bg-gray-800 whitespace-nowrap ">

                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $i }}
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->challan_series }}-{{ $columnName->series_num }}
                    </td>
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                        @php

                        $columnName->statuses[0] = (object) $columnName->statuses[0];
                    @endphp
                        {{ $columnName->statuses[0]->status == 'self_return' || $columnName->statuses[0]->status == 'partially_self_return' ? 'SELF(' . $columnName->sender . ')' : $columnName->sender }}
                    </td>
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date(' h:i A', strtotime($columnName->created_at)) }}</td>
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('d-m-y', strtotime($columnName->created_at)) }}</td>
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->total_qty }}</td>
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->total }}</td>
                    {{-- {{dd($columnName)}} --}}
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
                    {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                        <a href="javascript:void(0);" onclick="my_modal_1.showModal()"
                            wire:click.prevent="updateVariable('challan_sfp', {{ json_encode($columnName->sfp) }})"

                            class="block px-4 py-1 @if (count($columnName->sfp ?? []) > 0) text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">Check</a>
                    </td> --}}
                    <td class="border-2 border-gray-300">
                        <a  data-modal-target="sfp-return-challan-{{ json_encode($columnName->sfp) }}"
                            data-modal-toggle="sfp-return-challan-{{ json_encode($columnName->sfp) }}"
                            class="block px-4
                            {{-- @foreach ($columnName->sfp as $sfp)
                            @if($sfp->sfp_by_id == Auth::user()->id) bg-red-600 @else bg-green-500 @endif
                            @endforeach --}}

                            @if (count($columnName->sfp) > 0) text-white
                        @foreach ($columnName->sfp as $sfp)
                            @if ($columnName->statuses[0]->status == 'accept' || $columnName->statuses[0]->status == 'reject')
                            bg-green-600
                            @else
                                @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                bg-red-600
                                @else
                                bg-red-600
                                @endif
                            @endif
                        @endforeach

                             hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 border-2 border-gray-300 px-2 .5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">@if (count($columnName->sfp) > 0)
                             @if ($columnName->statuses[0]->status == 'accept' || $columnName->statuses[0]->status == 'reject')
                            Done
                            @else
                                @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                    Processing
                                @else
                                    Check
                                @endif
                            @endif

                            @else
                        @endif
                    </td>
                    {{-- SFP MODAL --}}
                    <div id="sfp-return-challan-{{ json_encode($columnName->sfp) }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <!-- Modal header -->
                                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SFP Process</h3>
                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="sfp-return-challan-{{ json_encode($columnName->sfp) }}">
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
                                                    <span class="text-xs text-black"> {{ ucfirst($sfp->status) }} By {{ ucfirst($sfp->sfp_by_name) }} @if(!empty($sfp->sfp_to_name))   to {{ ucfirst($sfp->sfp_to_name) }} @endif  </span>
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
                    </div>
                    <td class="px-2 text-[0.6rem] border-2 border-gray-300 whitespace-normal "
                data-modal-target="timeline-modal-received_challan-{{ $columnName->id }}"
                data-modal-toggle="timeline-modal-received_challan-{{ $columnName->id }}"
                >
                @php
                    $statusComments = json_decode($columnName->status_comment, true);
                    $firstComment = (is_array($statusComments) && isset($statusComments[0])) ? $statusComments[0] : null;
                    // dump($firstComment);
                @endphp
                     <h3 class="flex items-start mb-1 text-xs text-gray-900 dark:text-white cursor-pointer">{{ isset($firstComment['comment']) ? Str::limit(ucfirst($firstComment['comment']), 4, '...') : '' }}</h3>

                </td>
                <div id="timeline-modal-received_challan-{{ $columnName->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-1.5 px-3 rounded-t dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="timeline-modal-received_challan-{{ $columnName->id }}">
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
                                                <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                                    {{ $loop->iteration }}
                                                </span>
                                                <h3 class="flex items-start mb-1 text-sm font-semibold text-gray-900 dark:text-white">{{ isset($statusComment['comment']) ? (ucfirst($statusComment['comment']) ) : '' }}</h3>
                                                <time class="block mb-3 text-xs font-normal leading-none text-gray-500 dark:text-gray-400">
                                                    {{ isset($statusComment['name']) ? (ucfirst($statusComment['name']) ) : '' }}
                                                    {{ isset($statusComment['date']) ? (date('d-m-y', strtotime($statusComment['date'])) ) : '' }}
                                                    {{ isset($statusComment['time']) ? (ucfirst($statusComment['time']) ) : '' }}
                                                </time>
                                            </li>
                                        @endforeach
                                    @endif
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        data-modal-target="sender-sent-challan"
                                        wire:click="updateVariable('challan_id', {{ $columnName->id }})"
                                        data-modal-toggle="sender-sent-challan"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">SFP</a>
                                </li>
                                @endif
                                @endif
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->sender->view_challan == 1)
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
                                    <a data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                    onclick="setModalData({{ $columnName->id }}, 'addComment')"
                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Add Comment</a>
                                </li>
                                @if (isset($columnName->statuses) && count($columnName->statuses) > 0)
                                    @php
                                        $latestStatus = (object) $latestStatus;
                                    @endphp

                                    {{-- @if (in_array($latestStatus->status, ['sent', 'draft', 'accept', 'modified', 'reject'])) --}}
                                    @if (in_array($latestStatus->status, ['sent']))

                                        {{-- @php
                                            $mainUser = json_encode($mainUser);
                                            // dd($mainUser);
                                        @endphp --}}
                                        {{-- @if (!is_null($mainUser) && property_exists($mainUser, 'team_user')) --}}
                                        @if ($mainUser->team_user != null)
                                            @if ($mainUser->team_user->permissions->permission->sender->accept_challan == 1)
                                                <li>
                                                    <a href="javascript:void(0)"
                                                    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                    onclick="setModalData({{ $columnName->id }}, 'accept')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>

                                                </li>
                                            @endif
                                        @else
                                            <li>
                                                <a href="javascript:void(0)"
                                                data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                onclick="setModalData({{ $columnName->id }}, 'accept')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>

                                            </li>
                                        @endif

                                        @if ($mainUser->team_user != null)
                                            @if ($mainUser->team_user->permissions->permission->sender->reject_challan == 1)
                                                <li>
                                                    <a href="javascript:void(0)"
                                                    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                    onclick="setModalData({{ $columnName->id }}, 'reject')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                                </li>
                                            @endif
                                        @else
                                            <li>
                                                <a href="javascript:void(0)"
                                                data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                onclick="setModalData({{ $columnName->id }}, 'reject')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                            </li>
                                        @endif
                                        {{-- @endif --}}
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @php ++$i; @endphp
            @endif
        @endif
    @endforeach
    <div id="sender-sent-challan" tabindex="-1"
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore    >
    <div class="relative w-full max-w-lg max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex mt-20 justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class=" font-medium text-gray-900 dark:text-white">
                    Send For Processing

                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-[0.6rem] border-2 border-gray-300 w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="sender-sent-challan">
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
                    <button  type="button" wire:click='sfpReturnChallan'
                        class="text-white btn btn-sm mt-6 btn-circle btn-ghost bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs border-2 border-gray-300 px-10 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif


@if ($this->persistedTemplate == 'detailed_received_challan' && !is_null($this->tableTdData))
    @foreach ($this->tableTdData as $key => $columnName)
        {{-- @dump($columnName) --}}
        @php
        $columnName = (object) $columnName;
            // $columnName->statuses[0] = (object) $columnName->statuses[0];
            $mainUser = json_decode($this->mainUser);
            $panel = Session::get('panel');
                                $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
            // dd($columnName);
        @endphp
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                {{ ++$key }}</div>
            </td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>


            @foreach ($columnName->order_details as $keys => $details)
            @php
            $details = (object) $details;
            @endphp

                @foreach ($details->columns as $index => $column)
                @php
                $column = (object) $column;
                @endphp
                    @if ($index < 3)
                        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $column->column_value }}</td>
                    @endif
                @endforeach


                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->unit }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->qty }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->rate }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->total_amount }}</td>
        <tr>
            @if(count($columnName->order_details) > 1 && $keys < count($columnName->order_details) - 1)
                <td></td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>
                @endif

            @endforeach

        </tr>
    </tr>
    @endforeach
@endif

@if ($this->persistedTemplate == 'check_balance')
    @php
    $i=0;
    $totalBalance = 0; // Initialize the total balance
    @endphp

    @foreach ($tableTdData as $key => $columnName)
        @php
            $checkBalanceIsRemaning = 0 - 0;
        @endphp
        {{-- @if(in_array($columnName->{'Challan Status'}, ['accept', 'self_accept', 'self_return', 'partially_self_return'])) --}}
            <tr class="all-challans {{ $i % 2 != 0 ? 'bg-white' : 'bg-white' }} border-r px-2 whitespace-nowrap border text-black"
                data-Receiver="@if(is_object($columnName) && is_array($columnName->Receiver))
                                {{ implode(', ', $columnName->Receiver) }}
                            @elseif(is_object($columnName) && is_object($columnName->Receiver))
                                {{ $columnName->Receiver }} <!-- replace 'property' with the actual property name -->
                            @elseif(is_object($columnName))
                                {{ $columnName->Receiver }}
                            @endif">

                <td class="border-r px-2">{{ ++$key }}</td>
                <td class="border-r px-2">
                    @if(is_object($columnName) && is_array($columnName->Receiver))
                    {{ implode(', ', $columnName->Receiver) }}
                @elseif(is_object($columnName) && is_object($columnName->Receiver))
                    {{ $columnName->Receiver }} <!-- replace 'property' with the actual property name -->
                @elseif(is_object($columnName))
                    {{ $columnName->Receiver }}
                @endif

                </td>
                <td class="border-r px-2">{{ date('d-m-Y', strtotime($columnName->{'Sent Date'})) ?? '' }}</td>
                <td class="border-r px-2">{{ $columnName->{'Challan No.'} }}</td>
                <td class="border-r px-2">{{ $columnName->Article }}</td>
                <td class="border-r px-2">{{ $columnName->{'QTY Sent'} }}</td>
                <td class="border-r px-2">
                    @if($columnName->{'Challan Status'} == 'accept')
                        Accepted
                    @elseif($columnName->{'Challan Status'} == 'self_accept')
                        Self Accepted
                    @elseif($columnName->{'Challan Status'} == 'self_return')
                        Self Returned
                    @elseif($columnName->{'Challan Status'} == 'partially_self_return')
                        Partially Self Returned
                        @elseif($columnName->{'Challan Status'} == 'sent')
                        Sent
                        @elseif($columnName->{'Challan Status'} == 'draft')
                        Not Sent
                    @endif
                </td>

                {{-- @if(property_exists($columnName, 'Return Challan Status') && $columnName->{'Return Challan Status'} != 'draft' && $columnName->{'Return Challan Status'} != 'reject' ) --}}
                    <td class="border-r px-2">{{ $columnName->{'Recvd Challan No.'} ??  ''}}</td>
                    <td class="border-r px-2">{{ isset($columnName->{'Recvd Date'}) ? date('d-m-Y', strtotime($columnName->{'Recvd Date'})) :  '' }}</td>
                    <td class="border-r px-2">{{ $columnName->RecvArticle ??  '' }}</td>
                    <td class="border-r px-2">{{ $columnName->{'Recvd QTY'} ??  '' }}</td>
                    <td class="border-r px-2">
                        @if(isset($columnName->{'Return Challan Status'}))
                            @if($columnName->{'Return Challan Status'} == 'accept')
                                Accepted
                            @elseif($columnName->{'Return Challan Status'} == 'self_accept')
                                Self Accepted
                            @elseif($columnName->{'Return Challan Status'} == 'self_return')
                                Self Returned
                            @elseif($columnName->{'Return Challan Status'} == 'partially_self_return')
                                Partially Self Returned
                                @elseif($columnName->{'Return Challan Status'} == 'sent')
                                Not Accepted
                                @elseif($columnName->{'Return Challan Status'} == 'reject')
                                Rejected
                            @endif
                        @else

                        @endif
                    </td>
                    <td class="border-r">{{ $columnName->Balance ??  '' }}</td>
                    <td class="border-r px-2">{{ $columnName->{'Margin QTY'} ??  '' }}</td>

                    <td class="border-r px-2">
                        @if(isset($columnName->{'Balance'}) && $columnName->{'Balance'} != 0)
                            <a href="javascript:void(0)" onclick="confirmAcceptMargin({{ $columnName->{'Challan Id'} ?? null }})"
                                class="block px-4 py-1 text-white rounded-lg bg-gray-700 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept Margin</a>
                        @endif
                    </td>
                    @php
                        if (isset($columnName->Balance) && is_numeric($columnName->Balance)) {
                            $totalBalance += $columnName->Balance; // Add the balance to the total
                        }
                    @endphp
                {{-- @endif --}}
            </tr>
        {{-- @endif --}}
        @php ++$i; @endphp
    @endforeach
    <tr>
        <td colspan="12" class="text-right">Total</td>
        <td class="border-r">{{ $totalBalance }}</td>
        <td colspan="2"></td>
    </tr>

@endif



@if ($this->persistedTemplate == 'received_return_challan')
    @php $i=1; @endphp
    @foreach ($tableTdData as $key => $columnName)
    {{-- @dd($columnName); --}}
        @php
            $columnName = (object) $columnName;

        @endphp
        @if (isset($columnName->statuses) && count($columnName->statuses) > 0)
            @php
                $latestStatus = (object) $columnName->statuses[0];
            @endphp
            {{-- @if ($latestStatus->status == 'sent') --}}
            @if (in_array($latestStatus->status, ['sent', 'accept', 'modified', 'reject','self_accept']))
                <tr
                    class="@if ($key % 2 == 0) bg-[#e9e6e6] dark:bg-gray-800 @endif dark:border-gray-700 dark:bg-gray-800 whitespace-nowrap ">

                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $i }}</td>

                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->challan_series }}-{{ $columnName->series_num }}
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date(' h:i A', strtotime($columnName->created_at)) }}</td>
                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
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


                    <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                        <a data-modal-target="sfp_rReceived_challan-{{ json_encode($columnName->sfp) }}"
                            data-modal-toggle="sfp_rReceived_challan-{{ json_encode($columnName->sfp) }}"

                            class="block px-4 py-1

                            @if (count($columnName->sfp) > 0) text-white
                    @foreach ($columnName->sfp as $sfp)
                        @if ($columnName->statuses[0]->status == 'accept' || $columnName->statuses[0]->status == 'reject')
                        bg-green-600
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                            bg-red-600
                            @else
                            bg-red-600
                            @endif
                        @endif
                    @endforeach

                         hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 border-2 border-gray-300 px-2 .5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">@if (count($columnName->sfp) > 0)
                        @if ($columnName->statuses[0]->status == 'accept' || $columnName->statuses[0]->status == 'reject')
                        Done
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                Processing
                            @else
                                Check
                            @endif
                        @endif

                        @else
                    @endif
                    </td>
                    {{-- SFP RECEIVER SENT RETURN CHALLAN --}}
                    <div id="sfp_rReceived_challan-{{ json_encode($columnName->sfp) }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
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
                    </div>
                    {{-- @dump(json_decode($columnName->status_comment)) --}}
                <td class="px-2 text-[0.6rem] border-2 border-gray-300 whitespace-normal "
                data-modal-target="timeline-modal-received_return_challan-{{ $columnName->id }}"
                data-modal-toggle="timeline-modal-received_return_challan-{{ $columnName->id }}"
                >
                @php
                    $statusComments = json_decode($columnName->status_comment, true);
                    $firstComment = (is_array($statusComments) && isset($statusComments[0])) ? $statusComments[0] : null;
                    // dump($firstComment);
                @endphp
                     <h3 class="flex items-start mb-1 text-xs text-gray-900 dark:text-white cursor-pointer">{{ isset($firstComment['comment']) ? Str::limit(ucfirst($firstComment['comment']), 4, '...') : '' }}</h3>

                </td>
                <div id="timeline-modal-received_return_challan-{{ $columnName->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-1.5 px-3 rounded-t dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="timeline-modal-received_return_challan-{{ $columnName->id }}">
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
                                                <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                                    {{ $loop->iteration }}
                                                </span>
                                                <h3 class="flex items-start mb-1 text-sm font-semibold text-gray-900 dark:text-white">{{ isset($statusComment['comment']) ? (ucfirst($statusComment['comment']) ) : '' }}</h3>
                                                <time class="block mb-3 text-xs font-normal leading-none text-gray-500 dark:text-gray-400">
                                                    {{ isset($statusComment['name']) ? (ucfirst($statusComment['name']) ) : '' }}
                                                    {{ isset($statusComment['date']) ? (date('d-m-y', strtotime($statusComment['date'])) ) : '' }}
                                                    {{ isset($statusComment['time']) ? (ucfirst($statusComment['time']) ) : '' }}
                                                </time>
                                            </li>
                                        @endforeach
                                    @endif
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
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
                                            wire:click="updateVariable('challan_id', {{ $columnName->id }})"
                                            data-modal-target="receiver-received-challan" data-modal-toggle="receiver-received-challan"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">SFP</a>
                                    </li>
                                    @endif
                                @endif
                                {{-- <button data-modal-target="default-modal" data-modal-toggle="default-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                    Toggle modal
                                  </button> --}}
                                {{-- <li>
                                    <a target="_blank"
                                        href="{{ Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5)) }}"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                                </li> --}}
                                @if ($mainUser->team_user != null)
                                @if ($mainUser->team_user->permissions->permission->sender->view_challan == 1)
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
                                <a data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                onclick="setModalData({{ $columnName->id }}, 'addComment')"
                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Add Comment</a>
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
                                        @if ($mainUser->team_user != null)
                                            @if ($mainUser->team_user->permissions->permission->receiver->accept_challan == 1)
                                                <li>
                                                    <a href="javascript:void(0)"
                                                    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                    onclick="setModalData({{ $columnName->id }}, 'accept')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>

                                                </li>
                                            @endif
                                        @else
                                            <li>
                                                <a href="javascript:void(0)"
                                                data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                onclick="setModalData({{ $columnName->id }}, 'accept')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>

                                            </li>
                                        @endif

                                        @if ($mainUser->team_user != null)
                                            @if ($mainUser->team_user->permissions->permission->sender->reject_challan == 1)
                                                <li>
                                                    <a href="javascript:void(0)"
                                                    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                    onclick="setModalData({{ $columnName->id }}, 'reject')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                                </li>
                                            @endif
                                        @else
                                            <li>
                                                <a href="javascript:void(0)"
                                                data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                onclick="setModalData({{ $columnName->id }}, 'reject')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                            </li>
                                        @endif
                                    @endif
                                @endif
                            </ul>


                        </div>

                    </td>

                </tr>
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
     {{-- SENT RETURN CHALLAN  --}}
@endif

@if ($this->persistedTemplate == 'detailed_received_return_challan' && !is_null($this->tableTdData))
    @foreach ($this->tableTdData as $key => $columnName)
        {{-- @dump($columnName) --}}
        @php
        $columnName = (object) $columnName;
            // $columnName->statuses[0] = (object) $columnName->statuses[0];
            $mainUser = json_decode($this->mainUser);
            $panel = Session::get('panel');
                                $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
            // dd($columnName);
        @endphp
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                {{ ++$key }}</div>
            </td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>


            @foreach ($columnName->order_details as $keys => $details)
            @php
            $details = (object) $details;
            @endphp

                @foreach ($details->columns as $index => $column)
                @php
                $column = (object) $column;
                @endphp
                    @if ($index < 3)
                        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $column->column_value }}</td>
                    @endif
                @endforeach


                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->unit }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->qty }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->rate }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->total_amount }}</td>
        <tr>
            @if(count($columnName->order_details) > 1 && $keys < count($columnName->order_details) - 1)
                <td></td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>
                @endif

            @endforeach

        </tr>
    </tr>
    @endforeach
@endif

@if ($this->persistedTemplate == 'detailed_sent_challan' && !is_null($this->tableTdData))
    @foreach ($this->tableTdData as $key => $columnName)
        {{-- @dump($columnName) --}}
        @php
        $columnName = (object) $columnName;
            // $columnName->statuses[0] = (object) $columnName->statuses[0];
            $mainUser = json_decode($this->mainUser);
            $panel = Session::get('panel');
                                $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
            // dd($columnName);
        @endphp
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                {{ ++$key }}</div>
            </td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>


            @foreach ($columnName->order_details as $keys => $details)
            @php
            $details = (object) $details;
            @endphp

                @foreach ($details->columns as $index => $column)
                @php
                $column = (object) $column;
                @endphp
                    @if ($index < 3)
                        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $column->column_value }}</td>
                    @endif
                @endforeach


                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->unit }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->qty }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->rate }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->total_amount }}</td>
        <tr>
            @if(count($columnName->order_details) > 1 && $keys < count($columnName->order_details) - 1)
                <td></td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>
                @endif

            @endforeach

        </tr>
    </tr>
    @endforeach
@endif

@if ($this->persistedTemplate == 'deleted_sent_challan' && !is_null($this->invoiceData))
    @foreach ($this->invoiceData as $key => $columnName)
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap">

            <th scope="row"
                class="flex items-center whitespace-nowrap px-2 py-1 text-[0.6rem] border-2 border-gray-300 text-gray-900 dark:text-white">
                <div class="pl-0">
                    <div class="text-[0.6rem]  font-xs">{{ ++$key }}</div>

                </div>
            </th>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->invoice_series }}-{{ $columnName->series_num }}</td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->buyer }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j/m/Y', strtotime($columnName->created_at)) }}</td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->seller }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->qty }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->total_amount }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
            <!-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->unit }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->rate }}</td> -->
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300 whitespace-normal">
                @if (isset($columnName->comment))
                    {{ $columnName->comment }}
                @endif
            </td>
            <td>
                <button id="dropdownDefaultButton-{{ $key }}"
                    data-dropdown-toggle="dropdown-{{ $key }}"
                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-5 py-1 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
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
                            <li>

                                <a href="#"
                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                            </li>
                            @if ($columnName->statuses[0]->status == 'draft')
                                <li>
                                    <a href="#"
                                        wire:click="innerFeatureRedirect('modify_sent_invoice', {{ $columnName->id }})"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>

                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'draft')
                                <li>
                                    <a href="#" wire:click="sendInvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'modified')
                                <li>
                                    <a href="#" wire:click="reSendinvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'sent')
                                <li>
                                    <a href="#" wire:click="reSendInvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                </li>
                                <li>
                                    <a href="#" wire:click="selfAcceptInvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                        Delivery</a>
                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'reject')
                                @if ($columnName->statuses[0]->status == 'draft' || $columnName->statuses[0]->status == 'reject')
                                    <li>
                                        <a href="#" wire:click="deleteInvoice('{{ $columnName->id }}')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    @endif

                </div>
            </td>
        </tr>
    @endforeach
@endif

@if ($this->persistedTemplate == 'sent_return_challan')
    @foreach ($tableTdData as $key => $columnName)
        @php
            $columnName = (object) $columnName;
        @endphp
        <tr
            class="@if ($key % 2 == 0) bg-[#e9e6e6] @else bg-white @endif dark:border-gray-700 text-black whitespace-nowrap">

            <td class="px-2  text-[0.6rem] border-2 border-gray-300"> {{ ++$key }}</td>


            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ date(' h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->sender }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->receiver }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->total_qty }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->total }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->receiver_details->state ?? null }}</td>
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
            {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td> --}}
            <td class="border-2 border-gray-300">
                <a  data-modal-target="sfp-rSent_challan-{{ json_encode($columnName->sfp) }}"
                    data-modal-toggle="sfp-rSent_challan-{{ json_encode($columnName->sfp) }}"

                    class="block px-4

                    @if (count($columnName->sfp) > 0) text-white
                    @foreach ($columnName->sfp as $sfp)
                        @if ($columnName->statuses[0]->status == 'sent')
                        bg-green-600
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                            bg-red-600
                            @else
                            bg-red-600
                            @endif
                        @endif
                    @endforeach

                         hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 border-2 border-gray-300 px-2 .5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">@if (count($columnName->sfp) > 0)
                        @if ($columnName->statuses[0]->status == 'sent')
                        Done
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                Processing
                            @else
                                Check
                            @endif
                        @endif

                        @else
                    @endif
                </a>
            </td>
            <div id="sfp-rSent_challan-{{ json_encode($columnName->sfp) }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SFP Process</h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="sfp-rSent_challan-{{ json_encode($columnName->sfp) }}">
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
                                            <span class="text-xs text-black"> {{ ucfirst($sfp->status) }} By {{ ucfirst($sfp->sfp_by_name) }} @if(!empty($sfp->sfp_to_name))   to {{ ucfirst($sfp->sfp_to_name) }} @endif  </span>
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
            </div>

            <td class="px-2 text-[0.6rem] border-2 border-gray-300 whitespace-normal "
                    data-modal-target="timeline-modal-sent-return-challan-{{ $columnName->id }}"
                    data-modal-toggle="timeline-modal-sent-return-challan-{{ $columnName->id }}"
                    >
                    @php
                        $statusComments = json_decode($columnName->status_comment, true);
                        $firstComment = (is_array($statusComments) && isset($statusComments[0])) ? $statusComments[0] : null;
                    @endphp
                    @if($firstComment !== null && isset($firstComment['comment']) && isset($firstComment['name']) && $firstComment['date'] && $firstComment['time'] && !empty(trim($firstComment['comment'])))
                        <h3 class="flex items-start mb-1 text-xs text-gray-900 dark:text-white cursor-pointer">{{ Str::limit(ucfirst($firstComment['comment']), 4, '...') }}</h3>
                    @endif
                </td>


                <div id="timeline-modal-sent-return-challan-{{ $columnName->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-1.5 px-3 rounded-t dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="timeline-modal-sent-return-challan-{{ $columnName->id }}">
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
                </div>

            <td class="px-2  text-[0.6rem] border-2 border-gray-300">
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

                                @if((isset($columnName->statuses[0]->status) && $columnName->statuses[0]->status !== 'sent'))
                                @if($teamMembers != null)
                                <li>
                                    <a href="javascript:void(0);" data-modal-target="sent-return-challan"
                                        wire:click="updateVariable('challan_id', {{ $columnName->id }})"
                                        data-modal-toggle="sent-return-challan"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">SFP</a>
                                </li>
                                @endif

                            @endif
                            {{-- @if ($columnName->statuses[0]->status != 'draft') --}}
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->receiver->view_return_challan == 1)
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
                                    <a data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                    onclick="setModalData({{ $columnName->id }}, 'addComment')"
                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Add Comment</a>
                                </li>
                            {{-- @if ($columnName->statuses[0]->status == 'draft') --}}

                            @if ($mainUser->team_user != null)
                                {{-- @if ($mainUser->team_user->permissions->permission->receiver->send_challan == 1) --}}
                                    <li>
                                        <a href="#"
                                        data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                        onclick="setModalData({{ $columnName->id }})"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                    </li>
                                {{-- @endif --}}
                            @else
                                <li>
                                    <a href="#"
                                    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                    onclick="setModalData({{ $columnName->id }})"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                </li>
                            @endif
                            {{-- @endif --}}
                            @if ($columnName->statuses[0]->status == 'modified')
                                @if ($mainUser->team_user != null)
                                    {{-- @if ($mainUser->team_user->permissions->permission->sender->send_challan == 1) --}}
                                        <li>
                                            <a href="#" wire:click="reSendChallan('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                        </li>
                                    {{-- @endif --}}
                                @else
                                    <li>
                                        <a href="#" wire:click="reSendChallan('{{ $columnName->id }}')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                    </li>
                                @endif
                            @endif
                            {{-- @if ($columnName->statuses[0]->status == 'sent')
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->sender->accept_challan == 1)
                                        <li>
                                            <a href="#"
                                                wire:click="selfAcceptChallan('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                Delivery</a>
                                        </li>
                                    @endif
                                @else
                                    <li>
                                        <a href="#" wire:click="selfAcceptChallan('{{ $columnName->id }}')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                            Delivery</a>
                                    </li>
                                @endif
                            @endif --}}
                            {{-- @if ($columnName->statuses[0]->status == 'self_accept')
                                @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->sender->self_return_challan == 1)
                                        <li>
                                            <a href="#"
                                                wire:click="innerFeatureRedirect('self_return_challan' {{ $columnName->id }})"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                Return</a>
                                        </li>
                                    @endif
                                @else
                                    <li>
                                        <a href="#"
                                            wire:click="innerFeatureRedirect('self_return_challan', {{ $columnName->id }})"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                            Return</a>
                                    </li>
                                @endif
                            @endif --}}
                            @if ($columnName->statuses[0]->status == 'reject')
                            @if ($mainUser->team_user != null)
                                {{-- @if ($mainUser->team_user->permissions->permission->receiver->modify_return_challan == 1) --}}
                                    <li>
                                        <a href="#"
                                            wire:click="innerFeatureRedirect('modify_return_challan' {{ $columnName->id }})"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                    </li>
                                {{-- @endif --}}
                            @else
                                <li>
                                    <a href="#"
                                        wire:click="innerFeatureRedirect('modify_return_challan', {{ $columnName->id }})"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                </li>
                            @endif
                            @endif
                        </ul>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    {{-- SFP SENT RETURN CHALLAN MODAL --}}
    <div id="sent-return-challan" tabindex="-1"
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
                    data-modal-hide="sent-return-challan">
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
                    <button  type="button" wire:click='sfpReturnChallan'
                        class="text-white btn btn-sm mt-6 btn-circle btn-ghost bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs border-2 border-gray-300 px-10 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
                </div>
            </div>
    </div>
    </div>
    {{-- SENT RETURN CHALLAN  --}}
@endif



@if ($this->persistedTemplate == 'detailed_sent_return_challan' && !is_null($this->tableTdData))
@foreach ($this->tableTdData as $key => $columnName)
{{-- @dump($columnName) --}}
@php
$columnName = (object) $columnName;
    // $columnName->statuses[0] = (object) $columnName->statuses[0];
    $mainUser = json_decode($this->mainUser);
@endphp
<tr
    class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
        {{ ++$key }}</div>
    </td>
    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>


    @foreach ($columnName->order_details as $keys => $details)
            @php
            $details = (object) $details;
            @endphp
        @foreach ($details->columns as $index => $column)
                @php
                $column = (object) $column;
                @endphp
            @if ($index < 3)
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $column->column_value }}</td>
            @endif
        @endforeach


        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->unit }}</td>
        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->qty }}</td>
        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->rate }}</td>
        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->total_amount }}</td>
    <tr>
        @if(count($columnName->order_details) > 1 && $keys < count($columnName->order_details) - 1)
            <td></td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>
            @endif

        @endforeach

    </tr>
</tr>
@endforeach
@endif




@if ($this->persistedTemplate == 'detailed_sent_invoice' && !is_null($this->tableTdData))
@foreach ($this->tableTdData as $key => $columnName)
{{-- @dump($columnName) --}}
<tr
    class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

    <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
        {{ ++$key }}</div>
    </td>
    <td class="px-2 whitespace-nowrap  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->invoice_series }}-{{ $columnName->series_num }}</td>
    <td class="px-2 whitespace-nowrap  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->buyer }}</td>
    <td class="px-2 whitespace-nowrap  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
    <td class="px-2 whitespace-nowrap  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
    <td class="px-2 whitespace-nowrap  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->seller }}</td>


    @foreach ($columnName->order_details as $keys => $details)
        @foreach ($details->columns as $index => $column)
            @if ($index < 3)
                <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $column->column_value }}</td>
            @endif
        @endforeach


        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $details->unit }}</td>
        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $details->qty }}</td>
        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $details->rate }}</td>
        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $details->tax }}</td>
        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $details->total_amount }}</td>
<tr>
    @if(count($columnName->order_details) > 1 && $keys < count($columnName->order_details) - 1)
        <td></td>
        <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->invoice_series }}-{{ $columnName->series_num }}</td>
        <td class="px-2 whitespace-nowrap  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->buyer }}</td>
        <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
        <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
        <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->seller }}</td>
        @endif

    @endforeach

</tr>
</tr>
@endforeach
@endif

@if ($this->persistedTemplate == 'deleted_sent_invoice' && !is_null($this->invoiceData))
    @foreach ($this->invoiceData as $key => $columnName)
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap">

            <th scope="row"
                class="flex items-center whitespace-nowrap px-2 py-1 text-[0.6rem] border-2 border-gray-300 text-gray-900 dark:text-white">
                <div class="pl-0">
                    <div class="text-[0.6rem]  font-xs">{{ ++$key }}</div>

                </div>
            </th>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->invoice_series }}-{{ $columnName->series_num }}</td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->buyer }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j/m/Y', strtotime($columnName->created_at)) }}</td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->seller }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->qty }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->total_amount }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
            <!-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->unit }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->rate }}</td> -->
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300 whitespace-normal">
                @if (isset($columnName->comment))
                    {{ $columnName->comment }}
                @endif
            </td>
            <td>
                <button id="dropdownDefaultButton-{{ $key }}"
                    data-dropdown-toggle="dropdown-{{ $key }}"
                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-5 py-1 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
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
                            <li>
                                <a href="#"
                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                            </li>
                            @if ($columnName->statuses[0]->status == 'draft')
                                <li>
                                    <a href="#"
                                        wire:click="innerFeatureRedirect('modify_sent_invoice', {{ $columnName->id }})"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>

                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'draft')
                                <li>
                                    <a href="#" wire:click="sendInvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'modified')
                                <li>
                                    <a href="#" wire:click="reSendinvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'sent')
                                <li>
                                    <a href="#" wire:click="reSendInvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                </li>
                                <li>
                                    <a href="#" wire:click="selfAcceptInvoice('{{ $columnName->id }}')"
                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                        Delivery</a>
                                </li>
                            @endif
                            @if ($columnName->statuses[0]->status == 'reject')
                                @if ($columnName->statuses[0]->status == 'draft' || $columnName->statuses[0]->status == 'reject')
                                    <li>
                                        <a href="#" wire:click="deleteInvoice('{{ $columnName->id }}')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    @endif

                </div>
            </td>
        </tr>
    @endforeach
@endif

@if ($this->persistedTemplate == 'all_invoice')
    {{-- @php $i=1; @endphp --}}
    {{-- @dd($tableTdData); --}}
    @php $i=1; @endphp
    @foreach ($tableTdData as $key => $columnName)
        @php
            $columnName = (object) $columnName;
            if (isset($columnName->statuses[0])) {
                $columnName->statuses[0] = (object) $columnName->statuses[0];
            }
        @endphp
        @if (isset($columnName->statuses) && count($columnName->statuses) > 0)
            @php
                $latestStatus = (object) $columnName->statuses[0];
            @endphp

            @if (in_array($latestStatus->status, ['sent', 'draft', 'accept', 'modified', 'reject']))
            @if (isset($columnName->pdf_url))
                <tr ondblclick="window.open('https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}', '_blank')"
                    class="cursor-pointer whitespace-nowrap @if ($key % 2 == 0) bg-[#e9e6e6] dark:bg-gray-800 @endif dark:border-gray-700 dark:bg-gray-800 ">


                    <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $i }}</td>
                    <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $columnName->invoice_series }}-{{ $columnName->series_num }}
                        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ date(' h:i A', strtotime($columnName->created_at)) }}</td>
                        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                        <td class= "whitespace-nowrap px-2  text-[0.6rem] border-2 border-gray-300">{{ $columnName->buyer }}</td>
                        <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $columnName->seller }}</td>
                     <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $columnName->total }}</td>
                    <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">{{ $columnName->buyer_details->state ?? null }}</td>
                    <td class="px-2 whitespace-nowrap text-[0.6rem] border-2 border-gray-300">
                        @if (isset($columnName->statuses[0]))
                            {{ $columnName->statuses[0]->status }} By {{ $columnName->statuses[0]->user_name }} <br>

                            <p class="text-[0.6rem] ">
                                {{ date('j F Y, h:i A', strtotime($columnName->statuses[0]->created_at)) }}</p>
                        @endif
                    </td>

                    <td class="px-2  text-[0.6rem] border-2 border-gray-300">
                        <a href="javascript:void(0);" onclick="my_modal_1.showModal()"
                            wire:click="updateVariable('challan_sfp', {{ json_encode($columnName->sfp) }})"

                            class="block px-4  py-1

                        @if (count($columnName->sfp) > 0) text-white
                    @foreach ($columnName->sfp as $sfp)
                        @if ($columnName->statuses[0]->status == 'sent')
                        bg-green-600
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                            bg-red-600
                            @else
                            bg-red-600
                            @endif
                        @endif
                    @endforeach

                         hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 border-2 border-gray-300 px-2 .5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">@if (count($columnName->sfp) > 0)
                        @if ($columnName->statuses[0]->status == 'sent')
                        Done
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                Processing
                            @else
                                Check
                            @endif
                        @endif

                        @else
                    @endif
                    </td>
                    <td class="px-2 text-[0.6rem] border-2 border-gray-300 whitespace-normal "
                    data-modal-target="timeline-modal-all-invoice-{{ $columnName->id }}"
                    data-modal-toggle="timeline-modal-all-invoice-{{ $columnName->id }}"
                    >
                    @php
                        $statusComments = json_decode($columnName->status_comment, true);
                        $firstComment = (is_array($statusComments) && isset($statusComments[0])) ? $statusComments[0] : null;
                    @endphp
                    @if($firstComment !== null && isset($firstComment['comment']) && isset($firstComment['name']) && $firstComment['date'] && $firstComment['time'] && !empty(trim($firstComment['comment'])))
                        <h3 class="flex items-start mb-1 text-xs text-gray-900 dark:text-white cursor-pointer">{{ Str::limit(ucfirst($firstComment['comment']), 4, '...') }}</h3>
                    @endif
                </td>


                <div id="timeline-modal-all-invoice-{{ $columnName->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-2 px-3 rounded-t dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="timeline-modal-all-invoice-{{ $columnName->id }}">
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
                </div>

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
                                {{-- @if((isset($columnName->statuses[0]->status) && $columnName->statuses[0]->status !== 'sent'))
                                    @if($teamMembers != null)
                                    <li>
                                        <a href="javascript:void(0);" data-modal-target="edit-modal-return-challan"
                                            wire:click="updateVariable('invoice_id', {{ $columnName->id }})"
                                            data-modal-toggle="edit-modal-return-challan"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">SFP</a>
                                    </li>
                                    @endif
                                @endif --}}
                                @if ($mainUser->team_user != null)
                                @if ($mainUser->team_user->permissions->permission->buyer->view_invoices == 1)
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
                                <a data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                onclick="setModalData({{ $columnName->id }}, 'addComment')"
                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Add Comment</a>
                            </li>
                                @if (isset($columnName->statuses) && count($columnName->statuses) > 0)
                                    @php
                                        $latestStatus = (object) $columnName->statuses[0];
                                    @endphp

                                    @if (in_array($latestStatus->status, ['sent']))
                                        {{-- @php
                                        $mainUser = json_decode($mainUser);
                                    @endphp --}}
                                        @if ($mainUser->team_user != null)
                                            @if ($mainUser->team_user->permissions->permission->buyer->accept_invoice == 1)
                                                <li>
                                                    <a href="javascript:void(0)"
                                                    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                    onclick="setModalData({{ $columnName->id }}, 'accept')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>

                                                </li>
                                            @endif
                                        @else
                                            <li>
                                                <a href="javascript:void(0)"
                                                data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                onclick="setModalData({{ $columnName->id }}, 'accept')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>

                                            </li>
                                        @endif

                                        @if ($mainUser->team_user != null)
                                            @if ($mainUser->team_user->permissions->permission->buyer->reject_invoice == 1)
                                                <li>
                                                    <a href="javascript:void(0)"
                                                    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                    onclick="setModalData({{ $columnName->id }}, 'reject')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                                </li>
                                            @endif
                                        @else
                                            <li>
                                                <a href="javascript:void(0)"
                                                data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                                onclick="setModalData({{ $columnName->id }}, 'reject')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                            </li>
                                        @endif
                                    @endif
                                @endif


                            </ul>


                        </div>

                    </td>

                </tr>
                @endif
                @php ++$i; @endphp
            @endif
        @endif
    @endforeach
@endif

@if ($this->persistedTemplate == 'detailed_all_buyers' && !is_null($this->invoiceData))
    @foreach ($this->invoiceData as $key => $columnName)
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap">

            <th scope="row"
                class="flex items-center whitespace-nowrap px-2 py-1 text-[0.6rem] border-2 border-gray-300 text-gray-900 dark:text-white">
                <div class="pl-0">
                    <div class="text-[0.6rem]  font-xs">{{ ++$key }}</div>

                </div>
            </th>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['invoice_series'] }} </td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['seller'] }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('h:i A', strtotime($columnName['invoice_date'])) }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j/m/Y', strtotime($columnName['invoice_date'])) }}</td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['seller'] }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['column_name'] }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['column_value'] }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['unit'] }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['qty'] }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName['rate'] }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300 ">{{ $columnName['total_amount'] }}</td>



        </tr>
    @endforeach
@endif


@if ($this->persistedTemplate == 'purchase_order_seller')
    @if (count($tableTdData))
        @foreach ($tableTdData as $key => $columnName)
            @php
                $columnName = (object) $columnName;
                $mainUser = json_decode($this->mainUser);
                $panel = Session::get('panel');
                                $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
            @endphp
            @if (isset($columnName->pdf_url))
            <tr ondblclick="window.open('https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}', '_blank')"
                class="cursor-pointer whitespace-nowrap @if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    {{ ++$key }}</div>
                </td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    {{ $columnName->purchase_order_series }}-{{ $columnName->series_num }}
                </td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ ucfirst($columnName->seller_name) }}</td>

                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>

                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ ucfirst($columnName->buyer_name) }}</td> --}}
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->total }}</td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j/m/Y', strtotime($columnName->created_at)) }}</td> --}}

                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->receiver_details->state ?? null }}</td> --}}
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
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    <button id="dropdownDefaultButton-{{ $key }}"
                        data-dropdown-toggle="dropdown-{{ $key }}"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                        type="button">Select <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg></button>
                    <!-- Dropdown menu -->
                    <div id="dropdown-{{ $key }}"
                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">
                        @if (isset($columnName->statuses[0]))
                            <ul class="py-1 text-[0.6rem] border-2 border-gray-300 text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton-{{ $key }}">
                                @if ($mainUser->team_user != null)
                                @if ($mainUser->team_user->permissions->permission->sender->view_challan == 1)
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
                                @if ($columnName->statuses[0]->status == 'sent')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->modify_purchase_order == 1)
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    <a href="javascript:void(0);" onclick="my_modal_1.showModal()"
                        wire:click="updateVariable('challan_sfp', {{ json_encode($columnName->sfp) }})"

                        class="block px-4 py-1

                        @if (count($columnName->sfp) > 0) text-white
                    @foreach ($columnName->sfp as $sfp)
                        @if ($columnName->statuses[0]->status == 'sent')
                        bg-green-600
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                            bg-red-600
                            @else
                            bg-red-600
                            @endif
                        @endif
                    @endforeach

                         hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 border-2 border-gray-300 px-2 .5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 @endif dark:hover:text-white">@if (count($columnName->sfp) > 0)
                        @if ($columnName->statuses[0]->status == 'sent')
                        Done
                        @else
                            @if (isset($sfp->sfp_by_id) && $sfp->sfp_by_id == Auth::user()->id)
                                Processing
                            @else
                                Check
                            @endif
                        @endif

                        @else
                    @endif
                </td>
                <li>
                    <a href="#" wire:click="acceptPurchaseOrder('{{ $columnName->id }}')"
                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>
                </li>
                <li>
                    <a href="#" wire:click="rejectPurchaseOrder('{{ $columnName->id }}')"
                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                </li>
        @endif
    @else
        <li>
            <a href="#" wire:click="acceptPurchaseOrder('{{ $columnName->id }}')"
                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>
        </li>
        <li>
            <a href="#" wire:click="rejectPurchaseOrder('{{ $columnName->id }}')"
                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
        </li>

    @endif
@endif
{{-- @if ($columnName->statuses[0]->status == 'draft') --}}
{{-- @if ($mainUser->team_user != null)
                                    @if ($mainUser->team_user->permissions->permission->buyer->send_purchase_order == 1)
                                        <li>
                                            <a href="#"
                                                wire:click="sendPurchaseOrder('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                        </li>
                                    @endif
                                @else
                                    <li>
                                        <a href="#" wire:click="sendPurchaseOrder('{{ $columnName->id }}')"
                                            class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                    </li>
                                @endif
                                @if ($columnName->statuses[0]->status == 'modified')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->send_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="reSendPurchaseOrders('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                wire:click="reSendPurchaseOrders('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                        </li>
                                    @endif
                                @endif
                                @if ($columnName->statuses[0]->status == 'sent')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->accept_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="selfAcceptPurchaseOrders('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                    Delivery</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                wire:click="selfAcceptPurchaseOrders('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                Delivery</a>
                                        </li>
                                    @endif
                                @endif
                                @if ($columnName->statuses[0]->status == 'reject')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->modify_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
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


@endif

@if ($this->persistedTemplate == 'detailed_purchase_order' && !is_null($this->purchaseOrderBuyerData))
    {{-- @dd($this->purchaseOrderBuyerData) --}}
    @foreach ($this->purchaseOrderBuyerData as $key => $columnName)
                    @php
                        $columnName = (object) $columnName;
                    @endphp
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap">

            <th scope="row"
                class="flex items-center whitespace-nowrap px-2 py-1 text-[0.6rem] border-2 border-gray-300 text-gray-900 dark:text-white">
                <div class="pl-0">
                    <div class="text-[0.6rem]  font-xs">{{ ++$key }}</div>

                </div>
            </th>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->purchase_order_series }} </td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->seller_name }}</td>


            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j/m/Y', strtotime($columnName->created_at)) }}</td>

            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->buyer_name }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->column_name }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->column_value }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->unit }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->qty }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->rate }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->tax }}</td>
            <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->total_amount }}</td>
            {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td> --}}

        </tr>
    @endforeach
@endif
{{-- SELLER PANLE --}}

{{-- BUYER PANEL --}}
@if ($this->persistedTemplate == 'purchase_order')
    @if (count($tableTdData))
        @foreach ($tableTdData as $key => $columnName)
            @php
                $columnName = (object) $columnName;
                $mainUser = json_decode($this->mainUser);
                $panel = Session::get('panel');
                                $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
            @endphp
             @if (isset($columnName->pdf_url))
            <tr ondblclick="window.open('https://docs.google.com/viewer?url={{ urlencode(Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5))) }}', '_blank')"
                class="cursor-pointer whitespace-nowrap @if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    {{ ++$key }}</div>
                </td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    {{ $columnName->purchase_order_series }}-{{ $columnName->series_num }}
                </td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ ucfirst($columnName->buyer_name) }}</td>

                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>

                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ ucfirst($columnName->seller_name) }}
                    @if (isset($columnName->statuses[0]->team_user_name) && $columnName->statuses[0]->team_user_name != null)
                                            ({{ ucfirst($columnName->statuses[0]->team_user_name) }})
                                        @endif
                </td>
                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->qty }}</td> --}}
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->total }}</td>
                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>
                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ date('j/m/Y', strtotime($columnName->created_at)) }}</td> --}}

                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">{{ $columnName->receiver_details->state ?? null }}</td> --}}
                <td class="px-2 text-[0.6rem] border-2 border-gray-300  ">
                    @php
                        $columnName = (object) $columnName;
                    @endphp
                    <div class="flex justify-between items-center">
                        <div @if(!empty($columnName->statuses) && isset($columnName->statuses[0]) && (is_array($columnName->statuses[0]) ? $columnName->statuses[0]['status'] : $columnName->statuses[0]->status) == 'reject') bg-red-500 @endif>
                            @if (!empty($columnName->statuses) && isset($columnName->statuses[0]))    @php
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
                                                                                @php
                                                                                    $statusValue = is_array($status) ? $status['status'] : $status->status;
                                                                                @endphp
                                                                                @if($statusValue === 'draft')
                                                                                    Created
                                                                                @elseif($statusValue === 'accept')
                                                                                    Accepted
                                                                                @elseif($statusValue === 'reject')
                                                                                    Rejected
                                                                                @else
                                                                                    {{ ucfirst($statusValue) }}
                                                                                @endif
                                                                            </span>
                                                                            <time class="block mb-3 text-xs leading-none text-black">
                                                                                @php
                                                                                    $createdAt = is_array($status) ? $status['created_at'] : $status->created_at;
                                                                                @endphp
                                                                                {{ date('j F Y, h:i A', strtotime($createdAt ?? '')) }}
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


                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300"></td>

                {{-- <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                @if (isset($columnName->statuses[0]))
                    {{ $columnName->statuses[0]->comment }}
                @endif
            </td> --}}

                <td class="px-2 py-1 text-[0.6rem] border-2 border-gray-300">
                    <button id="dropdownDefaultButton-{{ $key }}"
                        data-dropdown-toggle="dropdown-{{ $key }}"
                        class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                        type="button">Select <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg></button>
                    <!-- Dropdown menu -->
                    <div id="dropdown-{{ $key }}"
                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">
                        @if (isset($columnName->statuses[0]))
                            <ul class="py-1 text-[0.6rem] border-2 border-gray-300 text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton-{{ $key }}">
                                @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->view_purchase_order == 1)
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
                                    @php
                                    $columnName->statuses[0] = (object) $columnName->statuses[0];
                                    @endphp
                                @if ($columnName->statuses[0]->status == 'draft')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->send_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="sendPurchaseOrder('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                wire:click="sendPurchaseOrder('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Send</a>
                                        </li>
                                    @endif
                                @endif
                                {{-- @if ($columnName->statuses[0]->status == 'sent')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->send_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="reSendPurchaseOrders('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                wire:click="reSendPurchaseOrders('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                        </li>
                                    @endif
                                @endif --}}
                                @if ($columnName->statuses[0]->status == 'modified')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->send_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="reSendPurchaseOrders('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                wire:click="reSendPurchaseOrders('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Resend</a>
                                        </li>
                                    @endif
                                @endif
                                @if ($columnName->statuses[0]->status == 'sent')
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->self_delivery_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="selfAcceptPurchaseOrders('{{ $columnName->id }}')"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                    Delivery</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                wire:click="selfAcceptPurchaseOrders('{{ $columnName->id }}')"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Self
                                                Delivery</a>
                                        </li>
                                    @endif
                                @endif
                                @if (!empty($columnName->statuses) && isset($columnName->statuses[0]) && ($columnName->statuses[0]->status == 'draft' || $columnName->statuses[0]->status == 'reject'))
                                    @if ($mainUser->team_user != null)
                                        @if ($mainUser->team_user->permissions->permission->buyer->modify_purchase_order == 1)
                                            <li>
                                                <a href="#"
                                                    wire:click="innerFeatureRedirect('modify_purchase_order', {{ $columnName->id }})"
                                                    class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                            </li>
                                        @endif
                                    @else
                                        <li>
                                            <a href="#"
                                                wire:click="innerFeatureRedirect('modify_purchase_order', {{ $columnName->id }})"
                                                class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white">Modify</a>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        @endif
                    </div>
                </td>

            </tr>
            @endif
        @endforeach
    @endif


@endif

@if ($this->persistedTemplate == 'detailed_purchase_order_buyer')
@foreach ($this->tableTdData as $key => $columnName)
@php
$columnName = (object) $columnName;
// dd($columnName);
// $panelName = $panel ? strtolower(str_replace('_', ' ', $panel['panel_name'])) : null;
@endphp
        <tr
            class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">
                {{ ++$key }}</div>
            </td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->purchase_order_series }}-{{ $columnName->series_num }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->seller_name }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
            <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->buyer_name }}</td>


            @foreach ($columnName->order_details as $keys => $details)
            @php
                $details = (object) $details;
                @endphp
                @foreach ($details->columns as $index => $column)
                @php
                    $column = (object) $column;
                    @endphp
                    @if ($index < 3)
                        <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $column->column_value }}</td>
                    @endif
                @endforeach


                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->unit }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->rate }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->qty }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->tax }}</td>
                <td class="px-2 text-[0.6rem] border-2 border-gray-300">{{ $details->total_amount }}</td>
        <tr>
            @if(count($columnName->order_details) > 1 && $keys < count($columnName->order_details) - 1)
                <td></td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->purchase_order_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->seller_name }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->buyer_name }}</td>
                @endif

            @endforeach

        </tr>
    </tr>
    @endforeach
@endif
{{-- BUYER PANEL --}}











<script>
    document.addEventListener('DOMContentLoaded', function() {
    const multiSelectInputs = document.querySelectorAll('.multi-select-input');
    const multiSelectDropdowns = document.querySelectorAll('.multi-select-dropdown');

    multiSelectInputs.forEach((input, index) => {
        const dropdown = multiSelectDropdowns[index];
        const options = dropdown.querySelectorAll('.multi-select-option');
        const selectedValues = [];

        input.addEventListener('click', () => {
            dropdown.classList.toggle('hidden');
        });

        options.forEach(option => {
            option.addEventListener('change', () => {
                if (option.checked) {
                    selectedValues.push({ id: option.value, name: option.dataset.name });
                } else {
                    const index = selectedValues.findIndex(item => item.id === option.value);
                    if (index !== -1) {
                        selectedValues.splice(index, 1);
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
});
</script>


{{--
<div id="edit-modal-sent-invoice" tabindex="-1"
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
                <button type="button" wire:click.prevent='resetChallanSeries()'
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-[0.6rem] border-2 border-gray-300 w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="edit-modal-sent-invoice">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <div class="relative">
                    <label for="receiver_user_id" class="block text-[0.6rem] font-medium">Choose User</label>

                    <select id="select" wire:model.defer="team_user_id"
                        class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0">
                        <option value="">Select Team User</option>

                        @if (isset($teamMembers) && is_array($teamMembers))
                            @foreach ($teamMembers as $team)
                                @php
                                    $team = isset($team) ? (object) $team : null;
                                @endphp
                                @if ($team !== null)
                                    <option value="{{ $team->id }}">{{ $team->team_user_name }}</option>
                                @endif
                            @endforeach
                        @endif


                    </select>
                </div>

                <div>
                    <label for="company_name" class="block text-[0.6rem] font-medium">Comment</label>
                    <div class="relative">

                        <input type="text" wire:model.defer='comment'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] border-2 border-gray-300 w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                            placeholder="Comment">
                        <div class="text-center mt-2 text-[0.6rem] ">Less Then 100 Words Only </div>
                    </div>
                </div>

            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="edit-modal-sent-invoice" type="button" wire:click='sfpInvoice'
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-5 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
                <button data-modal-hide="edit-modal-sent-invoice" type="button"
                    class="text-black bg-white hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-[0.6rem] border-2 border-gray-300 font-medium px-5 py-1.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
            </div>
        </div>
    </div>
</div> --}}


<div id="edit-modal-received-po" tabindex="-1"
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
                <button type="button" wire:click.prevent='resetChallanSeries()'
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-[0.6rem] border-2 border-gray-300 w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="edit-modal-received-po">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <div class="relative">
                    <label for="receiver_user_id" class="block text-[0.6rem] font-medium">Choose User</label>

                    <select id="select" wire:model="team_user_id"
                        class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0">
                        <option value="">Select Team User</option>

                        @if (isset($teamMembers) && is_array($teamMembers))
                            @foreach ($teamMembers as $team)
                                @php
                                    $team = isset($team) ? (object) $team : null;
                                @endphp
                                @if ($team !== null)
                                    <option value="{{ $team->id }}">{{ $team->team_user_name }}</option>
                                @endif
                            @endforeach
                        @endif


                    </select>
                </div>

                <div>
                    <label for="company_name" class="block text-[0.6rem] font-medium">Comment</label>
                    <div class="relative">

                        <input type="text" wire:model='comment'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] border-2 border-gray-300 w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                            placeholder="Comment">
                        <div class="text-center mt-2 text-[0.6rem] ">Less Then 100 Words Only </div>
                    </div>
                </div>

            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="edit-modal-received-po" type="button" wire:click='sfpReceivedPo'
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-5 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
                <button data-modal-hide="edit-modal-received-po" type="button"
                    class="text-black bg-white hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-[0.6rem] border-2 border-gray-300 font-medium px-5 py-1.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
            </div>
        </div>
    </div>
</div>



<div id="edit-modal-sent-po" tabindex="-1"
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
                <button type="button" wire:click.prevent='resetChallanSeries()'
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-[0.6rem] border-2 border-gray-300 w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="edit-modal-sent-po">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <div class="relative">
                    <label for="receiver_user_id" class="block text-[0.6rem] font-medium">Choose User</label>

                    <select id="select" wire:model="team_user_id"
                        class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0">
                        <option value="">Select Team User</option>

                        @if (isset($teamMembers) && is_array($teamMembers))
                            @foreach ($teamMembers as $team)
                                @php
                                    $team = isset($team) ? (object) $team : null;
                                @endphp
                                @if ($team !== null)
                                    <option value="{{ $team->id }}">{{ $team->team_user_name }}</option>
                                @endif
                            @endforeach
                        @endif


                    </select>
                </div>

                <div>
                    <label for="company_name" class="block text-[0.6rem] font-medium">Comment</label>
                    <div class="relative">

                        <input type="text" wire:model='comment'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] border-2 border-gray-300 w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                            placeholder="Comment">
                        <div class="text-center mt-2 text-[0.6rem] ">Less Then 100 Words Only </div>
                    </div>
                </div>

            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="edit-modal-sent-po" type="button" wire:click='sfpPo'
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-5 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
                <button data-modal-hide="edit-modal-sent-po" type="button"
                    class="text-black bg-white hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-[0.6rem] border-2 border-gray-300 font-medium px-5 py-1.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="edit-modal-received-invoice" tabindex="-1"
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
                <button type="button" wire:click.prevent='resetChallanSeries()'
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-[0.6rem] border-2 border-gray-300 w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="edit-modal-received-invoice">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <div class="relative">
                    <label for="receiver_user_id" class="block text-[0.6rem] font-medium">Choose User</label>

                    <select id="select" wire:model="team_user_id"
                        class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0">
                        <option value="">Select Team User</option>

                        @if (isset($teamMembers) && is_array($teamMembers))
                            @foreach ($teamMembers as $team)
                                @php
                                    $team = isset($team) ? (object) $team : null;
                                @endphp
                                @if ($team !== null)
                                    <option value="{{ $team->id }}">{{ $team->team_user_name }}</option>
                                @endif
                            @endforeach
                        @endif


                    </select>
                </div>

                <div>
                    <label for="company_name" class="block text-[0.6rem] font-medium">Comment</label>
                    <div class="relative">

                        <input type="text" wire:model='comment'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] border-2 border-gray-300 w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                            placeholder="Comment">
                        <div class="text-center mt-2 text-[0.6rem] ">Less Then 100 Words Only </div>
                    </div>
                </div>

            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="edit-modal-received-invoice" type="button"
                    wire:click='sfpReceivedInvoice'
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-[0.6rem] border-2 border-gray-300 px-5 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send</button>
                <button data-modal-hide="edit-modal-received-invoice" type="button"
                    class="text-black bg-white hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-[0.6rem] border-2 border-gray-300 font-medium px-5 py-1.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Default Modal CHECK SFP-->
