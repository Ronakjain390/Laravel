<div class="h-full px-2 mt-16  overflow-y-auto">
    <ul class="space-y-2 font-bold">
        <li>
            <a href="#"
                class="flex items-center px-2 py-1 border-gray-400 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg dark:focus:ring-gray-500 group text-center">

                <span class="ml-auto text-blue-800 font-semibold capitalize ">Hey 
                    {{-- {{ Auth::user()->company_name ?? (Auth::user()->name ?? Auth::user()->team_user_name) }} --}}
                     Admin !!</span>
                <svg width="24" height="24" class="mr-auto" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M14.82 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H9.18C9.6 1.84 10.7 1 12 1C13.3 1 14.4 1.84 14.82 3ZM13 4C13 3.45 12.55 3 12 3C11.45 3 11 3.45 11 4C11 4.55 11.45 5 12 5C12.55 5 13 4.55 13 4ZM12 7C13.66 7 15 8.34 15 10C15 11.66 13.66 13 12 13C10.34 13 9 11.66 9 10C9 8.34 10.34 7 12 7ZM6 17.6V19H18V17.6C18 15.6 14 14.5 12 14.5C10 14.5 6 15.6 6 17.6Z"
                        fill="#2F80ED" />
                </svg>
            </a>
        </li>
        {{-- @dd($sidenav); --}} 
        @if (isset($sidenav))
            @foreach ($sidenav as $nav)
                @php
                    $user = json_decode($this->user);
                    $template = isset($nav['template']) ? $nav['template']['template_page_name'] : 'index';
                    // dd($user->team_user->permissions->permission->{strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']))}->{strtolower(str_replace(' ', '_', $nav['feature_name']))}, $sidenav, $nav);
                    // dd();
                @endphp
                @if (isset(
                        $user->team_user->permissions->permission->{strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']))}->{strtolower(str_replace(' ', '_', $nav['feature_name']))}))
                    @if ($user->team_user->permissions->permission->{strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']))}->{strtolower(str_replace(' ', '_', $nav['feature_name']))} == 1)
                        <li>
                            <a style="background: #F8F6F3!important; color:#747272!important;"
                                wire:click="featureRedirect('{{ $template }}', '{{ $nav['id'] }}')"
                                @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                         disabled @endif
                                @endif
                                class="
                        @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                        cursor-not-allowed
                        @else @endif
            @else

            @endif
                        flex px-1 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg text-sm items-center dark:focus:ring-gray-500 group drop-shadow-lg">
                                @if (isset($nav['feature_icon']))
                                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 22 21">
                                        <path
                                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                        <path
                                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                    </svg>
                                @endif
                                <span class="flex-1 ml-1 whitespace-nowrap text-sm">{{ $nav['feature_name'] }}</span>
                                @if ($nav['status'] != 'active')
                                    <span
                                        class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs font-bold mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Disabled</span>
                                @endif
                                @if ($nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0)
                                    @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                                        <span
                                            class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs font-bold mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Expired</span>
                                    @endif
                                @endif
                                @if (isset($nav['total_count']))
                                    <span
                                        class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs font-bold text-green-800 bg-green-200 rounded-full dark:bg-green-900 dark:text-green-300">{{ $nav['total_count'] }}</span>
                                @endif
                            </a>

                        </li>
                    @endif
                @else
                    <li>
                        <a style="background: #F8F6F3!important; color:#747272!important;"
                            wire:click="featureRedirect('{{ $template }}', '{{ $nav['id'] }}')"
                            @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                         disabled @endif
                            @endif
                            class="
                        @if ($nav['status'] != 'active' || $nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0) @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                        cursor-not-allowed
                        @else @endif
                            @else
                            @endif
                        flex px-1 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg text-sm items-center dark:focus:ring-gray-500 group drop-shadow-lg">
                            @if (isset($nav['feature_icon']))
                                <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                            @endif
                            <span class="flex-1 ml-1 whitespace-nowrap text-sm">{{ $nav['feature_name'] }}</span>
                            @if ($nav['status'] != 'active')
                                <span
                                    class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs font-bold mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Disabled</span>
                            @endif
                            @if ($nav['total_available_usage'] + $nav['total_available_usage_topup'] == 0)
                                @if ($nav['total_usage_limit'] != null && $nav['total_usage_limit_topup'] != null)
                                    <span
                                        class="inline-flex items-center justify-center bg-blue-200 text-blue-800 text-xs font-bold mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Expired</span>
                                @endif
                            @endif
                            @if (isset($nav['total_count']))
                                <span
                                    class="inline-flex items-center justify-center w-3 h-3 p-3 text-xs font-bold text-green-800 bg-green-200 rounded-full dark:bg-green-900 dark:text-green-300">{{ $nav['total_count'] }}</span>
                            @endif
                        </a>

                    </li>

                @endif
            @endforeach
            {{-- <li>
                <a style="background: #F8F6F3!important; color:#747272!important;"
                    wire:click="featureRedirect('challan_design', '44')"
                    class=" flex drop-shadow-lg px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold text-sm rounded-lg  items-center dark:focus:ring-gray-500 group">
                    <span class="">Challan Design</span>
                </a>
            </li> --}}
        @endif
        {{-- <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('all-users') }}"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">All Users</span>
            </a>
        </li> --}}
        <li>
            <button style="background: #F8F6F3!important; color:#747272!important;"  
            class="flex drop-shadow-lg w-full text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group" aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                  {{-- <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 21">
                     <path d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z"/>
                  </svg> --}}
                  <span class="flex-1  text-left rtl:text-right whitespace-nowrap">Users</span>
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                  </svg>
            </button>
            <ul id="dropdown-example" class="hidden py-2 space-y-2">
                <li>
                    <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('all-users') }}"
                        class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">
        
                        <span class="">All Users</span>
                    </a>
                </li>
                <li>
                    <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('admin.test-users') }}"
                        class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">
        
                        <span class="">Test Users</span>
                    </a>
                </li>
            </ul>
         </li>
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('all-subusers') }}"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">All Sub Users</span>
            </a>
        </li>
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('packages') }}"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">Packages</span>
            </a>
        </li>
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('topups') }}"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">Topup</span>
            </a>
        </li>
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('pages') }}"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">Pages</span>
            </a>
        </li>
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" href="{{ route('coupons') }}"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">Add Coupuns</span>
            </a>
        </li>

            @php
                if(isset($user->team_user))
                $user = json_decode($this->user);

            @endphp
            @if(isset($user->team_user))
            @if($user->team_user != null)
            @if (Route::is('sender'))
                        
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" wire:click="featureRedirect('view_sfp_sender_challan', null)"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP Challan</span>
            </a>
        </li>
        
        @elseif (Route::is(['receiver']))
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" wire:click="featureRedirect('view_sfp_receiver_challan', null)"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP Challan</span>
            </a>
        </li>
        @elseif (Route::is(['seller']))
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" wire:click="featureRedirect('view_sfp_seller', null)"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP</span>
            </a>
        </li>
        @elseif (Route::is(['buyer']))
        <li>
            <a style="background: #F8F6F3!important; color:#747272!important;" wire:click="featureRedirect('view_sfp_buyer', null)"
                class=" flex drop-shadow-lg text-sm px-2 py-2 hover:bg-gray-200 border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP</span>
            </a>
        </li>
        {{-- @endif
       @endif --}}
       @endif
    @endif
    @endif
       
</div>
