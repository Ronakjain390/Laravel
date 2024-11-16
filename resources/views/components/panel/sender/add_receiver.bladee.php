<div class="mt-20" id="receiver-code" role="tabpanel" aria-labelledby="receiver-code-tab">
@if ($errorMessage)
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">Error:</span> {{ $errorMessage }}
    </div>
    @endif
    <div>
        <label for="receiver_code" class="block text-sm font-medium">Receiver Code</label>
        <input type="text" wire:model="addReceiverData.receiver_special_id" id="receiver_special_id" name="receiver_special_id" class="mt-1 p-2 h-8  block w-full rounded-md bg-[#aeaaaa] border-transparent focus:border-gray-500 ">
    </div>

    
    <div class="flex justify-center">
        <button type="button" wire:click="callAddReceiver" class="rounded-full w-full bg-gray-900 px-8 py-2 mt-4 text-white hover:bg-yellow-200 hover:text-black">Add Receiver</button>
    </div>
</div>