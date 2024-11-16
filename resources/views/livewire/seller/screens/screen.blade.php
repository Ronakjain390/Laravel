<div id="dynamic-view">
    <div class="mt-2">




            @include('components.panel.seller.' . $persistedTemplate)


        <script>
             window.addEventListener('DOMContentLoaded', (event) => {
        // Initialize Flowbite on page load
        initFlowbite();
          console.log('DOM Loaded');
         });
        document.addEventListener('livewire:update', function () {
        // Reinitialize Flowbite components after Livewire updates
        initFlowbite();
        // console.log('Livewire reloaded 21');

    });
    // Event listener to detect tab change and reinitialize dropdown
    document.addEventListener('livewire:load', function () {
        // Livewire.hook('message.processed', (message, component) => {
            // initDropdown();
            initFlowbite();
            // console.log('Livewire reloaded 2');
        // });
    });
        </script>
    </div>

</div>

<!-- <div id="dynamic-view">
    @if (request()->routeIs('sender.*'))
@include('components.panel.sender.' . $persistedTemplate)
@elseif (request()->routeIs('receiver.*'))
@include('components.panel.receiver.' . $persistedTemplate)
@endif
</div>
