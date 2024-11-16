<div class="">
    <div class="rounded-lg bg-gray-100 p-2">
        <div class="flex rounded-md pb-2 shadow-sm justify-end" role="group">
            {{-- <button wire:click="innerFeatureRedirect('sent_invoice', null)" type="button" class="rounded-l-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Sent Invoice</button> --}}
            <!-- <button type="button" class="border-b border-t border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Challan Design</button>
            <button type="button" class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Deleted Challans</button>
            <button wire:click="innerFeatureRedirect('detailed_sent_invoice', null)" type="button" class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed View</button> -->
            {{-- <button type="button" class="rounded-r-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button> --}}

            {{-- <a  href="{{ route('challan.exportDetailedReturnChallan') }}"
                class="rounded-l-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</a> --}}
                <button type="button" wire:click="innerFeatureRedirect('sent_return_challan', null)" class="rounded-r-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Back</button>
        </div>
        <div wire:init="loadData">

            @if ($this->isLoading)
            <!-- Show nothing or a minimal loading state -->
            @include('livewire.sender.screens.placeholders')
            @else
            @include('components.assets.tableComponent.table')
            @endif
        </div>
    </div>
    </div>
</div>
</div>
