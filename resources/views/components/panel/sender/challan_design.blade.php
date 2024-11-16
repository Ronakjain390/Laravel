<div class="flex justify-center mt-5 h-screen bg-[#f2f3f4] ">
    <div class="justify-center rounded-lg w-2/3">
        @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="success-alert" class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
            <div class="ms-3 text-sm ">
                <span class="font-medium">Success:</span>  {{ session()->get('success') }}
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <div class=" gap-4 justify-between bg-white rounded-lg p-3">
            <div class="">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="text-lg font-semibold text-black">Default Columns</h2>
                    </div>
                    <div class="card-body grid grid-cols-2">
                        @foreach ($challanDesignData as $index => $designData)
                            @if ($index < 3)
                                <div class="input-group my-2 column-group text-black">
                                    <div class="flex justify-between">
                                        <label for="column{{ $index }}" class="block mr-10">Column {{ $index + 1 }}</label>
                                        {{-- <button onclick="toggleEditable({{ $index }})" class="btn bg-orange text-black">Edit</button> --}}
                                        <svg onclick="toggleEditable({{ $index }})" class="w-6 h-6 mr-auto ml-auto text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                          </svg>
                                          
                                    </div>
                                    <div class="flex">
                                        <input
                                            id="input{{ $index }}"
                                            wire:model="challanDesignData.{{ $index }}.panel_column_display_name"
                                            disabled
                                            class="hsn-box h-7 w-2/3 mt-2 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" name="column{{ $index }}"
                                            placeholder="Enter column {{ $index + 1 }} name"
                                        />
                                        <button id="saveButton{{ $index }}"  type="button" wire:click='createChallanDesign' class="rounded-lg bg-gray-900 px-3 mt-2 ml-2 text-white hover:bg-yellow-200 hover:text-black hidden">Save</button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="gap-4 justify-between bg-white rounded-lg p-3 mt-4">
            <div class="">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="text-lg font-semibold text-black">Additionals Columns</h2>
                    </div>
                    <div class="card-body grid grid-cols-2">
                        @foreach ($challanDesignData as $index => $designData)
                            @if ($index >= 3)
                                <div class="input-group my-2 column-group text-black">
                                    <label for="column{{ $index }}" class="block">Column {{ $index + 1 }}</label>
                                    <div class="flex justify-between">
                                        <input
                                            wire:model="challanDesignData.{{ $index }}.panel_column_display_name"
                                            @if (!$editMode || is_null($designData->panel_column_display_name)) disabled @endif
                                            class="hsn-box h-7 w-2/3 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                            type="text" name="column{{ $index }}"
                                            placeholder="Enter column {{ $index + 1 }} name"
                                        />
                                        <svg class="w-6 h-6 mr-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                          </svg>
                                          
                                    </div>
                                    <span id="message{{ $index }}" class="text-red-500"></span>
                                </div>
                                {{-- <button id="saveButton{{ $index }}"  type="button" wire:click='createChallanDesign({{ $index }})' class="rounded-lg bg-gray-900 px-3 mt-2 ml-3 text-white hover:bg-yellow-200 hover:text-black hidden">Save</button> --}}

                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        

        <div class="flex items-center justify-between">

            <button id="toggleColumns" data-tooltip-target="tooltip-toggleColumns" class="btn bg-orange text-black">+</button>

            <div id="tooltip-toggleColumns" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Show additional columns
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </div>
    </div>

    <script>
        var columnsVisible = false;
        var tooltip = document.getElementById('tooltip-toggleColumns');
        var button = document.getElementById('toggleColumns');
        var editMode = false;

        button.addEventListener('click', function() {
            var columnGroups = document.getElementsByClassName('column-group');
            for (var i = 3; i < columnGroups.length; i++) {
                columnGroups[i].style.display = columnsVisible ? 'none' : 'block';
            }

            columnsVisible = !columnsVisible;
            this.textContent = columnsVisible ? '-' : '+';
            tooltip.textContent = columnsVisible ? 'Hide additional columns' : 'Show additional columns';
        });

        Livewire.on('editModeChanged', (value) => {
            editMode = value;
        });
        function toggleEditable(index) {
        var input = document.getElementById(`input${index}`);
        var saveButton = document.getElementById(`saveButton${index}`);
        input.disabled = !input.disabled;
        input.classList.toggle('bg-white');
        saveButton.classList.toggle('hidden');
    }
    </script>
</div>
