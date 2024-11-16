<div class="ml-64">
    {{-- Be like water. --}}
    <div class="flex">
    @foreach ([1, 2, 3, 4] as $panelId)
        <span class="ms-3 m-2 cursor-pointer text-sm font-medium @if ($activeTab === 'panel-' . $panelId) text-gray-900 dark:text-gray-300 @else text-gray-300 dark:text-gray-500 @endif" wire:click="changeTab('{{ $panelId }}')">
            Panel {{ $panelId }}
        </span>
    @endforeach
</div>
{{-- @dd('jdbk'); --}}
<ul class="flex flex-wrap text-center text-sm font-medium ml-64" id="myTab" data-tabs-toggle="#myTabContent"
            role="tablist">
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-1' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-1')" id="sender-tab" data-tabs-target="#panel-1" type="button" role="tab" aria-controls="panel-1" aria-selected="{{ $activeTab === 'panel-1' ? 'true' : 'false' }}">Sender</button>
            </li>
            <!-- Repeat similar buttons for other tabs with proper wire:click and active checks -->
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-2' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-2')" id="receiver-tab" data-tabs-target="#panel-2" type="button" role="tab" aria-controls="panel-2" aria-selected="{{ $activeTab === 'panel-2' ? 'true' : 'false' }}">Receiver</button>
            </li>
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-3' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-3')" id="seller-tab" data-tabs-target="#panel-3" type="button" role="tab" aria-controls="panel-3" aria-selected="{{ $activeTab === 'panel-3' ? 'true' : 'false' }}">Seller</button>
            </li>
            <li class="group" role="presentation">
                <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-4' ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-4')" id="buyer-tab" data-tabs-target="#panel-4" type="button" role="tab" aria-controls="panel-4" aria-selected="{{ $activeTab === 'panel-4' ? 'true' : 'false' }}">Buyer</button>
            </li>        
            
              {{-- {{ $showAnnualPlans ? 'Yearly' : 'Monthly' }} --}} 
            </div>
            <!-- Add more tab buttons for other panels if needed -->
        </ul>
<ul class="flex flex-wrap text-center text-sm font-medium ml-64 mt-20" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
    @foreach ([1, 2] as $panelId)
        <li class="group" role="presentation">
            <button class="inline-block bg-[#4a4f58] p-2 focus:bg-[#f0ac49] text-white group-hover:bg-[#f0ac49] group-hover:text-white {{ $activeTab === 'panel-' . $panelId ? 'bg-[#f0ac49]' : '' }}" wire:click="changeTab('panel-{{ $panelId }}')" id="{{ $panelId === 1 ? 'sender-tab' : 'receiver-tab' }}" data-tabs-target="#panel-{{ $panelId }}" type="button" role="tab" aria-controls="panel-{{ $panelId }}" aria-selected="{{ $activeTab === 'panel-' . $panelId ? 'true' : 'false' }}">
                {{ $panelId === 1 ? 'Sender' : 'Receiver' }}
            </button>
        </li>
    @endforeach
</ul>
<div>
    {{ $content }}
</div>
</div>
