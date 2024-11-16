<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="p-4 ">
        <div>

            <div wire:loading class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  z-30 bg-opacity-50 ">
                <span class="loading loading-spinner loading-md"></span>
            </div>
            <div id="successModal" style="display: none;">
                <div class="modal-content">
                    <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs" id="successMessage"></p>
                </div>
            </div>
            <div id="errorModal" style="display: none;">
                <div class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs">
                    <p class="mt-3 " id="errorMessage">\
                    </p>
                </div>
            </div>

            <div class="max-w-5xl mt-4 mx-auto">
                @if(Auth::getDefaultDriver() !== 'team-user')
                <div class="rounded-xl mt-4 border bg-white p-4 border-gray-200 shadow dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <div class="grid gap-6 mb-4 md:grid-cols-2" wire:ignore.self>
                        <div>
                            <label for="team" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">
                                Select Team
                                {{ $team_name != '' ? '( ' . $team_name . ' )' : '' }}
                            </label>
                            <div x-data="{ search: '', selectedTeam: null }" wire:ignore.self>
                                <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom" data-dropdown-trigger="click"
                                    class="text-white flex w-full bg-[#24292F] hover:bg-[#24292F]/90 focus:ring-4 focus:outline-none focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center dark:focus:ring-gray-500 dark:hover:bg-[#050708]/30 mr-2 mb-2"
                                    type="button">
                                    {{ $team_name != '' ?  $team_name  : 'Click To Choose' }}
                                    <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>
                                <div x-data="{ search: '', selectedTeam: null }" id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-100 dark:bg-gray-700">
                                    <div class="p-3">
                                        <label for="input-group-search" class="sr-only">Search</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                </svg>
                                            </div>
                                            <input x-model="search" type="text" id="input-group-search"
                                                class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Search team" onfocus="this.placeholder = ''" required>
                                        </div>
                                    </div>
                                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">
                                        @foreach ($teams as $team)
                                            <li class="cursor-pointer"
                                                x-show="search === '' || '{{ strtolower($team->team_name ?? null) }}'.includes(search.toLowerCase())"
                                                wire:click.prevent="selectTeam({{ json_encode($team) }})">
                                                <div class="flex items-center pl-2 rounded hover:bg-gray-100 cursor-pointer dark:hover:bg-gray-600">
                                                    <label class="w-full py-2 ml-2 text-sm font-medium text-gray-900 rounded cursor-pointer dark:text-gray-300">{{ $team->team_name ?? null }}</label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <a href="{{ route('teams') }}" class="flex w-full items-center p-3 text-sm font-medium text-green-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-500 hover:underline">
                                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                            <path d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-2V5a1 1 0 0 0-2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 0 0 2 0V9h2a1 1 0 1 0 0-2Z" />
                                        </svg>
                                        Add team
                                    </a>
                                </div>
                            </div>
                            @error('team_member.team_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="name" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">
                                Name <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="name" wire:model.defer='team_member.team_user_name'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Name" required>
                            @error('team_member.team_user_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid gap-6 mb-4 md:grid-cols-2">
                        <div>
                            <label for="email" class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                Email address
                            </label>
                            <input type="email" id="email" wire:model.defer='team_member.email' autocomplete="off"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Email" required>
                                <div id="email_error" class="text-red-500 text-sm mt-1"></div>
                            @error('team_member.email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                Phone
                            </label>
                            <input type="number" maxlength="10"  id="phone" autocomplete="off" wire:model.defer='team_member.phone'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Phone Number" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);" >
                                <div id="phone_error"
                                class="text-red-500 text-[0.6rem] sm:text-xs mt-1"> </div>
                        </div>
                        </div>
                        <div class="grid gap-6 mb-4 md:grid-cols-1">
                            <div class="relative">
                                <label for="password" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">
                                    Password <span class="text-red-600">*</span>
                                </label>
                                <input type="password" id="password" wire:model.defer='team_member.password'
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="*******" required>
                                <p id="passwordError" class="text-red-500 text-xs mt-1 hidden">Password should be at least 8 characters</p>
                                @error('team_member.password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <button id="saveButton"  type="button" wire:click='createTeamMember'
                            class="text-white bg-gray-300 cursor-not-allowed focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 mr-2 mb-2 dark:bg-gray-300 dark:focus:ring-gray-700 dark:border-gray-700"
                            disabled>
                            Save
                        </button>
                        </div>

                        <script>
                            document.addEventListener('livewire:update', function () {
                            const saveButton = document.getElementById('saveButton');
                            const teamDropdown = document.getElementById('dropdownSearchButton');
                            const nameInput = document.getElementById('name');
                            // const phoneInput = document.getElementById('phone');
                            const passwordInput = document.getElementById('password');
                            // const phoneError = document.getElementById('phoneError');
                            const passwordError = document.getElementById('passwordError');

                            function validateForm() {
                                const isTeamSelected = teamDropdown.textContent.trim() !== 'Click To Choose';
                                const isNameFilled = nameInput.value.trim() !== '';
                                // const isPhoneValid = phoneInput.value.trim().length === 10;
                                const isPasswordValid = passwordInput.value.trim().length >= 8;

                                // if (phoneInput === document.activeElement) {
                                //     phoneError.classList.toggle('hidden', isPhoneValid);
                                // }

                                if (passwordInput === document.activeElement) {
                                    passwordError.classList.toggle('hidden', isPasswordValid);
                                }

                                // if (isTeamSelected && isNameFilled && isPhoneValid && isPasswordValid) {
                                if (isTeamSelected && isNameFilled  && isPasswordValid) {
                                    saveButton.disabled = false;
                                    saveButton.classList.remove('bg-gray-300', 'cursor-not-allowed');
                                    saveButton.classList.add('bg-gray-800', 'hover:bg-gray-900');
                                } else {
                                    saveButton.disabled = true;
                                    saveButton.classList.remove('bg-gray-800', 'hover:bg-gray-900');
                                    saveButton.classList.add('bg-gray-300', 'cursor-not-allowed');
                                }
                            }

                            // Event listeners for real-time validation
                            teamDropdown.addEventListener('click', validateForm);
                            nameInput.addEventListener('input', validateForm);
                            // phoneInput.addEventListener('focus', validateForm);
                            // phoneInput.addEventListener('input', validateForm);
                            passwordInput.addEventListener('focus', validateForm);
                            passwordInput.addEventListener('input', validateForm);
                        });
                        document.addEventListener('team-member-created', function () {
                            location.reload();
                        });
                        function initializeEmailValidation() {
                                const emailInput = document.getElementById('email');
                                const emailError = document.getElementById('email_error');
                                const addButton = document.getElementById('saveButton');

                                if (emailInput) {
                                    emailInput.addEventListener('input', function () {
                                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                        if (emailInput.value && !emailPattern.test(emailInput.value)) {
                                            emailError.textContent = 'Invalid email address.';
                                            emailInput.classList.add('border-red-500');
                                            addButton.disabled = true;
                                            addButton.classList.add('cursor-not-allowed');
                                            addButton.classList.add('opacity-50');
                                        } else {
                                            emailError.textContent = '';
                                            emailInput.classList.remove('border-red-500');
                                            addButton.disabled = false;
                                            addButton.classList.remove('cursor-not-allowed');
                                            addButton.classList.remove('opacity-50');
                                        }
                                    });
                                }
                            }

                            function initializePhoneValidation() {
                                const phoneInput = document.getElementById('phone');
                                const phoneError = document.getElementById('phone_error');
                                const addButton = document.getElementById('saveButton');

                                if (phoneInput) {
                                    phoneInput.addEventListener('input', function () {
                                        const phonePattern = /^\d{10}$/;
                                        if (phoneInput.value && !phonePattern.test(phoneInput.value)) {
                                            phoneError.textContent = 'Phone number must be 10 digits.';
                                            phoneInput.classList.add('border-red-500');
                                            addButton.disabled = true;
                                            addButton.classList.add('cursor-not-allowed');
                                            addButton.classList.add('opacity-50');
                                        } else {
                                            phoneError.textContent = '';
                                            phoneInput.classList.remove('border-red-500');
                                            addButton.disabled = false;
                                            addButton.classList.remove('cursor-not-allowed');
                                            addButton.classList.remove('opacity-50');
                                        }
                                    });
                                }
                            }

                            document.addEventListener('DOMContentLoaded', function () {
                                initializeEmailValidation();
                                initializePhoneValidation();
                            });

                            document.addEventListener('livewire:load', function () {
                                initializeEmailValidation();
                                initializePhoneValidation();
                            });

                            document.addEventListener('livewire:update', function () {
                                initializeEmailValidation();
                                initializePhoneValidation();
                            });
                        </script>

                @endif
                <div x-data="{
                    errorMessage: @entangle('errorMessage'),
                    successMessage: @entangle('successMessage'),
                    resetMessages() {
                        setTimeout(() => {
                            this.errorMessage = null;
                            this.successMessage = null;
                        }, 10000);
                    }
                }" x-init="resetMessages">
                    @if ($errorMessage)
                        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" class="bg-red-200 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline" x-text="errorMessage"></span>
                        </div>
                    @endif
                    @if ($successMessage)
                        <div x-show="successMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" class="bg-green-200 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 mt-4 p-4 rounded-xl shadow text-black" role="alert">
                            <span class="block sm:inline" x-text="successMessage"></span>
                        </div>
                    @endif
                </div>

                <div class="rounded-xl mt-4 border bg-white p-4 border-gray-200  shadow  dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                    wire:ignore.self>
                    <div class="bg-white rounded-lg shadow overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 ">
                            <thead
                                class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="whitespace-nowrap">
                                    <th scope="col" class="px-4 py-3 normal-case text-sm font-semibold">
                                        S. No.
                                    </th>
                                    <th scope="col" class="px-4 py-3 normal-case text-sm font-semibold">
                                        Unique Login Id
                                    </th>
                                    <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">
                                        Member Name
                                    </th>
                                    <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">
                                        Email
                                    </th>
                                    <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">
                                        Phone
                                    </th>
                                    <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">
                                        Team
                                    </th>
                                    @if(Auth::getDefaultDriver() !== 'team-user')
                                    <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">
                                        Rights
                                    </th>
                                    <th scope="col" class="va-b px-4 py-3 capitalize whitespace-nowrap text-sm font-semibold">
                                        Action
                                    </th>
                                    @endif
                                </tr>
                            </thead>
                            @foreach ($teamMembers as $index => $member)
                                @php
                                    $member = (object) $member;
                                    $member->team = (object) $member->team;
                                @endphp
                                <tbody>
                                    <tr
                                        class="@if ($index % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0 ">
                                        <td class="px-6 text-xs text-black py-2">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 text-xs text-black ">
                                            {{ $member->unique_login_id }}
                                        </td>
                                        <td class="px-6 text-xs text-black">
                                            {{ $member->team_user_name }}
                                        </td>
                                        <td class="px-6 text-xs text-black ">
                                            {{ $member->email }}
                                        </td>
                                        <td class="px-6 text-xs text-black ">
                                            <p> {{ $member->phone }} </p>
                                        </td>
                                        <td class="px-6 text-xs text-black ">
                                            @if(isset($member->team) && isset($member->team->team_name))
                                                <p> {{ $member->team->team_name }} team</p>
                                            @endif
                                        </td>
                                        {{-- <td class="">
                                            <button id="dropdownDefaultButton-{{ $index }}"
                                                data-dropdown-toggle="dropdown-{{ $index }}"
                                                class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                                type="button">Select <svg class="w-2.5  ml-2.5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                            <div id="dropdown-{{ $index }}"
                                                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                                    aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    <li>
                                                        <a href="#" wire:click="view({{ $member->id }})"
                                                            data-modal-target="assign-feature-modal"
                                                            data-modal-toggle="assign-feature-modal"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Assign
                                                            Feature</a>
                                                    </li>

                                                    <li>
                                                        <a href="javascript:void(0);"
                                                            wire:click="$emit('triggerDelete', {{ $member->id }})"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </td> --}}
                                        {{-- @dump( Auth::getDefaultDriver()) --}}
                                        @if(Auth::getDefaultDriver() !== 'team-user')
                                            <td>
                                                    <a onclick="my_modal_3.showModal()"  wire:click="view({{ $member->id }})"

                                                        class=" block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white bg-[#24292F] rounded-xl text-white hover:bg-orange hover:text-black">Assign
                                                        Rights</a>
                                            </td>
                                            {{-- <a href="#my_modal_8" class="btn">open modal</a> --}}
                                            {{-- <td></td> --}}
                                            {{-- onclick="my_modal_4.showModal()" --}}
                                            {{-- <td class="flex">
                                                <svg  class="w-5 h-5 mx-auto   text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M5 8a4 4 0 1 1 7.8 1.3l-2.5 2.5A4 4 0 0 1 5 8Zm4 5H7a4 4 0 0 0-4 4v1c0 1.1.9 2 2 2h2.2a3 3 0 0 1-.1-1.6l.6-3.4a3 3 0 0 1 .9-1.5L9 13Zm9-5a3 3 0 0 0-2 .9l-6 6a1 1 0 0 0-.3.5L9 18.8a1 1 0 0 0 1.2 1.2l3.4-.7c.2 0 .3-.1.5-.3l6-6a3 3 0 0 0-2-5Z" clip-rule="evenodd"/>
                                                </svg>


                                                <svg  wire:click="$emit('triggerDelete', {{ $member->id }})" class="w-5 mx-auto text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                                                </svg>


                                            </td> --}}
                                            <!-- You can open the modal using ID.showModal() method -->


                                            <td class="">
                                                <button id="dropdownDefaultButton-{{ $index }}"
                                                    data-dropdown-toggle="dropdown-{{ $index }}"
                                                    class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 py-1.5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                                    type="button">Select <svg class="w-2.5  ml-2.5" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                    </svg></button>
                                                <div id="dropdown-{{ $index }}"
                                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                                        aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                        <li>
                                                            <a href="javascript:void(0);" onclick="my_modal_1.showModal()" wire:click.prevent="selectTeamMember('{{  ($member->id) ?? '' }}')"  class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Change Password</a>
                                                        <li>
                                                            <a href="javascript:void(0);"
                                                                wire:click="$emit('triggerDelete1', {{ $member->id }})"
                                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" wire:click="editTeamMember({{ json_encode($member) }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
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
    </div>

<!-- Put this part before </body> tag -->

{{-- EDIT TEAM  --}}
<div x-data="{ isOpen: @entangle('isOpen') }"
            x-show="isOpen"
            x-on:keydown.escape.window="isOpen = false"
            x-on:close.stop="isOpen = false"
            class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
            <div class="bg-white p-6 rounded shadow-lg w-80 sm:w-96">
                <div class="mb-4">
                    <h1 class="text-lg text-black border-b border-gray-400">{{ $modalHeading }}</h1>


                    <div  >
                        <div class="relative w-full min-w-[200px] h-10 mt-5">
                          <input wire:model.defer="team_user_name"
                            class="peer w-full h-10 bg-transparent text-black text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                            placeholder=" " /><label
                            class="flex w-full h-full text-black select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">Username
                          </label>
                        </div>
                    </div>


                    <div >
                        <div class="relative w-full min-w-[200px] h-10 mt-5">
                          <input wire:model.defer="email" id="email"
                            class="peer w-full h-10 bg-transparent text-black text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                            placeholder=" " /><label
                            class="flex w-full h-full text-black select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">Email
                          </label>
                          <div id="email_error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>
                    <div  >
                        <div class="relative w-full min-w-[200px] h-10 mt-5">
                          <input wire:model.defer="phone" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"
                           id="phone" class="peer w-full h-10 bg-transparent text-black text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 border-t-transparent focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                            placeholder=" " /><label
                            class="flex w-full h-full text-black select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">Phone
                          </label>
                          <div id="phone_error"
                                class="text-red-500 text-[0.6rem] sm:text-xs mt-1"> </div>
                        </div>
                    </div>

                </div>
                <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                    <button x-on:click="isOpen = false"
                            class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        Cancel
                    </button>
                    <button wire:click="{{ $modalAction }}"
                            class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs   text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                        {{ $modalButtonText }}
                    </button>
                </div>
            </div>
            <div x-on:click.self="isOpen = false" class="inset-0 bg-black opacity-50"></div>
        </div>
        <dialog id="my_modal_3" class="modal" wire:ignore.self>
        <div class="modal-box w-11/12 max-w-2xl overflow-auto bg-white text-black pt-2" >
            <div class="sticky top-0 z-10 bg-white">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="font-bold text-lg">Edit Team</h3>
            </div>
            <div class="p-3 space-y-2">
                @php
                    $teamMember = json_decode($this->teamMember);
                    // dd($teamMember);
                    $teamMemberPermissions = json_decode($this->teamMemberPermissions);
                @endphp
                <div class="max-w-5xl mt-4 mx-auto">
                    @if ($errorMessage)
                        @foreach (json_decode($this->errorMessage) as $error)
                            <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                                role="alert">
                                <span class="font-medium">Error:</span> {{ $error }}
                            </div>
                        @endforeach
                    @endif
                    @if ($successMessage)
                        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
                            role="alert">
                            <span class="font-medium">Success:</span> {{ $successMessage }}
                        </div>
                    @endif
                </div>
                <div class="container mx-auto">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="">
                            <!-- Content for column 1 -->
                            <h5 class="text-sm font-bold">Member Name</h5>
                            <p class="text-sm">{{ $teamMember->team_user_name ?? '' }}</p>
                        </div>
                        <div class="">
                            <!-- Content for column 2 -->
                            <h5 class="text-sm font-bold">Team</h5>
                            <p class="text-sm">{{ $teamMember->team->team_name ?? '' }}</p>
                        </div>
                        <div class="">
                            <!-- Content for column 1 -->
                            <h5 class="text-sm font-bold">Email</h5>
                            <p class="text-sm">{{ $teamMember->email ?? '' }}</p>
                        </div>
                        <div class="">
                            <!-- Content for column 2 -->
                            <h5 class="text-sm font-bold">Phone</h5>
                            <p class="text-sm">{{ $teamMember->phone ?? '' }}</p>
                        </div>
                    </div>
                </div>
                @if ($teamMember)
                    <div class="container">
                        <div class="grid grid-cols-1 gap-2 mt-4">
                            <div class="bg-white rounded-lg shadow p-2">
                                <div class="bg-white rounded-t-lg">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-base font-semibold">Store permissions</h3>
                                        <a href="#" target="_blank" class="text-blue-600"
                                            rel="noopener noreferrer">

                                        </a>
                                    </div>
                                    <p class="text-xs text-gray-600">Manage permissions for My Panel</p>
                                </div>

                                <div class="pt-4">
                                    {{-- {{dd($teamMemberPermissions)}} --}}
                                    @if(isset($teamMemberPermissions->permission))

                                    @foreach (json_decode($teamMemberPermissions->permission) as $panelKey => $panel)
                                        <div class="mb-2 pb-2 border-b ">

                                            <div x-data="{ open: false }" x-init="() => { $watch('open', value => value ? $dispatch('open-accordion', {{ $panelKey }}) : $dispatch('close-accordion', {{ $panelKey }})) }">
                                                <h2 id="accordion-collapse-{{ $panelKey }}-heading-1">
                                                    <button x-on:click="open = !open"
                                                        class="flex justify-between w-full pt-2 pb- font-medium text-left text-gray-500 rounded-t-xl focus:ring-gray-200 dark:focus:ring-gray-800 dark:text-gray-400"
                                                        aria-expanded="false"
                                                        aria-controls="accordion-collapse-{{ $panelKey }}-body-1">
                                                        <label class="flex items-center">
                                                            {{-- <input type="checkbox"
                                                                class="form-checkbox text-blue-500"> --}}
                                                            <span
                                                                class="ml-2 text-sm font-semibold text-black">{{ ucfirst($panelKey) }}</span>
                                                        </label>
                                                        <div class="flex items-center pl-2">
                                                            {{-- <span class="ml-auto pr-2">0/8</span> --}}
                                                            <svg x-bind:class="{ 'rotate-180': !open }"
                                                            class="w-3 h-3 shrink " aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 10 6">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>

                                                    </svg>


                                                        </div>
                                                    </button>
                                                </h2>
                                                <div x-show="open"
                                                    id="accordion-collapse-{{ $panelKey }}-body-1"
                                                    aria-labelledby="accordion-collapse-{{ $panelKey }}-heading-1">
                                                    @foreach ($panel as $perKey => $permission)
                                                    <div class="flex items-center pl-5 pt-3">
                                                        <input
                                                            id="checkbox-item-{{ str_replace('_', '-', $perKey) }}"
                                                            type="checkbox"
                                                            x-model="checkboxStates.{{ $perKey }}"
                                                            wire:click='assign({{ $teamMemberPermissions->team_user_id }},"{{ $panelKey }}","{{ $perKey }}",{{ $permission == 0 ? 1 : 0 }})'
                                                            @if ($permission == 1) checked="true" @else @endif
                                                            class="w-4 h-4  text-blue-600 bg-gray-100   focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2">
                                                        <label
                                                            for="checkbox-item-{{ str_replace('_', '-', $perKey) }}"
                                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $perKey)) }}</label>
                                                    </div>
                                                @endforeach
                                                </div>
                                            </div>
                                            </div>

                                    @endforeach
                                    @endif
                                    <!-- Add more categories here -->
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </dialog>


    {{-- MODAL --}}

    <!-- Default Modal -->


<!-- Modal toggle -->
{{-- <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
    Toggle modal
  </button> --}}
  {{-- <button class="btn" onclick="my_modal_1.showModal()">open modal</button> --}}
    <dialog id="my_modal_1" class="modal" wire:ignore.self>
        <div class="modal-box bg-white">
            <h3 class="font-bold text-lg">Change Password</h3>
            <div class="p-4 md:p-5 space-y-4" x-data="{ passwordVisible: false, passwordConfirmVisible: false }">
                @if ($errorMessage)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ $errorMessage }}</span>
                    </div>
                @endif
                @if ($successMessage)
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ $successMessage }}</span>
                    </div>
                @endif
                <div class="mb-6 relative">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New Password</label>
                    <input wire:model.defer="password" :type="passwordVisible ? 'text' : 'password'"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="•••••••••" required onfocus="this.placeholder = ''" onblur="this.placeholder = '•••••••••'">
                    <button type="button" @click="passwordVisible = !passwordVisible" class="absolute inset-y-0 right-0 pr-3 flex items-center py-12 text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                        <svg x-show="!passwordVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.9120000000000001"></g><g id="SVGRepo_iconCarrier"> <path d="M3 14C3 9.02944 7.02944 5 12 5C16.9706 5 21 9.02944 21 14M17 14C17 16.7614 14.7614 19 12 19C9.23858 19 7 16.7614 7 14C7 11.2386 9.23858 9 12 9C14.7614 9 17 11.2386 17 14Z" stroke="#000000" stroke-width="0.4800000000000001" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                        <svg x-show="passwordVisible" class="h-5 w-5"viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <path d="M2.99902 3L20.999 21M9.8433 9.91364C9.32066 10.4536 8.99902 11.1892 8.99902 12C8.99902 13.6569 10.3422 15 11.999 15C12.8215 15 13.5667 14.669 14.1086 14.133M6.49902 6.64715C4.59972 7.90034 3.15305 9.78394 2.45703 12C3.73128 16.0571 7.52159 19 11.9992 19C13.9881 19 15.8414 18.4194 17.3988 17.4184M10.999 5.04939C11.328 5.01673 11.6617 5 11.9992 5C16.4769 5 20.2672 7.94291 21.5414 12C21.2607 12.894 20.8577 13.7338 20.3522 14.5" stroke="#000000" stroke-width="0.384" stroke-linecap="round" stroke-linejoin="round"/> </g>
                        </svg>
                    </button>
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-6 relative ">
                    <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm New password</label>
                    <input :type="passwordConfirmVisible ? 'text' : 'password'" id="confirm_password" wire:model.defer="password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="•••••••••" required onfocus="this.placeholder = ''" onblur="this.placeholder = '•••••••••'">
                    <button type="button" @click="passwordConfirmVisible = !passwordConfirmVisible" class="absolute inset-y-0 right-0 pr-3 flex items-center py-12 text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                        <svg x-show="!passwordConfirmVisible" class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.9120000000000001"></g><g id="SVGRepo_iconCarrier"> <path d="M3 14C3 9.02944 7.02944 5 12 5C16.9706 5 21 9.02944 21 14M17 14C17 16.7614 14.7614 19 12 19C9.23858 19 7 16.7614 7 14C7 11.2386 9.23858 9 12 9C14.7614 9 17 11.2386 17 14Z" stroke="#000000" stroke-width="0.4800000000000001" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                        <svg x-show="passwordConfirmVisible" class="h-5 w-5"viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <path d="M2.99902 3L20.999 21M9.8433 9.91364C9.32066 10.4536 8.99902 11.1892 8.99902 12C8.99902 13.6569 10.3422 15 11.999 15C12.8215 15 13.5667 14.669 14.1086 14.133M6.49902 6.64715C4.59972 7.90034 3.15305 9.78394 2.45703 12C3.73128 16.0571 7.52159 19 11.9992 19C13.9881 19 15.8414 18.4194 17.3988 17.4184M10.999 5.04939C11.328 5.01673 11.6617 5 11.9992 5C16.4769 5 20.2672 7.94291 21.5414 12C21.2607 12.894 20.8577 13.7338 20.3522 14.5" stroke="#000000" stroke-width="0.384" stroke-linecap="round" stroke-linejoin="round"/> </g>
                        </svg>
                    </button>
                    @error('password_confirmation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
                </form>
                <button class="btn bg-gray-800 text-white rounded-lg " wire:click="changePassword">Change Password</button>
            </div>
        </div>
    </dialog>
  <!-- Small Modal -->



</div>

{{-- <canvas id="signature-board" width="500" height="200"></canvas>

<form id="signature-form" method="POST" action="{{ url('/signature/save') }}">
    @csrf
    <input type="hidden" name="signatureData" id="signature-data" value="">

    <button type="submit">Save Signature</button>
</form>

<script>
    var canvas = document.getElementById('signature-board');
    var ctx = canvas.getContext('2d');

    var mouse = { x: 0, y: 0 };
    var isDrawing = false;

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    function startDrawing(event) {
        isDrawing = true;
        var rect = canvas.getBoundingClientRect();
        mouse.x = event.clientX - rect.left;
        mouse.y = event.clientY - rect.top;
    }

    function draw(event) {
        if (isDrawing) {
            var rect = canvas.getBoundingClientRect();
            ctx.beginPath();
            ctx.moveTo(mouse.x, mouse.y);
            mouse.x = event.clientX - rect.left;
            mouse.y = event.clientY - rect.top;
            ctx.lineTo(mouse.x, mouse.y);
            ctx.stroke();
        }
    }

    function stopDrawing() {
        isDrawing = false;
        document.getElementById('signature-data').value = canvas.toDataURL();
    }
</script> --}}

<script type="text/javascript">

window.addEventListener('close-modal', event => {
    document.getElementById('my_modal_1').close();
});

 window.addEventListener('show-error-message', event => {
            // Set the message in the modal
            document.getElementById('errorMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('errorModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('errorModal').style.display = 'none';
            }, 15000);
        });

        window.addEventListener('show-success-message', event => {
            // Set the message in the modal
            document.getElementById('successMessage').textContent = event.detail[0];

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 15000);
        });


    document.addEventListener('livewire:update', function(e) {
        @this.on('triggerDelete1', (id) => {
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6fc5e0",
                cancelButtonColor: "#d33",
                confirmButtonText: "Delete",
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteTeamMember', id);
                    console.log('hello');
                } else {
                    console.log("Canceled");
                }
            });
        });
    });
    // const passwordInput = document.getElementById('password');
    // const togglePassword = document.getElementById('togglePassword');

    // togglePassword.addEventListener('click', () => {
    //     const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    //     passwordInput.setAttribute('type', type);
    // });


    // function toggleAllCheckboxes() {
    //     Object.keys(checkboxStates).forEach(key => {
    //         checkboxStates[key] = masterCheckbox;
    //     });
    // }
</script>

