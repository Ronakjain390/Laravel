<div class="max-w-full mt-10 mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800"">
    <!-- <div class=" mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800"> -->
    <div class="mb-4 mt-4">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">


        </ul>
    </div>
    <div id="myTabContent" class=" p-4 rounded-lg dark:bg-gray-800">

        <div class="grid gap-4">
            <label for="content" class="block text-sm font-medium">Terms And Conditions</label>
            <div class="flex items-center"> 
                <input type="text" id="content" name="content" class="mt-1 p-2 h-10 block w-full text-dark rounded-md border-transparent focus:" wire:model.defer="termsAndConditionsData.content">
                {{-- <button type="button"  class="rounded-full bg-danger px-8 py-2 ml-2 text-white bg-red-600 dark:hover:bg-red-700 hover:text-black">-</button> --}}
            </div>

            <div class="flex justify-between ">
                {{-- <button type="button"  class="rounded-full bg-[#28a745] px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">+</button> --}}
                <button type="button" wire:click="addTerms" class="rounded-full bg-[#28a745] px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">Add</button>
            </div>
        </div>

    </div>


    <div class="relative overflow-x-auto shadow-md sm:rounded-lg h-screen " wire:ignore>

        <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 whitespace-nowrap py-3 normal-case">
                        #
                    </th>

                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Terms
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Date
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Action
                    </th>
                </tr>
            </thead>



            <tbody>
                {{-- @dd($termsIndexData); --}}
                @if(isset($termsIndexData))
                @foreach($termsIndexData as $index => $item)
                <tr class=" bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                    <td class="w-4 p-4">
                        <div class="font-normal text-gray-500"> {{ $index+1 }} </div>
                    </td>
                    <td class="px-6 ">{{ $item->content ?? ''}}
                    </td>

                    <td class="px-6 ">

                        {{$item->created_at ?? ''}}

                    </td>
                    </td>
                    <td class="">
                        <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg></button>
                        <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                <li>
                                    {{-- wire:click="selectInvoiceSeries('{{ $item->id ?? '' }}','{{ $item->series_number ?? '' }}','{{ $item->valid_till ?? '' }}','{{ $item->valid_from ?? '' }}', '{{ $item->assigned_to_name ?? '' }}' )" --}} 
                                    {{-- <a href="javascript:void(0);" 
                                    wire:click="selectInvoiceTerms('{{ json_encode($item) ?? '' }}')" data-modal-target="edit-modal" data-modal-toggle="edit-modal" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a> --}}
                                </li>
                                <li>
                                    <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $item->id ?? '' }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                </li>

                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

    </div>
    <!-- Default Modal -->
    <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
        <div class="relative w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        Edit Terms and Conditions
                        <button type="button" wire:click='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                    </h3>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <div>
                        <label for="terms" class="block text-sm font-medium ">Terms</label>
                        <input wire:model.defer="selectedContent" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>



                    <div class="relative">
                        <label for="receiver_user_id" class="block text-sm font-medium">Update</label>


                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="edit-modal" type="button" wire:click='updatePanelSeries()' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                    <button data-modal-hide="edit-modal" type="button"  class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                </div>
            </div>
        </div>
    </div>


</div>


</div>


<script type="text/javascript">
    document.addEventListener('livewire:load', function(e) {
        @this.on('triggerDelete', id => {
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6fc5e0",
                cancelButtonColor: "#d33",
                confirmButtonText: "Delete",
            }).then((result) => {
                if (result.value) {
                    @this.call('deleteInvoiceTerms', id);
                    location.reload();
                    console.log('hello');
                } else {
                    console.log("Canceled");
                }
            });
        });
    });
</script>
