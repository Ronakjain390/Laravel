<div class="">
    <div class="rounded-lg bg-gray-100 p-2">
       
         
        {{-- @include('components.assets.tableComponent.table') --}}
        <div wire:init="loadData">
            @if ($this->isLoading)
            <!-- Show nothing or a minimal loading state -->
            @include('livewire.sender.screens.placeholders')
            @else
            <livewire:sender.screens.detailed-sent-challan lazy>
                    @endif
                </div>
    </div>
</div>
</div>
