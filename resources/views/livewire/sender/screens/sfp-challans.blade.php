<div>
    <div class="border-b px-2 mb-2 pb-1 border-gray-400 text-black text-lg font-medium hidden sm:flex justify-between">
        <div>
        <button class=" p-0.5 px-2 w-auto text-center {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Sent Challans</button>
        <button class=" p-0.5 px-2 w-auto text-center {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Received Challans</button>

        </div>
        {{-- <a href="{{ route('whatsapp-logs') }}"  class="px-4 p-1.5 w-auto text-center text-sm text-blue-500 hover:underline ">Deductions Details</a> --}}
    </div>

    <div>
        @if ($activeTab === 'tab1')
            {{-- @livewire('sender.screen.sent-sfp-challans.') --}}
            <livewire:sender.screens.sent-sfp-challans />
        @elseif ($activeTab === 'tab2')
            <livewire:sender.screens.received-sfp-challans />
        @endif
    </div>
</div>