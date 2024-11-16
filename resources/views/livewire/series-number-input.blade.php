<div x-data="{ isChanged: false }">
    {{-- <input type="text" name="seriesNumber" wire:model="seriesNumber" class="w-5/12 sm:w-12 h-5 rounded px-2 py-1 text-xs"
           x-on:input="isChanged = true" inputmode="numeric" pattern="[0-9]*"> <br> --}}
           <input type="text" id="floating_standard" class="appearance-none bg-gray-100 block border-0 border-b dark:border-gray-600 dark:focus:border-blue-500 dark:text-white focus:border-blue-600 focus:outline-none focus:ring-0 p-0.5 peer px-0 text-center text-gray-900 text-xs w-11" placeholder=" " wire:model="seriesNumber" x-on:input="isChanged = true" inputmode="numeric" pattern="[0-9]*" />
           <label for="floating_standard" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto"></label>

    @error('seriesNumber') <span class="text-red-500 text-[0.6rem] whitespace-normal">{{ $message }}</span> @enderror

    @if (session()->has('error'))
        <div class="text-red-500 text-[0.6rem] mt-2  ">
            {{ session('error') }}
        </div>
    @endif
</div>
