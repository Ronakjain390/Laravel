<div>
    {{-- <h1 class="max-w-6xl ml-64 text-xl font-se mibold  mx-auto mt-20">{{strtoupper('Create Coupon Code')}}</h1> --}}
    
    <div class=" gap-6 mb-6 mt-12 p-12 rounded-md bg-[#e9e6e6]">
        @if(session()->has('message'))
            <div class="alert alert-success p-1.5" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show">
                {{ session('message') }}
            </div>
        @endif
        @if ($successMessage)
        <div id="alert-border-3" class="p-2 mb-4 text-xs text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
            role="alert">
            <span class="font-medium">Success:</span> {{ $successMessage }}
        </div>
    
        @endif
            @if ($errorMessage)
            {{-- {{dd($errorMessage)}} --}}
            @foreach (json_decode($errorMessage) as $error)
            <div class=" text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Error:</span> {{ $error[0] }}
            </div>
            @endforeach
            @endif
         
        {{-- <h1>Create Coupon Code</h1> --}}
       <div class="mt-1">
        <div x-data="couponForm()" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Code</label>
                <input type="text" id="code" wire:model.defer="couponDataset.code" x-model="couponDataset.code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Code" required />
            </div>
            <div>
                <label for="discount_amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Discount Amount</label>
                <input type="text" id="discount_amount" wire:model.defer="couponDataset.discount_amount" x-model="couponDataset.discount_amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Discount Amount" required />
            </div>
            <div>
                <label for="discount_basis" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Discount Basis</label>
                <select id="discount_basis" wire:model.defer="couponDataset.discount_basis" x-model="couponDataset.discount_basis" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="">Select Discount Basis</option>
                    <option value="percentage">Percentage</option>
                    <option value="direct">Direct Discount</option>
                </select>
            </div>
            <div>
                <label for="valid_from" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Valid From</label>
                <input type="date" id="valid_from" wire:model.defer="couponDataset.valid_from" x-model="couponDataset.valid_from" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Valid From" required />
            </div>
            <div>
                <label for="valid_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Valid Till</label>
                <input type="date" id="valid_to" wire:model.defer="couponDataset.valid_to" x-model="couponDataset.valid_to" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Valid Till" required />
            </div>
            <div>
                <label for="usage_count" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Usage Limit</label>
                <input type="text" id="usage_count" wire:model.defer="couponDataset.usage_count" x-model="couponDataset.usage_count" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Usage Limit" required />
            </div>
            <div>
                <label for="applicable_on" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Type</label>
                <select id="applicable_on" wire:model.defer="couponDataset.applicable_on" x-model="couponDataset.applicable_on" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="">Applicable On</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="payment">Payment</option>
                </select>
            </div>
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select id="status" wire:model.defer="couponDataset.status" x-model="couponDataset.status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="">Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        
            <div class="max-w-5xl mt-2">
                <button type="button" wire:click="createCoupons" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                    :class="{
                        'bg-gray-400 dark:bg-gray-300': !isFormValid(),
                        'bg-gray-700 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-800': isFormValid()
                    }"
                    :disabled="!isFormValid()">Submit</button>
            </div>
        </div>
        
        <script>
            function couponForm() {
                return {
                    couponDataset: {
                        code: '',
                        discount_amount: '',
                        discount_basis: '',
                        valid_from: '',
                        valid_to: '',
                        usage_count: '',
                        applicable_on: '',
                        status: ''
                    },
                    isFormValid() {
                        return this.couponDataset.code &&
                            this.couponDataset.discount_amount &&
                            this.couponDataset.discount_basis &&
                            this.couponDataset.valid_from &&
                            this.couponDataset.valid_to &&
                            this.couponDataset.usage_count &&
                            this.couponDataset.applicable_on &&
                            this.couponDataset.status;
                    }
                };
            }
        </script>
        
       </div>
  
    
    
    <div class="card-body table-responsive p-0 whitespace-nowrap mt-5 overflow-auto" style="height: 300px;">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-sm text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="va-b px-2 py-2 text-sm">S.NO</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">CODE</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">DISCOUNT AMOUNT</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">DISCOUNT BASIS</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">VALID FROM</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">VALID TO </th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">USAGE LIMIT</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">Applicable On</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">Select Type</th>
                    <th scope="col" class="va-b px-2 py-2 text-sm">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @php
                $columnsData = json_decode($columnsData);
                // dd($allTopupData);
            @endphp
                @foreach ($columnsData as $key => $data)
                    <tr
                        class="@if ($key % 2 == 0) border-b bg-[#e9e6e6] dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600 @else bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 @endif whitespace-nowrap text-black h-0 w-0">

                        <td class="px-2 py-2 text-sm"> {{ ++$key }} </td>
                       
                        <td class="px-2 py-2 text-sm">

                            {{ $data->code }}

                        </td>
                        <td class="px-2 py-2 text-sm">{{ $data->discount_amount }}</td>
                        <td class="px-2 py-2 text-sm">{{ $data->discount_basis }}</td>

                        <td class="px-2 py-2 text-sm">{{ $data->valid_from ?? '' }} </td>
                        <td class="px-2 py-2 text-sm">{{ $data->valid_to }}</td>
                        <td class="px-2 py-2 text-sm">{{ $data->usage_limit }}</td>
                        <td class="px-2 py-2 text-sm">{{ $data->applicable_on }}</td>
                        <td class="px-2 py-2 text-sm">{{ $data->status }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>