<div class="rounded-lg dark:border-gray-700 mt-4">
    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 text-xs border border-green-400 text-black px-4 py-2 rounded relative"
               id="successMessage"></p>
        </div>
    </div>
    <div id="errorModal" style="display: none;">
        <div class="modal-content flex items-end bg-red-100 text-xs border border-red-400 text-black px-4 py-2 rounded relative">
            <p class="mt-3" id="errorMessage"></p>
            @if ($errorFileUrl)
                <a class="hover:cursor-pointer hover:underline mt-2 bg-gray-800 text-white px-2 rounded ml-1"
                   href="{{ $errorFileUrl }}" download>Download</a>
            @endif
        </div>
    </div>
    <div wire:loading class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  z-30 bg-opacity-50 ">
        <span class="loading loading-spinner loading-md"></span>
    </div>
    <script>
        window.addEventListener('show-error-message', event => {
            document.getElementById('errorMessage').textContent = event.detail.message;
            document.getElementById('errorModal').style.display = 'block';
            setTimeout(() => {
                document.getElementById('errorModal').style.display = 'none';
            }, 10000);
        });

        window.addEventListener('show-success-message', event => {
            document.getElementById('successMessage').textContent = event.detail.message;
            document.getElementById('successModal').style.display = 'block';
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 10000);
        });
    </script>
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



        <!-- Right side: Upload CSV Sheet input group -->
        {{-- <div class="flex items-center space-x-2 w-5/12">
            <div class="w-full">
                <form wire:submit.prevent="bulkInvoiceUpload" enctype="multipart/form-data">
                    <div class="mt-2">
                        <label for="file" class="block font-medium text-gray-700">Choose New Product
                            Excel</label>
                        <div class="relative">
                            <input type="file" wire:model.defer="uploadFile"
                                class="appearance-none text-xs block w-full bg-white border border-gray-300 rounded py-2 md:px-3 leading-tight focus:outline-none focus:border-blue-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-1 px-2 rounded"
                                type="submit">
                                Bulk Invoice Create
                            </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div> --}}
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
                {{-- @if (session()->has('success'))
                    <div class="flex justify-end space-x-2 mt-4">
                        <button wire:click="sendBulkInvoice('send')" class="bg-white hover:bg-orange text-black text-xs py-2 px-4 rounded-lg border border-gray-300">
                            Send
                        </button>
                        <button  wire:click="sendBulkInvoice('send_later')" class="bg-white hover:bg-orange text-black text-xs py-2 px-4 rounded-lg border border-gray-300">
                            Send Later
                        </button>
                    </div>
                @endif --}}
                @if ($showButtons)
                    <div class="flex justify-end space-x-2 mt-4">
                        <button wire:click="sendBulkInvoice('send')"
                                class="bg-white hover:bg-orange text-black text-xs py-2 px-4 rounded-lg border border-gray-300"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Send</span>
                            <span wire:loading>Sending...</span>
                        </button>
                        <button wire:click="sendBulkInvoice('send_later')"
                                class="bg-white hover:bg-orange text-black text-xs py-2 px-4 rounded-lg border border-gray-300"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Send Later</span>
                            <span wire:loading>Sending Later...</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <livewire:components.bulk-import-logs :type="'invoice'"/>
</div>
