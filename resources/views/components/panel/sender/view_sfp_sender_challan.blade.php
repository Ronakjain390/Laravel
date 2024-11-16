<div class="">
    <div class="rounded-lg bg-gray-100 p-2">
        {{-- <div class="hidden sm:flex rounded-md pb-2 shadow-sm justify-end " role="group">
            <button wire:click="innerFeatureRedirect('check_balance', null)" type="button" class="rounded-l-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check Balance</button>
            <button type="button" class="border-b border-t border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Challan Design</button>
            <button type="button" wire:click="innerFeatureRedirect('deleted_sent_challan', null)"  class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Deleted Challans</button>
            <button type="button" wire:click="innerFeatureRedirect('detailed_sent_challan', null)" class="border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed View</button>
            <button type="button" class="rounded-r-lg border border-gray-900  px-4 py-1 text-sm  text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button>
        </div> --}}
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg h-screen ">
            <div class="flex items-center justify-between bg-white dark:bg-gray-900">

                @include('components.assets.tableComponent.table')
            </div>
          
        </div>
    </div>
</div>
