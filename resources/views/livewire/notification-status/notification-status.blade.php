<div x-cloak x-data="{ open: false, notifications: @json($notifications), unreadCount: {{ $unreadCount }} }"
     class="relative inline-block text-left"
     wire:poll.30000ms>
    <button @click="open = !open"
            class="relative inline-flex items-center p-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            id="notifications-menu"
            aria-haspopup="true"
            x-bind:aria-expanded="open">
        <span class="sr-only">Open notifications</span>
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span x-show="unreadCount > 0" x-transition
            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
          <span x-text="unreadCount"></span>
      </span>
    </button>
    <div x-show="open"
         @click.away="open = false"
         class="origin-top-right absolute right-0 mt-2 w-80 z-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none"
         role="menu"
         aria-orientation="vertical"
         aria-labelledby="notifications-menu">
        <div class="px-4 py-3">
            <p class="text-sm font-medium text-gray-700">Notifications</p>
        </div>
        <div class="py-1 max-h-60 sm:max-h-96 overflow-y-auto">
            @forelse ($notifications as $notification)
                @php
                    $dynamicRoute = $notification->panel . '/' . $notification->template_name;
                @endphp
                <a href="{{ url($dynamicRoute) }}"
                   class="flex px-4 py-3 hover:bg-gray-100 transition duration-150 ease-in-out {{ $notification->read_at == null ? 'bg-blue-50' : '' }}">
                    <div class="flex-shrink-0">
                        <span class="inline-block w-2 h-2 mt-2 rounded-full {{ $notification->read_at == null ? 'bg-blue-500' : 'bg-gray-300' }}"></span>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm text-gray-900">{{ $notification->message }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500">
                    No notifications
                </div>
            @endforelse
        </div>
        {{-- @if(count($notifications) > 0)
            <div class="py-1">
                <button @click="unreadCount = 0"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                        role="menuitem">
                    Mark all as read
                </button>
            </div>
        @endif --}}
    </div>
</div>
