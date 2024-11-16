<div>
    <div x-data="{ showExportDropdown: false }" class=" rounded-md pb-2 shadow-sm justify-end hidden sm:flex" role="group">
        {{-- <button wire:click="innerFeatureRedirect('sent_invoice', null)" type="button" class="rounded-l-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Sent Invoice</button> --}}
        <!-- <button type="button" class="border-b border-t border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Challan Design</button>
        <button type="button" class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Deleted Challans</button>
        <button wire:click="innerFeatureRedirect('detailed_sent_challan', null)" type="button" class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed View</button> -->

        <button @click="showExportDropdown = !showExportDropdown"
        class="rounded-l-lg border border-gray-900 px-4 py-1 text-sm text-black focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">
        Export
        <svg class="w-4 h-4 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="showExportDropdown" @click.away="showExportDropdown = false"
        class="absolute z-10 mt-10 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
            <a href="#" wire:click="export('current_page')" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-100" role="menuitem">Current Page</a>
            <a href="#"
               wire:click="{{ $isFilterApplied ? 'export(\'filtered_data\')' : 'null' }}"
               class="block px-4 py-2 text-xs {{ $isFilterApplied ? 'text-gray-700 hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed' }}"
               role="menuitem">
               Filtered Data
               @if($isFilterApplied && $totalChallansCount !== null)
                   ({{ $totalChallansCount }} records)
               @endif
            </a>
            <a href="#" wire:click="export('all_data')" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-100" role="menuitem">All Data</a>
        </div>
    </div>
      <a href="javascript:history.back()" class="rounded-r-lg border border-gray-900 px-4 py-1 text-sm text-black focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Back</a>
    </div>
    @php
    // dd(json_decode($mainUser));
    $mainUser = json_decode($mainUser);
    @endphp

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
         <table  class="w-full text-xs text-left text-gray-500 dark:text-gray-400" x-data="{ showSelected: false }">
            <thead   class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                <div x-show="selectedCount > 0" >

                    @include('components.assets.tableComponent.th')
                </div>

            </thead>
            <tbody class="text-black">

    @foreach ($returnChallans as $key => $columnName)
    {{-- @dump($columnName) --}}
    @php
    $columnName = (object) $columnName;
        // $columnName->statuses[0] = (object) $columnName->statuses[0];
        $mainUser = json_decode($this->mainUser);
        $panelName = strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']));
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
        {{-- @dump($columnName->orderDetails) --}}
        @if($columnName->orderDetails)
            @foreach ($columnName->orderDetails as $keys => $details)
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
            @if(count($columnName->orderDetails) > 1 && $keys < count($columnName->orderDetails) - 1)
                <td></td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->challan_series }}-{{ $columnName->series_num }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('h:i A', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ date('j F Y', strtotime($columnName->created_at)) }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->sender }}</td>
                <td class="px-2  text-[0.6rem] border-2 border-gray-300 ">{{ $columnName->receiver }}</td>
                @endif

            @endforeach
        @endif
    </tr>
</tr>
@endforeach
</tbody>

</table>
</div>
<div class="py-5 w-full">

    {{ $returnChallans->links()}}
</div>
</div>
