<div id="dynamic-view">
    <div class="mt-2">
        <div class="flex min-h-full flex-col justify-center py-8 lg:px-8  overflow-x-hidden">
            <div class="mx-auto w-full lg:w-5/12 md:w-11/12 max-w-sm">
                <div wire:loading    class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <span   class="loading loading-spinner loading-md"></span>
            </div>
                <div class="w-full max-h-full p-4 border border-gray-200 shadow sm:px-6  dark:border-gray-700 bg-[#E7E7E7]">
                    <div class="flex flex-col items-center">
                        <img class="w-44 mb-8 md:mb-12 mt-8 md:mt-12" src="{{asset('image/Vector.png')}}" alt="Bonnie image" />
                        <div class="w-full mb-6">
                            <div class="flex mt-6 space-x-3 md:mt-6 w-full">
                                {{-- @dd($teamUsers) --}}
                                @php
                                $user = json_decode($this->user);
                                $panelKey = strtolower(str_replace(' ', '_', Session::get('panel'))); // Convert to lowercase and replace spaces with underscores

                                // $panelName = strtolower(str_replace('_', ' ', Session::get('panel')));
                                // dd(Session::get('panel'));    
                                @endphp
                                {{-- @dd($user->team_user->permissions->permission  ) --}}
                                @if (isset($user->team_user) && isset($user->team_user->permissions) && isset($user->team_user->permissions->permission))
                                @if (isset($user->team_user->permissions->permission->$panelKey) && $user->team_user->permissions->permission->$panelKey->create_receipt_note == 1)
                                <a type="button" 
                                        @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                                            onclick="window.location.href = '{{ route('profile') }}'; return false;"
                                        @else
                                           href="{{ route('grn', ['template' => 'create-goods-receipt']) }}"
                                        @endif
                                        class="w-full justify-center text-black bg-[#f0ac49] hover:text-white text-lg md:text-2xl rounded-lg px-4 md:px-5 py-2 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2">
                                    Create Receipt Note 
                                </a>
                                @endif
                                @else
                                <a type="button"
                                @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city) || $PlanFeatureUsageRecordResponse !== 'active')
                                    onclick="event.preventDefault(); @if($PlanFeatureUsageRecordResponse !== 'active') togglePlanExpiredMessage(true); @else window.location.href = '{{ route('profile') }}'; @endif"
                                @else
                                    href="{{ route('grn', ['template' => 'create-goods-receipt']) }}"
                                @endif
                                class="w-full justify-center text-black bg-[#f0ac49] hover:text-white text-lg md:text-2xl rounded-lg px-4 md:px-5 py-2 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2
                                {{ $PlanFeatureUsageRecordResponse !== 'active' ? 'cursor-not-allowed opacity-50' : '' }}">
                                Create Receipt Note
                             </a>
                             
                             <div id="planExpiredMessage" class="text-red-600 text-xs" style="display: none;">
                                 Plan Expired! <a class="hover:underline" href="{{route('pricing')}}">Please Topup</a>
                             </div>
                             @endif
                            </div>
                            @if (isset($user->team_user) && isset($user->team_user->permissions) && isset($user->team_user->permissions->permission))
                            @if (isset($user->team_user->permissions->permission->$panelKey) && $user->team_user->permissions->permission->$panelKey->add_receiver == 1)
                            <div class="flex mt-6 space-x-3 md:mt-6 w-full">
                                <a type="button" 
                                        @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                                            onclick="window.location.href = '{{ route('profile') }}'; return false;"
                                        @else
                                            href="{{ route('grn', ['template' => 'add-goods-receiver']) }}"
                                        @endif
                                        class="w-full justify-center text-[#E5F811] bg-black hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-100 text-lg md:text-2xl rounded-lg px-4 md:px-5 py-3 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2">
                                    Add Receiver
                                </a>
                            </div>
                            @endif
                            @else
                            <div class="flex mt-6 space-x-3 md:mt-6 w-full">
                            <a type="button" 
                                        @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                                            onclick="window.location.href = '{{ route('profile') }}'; return false;"
                                        @else
                                            href="{{ route('grn', ['template' => 'add-goods-receiver']) }}"
                                        @endif
                                        class="w-full justify-center text-[#E5F811] bg-black hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-100 text-lg md:text-2xl rounded-lg px-4 md:px-5 py-3 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2">
                                    Add Receiver
                                </a>
                            </div>
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>
        
         
        
        <script>
             window.addEventListener('DOMContentLoaded', (event) => {
        // Initialize Flowbite on page load
        initFlowbite();
          console.log('DOM Loaded');
         });
        document.addEventListener('livewire:update', function () { 
        initFlowbite(); 

        function togglePlanExpiredMessage(show) {
        const messageElement = document.getElementById('planExpiredMessage');
        if (show) {
            messageElement.style.display = 'block';
        } else {
            messageElement.style.display = 'none';
        }
    }
    });
    // Event listener to detect tab change and reinitialize dropdown
    document.addEventListener('livewire:load', function () {
         
            initFlowbite();
             
    });
        </script>
    </div>

</div>

