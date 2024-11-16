<div class="relative overflow-x-auto shadow-md sm:rounded-lg border">

    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 ">
        <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
            {{-- <tr class="whitespace-nowrap"> --}}
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
               #
            </th>

            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Receiver
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Time
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Date
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Amount
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                State
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Status
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Pending
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                SFP
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Comment
            </th>
            <th scope="col" class="va-b px-2 py-2 capitalize whitespace-nowrap">
                Actions
            </th>
            
            {{-- </tr> --}}
        </thead>
        
        {{-- @foreach ($receiverDatas as $index => $data)
            <tbody>
                <tr
                    class="@if ($index % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0 ">
                    <td class="px-6">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6">
                        {{ $data->receiver_name }}
                    </td>
                    <td class="px-6 ">
                        {{ $data->receiver_special_id }}
                    </td>
                    <td class="px-6 ">
                        <p> {{ $data->details[0]->address ?? '' }} </p>
                    </td>

                    <td class="px-6 ">
                        {{ $data->details[0]->state ?? '' }}
                    </td>
                    <td class="px-6 ">
                        {{ $data->details[0]->pincode ?? '' }}
                    </td>
                    <td class="px-6 ">
                    </td>

                    <td class="px-6 ">
                        {{ $data->details[0]->phone ?? '' }}
                    </td>
                    <td class="px-6 ">
                    </td>
                    <td class="px-6 ">
                        {{ $data->details[0]->gst_number ?? '' }}
                    </td>
                    <td class="px-6">
                        {{ date('j/m/Y', strtotime($data->created_at)) }}
                    </td>

                    <td class="px-6 ">
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-green-500 mr-2"></div> Online
                        </div>
                    </td>

                    <td class="">
                        <button id="dropdownDefaultButton-{{ $index }}"
                            data-dropdown-toggle="dropdown-{{ $index }}"
                            class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                            type="button">Select <svg class="w-2.5  ml-2.5" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg></button>
                        <div id="dropdown-{{ $index }}"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                <li>
                                    <a href="#" wire:click="selectReceiver({{ json_encode($data) }} )"
                                        data-modal-target="medium-modal" data-modal-toggle="medium-modal"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        wire:click="$emit('triggerDelete', {{ $data->id }})"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                </li>

                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        @endforeach --}}
   

    </table>

</div>

<script>
    // JavaScript function to remove an address card
    function removeAddress(button) {
        const addressCard = button.parentElement;
        addressCard.remove();
    }
</script>
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
                    @this.call('deleteReceiver', id);
                    console.log('hello');
                } else {
                    console.log("Canceled");
                }
            });
        });
    });
</script>
