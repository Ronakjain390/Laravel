@if (session('success'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 mb-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-800 " role="alert">
    <span class="font-medium">Success:</span> {{ session('success') }}
</div>
@endif

@if (session('error'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
    <span class="font-medium">Error:</span> {{ session('error') }}
</div>
@endif
<div wire:init="loadData" class="items-center justify-between bg-white dark:bg-gray-900">
    @if ($this->isLoading)
    <!-- Show nothing or a minimal loading state -->
    @include('livewire.sender.screens.placeholders')
    @else
    <livewire:receiver.content.received-challan lazy>
    @endif
</div>
@push('scripts')
 <script>
     

     window.addEventListener('DOMContentLoaded', (event) => {
        // Initialize Flowbite on page load
        initFlowbite();
          console.log('DOM Loaded');
    });
    document.addEventListener('livewire:load', function () {
    // Initialize Flowbite components after Livewire loads
    console.log('Livewire reloaded 21');
    initializeFlowbiteComponents();
});

document.addEventListener('livewire:update', function () {
    // Reinitialize Flowbite components after Livewire updates
    initializeFlowbiteComponents();
    console.log('Livewire reloaded 21');

});
    // Event listener to detect tab change and reinitialize dropdown
    document.addEventListener('livewire:load', function () {
        // Livewire.hook('message.processed', (message, component) => {
            // initDropdown();
            initFlowbite();
            console.log('Livewire reloaded 2');
        // });
    });
     
 </script>
 @endpush
 