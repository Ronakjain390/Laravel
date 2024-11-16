<div class="min-h-screen px-2 overflow-y-auto border-neutral-400 border-r flex flex-col justify-between">
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
        
        @php
        $user = json_decode($this->user);
        $permissionTemplateMap = [
            'view_invoices' => 'all_invoice',
            'view_purchase_order_tables' => 'purchase_order',
            'new_purchase_order' => 'new_purchase_order',
            'view_seller' => 'all_seller',
            'add_seller' => 'add_seller',
        ];
        @endphp

            @foreach ($templates as $template)
            @php
                // Determine if the current template has a corresponding permission for sub-user
                $hasSubUserPermission = false;
                $templatePageName = $template->template_page_name; // Adjust this to match your actual template page name attribute
                if (isset($user->team_user) && isset($user->team_user->permissions) && isset($user->team_user->permissions->permission)) {
                    $permissions = $user->team_user->permissions->permission->{'buyer'};
                    foreach ($permissionTemplateMap as $permission => $templateName) {
                        if ($templatePageName === $templateName && !empty($permissions->$permission) && $permissions->$permission == 1) {
                            $hasSubUserPermission = true;
                            break;
                        }
                    }
                }
            @endphp

            @if ($hasSubUserPermission)
            <li>
            <a href="{{ $template->template_name === 'Create Invoice' && (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city)) ? route('profile') : route('buyer', ['template' => $template->template_page_name]) }}"
            class="flex drop-shadow-lg text-sm px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 rounded-lg items-center dark:focus:ring-gray-500 group">
                <span>{{ $template->template_name }}</span>
            </a>
            </li>
            @elseif (!isset($user->team_user) || !isset($user->team_user->permissions) || !isset($user->team_user->permissions->permission))
            <li>
            <a href="{{ $template->template_name === 'Create Invoice' && (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city)) ? route('profile') : route('buyer', ['template' => $template->template_page_name]) }}"
            class="flex drop-shadow-lg text-sm px-2 text-black font-normal hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100 rounded-lg items-center dark:focus:ring-gray-500 group">
                <span>{{ $template->template_name }}</span>
            </a>
            </li>
            @endif
            @endforeach
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
    </ul>
    <script>
        function showExpiredMessage() {
            document.getElementById('expiredMessage').classList.remove('hidden');
        }
    </script>
</div>