<div>
    @php
        // Store the previous URL in the session if not already stored
        if (!session()->has('previous_url')) {
            session(['previous_url' => url()->previous()]);
        }

        // Retrieve the previous URL from the session
        $previousUrl = session('previous_url');

        // Extract the route prefix considering both slashes and dots
        $pathSegments = explode('/', parse_url($previousUrl, PHP_URL_PATH));
        $currentRoutePrefix = '';

        // Iterate through the segments to find the prefix
        foreach ($pathSegments as $segment) {
            if (strpos($segment, '.') !== false) {
                $currentRoutePrefix = explode('.', $segment)[0];
                break;
            }
        }

        // Fallback if no dot is found
        if (empty($currentRoutePrefix)) {
            $currentRoutePrefix = $pathSegments[1] ?? 'default';
        }

        // dump($previousUrl, $currentRoutePrefix);
    @endphp

    @if($currentRoutePrefix === 'sender')
        @livewire('sender.sidebar.sidebar', ['sidenav' => Session::get('panel')['feature'] ?? null])
    @elseif($currentRoutePrefix === 'seller')
        @livewire('seller.sidebar.sidebar', ['sidenav' => Session::get('panel')['feature'] ?? null])
    @else
        <!-- Default or empty sidebar -->
        <div>Sidebar not found</div>
    @endif
</div>