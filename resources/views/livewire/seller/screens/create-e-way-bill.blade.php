<div>
    <form wire:submit.prevent="submitForm">
        <div class="mx-auto p-6 bg-white shadow-md rounded-lg">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">General Details</h2>
            @if (session()->has('error'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Error:</span> {{ session('error') }}
                </div>
            @endif
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show" class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">Success:</span> {{ session('success') }}
                </div>
            @endif
            <div class="space-y-4">
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transaction Type <span class="text-red-600">*</span></label>
                        <select wire:model.defer="ewayBillData.transactionType" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="" disabled>Select Transaction Type</option>
                            <option value="1">Regular</option>
                            <option value="2">Bill To - Ship To</option>
                            <option value="3">Bill From - Dispatch From</option>
                            <option value="4">Combination of 2 and 3</option>
                        </select>
                        @error('ewayBillData.transactionType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supply Type <span class="text-red-600">*</span></label>
                        <select wire:model.defer="ewayBillData.supplyType" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="" disabled>Supply Type</option>
                            <option value="O">Outward</option>
                            <option value="I">Inward</option>
                        </select>
                        @error('ewayBillData.supplyType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sub Supply Type <span class="text-red-600">*</span></label>
                        <select wire:model.defer="ewayBillData.subSupplyType" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="" disabled>Select Sub Supply Type</option>
                            <option value="1">Supply</option>
                            <option value="2">Import</option>
                            <option value="3">Export</option>
                            <option value="4">Job Work</option>
                            <option value="5">For Own Use</option>
                            <option value="6">Job work Returns</option>
                            <option value="7">Sales Return</option>
                            <option value="8">Others</option>
                            <option value="9">SKD/CKD/Lots</option>
                            <option value="10">Line Sales</option>
                            <option value="11">Recipient Not Known</option>
                            <option value="12">Exhibition or Fairs</option>
                        </select>
                        @error('ewayBillData.subSupplyType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Your GSTIN No <span class="text-red-600">*</span></label>
                        <input type="text" wire:model.defer="ewayBillData.fromGstin" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="29AAGCV9438G1ZR" required>
                        @error('ewayBillData.fromGstin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Pincode <span class="text-red-600">*</span></label>
                        <input wire:model.defer="ewayBillData.fromPincode" type="text" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter your pincode" required>
                        @error('ewayBillData.fromPincode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Party GSTIN No (If Applicable)</label>
                        <input type="text" wire:model.defer="ewayBillData.toGstin" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="29AABCU9603R1ZJ">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">To Pincode <span class="text-red-600">*</span></label>
                        <input wire:model.defer="ewayBillData.toPincode" type="text" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter party pincode" required>
                        @error('ewayBillData.toPincode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Address</label>
                        <textarea wire:model.defer="ewayBillData.fromAddr1" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="11th Main, S.T. Bed, Koramangala Bangalore"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">To Address</label>
                        <textarea wire:model.defer="ewayBillData.toAddr1" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="5th block 7th Main Krishna Nagar Mangalore"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From State <span class="text-red-600">*</span></label>
                        <select wire:model.defer="ewayBillData.actFromStateCode" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-100" required>
                            <option value="" disabled>Select From State</option>
                            @foreach($states as $id => $name)
                                <option wire:ignore value="{{ $id }}" {{ (strtoupper($sellerUser->state ?? '') == $name) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">To State <span class="text-red-600">*</span></label>
                        <select wire:model.defer="ewayBillData.actToStateCode" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-100" required>
                            <option value="" disabled>Select To State</option>
                            @foreach($states as $id => $name)
                                <option value="{{ $id }}" {{ (strtoupper($buyerUser->state ?? '') == $name) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <h2 class="text-xl font-semibold my-4 text-gray-700">Transportation Details</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transporter ID <span class="text-red-600">*</span></label>
                        <input type="text" wire:model.defer="ewayBillData.transporterId" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="ex - 12345687" required>
                        @error('ewayBillData.transporterId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Approx Distance (In KM) </label>
                        <input type="number" wire:model.defer="ewayBillData.transDistance" value="0" class="mt-1 block w-full border-gray-300 text-xs text-black rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" required>
                        @error('ewayBillData.transDistance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-4 mt-5">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600" onclick="window.location.href='{{ url()->previous() }}'">Cancel</button>
            </div>
        </div>
    </form>
</div>
