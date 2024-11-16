<div class="bg-[#f2f3f4] p-5 flex justify-center gap-6">

        <div class="gap-4 bg-white rounded-lg p-3 w-4/12">
           
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="text-lg font-semibold text-black text-sm">Default Columns</h2>
                    </div>
                    <div class="card-body ">
                        @foreach ($challanDesignData as $index => $designData)
                            @if ($index < 3)
                                <div class="input-group my-2 column-group text-black">
                                    <div class="flex justify-between">
                                        {{-- <label for="column{{ $index }}" class="block mr-3">Column {{ $index + 1 }}</label> --}}
                                        {{-- <svg onclick="toggleEditable({{ $index }})" class="w-6 h-6 mr-auto ml-auto text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                        </svg> --}}
                                        {{-- <svg onclick="toggleEditable({{ $index }})" class="w-6 h-6 mr-auto ml-auto text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                          </svg> --}}
                                          
                                          
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
                                        <svg id="editButton{{ $index }}"  onclick="toggleEditable({{ $index }})" class="w-6 h-6 mt-2  ml-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                          </svg>
                                          {{-- <button id="editButton{{ $index }}" onclick="toggleEditable({{ $index }})" class="rounded-lg bg-gray-900 px-3 mt-2 ml-3 text-white hover:bg-yellow-200 hover:text-black">Edit</button> --}}
                                        <button id="saveButton{{ $index }}"  type="button" wire:click='createChallanDesign' class="rounded-lg bg-gray-900 px-3 mt-2 ml-3 text-white hover:bg-yellow-200 hover:text-black hidden">Save</button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
         
        </div>
 
        <div class="gap-4 bg-white rounded-lg p-3 w-4/12">
            <div class="">
                <div class="card mb-4">
                    <div class="card-header flex justify-between">
                        <h2 class=" font-semibold text-black text-sm">Additional Columns</h2>
                        <button id="toggleColumns" data-tooltip-target="tooltip-toggleColumns" class="bg-orange text-black rounded-lg px-2.5">+</button>

                        <div id="tooltip-toggleColumns" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Show additional columns
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach ($challanDesignData as $index => $designData)
                            @if ($index >= 3)
                                <div class="input-group my-2 column-group text-black" style="display: none;">
                                    <div class="flex justify-between">
                                        <input
                                        id="input{{ $index }}"
                                        wire:model.defer="challanDesignData.{{ $index }}.panel_column_display_name"
                                        @if (!$editMode || is_null($designData->panel_column_display_name)) disabled @endif
                                        class="hsn-box h-7 w-2/3 mt-2 rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                                        type="text" name="column{{ $index }}"
                                        placeholder="Enter column {{ $index + 1 }} name"
                                    />
                                        <svg id="editButton{{ $index }}"  onclick="toggleEditable({{ $index }})" class="w-6 h-6 mt-2  ml-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                        </svg>
                                        <button id="saveButton{{ $index }}"  type="button"  wire:click='createChallanDesign({{ $index }})' class="rounded-lg bg-gray-900 px-3 mt-2 ml-3 text-white hover:bg-yellow-200 hover:text-black hidden">Save</button>
                                        <button id="removeButton{{ $index }}"  type="button" onclick="removeColumn({{ $index }})" class="rounded-lg bg-red-500 px-3 mt-2 ml-3 text-white hover:bg-red-700 hover:text-white">x</button>
                                    </div>
                                    <span id="message{{ $index }}" class="text-red-500 text-[0.6rem]"></span>

                                    

                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <script>
            var columnsVisible = false;
            var tooltip = document.getElementById('tooltip-toggleColumns');
            var button = document.getElementById('toggleColumns');
            var editMode = false;
            var columnGroups = document.getElementsByClassName('column-group');
            var currentColumn = 3;

            window.onload = function() {
            for (var i = 3; i <= 6; i++) {
                var input = document.getElementById(`input${i}`);
                if (input.value.trim() !== '') {
                    columnGroups[i].style.display = 'block';
                    currentColumn = i + 1;
                }
            }
        };

        button.addEventListener('click', function() {
            if (currentColumn > 0) {
                var lastInput = document.getElementById(`input${currentColumn - 1}`);
                var message = document.getElementById(`message${currentColumn - 1}`);
                if (lastInput.value.trim() === '') {
                    message.textContent = 'Please fill in the last column before adding a new one.';
                    return;
                }
        
            }

            if (currentColumn < columnGroups.length) {
                columnGroups[currentColumn].style.display = 'block';
                currentColumn++;
            }
            this.textContent = currentColumn < columnGroups.length ? '+' : '-';
            tooltip.textContent = currentColumn < columnGroups.length ? 'Show additional columns' : 'Hide additional columns';
        });

            Livewire.on('editModeChanged', (value) => {
                editMode = value;
            });

            function toggleEditable(index) {
                var input = document.getElementById(`input${index}`);
                var saveButton = document.getElementById(`saveButton${index}`);
                var editButton = document.getElementById(`editButton${index}`);
                var removeButton = document.getElementById(`removeButton${index}`);
                input.disabled = !input.disabled;
                input.classList.toggle('bg-white');
                saveButton.classList.toggle('hidden');
                editButton.classList.toggle('hidden');
                removeButton.classList.toggle('hidden');
            }

            function removeColumn(index) {
                var input = document.getElementById(`input${index}`);
                if (input.value.trim() === '') {
                    var message = document.getElementById(`message${index}`);
                    var columnGroup = document.getElementsByClassName('column-group')[index];
                    columnGroup.style.display = 'none';
                } else {
                    message.textContent = 'Cannot remove a column with a value. Please clear the value first.';
                }
            }
            Livewire.on('createChallanDesign', (index) => {
                var columnGroup = document.getElementsByClassName('column-group')[index];
                console.log(columnGroup);
                columnGroup.style.display = 'block';
            });
            
        </script>
        
</div>
