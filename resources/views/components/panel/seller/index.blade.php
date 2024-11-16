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
                        // dd($user);
                        @endphp
                        {{-- @dd($user->team_user->permissions->permission->{strtolower(Session::get('panel')['panel_name']))}) --}}
                        @if (isset($user->team_user) && isset($user->team_user->permissions) && isset($user->team_user->permissions->permission))
                        @if (isset($user->team_user->permissions->permission->{'seller'}) && $user->team_user->permissions->permission->{'seller'}->create_invoice == 1)

                        <button type="button" 
                                @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                                    onclick="window.location.href = '{{ route('profile') }}'; return false;"
                                @else
                                    wire:click="innerFeatureRedirect('create_invoice', '12')"
                                @endif
                                class="w-full justify-center text-black bg-[#f0ac49] hover:text-white text-lg md:text-2xl rounded-lg px-4 md:px-5 py-2 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2">
                            Create Invoice
                        </button>
                        @endif
                        @else
                        <button type="button" 
                                @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                                    onclick="window.location.href = '{{ route('profile') }}'; return false;"
                                @else
                                    wire:click="innerFeatureRedirect('create_invoice', '12')"
                                @endif
                                class="w-full justify-center text-black bg-[#f0ac49] hover:text-white text-lg md:text-2xl rounded-lg px-4 md:px-5 py-2 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2">
                            Create Invoice
                        </button>
                        @endif
                    </div>
                    @if (isset($user->team_user) && isset($user->team_user->permissions) && isset($user->team_user->permissions->permission))
                    @if (isset($user->team_user->permissions->permission->{'seller'}) && $user->team_user->permissions->permission->{'seller'}->add_buyer == 1)
                    <div class="flex mt-6 space-x-3 md:mt-6 w-full">
                        <button type="button" 
                                @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                                    onclick="window.location.href = '{{ route('profile') }}'; return false;"
                                @else
                                    wire:click="innerFeatureRedirect('sent_invoice', '13')"
                                @endif
                                class="w-full justify-center text-[#E5F811] bg-black hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-100 text-lg md:text-2xl rounded-lg px-4 md:px-5 py-3 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2">
                             View Sent Invoice
                        </button>
                    </div>
                    @endif
                    @else
                    <div class="flex mt-6 space-x-3 md:mt-6 w-full">
                    <button type="button" 
                                @if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city))
                                    onclick="window.location.href = '{{ route('profile') }}'; return false;"
                                @else
                                    wire:click="innerFeatureRedirect('sent_invoice', '13')"
                                @endif
                                class="w-full justify-center text-[#E5F811] bg-black hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-100 text-lg md:text-2xl rounded-lg px-4 md:px-5 py-3 md:py-3 text-center inline-flex items-center dark:focus:ring-gray-500 mb-2">
                             View Sent Invoice
                        </button>
                    </div>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
    
</div>
