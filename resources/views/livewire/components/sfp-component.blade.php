<div>

    @if ($sfpModal == true)
        <div x-data="{ sfpModal: @entangle('sfpModal') }" x-show="sfpModal" x-on:keydown.escape.window="sfpModal = false"
            x-on:close.stop="sfpModal = false"
            class="fixed inset-0 flex items-center justify-center px-2.5 z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60"
            wire:ignore.self>
            <div class="bg-white p-6 rounded shadow-lg w-full max-w-md"
                x-data="{
                    selected: @entangle('team_user_ids'),
                    get isButtonEnabled() {
                        return this.selected.length > 0;
                    }
                }">                <div class="mb-4">
                     <div class="">

                        <div class="p-4 md:p-5 text-center">
                            <h1 class="text-lg text-black text-left">Send for Processing</h1>
                            <form class="max-w-md mx-auto mt-5">
                                <div class="grid  gap-4 mt-2 text-xs">
                                    <!-- Left side (Dropdown) -->
                                    <div class="relative text-left">
                                        <label for="" class="test-xs text-black">Select Team Members</label>
                                        <br>
                                        {{-- @dump($teamMembers) --}}
                                        <select class="js-example-basic-multiple" name="team_user_ids[]"
                                            multiple="multiple" wire:model.defer="team_user_ids">
                                            <option disabled>Select Team Members...</option>
                                            @if (isset($teamMembers) && is_array($teamMembers))
                                                @php
                                                    $addedOwners = [];
                                                    $hasSubusers = false;
                                                    function arrayToObject($array)
                                                    {
                                                        if (is_array($array)) {
                                                            return (object) array_map('arrayToObject', $array);
                                                        }
                                                        return $array;
                                                    }
                                                @endphp
                                                @foreach ($teamMembers as $team)
                                                    @php $team = arrayToObject($team); @endphp
                                                    @if ($team !== null && $team->id !== auth()->id())
                                                        <!-- Team Member Option -->
                                                        <option value="{{ $team->id }}"
                                                            data-name="{{ $team->team_user_name }}">
                                                            {{ $team->team_user_name }}
                                                        </option>
                                                        @php $hasSubusers = true; @endphp
                                                    @endif

                                                    @if (isset($team->owner) && !in_array($team->owner->id, $addedOwners) && $team->owner->id !== auth()->id())
                                                        <!-- Team Owner Option -->
                                                        <option value="{{ $team->owner->id }}" data-name="Admin">
                                                            Admin ({{ $team->owner->name }})
                                                        </option>
                                                        @php $addedOwners[] = $team->owner->id; @endphp
                                                    @endif
                                                @endforeach

                                                @if (!$hasSubusers && !empty($addedOwners))
                                                    <!-- Show Admin option when there are no subusers -->
                                                    @foreach ($teamMembers as $team)
                                                        @php $team = arrayToObject($team); @endphp
                                                        @if (isset($team->owner) && !in_array($team->owner->id, $addedOwners) && $team->owner->id !== auth()->id())
                                                            <option value="{{ $team->owner->id }}" data-name="Admin">
                                                                Admin ({{ $team->owner->name }})
                                                            </option>
                                                            @php break; @endphp
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endif
                                        </select>

                                    </div>
                                    <!-- Right side (Comment box) -->
                                    <div class="relative">
                                        <textarea oninput="if(this.value.length > 100) this.value = this.value.slice(0, 100);" wire:model.defer="comment"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-[0.6rem] w-2/3  rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Comment"></textarea>
                                        <div class="text-center mt-2 text-[0.6rem]">Less than 100 words only</div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
                <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                    <button wire:click="closeSfpModal" x-on:click.self="sfpModal = false"
                        class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        Cancel
                    </button>
                    <button wire:click="createSfp"
                        class=" @if(!$this->hasSelectedTeamMembers()) disabled cursor-not-allowed @endif middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        @if(!$this->hasSelectedTeamMembers()) disabled @endif>
                        Send
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>

    function initializeSelect2() {
                    $('.js-example-basic-multiple').select2().on('change', function (e) {
                        @this.set('team_user_ids', $(this).val());
                    });
                }

                document.addEventListener('livewire:update', function() {
                    initializeSelect2();
                    @this.on('openSfpModal', () => {
                        setTimeout(() => {
                            $('.js-example-basic-multiple').select2({
                                templateResult: formatState
                            }).on('change', function (e) {
                                @this.set('team_user_ids', $(this).val());
                                Alpine.store('selected', $(this).val());
                            });
                        }, 100);
                    });
                    console.log('Livewire Update');

                    initializeMultiSelect();
                    window.dispatchEvent(new CustomEvent('challansUpdated'));
                });

                document.addEventListener('DOMContentLoaded', function() {
                    initializeMultiSelect();
                    initializeSelect2();
                    console.log('DOMContentLoaded');
                });

                document.addEventListener('livewire:load', function () {
                    console.log('Livewire Load');
                    initializeSelect2();
                });

            function initializeMultiSelect() {
                const multiSelectInputs = document.querySelectorAll('.multi-select-input');
                const multiSelectDropdowns = document.querySelectorAll('.multi-select-dropdown');

                multiSelectInputs.forEach((input, index) => {
                    const dropdown = multiSelectDropdowns[index];
                    const options = dropdown.querySelectorAll('.multi-select-option');
                    const selectedValues = [];

                    input.addEventListener('click', (e) => {
                        e.stopPropagation();
                        dropdown.classList.toggle('hidden');
                    });

                    options.forEach(option => {
                        option.addEventListener('change', () => {
                            if (option.checked) {
                                selectedValues.push({ id: option.value, name: option.dataset.name });
                            } else {
                                const valueIndex = selectedValues.findIndex(item => item.id === option.value);
                                if (valueIndex !== -1) {
                                    selectedValues.splice(valueIndex, 1);
                                }
                            }
                            input.value = selectedValues.map(item => item.name).join(', ');
                        });
                    });

                    document.addEventListener('click', (e) => {
                        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                });
            }
</script>
