<div>
    <div>
        @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-4 sm:mx-6 md:mx-8 lg:mx-10 xl:mx-12">
                <h2 class="text-xl mb-4 text-black"> E-way Bill Login</h2>

                @if ($loginSuccess)
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" class="alert alert-success mb-4">
                        Login successful! Proceed to create your Eway Bill.
                    </div>
                @endif

                @if ($errorMessage)
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" class="alert alert-danger bg-red-600 mb-4 p-1 rounded text-white">
                        {{ $errorMessage }}
                    </div>
                @endif

                <form id="ewayBillForm" wire:submit.prevent="login" class="max-w-md mx-auto">
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="text" name="username" wire:model.lazy="username" id="username" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "  />
                        <label for="username" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Username</label>
                        @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="password" name="password" wire:model.lazy="password" id="password" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "  />
                        <label for="password" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Password</label>
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="relative z-0 w-full mb-5 group">
                        <input type="text" name="gstin" id="gstin" wire:model.lazy="gstin" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "  />
                        <label for="gstin" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">GSTIN</label>
                        @error('gstin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="relative z-0 w-full mb-5 group">
                        <input type="checkbox" name="remember" wire:model.defer="remember" id="remember" />
                        <label for="remember" class="text-sm text-gray-500">Remember Me</label>
                    </div>

                    <button type="submit" class="text-white bg-black hover:bg-black-800 focus:ring-4 focus:outline-none focus:ring-black-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Login</button>
                    <button type="button" wire:click="closeModal" class="mt-4 px-4 py-2 bg-red-500 text-white rounded">Cancel</button>
                </form>
            </div>
        </div>
        @endif

        @if($createEwayBill)
            @livewire('sender.screens.create-e-way-bill', ['columnId' => $columnId])
        @endif
    </div>

</div>
