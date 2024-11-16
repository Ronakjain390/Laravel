<div id="dynamic-view">
    <div class="">

    @if ($errorMessage)
        {{-- {{dd($errorMessage)}} --}}
        @foreach (json_decode($errorMessage) as $error)
        <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">Error:</span> {{ $error[0] }}
        </div>
        @endforeach
        @endif
        @if ($successMessage)
            <div class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">Success:</span> {{ $successMessage }}
            </div>
        @endif

        @include('components.panel.seller.' . $persistedTemplate)
    </div>

</div>

<!-- <div id="dynamic-view">
    @if (request()->routeIs('sender.*'))
@include('components.panel.sender.' . $persistedTemplate)
@elseif (request()->routeIs('receiver.*'))
@include('components.panel.receiver.' . $persistedTemplate)
@endif
</div>
