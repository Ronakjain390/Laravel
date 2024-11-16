<div class="max-w-full mt-10 mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800">
    <!-- <div class=" mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800"> -->
    <div class="mb-4 mt-4">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">


        </ul>
    </div>
    <div id="myTabContent" class=" p-4 rounded-lg dark:bg-gray-800" wire:ignore>

        <div class="grid gap-4">
            <div>
                <label for="series_number" class="block text-sm font-medium ">Challan Series Number </label>
                <input type="text" wire:model.defer="addChallanSeriesData.series_number" id="series_number" name="series_number" class="mt-1 p-2 h-10 block w-full text-white rounded-md bg-[#2e2828] border-transparent focus: ">
            </div>
            <div>
                <label for="company_name" class="block text-sm font-medium ">Valid From</label>
                <div class="relative">

                    <input wire:model.defer="addChallanSeriesData.valid_from" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Select date">
                </div>
            </div>
            <div>
                <label for="company_name" class="block text-sm font-medium">Valid Till</label>
                <div class="relative">

                    <input wire:model.defer="addChallanSeriesData.valid_till" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Select date">
                </div>
            </div>
            {{-- @foreach ($receiverDatas as $receiverData)
            @dump($receiverData);
            @endforeach --}}
            {{-- @dump($receiverDatas); --}}
            <div class="relative">
                <label for="receiver_user_id" class="block text-sm font-medium">Assign Receiver</label>
                <select id="select" wire:model.prevent="addChallanSeriesData.assigned_to_s_id" class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border focus:border-gray-500 focus:bg-white focus:ring-0">
                    <option value="">Keep It Unassigned</option>
                    <option value="default">Default</option>
                   
                    @foreach ($receiverDatas as $receiverData)
                        @if (isset($receiverData->sender_id))
                            <option value="{{$receiverData->sender_id}}">
                                {{ $receiverData->sender }}
                            </option>
                        @endif
                    @endforeach 
                </select>
            </div>
            

            <div class="flex justify-center">
                <button type="button" wire:click="challanSeries" class="rounded-full w-full bg-[#28a745] px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">Add</button>
            </div>
        </div>
    </div>


    <div class="relative overflow-x-auto shadow-md sm:rounded-lg h-screen h-screen">

        <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 whitespace-nowrap py-3 normal-case">
                        S. No.
                    </th>

                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Challan Series Number
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Assigned To
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Status
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Valid From
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Valid Till
                    </th>
                    <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                        Action
                    </th>
                </tr>
            </thead>

            {{-- @if (count($seriesNoData) > 0) --}}

            @foreach ($seriesNoData as $index => $data)
            @php
            $data = (object) $data;
            @endphp
            <tbody>
                <tr class="{{ $index % 2 === 0 ? 'bg-[#E9E6E6]' : 'bg-white' }} bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                    <td class="w-4 p-4">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 ">
                        {{ $data->series_number ?? '' }}
                    </td>
                    <td class="px-6 ">
                        @if ($data->default == 0)
                        {{ $data->assigned_to_name ?? '' }}
                        @else
                        Default
                        @endif
                    </td>
                    <td class="px-6 ">
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full @if (($data->assigned_to_id ?? '') != null) bg-green-500 @elseif($data->default = '1') bg-green-500 @else bg-red-500 @endif mr-2">
                            </div>
                            @if (($data->assigned_to_id ?? '') != null)
                            Assigned
                            @elseif($data->default == 0)
                            Not Assigned
                            @else
                            Default
                            @endif
                        </div>
                    </td>
                    <td class="px-6 ">
                        {{ $data->valid_from ?? '' }}
                    </td>
                    <td class="px-6 ">
                        {{ $data->valid_till ?? '' }}
                    </td>
                    <td class="">
                        @if (!Str::startsWith($data->series_number, 'CH'))
                        <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg></button>
                        <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                <li>
                                    <a href="javascript:void(0);" {{-- wire:click="selectChallanSeries('{{ $data->id ?? '' }}','{{ $data->series_number ?? '' }}','{{ $data->valid_till ?? '' }}','{{ $data->valid_from ?? '' }}', '{{ $data->assigned_to_name ?? '' }}' )" --}} wire:click="selectChallanSeries('{{ json_encode($data) ?? '' }}')" data-modal-target="edit-modal" data-modal-toggle="edit-modal" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $data->id ?? '' }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                </li>

                            </ul>
                        </div>
                        @endif
                    </td>
                </tr>
            </tbody>
            @endforeach
            {{-- @endif --}}
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
                        Edit Series Number
                        <button type="button" wire:click.prevent='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
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
                        <label for="series_number" class="block text-sm font-medium ">Challan Series Number </label>
                        <input wire:model.prevent="updateChallanSeriesData.series_number" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium ">Valid From</label>
                        <div class="relative">

                            <input wire:model.prevent="updateChallanSeriesData.valid_from" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Select date">
                        </div>
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium">Valid Till</label>
                        <div class="relative">

                            <input wire:model.prevent="updateChallanSeriesData.valid_till" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ">
                        </div>
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium">Receiver Name</label>
                        <div class="relative">

                            <input wire:model.prevent="updateChallanSeriesData.assigned_to_name" type="text" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " placeholder="Receiver Name">
                        </div>
                    </div>
                    <div class="relative">
                        <label for="receiver_user_id" class="block text-sm font-medium">Update Receiver</label>

                        <select id="select" wire:model.prevent="updateChallanSeriesData.assigned_to_s_id" class="mt-1 p-2 pr-8 h-10 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0">
                            <option value="">Keep It Unassigned</option>
                            <option value="default">Default</option>
                            @foreach ($receiverDatas as $receiverData)
                            @if (isset($receiverData->sender_id))
                                <option value="{{$receiverData->sender_id}}">
                                    {{ $receiverData->sender }}
                                </option>
                            @endif
                        @endforeach 
                            {{-- @foreach ($receiverDatas as $receiverData)
                            @if (isset($updateChallanSeriesData['assigned_to_id']))
                            @php
                            $receiverData = (object) $receiverData;
                            @endphp
                            @if ($receiverData->id == $updateChallanSeriesData['assigned_to_id'])
                            <option selected value="{{ $receiverData->id }}">
                                {{ $receiverData->receiver_name }}
                            </option>
                            @else
                            <option value="{{ $receiverData->id }}">
                                {{ $receiverData->receiver_name }}
                            </option>
                            @endif
                            @else
                            @php
                            $receiverData = (object) $receiverData;
                            @endphp
                            <option value="{{ $receiverData->id }}">
                                {{ $receiverData->receiver_name }}
                            </option>
                            @endif
                            @endforeach --}}
                        </select>
                        


                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="edit-modal" type="button" wire:click='updatePanelSeries' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>

                    <button data-modal-hide="edit-modal" type="button" wire:click='resetChallanSeries()' class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
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
                    @this.call('deleteChallanSeries', id);
                    console.log('hello');
                } else {
                    console.log("Canceled");
                }
            });
        });
    });
</script>
