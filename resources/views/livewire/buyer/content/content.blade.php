<div id="dynamic-view">
    <div class="">
       
        @include('components.panel.buyer.' . $persistedTemplate)
        <script>
            // Event listener to detect tab change and reinitialize dropdown
            document.addEventListener('livewire:load', function () {
                Livewire.hook('message.processed', (message, component) => {
                    // initDropdown();
                    initFlowbite();
                });
            });
            // Event listener to detect tab change and reinitialize dropdown
            document.addEventListener('livewire:load', function () {
                Livewire.hook('message.processed', (message, component) => {
                    // initDropdown();
                    initFlowbite();
                    // console.log('Livewire reloaded');
                });
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
