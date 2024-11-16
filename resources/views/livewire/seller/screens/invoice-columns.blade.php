<div class="bg-[#f2f3f4] sm:p-5 md:flex lg:flex justify-center gap-6">
    <div class="gap-4 bg-white rounded-lg p-3   w-80">
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-black text-sm">Default Columns</h2>
            </div>
            <div class="">
                @foreach ($invoiceDesignData as $index => $designData)
                    @if ($index < 3)
                        <div class="input-group my-2 column-group text-black">
                            <div class="flex ">
                                <input
                                    id="input{{ $index }}"
                                    wire:model.defer="invoiceDesignData.{{ $index }}.panel_column_display_name"
                                    @if (!$editMode || $editModeIndex !== $index) disabled @endif
                                    class="hsn-box h-7 w-2/3 mt-2 rounded-lg @if ($editMode && $editModeIndex === $index) bg-white @else bg-gray-300 @endif text-center font-mono text-xs font-normal text-black focus:outline-none"
                                    type="text" name="column{{ $index }}"
                                    placeholder="Enter column {{ $index + 1 }} name"
                                />
                                @if ($editMode && $editModeIndex === $index)
                                    <button type="button" wire:click='createInvoiceDesign({{ $designData['id'] }}, {{ $index }})' class="rounded-lg bg-gray-900 px-3 mt-2 ml-3 text-white hover:bg-yellow-200 hover:text-black">Save</button>
                                @else
                                    <svg wire:click="toggleEditMode({{ $index }})" class="w-6 h-6 mt-2 ml-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="gap-4 bg-white rounded-lg p-3 w-80">
        <div class="">
            <div class="card mb-4">
                <div class="card-header flex justify-between">
                    <h2 class=" font-semibold text-black text-sm">Additional Columns</h2>
                    <button wire:click="addColumn" class="bg-orange text-black rounded-lg px-2.5">+</button>
                </div>
                <div class="">
                    {{-- @dump($invoiceDesignData) --}}
                    @foreach ($invoiceDesignData as $index => $designData)
                        @if ($index >= 3)
                            <div class="input-group my-2 column-group text-black">
                                <div class="flex justify-between">
                                    <input
                                        id="input{{ $index }}"
                                        wire:model.defer="invoiceDesignData.{{ $index }}.panel_column_display_name"
                                        @if (!$editMode || $editModeIndex !== $index) disabled @endif
                                        class="hsn-box h-7 w-2/3 mt-2 rounded-lg @if ($editMode && $editModeIndex === $index) bg-white @else bg-gray-300 @endif text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        type="text" name="column{{ $index }}"
                                        placeholder="Enter column {{ $index + 1 }} name"
                                    />

                                    @if ($editMode && $editModeIndex === $index)
                                        <button type="button" wire:click='createInvoiceDesign({{ $designData['id'] }}, {{ $index }})' class="rounded-lg bg-gray-900 px-3 mt-2 ml-3 text-white hover:bg-yellow-200 hover:text-black">Save</button>
                                    @else
                                        <svg wire:click="toggleEditMode({{ $index }})" class="w-6 h-6 mt-2 ml-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                        </svg>
                                    @endif
                                    <button type="button" wire:click='removeColumn({{ $index }})' class="rounded-lg bg-red-500 px-3 mt-2 ml-3 text-white hover:bg-red-700 hover:text-white">x</button>
                                </div>
                                @if ($errorMessage && $loop->last)
                                <span class="text-red-500 text-[0.6rem]">{{ $errorMessage }}</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('swal:confirm', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call the Livewire method to update or delete the column
                    @this.updateOrDeleteColumn(event.detail.id, event.detail.index);
                }
            });
        });
    </script>
    @if (session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
        });
    </script>
@endif
</div>
