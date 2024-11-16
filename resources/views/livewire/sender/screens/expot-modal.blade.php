<div>
    <div x-data="{ open: false }" @open-modal.window="open = true" x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-1/3 p-4">
            <h2 class="text-xl font-bold mb-4">Export Products</h2>
            <p class="text-sm mb-4">
                This CSV file can update all product information. To update just inventory quantities, use the 
                <a href="#" class="text-blue-500">CSV file for inventory</a>.
            </p>
    
            <div class="mb-4">
                <h3 class="text-md font-semibold">Export</h3>
                <div>
                    <input type="radio" id="current_page" value="current_page" wire:model="exportOption">
                    <label for="current_page">Current page</label>
                </div>
                <div>
                    <input type="radio" id="all_data" value="all_data" wire:model="exportOption" :disabled="$filtersApplied">
                    <label for="all_data">All products</label>
                </div>
                <div x-show="!$filtersApplied">
                    <input type="radio" id="filtered_data" value="filtered_data" wire:model="exportOption">
                    <label for="filtered_data">Export filtered data</label>
                </div>
            </div>
    
            <div class="mb-4">
                <h3 class="text-md font-semibold">Export as</h3>
                <div>
                    <input type="radio" id="csv_for_excel" value="csv_for_excel" wire:model="exportType">
                    <label for="csv_for_excel">CSV for Excel, Numbers, or other spreadsheet programs</label>
                </div>
                <div>
                    <input type="radio" id="plain_csv" value="plain_csv" wire:model="exportType">
                    <label for="plain_csv">Plain CSV file</label>
                </div>
            </div>
    
            <div class="flex justify-end">
                <button @click="open = false" class="bg-gray-200 text-black px-4 py-2 rounded mr-2">Cancel</button>
                <button wire:click="export" class="bg-blue-500 text-white px-4 py-2 rounded">Export products</button>
            </div>
        </div>
    </div>
    
</div>
