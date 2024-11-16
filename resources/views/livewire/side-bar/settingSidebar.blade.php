<!-- component -->
<div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased  text-gray-800 px-2 border-neutral-400 border-r bg-[#ebebeb]">
   <div class="fixed flex flex-col top-0 left-0 w-64  h-full border-r">
     <div class="flex items-center justify-center h-11 ">
       <div> <a href="{{ route('dashboard') }}"
         class="flex items-center justify-center  sm:ml-auto my-3  border-gray-400 ">
         <img src="{{asset('image/Vector.png')}}" class="h-8 mr-3" alt="TheParrchi" />
         <!-- <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">TheParchi</span> -->
      </a></div>
     </div>
     <div class="overflow-y-auto overflow-x-hidden flex-grow p-2">
       <ul class="flex flex-col mt-2 space-y-2">
         <li class="border-gray-400 border-t border-b px-2 py-1">
            <a href="{{ route('profile') }}"
                class="flex items-center px-2 py-1  focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg dark:focus:ring-gray-500 group text-center">

                <span class="ml-auto text-blue-800 font-semibold capitalize ">Hey
                    {{ Auth::user()->company_name ?? (Auth::user()->name ?? Auth::user()->team_user_name) }} !!</span>
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
            <a href="{{route('order-history')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

               <span class="flex-1 ml-3 whitespace-nowrap">Order History</span>
            </a>
         </li>
         <li>
            <a href="{{route('active-plans')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

               <span class="flex-1 ml-3 whitespace-nowrap">Active Plan</span>
            </a>
         </li>
         <li>
            <a href="{{route('company-logo')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

               <span class="flex-1 ml-3 whitespace-nowrap">Customize</span>
            </a>
         </li>
         <li>
            <a href="{{route('teams')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

               <span class="flex-1 ml-3 whitespace-nowrap">Teams</span>
            </a>
         </li>
         {{-- <li>
            <a href="{{route('team-member')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

               <span class="flex-1 ml-3 whitespace-nowrap">Team Member</span>
            </a>
         </li> --}}
         {{-- <li>
            <a href="{{route('tabs-component')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">


               <span class="flex-1 ml-3 whitespace-nowrap">Profile</span>
            </a>
         </li> --}}
         <li>
            <a href="{{route('user-address')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

               <span class="flex-1 ml-3 whitespace-nowrap">User Address</span>
            </a>
         </li>
         {{-- Show Only to Admin --}}
         @if(Auth::getDefaultDriver() == 'user' || Auth::getDefaultDriver() == 'web')
         <li>
            <a href="{{route('notification')}}"
               class="flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange bg-grays border-gray-300 border focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">

               <span class="flex-1 ml-3 whitespace-nowrap">Notifications</span>
            </a>
         </li>
         @endif
         {{-- Show Only to Admin --}}
       </ul>
     </div>
     <div class="sm:hidden justify-center   flex">

         <a href="#" wire:click='Logout'
          class="justify-center   flex drop-shadow-lg text-sm px-2 text-gray-500 font-semibold hover:text-white py-2 hover:bg-orange   items-center dark:focus:ring-gray-500 group">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-7 h-7 ">
              <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
          </svg>
          {{-- <span class="flex-1 ml-3 whitespace-nowrap">Logout</span> --}}
         </a>

            <a  href="{{ route('help') }}"
                class=" flex drop-shadow-lg px-2 ml-2 py-2 text-gray-500 font-semibold hover:text-white text-sm hover:bg-orange    focus:ring-4 focus:outline-none focus:ring-gray-100  rounded-lg  items-center dark:focus:ring-gray-500 group">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 19V17H13V19H11ZM14.17 12.17L15.07 11.25C15.64 10.68 16 9.88 16 9C16 6.79 14.21 5 12 5C9.79 5 8 6.79 8 9H10C10 7.9 10.9 7 12 7C13.1 7 14 7.9 14 9C14 9.55 13.78 10.05 13.41 10.41L12.17 11.67C11.45 12.4 11 13.4 11 14.5V15H13C13 13.5 13.45 12.9 14.17 12.17Z" fill="black" fill-opacity="0.54"/>
                    </svg>

            </a>

     </div>
   </div>
 </div>
