<div>
    {{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2" defer></script> --}}
    {{-- <style>
        [x-cloak] { display: none !important; }
    </style> --}}
    <div :class="{ 'w-full': !menuBarOpen, 'md:ml-64': menuBarOpen }" :class="{'ml-0 : menuBarOpen'}" class="navbar w-auto bg-white ">



            <div class="navbar-start text-center text-xs sm:text-left sm:text-2xl pl-2 ml-[0.5rem]  ">
                <button type="button"  @click="menuBarOpen = !menuBarOpen"
                    class="flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg items-center dark:focus:ring-gray-500 group">
                    <svg x-show="menuBarOpen" class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 1 1 5l4 4m6-8L7 5l4 4" />
                    </svg>
                    <svg x-show="!menuBarOpen" class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                    </svg>
                </button>


            </div>

        <div class="navbar-center sm:text-2xl text-sm text-black">
            @php
                $routeMappings = [
                    'dashboard' => 'WELCOME TO THEPARCHI',
                    'how-theparchi-works' => 'HOW THE PARCHI WORKS',
                    'pricing' => 'PRICING',
                    'topup' => 'TOPUPS',
                    'all-feature' => 'ALL FEATURES',
                    'help' => 'HELP',
                    'stock' => 'STOCK',
                    'order-history' => 'ORDER HISTORY',
                    'active-plans' => 'ACTIVE PLANS',
                    'company-logo' => 'CUSTOMIZE',
                    'user-address' => 'USER ADDRESSES',
                    'teams' => 'TEAMS',
                    'tabs-component' => 'PROFILE',
                    'team-member' => 'TEAM MEMBER',
                    'sender' => 'SENDER',
                    'receiver' => 'RECEIVER',
                    'seller' => 'SELLER',
                    'buyer' => 'BUYER',
                    'grn' => 'RECEIPT NOTE',
                    'setting' => 'SETTINGS',
                    'checkout' => 'CHECKOUT',
                    'notification' => 'NOTIFICATION',
                    'whatsapp-logs' => 'WHATSAPP LOGS',
                    'profile' => 'PROFILE',
                    'sender/challan_series_no' => 'Challan Prefix',
                    'seller/invoice_series_no' => 'Invoice Prefix',
                ];

                $currentRoute = Route::currentRouteName();
                $includeClass = !in_array($currentRoute, ['sender', 'receiver', 'dashboard', 'grn']);
            @endphp
         <div x-data="{ open: false, showDropdown: false }" @click.away="showDropdown = false" class="relative">
             <!-- Trigger -->
             @php
             use Illuminate\Support\Str;

             $currentPath = request()->path();
             $isRestrictedRoute = in_array($currentRoute, ['receiver', 'grn', 'seller', 'stock', 'dashboard', 'pricing']) || Str::startsWith($currentPath, 'setting');
             $isSellerSentInvoice = $currentRoute === 'seller' && isset($this->persistedTemplate) && ($this->persistedTemplate === 'sent_invoice' || $this->persistedTemplate === 'sent_quotation');
         @endphp

             <div class="flex items-center">
                 <a @click="showDropdown = {{ $isSellerSentInvoice ? '!showDropdown' : 'false' }}; open = !open"
                     @if(!$isRestrictedRoute && !$isSellerSentInvoice) wire:click="panelRedirect" @endif
                     class="sm:text-2xl text-xs ml-3 text-black {{ $isRestrictedRoute || $isSellerSentInvoice ? 'cursor-pointer' : 'cursor-default' }} flex items-center">
                     {{ $routeMappings[$currentRoute] ?? '' }}
                     @if(isset($this->persistedTemplate) && $this->persistedTemplate != 'index')
                         - {{ strtoupper(str_replace('_', ' ', ucwords($this->persistedTemplate))) }}
                     @endif

                     @if($isSellerSentInvoice)
                         <svg :class="{'rotate-180': showDropdown}" class="w-5 h-11 ml-1 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                         </svg>
                     @endif
                 </a>
             </div>

             <!-- Dropdown for Seller - Sent Invoice -->
             @if($isSellerSentInvoice)
                 <div x-show="showDropdown"
                      x-transition:enter="transition ease-out duration-100"
                      x-transition:enter-start="transform opacity-0 scale-95"
                      x-transition:enter-end="transform opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="transform opacity-100 scale-100"
                      x-transition:leave-end="transform opacity-0 scale-95"
                      class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                     <div x-cloak class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                        @if($this->persistedTemplate === 'sent_invoice')
                        <a href="{{ route('seller', ['template' => 'sent_quotation']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sent Quotation</a>
                        @elseif($this->persistedTemplate === 'sent_quotation')
                            <a href="{{ route('seller', ['template' => 'sent_invoice']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sent Invoices</a>
                        @endif
                     </div>
                 </div>
             @endif

             @if(in_array($currentRoute, ['sender', 'receiver', 'seller', 'buyer', 'grn']) && !$isSellerSentInvoice)
             <div x-show="showTooltip" wire:loading.remove style="display: none;"
                  class="absolute z-10 inline-block px-3 py-2 text-xs font-medium text-gray-600 rounded-lg shadow-sm bg-white whitespace-nowrap"
                  role="tooltip">
                 Go back to {{ $currentRoute }} dashboard
                 <div class="tooltip-arrow" data-popper-arrow></div>
             </div>
             @endif
         </div>




        </div>

        <div class="navbar-end">
            @if (isset($persistedTemplate))
            @if ($persistedTemplate == 'create_challan')
            <div class="mr-3 hidden sm:block">
                <a wire:click="featureRedirect('bulk_create_challan', 'null')"
                    class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-500 rounded-xl hover:bg-green-600 focus:ring-2 focus:ring-offset-2 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    Bulk Challan
                    <svg class="w-3 h-3 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </a>
            </div>
            @elseif ($persistedTemplate == 'create_invoice')
            <div class="mr-3 hidden sm:block">
                <a wire:click="featureRedirect('bulk_create_invoice', 'null')"
                    class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-500 rounded-xl hover:bg-green-600 focus:ring-2 focus:ring-offset-2 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    Bulk Invoice
                    <svg class="w-3 h-3 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </a>
            </div>
            @endif
            {{-- @if(Route::is(['receiver', 'grn']))
                <div class="hidden md:flex navbar-center">
                    <a href="{{ route('receiver') }}" class="{{ Route::is('receiver') ? 'bg-orange text-black' : 'text-black' }} rounded-l-lg border border-gray-900 px-4 py-1 text-sm  focus:z-10">Receiver</a>
                    <a href="{{ route('grn') }}" class="{{ Route::is('grn') ? 'bg-orange text-black' : 'text-black' }} rounded-r-lg border border-gray-900 px-4 py-1 text-sm  focus:z-10">Goods Receipt</a>
                </div>
            @endif --}}
            @endif
            {{-- @if (Route::is(['pricing']))
            <div class="mr-2">
                <a href="{{ route('topup') }} "
                    class="px-2 text-sm sm:hidden text-white bg-[#dc3545] inline-block p-1 border-b-2 border-transparent rounded-xl hover:text-white dark:hover:text-white hover:bg-[#dc3545] ml-auto">Top-Ups</a>
            </div>
            @endif --}}
            @if (Route::is(['topup']))
            <div class="mr-2">
                <a href="{{ route('pricing') }} "
                    class="px-2 text-sm sm:hidden text-white bg-[#dc3545] inline-block p-1 border-b-2 border-transparent rounded-xl hover:text-white dark:hover:text-white hover:bg-[#dc3545] ml-auto">Packages</a>
            </div>
            @endif
            @php
            $routeMappings = [
            'order-history' => 'ORDER HISTORY',
            'active-plans' => 'ACTIVE PLANS',
            'company-logo' => 'COMPANY LOGO',
            'user-address' => 'USER ADDRESSES',
            'teams' => 'TEAMS',
            'tabs-component' => 'PROFILE',
            'team-member' => 'TEAM MEMBER',
            'setting' => 'SETTINGS'
            ];
            $currentRoute = Route::currentRouteName();
            $includeClass = in_array($currentRoute, ['setting', 'team-member', 'tabs-component',
            'user-address','company-logo','active-plans', 'order-history','teams', 'profile']);
            @endphp
            @if($includeClass)
            {{-- @if (Route::is(['setting'])) --}}
            <a href="#" wire:click='Logout' class="hidden sm:block" data-tooltip-target="tooltip-logout"
                data-tooltip-placement="bottom">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>

                <div id="tooltip-logout" role="tooltip"
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    Logout
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </a>
            @endif
            {{-- <button class="btn btn-ghost btn-circle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button> --}}
            {{-- <button class="btn btn-ghost btn-circle">
                <div class="indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="badge badge-xs badge-primary indicator-item"></span>
                </div>
            </button> --}}
            @if (Route::is(['sender', 'seller', 'buyer', 'receiver']) &&
                in_array($this->persistedTemplate, [
                    'sent_challan',
                    'check_balance',
                    'detailed_sent_challan',
                    'challan_design',
                    'sent_invoice'
                ]))
            <div class="flex-none sm:hidden">
                <ul class="menu menu-horizontal px-1 dropdown-end">

                    <li>
                        <details class="text-black">
                            <summary>

                            </summary>
                            <ul class="p-2 bg-white rounded-t-none dropdown-content z-40">
                                @if ($this->persistedTemplate == 'sent_challan')
                                <button type="button" wire:click="handleFeatureRoute('check_balance', null)"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check
                                    Balance</button>

                                <button type="button" wire:click="handleFeatureRoute('deleted_sent_challans', null)"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Deleted Challans</button>
                                <button type="button" wire:click="handleFeatureRoute('detailed_sent_challan', null)"
                                    class="block px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Detailed
                                    View</button>
                                {{-- <button type="button" href="{{ route('challan.exportChallan') }}"
                                    class="block px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Export</button> --}}
                                @elseif ($this->persistedTemplate == 'detailed_sent_challan')
                                        <button type="button" wire:click="handleFeatureRoute('check_balance', null)"
                                            class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check
                                            Balance</button>
                                        {{-- <button wire:click="#" type="button"
                                            class="block px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Deleted
                                            Challan</button> --}}
                                        <button type="button" wire:click="handleFeatureRoute('sent_challan', null)"
                                            class="block px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Sent Challan
                                            </button>
                                        {{-- <button type="button" href="{{ route('challan.exportDetailedChallan') }}"
                                            class="block px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Export</button> --}}

                                    @elseif($this->persistedTemplate == 'check_balance')
                                <button wire:click="handleFeatureRoute('check_balance', null)" type="button"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check
                                    Balance</button>
                                {{-- <button type="button" href="{{ route('challan.exportChallan') }}"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button> --}}
                                    <button type="button" wire:click="handleFeatureRoute('challan_design', '44')"
                                    class="block px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Challan
                                    Design</button>

                                    @elseif($this->persistedTemplate == 'sent_return_challan')
                                <button wire:click="#" type="button"
                                    wire:click="handleFeatureRoute('check_balance', null)"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check
                                    Balance</button>
                                {{-- <button type="button"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button> --}}
                                @elseif($this->persistedTemplate == 'received_return_challan')
                                <button wire:click="#" type="button"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check
                                    Balance</button>
                                {{-- <button type="button"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button> --}}
                                @elseif($this->persistedTemplate == 'all_buyer')
                                <button wire:click="#" type="button"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Check
                                    Balance</button>
                                {{-- <button type="button"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Export</button> --}}
                                @elseif ($this->persistedTemplate == 'sent_invoice')
                                <button type="button" wire:click="handleFeatureRoute('detailed_sent_invoice', null)"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed View</button>
                                <button type="button" wire:click="handleFeatureRoute('deleted_sent_invoice', 'null')"
                                    class="block px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500">Deleted Invoice</button>
                                @elseif ($this->persistedTemplate == 'deleted_invoice')
                                <button type="button" wire:click="handleFeatureRoute('sent_invoice', null)"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Sent Invoice</button>
                                    <button type="button" wire:click="handleFeatureRoute('detailed_sent_invoice', null)"
                                    class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed View</button>
                                @elseif($this->persistedTemplate == 'detailed_sent_invoice')
                                <button type="button" wire:click="handleFeatureRoute('sent_invoice', null)"
                                class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Sent Invoice</button>
                                <button type="button" wire:click="handleFeatureRoute('deleted_sent_invoice', null)"
                                class="rounded-l-lg bg-transparent px-4 py-2 text-xs text-left border-b w-full text-black  focus:z-10 focus:bg-gray-900 focus:text-white focus:ring-2 focus:ring-gray-500 dark:border-white dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700">Detailed View</button>
                                @endif
                            </ul>
                        </details>
                    </li>
                </ul>
            </div>
            @endif
        </div>

        {{-- @dump(session()->has('cart.items') == null) --}}
        @if (session()->get('cart.items') != null)


        <a class="text-dark mr-3" href="{{ route('checkout') }}" data-tooltip-target="tooltip-cart"
            data-tooltip-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-shopping-cart">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            {{-- <sup>&nbsp;3</sup> --}}
            <div id="tooltip-cart" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Cart
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </a>
        @endif

        @if (Route::is(['dashboard']))
        <livewire:notification-status.notification-status />
        @endif
    </div>
</div>
