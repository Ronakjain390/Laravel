<div id="dynamic-view">
    <div class="mt-2">
        @if ($errorMessage)
            @foreach (json_decode($errorMessage, true) as $error)
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Error:</span> {{ $error[0] }}
                </div>
            @endforeach
        @endif

        @if ($successMessage)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="flex items-center p-2 mb-4 text-green-800 border-t-4 border-green-300 bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
                <div class="ms-3 text-sm font-medium">
                    {{ $successMessage }}
                </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-border-3" aria-label="Close">
                    <span class="sr-only">Dismiss</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        @endif

        @if (Session::has('message'))
            @php
                $message = Session::get('message');
            @endphp
            @if (isset($message['type']) && $message['type'] === 'error' && is_array($message['content']))
                @foreach ($message['content'] as $msg)
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span class="font-medium">Error:</span> {{ $msg }}
                    </div>
                @endforeach
            @elseif (isset($message['type']) && $message['type'] === 'success')
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="flex items-center p-2 mb-4 text-green-800 rounded-lg bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
                    <div class="ms-3 text-sm">
                        <span class="font-medium">Success:</span> {{ $message['content'] }}
                    </div>
                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-border-3" aria-label="Close">
                        <span class="sr-only">Dismiss</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
            @endif
        @endif



        @include('components.panel.sender.' . $persistedTemplate)
    </div>
    <script>
        setTimeout(function() {
            var element = document.getElementById('alert-border-3');
            if (element) {
                element.style.display = 'none';
            }
        }, 5000); // Hide the notification after 5 seconds

        setTimeout(function() {
            var element = document.getElementById('alert-error');
            if (element) {
                element.style.display = 'none';
            }
        }, 5000); // Hide the notification after 5 seconds

        setTimeout(function() {
            var element = document.getElementById('alert-success-2');
            if (element) {
                element.style.display = 'none';
            }
        }, 5000); // Hide the notification after 5 seconds

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



                        document.addEventListener('alpine:init', () => {
                            Alpine.data('signaturePad', () => ({
                                signaturePadInstance: null,
                                init() {
                                    this.signaturePadInstance = new SignaturePad(this.$refs.signature_canvas);
                                    this.$nextTick(() => {
                                        this.$refs.signature_canvas.width = 350;
                                        this.$refs.signature_canvas.height = 450;
                                    });
                                },
                                upload() {
                                    let columnId = document.getElementById('column-id').value;
                                    @this.set('signature', this.signaturePadInstance.toDataURL('image/png'));
                                    @this.set('columnId', columnId);
                                    console.log(columnId)
                                    // console.log(this.signaturePadInstance.toDataURL('image/png'));
                                    @this.call('saveSignature');
                                },
                                clear() {
                                    this.signaturePadInstance.clear();
                                }
                            }))
                        });

    </script>
</div>

<!-- <div id="dynamic-view">
    @if (request()->routeIs('sender.*'))
@include('components.panel.sender.' . $persistedTemplate)
@elseif (request()->routeIs('receiver.*'))
@include('components.panel.receiver.' . $persistedTemplate)
@endif
</div>
