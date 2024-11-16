 {{-- <div wire:init="loadData">
     @if ($this->isLoading)
         <!-- Show nothing or a minimal loading state -->
         @include('livewire.sender.screens.placeholders')
     @else --}}
         <livewire:sender.screens.sent-challanpagination lazy />
     {{-- @endif
 </div> --}}


 <!-- Add this script at the end of your Blade file -->
 {{-- @push('scripts') --}}
     <script>
         function exportChallan() {
             let route = "{{ route('challan.exportChallan') }}";
             route += `?from_date=${encodeURIComponent(this.fromDate)}&page=${encodeURIComponent(this.currentPage)}`;
             window.location.href = route;
         }

         window.addEventListener('DOMContentLoaded', (event) => {
             // Initialize Flowbite on page load
             initFlowbite();
             console.log('DOM Loaded');
         });
         document.addEventListener('livewire:load', function() {
             // Initialize Flowbite components after Livewire loads
             console.log('Livewire reloaded 21');
             initializeFlowbiteComponents();
         });

         document.addEventListener('livewire:update', function() {
             // Reinitialize Flowbite components after Livewire updates
             initializeFlowbiteComponents();
             console.log('Livewire reloaded 21');

         });
         // Event listener to detect tab change and reinitialize dropdown
         document.addEventListener('livewire:load', function() {
             // Livewire.hook('message.processed', (message, component) => {
             // initDropdown();
             initFlowbite();
             console.log('Livewire reloaded 2');
             // });
         });
     </script>
 {{-- @endpush --}}
