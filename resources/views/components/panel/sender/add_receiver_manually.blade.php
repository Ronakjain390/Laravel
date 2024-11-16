<div class="mt-20">
    @if ($statusCode == 200)
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
        {{ $message }}
    </div>
    @elseif ($statusCode == 400)
    @php
    $decodedValidationErrors = json_decode($validationErrorsJson, true);
    @endphp

    @if ($decodedValidationErrors && count($decodedValidationErrors) > 0)
    <ul class="text-red-500">
        @foreach ($decodedValidationErrors as $field => $fieldErrors)
        @foreach ($fieldErrors as $error)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
            {{ $error }}
        </div>
        @endforeach
        @endforeach
    </ul>
    @endif
    @endif


    <div class="grid gap-4">
        <div> 
            <label for="receiver_name" class="block text-sm font-medium ">Name <span class="text-red-600">*</span></label>
            <input type="text" wire:model="addReceiverData.receiver_name" id="receiver_name" name="receiver_name" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus: ">
        </div>
        <div>
            <label for="company_name" class="block text-sm font-medium ">Company Name</label>
            <input type="text" wire:model="addReceiverData.company_name" id="company_name" name="company_name" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium">E- Mail</label>
            <input type="text" wire:model="addReceiverData.email" id="email" name="email" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
            @error('addReceiverData.email')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <div>
                <label for="pincode" class="block text-sm font-medium">Pincode <span class="text-red-600">*</span></label>
                <input type="text" wire:model="addReceiverData.pincode" id="pincode" name="pincode" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
            </div>
            <label for="address" class="block text-sm font-medium">Address <span class="text-red-600">*</span></label>
            <input type="text" wire:model="addReceiverData.address" id="address" name="address" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
        </div>

        <div>
            <label for="city" class="block text-sm font-medium">City <span class="text-red-600">*</span></label>
            <input type="text" wire:model="addReceiverData.city" id="city" name="city" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
        </div>
        <div>
            <label for="state" class="block text-sm font-medium">State <span class="text-red-600">*</span></label>
            <input type="text" wire:model="addReceiverData.state" id="state" name="state" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium">Phone</label>
            <input type="text" wire:model="addReceiverData.phone" id="phone" name="phone" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
            @error('addReceiverData.phone')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="pancard" class="block text-sm font-medium">Pancard</label>
            <input type="text" wire:model="addReceiverData.pancard" id="pancard" name="pancard" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
        </div>
        <div>
            <label for="tan" class="block text-sm font-medium">Tan Number</label>
            <input type="text" wire:model="addReceiverData.tan" id="tan" name="tan" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
        </div>
        <div class="flex justify-center">
            <button type="button" wire:click="callAddReceiverManually" class="rounded-full w-full bg-gray-900 px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">Add Receiver</button>
        </div>
    </div>

</div>