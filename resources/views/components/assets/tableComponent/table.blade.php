<table  x-data="{
    showSelected: false,
    selectedProducts: [],
    selectedCount: 0,
    allChecked: false,
    successMessage: '',
    errorMessage: '',
    toggleAll() {
        this.allChecked = !this.allChecked;
        this.selectedProducts = this.allChecked
            ? Array.from(document.querySelectorAll('input[type=checkbox][data-id]')).map(el => parseInt(el.dataset.id))
            : [];
        this.updateSelectedCount();
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
    },
    updateSelectedCount() {
        this.selectedCount = this.selectedProducts.length;
        this.showSelected = this.selectedCount > 0;
    },
    resetSelection() {
        this.selectedProducts = [];
        this.allChecked = false;
        this.updateSelectedCount();
        this.showSelected = false; // Explicitly set showSelected to false
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
"
 class="w-full text-xs text-left text-gray-500 dark:text-gray-400" x-data="{ showSelected: false }">
    <thead  x-show="!showSelected" class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
        @include('components.assets.tableComponent.th')
    </thead>
    <tbody class="text-black">
        @include('components.assets.tableComponent.td')
    </tbody>
</table>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<div class="modal" role="dialog" id="my_modal_8">
  <div class="modal-box max-w-4xl">
    <h3 class="font-bold text-lg">SFP Process</h3>
    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 mt-5 ">
        <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
            {{-- <tr> --}}
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Steps
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Date & Time
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Status
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Comment
            </th>
            {{-- @if (Auth::getDefaultDriver() == 'team-user')
                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                    Action
                </th>
            @endif --}}
            {{-- </tr> --}}
        </thead>

        <tbody>
            @if (isset($challan_sfp))
                @foreach ($challan_sfp as $key => $sfp)
                    <tr
                        class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                        <td class="px-2 py-2 text-xs">
                            {{ ++$key }}
                        </td>
                        <td class="px-2 py-2 text-xs">
                            {{ date('j F Y, h:i A', strtotime($sfp['created_at'] ?? '')) }}
                        </td>
                        <td class="px-2 py-2 text-xs">
                            {{ $sfp['status'] }} By {{ $sfp['sfp_by_name'] }} <br> to
                            {{ $sfp['sfp_to_name'] }}
                        </td>
                        <td class="px-2 py-2 text-xs">
                            {{ $sfp['comment'] }}
                        </td>
                        {{-- @if (Auth::getDefaultDriver() == 'team-user')
                            <td class="px-2 py-2 text-xs">
                                <button id="dropdownDefaultSfpButton-{{ $key }}"
                                    data-dropdown-toggle="dropdown-sfp-{{ $key }}"
                                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                    type="button">Select <svg class="w-2.5 h-2.5 ml-2.5"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg></button>
                                <!-- Dropdown-sfp menu -->
                                <div id="dropdown-sfp-{{ $key }}"
                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">
                                    @if (isset($columnName->statuses[0]))
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                            aria-labelledby="dropdownDefaultSfpButton-{{ $key }}">
                                            <li>
                                                <a href="javascript:void(0)"
                                                    wire:click="sfpAccept('{{ $sfp['id'] }}')"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)"
                                                    wire:click="sfpReject('{{ $sfp['id'] }}')"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                            </td>
                        @endif --}}
                    </tr>
                @endforeach
            @endif
            @if (isset($invoice_sfp))
                @foreach ($invoice_sfp as $key => $sfp)
                    <tr
                        class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                        <td class="px-2 py-2 text-xs">
                            {{ ++$key }}
                        </td>
                        <td class="px-2 py-2 text-xs">
                            {{ date('j F Y, h:i A', strtotime($sfp['created_at'] ?? '')) }}
                        </td>
                        <td class="px-2 py-2 text-xs">
                            {{ $sfp['status'] }} By {{ $sfp['sfp_by_name'] }} <br> to
                            {{ $sfp['sfp_to_name'] }}
                        </td>
                        <td class="px-2 py-2 text-xs">
                            {{ $sfp['comment'] }}
                        </td>

                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <div class="modal-action">
     <a href="#"  wire:click="updateVariable('challan_sfp', {{ json_encode([]) }})" class="btn">Close</a>
    </div>
  </div>
</div>
<div id="default-modal" tabindex="-1" wire:ignore.self
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex mt-20 justify-between p-3 border-b rounded-t dark:border-gray-600">
                <h3 class=" font-medium text-gray-900 dark:text-white">
                    SFP Process

                </h3>
                <button type="button" wire:click="updateVariable('challan_sfp', {{ json_encode([]) }})"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="default-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="modal-body flex items-center justify-center p-4 space-y-4 ">
                <div class="rounded-lg w-full overflow-auto">
                    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 ">
                        <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            {{-- <tr> --}}
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Steps
                            </th>
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Date & Time
                            </th>
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Status
                            </th>
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Comment
                            </th>
                            {{-- @if (Auth::getDefaultDriver() == 'team-user')
                                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                    Action
                                </th>
                            @endif --}}
                            {{-- </tr> --}}
                        </thead>

                        <tbody>
                            @if (isset($challan_sfp))
                                @foreach ($challan_sfp as $key => $sfp)
                                    <tr
                                        class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                                        <td class="px-2 py-2 text-xs">
                                            {{ ++$key }}
                                        </td>
                                        <td class="px-2 py-2 text-xs">
                                            {{ date('j F Y, h:i A', strtotime($sfp['created_at'] ?? '')) }}
                                        </td>
                                        <td class="px-2 py-2 text-xs">
                                            {{ $sfp['status'] }} By {{ $sfp['sfp_by_name'] }} <br> to
                                            {{ $sfp['sfp_to_name'] }}
                                        </td>
                                        <td class="px-2 py-2 text-xs">
                                            {{ $sfp['comment'] }}
                                        </td>
                                        {{-- @if (Auth::getDefaultDriver() == 'team-user')
                                            <td class="px-2 py-2 text-xs">
                                                <button id="dropdownDefaultSfpButton-{{ $key }}"
                                                    data-dropdown-toggle="dropdown-sfp-{{ $key }}"
                                                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                                    type="button">Select <svg class="w-2.5 h-2.5 ml-2.5"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                    </svg></button>
                                                <!-- Dropdown-sfp menu -->
                                                <div id="dropdown-sfp-{{ $key }}"
                                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">
                                                    @if (isset($columnName->statuses[0]))
                                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                            aria-labelledby="dropdownDefaultSfpButton-{{ $key }}">
                                                            <li>
                                                                <a href="javascript:void(0)"
                                                                    wire:click="sfpAccept('{{ $sfp['id'] }}')"
                                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)"
                                                                    wire:click="sfpReject('{{ $sfp['id'] }}')"
                                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif --}}
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">

                <button data-modal-hide="edit-modal-check" type="button"

                    class="text-white bg-[#dc3545] ml-auto focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Open the modal using ID.showModal() method -->
{{-- <button class="btn" onclick="my_modal_1.showModal()">open modal</button> --}}
<dialog id="my_modal_1" class="modal" wire:ignore.self>
  <div class="modal-box max-w-4xl">
    <h3 class="font-bold text-lg">SFP Process</h3>
    <div class="modal-body flex items-center justify-center p-4 space-y-4 mt-5">
        <div class="rounded-lg w-full">
            <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                    {{-- <tr> --}}
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Steps
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Date & Time
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Status
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Comment
                    </th>
                    {{-- @if (Auth::getDefaultDriver() == 'team-user')
                        <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                            Action
                        </th>
                    @endif --}}
                    {{-- </tr> --}}
                </thead>

                <tbody>
                    @if (isset($challan_sfp))
                        @foreach ($challan_sfp as $key => $sfp)
                            <tr
                                class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                                <td class="px-2 py-2 text-xs">
                                    {{ ++$key }}
                                </td>
                                <td class="px-2 py-2 text-xs">
                                    {{ date('j F Y, h:i A', strtotime($sfp['created_at'] ?? '')) }}
                                </td>
                                <td class="px-2 py-2 text-xs">
                                    {{ $sfp['status'] }} By {{ $sfp['sfp_by_name'] }} <br> to
                                    {{ $sfp['sfp_to_name'] }}
                                </td>
                                <td class="px-2 py-2 text-xs">
                                    {{ $sfp['comment'] }}
                                </td>
                                {{-- @if (Auth::getDefaultDriver() == 'team-user')
                                    <td class="px-2 py-2 text-xs">
                                        <button id="dropdownDefaultSfpButton-{{ $key }}"
                                            data-dropdown-toggle="dropdown-sfp-{{ $key }}"
                                            class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                            type="button">Select <svg class="w-2.5 h-2.5 ml-2.5"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                            </svg></button>
                                        <!-- Dropdown-sfp menu -->
                                        <div id="dropdown-sfp-{{ $key }}"
                                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">
                                            @if (isset($columnName->statuses[0]))
                                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                    aria-labelledby="dropdownDefaultSfpButton-{{ $key }}">
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            wire:click="sfpAccept('{{ $sfp['id'] }}')"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            wire:click="sfpReject('{{ $sfp['id'] }}')"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                                    </li>
                                                </ul>
                                            @endif
                                        </div>
                                    </td>
                                @endif --}}
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-action">
      <form method="dialog">
        <!-- if there is a button in form, it will close the modal -->
        <button  wire:click="updateVariable('challan_sfp', {{ json_encode([]) }})" class="btn">Close</button>
      </form>
    </div>
  </div>
</dialog>

<div id="edit-modal-check-re" tabindex="-1" wire:ignore.self
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex mt-20 justify-between p-3 border-b rounded-t dark:border-gray-600">
                <h3 class=" font-medium text-gray-900 dark:text-white">
                    SFP Process

                </h3>
                <button type="button" wire:click="updateVariable('challan_sfp', {{ json_encode([]) }})"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="edit-modal-check-re">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="modal-body flex items-center justify-center p-4 space-y-4 ">
                <div class="rounded-lg w-full">
                    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            {{-- <tr> --}}
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Steps
                            </th>
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Date & Time
                            </th>
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Status
                            </th>
                            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                Comment
                            </th>
                            {{-- @if (Auth::getDefaultDriver() == 'team-user')
                                <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                                    Action
                                </th>
                            @endif --}}
                            {{-- </tr> --}}
                        </thead>

                        <tbody>
                            @if (isset($challan_sfp))
                                @foreach ($challan_sfp as $key => $sfp)
                                    <tr
                                        class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                                        <td class="px-2 py-2 text-xs">
                                            {{ ++$key }}
                                        </td>
                                        <td class="px-2 py-2 text-xs">
                                            {{ date('j F Y, h:i A', strtotime($sfp['created_at'] ?? '')) }}
                                        </td>
                                        <td class="px-2 py-2 text-xs">
                                            {{ $sfp['status'] }} By {{ $sfp['sfp_by_name'] }} <br> to
                                            {{ $sfp['sfp_to_name'] }}
                                        </td>
                                        <td class="px-2 py-2 text-xs">
                                            {{ $sfp['comment'] }}
                                        </td>
                                        {{-- @if (Auth::getDefaultDriver() == 'team-user')
                                            <td class="px-2 py-2 text-xs">
                                                <button id="dropdownDefaultSfpButton-{{ $key }}"
                                                    data-dropdown-toggle="dropdown-sfp-{{ $key }}"
                                                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                                    type="button">Select <svg class="w-2.5 h-2.5 ml-2.5"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                    </svg></button>
                                                <!-- Dropdown-sfp menu -->
                                                <div id="dropdown-sfp-{{ $key }}"
                                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border">
                                                    @if (isset($columnName->statuses[0]))
                                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                            aria-labelledby="dropdownDefaultSfpButton-{{ $key }}">
                                                            <li>
                                                                <a href="javascript:void(0)"
                                                                    wire:click="sfpAccept('{{ $sfp['id'] }}')"
                                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Accept</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)"
                                                                    wire:click="sfpReject('{{ $sfp['id'] }}')"
                                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reject</a>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif --}}
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">

                <button data-modal-hide="edit-modal-check-re" type="button"
                    wire:click="updateVariable('challan_sfp', {{ json_encode([]) }})"
                    class="text-white bg-[#dc3545] ml-auto focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
            </div>
        </div>
    </div>
</div>
