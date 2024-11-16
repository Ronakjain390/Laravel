<div class="min-h-screen px-2 overflow-y-auto border-neutral-400 border-r flex flex-col justify-between w-full">
    <div>
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center sm:ml-auto my-4">
            <img src="{{asset('image/Vector.png')}}" class="h-8 mr-3" alt="TheParrchi" />
            <!-- <span class="self-center text-xl font-normal sm:text-2xl whitespace-nowrap dark:text-white">TheParchi</span> -->
        </a>
           
    <ul class="space-y-2 ">
        <li class="border-gray-400 border-t border-b">
            <a href="{{ route('profile') }}" 
                class="flex items-center px-2 py-1  focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg dark:focus:ring-gray-500 group text-center">

                <span class="ml-auto text-blue-800 font-normal capitalize ">
                    {{ !empty(trim(Auth::user()->company_name)) ? Auth::user()->company_name : (Auth::user()->name ?? Auth::user()->team_user_name) }}</span>
                <svg data-tooltip-target="tooltip-bottom-user" data-tooltip-placement="bottom" width="24" height="24" class="mr-auto" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M14.82 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H9.18C9.6 1.84 10.7 1 12 1C13.3 1 14.4 1.84 14.82 3ZM13 4C13 3.45 12.55 3 12 3C11.45 3 11 3.45 11 4C11 4.55 11.45 5 12 5C12.55 5 13 4.55 13 4ZM12 7C13.66 7 15 8.34 15 10C15 11.66 13.66 13 12 13C10.34 13 9 11.66 9 10C9 8.34 10.34 7 12 7ZM6 17.6V19H18V17.6C18 15.6 14 14.5 12 14.5C10 14.5 6 15.6 6 17.6Z"
                        fill="#2F80ED" />
                </svg>
            </a>
            <div id="tooltip-bottom-user" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Profile
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </li>
        
       
            @if (isset($sidenav))
                    @unless(Route::is(['receiver']))
                    <li>
                        <a  href="{{ route('stock') }}"
                            class=" flex drop-shadow-lg text-sm px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

                            <span class="">Stock</span>
                        </a>
                    </li>
                    @endunless
                    <div class="justify-center">
                        <li>
                            <a  href="{{ route('setting') }}"
                                class=" flex drop-shadow-lg px-2 py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                                <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5 12C19.5 12.34 19.47 12.66 19.43 12.98L21.54 14.63C21.73 14.78 21.78 15.05 21.66 15.27L19.66 18.73C19.54 18.95 19.28 19.04 19.05 18.95L16.56 17.95C16.04 18.34 15.48 18.68 14.87 18.93L14.49 21.58C14.46 21.82 14.25 22 14 22H10C9.75002 22 9.54002 21.82 9.51002 21.58L9.13002 18.93C8.52002 18.68 7.96002 18.35 7.44002 17.95L4.95002 18.95C4.73002 19.03 4.46002 18.95 4.34002 18.73L2.34002 15.27C2.22002 15.05 2.27002 14.78 2.46002 14.63L4.57002 12.98C4.53002 12.66 4.50002 12.33 4.50002 12C4.50002 11.67 4.53002 11.34 4.57002 11.02L2.46002 9.37C2.27002 9.22 2.21002 8.95 2.34002 8.73L4.34002 5.27C4.46002 5.05 4.72002 4.96 4.95002 5.05L7.44002 6.05C7.96002 5.66 8.52002 5.32 9.13002 5.07L9.51002 2.42C9.54002 2.18 9.75002 2 10 2H14C14.25 2 14.46 2.18 14.49 2.42L14.87 5.07C15.48 5.32 16.04 5.65 16.56 6.05L19.05 5.05C19.27 4.97 19.54 5.05 19.66 5.27L21.66 8.73C21.78 8.95 21.73 9.22 21.54 9.37L19.43 11.02C19.47 11.34 19.5 11.66 19.5 12ZM8.50002 12C8.50002 13.93 10.07 15.5 12 15.5C13.93 15.5 15.5 13.93 15.5 12C15.5 10.07 13.93 8.5 12 8.5C10.07 8.5 8.50002 10.07 8.50002 12Z" fill="black" fill-opacity="0.54"/>
                                    </svg>
                                    
                                <span class="ml-2">Settings</span>
                            </a>
                        </li>
                        </div>
                @endif
        {{-- @endif --}}

            @php
                $user = json_decode($this->user);
            @endphp
            @if(isset($user->team_user))
            @if($user->team_user != null)
            @if (Route::is('sender'))
                        
        <li>
            <a  wire:click="featureRedirect('view_sfp_sender_challan', null)"
                class=" flex drop-shadow-lg text-sm px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP Challan</span>
            </a>
        </li>
        
        @elseif (Route::is(['receiver']))
        <li>
            <a  wire:click="featureRedirect('view_sfp_receiver_challan', null)"
                class=" flex drop-shadow-lg text-sm px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP Challan</span>
            </a>
        </li>
        @elseif (Route::is(['seller']))
        <li>
            <a  wire:click="featureRedirect('view_sfp_seller', null)"
                class=" flex drop-shadow-lg text-sm px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP</span>
            </a>
        </li>
        @elseif (Route::is(['buyer']))
        <li>
            <a  wire:click="featureRedirect('view_sfp_buyer', null)"
                class=" flex drop-shadow-lg text-sm px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

                <span class="">View SFP</span>
            </a>
        </li>
        {{-- @endif
       @endif --}}
       @endif
@endif
@endif
        @if (!isset($sidenav))
                {{-- @php
                    $user = json_decode($this->user);
                    $template = isset($nav['template']) ? $nav['template']['template_page_name'] : 'index';
                    // dd($user->team_user->permissions->permission->{strtolower(str_replace('_', ' ', Session::get('panel')['panel_name']))}->{strtolower(str_replace(' ', '_', $nav['feature_name']))}, $sidenav, $nav);
                    // dd();
                @endphp --}}
                {{-- <li>
                    <a  href="{{ route('pricing') }}"
                        class="flex px-2 drop-shadow-lg py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
            
                        <span class="">How "TheParchi Works"</span>
                    </a>
                    </li>
        <li> --}} 
            @if (isset($user->team_user->permissions->permission->pages->how_theparchi_works))
                @if($user->team_user->permissions->permission->pages->how_theparchi_works)
                    <li>
                        <a  href="{{ route('how-theparchi-works') }}"
                            class="flex px-2 drop-shadow-lg py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM13 11H11V5H13V11ZM11 15H13V13H11V15Z" fill="black" fill-opacity="0.54"/>
                            </svg>
                            <span class="ml-2">How "TheParchi Works"</span>
                        </a>
                    </li>
                @endif
            @else
            <li>
                <a  href="{{ route('how-theparchi-works') }}"
                    class="flex px-2 drop-shadow-lg py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                    <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM13 11H11V5H13V11ZM11 15H13V13H11V15Z" fill="black" fill-opacity="0.54"/>
                    </svg>     
                    <span class="ml-2">How "TheParchi Works"</span>
                </a>
            </li>
            @endif
            @if (isset($user->team_user->permissions->permission->pages->pricing))
                @if($user->team_user->permissions->permission->pages->pricing)
                <a  href="{{ route('pricing') }}"
                    class="flex px-2 drop-shadow-lg py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                    <svg class="w-4 h-4 mr-1" focusable="false" aria-hidden="true" fill="gray" viewBox="0 0 20 20"  tabindex="-1" ><path d="M13.66 7c-.56-1.18-1.76-2-3.16-2H6V3h12v2h-3.26c.48.58.84 1.26 1.05 2H18v2h-2.02c-.25 2.8-2.61 5-5.48 5h-.73l6.73 7h-2.77L7 14v-2h3.5c1.76 0 3.22-1.3 3.46-3H6V7z"></path></svg>
                    <span class="ml-2">Pricing</span>
                </a>
                @endif
            @else
            <a  href="{{ route('pricing') }}"
                    class="flex px-2 drop-shadow-lg py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                    <svg class="w-4 h-4 mr-1" focusable="false" aria-hidden="true" fill="gray" viewBox="0 0 20 20"  tabindex="-1" ><path d="M13.66 7c-.56-1.18-1.76-2-3.16-2H6V3h12v2h-3.26c.48.58.84 1.26 1.05 2H18v2h-2.02c-.25 2.8-2.61 5-5.48 5h-.73l6.73 7h-2.77L7 14v-2h3.5c1.76 0 3.22-1.3 3.46-3H6V7z"></path></svg>
                    <span class="ml-2">Pricing</span>
                </a>
            @endif
        </li>
        @if (isset($user->team_user->permissions->permission->pages->pricing))
                @if($user->team_user->permissions->permission->pages->pricing)
                <li>
                    <a  href="{{ route('all-feature') }}"
                        class=" flex drop-shadow-lg px-2 py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                        <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21 3H3C1.9 3 1 3.9 1 5V19C1 20.1 1.9 21 3 21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3ZM12 11H3V9H12V11ZM3 7H12V5H3V7Z" fill="black" fill-opacity="0.54"/>
                            </svg>
                            
                        <span class="ml-2">All Features</span>
                    </a>
                </li>
                @endif

        @else
        <li>
            <a  href="{{ route('all-feature') }}"
                class=" flex drop-shadow-lg px-2 py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21 3H3C1.9 3 1 3.9 1 5V19C1 20.1 1.9 21 3 21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3ZM12 11H3V9H12V11ZM3 7H12V5H3V7Z" fill="black" fill-opacity="0.54"/>
                    </svg>
                    
                <span class="ml-2">All Features</span>
            </a>
        </li>
        @endif

        @if (isset($user->team_user->permissions->permission->pages->help))
                @if($user->team_user->permissions->permission->pages->help)
            <li>
                <a  href="{{ route('help') }}"
                    class=" flex drop-shadow-lg px-2 py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                    <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 19V17H13V19H11ZM14.17 12.17L15.07 11.25C15.64 10.68 16 9.88 16 9C16 6.79 14.21 5 12 5C9.79 5 8 6.79 8 9H10C10 7.9 10.9 7 12 7C13.1 7 14 7.9 14 9C14 9.55 13.78 10.05 13.41 10.41L12.17 11.67C11.45 12.4 11 13.4 11 14.5V15H13C13 13.5 13.45 12.9 14.17 12.17Z" fill="black" fill-opacity="0.54"/>
                        </svg>
                        
                    <span class="ml-2">Help</span>
                </a>
            </li>
            @endif
            @else
            <li>
                <a  href="{{ route('help') }}"
                    class=" flex drop-shadow-lg px-2 py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                    <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 19V17H13V19H11ZM14.17 12.17L15.07 11.25C15.64 10.68 16 9.88 16 9C16 6.79 14.21 5 12 5C9.79 5 8 6.79 8 9H10C10 7.9 10.9 7 12 7C13.1 7 14 7.9 14 9C14 9.55 13.78 10.05 13.41 10.41L12.17 11.67C11.45 12.4 11 13.4 11 14.5V15H13C13 13.5 13.45 12.9 14.17 12.17Z" fill="black" fill-opacity="0.54"/>
                        </svg>
                        
                    <span class="ml-2">Help</span>
                </a>
            </li>
        @endif

        <div class="justify-center    ">
            <li>
                <a  href="{{ route('setting') }}"
                    class=" flex drop-shadow-lg px-2 py-2 text-black font-normal hover:text-white text-sm hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                    <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5 12C19.5 12.34 19.47 12.66 19.43 12.98L21.54 14.63C21.73 14.78 21.78 15.05 21.66 15.27L19.66 18.73C19.54 18.95 19.28 19.04 19.05 18.95L16.56 17.95C16.04 18.34 15.48 18.68 14.87 18.93L14.49 21.58C14.46 21.82 14.25 22 14 22H10C9.75002 22 9.54002 21.82 9.51002 21.58L9.13002 18.93C8.52002 18.68 7.96002 18.35 7.44002 17.95L4.95002 18.95C4.73002 19.03 4.46002 18.95 4.34002 18.73L2.34002 15.27C2.22002 15.05 2.27002 14.78 2.46002 14.63L4.57002 12.98C4.53002 12.66 4.50002 12.33 4.50002 12C4.50002 11.67 4.53002 11.34 4.57002 11.02L2.46002 9.37C2.27002 9.22 2.21002 8.95 2.34002 8.73L4.34002 5.27C4.46002 5.05 4.72002 4.96 4.95002 5.05L7.44002 6.05C7.96002 5.66 8.52002 5.32 9.13002 5.07L9.51002 2.42C9.54002 2.18 9.75002 2 10 2H14C14.25 2 14.46 2.18 14.49 2.42L14.87 5.07C15.48 5.32 16.04 5.65 16.56 6.05L19.05 5.05C19.27 4.97 19.54 5.05 19.66 5.27L21.66 8.73C21.78 8.95 21.73 9.22 21.54 9.37L19.43 11.02C19.47 11.34 19.5 11.66 19.5 12ZM8.50002 12C8.50002 13.93 10.07 15.5 12 15.5C13.93 15.5 15.5 13.93 15.5 12C15.5 10.07 13.93 8.5 12 8.5C10.07 8.5 8.50002 10.07 8.50002 12Z" fill="black" fill-opacity="0.54"/>
                        </svg>
                        
                    <span class="ml-2">Settings</span>
                </a>
            </li>
            </div>
    </ul>
    @endif
    </div>

    {{-- <div class="mb-1 ">
        <a  href="{{ route('setting') }}"
                class=" flex drop-shadow-lg px-2 py-2 text-black font-normal hover:text-white text-xs hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5 12C19.5 12.34 19.47 12.66 19.43 12.98L21.54 14.63C21.73 14.78 21.78 15.05 21.66 15.27L19.66 18.73C19.54 18.95 19.28 19.04 19.05 18.95L16.56 17.95C16.04 18.34 15.48 18.68 14.87 18.93L14.49 21.58C14.46 21.82 14.25 22 14 22H10C9.75002 22 9.54002 21.82 9.51002 21.58L9.13002 18.93C8.52002 18.68 7.96002 18.35 7.44002 17.95L4.95002 18.95C4.73002 19.03 4.46002 18.95 4.34002 18.73L2.34002 15.27C2.22002 15.05 2.27002 14.78 2.46002 14.63L4.57002 12.98C4.53002 12.66 4.50002 12.33 4.50002 12C4.50002 11.67 4.53002 11.34 4.57002 11.02L2.46002 9.37C2.27002 9.22 2.21002 8.95 2.34002 8.73L4.34002 5.27C4.46002 5.05 4.72002 4.96 4.95002 5.05L7.44002 6.05C7.96002 5.66 8.52002 5.32 9.13002 5.07L9.51002 2.42C9.54002 2.18 9.75002 2 10 2H14C14.25 2 14.46 2.18 14.49 2.42L14.87 5.07C15.48 5.32 16.04 5.65 16.56 6.05L19.05 5.05C19.27 4.97 19.54 5.05 19.66 5.27L21.66 8.73C21.78 8.95 21.73 9.22 21.54 9.37L19.43 11.02C19.47 11.34 19.5 11.66 19.5 12ZM8.50002 12C8.50002 13.93 10.07 15.5 12 15.5C13.93 15.5 15.5 13.93 15.5 12C15.5 10.07 13.93 8.5 12 8.5C10.07 8.5 8.50002 10.07 8.50002 12Z" fill="black" fill-opacity="0.54"/>
                    </svg>
                    
                <span class="ml-2">Settings</span>
            </a>
    </div> --}}

    <script>
        function showExpiredMessage() {
            document.getElementById('expiredMessage').classList.remove('hidden');
        }
    </script>
</div>