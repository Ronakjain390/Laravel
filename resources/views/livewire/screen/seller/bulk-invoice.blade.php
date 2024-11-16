

<div class="rounded-lg dark:border-gray-700 mt-4">
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 15000)" x-show="show" id="success-alert" class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <div class="ms-3 text-sm">
            <span class="font-medium">Success:</span> {{ session()->get('success') }}
        </div>
        <button @click="show = false" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8 items-center justify-center" aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif
@if (session()->has('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 15000)" x-show="show" id="error-alert" class="flex items-center p-2 mb-4 text-red-800 rounded-lg bg-red-500 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
        <div class="ms-3 text-sm">
            <span class="font-medium">Error:</span> {{ session()->get('error') }}
        </div>
        <button @click="show = false" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8 items-center justify-center" aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif
    <div
        class="p-1 mb-2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 flex items-center justify-between">

        <!-- Include Tailwind CSS (you may need to adjust the paths based on your project) -->
        {{--
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"> --}}

        <!-- Your HTML button -->
        <button onclick="openDownloadModal()"
            class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-500 rounded hover:bg-green-600 focus:ring-2 focus:ring-offset-2 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            Download Bulk CSV Sheet
            <svg class="w-3 h-3 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M1 5h12m0 0L9 1m4 4L9 9" />
            </svg>
        </button>

        <!-- Tailwind CSS Modal -->
        <div id="downloadModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded shadow-lg">
                <h2 class="text-lg font-semibold mb-4">Choose one option to create Invoice </h2>
                <div class="flex space-x-4">
                    <button onclick="directDownloadOption(1)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Manually </button>
                    <button onclick="directDownloadOption(2)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">From Stock</button>
                </div>
            </div>
        </div>

        <!-- JavaScript code -->
        <script>
            function openDownloadModal() {
                // Show the Tailwind CSS modal
                document.getElementById('downloadModal').classList.remove('hidden');
            }

            function directDownloadOption(option) {
                // Close the modal
                document.getElementById('downloadModal').classList.add('hidden');

                // Change the location
                window.location.href = "{{ url('seller/invoice/export-columns') }}/" + option;
            }
        </script>



        <div class="flex items-center space-x-2 w-5/12">
            <div class="w-full">
                <form wire:submit.prevent="bulkInvoiceUpload" enctype="multipart/form-data">
                    <label for="file" class="block font-medium text-gray-700">Choose New Product Excel</label>
                    <div class="mt-2 flex">
                        <div class="relative flex gap-3 items-center w-auto">
                            @if (!$uploadFile)
                            <input type="file" wire:model="uploadFile" wire:ignore.self
                            class="appearance-none block w-full bg-white border border-gray-300 rounded text-xs leading-tight focus:outline-none focus:border-blue-500">
                            @endif
                            <!-- Display file name here -->
                             <!-- Display file name and remove button here -->
                             @if ($uploadFile)
                             <div wire:ignore.self class="flex gap-2">
                                 <span class="text-xs text-gray-500 border border-gray-600 p-2 rounded block" style="display: block !important">
                                     {{ $uploadFile->getClientOriginalName() }}
                                 </span>
                                 <button type="button" wire:click="removeFile" class="text-xs text-red-500 hover:text-red-700">X</button>
                             </div>
                         @endif

                            <span wire:loading wire:target="uploadFile">Processing...</span>
                        </div>
                        @if($uploadFile)
                            <button class="bg-gray-800 hover:bg-orange text-white whitespace-nowrap text-xs ml-3 hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                    type="submit"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="bulkInvoiceUpload">Create Bulk Invoice</span>
                                <span wire:loading wire:target="bulkInvoiceUpload">Creating Bulk Invoice...</span>
                            </button>
                        @endif
                    </div>
                </form>
                {{-- @if ($successMessage) --}}
                @if (session()->has('success'))
                    <div class="flex justify-end space-x-2 mt-4">
                        <button wire:click="sendBulkInvoice('send')" class="bg-white hover:bg-orange text-black text-xs py-2 px-4 rounded-lg border border-gray-300">
                            Send
                        </button>
                        <button  wire:click="sendBulkInvoice('send_later')" class="bg-white hover:bg-orange text-black text-xs py-2 px-4 rounded-lg border border-gray-300">
                            Send Later
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>



</div>
