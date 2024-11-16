<div>
    <div class="border-b px-2 mb-2 pb-1 border-gray-400 text-black text-lg font-medium hidden sm:flex justify-between">
        <div>
        <button class=" p-0.5 px-2 w-auto text-center {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Sent Challans</button>
        <button class=" p-0.5 px-2 w-auto text-center {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Received Challans</button>
        </div>
    </div>

    <div>
        @if ($activeTab === 'tab1')
            {{-- @livewire('sender.screen.sent-sfp-challans.') --}}
            <livewire:seller.screens.sent-sfp-invoice />
        @elseif ($activeTab === 'tab2')
            <livewire:sender.screens.received-sfp-challans />
        @endif
    </div>
</div>
