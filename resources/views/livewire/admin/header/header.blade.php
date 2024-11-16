<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 lg:px-5 lg:pl-3 sm:py-0 lg:py-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class=" ml-24 md:mr-24 hidden sm:block">
                    <img src="/image/Vector.png" class="h-8 mr-3 " alt="TheParrchi" />
                    <!-- <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">TheParchi</span> -->
                </a>
            </div>
            <div class="text-center text-xs sm:text-left sm:text-2xl ">
                <div class="hidden sm:block">
                    @if (Route::is('dashboard'))
                        
                    WELCOME TO THEPARCHI
                    @elseif (Route::is('sender'))
                    SENDER DASHBOARD
                    @elseif (Route::is('receiver'))
                    RECEIVER DASHBOARD
                    @elseif (Route::is('seller'))
                    SELLER DASHBOARD
                    @elseif (Route::is('seller'))
                    BUYER DASHBOARD
                    @elseif (Route::is('buyer'))
                    @endif
                </div>
                <!-- Other content for both small and large screens -->
            </div>

            <div class="flex items-right">
                <div class="flex items-center ml-3">
                    @if (isset($persistedTemplate))
                    @if ($persistedTemplate == 'create_challan')
                    <div class="mr-3">
                        <a wire:click="featureRedirect('bulk_create_challan', 'null')" class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-500 rounded hover:bg-green-600 focus:ring-2 focus:ring-offset-2 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            Bulk Challan
                            <svg class="w-3 h-3 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                            </svg>
                        </a>
                    </div>
                    @endif
                    @endif

                    <div class="mr-2">
                        <p class="text-sm text-gray-900 dark:text-white hidden sm:block" role="none">
                            {{-- Hi, {{Auth::user()->name??Auth::user()->team_user_name}} --}}
                        </p>
                    </div>
                    <div class="flex justify-items-center">
                        {{-- <a href="{{ route('setting') }}" class="hidden sm:flex">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                        </a> --}}
                        <a href="#" wire:click='Logout' data-tooltip-target="tooltip-logout" data-tooltip-placement="bottom"  class="hidden sm:block">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>

                            <div id="tooltip-logout" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Logout
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </a>
                        <!-- @dump(isset($persistedTemplate)); -->
                        @if (isset($persistedTemplate))
                        @if ($this->persistedTemplate == 'sent_challan' || $this->persistedTemplate == 'sent_return_challan' || $this->persistedTemplate == 'check_balance' || $this->persistedTemplate == 'sent_return_challan' || $this->persistedTemplate == 'sent_return_challan')
                        <div class="sm:hidden">
                            <div class="flex rounded-md pb-2 shadow-sm justify-end" role="group">
                                <div class="relative inline-block text-gray-900">
                                    <button type="button" class="bg-transparent px-4 py-2 text-sm font-medium focus:z-10 focus:text-black focus:ring-gray-500 dark:border-white dark:text-white dark:hover:text-white" id="optionsMenu">
                                        <span class="sr-only">Options</span>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4zm0-4a2 2 0 100-4 2 2 0 000 4zm0 8a2 2 0 100-4 2 2 0 000 4z"></path>
                                        </svg>
                                    </button>
                                    <div class="hidden origin-top-right absolute right-0 mt-2 w-48 bg-[#464647] border border-gray-300 rounded-lg shadow-lg z-10" id="optionsMenuContent">
                                        @if($this->persistedTemplate == 'sent_challan')
                                        <button type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check Balance</button>
                                        <button wire:click="innerFeatureRedirect('check_balance', null)" type="button" class="block px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Challan Design</button>
                                        <button wire:click="#" type="button" class="block px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Deleted Challan</button>
                                        <button wire:click="#" type="button" class="block px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Detailed View</button>
                                        <button type="button" class="block px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Export</button>

                                        @elseif($this->persistedTemplate == 'check_balance')
                                        <button wire:click="innerFeatureRedirect('check_balance', null)" type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check Balance</button>
                                        <button type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button>


                                         @elseif($this->persistedTemplate == 'sent_return_challan')
                                        <button wire:click="#" type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check Balance</button>
                                        <button type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button>

                                        @elseif($this->persistedTemplate == 'received_return_challan')
                                        <button wire:click="#" type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check Balance</button>
                                        <button type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button>

                                        @elseif($this->persistedTemplate == 'all_buyer')
                                        <button wire:click="#" type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check Balance</button>
                                        <button type="button" class="rounded-l-lg bg-transparent px-4 py-2 text-sm font-medium text-white  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button>


                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                        @endif
                        @endif

                        <!-- <button type="button"
                            class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <img class="w-8 h-8 rounded-full"
                                src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="user photo">
                        </button>  -->


                    </div>
                </div>
            </div>
        </div>
</nav>
<script>
    document.getElementById('optionsMenu').addEventListener('click', function() {
        var optionsMenuContent = document.getElementById('optionsMenuContent');
        optionsMenuContent.classList.toggle('hidden');
    });
</script>
