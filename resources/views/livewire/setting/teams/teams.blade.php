<div>
    <div class=" ">
        <div class="max-w-5xl mt-4 mx-auto">
            @if ($errorMessage)
                {{-- {{dd($errorMessage)}} --}}
                @foreach (json_decode($errorMessage) as $error)
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="success-alert"
                        class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                        role="alert">
                        <span class="font-medium">Error:</span> {{ $error }}
                    </div>
                @endforeach
            @endif
            @if ($successMessage)
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" id="success-alert"
                    class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
                    role="alert">
                    <span class="font-medium">Success:</span> {{ $successMessage }}
                </div>
            @endif
        </div>

        <div wire:loading
            class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  z-30 bg-opacity-50 ">
            <span class="loading loading-spinner loading-md"></span>
        </div>
        <div id="successModal" style="display: none;">
            <div class="modal-content">
                <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs"
                    id="successMessage"></p>
            </div>
        </div>
        <div id="errorModal" style="display: none;">
            <div
                class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs">
                <p class="mt-3 " id="errorMessage">\
                </p>
            </div>
        </div>

        <div class="border-b border-gray-400 text-black text-sm flex flex-col sm:flex-row sm:hidden px-2">
            <select class="px-2 my-2 w-full text-center rounded-lg text-xs" wire:model="activeTab">
                <option value="tab1">Team</option>
                <option value="tab2">Team Member</option>
            </select>
        </div>
        <div class="border-b p-1.5 border-gray-400 text-black text-sm hidden sm:flex">

            {{-- @php
                    $mainUser = json_decode($this->mainUser);
                @endphp

                @if ($mainUser->team_user != null) --}}

            <button
                class="px-4 p-1.5 w-auto text-center font-medium {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}"
                wire:click="$set('activeTab', 'tab1')">Team</button>
            <button
                class="px-4 p-1.5 w-auto text-center font-medium {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}"
                wire:click="$set('activeTab', 'tab2')">Team Member</button>
            {{-- @endif --}}
        </div>
        @if ($activeTab === 'tab1')
            <!-- Content for Tab 1 -->
            <div class="flex-grow md:ml-2 mt-2">
                <!-- Add new products form -->
                <div class="bg-[#f2f3f4] rounded-lg p-2">
                    <div class="max-w-5xl mx-auto">
                        @if (Auth::getDefaultDriver() !== 'team-user')
                        <div class="rounded-xl mt-4 border bg-white p-4 border-gray-200 shadow dark:bg-gray-800 dark:border-gray-700">
                            <div class="bg-white rounded-t-lg mb-3 mx-auto">
                                <div class="flex justify-center items-center border-b pb-1.5">
                                    <h3 class="font-medium text-black">Create Team</h3>
                                </div>
                            </div>

                            <div x-data="{ team_name: @entangle('team_name').defer, view_preference: @entangle('view_preference').defer }"
                            @team-created.window="team_name = ''; view_preference = ''">
                            <form id="team-form">
                                <div class="grid gap-6 mb-4 md:grid-cols-2 sm:grid-cols-1"> <!-- Added responsive column grid -->
                                    <div>
                                        <label for="team_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Team Name</label>
                                        <input type="text" wire:model.defer='team_name' id="team_name"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Team Name" required>
                                        @error('team_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <div>
                                            <label for="countries" class="block mb-2 text-sm flex items-center font-medium text-gray-900 dark:text-white">
                                                Data Accessibility
                                                <div class="relative ml-2 group">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div class="absolute z-10 invisible group-hover:visible bg-gray-800 text-white text-xs rounded p-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48">
                                                        Choose who can access the data: same team only or all teams.
                                                        {{-- <div class="tooltip-arrow"></div> --}}
                                                    </div>
                                                </div>
                                            </label>

                                        </div>

                                        <select wire:model.defer='view_preference' id="view_preference"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option selected>Select</option>
                                            <option value="own_team">Same team only</option>
                                            <option value="all_team">All teams</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="self-end mt-4"> <!-- Added some margin for separation -->
                                    <button type="button" id="save-button" wire:click='createTeam'
                                        class="text-white focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 mr-2 dark:focus:ring-gray-700 dark:border-gray-700 bg-gray-300 dark:bg-gray-300"
                                        disabled>
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>


                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const teamNameInput = document.getElementById('team_name');
                                    const viewPreferenceSelect = document.getElementById('view_preference');
                                    const saveButton = document.getElementById('save-button');

                                    function updateSaveButtonState() {
                                        const teamName = teamNameInput.value.trim();
                                        const viewPreference = viewPreferenceSelect.value.trim();
                                        if (teamName && viewPreference && viewPreference !== 'Select') {
                                            saveButton.disabled = false;
                                            saveButton.classList.remove('bg-gray-300', 'dark:bg-gray-300');
                                            saveButton.classList.add('bg-gray-800', 'hover:bg-gray-900', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
                                        } else {
                                            saveButton.disabled = true;
                                            saveButton.classList.add('bg-gray-300', 'dark:bg-gray-300');
                                            saveButton.classList.remove('bg-gray-800', 'hover:bg-gray-900', 'dark:bg-gray-800', 'dark:hover:bg-gray-700');
                                        }
                                    }

                                    teamNameInput.addEventListener('input', updateSaveButtonState);
                                    viewPreferenceSelect.addEventListener('change', updateSaveButtonState);

                                    // Initialize button state on page load
                                    updateSaveButtonState();
                                });
                            </script>
                        </div>

                        <script>
                            document.addEventListener('livewire:update', function() {
                                Livewire.on('teamCreated', () => {
                                    // Trigger Alpine.js to reset the fields
                                    window.dispatchEvent(new CustomEvent('team-created'));
                                });
                            });
                        </script>
                        @endif

                        <!-- Teams Table -->
                        <div class="rounded-xl mt-4 border bg-white p-4 border-gray-200 shadow dark:bg-gray-800 dark:border-gray-700">
                            <div class="bg-white rounded-lg shadow overflow-x-auto">
                                <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                        <tr class="whitespace-nowrap">
                                            <th scope="col" class="px-4 py-3 normal-case text-sm font-semibold">S. No.</th>
                                            <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">Team Name</th>
                                            <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">Team Owner</th>
                                            <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">Data Accessibility</th>
                                            @if (Auth::getDefaultDriver() !== 'team-user')
                                            <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    @foreach ($teams as $index => $data)
                                    @php
                                    $data = (object) $data;
                                    @endphp
                                    <tbody>
                                        <tr class="@if ($index % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">
                                            <td class="px-6 py-2">{{ $index + 1 }}</td>
                                            <td class="px-6 py-2">{{ $data->team_name }}</td>
                                            <td class="px-6">{{ $data->team_owner_user }}</td>
                                            <td class="px-6">
                                                <p>{{ $data->view_preference == 'own_team' ? 'Same team only' : ($data->view_preference == 'all_team' ? 'All teams' : '') }}</p>
                                            </td>
                                            @if (Auth::getDefaultDriver() !== 'team-user')
                                            <td>
                                                <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}"
                                                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                                    type="button">Select
                                                    <svg class="w-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                                    </svg>
                                                </button>
                                                <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                        <li>
                                                            <a href="#" wire:click.prevent="edit({{ $data->id }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $data->id }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div x-data="{ showModal: @entangle('showModal') }" x-cloak x-show="showModal"
                    class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 w-full max-w-sm">
                        <div class="space-y-6">
                            <div>
                                <label for="team_name" class="block text-md font-medium">Team Name</label>
                                <input type="text" wire:model='team.update_team_name'
                                    class="bg-gray-50 border border-gray-300 mt-3 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </div>
                        </div>
                        <div class="border-gray-200 border-t dark:border-gray-600 flex justify-end pt-3 rounded-b space-x-2">
                            <button wire:click.prevent="updateTeam" type="button"
                                class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                Save Changes
                            </button>
                            <button @click="showModal = false" type="button"
                                class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        @elseif ($activeTab === 'tab2')
            <!-- Content for Tab 2 -->


            <livewire:setting.team-member lazy />
        @endif


        <!-- Default Modal -->
        <div id="medium-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
            wire:ignore.self>
            <div class="relative w-full max-w-xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Edit Team
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="medium-modal" onclick="removeBackdrop()">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="team_name" class="block text-md font-medium">Team Name</label>
                            <input type="text" wire:model='team.update_team_name'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                    </div>
                    <div
                        class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button wire:click.prevent="updateTeam" type="button"
                            class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 m-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                            onclick="removeBackdrop()">Save
                            Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



        <script>
            function removeBackdrop() {
                const backdrop = document.querySelector('[modal-backdrop]');
                if (backdrop) {
                    backdrop.remove();
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('medium-modal');
                modal.addEventListener('hidden.bs.modal', function() {
                    removeBackdrop();
                });
            });
        </script>

        <div id="medium-view-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
            wire:ignore.self>
            <div class="relative w-full max-w-4xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Edit Team
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="medium-view-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </h3>
                    </div>
                    <!-- Modal body -->
                    <div class="p-3 space-y-2">
                        <div class="container mx-auto">
                            <div class="grid grid-cols-2 gap-2">
                                <div class="">
                                    <!-- Content for column 1 -->
                                    <h5 class="text-sm font-bold">Team Name</h5>
                                    <p class="text-sm">{{ $team->team_name ?? '' }}</p>
                                </div>
                                <div class="">
                                    <!-- Content for column 2 -->
                                    <h5 class="text-sm font-bold">Team Owner</h5>
                                    <p class="text-sm">{{ $team->team_owner_user ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="container overflow-auto">
                            <div class="grid grid-cols-1 gap-2 mt-4">
                                <div class="">
                                    <!-- Content for column 1 -->
                                    <h5 class="text-sm font-bold">Team Users</h5>
                                </div>
                                <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 ">
                                    <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                        <tr class="whitespace-nowrap">
                                            <th scope="col" class="px-4 py-3 normal-case">
                                                S. No.
                                            </th>

                                            <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap">
                                                Team Name
                                            </th>
                                            <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap">
                                                Team Owner
                                            </th>
                                            <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap">
                                                Active Users
                                            </th>
                                            {{-- <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap">
                                            Action
                                        </th> --}}
                                        </tr>
                                    </thead>
                                    @foreach ($teams as $index => $data)
                                        @php
                                            $data = (object) $data;
                                        @endphp
                                        <tbody>
                                            <tr
                                                class="@if ($index % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0 ">
                                                <td class="px-6 py-2">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-6 py-2">
                                                    {{ $data->team_name }}
                                                </td>
                                                <td class="px-6 ">
                                                    {{ $data->team_owner_user }}
                                                </td>
                                                <td class="px-6 ">
                                                    <p> {{ '' }} </p>
                                                </td>
                                                {{-- <td class="">
                                                <button id="dropdownDefaultButton-{{ $index }}"
                                                    data-dropdown-toggle="dropdown-{{ $index }}"
                                                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                                    type="button">Select <svg class="w-2.5  ml-2.5"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m1 1 4 4 4-4" />
                                                    </svg></button>
                                                <div id="dropdown-{{ $index }}"
                                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                                        aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                        <li>
                                                            <a href="#" wire:click="view({{ $data->id }})"
                                                                data-modal-target="medium-view-modal"
                                                                data-modal-toggle="medium-view-modal"
                                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">View</a>
                                                        </li>
                                                        <li>
                                                            <a href="#" wire:click="edit({{ $data->id }})"
                                                                data-modal-target="medium-modal"
                                                                data-modal-toggle="medium-modal"
                                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);"
                                                                wire:click="$emit('triggerDelete', {{ $data->id }})"
                                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </td> --}}
                                            </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center p-3 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="medium-view-modal" type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
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
                            @this.call('deleteTeam', id);
                            console.log('hello');
                        } else {
                            console.log("Canceled");
                        }
                    });
                });
            });

            // Event listener to detect tab change and reinitialize dropdown
            document.addEventListener('livewire:load', function() {
                Livewire.hook('message.processed', (message, component) => {
                    // initDropdown();
                    //  console.log('hello');

                    initFlowbite();
                });
            });
            document.addEventListener('livewire:load', function() {
                Livewire.on('reloadTab1', function(tabName) {
                    // Reload the page with the query parameter set to the tab name
                    window.location.href = '{{ request()->url() }}?tab=' + tabName;
                });
            });
            document.addEventListener('livewire:update', function() {
                console.log('Livewire Update');
            });
            document.addEventListener('livewire:load', function() {
                console.log('Livewire load');
            });
        </script>
    </div>
